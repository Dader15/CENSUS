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
              <label class="form-label">Full Name <span class="required">*</span></label>
              <input type="text" id="profileFullName" class="form-control" placeholder="Enter full name" required>
            </div>
            <div class="form-group">
              <label class="form-label">Username <span class="required">*</span></label>
              <input type="text" id="profileUsername" class="form-control" placeholder="Enter username" required>
            </div>
          </div>
          <div class="form-row">
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
              <label class="form-label">Position</label>
              <input type="text" id="profilePosition" class="form-control" placeholder="Enter position">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Password</label>
              <input type="password" id="profilePassword" class="form-control" placeholder="Leave blank to keep current password">
            </div>
            <div class="form-group">
              <label class="form-label">Confirm Password</label>
              <input type="password" id="profileConfirmPassword" class="form-control" placeholder="Leave blank to keep current password">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveProfileBtn">Save Changes</button>
      </div>
    </div>
  </div>
</div>
