/**
 * Census Form Builder - Multi-step Google Form Style Census Data Entry
 * Matches census_system.html design with card-based layout
 */

class CensusFormBuilder {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 6;
        this.memberCount = 1;
        this.memberData = [{}]; // Array of member objects: memberData[memberIndex][questionId] = value
        
        // Step 1: Household Information
        this.householdData = {
            household_code: '1', // 1 for Household, 0 for Institutional
            household_code_number: '', // Household code identifier/number
            respondent_surname: '',
            respondent_firstname: '',
            respondent_mi: '',
            respondent_name: '', // composed
            household_head_surname: '',
            household_head_firstname: '',
            household_head_mi: '',
            household_head: '', // composed
            province: '',
            city: '',
            barangay: '',
            address_unit: '',
            address_house: '',
            address_street: '',
            time_start: this.getFormattedTime(),
            interviewer_surname: '',
            interviewer_firstname: '',
            interviewer_mi: '',
            interviewer_name: '', // composed
            interviewer_date: '',
            supervisor_surname: '',
            supervisor_firstname: '',
            supervisor_mi: '',
            supervisor_name: '', // composed
            supervisor_date: '',
            encoder_name: this.getEncoderName(),
            date_encoded: this.getTodayDate(),
            total_members: 1
        };

        // Step 2: Census Questions (Members) - Matching census_system.html structure
        this.QUESTIONS = [
            {
                id: 'Q1',
                badge: 'Q1',
                label: 'Full Name',
                hint: 'Surname, First Name, Middle Initial, Suffix',
                type: 'name',
                required: true
            },
            {
                id: 'Q2',
                badge: 'Q2',
                label: 'Relationship to Household Head',
                hint: 'Select relationship',
                type: 'select',
                required: true,
                options: [
                    'Head',
                    'Spouse',
                    'Son',
                    'Daughter',
                    'Stepson',
                    'Stepdaughter',
                    'Son-in-law',
                    'Daughter-in-law',
                    'Grandson',
                    'Granddaughter',
                    'Father',
                    'Mother',
                    'Sister',
                    'Uncle',
                    'Aunt',
                    'Nephew',
                    'Niece',
                    'Other Relative',
                    'Non-relative',
                    'Boarder',
                    'Domestic Helper'
                ]
            },
            {
                id: 'Q3',
                badge: 'Q3',
                label: 'Sex',
                hint: 'Select biological sex',
                type: 'radio',
                required: true,
                options: ['Male', 'Female']
            },
            {
                id: 'Q4',
                badge: 'Q4',
                label: 'Age',
                hint: 'Auto-computed from Date of Birth',
                type: 'number',
                required: false,
                computed: true
            },
            {
                id: 'Q5',
                badge: 'Q5',
                label: 'Date of Birth',
                hint: 'Age will be auto-computed',
                type: 'date',
                required: true
            },
            {
                id: 'Q6',
                badge: 'Q6',
                label: 'Place of Birth',
                hint: 'Province or Country',
                type: 'text',
                required: true,
                placeholder: 'Enter place of birth'
            },
            {
                id: 'Q7',
                badge: 'Q7',
                label: 'Nationality',
                hint: 'Select nationality',
                type: 'select',
                required: true,
                options: ['Filipino', 'Non-Filipino'],
                showIf: null
            },
            {
                id: 'Q7b',
                badge: 'Q7b',
                label: 'Specify Nationality',
                hint: 'If Non-Filipino, specify country',
                type: 'text',
                required: false,
                placeholder: 'Enter nationality',
                showIf: { id: 'Q7', val: 'Non-Filipino' }
            },
            {
                id: 'Q8',
                badge: 'Q8',
                label: 'Marital Status',
                hint: 'Select current marital status',
                type: 'select',
                required: true,
                options: [
                    'Single',
                    'Married',
                    'Living-in',
                    'Widowed',
                    'Separated',
                    'Divorced',
                    'Unknown'
                ]
            },
            {
                id: 'Q9',
                badge: 'Q9',
                label: 'Religion',
                hint: 'Professed religion',
                type: 'text',
                required: true,
                placeholder: 'Enter religion'
            },
            {
                id: 'Q10',
                badge: 'Q10',
                label: 'Ethnicity',
                hint: 'Ethnic or tribal affiliation',
                type: 'text',
                required: true,
                placeholder: 'Enter ethnicity'
            },
            {
                id: 'Q11',
                badge: 'Q11',
                label: 'Educational Attainment',
                hint: 'Highest level completed',
                type: 'select',
                required: true,
                options: [
                    'No Education',
                    'Pre-school',
                    'Elementary Level',
                    'Elementary Graduate',
                    'High School Level',
                    'High School Graduate',
                    'Junior HS',
                    'Junior HS Graduate',
                    'Senior HS Level',
                    'Senior HS Graduate',
                    'Vocational/Tech',
                    'College Level',
                    'College Graduate',
                    'Post-graduate'
                ]
            },
            {
                id: 'Q12',
                badge: 'Q12',
                label: 'Currently Enrolled',
                hint: 'Is member currently enrolled in school?',
                type: 'select',
                required: true,
                options: [
                    'Yes, public',
                    'Yes, private',
                    'No'
                ]
            },
            {
                id: 'Q13',
                badge: 'Q13',
                label: 'School Level',
                hint: 'Current school level',
                type: 'select',
                required: false,
                options: [
                    'Pre-school',
                    'Elementary',
                    'Junior High School',
                    'Senior High School',
                    'Vocational/Technical',
                    'College/University'
                ]
            },
            {
                id: 'Q14',
                badge: 'Q14',
                label: 'Place of School',
                hint: 'Name or location of school',
                type: 'text',
                required: false,
                placeholder: 'Enter school name or location'
            },
            {
                id: 'Q15',
                badge: 'Q15',
                label: 'Monthly Income',
                hint: 'Monthly income amount (write 0 if none)',
                type: 'number',
                required: true,
                placeholder: '0'
            },
            {
                id: 'Q16',
                badge: 'Q16',
                label: 'Source of Income',
                hint: 'Primary source of income',
                type: 'select',
                required: true,
                options: [
                    'Employment',
                    'Business',
                    'Remittance',
                    'Investments',
                    'Others'
                ],
                showIf: { id: 'Q15', val: (val) => val && parseInt(val) !== 0 }
            },
            {
                id: 'Q17',
                badge: 'Q17',
                label: 'Status of Work/Business',
                hint: 'Employment status or business type',
                type: 'select',
                required: true,
                options: [
                    'Permanent Work',
                    'Casual Work',
                    'Contractual Work',
                    'Individually Owned Business',
                    'Share/Partnership Business',
                    'Corporate Business'
                ],
                showIf: { id: 'Q16', val: ['Employment', 'Business'] }
            },
            {
                id: 'Q18',
                badge: 'Q18',
                label: 'Place of Work/Business',
                hint: 'Name or location of workplace/business',
                type: 'text',
                required: true,
                placeholder: 'Enter workplace or business location',
                showIf: { id: 'Q16', val: ['Employment', 'Business'] }
            },
            {
                id: 'Q19',
                badge: 'Q19',
                label: 'Place of Delivery',
                hint: 'Where was the infant delivered',
                type: 'select',
                required: false,
                options: [
                    'Public Hospital',
                    'Private Hospital',
                    'Lying-in Clinic',
                    'Home',
                    'Others'
                ]
            },
            {
                id: 'Q20',
                badge: 'Q20',
                label: 'Birth Attendant',
                hint: 'Person who assisted in delivery',
                type: 'select',
                required: false,
                options: [
                    'Doctor',
                    'Nurse',
                    'Midwife',
                    'Hilot',
                    'Others'
                ]
            },
            {
                id: 'Q21',
                badge: 'Q21',
                label: 'Immunization',
                hint: 'Last vaccine received by infant',
                type: 'text',
                required: false,
                placeholder: 'Enter vaccine name'
            },
            {
                id: 'Q22',
                badge: 'Q22',
                label: 'Living Children',
                hint: 'Total pregnancies / How many children still living',
                type: 'text',
                required: false,
                placeholder: 'e.g. 3/2 (pregnancies/living)'
            },
            {
                id: 'Q23',
                badge: 'Q23',
                label: 'Family Planning (FP) Use',
                hint: 'What family planning method does member and partner currently use?',
                type: 'select',
                required: false,
                options: [
                    'None',
                    'Female sterilization/Ligation',
                    'Male sterilization/Vasectomy',
                    'IUD',
                    'Injectables',
                    'Implants',
                    'Pill',
                    'Condom',
                    'Modern natural FP',
                    'Lactational Amenorrhea Method (LAM)'
                ],
                showIf: { id: 'Q22', val: (val) => val && val.trim() !== '' && val.trim() !== '0/99' && val.trim() !== '0' }
            },
            {
                id: 'Q24',
                badge: 'Q24',
                label: 'Source of FP Method',
                hint: 'Where did they obtain the FP method?',
                type: 'select',
                required: false,
                options: [
                    'Government hospital',
                    'RHU/Health center',
                    'Brgy. Health Station',
                    'Private hospital',
                    'Pharmacy',
                    'Other'
                ],
                showIf: { id: 'Q23', val: (val) => val && val !== 'None' && val !== '' }
            },
            {
                id: 'Q25',
                badge: 'Q25',
                label: 'Intention to Use FP',
                hint: 'Does member and partner intend to use FP method?',
                type: 'select',
                required: false,
                options: [
                    'Yes',
                    'No'
                ],
                showIf: { id: 'Q23', val: (val) => !val || val === 'None' || val === '' }
            }
        ];

