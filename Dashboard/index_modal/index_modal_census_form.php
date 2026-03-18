<?php
// RBIM Form: Modern multi-step registry data entry form
?>

<style>
:root {
  --navy: #2caf33;
  --blue: #1f7a1f;
  --sky: #4CAF50;
  --mint: #66BB6A;
  --gold: #FFA500;
  --white: #ffffff;
  --gray: #6b7280;
  --light: #f9fafb;
  --red: #ef4444;
  --shadow: 0 8px 32px rgba(44,175,51,0.13);
  --shadow-lg: 0 20px 60px rgba(44,175,51,0.20);
}

* { margin: 0; padding: 0; box-sizing: border-box; }

.form-container {
  background: linear-gradient(135deg, #2caf33 0%, #1f7a1f 100%);
  min-height: 100vh;
  padding: 36px 16px 100px;
  margin: -20px;
}

.form-header {
  text-align: center;
  margin-bottom: 36px;
}

.form-header h2 {
  font-size: 32px;
  color: var(--white);
  margin-bottom: 10px;
  font-weight: 600;
}

.form-header p {
  color: var(--gray);
  font-size: 14px;
  margin-bottom: 20px;
}

.form-progress {
  height: 5px;
  background: rgba(255,255,255,0.07);
  border-radius: 100px;
  overflow: hidden;
  margin-bottom: 12px;
}

.form-progress-bar {
  background: linear-gradient(90deg, #4CAF50, #66BB6A);
  border-radius: 100px;
  transition: width 0.5s ease;
}

.progress-text {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12px;
  color: var(--gray);
}

.progress-text strong {
  color: var(--mint);
}

.toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  background: rgba(76,175,80,0.05);
  border: 1px solid rgba(76,175,80,0.09);
  border-radius: 16px;
  padding: 16px 20px;
  margin-bottom: 28px;
  max-width: 860px;
  margin-left: auto;
  margin-right: auto;
}

.member-chip {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(76,175,80,0.15);
  border: 1.5px solid rgba(76,175,80,0.3);
  color: #4CAF50;
  font-size: 13px;
  font-weight: 600;
  padding: 7px 16px;
  border-radius: 100px;
}

.member-count {
  color: var(--white);
  font-size: 16px;
  font-weight: 700;
}

.btn-add {
  display: flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg, #4CAF50, #2E9D2A);
  color: #fff;
  border: none;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  font-weight: 600;
  padding: 9px 20px;
  border-radius: 100px;
  box-shadow: 0 4px 16px rgba(76,175,80,0.3);
  transition: all 0.2s;
}

.btn-add:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(76,175,80,0.4);
}

.btn-remove {
  display: flex;
  align-items: center;
  gap: 6px;
  background: rgba(232,92,74,0.10);
  color: var(--red);
  border: 1.5px solid rgba(232,92,74,0.22);
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  font-weight: 600;
  padding: 8px 16px;
  border-radius: 100px;
  transition: all 0.2s;
}

.btn-remove:hover {
  background: rgba(232,92,74,0.2);
}

.form-body {
  max-width: 860px;
  margin: 0 auto;
}

.form-step {
  display: none;
}

.form-step.active {
  display: block;
  animation: fadeIn 0.4s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.section-label {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
}

.section-label .dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--gold);
  flex-shrink: 0;
}

.section-label h3 {
  color: var(--white);
  font-size: 17px;
  font-weight: 600;
  margin: 0;
}

.q-card {
  background: var(--white);
  border-radius: 22px;
  box-shadow: var(--shadow);
  margin-bottom: 18px;
  overflow: visible;
  transition: box-shadow 0.25s;
}

.q-card:hover {
  box-shadow: var(--shadow-lg);
}

