<?php
header('Content-Type: application/json');
require_once '../../connection/config.php';
session_start();

function table_has_column(mysqli $con, string $table, string $column): bool {
    $tableEsc = $con->real_escape_string($table);
    $columnEsc = $con->real_escape_string($column);
    $result = $con->query("SHOW COLUMNS FROM `{$tableEsc}` LIKE '{$columnEsc}'");
    return $result && $result->num_rows > 0;
}

function get_column_type(mysqli $con, string $table, string $column): string {
    $tableEsc = $con->real_escape_string($table);
    $columnEsc = $con->real_escape_string($column);
    $result = $con->query("SHOW COLUMNS FROM `{$tableEsc}` LIKE '{$columnEsc}'");
    if (!$result || $result->num_rows === 0) {
        return '';
    }
    $row = $result->fetch_assoc();
    return strtolower(strval($row['Type'] ?? ''));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['UID'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    // Get JSON data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data || !isset($data['household']) || !isset($data['members'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }

    $household = $data['household'];
    $members = $data['members'];
    $householdQuestions = $data['householdQuestions'] ?? [];
    $currentUID = intval($_SESSION['UID']);

    // Sanitize helper
    $s = function($val) { return isset($val) ? trim(strval($val)) : ''; };

    $userStr = strval($currentUID);

    // Start transaction
    $con->begin_transaction();

    // Generate unique number code
    $number_code = $s($household['household_code_number']) ?: uniqid(rand() . '_', true);

    // Determine household/institutional
    $householdVal = ($s($household['household_code']) === '1') ? 1 : 0;
    $institutionalVal = ($s($household['household_code']) === '0') ? 1 : 0;

    $hasMasterUserColumn = table_has_column($con, 'master_tbl', 'user');
    $hasInterviewUserColumn = table_has_column($con, 'interview_tbl', 'user');
    $masterIdType = get_column_type($con, 'master_tbl', 'id');
    $masterIdIsInt = (strpos($masterIdType, 'int') !== false);

    // Generate ID compatible with installed schema.
    if ($masterIdIsInt) {
        $idRes = $con->query("SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM master_tbl");
        $idRow = $idRes ? $idRes->fetch_assoc() : null;
        $master_id = strval(intval($idRow['next_id'] ?? 1));
    } else {
        // Custom ID format: YYMMDDHHmmBBBNNNN
        $brgyStmt = $con->prepare("SELECT brgy FROM user_tbl WHERE id = ?");
        $brgyStmt->bind_param('i', $currentUID);
        $brgyStmt->execute();
        $brgyRow = $brgyStmt->get_result()->fetch_assoc();
        $brgyCode = str_pad($brgyRow['brgy'] ?? '0', 3, '0', STR_PAD_LEFT);
        $brgyStmt->close();

        $idPrefix = date('ymdHi') . $brgyCode;
        $master_id = '';
        $maxAttempts = 100;
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $randId = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $candidateId = $idPrefix . $randId;
            $checkStmt = $con->prepare("SELECT 1 FROM master_tbl WHERE id = ? LIMIT 1");
            $checkStmt->bind_param('s', $candidateId);
            $checkStmt->execute();
            $exists = $checkStmt->get_result()->num_rows > 0;
            $checkStmt->close();
            if (!$exists) {
                $master_id = $candidateId;
                break;
            }
        }
        if ($master_id === '') {
            throw new Exception("Unable to generate unique ID after {$maxAttempts} attempts.");
        }
    }

    $respondentSname = $s($household['respondent_surname']);
    $respondentFname = $s($household['respondent_firstname']);
    $respondentMI = $s($household['respondent_mi']);
    $respondentSuffix = $s($household['respondent_suffix']);
    $hhSname = $s($household['household_head_surname']);
    $hhFname = $s($household['household_head_firstname']);
    $hhMI = $s($household['household_head_mi']);
    $hhSuffix = $s($household['household_head_suffix']);
    $totalMembers = $s($household['total_members']);
    $province = $s($household['province']);
    $city = $s($household['city']);
    $brgy = $s($household['barangay']);
    $unitno = $s($household['address_unit']);
    $lotblock = $s($household['address_house']);
    $street = $s($household['address_street']);
    $encoderName = $s($household['encoder_name']);
    $supervisorName = $s($household['supervisor_name']);

    if ($hasMasterUserColumn) {
        $stmt = $con->prepare(" 
            INSERT INTO master_tbl (
                id, number_code, household, intitutional_living,
                nameofrespondentsname, nameofrespondentfname, nameofrespondentmiddleinitial, nameofrespondentsuffix,
                household_headsname, household_headfname, household_headmiddleinitial, household_headsuffix,
                totalnohouseholdmembers, province, city_municipality, brgy,
                unitno, lotblockno, streetname, dateencoded, nameencoder, nameofsupervisor, user
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)
        ");
        $masterTypes = 'ssii' . str_repeat('s', 18);
        $stmt->bind_param(
            $masterTypes,
            $master_id, $number_code, $householdVal, $institutionalVal,
            $respondentSname, $respondentFname, $respondentMI, $respondentSuffix,
            $hhSname, $hhFname, $hhMI, $hhSuffix,
            $totalMembers, $province, $city, $brgy,
            $unitno, $lotblock, $street, $encoderName, $supervisorName, $userStr
        );
    } else {
        $stmt = $con->prepare(" 
            INSERT INTO master_tbl (
                id, number_code, household, intitutional_living,
                nameofrespondentsname, nameofrespondentfname, nameofrespondentmiddleinitial, nameofrespondentsuffix,
                household_headsname, household_headfname, household_headmiddleinitial, household_headsuffix,
                totalnohouseholdmembers, province, city_municipality, brgy,
                unitno, lotblockno, streetname, dateencoded, nameencoder, nameofsupervisor
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)
        ");
        $masterTypes = 'ssii' . str_repeat('s', 17);
        $stmt->bind_param(
            $masterTypes,
            $master_id, $number_code, $householdVal, $institutionalVal,
            $respondentSname, $respondentFname, $respondentMI, $respondentSuffix,
            $hhSname, $hhFname, $hhMI, $hhSuffix,
            $totalMembers, $province, $city, $brgy,
            $unitno, $lotblock, $street, $encoderName, $supervisorName
        );
    }

    if (!$stmt->execute()) {
        throw new Exception("Error inserting master record: " . $stmt->error);
    }
    $stmt->close();

    $timeStartRaw = $s($household['time_start']);
    if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $timeStartRaw)) {
        $timeStart = str_replace('T', ' ', $timeStartRaw) . ':00';
    } elseif (preg_match('/^\d{2}:\d{2}$/', $timeStartRaw)) {
        $timeStart = date('Y-m-d') . ' ' . $timeStartRaw . ':00';
    } else {
        $timeStart = date('Y-m-d H:i:s');
    }
    $interviewerSname = $s($household['interviewer_surname']);
    $interviewerFname = $s($household['interviewer_firstname']);
    $interviewerMI = $s($household['interviewer_mi']);
    $interviewerSuffix = $s($household['interviewer_suffix']);
    $supervisorSname = $s($household['supervisor_surname']);
    $supervisorFname = $s($household['supervisor_firstname']);
    $supervisorMI = $s($household['supervisor_mi']);
    $supervisorSuffix = $s($household['supervisor_suffix']);
    $encoderFnameEmpty = '';
    $encoderMIEmpty = '';
    $encoderSuffixEmpty = '';

    if ($hasInterviewUserColumn) {
        $stmt = $con->prepare(" 
            INSERT INTO interview_tbl (
                id, timestart,
                nameofencodersname, nameofencoderfname, nameofencodermiddleinitial, nameofencodersuffix,
                nameofsupervisorsname, nameofsupervisorfname, namenameofsupervisormiddleinitial, namenameofsupervisorsuffix,
                nameofinterviewersname, nameofinterviewerfname, nameofinterviewermiddlename, nameofinterviewersuffix,
                datencoded, user
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
        ");
        $stmt->bind_param(
            str_repeat('s', 15),
            $master_id, $timeStart,
            $encoderName, $encoderFnameEmpty, $encoderMIEmpty, $encoderSuffixEmpty,
            $supervisorSname, $supervisorFname, $supervisorMI, $supervisorSuffix,
            $interviewerSname, $interviewerFname, $interviewerMI, $interviewerSuffix,
            $userStr
        );
    } else {
        $stmt = $con->prepare(" 
            INSERT INTO interview_tbl (
                id, timestart,
                nameofencodersname, nameofencoderfname, nameofencodermiddleinitial, nameofencodersuffix,
                nameofsupervisorsname, nameofsupervisorfname, namenameofsupervisormiddleinitial, namenameofsupervisorsuffix,
                nameofinterviewersname, nameofinterviewerfname, nameofinterviewermiddlename, nameofinterviewersuffix,
                datencoded
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param(
            str_repeat('s', 14),
            $master_id, $timeStart,
            $encoderName, $encoderFnameEmpty, $encoderMIEmpty, $encoderSuffixEmpty,
            $supervisorSname, $supervisorFname, $supervisorMI, $supervisorSuffix,
            $interviewerSname, $interviewerFname, $interviewerMI, $interviewerSuffix
        );
    }

    if (!$stmt->execute()) {
        throw new Exception("Error inserting interview record: " . $stmt->error);
    }
    $stmt->close();

    // Process each member
    $memberCount = count($members);

    for ($i = 0; $i < $memberCount; $i++) {
        $m = $members[$i];
        if (!$m || empty($m)) continue;

        $mg = function($key) use ($m, $s) { return $s($m[$key] ?? ''); };

        // --- demochar_tbl ---
        $nationality = $mg('Q7');
        if ($nationality === 'Non-Filipino' && !empty($m['Q7b'])) {
            $nationality .= ' (' . $s($m['Q7b']) . ')';
        }

        $stmt = $con->prepare("
            INSERT INTO demochar_tbl (
                id, q1_fullname, q2_relationshiptohhh, q3_sex, q4_age, q5_dob,
                q6_placeofbirth, q7_nationality, q8_maritalstatus, q9_religion, q10_ethnicity,
                q11_highesteduc, q12_currentlyenrolled, q13_typeofschool, q14_placeofschool, user
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $q1 = $mg('Q1'); $q2 = $mg('Q2'); $q3 = $mg('Q3'); $q4 = $mg('Q4'); $q5 = $mg('Q5');
        $q6 = $mg('Q6'); $q8 = $mg('Q8'); $q9 = $mg('Q9'); $q10 = $mg('Q10');
        $q11 = $mg('Q11'); $q12 = $mg('Q12'); $q13 = $mg('Q13'); $q14 = $mg('Q14');

        $stmt->bind_param('sssssssssssssssi',
            $master_id, $q1, $q2, $q3, $q4, $q5,
            $q6, $nationality, $q8, $q9, $q10,
            $q11, $q12, $q13, $q14, $currentUID
        );
        if (!$stmt->execute()) throw new Exception("Error inserting demochar member " . ($i+1) . ": " . $stmt->error);
        $stmt->close();

        // --- economicactivity_tbl ---
        $stmt = $con->prepare("
            INSERT INTO economicactivity_tbl (id, q15_monthlyincome, q16_sourceincome, q17_statusofworkorbusiness, q18_placeofworkorbusiness, user)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $q15 = $mg('Q15'); $q16 = $mg('Q16'); $q17 = $mg('Q17'); $q18 = $mg('Q18');
        $stmt->bind_param('ssssss', $master_id, $q15, $q16, $q17, $q18, $userStr);
        if (!$stmt->execute()) throw new Exception("Error inserting economic activity member " . ($i+1) . ": " . $stmt->error);
        $stmt->close();

        // --- healthinfo_tbl ---
        $stmt = $con->prepare("
            INSERT INTO healthinfo_tbl (
                id, q19_placeofdelivery, q20_birthattendant, q21_immunization, q22_livingchildren,
                q23_familyplanninguse, q24_sourceoffpmethod, q25_intentionoffp,
                q26_healthinsurance, q27_facilityvisited, q28_reasonforvisit, q29_disability, user
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $q19 = $mg('Q19'); $q20 = $mg('Q20'); $q21 = $mg('Q21'); $q22 = $mg('Q22');
        $q23 = $mg('Q23'); $q24 = $mg('Q24'); $q25 = $mg('Q25');
        $q26 = $mg('Q26'); $q27 = $mg('Q27'); $q28 = $mg('Q28'); $q29 = $mg('Q29');
        $stmt->bind_param('sssssssssssss',
            $master_id, $q19, $q20, $q21, $q22, $q23, $q24, $q25, $q26, $q27, $q28, $q29, $userStr
        );
        if (!$stmt->execute()) throw new Exception("Error inserting health info member " . ($i+1) . ": " . $stmt->error);
        $stmt->close();

        // --- sociocivicparticipation_tbl ---
        $stmt = $con->prepare("
            INSERT INTO sociocivicparticipation_tbl (id, q30_soloparent, q31_registeredsenior, q32_registeredvoter, user)
            VALUES (?, ?, ?, ?, ?)
        ");
        $q30 = $mg('Q30'); $q31 = $mg('Q31'); $q32 = $mg('Q32');
        $stmt->bind_param('sssss', $master_id, $q30, $q31, $q32, $userStr);
        if (!$stmt->execute()) throw new Exception("Error inserting socio-civic member " . ($i+1) . ": " . $stmt->error);
        $stmt->close();

        // --- migrationinfo_tbl ---
        $stmt = $con->prepare("
            INSERT INTO migrationinfo_tbl (
                id, q33_previousresidence, q34_previousresidence, q35_lengthofstayinbrgy, q36_typeofresident,
                q37_dateoftransfer, q38a_reasonforleavingprevresidence, q38b_reasonforleavingprevresidence,
                q38c_reasonforleavingprevresidence, q39a_returntoprevresidence, q39b_returntoprevresidence,
                q40a_reasonfortransferinbrgy, q40b_reasonfortransferinbrgy, q40c_reasonfortransferinbrgy,
                q41a_durationofstayinbrgy, q41b_durationofstayinbrgy, user
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $q33 = $mg('Q33'); $q34 = $mg('Q34'); $q35 = $mg('Q35'); $q36 = $mg('Q36');
        $q37 = $mg('Q37'); $q38a = $mg('Q38a'); $q38b = $mg('Q38b'); $q38c = $mg('Q38c');
        $q39a = $mg('Q39a'); $q39b = $mg('Q39b');
        $q40a = $mg('Q40a'); $q40b = $mg('Q40b'); $q40c = $mg('Q40c');
        $q41a = $mg('Q41a'); $q41b = $mg('Q41b');
        $stmt->bind_param('sssssssssssssssss',
            $master_id, $q33, $q34, $q35, $q36, $q37, $q38a, $q38b, $q38c,
            $q39a, $q39b, $q40a, $q40b, $q40c, $q41a, $q41b, $userStr
        );
        if (!$stmt->execute()) throw new Exception("Error inserting migration info member " . ($i+1) . ": " . $stmt->error);
        $stmt->close();

        // --- communitytaxcert_tbl ---
        $stmt = $con->prepare("
            INSERT INTO communitytaxcert_tbl (id, q42a_ctcinformation, q42b_ctcinformation, user)
            VALUES (?, ?, ?, ?)
        ");
        $q42a = $mg('Q42a'); $q42b = $mg('Q42b');
        $stmt->bind_param('ssss', $master_id, $q42a, $q42b, $userStr);
        if (!$stmt->execute()) throw new Exception("Error inserting CTC member " . ($i+1) . ": " . $stmt->error);
        $stmt->close();

        // --- skillsdevelopment_tbl ---
        $stmt = $con->prepare("
            INSERT INTO skillsdevelopment_tbl (id, q43_skilldevelopment, q44_skills, user)
            VALUES (?, ?, ?, ?)
        ");
        $q43 = $mg('Q43'); $q44 = $mg('Q44');
        $stmt->bind_param('ssss', $master_id, $q43, $q44, $userStr);
        if (!$stmt->execute()) throw new Exception("Error inserting skills member " . ($i+1) . ": " . $stmt->error);
        $stmt->close();
    }

    // --- questionforhousehold_tbl ---
    $hq = function($key) use ($householdQuestions, $s) { return $s($householdQuestions[$key] ?? ''); };

    $stmt = $con->prepare("
        INSERT INTO questionforhousehold_tbl (
            id, q45, q46, q47, q48, q49, q50, q51, q52, q53,
            q54a, q54b, q55a, q55b, q55c, q56a, q56b, q56c,
            q57a, q57b, q57c, q58a, q58b, q58c, user
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $hq45 = $hq('Q45'); $hq46 = $hq('Q46'); $hq47 = $hq('Q47'); $hq48 = $hq('Q48');
    $hq49 = $hq('Q49'); $hq50 = $hq('Q50a'); $hq51 = $hq('Q50b'); $hq52 = $hq('Q51');
    // Q52 (building type) + Q53 (construction materials) combined
    $hq53 = $hq('Q52');
    $q53_val = $hq('Q53');
    if ($hq53 !== '' && $q53_val !== '') {
        $hq53 = $hq53 . ' | ' . $q53_val;
    } elseif ($hq53 === '') {
        $hq53 = $q53_val;
    }
    $hq54a = $hq('Q54a'); $hq54b = $hq('Q54b');
    $hq55a = $hq('Q55a'); $hq55b = $hq('Q55b'); $hq55c = $hq('Q55c');
    $hq56a = $hq('Q56a'); $hq56b = $hq('Q56b'); $hq56c = $hq('Q56c');
    $hq57a = $hq('Q57a'); $hq57b = $hq('Q57b'); $hq57c = $hq('Q57c');
    $hq58a = $hq('Q58a'); $hq58b = $hq('Q58b'); $hq58c = $hq('Q58c');

    $stmt->bind_param('sssssssssssssssssssssssss',
        $master_id,
        $hq45, $hq46, $hq47, $hq48, $hq49, $hq50, $hq51, $hq52, $hq53,
        $hq54a, $hq54b, $hq55a, $hq55b, $hq55c, $hq56a, $hq56b, $hq56c,
        $hq57a, $hq57b, $hq57c, $hq58a, $hq58b, $hq58c, $userStr
    );
    if (!$stmt->execute()) throw new Exception("Error inserting household questions: " . $stmt->error);
    $stmt->close();

    // Commit transaction
    $con->commit();

    echo json_encode([
        'success' => true,
        'message' => 'RBIM entry has been submitted successfully.',
        'entry_id' => $master_id,
        'number_code' => $number_code
    ]);

} catch (Exception $e) {
    $con->rollback();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