        // Age requirements for each question
        this.ageRequirements = {
            Q11: (age) => parseInt(age) >= 5,           // Educational Attainment: 5+
            Q12: (age) => { const a = parseInt(age); return a >= 3 && a <= 24; }, // Currently Enrolled: 3-24
            Q13: (age) => { const a = parseInt(age); return a >= 3 && a <= 24; }, // School Level: 3-24
            Q14: (age) => { const a = parseInt(age); return a >= 3 && a <= 24; }, // Place of School: 3-24
            Q15: (age) => parseInt(age) >= 15,          // Monthly Income: 15+
            Q16: (age) => parseInt(age) >= 15,          // Source of Income: 15+
            Q17: (age) => parseInt(age) >= 15,          // Status of Work/Business: 15+
            Q18: (age) => parseInt(age) >= 15,          // Place of Work/Business: 15+
            Q19: (age) => parseInt(age) === 0,          // Place of Delivery: 0-11 months old
            Q20: (age) => parseInt(age) === 0,          // Birth Attendant: 0-11 months old
            Q21: (age) => parseInt(age) === 0,          // Immunization: 0-11 months old
            Q22: (age) => { const a = parseInt(age); return a >= 10 && a <= 54; }, // Living Children: women 10-54
            Q23: (age) => { const a = parseInt(age); return a >= 10 && a <= 54; }, // FP Use: women 10-54
            Q24: (age) => { const a = parseInt(age); return a >= 10 && a <= 54; }, // Source of FP: women 10-54
            Q25: (age) => { const a = parseInt(age); return a >= 10 && a <= 54; }  // Intention to Use FP: women 10-54
        };

