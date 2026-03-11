<!-- Change Password Modal -->
<form id="changePasswordForm">
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password <span style="color: red;">*</span></label>
                        <input type="password" class="form-control" id="currentPassword" placeholder="Enter your current password" required>
                    </div>
                    <div class="alert alert-info alert-sm" role="alert" style="font-size: 0.9rem; margin-bottom: 15px;">
                        <i class="fas fa-info-circle"></i> 
                        Password must be at least 12 characters and contain 1 uppercase, 1 lowercase, 1 number, and 1 symbol
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password <span style="color: red;">*</span></label>
                        <input type="password" class="form-control" id="newPassword" placeholder="Enter new password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password <span style="color: red;">*</span></label>
                        <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm new password" required>
                    </div>
                    <div id="passwordMismatchError" class="text-danger d-none">Passwords do not match!</div>
                    <div id="passwordIncorrectError" class="text-danger d-none">Current password is incorrect!</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="savePasswordBtn">Save Password</button>
            </div>
        </div>
    </div>
</div>
</form>

