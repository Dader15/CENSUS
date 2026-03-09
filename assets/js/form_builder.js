// Census Multi-Step Form Builder
class CensusFormBuilder {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 2;
        this.householdMembers = [];
        this.totalHouseholdMembers = 0;
        this.formData = {};
        this.init();
    }

    init() {
        this.attachEventListeners();
    }

    attachEventListeners() {
        // Create New Entry Button
        $(document).on('click', '#createNewBtn', () => {
            this.showForm();
        });

        // Step Navigation
        $(document).on('click', '#nextStep', () => {
            if (this.validateStep(this.currentStep)) {
                this.saveStepData();
                this.nextStep();
                // Scroll to top of form
                $('.form-body').scrollTop(0);
            }
        });

        $(document).on('click', '#prevStep', () => {
            this.previousStep();
        });

        // Close Form - only on close button (not on overlay click)
        $(document).on('click', '.close-form-btn', (e) => {
            if (confirm('Are you sure? Any unsaved data will be lost.')) {
                this.hideForm();
            }
        });

        // Back to Dashboard
        $(document).on('click', '#backToDashboard', () => {
            if (confirm('Are you sure? Any unsaved data will be lost.')) {
                this.hideForm();
            }
        });

        // Household Members Management
        $(document).on('click', '.add-household-member-btn', () => {
            this.addHouseholdMemberRow();
        });

        $(document).on('click', '.delete-member-btn', (e) => {
            $(e.target).closest('tr').remove();
            this.householdMembers = this.getHouseholdMembersFromTable();
        });

        // Nationality change - show "Others" field if needed
        $(document).on('change', 'select[name="nationality_member[]"]', (e) => {
            const $row = $(e.target).closest('tr');
            const $othersField = $row.find('input[name="nationality_other_member[]"]').parent();
            
            if ($(e.target).val() === 'Non-Filipino') {
                $othersField.show();
            } else {
                $othersField.hide();
            }
        });

        // Total Household Members Change
        $(document).on('change', 'input[name="total_household_members"]', (e) => {
            const total = parseInt($(e.target).val()) || 0;
            this.totalHouseholdMembers = total;
            this.updateHouseholdMembersTable(total);
        });
    }

    showForm() {
        // Load form HTML into the inline container
        $.get('index_modal/index_modal_census_form.php', (formHTML) => {
            $('#censusFormContainer').html(formHTML);
            $('#censusFormContainer').slideDown(300);
            $('#mainContentContainer').slideUp(300);
            
            this.currentStep = 1;
            this.householdMembers = [];
            this.totalHouseholdMembers = 0;
            this.formData = {};
            this.updateStepDisplay();
            
            // Scroll to top of page
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    hideForm() {
        $('#censusFormContainer').slideUp(300, () => {
            this.resetForm();
            $('#censusFormContainer').html('');
        });
        $('#mainContentContainer').slideDown(300);
    }

    resetForm() {
        // Reset all form inputs
        $('#censusFormContainer').find('input, select, textarea').val('');
        $('#censusFormContainer').find('input[type="radio"], input[type="checkbox"]').prop('checked', false);
        
        // Reset form state
        this.currentStep = 1;
        this.householdMembers = [];
        this.totalHouseholdMembers = 0;
        this.formData = {};
        
        // Remove any dynamically generated household member rows
        $('#censusFormContainer').find('.household-member-row').remove();
        
        // Clear validation errors
        $('#censusFormContainer').find('.form-group').removeClass('error');
        $('#censusFormContainer').find('.error-message').remove();
    }

    updateStepDisplay() {
        // Hide all steps
        $('.form-step').removeClass('active');
        
        // Show current step
        $(`#step-${this.currentStep}`).addClass('active');

        // Update step indicator
        $('#currentStep').text(this.currentStep);

        // Update buttons
        if (this.currentStep === 1) {
            $('#backToDashboard').show();
            $('#prevStep').hide();
            $('#nextStep').show();
            $('#submitForm').hide();
        } else if (this.currentStep === this.totalSteps) {
            $('#backToDashboard').hide();
            $('#prevStep').show();
            $('#nextStep').hide();
            $('#submitForm').show();
        } else {
            $('#backToDashboard').hide();
            $('#prevStep').show();
            $('#nextStep').show();
            $('#submitForm').hide();
        }

        // Update progress bar
        const progress = (this.currentStep / this.totalSteps) * 100;
        $('.form-progress-bar').css('width', progress + '%');
    }

    saveStepData() {
        const formInputs = $(`#step-${this.currentStep}`).find('input, select, textarea');
        
        formInputs.each((i, el) => {
            const $el = $(el);
            const name = $el.attr('name');
            let value = $el.val();
            
            if (name) {
                // Handle different input types
                if ($el.attr('type') === 'radio' || $el.attr('type') === 'checkbox') {
                    if ($el.is(':checked')) {
                        this.formData[name] = value;
                    }
                } else if (name.includes('[]')) {
                    // Handle array inputs
                    if (!this.formData[name]) {
                        this.formData[name] = [];
                    }
                    this.formData[name].push(value);
                } else {
                    this.formData[name] = value;
                }
            }
        });
    }

    nextStep() {
        if (this.currentStep === 1) {
            // After step 1, prepare household members form
            this.totalHouseholdMembers = parseInt($('input[name="total_household_members"]').val()) || 0;
            this.generateHouseholdMembersForm();
        }

        this.currentStep++;
        if (this.currentStep > this.totalSteps) {
            this.currentStep = this.totalSteps;
        }
        this.updateStepDisplay();
        
        // Scroll to top of form body
        setTimeout(() => {
            $('.form-body').scrollTop(0);
        }, 100);
    }

    previousStep() {
        this.currentStep--;
        if (this.currentStep < 1) {
            this.currentStep = 1;
        }
        this.updateStepDisplay();
        
        // Scroll to top of form body
        setTimeout(() => {
            $('.form-body').scrollTop(0);
        }, 100);
    }

    validateStep(step) {
        const $step = $(`#step-${step}`);
        const requiredInputs = $step.find('[required]');
        let isValid = true;

        requiredInputs.each((i, el) => {
            const $el = $(el);
            let hasValue = false;
            
            if ($el.attr('type') === 'radio') {
                // For radio buttons, check if any in the group is checked
                hasValue = $(`input[name="${$el.attr('name')}"]:checked`).length > 0;
            } else if ($el.attr('type') === 'checkbox') {
                hasValue = $el.is(':checked');
            } else {
                hasValue = $el.val() && $el.val().trim() !== '';
            }
            
            if (!hasValue) {
                isValid = false;
                $el.addClass('error');
                $el.closest('.form-group').addClass('error');
            } else {
                $el.removeClass('error');
                $el.closest('.form-group').removeClass('error');
            }
        });

        if (!isValid) {
            alert('Please fill in all required fields.');
        }

        return isValid;
    }

    generateHouseholdMembersForm() {
        const $container = $('#household-members-container');
        $container.html('');

        for (let i = 0; i < this.totalHouseholdMembers; i++) {
            const memberForm = this.generateMemberForm(i + 1);
            $container.append(memberForm);
        }

        // Add scroll behavior
        setTimeout(() => {
            $container.scrollIntoView({ behavior: 'smooth' });
        }, 100);
    }

    generateMemberForm(memberNumber) {
        return `
            <div class="household-detail-form" id="member-form-${memberNumber}">
                <h4>Household Member ${memberNumber}</h4>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Name <span class="required">*</span></label>
                        <input type="text" name="member_name_${memberNumber}" placeholder="Enter full name" required>
                    </div>
                </div>

                <div class="form-row two-col">
                    <div class="form-group">
                        <label>Relationship to HHH <span class="required">*</span></label>
                        <select name="relationship_${memberNumber}" required>
                            <option value="">-- Select Relationship --</option>
                            <option value="Head">Head</option>
                            <option value="Spouse">Spouse</option>
                            <option value="Son">Son</option>
                            <option value="Daughter">Daughter</option>
                            <option value="Stepson">Stepson</option>
                            <option value="Stepdaughter">Stepdaughter</option>
                            <option value="Son-in-law">Son-in-law</option>
                            <option value="Daughter-in-law">Daughter-in-law</option>
                            <option value="Grandson">Grandson</option>
                            <option value="Granddaughter">Granddaughter</option>
                            <option value="Father">Father</option>
                            <option value="Mother">Mother</option>
                            <option value="Brother">Brother</option>
                            <option value="Sister">Sister</option>
                            <option value="Uncle">Uncle</option>
                            <option value="Aunt">Aunt</option>
                            <option value="Nephew">Nephew</option>
                            <option value="Niece">Niece</option>
                            <option value="Other Relative">Other Relative</option>
                            <option value="Non-relative">Non-relative</option>
                            <option value="Boarder">Boarder</option>
                            <option value="Domestic Helper">Domestic Helper</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Sex <span class="required">*</span></label>
                        <select name="sex_${memberNumber}" required>
                            <option value="">-- Select Sex --</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>

                <div class="form-row two-col">
                    <div class="form-group">
                        <label>Age <span class="required">*</span></label>
                        <input type="number" name="age_${memberNumber}" min="0" max="150" placeholder="Enter age" required>
                    </div>

                    <div class="form-group">
                        <label>Date of Birth (MM/DD/YYYY) <span class="required">*</span></label>
                        <input type="text" name="dob_${memberNumber}" placeholder="MM/DD/YYYY" pattern="\\d{2}/\\d{2}/\\d{4}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Place of Birth <span class="required">*</span></label>
                        <input type="text" name="pob_${memberNumber}" placeholder="Enter place of birth" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nationality <span class="required">*</span></label>
                        <select name="nationality_${memberNumber}" class="nationality-select" data-member="${memberNumber}" required>
                            <option value="">-- Select Nationality --</option>
                            <option value="Filipino">Filipino</option>
                            <option value="Non-Filipino">Non-Filipino</option>
                        </select>
                    </div>

                    <div class="form-group conditional-field" id="nationality-other-field-${memberNumber}">
                        <label>Other Nationality <span class="required">*</span></label>
                        <input type="text" name="nationality_other_${memberNumber}" placeholder="Specify other nationality">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>Marital Status <span class="required">*</span></label>
                    <select name="marital_status_${memberNumber}" required>
                        <option value="">-- Select Marital Status --</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Living-in">Living-in</option>
                        <option value="Widowed">Widowed</option>
                        <option value="Separated">Separated</option>
                        <option value="Divorced">Divorced</option>
                        <option value="Unknown">Unknown</option>
                    </select>
                </div>

                <div class="form-row two-col" style="margin-top: 15px;">
                    <div class="form-group">
                        <label>Religion <span class="required">*</span></label>
                        <input type="text" name="religion_${memberNumber}" placeholder="Enter religion" required>
                    </div>

                    <div class="form-group">
                        <label>Ethnicity <span class="required">*</span></label>
                        <input type="text" name="ethnicity_${memberNumber}" placeholder="Enter ethnicity" required>
                    </div>
                </div>
            </div>
        `;
    }

    updateHouseholdMembersTable(total) {
        // This is handled by generateHouseholdMembersForm in nextStep
    }

    getHouseholdMembersFromTable() {
        const members = [];
        // This will be handled when submitting
        return members;
    }

    collectAllFormData() {
        const completeData = {};

        // Collect Step 1 Data
        $('#step-1 input, #step-1 select, #step-1 textarea').each((i, el) => {
            const $el = $(el);
            const name = $el.attr('name');
            const value = $el.val();
            if (name) {
                completeData[name] = value;
            }
        });

        // Collect Step 2 Data
        $('#step-2 input, #step-2 select, #step-2 textarea').each((i, el) => {
            const $el = $(el);
            const name = $el.attr('name');
            const value = $el.val();
            if (name) {
                completeData[name] = value;
            }
        });

        return completeData;
    }

    submitForm() {
        // Validate all steps
        if (!this.validateStep(1) || !this.validateStep(2)) {
            alert('Please fill in all required fields.');
            return;
        }

        const formData = this.collectAllFormData();

        // Send to server
        $.ajax({
            url: '../assets/api/save_census_entry.php',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: (response) => {
                console.log('Success:', response);
                this.showSuccessMessage();
                setTimeout(() => {
                    this.hideForm();
                    // Reload table
                    if (typeof loadCensusData === 'function') {
                        loadCensusData();
                    }
                }, 2000);
            },
            error: (xhr, status, error) => {
                console.error('Error:', error);
                alert('Error saving census entry. Please try again.');
            }
        });
    }

    showSuccessMessage() {
        const $modal = $('.form-modal');
        $modal.html(`
            <div class="form-container">
                <div class="form-header">
                    <h2>Success!</h2>
                </div>
                <div class="form-body success-screen">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="success-message">
                        <h3>Entry Created Successfully</h3>
                        <p>Your census entry has been saved and will appear in the records shortly.</p>
                    </div>
                </div>
            </div>
        `);
    }
}

// Initialize on document ready
$(document).ready(function() {
    new CensusFormBuilder();

    // Handle Submit Form button
    $(document).on('click', '#submitForm', function() {
        // Find the form builder instance and submit
        const formBuilder = window.formBuilder || new CensusFormBuilder();
        formBuilder.submitForm();
    });
});
