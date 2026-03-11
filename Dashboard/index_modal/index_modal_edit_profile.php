<?php
// Edit Profile Modal
?>
<div class="modal fade" id="profileModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modern-modal">
      <div class="modal-header">
        <div class="modal-header-content">
          <h5 class="modal-title" id="profileModalLabel"><i class="fas fa-user-edit"></i> Edit Profile</h5>
          <p class="modal-subtitle">Update your profile information</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
        <!-- Edit Form -->
        <form id="profileForm">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Surname <span class="required">*</span></label>
              <input type="text" id="profileSname" class="form-control" placeholder="Surname" required>
            </div>
            <div class="form-group">
              <label class="form-label">First Name <span class="required">*</span></label>
              <input type="text" id="profileFname" class="form-control" placeholder="First Name" required>
            </div>
          </div>
          <div class="form-row four-col">
            <div class="form-group">
              <label class="form-label">M.I.</label>
              <input type="text" id="profileMI" class="form-control" placeholder="M.I." maxlength="5">
            </div>
            <div class="form-group">
              <label class="form-label">Suffix</label>
              <input type="text" id="profileSuffix" class="form-control" placeholder="Suffix" maxlength="10">
            </div>
            <div class="form-group">
              <label class="form-label">Barangay</label>
              <select id="profileBarangay" class="form-control">
                <option value="">Select Barangay</option>
                <?php for ($i = 1; $i <= 188; $i++): ?>
                  <option value="<?php echo $i; ?>">Barangay <?php echo $i; ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Username <span class="required">*</span></label>
              <input type="text" id="profileUsername" class="form-control" placeholder="Enter username" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Position</label>
              <input type="text" id="profilePosition" class="form-control" placeholder="Enter position">
            </div>
          </div>
          <!-- <div class="form-row">
            <div class="form-group">
              <label class="form-label">Position</label>
              <input type="text" id="profilePosition" class="form-control" placeholder="Enter position">
            </div>
          </div> -->
        </form>

        <!-- Change Password Section -->
        <hr>
        <div class="password-section">
          <h6><i class="fas fa-lock"></i> Change Password</h6>
          <p class="text-muted small">Click the button below to change your password with current password verification.</p>
          <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal" data-bs-dismiss="modal">
            <i class="fas fa-key"></i> Change Password
          </button>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveProfileBtn">Save Changes</button>
      </div>
    </div>
  </div>
</div>
