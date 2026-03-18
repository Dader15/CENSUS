<?php
// Modal to prompt for Interviewer and Supervisor names on every login
?>
<div class="modal fade" id="interviewerSupervisorModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="interviewerSupervisorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #2caf33 0%, #1f7a1f 100%); color: #fff;">
                <h5 class="modal-title" id="interviewerSupervisorModalLabel">
                    <i class="fas fa-clipboard-list"></i> Set Interviewer & Supervisor
                </h5>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">
                    Please provide the Interviewer and Supervisor details for this session. These will be auto-filled in RBIM forms.
                </p>
                <form id="interviewerSupervisorForm">
                    <h6 class="mb-3"><i class="fas fa-user-tie"></i> Interviewer</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="isInterviewerSurname" class="form-label">Surname <span style="color: red;">*</span></label>
                            <input type="text" class="form-control" id="isInterviewerSurname" placeholder="Surname" required>
                        </div>
                        <div class="col-md-4">
                            <label for="isInterviewerFirstname" class="form-label">First Name <span style="color: red;">*</span></label>
                            <input type="text" class="form-control" id="isInterviewerFirstname" placeholder="First Name" required>
                        </div>
                        <div class="col-md-2">
                            <label for="isInterviewerMI" class="form-label">M.I.</label>
                            <input type="text" class="form-control" id="isInterviewerMI" placeholder="M.I." maxlength="5">
                        </div>
                        <div class="col-md-2">
                            <label for="isInterviewerSuffix" class="form-label">Suffix</label>
                            <input type="text" class="form-control" id="isInterviewerSuffix" placeholder="Suffix" maxlength="10">
                        </div>
                    </div>
                    <hr>
                    <h6 class="mb-3"><i class="fas fa-user-shield"></i> Supervisor</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="isSupervisorSurname" class="form-label">Surname <span style="color: red;">*</span></label>
                            <input type="text" class="form-control" id="isSupervisorSurname" placeholder="Surname" required>
                        </div>
                        <div class="col-md-4">
                            <label for="isSupervisorFirstname" class="form-label">First Name <span style="color: red;">*</span></label>
                            <input type="text" class="form-control" id="isSupervisorFirstname" placeholder="First Name" required>
                        </div>
                        <div class="col-md-2">
                            <label for="isSupervisorMI" class="form-label">M.I.</label>
                            <input type="text" class="form-control" id="isSupervisorMI" placeholder="M.I." maxlength="5">
                        </div>
                        <div class="col-md-2">
                            <label for="isSupervisorSuffix" class="form-label">Suffix</label>
                            <input type="text" class="form-control" id="isSupervisorSuffix" placeholder="Suffix" maxlength="10">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="saveInterviewerSupervisorBtn">
                    <i class="fas fa-check"></i> Confirm & Continue
                </button>
            </div>
        </div>
    </div>
</div>
