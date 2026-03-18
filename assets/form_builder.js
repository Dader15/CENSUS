// RBIM Multi-Step Form Builder
class CensusFormBuilder {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 2;
        this.memberCount = 1;
        this.memberData = [{}];
        this.stepData = {};
        this.isFormOpen = false;
        
        // Questions array for Step 2 (RBIM questions)
        this.QUESTIONS = [
            { id:'Q1',  label:'Full Name',                       hint:'Surname, First Name, Middle Initial',         type:'text',   placeholder:'e.g. Dela Cruz, Juan A.',   required:true },
            { id:'Q2',  label:'Relationship to Household Head',  hint:'Relationship to the HH head',                 type:'select', required:true,
              options:['Head','Spouse','Son','Daughter','Father','Mother','Brother','Sister','Other Relative','Non-relative'] },
            { id:'Q3',  label:'Sex',                             hint:'Male or female',                              type:'radio',  required:true,  options:['Male','Female'] },
            { id:'Q4',  label:'Age',                             hint:'Age as of last birthday',                     type:'number', placeholder:'Years old',                 required:true },
            { id:'Q5',  label:'Date of Birth',                   hint:'MM/YYYY',                                     type:'text',   placeholder:'MM/YYYY',                   required:false },
            { id:'Q6',  label:'Place of Birth',                  hint:'City/Municipality and Province',              type:'text',   placeholder:'e.g. Manila, Metro Manila', required:false },
            { id:'Q7',  label:'Nationality',                     hint:'Filipino or specify if otherwise',            type:'radio',  required:true,  options:['Filipino','Non-Filipino'] },
            { id:'Q8',  label:'Marital Status',                  hint:'Current marital status',                      type:'select', required:true,
              options:['Single','Married','Widowed','Divorced','Separated','Living-in','Unknown'] },
            { id:'Q9',  label:'Religion',                        hint:'Religion of member',                          type:'text',   placeholder:'e.g. Roman Catholic, Islam', required:false },
            { id:'Q10', label:'Ethnicity',                       hint:'Tagalog, Cebuano, Bicolano, Bisaya, etc.',    type:'text',   placeholder:'e.g. Tagalog, Bisaya',      required:false }
        ];
        