        // Sex requirements for questions (only show for specific sex)
        this.sexRequirements = {
            Q22: 'Female',
            Q23: 'Female',
            Q24: 'Female',
            Q25: 'Female'
        };

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.renderStep1();
        this.updateProgress();
        this.attachCloseButton();
    }

    attachCloseButton() {
        $(document).off('click', '#closeFormBtn').on('click', '#closeFormBtn', () => {
            if (typeof hideCensusForm === 'function') {
                hideCensusForm();
            }
        });
    }

    setupEventListeners() {
        // Next/Previous buttons - use correct IDs
        $(document).off('click', '#btnNext').on('click', '#btnNext', () => this.nextStep());
        $(document).off('click', '#btnPrev').on('click', '#btnPrev', () => this.previousStep());
        $(document).off('click', '#btnSubmit').on('click', '#btnSubmit', () => this.submitForm());
        
        // Also support old button IDs for compatibility
        $(document).off('click', '#nextBtn').on('click', '#nextBtn', () => this.nextStep());
        $(document).off('click', '#prevBtn').on('click', '#prevBtn', () => this.previousStep());
        $(document).off('click', '#submitBtn').on('click', '#submitBtn', () => this.submitForm());
        
        // Household code radio
        $(document).on('change', 'input[name="household_code"]', (e) => {
            this.householdData.household_code = e.target.value;
        });
        $(document).on('change', '#household_code_number', (e) => {
            this.householdData.household_code_number = e.target.value;
        });
        
        // Step 1 inputs - name fields (split into surname, firstname, MI)
        const composeNameListener = (prefix, dataKey) => {
            $(document).on('input', `#${prefix}_surname, #${prefix}_firstname, #${prefix}_mi`, () => {
                const s = $(`#${prefix}_surname`).val() || '';
                const f = $(`#${prefix}_firstname`).val() || '';
                const m = $(`#${prefix}_mi`).val() || '';
                this.householdData[`${prefix}_surname`] = s;
                this.householdData[`${prefix}_firstname`] = f;
                this.householdData[`${prefix}_mi`] = m;
                this.householdData[dataKey] = [s, f, m ? m + '.' : ''].filter(Boolean).join(', ').replace(/,\s*$/, '');
            });
        };
        composeNameListener('respondent', 'respondent_name');
        composeNameListener('household_head', 'household_head');
        composeNameListener('interviewer', 'interviewer_name');
        composeNameListener('supervisor', 'supervisor_name');

        $(document).on('change', '#province', (e) => {
            this.householdData.province = e.target.value;
            this.loadCitiesByProvince();
        });
        $(document).on('change', '#city', (e) => {
            this.householdData.city = e.target.value;
            this.loadBarangaysByCity();
        });
        $(document).on('change', '#barangay', (e) => {
            this.householdData.barangay = e.target.value;
        });
        $(document).on('change', '#address_unit', (e) => {
            this.householdData.address_unit = e.target.value;
        });
        $(document).on('change', '#address_house', (e) => {
            this.householdData.address_house = e.target.value;
        });
        $(document).on('change', '#address_street', (e) => {
            this.householdData.address_street = e.target.value;
        });
        $(document).on('change', '#interviewer_date', (e) => {
            this.householdData.interviewer_date = e.target.value;
        });
        $(document).on('change', '#supervisor_date', (e) => {
            this.householdData.supervisor_date = e.target.value;
        });
        $(document).on('change', '#total_members', (e) => {
            const newCount = parseInt(e.target.value) || 1;
            this.householdData.total_members = newCount;
            this.setMemberCount(newCount);
        });
    }

    renderStep1() {
        const html = `
            <div class="section-label">
                <div class="dot"></div>
                <h3>Step 1: Household Information</h3>
            </div>

            <!-- Household Code Type -->
            <div class="q-card">
                <div class="q-card-header">
                    <div>
                        <div class="q-badge">S1.1</div>
                        <div class="q-title">Household Code Type</div>
                        <div class="q-hint">Type of household unit</div>
                    </div>
                    <span class="q-required">Required</span>
                </div>
                <div class="q-members">
                    <div class="member-row">
                        <div class="radio-group">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin: 0; font-weight: 500; color: #6A7F96;">
                                <input type="radio" name="household_code" value="1" checked>
                                Household
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin: 0; font-weight: 500; color: #6A7F96;">
                                <input type="radio" name="household_code" value="0">
                                Institutional Living Quarters
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Household Code Number -->
            <div class="q-card">
                <div class="q-card-header">
                    <div>
                        <div class="q-badge">S1.1B</div>
                        <div class="q-title">Household Code Number</div>
                        <div class="q-hint">Unique identifier for this household</div>
                    </div>
                    <span class="q-required">Required</span>
                </div>
                <div class="q-members">
                    <div class="member-row">
                        <input type="text" id="household_code_number" class="q-input" placeholder="e.g., HH-2026-001" required>
                    </div>
                </div>
            </div>

            <!-- Respondent Name -->
            <div class="q-card">
                <div class="q-card-header">
                    <div>
                        <div class="q-badge">S1.2</div>
                        <div class="q-title">Name of Respondent</div>
                        <div class="q-hint">Surname, First Name, Middle Initial</div>
                    </div>
                    <span class="q-required">Required</span>
                </div>
                <div class="q-members">
                    <div class="member-row" style="display: grid; grid-template-columns: 1fr 1fr 80px; gap: 8px;">
                        <input type="text" id="respondent_surname" class="q-input" placeholder="Surname" required>
                        <input type="text" id="respondent_firstname" class="q-input" placeholder="First Name" required>
                        <input type="text" id="respondent_mi" class="q-input" placeholder="M.I." maxlength="5">
                    </div>
                </div>
            </div>

            <!-- Household Head -->
            <div class="q-card">
                <div class="q-card-header">
                    <div>
                        <div class="q-badge">S1.3</div>
                        <div class="q-title">Household Head</div>
                        <div class="q-hint">Surname, First Name, Middle Initial</div>
                    </div>
                    <span class="q-required">Required</span>
                </div>
                <div class="q-members">
                    <div class="member-row" style="display: grid; grid-template-columns: 1fr 1fr 80px; gap: 8px;">
                        <input type="text" id="household_head_surname" class="q-input" placeholder="Surname" required>
                        <input type="text" id="household_head_firstname" class="q-input" placeholder="First Name" required>
                        <input type="text" id="household_head_mi" class="q-input" placeholder="M.I." maxlength="5">
                    </div>
                </div>
            </div>

            <!-- Province -->
            <div class="q-card">
                <div class="q-card-header">
                    <div>
                        <div class="q-badge">S1.4</div>
                        <div class="q-title">Province</div>
                        <div class="q-hint">Select province</div>
                    </div>
                    <span class="q-required">Required</span>
                </div>
                <div class="q-members">
                    <div class="member-row">
                        <select id="province" class="q-input" required>
                            <option value="">Select province</option>
                            <option value="NCR">National Capital Region (NCR)</option>
                            <option value="Abra">Abra</option>
                            <option value="Agusan del Norte">Agusan del Norte</option>
                            <option value="Agusan del Sur">Agusan del Sur</option>
                            <option value="Aklan">Aklan</option>
                            <option value="Albay">Albay</option>
                            <option value="Antique">Antique</option>
                            <option value="Apayao">Apayao</option>
                            <option value="Aurora">Aurora</option>
                            <option value="Basilan">Basilan</option>
                            <option value="Bataan">Bataan</option>
                            <option value="Batanes">Batanes</option>
                            <option value="Batangas">Batangas</option>
                            <option value="Benguet">Benguet</option>
                            <option value="Biliran">Biliran</option>
                            <option value="Bohol">Bohol</option>
                            <option value="Bukidnon">Bukidnon</option>
                            <option value="Bulacan">Bulacan</option>
                            <option value="Cagayan">Cagayan</option>
                            <option value="Camarines Norte">Camarines Norte</option>
                            <option value="Camarines Sur">Camarines Sur</option>
                            <option value="Camiguin">Camiguin</option>
                            <option value="Capiz">Capiz</option>
                            <option value="Catanduanes">Catanduanes</option>
                            <option value="Cavite">Cavite</option>
                            <option value="Cebu">Cebu</option>
                            <option value="Compostela Valley">Compostela Valley</option>
                            <option value="Cotabato">Cotabato</option>
                            <option value="Davao del Norte">Davao del Norte</option>
                            <option value="Davao del Sur">Davao del Sur</option>
                            <option value="Davao Occidental">Davao Occidental</option>
                            <option value="Davao Oriental">Davao Oriental</option>
                            <option value="Dinagat Islands">Dinagat Islands</option>
                            <option value="Eastern Samar">Eastern Samar</option>
                            <option value="Guimaras">Guimaras</option>
                            <option value="Ifugao">Ifugao</option>
                            <option value="Ilocos Norte">Ilocos Norte</option>
                            <option value="Ilocos Sur">Ilocos Sur</option>
                            <option value="Iloilo">Iloilo</option>
                            <option value="Isabela">Isabela</option>
                            <option value="Kalinga">Kalinga</option>
                            <option value="La Union">La Union</option>
                            <option value="Laguna">Laguna</option>
                            <option value="Lanao del Norte">Lanao del Norte</option>
                            <option value="Lanao del Sur">Lanao del Sur</option>
                            <option value="Leyte">Leyte</option>
                            <option value="Maguindanao">Maguindanao</option>
                            <option value="Marinduque">Marinduque</option>
                            <option value="Masbate">Masbate</option>
                            <option value="Misamis Occidental">Misamis Occidental</option>
                            <option value="Misamis Oriental">Misamis Oriental</option>
                            <option value="Mountain Province">Mountain Province</option>
                            <option value="Negros Occidental">Negros Occidental</option>
                            <option value="Negros Oriental">Negros Oriental</option>
                            <option value="Northern Samar">Northern Samar</option>
                            <option value="Nueva Ecija">Nueva Ecija</option>
                            <option value="Nueva Vizcaya">Nueva Vizcaya</option>
                            <option value="Occidental Mindoro">Occidental Mindoro</option>
                            <option value="Oriental Mindoro">Oriental Mindoro</option>
                            <option value="Palawan">Palawan</option>
                            <option value="Pampanga">Pampanga</option>
                            <option value="Pangasinan">Pangasinan</option>
                            <option value="Quezon">Quezon</option>
                            <option value="Quirino">Quirino</option>
                            <option value="Rizal">Rizal</option>
                            <option value="Romblon">Romblon</option>
                            <option value="Samar">Samar</option>
                            <option value="Sarangani">Sarangani</option>
                            <option value="Siquijor">Siquijor</option>
                            <option value="Sorsogon">Sorsogon</option>
                            <option value="South Cotabato">South Cotabato</option>
                            <option value="Southern Leyte">Southern Leyte</option>
                            <option value="Sultan Kudarat">Sultan Kudarat</option>
                            <option value="Sulu">Sulu</option>
                            <option value="Surigao del Norte">Surigao del Norte</option>
                            <option value="Surigao del Sur">Surigao del Sur</option>
                            <option value="Tarlac">Tarlac</option>
                            <option value="Tawi-Tawi">Tawi-Tawi</option>
                            <option value="Zambales">Zambales</option>
                            <option value="Zamboanga del Norte">Zamboanga del Norte</option>
                            <option value="Zamboanga del Sur">Zamboanga del Sur</option>
                            <option value="Zamboanga Sibugay">Zamboanga Sibugay</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- City/Municipality -->
            <div class="q-card">
                <div class="q-card-header">
                    <div>
                        <div class="q-badge">S1.5</div>
                        <div class="q-title">City/Municipality</div>
                        <div class="q-hint">Enter city or municipality name</div>
                    </div>
                    <span class="q-required">Required</span>
                </div>
                <div class="q-members">
                    <div class="member-row">
                        <input type="text" id="city" class="q-input" placeholder="Enter city or municipality name" required>
                    </div>
                </div>
            </div>

            <!-- Barangay -->
            <div class="q-card">
                <div class="q-card-header">
                    <div>
                        <div class="q-badge">S1.6</div>
                        <div class="q-title">Barangay</div>
                        <div class="q-hint">Select barangay</div>
                    </div>
                    <span class="q-required">Required</span>
                </div>
                <div class="q-members">
                    <div class="member-row">
                        <select id="barangay" class="q-input" required>
                            <option value="">Select barangay</option>
                            ${Array.from({length: 193}, (_, i) => `<option value="${i + 1}">${i + 1}</option>`).join('')}
                        </select>
                    </div>
                </div>
            </div>

            <!-- Address Details -->
            <div class="q-card">
                <div class="q-card-header">
                    <div>
                        <div class="q-badge">S1.7</div>
                        <div class="q-title">Address Details</div>
                        <div class="q-hint">Complete address information</div>
                    </div>
                    <span class="q-required">Required</span>
                </div>
                <div class="q-members">
                    <div class="member-row">
                        <input type="text" id="address_unit" class="q-input" placeholder="Room/Floor/Unit No. and Building Name" required>
                    </div>
                    <div class="member-row">
                        <input type="text" id="address_house" class="q-input" placeholder="House/Lot and Block No." required>
                    </div>
                    <div class="member-row">
                        <input type="text" id="address_street" class="q-input" placeholder="Street Name" required>
                    </div>
                </div>
            </div>

            <!-- Time Start -->
            <div class="q-card">
                <div class="q-card-header">
                    <div>
                        <div class="q-badge">S1.8</div>
                        <div class="q-title">Time Started</div>
                        <div class="q-hint">Time when interview was started</div>
                    </div>
                </div>
                <div class="q-members">
                    <div class="member-row">
                        <input type="text" class="q-input" value="${this.householdData.time_start}" readonly style="background: #F0F4F8; cursor: not-allowed;">
                    </div>
                </div>
            </div>

            <!-- Interviewer Information -->
            <div class="q-card">
                <div class="q-card-header">
                    <div>
                        <div class="q-badge">S1.9</div>
                        <div class="q-title">Interviewer Information</div>
                        <div class="q-hint">Surname, First Name, Middle Initial and date</div>
                    </div>
                    <span class="q-required">Required</span>
                </div>
                <div class="q-members">
                    <div class="member-row" style="display: grid; grid-template-columns: 1fr 1fr 80px; gap: 8px;">
                        <input type="text" id="interviewer_surname" class="q-input" placeholder="Surname" required>
                        <input type="text" id="interviewer_firstname" class="q-input" placeholder="First Name" required>
                        <input type="text" id="interviewer_mi" class="q-input" placeholder="M.I." maxlength="5">
                    </div>
                    <div class="member-row">
                        <input type="date" id="interviewer_date" class="q-input" required>
                    </div>
                </div>
            </div>

            <!-- Supervisor Information -->
            <div class="q-card">
                <div class="q-card-header">
                    <div>
                        <div class="q-badge">S1.10</div>
                        <div class="q-title">Supervisor Information</div>
                        <div class="q-hint">Surname, First Name, Middle Initial and date</div>
                    </div>
                    <span class="q-required">Required</span>
                </div>
                <div class="q-members">
                    <div class="member-row" style="display: grid; grid-template-columns: 1fr 1fr 80px; gap: 8px;">
                        <input type="text" id="supervisor_surname" class="q-input" placeholder="Surname" required>
                        <input type="text" id="supervisor_firstname" class="q-input" placeholder="First Name" required>
                        <input type="text" id="supervisor_mi" class="q-input" placeholder="M.I." maxlength="5">
                    </div>
                    <div class="member-row">
                        <input type="date" id="supervisor_date" class="q-input" required>
                    </div>
                </div>
            </div>

            <!-- Encoder Information -->
            <div class="q-card">
                <div class="q-card-header">
                    <div>
                        <div class="q-badge">S1.11</div>
                        <div class="q-title">Encoder Information</div>
                        <div class="q-hint">Auto-filled with current user</div>
                    </div>
                </div>
                <div class="q-members">
                    <div class="member-row">
                        <input type="text" class="q-input" value="${this.householdData.encoder_name}" readonly style="background: #F0F4F8; cursor: not-allowed;">
                    </div>
                    <div class="member-row">
                        <input type="text" class="q-input" value="${this.householdData.date_encoded}" readonly style="background: #F0F4F8; cursor: not-allowed;">
                    </div>
                </div>
            </div>

            <!-- Total Household Members -->
            <div class="q-card">
                <div class="q-card-header">
                    <div>
                        <div class="q-badge">S1.12</div>
                        <div class="q-title">Total Number of Household Members</div>
                        <div class="q-hint">How many members in this household? (1-20)</div>
                    </div>
                    <span class="q-required">Required</span>
                </div>
                <div class="q-members">
                    <div class="member-row">
                        <select id="total_members" class="q-input" required>
                            <option value="">Select total members</option>
                            ${Array.from({length: 20}, (_, i) => `<option value="${i+1}">${i+1}</option>`).join('')}
                        </select>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="form-navigation" style="display: flex; gap: 12px; margin-top: 40px; justify-content: flex-end;">
                <button id="btnNext" class="btn-next" style="background: linear-gradient(135deg, var(--sky), #2E78C9); color: #fff; border: none; padding: 12px 28px; border-radius: 100px; cursor: pointer; font-weight: 600; font-size: 15px;">
                    <i class="fas fa-arrow-right" style="margin-right: 8px;"></i> Next: DEMOGRAPHIC CHARACTERISTICS
                </button>
            </div>
        `;
        
        $('#step-1').html(html);
    }

    renderStep2() {
        console.log('renderStep2 called');
        const html = `
            <div class="section-label">
                <div class="dot"></div>
                <h3>Step 2: Member Details & Demographics (Q1-Q14)</h3>
            </div>

            <div id="questions-container"></div>

            <!-- Navigation Buttons -->
            <div class="form-navigation" style="display: flex; gap: 12px; margin-top: 40px; justify-content: space-between;">
                <button id="btnPrev" class="btn-prev" style="background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); padding: 12px 28px; border-radius: 100px; cursor: pointer; font-weight: 600; font-size: 15px;">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back
                </button>
                <button id="btnNext" class="btn-next" style="background: linear-gradient(135deg, var(--sky), #2E78C9); color: #fff; border: none; padding: 12px 28px; border-radius: 100px; cursor: pointer; font-weight: 600; font-size: 15px;">
                    <i class="fas fa-arrow-right" style="margin-right: 8px;"></i> Next: ECONOMIC ACTIVITY & HEALTH INFORMATION
                </button>
            </div>
        `;
        
        $('#step-2').html(html);
        console.log('about to call renderQuestionsForStep for step 2');
        this.renderQuestionsForStep(2, 'Q1', 'Q14');
    }

    renderStep3() {
        const html = `
            <div class="section-label">
                <div class="dot"></div>
                <h3>Step 3: ECONOMIC ACTIVITY & HEALTH INFORMATION</h3>
            </div>

            <div id="questions-container"></div>

            <!-- Navigation Buttons -->
            <div class="form-navigation" style="display: flex; gap: 12px; margin-top: 40px; justify-content: space-between;">
                <button id="btnPrev" class="btn-prev" style="background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); padding: 12px 28px; border-radius: 100px; cursor: pointer; font-weight: 600; font-size: 15px;">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back
                </button>
                <button id="btnSubmit" class="btn-submit" style="background: linear-gradient(135deg, var(--mint), #2DA897); color: #fff; border: none; padding: 12px 28px; border-radius: 100px; cursor: pointer; font-weight: 600; font-size: 15px;">
                    <i class="fas fa-check" style="margin-right: 8px;"></i> Submit
                </button>
            </div>
        `;
        
        $('#step-3').html(html);
        this.renderQuestionsForStep(3, 'Q15', 'Q25');
    }


    renderStep4() {
        const html = `
            <div class="section-label">
                <div class="dot"></div>
                <h3>Step 4: (Coming Soon)</h3>
            </div>

            <div id="questions-container"></div>

            <!-- Navigation Buttons -->
            <div class="form-navigation" style="display: flex; gap: 12px; margin-top: 40px; justify-content: space-between;">
                <button id="btnPrev" class="btn-prev" style="background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); padding: 12px 28px; border-radius: 100px; cursor: pointer; font-weight: 600; font-size: 15px;">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back
                </button>
                <button id="btnNext" class="btn-next" style="background: linear-gradient(135deg, var(--sky), #2E78C9); color: #fff; border: none; padding: 12px 28px; border-radius: 100px; cursor: pointer; font-weight: 600; font-size: 15px;">
                    <i class="fas fa-arrow-right" style="margin-right: 8px;"></i> Next
                </button>
            </div>
        `;
        
        $('#step-4').html(html);
    }

    renderStep5() {
        const html = `
            <div class="section-label">
                <div class="dot"></div>
                <h3>Step 5: (Coming Soon)</h3>
            </div>

            <div id="questions-container"></div>

            <!-- Navigation Buttons -->
            <div class="form-navigation" style="display: flex; gap: 12px; margin-top: 40px; justify-content: space-between;">
                <button id="btnPrev" class="btn-prev" style="background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); padding: 12px 28px; border-radius: 100px; cursor: pointer; font-weight: 600; font-size: 15px;">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back
                </button>
                <button id="btnNext" class="btn-next" style="background: linear-gradient(135deg, var(--sky), #2E78C9); color: #fff; border: none; padding: 12px 28px; border-radius: 100px; cursor: pointer; font-weight: 600; font-size: 15px;">
                    <i class="fas fa-arrow-right" style="margin-right: 8px;"></i> Next
                </button>
            </div>
        `;
        
        $('#step-5').html(html);
    }

    renderStep6() {
        const html = `
            <div class="section-label">
                <div class="dot"></div>
                <h3>Step 6: (Coming Soon)</h3>
            </div>

            <div id="questions-container"></div>

            <!-- Navigation Buttons -->
            <div class="form-navigation" style="display: flex; gap: 12px; margin-top: 40px; justify-content: space-between;">
                <button id="btnPrev" class="btn-prev" style="background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); padding: 12px 28px; border-radius: 100px; cursor: pointer; font-weight: 600; font-size: 15px;">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back
                </button>
                <button id="btnSubmit" class="btn-submit" style="background: linear-gradient(135deg, var(--mint), #2DA897); color: #fff; border: none; padding: 12px 28px; border-radius: 100px; cursor: pointer; font-weight: 600; font-size: 15px;">
                    <i class="fas fa-check" style="margin-right: 8px;"></i> Submit
                </button>
            </div>
        `;
        
        $('#step-6').html(html);
    }

    renderQuestionsForStep(step, startQ, endQ) {
        const container = $(`#step-${step} #questions-container`);
        console.log('renderQuestionsForStep called:', { step, startQ, endQ, containerFound: container.length > 0 });
        container.html('');
        
        // Get question IDs in range
        const questionIds = {};
        questionIds['Q1'] = 1; questionIds['Q2'] = 2; questionIds['Q3'] = 3; questionIds['Q4'] = 4;
        questionIds['Q5'] = 5; questionIds['Q6'] = 6; questionIds['Q7'] = 7; questionIds['Q7b'] = 7.5;
        questionIds['Q8'] = 8; questionIds['Q9'] = 9; questionIds['Q10'] = 10; questionIds['Q11'] = 11;
        questionIds['Q12'] = 12; questionIds['Q13'] = 13; questionIds['Q14'] = 14; questionIds['Q15'] = 15;
        questionIds['Q16'] = 16; questionIds['Q17'] = 17; questionIds['Q18'] = 18; questionIds['Q19'] = 19;
        questionIds['Q20'] = 20; questionIds['Q21'] = 21; questionIds['Q22'] = 22; questionIds['Q23'] = 23;
        questionIds['Q24'] = 24; questionIds['Q25'] = 25;
        
        const startNum = questionIds[startQ];
        const endNum = questionIds[endQ];
        
        let questionsRendered = 0;

        this.QUESTIONS.forEach((q, qi) => {
            const qNum = questionIds[q.id];
            if (qNum < startNum || qNum > endNum) return;
            
            // Skip Q4 (age) - it's auto-computed and displayed with Q5
            if (q.computed) return;
            
            questionsRendered++;

            const card = document.createElement('div');
            card.className = 'q-card';
            card.id = `card_${q.id}`;
            card.style.animationDelay = (qi * 0.04) + 's';

            const header = `
                <div class="q-card-header">
                    <div>
                        <div class="q-badge">${q.badge}</div>
                        <div class="q-title">${q.label}</div>
                        <div class="q-hint">${q.hint}</div>
                    </div>
                    ${q.required ? '<span class="q-required">Required</span>' : ''}
                </div>
            `;

            card.innerHTML = header + `<div class="q-members" id="members_${q.id}"></div>`;
            container.append(card);

            this.renderMemberRowsForStep(q, step);
        });
        
        console.log('renderQuestionsForStep completed:', { step, startQ, endQ, questionsRendered });
    }

    renderMemberRowsForStep(q, step) {
        const wrap = $(`#members_${q.id}`);
        if (!wrap.length) return;
        wrap.html('');

        // Check if any member will have rows visible
        let hasVisibleRows = false;
        
        if (q.noMemberRows) {
            hasVisibleRows = true;
        } else {
            for (let mi = 0; mi < this.memberCount; mi++) {
                if (this.isMemberEnabledForQuestion(q, mi)) {
                    hasVisibleRows = true;
                    break;
                }
            }
        }

        // Show/hide card based on visibility
        const card = $(`#card_${q.id}`);
        if (card.length) {
            card.css('display', hasVisibleRows ? '' : 'none');
        }

        // Handle noMemberRows questions - render once without member labels
        if (q.noMemberRows) {
            const row = document.createElement('div');
            row.className = 'member-row';
            let input = null;
            if (q.type === 'text' || q.type === 'number') {
                input = document.createElement('input');
                input.type = q.type;
                input.className = 'q-input';
                input.placeholder = q.placeholder || '';
                input.addEventListener('input', (e) => {
                    this.memberData[0] = this.memberData[0] || {};
                    this.memberData[0][q.id] = e.target.value;
                    e.target.classList.toggle('has-val', !!e.target.value);
                    this.QUESTIONS.forEach(dq => {
                        if (dq.showIf && dq.showIf.id === q.id) {
                            this.renderMemberRowsForStep(dq, step);
                        }
                    });
                });
            } else if (q.type === 'select') {
                input = document.createElement('select');
                input.className = 'q-input';
                const emptyOpt = document.createElement('option');
                emptyOpt.value = '';
                emptyOpt.textContent = 'Select...';
                input.appendChild(emptyOpt);
                q.options.forEach(opt => {
                    const option = document.createElement('option');
                    option.value = opt;
                    option.textContent = opt;
                    input.appendChild(option);
                });
                input.addEventListener('change', (e) => {
                    this.memberData[0] = this.memberData[0] || {};
                    this.memberData[0][q.id] = e.target.value;
                    e.target.classList.toggle('has-val', !!e.target.value);
                    this.QUESTIONS.forEach(dq => {
                        if (dq.showIf && dq.showIf.id === q.id) {
                            this.renderMemberRowsForStep(dq, step);
                        }
                    });
                });
            }
            if (input) row.appendChild(input);
            wrap.append(row);
            return;
        }

        // Member rows logic - only render enabled members (hide disabled ones)
        for (let mi = 0; mi < this.memberCount; mi++) {
            if (!this.isMemberEnabledForQuestion(q, mi)) continue;

            const val = (this.memberData[mi] || {})[q.id] || '';
            const name = this.memberName(mi);

            const row = document.createElement('div');
            row.className = 'member-row';

            const lbl = document.createElement('div');
            lbl.className = 'member-label';
            lbl.dataset.mi = mi;
            lbl.innerHTML = `Member ${mi + 1}${name ? `<span class="m-name">${name}</span>` : ''}`;
            row.appendChild(lbl);

            let input = null;

            // Q1: Name sub-fields (Surname, First Name, MI, Suffix)
            if (q.id === 'Q1') {
                const surname = (this.memberData[mi] || {})['Q1_surname'] || '';
                const firstname = (this.memberData[mi] || {})['Q1_firstname'] || '';
                const middleInit = (this.memberData[mi] || {})['Q1_mi'] || '';
                const suffix = (this.memberData[mi] || {})['Q1_suffix'] || '';

                const nameContainer = document.createElement('div');
                nameContainer.style.cssText = 'display: grid; grid-template-columns: 1fr 1fr 60px 70px; gap: 6px; flex: 1;';

                const surnameInput = document.createElement('input');
                surnameInput.type = 'text';
                surnameInput.className = 'q-input' + (surname ? ' has-val' : '');
                surnameInput.placeholder = 'Surname';
                surnameInput.value = surname;

                const firstNameInput = document.createElement('input');
                firstNameInput.type = 'text';
                firstNameInput.className = 'q-input' + (firstname ? ' has-val' : '');
                firstNameInput.placeholder = 'First Name';
                firstNameInput.value = firstname;

                const miInput = document.createElement('input');
                miInput.type = 'text';
                miInput.className = 'q-input' + (middleInit ? ' has-val' : '');
                miInput.placeholder = 'M.I.';
                miInput.value = middleInit;
                miInput.maxLength = 5;

                const suffixInput = document.createElement('input');
                suffixInput.type = 'text';
                suffixInput.className = 'q-input' + (suffix ? ' has-val' : '');
                suffixInput.placeholder = 'Suffix';
                suffixInput.value = suffix;
                suffixInput.maxLength = 10;

                const updateQ1 = () => {
                    const s = surnameInput.value.trim();
                    const f = firstNameInput.value.trim();
                    const m = miInput.value.trim();
                    const x = suffixInput.value.trim();
                    const composed = [s, f, m ? m + '.' : '', x].filter(Boolean).join(', ').replace(/,\s*$/, '');
                    this.setMemberVal(mi, 'Q1', composed);
                    this.setMemberVal(mi, 'Q1_surname', surnameInput.value);
                    this.setMemberVal(mi, 'Q1_firstname', firstNameInput.value);
                    this.setMemberVal(mi, 'Q1_mi', miInput.value);
                    this.setMemberVal(mi, 'Q1_suffix', suffixInput.value);
                    this.refreshMemberLabels();
                };

                [surnameInput, firstNameInput, miInput, suffixInput].forEach(inp => {
                    inp.addEventListener('input', (e) => {
                        e.target.classList.toggle('has-val', !!e.target.value);
                        updateQ1();
                    });
                });

                nameContainer.appendChild(surnameInput);
                nameContainer.appendChild(firstNameInput);
                nameContainer.appendChild(miInput);
                nameContainer.appendChild(suffixInput);
                row.appendChild(nameContainer);
            }
            // Q5: Date of Birth + computed age in single row
            else if (q.id === 'Q5') {
                const dobContainer = document.createElement('div');
                dobContainer.style.cssText = 'display: flex; gap: 10px; flex: 1; align-items: center;';

                const dateInput = document.createElement('input');
                dateInput.type = 'date';
                dateInput.className = 'q-input' + (val ? ' has-val' : '');
                dateInput.value = val;
                dateInput.style.flex = '1';

                const ageDisplay = document.createElement('span');
                ageDisplay.style.cssText = 'min-width: 70px; padding: 8px 12px; background: rgba(76,175,80,0.08); border-radius: 10px; text-align: center; font-weight: 700; color: #4CAF50; font-size: 13px; white-space: nowrap;';

                const ageVal = this.computeAge(val);
                ageDisplay.textContent = ageVal.display || '\u2014';

                dateInput.addEventListener('change', (e) => {
                    const dob = e.target.value;
                    this.setMemberVal(mi, 'Q5', dob);
                    e.target.classList.toggle('has-val', !!dob);

                    const computed = this.computeAge(dob);
                    ageDisplay.textContent = computed.display || '\u2014';
                    this.setMemberVal(mi, 'Q4', computed.years !== '' ? String(computed.years) : '');

                    // Re-render age-dependent questions on this step
                    this.refreshAgeDependentQuestions(mi, step);
                });

                dobContainer.appendChild(dateInput);
                dobContainer.appendChild(ageDisplay);
                row.appendChild(dobContainer);
            }
            else if (q.type === 'text' || q.type === 'number') {
                input = document.createElement('input');
                input.type = q.type;
                input.className = 'q-input' + (val ? ' has-val' : '');
                input.placeholder = q.placeholder || '';
                input.value = val;

                input.addEventListener('input', (e) => {
                    this.setMemberVal(mi, q.id, e.target.value);
                    e.target.classList.toggle('has-val', !!e.target.value);
                    // Refresh Q16-Q18 when Q15 changes
                    if (q.id === 'Q15') {
                        ['Q16', 'Q17', 'Q18'].forEach(qId => {
                            const dq = this.QUESTIONS.find(quest => quest.id === qId);
                            if (dq) this.renderMemberRowsForStep(dq, step);
                        });
                    }
                    // Refresh Q23-Q25 when Q22 changes
                    if (q.id === 'Q22') {
                        ['Q23', 'Q24', 'Q25'].forEach(qId => {
                            const dq = this.QUESTIONS.find(quest => quest.id === qId);
                            if (dq) this.renderMemberRowsForStep(dq, step);
                        });
                    }
                });
            } else if (q.type === 'select') {
                input = document.createElement('select');
                input.className = 'q-input' + (val ? ' has-val' : '');

                const emptyOpt = document.createElement('option');
                emptyOpt.value = '';
                emptyOpt.textContent = 'Select...';
                input.appendChild(emptyOpt);

                q.options.forEach(opt => {
                    const option = document.createElement('option');
                    option.value = opt;
                    option.textContent = opt;
                    if (val === opt) option.selected = true;
                    input.appendChild(option);
                });

                input.addEventListener('change', (e) => {
                    this.setMemberVal(mi, q.id, e.target.value);
                    e.target.classList.toggle('has-val', !!e.target.value);

                    // Refresh dependent questions (showIf)
                    this.QUESTIONS.forEach(dq => {
                        if (dq.showIf && dq.showIf.id === q.id) {
                            this.renderMemberRowsForStep(dq, step);
                        }
                    });

                    // Refresh Q13/Q14 when Q12 changes
                    if (q.id === 'Q12') {
                        ['Q13', 'Q14'].forEach(qId => {
                            const dq = this.QUESTIONS.find(quest => quest.id === qId);
                            if (dq) this.renderMemberRowsForStep(dq, step);
                        });
                    }
                    // Refresh Q17-Q18 when Q16 changes
                    if (q.id === 'Q16') {
                        ['Q17', 'Q18'].forEach(qId => {
                            const dq = this.QUESTIONS.find(quest => quest.id === qId);
                            if (dq) this.renderMemberRowsForStep(dq, step);
                        });
                    }
                });
            } else if (q.type === 'radio') {
                const grp = document.createElement('div');
                grp.className = 'radio-group';

                q.options.forEach(opt => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'radio-btn' + (val === opt ? ' selected' : '');
                    btn.textContent = opt;

                    btn.addEventListener('click', () => {
                        this.setMemberVal(mi, q.id, opt);
                        grp.querySelectorAll('.radio-btn').forEach(b => b.classList.remove('selected'));
                        btn.classList.add('selected');

                        // Refresh dependent questions
                        this.QUESTIONS.forEach(dq => {
                            if (dq.showIf && dq.showIf.id === q.id) {
                                this.renderMemberRowsForStep(dq, step);
                            }
                        });

                        // If sex changes, refresh sex-dependent questions
                        if (q.id === 'Q3') {
                            Object.keys(this.sexRequirements).forEach(qId => {
                                const dq = this.QUESTIONS.find(quest => quest.id === qId);
                                if (dq) this.renderMemberRowsForStep(dq, step);
                            });
                        }
                    });

                    grp.appendChild(btn);
                });

                input = grp;
            }

            if (input) row.appendChild(input);
            wrap.append(row);
        }
    }

    computeAge(dob) {
        if (!dob) return { years: '', display: '' };
        const birth = new Date(dob);
        if (isNaN(birth.getTime())) return { years: '', display: '' };
        const today = new Date();
        let years = today.getFullYear() - birth.getFullYear();
        let months = today.getMonth() - birth.getMonth();
        if (today.getDate() < birth.getDate()) months--;
        if (months < 0) { years--; months += 12; }
        if (years < 0) return { years: 0, display: 'Invalid' };
        if (years < 1) return { years: 0, display: `${Math.max(0, months)} mo` };
        return { years, display: `${years} yrs` };
    }

    isMemberEnabledForQuestion(q, mi) {
        if (!this.isVisible(q, mi)) return false;

        if (this.sexRequirements && this.sexRequirements[q.id]) {
            const sex = (this.memberData[mi] || {})['Q3'];
            if (sex !== this.sexRequirements[q.id]) return false;
        }

        const age = (this.memberData[mi] || {})['Q4'];
        if (this.ageRequirements[q.id]) {
            // If age is not yet computed (no DOB), hide age-dependent questions
            if (age === '' || age === undefined || age === null) return false;
            if (!this.ageRequirements[q.id](age)) return false;
        }

        if (['Q13', 'Q14'].includes(q.id)) {
            const q12Val = (this.memberData[mi] || {})['Q12'];
            if (q12Val !== 'Yes, public' && q12Val !== 'Yes, private') return false;
        }

        return true;
    }

    refreshAgeDependentQuestions(memberIdx, step) {
        Object.keys(this.ageRequirements).forEach(qId => {
            const q = this.QUESTIONS.find(quest => quest.id === qId);
            if (q) this.renderMemberRowsForStep(q, step);
        });
    }

    isVisible(q, memberIdx) {
        if (!q.showIf) return true;
        const val = (this.memberData[memberIdx] || {})[q.showIf.id];
        
        if (typeof q.showIf.val === 'function') {
            return q.showIf.val(val);
        }
        
        if (Array.isArray(q.showIf.val)) {
            return q.showIf.val.includes(val);
        }
        
        return val === q.showIf.val;
    }
    

    isFieldEnabled(q, memberIdx) {
        // Check if field should be enabled based on age requirements and visibility
        if (!this.isVisible(q, memberIdx)) return false;
        
        // Check sex requirement
        if (this.sexRequirements && this.sexRequirements[q.id]) {
            const sex = (this.memberData[memberIdx] || {})['Q3'];
            if (sex !== this.sexRequirements[q.id]) return false;
        }
        
        // Check age requirement
        const age = (this.memberData[memberIdx] || {})['Q4'];
        if (this.ageRequirements[q.id]) {
            if (age === '' || age === undefined || age === null) return false;
            if (!this.ageRequirements[q.id](age)) {
                return false;
            }
        }
        
        // Check if Q13 or Q14 should be disabled based on Q12's value
        if (['Q13', 'Q14'].includes(q.id)) {
            const q12Val = (this.memberData[memberIdx] || {})['Q12'];
            if (q12Val !== 'Yes, public' && q12Val !== 'Yes, private') {
                return false;
            }
        }
        
        return true;
    }

    shouldRequireField(q, memberIdx) {
        if (!q.required) return false;
        if (q.computed) return false;
        if (!this.isVisible(q, memberIdx)) return false;
        
        const age = (this.memberData[memberIdx] || {})['Q4'];
        if (this.ageRequirements[q.id]) {
            if (age === '' || age === undefined || age === null) return false;
            if (!this.ageRequirements[q.id](age)) return false;
        }
        
        return true;
    }

    memberName(memberIdx) {
        const data = this.memberData[memberIdx] || {};
        const surname = data['Q1_surname'] || '';
        const firstname = data['Q1_firstname'] || '';
        if (surname || firstname) {
            return `${surname}${surname && firstname ? ', ' : ''}${firstname}`.trim();
        }
        return data['Q1'] || '';
    }

    setMemberVal(memberIdx, questionId, value) {
        if (!this.memberData[memberIdx]) {
            this.memberData[memberIdx] = {};
        }
        this.memberData[memberIdx][questionId] = value;
        this.updateProgress();
    }

    refreshMemberLabels() {
        // Update member labels with names from Q1
        document.querySelectorAll('.member-label').forEach(lbl => {
            const mi = parseInt(lbl.dataset.mi);
            if (mi !== undefined) {
                const name = this.memberName(mi);
                const nameSpan = lbl.querySelector('.m-name') || document.createElement('span');
                nameSpan.className = 'm-name';
                if (name) {
                    nameSpan.textContent = name;
                    if (!lbl.querySelector('.m-name')) {
                        lbl.appendChild(nameSpan);
                    }
                }
            }
        });
    }

    setMemberCount(count) {
        const oldCount = this.memberCount;
        this.memberCount = Math.max(1, Math.min(20, count));

        // Extend or trim memberData array
        if (this.memberCount > oldCount) {
            for (let i = oldCount; i < this.memberCount; i++) {
                this.memberData[i] = {};
            }
        } else if (this.memberCount < oldCount) {
            this.memberData.length = this.memberCount;
        }

        // Update UI
        $('#memberCount').text(this.memberCount);
        if (this.memberCount > 1) {
            $('#btnRemove').show();
        } else {
            $('#btnRemove').hide();
        }

        // Re-render current step when member count changes
        if (this.currentStep === 2) {
            this.renderStep2();
        } else if (this.currentStep === 3) {
            this.renderStep3();
        }
    }

    addMember() {
        if (this.memberCount < 20) {
            this.setMemberCount(this.memberCount + 1);
        }
    }

    removeMember() {
        if (this.memberCount > 1) {
            this.setMemberCount(this.memberCount - 1);
        }
    }

    nextStep() {
        // Validate current step
        console.log('nextStep called, currentStep:', this.currentStep);
        if (!this.validateStep(this.currentStep)) {
            return;
        }

        if (this.currentStep < this.totalSteps) {
            this.currentStep++;
            this.updateStepDisplay();
        }
    }

    previousStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.updateStepDisplay();
        }
    }

    updateStepDisplay() {
        console.log('updateStepDisplay called, currentStep:', this.currentStep);
        $('.form-step').removeClass('active');
        $(`#step-${this.currentStep}`).addClass('active');

        // Show toolbar and render appropriate step
        if (this.currentStep === 2) {
            console.log('Rendering Step 2');
            $('#toolbar').show();
            $('#memberCount').text(this.memberCount);
            this.renderStep2();
        } else if (this.currentStep === 3) {
            console.log('Rendering Step 3');
            $('#toolbar').show();
            $('#memberCount').text(this.memberCount);
            this.renderStep3();
        } else if (this.currentStep === 4) {
            $('#toolbar').show();
            $('#memberCount').text(this.memberCount);
            this.renderStep4();
        } else if (this.currentStep === 5) {
            $('#toolbar').show();
            $('#memberCount').text(this.memberCount);
            this.renderStep5();
        } else if (this.currentStep === 6) {
            $('#toolbar').show();
            $('#memberCount').text(this.memberCount);
            this.renderStep6();
        } else {
            $('#toolbar').hide();
        }

        this.updateProgress();
        
        // Scroll to top
        setTimeout(() => {
            $('.form-body').scrollTop(0);
        }, 100);
    }

    validateStep(step) {
        if (step === 1) {
            const required = [
                { id: 'household_code_number', name: 'Household Code Number' },
                { id: 'respondent_surname', name: 'Respondent Surname' },
                { id: 'respondent_firstname', name: 'Respondent First Name' },
                { id: 'household_head_surname', name: 'Household Head Surname' },
                { id: 'household_head_firstname', name: 'Household Head First Name' },
                { id: 'province', name: 'Province' },
                { id: 'city', name: 'City/Municipality' },
                { id: 'barangay', name: 'Barangay' },
                { id: 'address_unit', name: 'Address (Unit/Building)' },
                { id: 'address_house', name: 'Address (House/Lot)' },
                { id: 'address_street', name: 'Address (Street)' },
                { id: 'interviewer_surname', name: 'Interviewer Surname' },
                { id: 'interviewer_firstname', name: 'Interviewer First Name' },
                { id: 'interviewer_date', name: 'Interviewer Date' },
                { id: 'supervisor_surname', name: 'Supervisor Surname' },
                { id: 'supervisor_firstname', name: 'Supervisor First Name' },
                { id: 'supervisor_date', name: 'Supervisor Date' },
                { id: 'total_members', name: 'Total Members' }
            ];

            for (let field of required) {
                const val = $(`#${field.id}`).val();
                if (!val || val.trim() === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Information',
                        text: `Please fill in: ${field.name}`,
                        confirmButtonColor: '#2caf33',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
            }
            return true;
        } else if (step === 2) {
            // Validate Step 2: Q1-Q14 (skip Q4 - auto-computed)
            const questionIds = ['Q1', 'Q2', 'Q3', 'Q5', 'Q6', 'Q7', 'Q7b', 'Q8', 'Q9', 'Q10', 'Q11', 'Q12', 'Q13', 'Q14'];
            for (let mi = 0; mi < this.memberCount; mi++) {
                // Validate Q1 sub-fields specifically
                const surname = (this.memberData[mi] || {})['Q1_surname'] || '';
                const firstname = (this.memberData[mi] || {})['Q1_firstname'] || '';
                if (!surname.trim()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Incomplete Member Information',
                        text: `Member ${mi + 1}: Please fill in Surname`,
                        confirmButtonColor: '#2caf33',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
                if (!firstname.trim()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Incomplete Member Information',
                        text: `Member ${mi + 1}: Please fill in First Name`,
                        confirmButtonColor: '#2caf33',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                for (let q of this.QUESTIONS) {
                    if (!questionIds.includes(q.id)) continue;
                    if (q.id === 'Q1') continue; // Already validated above
                    if (q.computed) continue; // Skip auto-computed fields
                    
                    // Only validate if field should be required (has required flag AND is enabled)
                    if (this.shouldRequireField(q, mi)) {
                        const val = (this.memberData[mi] || {})[q.id];
                        if (!val || (typeof val === 'string' && val.trim() === '')) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Incomplete Member Information',
                                text: `Member ${mi + 1}: Please fill in ${q.label}`,
                                confirmButtonColor: '#2caf33',
                                confirmButtonText: 'OK'
                            });
                            return false;
                        }
                    }
                }
            }
            return true;
        } else if (step === 3) {
            // Validate Step 3: Q15 (Monthly Income) and Q16-Q18 (Source/Work)
            for (let mi = 0; mi < this.memberCount; mi++) {
                const age = (this.memberData[mi] || {})['Q4'];
                if (age && this.ageRequirements['Q15'] && this.ageRequirements['Q15'](age)) {
                    const val = (this.memberData[mi] || {})['Q15'];
                    if (val === '' || val === undefined) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Missing Income Information',
                            text: `Member ${mi + 1}: Please fill in Monthly Income (write 0 if none)`,
                            confirmButtonColor: '#2caf33',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                }

                const q15Val = (this.memberData[mi] || {})['Q15'];
                if (q15Val && parseInt(q15Val) > 0) {
                    const q16Val = (this.memberData[mi] || {})['Q16'];
                    if (!q16Val) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Missing Income Information',
                            text: `Member ${mi + 1}: Please fill in Source of Income`,
                            confirmButtonColor: '#2caf33',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    if (q16Val === 'Employment' || q16Val === 'Business') {
                        const q17Val = (this.memberData[mi] || {})['Q17'];
                        if (!q17Val) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Missing Employment Information',
                                text: `Member ${mi + 1}: Please fill in Status of Work/Business`,
                                confirmButtonColor: '#2caf33',
                                confirmButtonText: 'OK'
                            });
                            return false;
                        }

                        const q18Val = (this.memberData[mi] || {})['Q18'];
                        if (!q18Val) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Missing Work Location',
                                text: `Member ${mi + 1}: Please fill in Place of Work/Business`,
                                confirmButtonColor: '#2caf33',
                                confirmButtonText: 'OK'
                            });
                            return false;
                        }
                    }
                }
            }
            return true;
        } else if (step === 4) {
            return true;
        } else if (step === 5) {
            return true;
        } else if (step === 6) {
            return true;
        }
        return true;
    }

    updateProgress() {
        const progress = (this.currentStep / this.totalSteps) * 100;
        $('#progressBar').css('width', progress + '%');
        $('#progressPct').text(Math.round(progress) + '%');
        
        // Update form step display button area
        $('#formStepText, #formStepDisplay').text('Step ' + this.currentStep + ' of ' + this.totalSteps);
        
        if (this.currentStep === 1) {
            $('#progressLabel').text('IDENTIFICATION');
        } else if (this.currentStep === 2) {
            $('#progressLabel').text('DEMOGRAPHIC CHARACTERISTICS');
        } else if (this.currentStep === 3) {
            $('#progressLabel').text('ECONOMIC ACTIVITY & HEALTH INFORMATION');
        } else {
            $('#progressLabel').text('Step ' + this.currentStep);
        }
    }

    submitForm() {
        if (!this.validateStep(3)) {
            return;
        }

        // Show loading state
        $('#btnSubmit, #submitBtn').prop('disabled', true).text('Submitting...');

        const formData = {
            household: this.householdData,
            members: this.memberData
        };

        $.ajax({
            url: '../assets/api/save_census_entry.php',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Census entry has been submitted successfully.',
                        confirmButtonColor: '#2caf33',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to save entry',
                        confirmButtonColor: '#2caf33',
                        confirmButtonText: 'OK'
                    });
                    $('#btnSubmit, #submitBtn').prop('disabled', false).text('Submit');
                }
            },
            error: (xhr, status, error) => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Submission Failed',
                    text: 'Error submitting form. Please try again.',
                    confirmButtonColor: '#2caf33',
                    confirmButtonText: 'OK'
                });
                $('#btnSubmit, #submitBtn').prop('disabled', false).text('Submit');
            }
        });
    }

    getFormattedTime() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        return `${hours}:${minutes}`;
    }

    getTodayDate() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        return `${month}/${day}/${year}`;
    }

    getEncoderName() {
        // Get from PHP SESSION or data attribute
        const name = document.body.dataset.encoderName || 'System';
        return name;
    }

    loadCitiesByProvince() {
        // Placeholder: Load cities based on selected province
        const province = this.householdData.province;
        // In real implementation, call API to get cities
        const cities = {
            'NCR': ['Manila', 'Quezon City', 'Taguig', 'Makati', 'Pasig']
        };
        
        const citySelect = $('#city');
        citySelect.html('<option value="">Select city</option>');
        
        if (cities[province]) {
            cities[province].forEach(city => {
                citySelect.append(`<option value="${city}">${city}</option>`);
            });
        }
    }

    loadBarangaysByCity() {
        // Load barangays based on selected city - regenerate all 193 barangays
        const citySelect = $('#barangay');
        let html = '<option value="">Select barangay</option>';
        for (let i = 1; i <= 193; i++) {
            html += `<option value="${i}">${i}</option>`;
        }
        citySelect.html(html);
    }
}

// Initialize form when modal opens
$(document).ready(function() {
    // Store reference globally
    window.censusFormBuilder = new CensusFormBuilder();
});
