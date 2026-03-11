<?php
// Initial Password Change Modal - For users who just had their password reset by admin
?>
<div class="modal fade" id="initialPasswordChangeModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="initialPasswordChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="initialPasswordChangeModalLabel">
                    <i class="fas fa-lock"></i> Set Your Password
                </h5>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">
                    Your password was reset by an administrator. Please set a new password to continue.
                </p>
                <form id="initialPasswordChangeForm">
                    <div class="mb-3">
                        <label for="initialNewPassword" class="form-label">New Password <span style="color: red;">*</span></label>
                        <input type="password" class="form-control" id="initialNewPassword" placeholder="Min. 12 chars: 1 uppercase, 1 lowercase, 1 number, 1 symbol" required>
                    </div>
                    <div class="alert alert-info alert-sm" role="alert" style="font-size: 0.9rem; margin-bottom: 15px;">
                        <i class="fas fa-info-circle"></i> 
                        Password must be at least 12 characters and contain 1 uppercase, 1 lowercase, 1 number, and 1 symbol
                    </div>
                    <div class="mb-3">
                        <label for="initialConfirmPassword" class="form-label">Confirm Password <span style="color: red;">*</span></label>
                        <input type="password" class="form-control" id="initialConfirmPassword" placeholder="Confirm new password" required>
                    </div>
                    <div id="initialPasswordMismatchError" class="text-danger d-none" style="margin-bottom: 10px;">Passwords do not match!</div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveInitialPasswordBtn">Save Password</button>
            </div>
        </div>
    </div>
</div>
