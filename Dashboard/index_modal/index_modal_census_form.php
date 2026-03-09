<?php
// Census Form: Multi-step census data entry form - Inline Display
?>

<div class="form-container">
    <div class="form-header">
        <h2>Census Data Entry Form</h2>
        <p>Complete all required fields to create a new census entry</p>
        <div class="form-progress">
            <div class="form-progress-bar" style="width: 50%;"></div>
        </div>
    </div>

            <div class="form-body">
                <!-- STEP 1: Household Information -->
                <div class="form-step active" id="step-1">
                    <h3 style="margin-bottom: 25px; color: #1f3722; font-weight: 600;">Household Information</h3>
                    
                    <!-- Number Code -->
                    <div class="form-row">
                        <div class="form-group">
                            <label>Number Code <span class="required">*</span></label>
                            <input type="text" name="number_code" placeholder="Enter number code" required>
                        </div>
                    </div>

                    <!-- Household Type Selection -->
                    <div class="form-row">
                        <div class="form-group" style="flex-direction: row; align-items: center; gap: 30px;">
                            <label style="margin-bottom: 0; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="radio" name="living_type" value="household" id="household_check" required>
                                <span>Household</span>
                            </label>
                            <label style="margin-bottom: 0; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="radio" name="living_type" value="institutional" id="institutional_check">
                                <span>Institutional Living</span>
                            </label>
                        </div>
                    </div>

                    <!-- Province -->
                    <div class="form-row">
                        <div class="form-group">
                            <label>Province <span class="required">*</span></label>
                            <input type="text" name="province" placeholder="Enter province" required>
                        </div>
                    </div>

                    <!-- Name of Respondent -->
                    <div class="form-row">
                        <div class="form-group">
                            <label>Name of Respondent <span class="required">*</span></label>
                            <input type="text" name="respondent_name" placeholder="Enter respondent name" required>
                        </div>
                    </div>

                    <!-- City/Municipality -->
                    <div class="form-row two-col">
                        <div class="form-group">
                            <label>City/Municipality <span class="required">*</span></label>
                            <input type="text" name="city_municipality" placeholder="Enter city/municipality" required>
                        </div>
                        <div class="form-group">
                            <label>Barangay <span class="required">*</span></label>
                            <select name="barangay" required>
                                <option value="">-- Select Barangay --</option>
                                <?php for ($i = 1; $i <= 193; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Household Head -->
                    <div class="form-row">
                        <div class="form-group">
                            <label>Household Head <span class="required">*</span></label>
                            <input type="text" name="household_head" placeholder="Enter household head name" required>
                        </div>
                    </div>

                    <!-- Address Components -->
                    <div class="form-row">
                        <div class="form-group">
                            <label>Room/Floor/Unit No. <span class="required">*</span></label>
                            <input type="text" name="address_unit" placeholder="e.g., Apt 102" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Building Name <span class="required">*</span></label>
                            <input type="text" name="address_building" placeholder="Enter building name" required>
                        </div>
                    </div>

                    <div class="form-row two-col">
                        <div class="form-group">
                            <label>House/Lot and Block No. <span class="required">*</span></label>
                            <input type="text" name="address_lot_block" placeholder="e.g., Lot 5 Block 3" required>
                        </div>
                        <div class="form-group">
                            <label>Street Name <span class="required">*</span></label>
                            <input type="text" name="address_street" placeholder="Enter street name" required>
                        </div>
                    </div>

                    <!-- Encoder & Supervisor Info -->
                    <div class="form-row two-col">
                        <div class="form-group">
                            <label>Name of Encoder <span class="required">*</span></label>
                            <input type="text" name="encoder_name" placeholder="Enter encoder name" required>
                        </div>
                        <div class="form-group">
                            <label>Name of Supervisor <span class="required">*</span></label>
                            <input type="text" name="supervisor_name" placeholder="Enter supervisor name" required>
                        </div>
                    </div>

                    <!-- Encoder & Supervisor Date -->
                    <div class="form-row two-col">
                        <div class="form-group">
                            <label>Encoder Date <span class="required">*</span></label>
                            <input type="date" name="encoder_date" required>
                        </div>
                        <div class="form-group">
                            <label>Supervisor Date <span class="required">*</span></label>
                            <input type="date" name="supervisor_date" required>
                        </div>
                    </div>

                    <!-- Interviewer Info -->
                    <div class="form-row two-col">
                        <div class="form-group">
                            <label>Name of Interviewer <span class="required">*</span></label>
                            <input type="text" name="interviewer_name" placeholder="Enter interviewer name" required>
                        </div>
                        <div class="form-group">
                            <label>Interview Date <span class="required">*</span></label>
                            <input type="date" name="interview_date" required>
                        </div>
                    </div>

                    <!-- Total Household Members -->
                    <div class="form-row">
                        <div class="form-group">
                            <label>Total Number of Household Members <span class="required">*</span></label>
                            <input type="number" name="total_household_members" min="1" placeholder="Enter total household members" required>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Household Members Details -->
                <div class="form-step" id="step-2">
                    <h3 style="margin-bottom: 25px; color: #1f3722; font-weight: 600;">Household Members Details</h3>
                    <p style="color: #6b7280; margin-bottom: 20px; font-size: 14px;">Please provide detailed information for each household member below.</p>
                    
                    <div id="household-members-container">
                        <!-- Members will be added here dynamically -->
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <div class="step-indicator">
                    Step <strong id="currentStep">1</strong> of <strong id="totalSteps">2</strong>
                </div>
                <div class="button-group">
                    <button type="button" class="btn btn-secondary" id="backToDashboard">Back to Dashboard</button>
                    <button type="button" class="btn btn-secondary" id="prevStep" style="display: none;">Back</button>
                    <button type="button" class="btn btn-primary" id="nextStep">Next</button>
                    <button type="button" class="btn btn-primary" id="submitForm" style="display: none;">Submit Entry</button>
                </div>
            </div>
        </div>
