<?php
header('Content-Type: application/json');
require_once '../connection/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['UID'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Get JSON data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        exit;
    }

    // Start transaction
    $con->begin_transaction();

    // Use provided number_code or generate unique ID
    $number_code = !empty($data['number_code']) ? $data['number_code'] : uniqid(rand() . '_', true);

    // Insert into master_tbl
    $stmt = $con->prepare("
        INSERT INTO master_tbl (
            number_code, 
            household,
            intitutional_living,
            nameofrespondent, 
            household_head, 
            totalnohouseholdmembers,
            province, 
            city_municipality, 
            brgy, 
            unitno, 
            lotblockno, 
            streetname,
            dateencoded, 
            nameencoder, 
            nameofsupervisor,
            user
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $dateEncoded = !empty($data['encoder_date']) ? $data['encoder_date'] . ' ' . date('H:i:s') : date('Y-m-d H:i:s');
    $currentUID = intval($_SESSION['UID']);
    
    // Determine household and institutional_living values
    $household = ($data['living_type'] === 'household') ? 1 : 0;
    $institutional_living = ($data['living_type'] === 'institutional') ? 1 : 0;

    $stmt->bind_param(
        'siiisssssssssssi',
        $data['number_code'],
        $household,
        $institutional_living,
        $data['respondent_name'],
        $data['household_head'],
        $data['total_household_members'],
        $data['province'],
        $data['city_municipality'],
        $data['barangay'],
        $data['address_unit'],
        $data['address_lot_block'],
        $data['address_street'],
        $dateEncoded,
        $data['encoder_name'],
        $data['supervisor_name'],
        $currentUID
    );

    if (!$stmt->execute()) {
        throw new Exception("Error inserting master record: " . $stmt->error);
    }

    $master_id = $con->insert_id;
    $stmt->close();

    // Insert into interview_tbl
    $stmt = $con->prepare("
        INSERT INTO interview_tbl (
            id,
            nameofencoder,
            nameofinterviewer,
            nameofsupervisor,
            datencoded
        ) VALUES (?, ?, ?, ?, ?)
    ");

    $datSupervised = !empty($data['supervisor_date']) ? $data['supervisor_date'] . ' ' . date('H:i:s') : date('Y-m-d H:i:s');

    $stmt->bind_param(
        'issss',
        $master_id,
        $data['encoder_name'],
        $data['interviewer_name'],
        $data['supervisor_name'],
        $datSupervised
    );

    if (!$stmt->execute()) {
        throw new Exception("Error inserting interview record: " . $stmt->error);
    }

    $stmt->close();

    // Insert household members demographic data
    for ($i = 1; $i <= intval($data['total_household_members']); $i++) {
        $stmt = $con->prepare("
            INSERT INTO demochar_tbl (
                id,
                q1_fullname,
                q2_relationshiptohhh,
                q3_sex,
                q4_age,
                q5_dob,
                q6_placeofbirth,
                q7_nationality,
                q8_maritalstatus,
                q9_religion,
                q10_ethnicity,
                user
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $memberName = $data['member_name_' . $i] ?? '';
        $relationship = $data['relationship_' . $i] ?? '';
        $sex = $data['sex_' . $i] ?? '';
        $age = $data['age_' . $i] ?? '';
        $dob = $data['dob_' . $i] ?? '';
        $pob = $data['pob_' . $i] ?? '';
        $nationality = $data['nationality_' . $i] ?? '';
        
        // If Non-Filipino, append the other nationality
        if ($nationality === 'Non-Filipino' && !empty($data['nationality_other_' . $i])) {
            $nationality = $nationality . ' (' . $data['nationality_other_' . $i] . ')';
        }
        
        $marital = $data['marital_status_' . $i] ?? '';
        $religion = $data['religion_' . $i] ?? '';
        $ethnicity = $data['ethnicity_' . $i] ?? '';

        $stmt->bind_param(
            'issssssssss',
            $master_id,
            $memberName,
            $relationship,
            $sex,
            $age,
            $dob,
            $pob,
            $nationality,
            $marital,
            $religion,
            $ethnicity,
            $currentUID
        );

        if (!$stmt->execute()) {
            throw new Exception("Error inserting household member " . $i . ": " . $stmt->error);
        }

        $stmt->close();
    }

    // Commit transaction
    $con->commit();

    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Census entry created successfully',
        'entry_id' => $master_id,
        'number_code' => $number_code
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $con->rollback();
    
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>