.q-card-header {
  border-radius: 22px 22px 0 0;

  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  padding: 20px 26px 16px;
  border-bottom: 1.5px solid #EDF2F8;
  background: linear-gradient(135deg, #F8FBFF 0%, #EEF5FF 100%);
}

.q-badge {
  display: inline-block;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: #4CAF50;
  background: rgba(76,175,80,0.12);
  padding: 3px 10px;
  border-radius: 100px;
  margin-bottom: 8px;
}

.q-title {
  font-size: 17px;
  font-weight: 700;
  color: var(--navy);
  line-height: 1.3;
  margin-bottom: 3px;
}

.q-hint {
  font-size: 12px;
  color: var(--gray);
}

.q-required {
  font-size: 11px;
  color: var(--red);
  font-weight: 700;
  background: rgba(232,92,74,0.08);
  padding: 3px 10px;
  border-radius: 100px;
  flex-shrink: 0;
  margin-left: 12px;
}

.q-members {
  padding: 0 26px 18px;
}

.member-row {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 13px 0;
  border-bottom: 1px solid #F0F4F8;
}

.member-row:last-child {
  border-bottom: none;
}

.member-label {
  font-size: 13px;
  font-weight: 700;
  color: #7A90A8;
  min-width: 88px;
  flex-shrink: 0;
}

.member-label .m-name {
  display: block;
  font-size: 11px;
  font-weight: 400;
  color: #B0BFCF;
  margin-top: 2px;
  max-width: 88px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.q-input {
  flex: 1;
  padding: 10px 14px;
  border: 2px solid #E5EAF0;
  border-radius: 12px;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  color: var(--navy);
  background: var(--light);
  transition: all 0.2s;
  outline: none;
}

.q-input:focus {
  border-color: #4CAF50;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(76,175,80,0.1);
}

.q-input.has-val {
  border-color: #66BB6A;
  background: rgba(102,187,106,0.04);
}

select.q-input {
  cursor: pointer;
}

.radio-group {
  flex: 1;
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.radio-btn {
  padding: 8px 14px;
  border: 2px solid #E5EAF0;
  border-radius: 10px;
  background: var(--light);
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  font-weight: 500;
  color: #6A7F96;
  cursor: pointer;
  transition: all 0.18s;
  white-space: nowrap;
}

.radio-btn:hover {
  border-color: #4CAF50;
  color: #4CAF50;
}

.radio-btn.selected {
  background: #4CAF50;
  border-color: #4CAF50;
  color: #fff;
  box-shadow: 0 3px 10px rgba(76,175,80,0.28);
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}

.form-row.single {
  grid-template-columns: 1fr;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-weight: 600;
  color: #EDF2F8;
  font-size: 14px;
}

.form-group label .required {
  color: #E85C4A;
  margin-left: 2px;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 10px 12px;
  border: 2px solid #E5EAF0;
  border-radius: 6px;
  font-family: inherit;
  font-size: 14px;
  transition: all 0.2s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--sky);
  box-shadow: 0 0 0 3px rgba(74,144,217,0.1);
}

.name-fields-row {
  padding: 13px 0;
}

.name-fields-row .q-input {
  min-width: 0;
}

@media (max-width: 560px) {
  .q-card-header, .q-members {
    padding-left: 16px;
    padding-right: 16px;
  }
  
  .member-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }

  .name-fields-row {
    grid-template-columns: 1fr 1fr 1fr 1fr !important;
  }
  
  .member-label {
    min-width: unset;
  }
  
  .q-input, .radio-group {
    width: 100%;
  }

  .form-row {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 400px) {
  .name-fields-row {
    grid-template-columns: 1fr 1fr !important;
  }
}
</style>

<div id="censusFormModal" style="display: none;">
    <!-- Close button -->
    <button id="closeFormBtn" onclick="hideCensusForm()" style="position: fixed; top: 20px; right: 20px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 20px; z-index: 101; display: flex; align-items: center; justify-content: center;">
        ×
    </button>
    
    <div class="form-header">
        <h2>Registry of Barangay Inhabitants and Migrants</h2>
        <p>Demographic Characteristics Survey</p>
        <div class="form-progress">
            <div class="form-progress-bar" id="progressBar" style="width: 0%;"></div>
        </div>
        <div class="progress-text">
            <span id="progressLabel">Fill in all fields</span>
            <strong id="progressPct">0%</strong>
        </div>
    </div>

    <!-- <div class="toolbar" id="toolbar" style="display: none;">
        <div class="member-chip">
            👥 Members: <span class="member-count" id="memberCount">1</span>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <button class="btn-remove" id="btnRemove" style="display:none;" onclick="window.censusFormBuilder.removeMember()">− Remove Last</button>
            <button class="btn-add" id="btnAdd" onclick="window.censusFormBuilder.addMember()">＋ Add Member</button>
        </div>
    </div> -->

    <div class="form-body">
        <!-- Step 1: IDENTIFICATION -->
        <div class="form-step active" id="step-1">
            <!-- Dynamically rendered by form_builder.js -->
        </div>

        <!-- Step 2: DEMOGRAPHIC CHARACTERISTICS -->
        <div class="form-step" id="step-2">
            <!-- Dynamically rendered by form_builder.js -->
        </div>

        <!-- Step 3: ECONOMIC ACTIVITY & HEALTH INFORMATION -->
        <div class="form-step" id="step-3">
            <!-- Dynamically rendered by form_builder.js -->
        </div>

        <!-- Step 4: Monthly Income (Q15) -->
        <div class="form-step" id="step-4">
            <!-- Dynamically rendered by form_builder.js -->
        </div>

        <!-- Step 5: Income Source & Work (Q16-Q18) -->
        <div class="form-step" id="step-5">
            <!-- Dynamically rendered by form_builder.js -->
        </div>

        <!-- Step 6: Delivery, Immunization & Employment (Q19-Q22) -->
        <div class="form-step" id="step-6">
            <!-- Dynamically rendered by form_builder.js -->
        </div>
    </div>

    <!-- <div style="position: fixed; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(44,175,51,0.95), transparent); padding: 32px 16px 20px; border-top: 1px solid rgba(76,175,80,0.08); display: flex; justify-content: space-between; align-items: center; z-index: 100;">
        <div style="font-size: 13px; color: #B0BFCF;">
            <span id="formStepDisplay">Step 1 of 2</span>
        </div>
        <div style="display: flex; gap: 12px;">
            <button id="btnPrev" class="btn-remove" onclick="if(window.censusFormBuilder) window.censusFormBuilder.previousStep()" style="display:none;">← Back</button>
            <button id="btnNext" class="btn-add" onclick="if(window.censusFormBuilder) window.censusFormBuilder.nextStep()" style="display:flex;align-items:center;gap:6px;"><i class="fas fa-arrow-right"></i> Next</button>
            <button id="btnSubmit" class="btn-add" onclick="if(window.censusFormBuilder) window.censusFormBuilder.submitForm()" style="display:none;background: linear-gradient(135deg, #66BB6A, #4CAF50);align-items:center;gap:6px;"><i class="fas fa-check"></i> Submit</button>
        </div>
    </div> -->
</div> <!-- End censusFormModal -->