        this.init();
    }

    init() {
        this.attachEventListeners();
    }

    attachEventListeners() {
        // Create New Entry Button
        $(document).on('click', '#createNewBtn', () => {
            if (!this.isFormOpen) {
                this.showForm();
            }
        });

        // Step Navigation
        $(document).on('click', '#nextBtn', (e) => {
            e.preventDefault();
            this.nextStep();
        });

        $(document).on('click', '#prevBtn', (e) => {
            e.preventDefault();
            this.previousStep();
        });

        // Back to Dashboard
        $(document).on('click', '#backToDashboard', () => {
            if (confirm('Are you sure? Any unsaved data will be lost.')) {
                this.hideForm();
            }
        });
    }

    showForm() {
        $.get('index_modal/index_modal_census_form.php', (formHTML) => {
            $('#censusFormContainer').html(formHTML);
            $('#censusFormContainer').slideDown(300);
            $('#mainContentContainer').slideUp(300);
            
            $('#createNewBtn').prop('disabled', true).css({
                'opacity': '0.6',
                'cursor': 'not-allowed'
            });
            this.isFormOpen = true;
            this.currentStep = 1;
            this.memberCount = 1;
            this.memberData = [{}];
            this.updateStepDisplay();
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    hideForm() {
        $('#censusFormContainer').slideUp(300, () => {
            this.resetForm();
            $('#censusFormContainer').html('');
        });
        $('#mainContentContainer').slideDown(300);
        
        $('#createNewBtn').prop('disabled', false).css({
            'opacity': '1',
            'cursor': 'pointer'
        });
        this.isFormOpen = false;
    }

    resetForm() {
        this.currentStep = 1;
        this.memberCount = 1;
        this.memberData = [{}];
        this.stepData = {};
    }

    updateStepDisplay() {
        // Hide all steps
        $('.form-step').removeClass('active');
        
        // Show current step
        $(`#step-${this.currentStep}`).addClass('active');

        // Update navigation buttons
        if (this.currentStep === 1) {
            $('#prevBtn').hide();
            $('#nextBtn').show();
            $('#submitBtn').hide();
            $('#toolbar').hide();
        } else if (this.currentStep === this.totalSteps) {
            $('#prevBtn').show();
            $('#nextBtn').hide();
            $('#submitBtn').show();
            $('#toolbar').show();
            this.renderAllQuestions();
        } else {
            $('#prevBtn').show();
            $('#nextBtn').show();
            $('#submitBtn').hide();
            $('#toolbar').hide();
        }

        // Update step indicator
        const stepText = `Step ${this.currentStep} of ${this.totalSteps}`;
        $('#formStepText').text(stepText);

        // Update progress bar
        const progress = (this.currentStep / this.totalSteps) * 100;
        $('#progressBar').css('width', progress + '%');
    }

    nextStep() {
        // Validate current step
        if (!this.validateStep(this.currentStep)) {
            alert('Please fill all required fields');
            return;
        }

        this.saveStepData();

        if (this.currentStep < this.totalSteps) {
            this.currentStep++;
            this.updateStepDisplay();
        }
    }

    previousStep() {
        if (this.currentStep > 1) {
            this.saveStepData();
            this.currentStep--;
            this.updateStepDisplay();
        }
    }

    validateStep(stepNum) {
        if (stepNum === 1) {
            return this.validateStep1();
        } else if (stepNum === 2) {
            return this.validateStep2();
        }
        return true;
    }

    validateStep1() {
        let valid = true;
        const required = ['#province', '#city', '#barangay', '#householdHead', '#totalMembers'];
        
        required.forEach(sel => {
            const $el = $(sel);
            if (!$el.val()) {
                $el.css('border-color', '#E85C4A');
                valid = false;
            } else {
                $el.css('border-color', '#E5EAF0');
            }
        });
        
        return valid;
    }

    validateStep2() {
        let valid = true;
        
        // Check all required questions for each member
        for (let mi = 0; mi < this.memberCount; mi++) {
            this.QUESTIONS.forEach(q => {
                if (!q.required) return;
                if (!(this.memberData[mi] && this.memberData[mi][q.id])) {
                    valid = false;
                }
            });
        }
        
        return valid;
    }

    saveStepData() {
        if (this.currentStep === 1) {
            this.stepData = {
                province: $('#province').val(),
                city: $('#city').val(),
                barangay: $('#barangay').val(),
                householdHead: $('#householdHead').val(),
                totalMembers: $('#totalMembers').val()
            };
        }
    }

    updateMemberCount() {
        const total = parseInt($('#totalMembers').val()) || 1;
        
        // Resize memberData array
        if (total > this.memberCount) {
            for (let i = this.memberCount; i < total; i++) {
                this.memberData[i] = {};
            }
        } else if (total < this.memberCount) {
            this.memberData.length = total;
        }
        
        this.memberCount = total;
        $('#memberCount').text(this.memberCount);
        $('#btnRemove').toggle(this.memberCount > 1);
    }

    addMember() {
        if (this.memberCount < 20) {
            this.memberCount++;
            this.memberData.push({});
            $('#totalMembers').val(this.memberCount);
            $('#memberCount').text(this.memberCount);
            $('#btnRemove').show();
            this.renderAllQuestions();
        }
    }

    removeMember() {
        if (this.memberCount > 1) {
            if (!confirm(`Remove Member ${this.memberCount}?`)) return;
            this.memberCount--;
            this.memberData.pop();
            $('#totalMembers').val(this.memberCount);
            $('#memberCount').text(this.memberCount);
            $('#btnRemove').toggle(this.memberCount > 1);
            this.renderAllQuestions();
        }
    }

    memberName(memberIdx) {
        return (this.memberData[memberIdx] && this.memberData[memberIdx]['Q1']) 
            ? this.memberData[memberIdx]['Q1'].split(',')[0].trim() 
            : '';
    }

    renderAllQuestions() {
        const container = $('#cardsContainer');
        container.html('');
        
        this.QUESTIONS.forEach((q, qi) => {
            const card = document.createElement('div');
            card.className = 'q-card';
            card.id = 'card_' + q.id;
            
            card.innerHTML = `
                <div class="q-card-header">
                    <div>
                        <div class="q-badge">${q.id}</div>
                        <div class="q-title">${q.label}</div>
                        ${q.hint ? `<div class="q-hint">${q.hint}</div>` : ''}
                    </div>
                    ${q.required ? `<span class="q-required">Required *</span>` : ''}
                </div>
                <div class="q-members" id="members_${q.id}"></div>
            `;
            container.append(card);
            this.renderMemberRows(q);
        });
        
        this.updateProgress();
    }

    renderMemberRows(q) {
        const wrap = $(`#members_${q.id}`);
        if (!wrap.length) return;
        
        wrap.html('');
        
        for (let mi = 0; mi < this.memberCount; mi++) {
            const val = (this.memberData[mi] || {})[q.id] || '';
            const name = this.memberName(mi);
            
            const row = document.createElement('div');
            row.className = 'member-row';
            
            // Member Label
            const lbl = document.createElement('div');
            lbl.className = 'member-label';
            lbl.innerHTML = `Member ${mi + 1}${name ? `<span class="m-name">${name}</span>` : ''}`;
            row.appendChild(lbl);
            
            // Input field based on type
            let input = null;
            
            if (q.type === 'text' || q.type === 'number') {
                input = document.createElement('input');
                input.type = q.type;
                input.className = 'q-input' + (val ? ' has-val' : '');
                input.placeholder = q.placeholder || '';
                input.value = val;
                input.addEventListener('input', (e) => {
                    this.setMemberVal(mi, q.id, e.target.value);
                    e.target.classList.toggle('has-val', !!e.target.value);
                    if (q.id === 'Q1') this.refreshMemberLabels();
                });
            } else if (q.type === 'select') {
                input = document.createElement('select');
                input.className = 'q-input' + (val ? ' has-val' : '');
                input.innerHTML = `<option value="">-- Select --</option>` +
                    q.options.map(o => `<option value="${o}"${val === o ? ' selected' : ''}>${o}</option>`).join('');
                input.addEventListener('change', (e) => {
                    this.setMemberVal(mi, q.id, e.target.value);
                    e.target.classList.toggle('has-val', !!e.target.value);
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
                    });
                    grp.appendChild(btn);
                });
                input = grp;
            }
            
            if (input) {
                row.appendChild(input);
            }
            
            wrap.append(row);
        }
    }

    setMemberVal(memberIdx, qId, val) {
        if (!this.memberData[memberIdx]) {
            this.memberData[memberIdx] = {};
        }
        this.memberData[memberIdx][qId] = val;
        this.updateProgress();
    }

    refreshMemberLabels() {
        document.querySelectorAll('.member-label').forEach((lbl, idx) => {
            if (idx < this.memberCount) {
                const name = this.memberName(idx);
                lbl.innerHTML = `Member ${idx + 1}${name ? `<span class="m-name">${name}</span>` : ''}`;
            }
        });
    }

    updateProgress() {
        let total = 0, filled = 0;
        
        for (let mi = 0; mi < this.memberCount; mi++) {
            this.QUESTIONS.forEach(q => {
                if (!q.required) return;
                total++;
                if (this.memberData[mi] && this.memberData[mi][q.id]) {
                    filled++;
                }
            });
        }
        
        const pct = total ? Math.round(filled / total * 100) : 0;
        $('#progressBar').css('width', pct + '%');
        $('#progressPct').text(pct + '%');
        $('#progressLabel').text(`${filled} of ${total} fields filled · ${this.memberCount} member${this.memberCount > 1 ? 's' : ''}`);
    }

    submitForm() {
        if (!this.validateStep(2)) {
            alert('Please fill all required fields for all members');
            return;
        }
        
        // Collect all form data
        const formData = {
            household: this.stepData,
            members: this.memberData
        };
        
        console.log('Submitting:', formData);
        
        // Send to server
        $.ajax({
            url: '../assets/api/save_census_entry.php',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: (response) => {
                alert('RBIM entry submitted successfully!');
                this.hideForm();
                if (typeof loadCensusData === 'function') {
                    loadCensusData();
                }
            },
            error: (xhr, status, error) => {
                console.error('Error:', error);
                alert('Error saving RBIM entry. Please try again.');
            }
        });
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
