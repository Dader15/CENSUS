<?php
// Change Password Modal for Admin
?>
<div class="modal fade" id="changeUserPasswordModal" tabindex="-1" aria-labelledby="changeUserPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeUserPasswordModalLabel"><i class="fas fa-key"></i> Change User Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="changePasswordUserId" value="">
                
                <div class="mb-3">
                    <label class="form-label">User: <strong id="changePasswordUsername"></strong></label>
                </div>
                
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle"></i> 
                    A random 12-character password will be generated (1 uppercase, 1 lowercase, 1 number, 1 symbol). 
                    The user will be required to change it on next login.
                </div>
                <button type="button" class="btn btn-primary w-100" id="generatePasswordBtn">
                    <i class="fas fa-random"></i> Generate Password
                </button>
                <div id="generatedPasswordDisplay" class="mt-3" style="display: none;">
                    <label class="form-label">Generated Password:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="generatedPassword" readonly style="font-weight: bold; background-color: #f8f9fa;">
                        <button class="btn btn-outline-secondary" type="button" id="copyPasswordBtn" title="Copy to clipboard">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <button type="button" class="btn btn-success w-100 mt-2" id="confirmGeneratedPasswordBtn">
                        <i class="fas fa-check"></i> Confirm & Update Password
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
