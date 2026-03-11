<?php
// User Management Modal: Add/Edit user form
?>
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel"><i class="fas fa-user-plus"></i> Add New User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="needs-validation" id="userForm" novalidate>
          <input type="hidden" id="userId" name="userId">
          
          <div class="form-row four-col">
            <div class="col-md-3 mb-3">
              <label for="userSname">Surname <span style="color: red;">*</span></label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-user"></i></span>
                </div>
                <input type="text" class="form-control" id="userSname" placeholder="Surname" required>
              </div>
            </div>
            <div class="col-md-3 mb-3">
              <label for="userFname">First Name <span style="color: red;">*</span></label>
              <div class="input-group">
                <input type="text" class="form-control" id="userFname" placeholder="First Name" required>
              </div>
            </div>
            <div class="col-md-3 mb-3">
              <label for="userMI">M.I.</label>
              <div class="input-group">
                <input type="text" class="form-control" id="userMI" placeholder="M.I." maxlength="5">
              </div>
            </div>
            <div class="col-md-3 mb-3">
              <label for="userSuffix">Suffix</label>
              <div class="input-group">
                <input type="text" class="form-control" id="userSuffix" placeholder="Suffix" maxlength="10">
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="col-md-6 mb-3">
              <label for="username">Username <span style="color: red;">*</span></label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                </div>
                <input type="text" class="form-control" id="username" placeholder="Username" required>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="position">Position</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-briefcase"></i></span>
                </div>
                <input type="text" class="form-control" id="position" placeholder="Position">
              </div>
            </div>
          </div>



          <div class="form-row">
            <div class="col-md-6 mb-3">
              <label for="userType">User Type <span style="color: red;">*</span></label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-tasks"></i></span>
                </div>
                <select class="form-control" id="userType" required>
                  <option value="">Select User Type</option>
                  <?php if ($_SESSION['usertype'] === 'SUPERADMIN'): ?>
                  <option value="SUPERADMIN">SUPERADMIN</option>
                  <?php endif; ?>
                  <option value="ADMIN">ADMIN</option>
                  <option value="USER">USER</option>
                </select>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="barangay">Barangay</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-map-marker"></i></span>
                </div>
                <select class="form-control" id="barangay">
                  <option value="">Select Barangay</option>
                  <?php for ($i = 1; $i <= 193; $i++): ?>
                    <option value="<?php echo $i; ?>">Barangay <?php echo $i; ?></option>
                  <?php endfor; ?>
                </select>
              </div>
            </div>
          </div>

          <div class="form-row">
            
            <div class="col-md-6 mb-3">
              <label for="deleteStatus">Status</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-info-circle"></i></span>
                </div>
                <select class="form-control" id="deleteStatus">
                  <option value="0" selected>Active</option>
                  <option value="1">Inactive</option>
                </select>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="saveUserBtn"><i class="fa fa-check"></i> Add User</button>
      </div>
    </div>
  </div>
</div>
