 // Mobile Menu Toggle
$(document).ready(function() {
    const menuToggle = $('#menuToggle');
    const sidebar = $('.sidebar');
    
    // Toggle sidebar on menu button click
    menuToggle.click(function() {
        sidebar.toggleClass('show');
    });
    
    // Close sidebar when clicking on nav items
    $('.nav-item').click(function() {
        if (window.innerWidth <= 768) {
            sidebar.removeClass('show');
        }
    });
    
    // Close sidebar when clicking outside on mobile
    $(document).click(function(e) {
        if (window.innerWidth <= 768) {
            if (!$(e.target).closest('.sidebar, .menu-toggle').length) {
                sidebar.removeClass('show');
            }
        }
    });
    
    // Handle window resize
    $(window).resize(function() {
        if (window.innerWidth > 768) {
            sidebar.removeClass('show');
        }
    });

    // User Management Navigation
    let currentPage = 'dashboard';

    // Navigate to Dashboard
    $(document).on('click', '.nav-item:has(i.fa-home)', function() {
        location.reload();
    });

    // Navigate to User Management
    $(document).on('click', '.nav-item:has(i.fa-cog)', function() {
        loadUserManagement();
    });

    // Edit Profile
    $(document).on('click', '.user-avatar:has(i.fa-user)', function() {
        // Always fetch current user data from server
        $.ajax({
            url: '../Dashboard/ad_function/ad_function_get_current_user.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 200 && response.data) {
                    let user = response.data;
                    // Populate all profile fields
                    $('#profileSname').val(user.sname || '');
                    $('#profileFname').val(user.fname || '');
                    $('#profileMI').val(user.middleinitial || '');
                    $('#profileSuffix').val(user.suffix || '');
                    $('#profileUsername').val(user.username || '');
                    // Handle brgy stored as "Barangay X" or just "X"
                    let brgyVal = String(user.brgy || '');
                    let brgyMatch = brgyVal.match(/\d+/);
                    $('#profileBarangay').val(brgyMatch ? brgyMatch[0] : brgyVal);
                    $('#profilePosition').val(user.position || '');
                } else {
                    Swal.fire('Error', 'Could not load profile data', 'error');
                    return;
                }
                let profileModal = new bootstrap.Modal(document.getElementById('profileModal'));
                profileModal.show();
            },
            error: function() {
                Swal.fire('Error', 'Could not load profile data', 'error');
            }
        });
    });

    // Load User Management
    function loadUserManagement() {
        currentPage = 'user-management';
        let mainContent = $('.main-content');
        
        // Load content from index_management_user.php
        mainContent.load('../Dashboard/index_management/index_management_user.php', function() {
            // Update sidebar nav-item active state
            $('.nav-item').removeClass('active');
            $('.nav-item:has(i.fa-cog)').addClass('active');

            // Load and populate users
            loadUsers();

            // Add New User Button (use off/on to prevent stacking)
            $(document).off('click.addUser').on('click.addUser', '#addUserBtn', function() {
                resetUserForm();
                $('#userModalLabel').html('<i class="fas fa-user-plus"></i> Add New User');
                $('#deleteStatus').val('0').show();
                $('#username').prop('disabled', false);
                $('#saveUserBtn').text('Add User');
                let userModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('userModal'));
                userModal.show();
            });
        });
    }

    // Load Users
    function loadUsers() {
        $.ajax({
            url: '../Dashboard/ad_function/ad_function_get_users.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 200 || response.success) {
                    let allUsers = response.data || response.users || [];
                    // Filter out current logged-in user from the client side
                    let filteredUsers = allUsers.filter(function(user) {
                        return user.id != window.currentUID;
                    });
                    populateTable(filteredUsers);
                    $('#userCount').text(filteredUsers.length + ' users');
                } else {
                    $('#tableBody').html('<tr><td colspan="8" class="text-center text-danger">Error loading users</td></tr>');
                }
            },
            error: function() {
                $('#tableBody').html('<tr><td colspan="8" class="text-center text-danger">Error connecting to server</td></tr>');
            }
        });
    }

    // Populate Table
    function populateTable(users) {
        let tableBody = $('#tableBody');
        tableBody.empty();

        if (users.length === 0) {
            $('#emptyState').show();
            tableBody.html('<tr><td colspan="8" class="text-center">No users found</td></tr>');
            return;
        }

        $('#emptyState').hide();

        users.forEach(function(user) {
            let statusBadge = user.delete_status === 0 
                ? '<span class="status-badge-active"><i class="fas fa-check-circle"></i> Active</span>'
                : '<span class="status-badge-inactive"><i class="fas fa-times-circle"></i> Inactive</span>';

            let editDataAttrs = `data-id="${user.id}" data-sname="${escapeHtml(user.sname || '')}" data-fname="${escapeHtml(user.fname || '')}" data-mi="${escapeHtml(user.middleinitial || '')}" data-suffix="${escapeHtml(user.suffix || '')}" data-username="${escapeHtml(user.username || '')}" data-usertype="${escapeHtml(user.usertype || '')}" data-brgy="${escapeHtml(user.brgy || '')}" data-position="${escapeHtml(user.position || '')}" data-delete-status="${user.delete_status}"`;

            let actions = user.delete_status === 0
                ? `<div class="action-buttons-container">
                    <button class="action-btn-sm edit-user-btn" ${editDataAttrs} title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="action-btn-sm change-password-btn" data-id="${user.id}" data-name="${escapeHtml(user.full_name)}" data-username="${escapeHtml(user.username)}" title="Change Password"><i class="fas fa-key"></i></button>
                    <button class="action-btn-sm delete-user-btn" data-id="${user.id}" title="Delete"><i class="fas fa-trash"></i></button>
                   </div>`
                : `<div class="action-buttons-container">
                    <button class="action-btn-sm edit-user-btn" ${editDataAttrs} title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="action-btn-sm restore-user-btn" data-id="${user.id}" title="Restore"><i class="fas fa-undo"></i></button>
                   </div>`;

            let createdDate = user['date created'] ? new Date(user['date created']).toLocaleDateString() : 'N/A';

            let row = `<tr>
                <td data-label="Full Name">${escapeHtml(user.full_name)}</td>
                <td data-label="Username">${escapeHtml(user.username)}</td>
                <td data-label="Type">${escapeHtml(user.usertype)}</td>
                <td data-label="Barangay">${user.brgy ? 'Barangay ' + escapeHtml(user.brgy) : 'N/A'}</td>
                <td data-label="Position">${escapeHtml(user.position || 'N/A')}</td>
                <td data-label="Status">${statusBadge}</td>
                <td data-label="Created Date">${createdDate}</td>
                <td data-label="Actions">${actions}</td>
            </tr>`;

            tableBody.append(row);
        });

        // Rebind event handlers (namespaced off/on prevents stacking)
        bindUserTableEvents();
    }

    // Bind User Table Events (once, using namespaced events to prevent stacking)
    function bindUserTableEvents() {
        // Remove any previously bound handlers, then re-bind
        $(document).off('click.userEvents');

        // Edit User
        $(document).on('click.userEvents', '.edit-user-btn', function() {
            let btn = $(this);
            let userId = btn.attr('data-id');

            $('#userId').val(userId);
            $('#userSname').val(btn.attr('data-sname') || '');
            $('#userFname').val(btn.attr('data-fname') || '');
            $('#userMI').val(btn.attr('data-mi') || '');
            $('#userSuffix').val(btn.attr('data-suffix') || '');
            $('#username').val(btn.attr('data-username') || '').prop('disabled', false);
            $('#userType').val(btn.attr('data-usertype') || '');
            // Set barangay - use attr() to get raw string value
            let brgyVal = btn.attr('data-brgy') || '';
            let brgyMatch = brgyVal.match(/\d+/);
            $('#userBarangay').val(brgyMatch ? brgyMatch[0] : brgyVal);
            $('#position').val(btn.attr('data-position') || '');
            
            let deleteStatus = btn.attr('data-delete-status') || '0';
            $('#deleteStatus').val(deleteStatus);

            $('#userModalLabel').html('<i class="fas fa-user-edit"></i> Edit User');
            $('#saveUserBtn').text('Update User');
            let userModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('userModal'));
            userModal.show();
        });

        // Delete User (Soft Delete)
        $(document).on('click.userEvents', '.delete-user-btn', function() {
            let userId = $(this).data('id');
            let fullName = $(this).closest('tr').find('td:eq(0)').text();

            Swal.fire({
                title: 'Deactivate User?',
                text: `Are you sure you want to deactivate ${fullName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Deactivate'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../Dashboard/ad_function/ad_function_delete_user.php',
                        type: 'POST',
                        data: {
                            id: userId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 200) {
                                Swal.fire('Deactivated', 'User has been deactivated', 'success').then(() => {
                                    loadUsers();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'An error occurred', 'error');
                        }
                    });
                }
            });
        });

        // Restore User
        $(document).on('click.userEvents', '.restore-user-btn', function() {
            let userId = $(this).data('id');
            let fullName = $(this).closest('tr').find('td:eq(0)').text();

            Swal.fire({
                title: 'Activate User?',
                text: `Are you sure you want to activate ${fullName}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Activate'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../Dashboard/ad_function/ad_function_restore_user.php',
                        type: 'POST',
                        data: {
                            id: userId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 200) {
                                Swal.fire('Activated', 'User has been activated', 'success').then(() => {
                                    loadUsers();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'An error occurred', 'error');
                        }
                    });
                }
            });
        });

        // Change User Password
        $(document).on('click.userEvents', '.change-password-btn', function() {
            let userId = $(this).data('id');
            let userName = $(this).data('name');
            let username = $(this).data('username');
            
            $('#changePasswordUserId').val(userId);
            $('#changePasswordUsername').text(userName + ' (' + username + ')');
            $('#generatedPassword').val('');
            $('#generatedPasswordDisplay').hide();
            $('#manualPassword').val('');
            $('#confirmManualPassword').val('');
            $('#passwordMismatchError').addClass('d-none');
            
            let changePasswordModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('changeUserPasswordModal'));
            changePasswordModal.show();
        });

        // Generate Password Button
        $(document).on('click.userEvents', '#generatePasswordBtn', function() {
            let userId = $('#changePasswordUserId').val();
            
            $.ajax({
                url: '../Dashboard/ad_function/ad_function_change_user_password.php',
                type: 'POST',
                data: {
                    userId: userId,
                    action: 'generate'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 200) {
                        $('#generatedPassword').val(response.data.password);
                        $('#generatedPasswordDisplay').show();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'An error occurred', 'error');
                }
            });
        });

        // Copy Generated Password
        $(document).on('click.userEvents', '#copyPasswordBtn', function() {
            let password = $('#generatedPassword').val();
            navigator.clipboard.writeText(password).then(() => {
                Swal.fire('Copied!', 'Password copied to clipboard', 'success');
            }).catch(err => {
                Swal.fire('Error', 'Could not copy to clipboard', 'error');
            });
        });

        // Confirm Generated Password
        $(document).on('click.userEvents', '#confirmGeneratedPasswordBtn', function() {
            let userId = $('#changePasswordUserId').val();
            let password = $('#generatedPassword').val();
            
            Swal.fire({
                title: 'Confirm Password Update',
                text: 'The user will be required to change the password on next login.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Save Password'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../Dashboard/ad_function/ad_function_change_user_password.php',
                        type: 'POST',
                        data: {
                            userId: userId,
                            action: 'save',
                            password: password
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 200) {
                                Swal.fire('Success', response.message, 'success').then(() => {
                                    let changePasswordModal = bootstrap.Modal.getInstance(document.getElementById('changeUserPasswordModal'));
                                    if (changePasswordModal) {
                                        changePasswordModal.hide();
                                    }
                                    loadUsers();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'An error occurred', 'error');
                        }
                    });
                }
            });
        });


    }

    // Save User (Add or Update)
    $(document).on('click', '#saveUserBtn', function() {
        let userId = $('#userId').val();
        let sname = $('#userSname').val().trim();
        let fname = $('#userFname').val().trim();
        let middleinitial = $('#userMI').val().trim();
        let suffix = $('#userSuffix').val().trim();
        let username = $('#username').val().trim();
        let userType = $('#userType').val();
        let barangay = $('#userBarangay').val().trim();
        let position = $('#position').val().trim();
        let deleteStatus = $('#deleteStatus').val() || 0;

        if (!sname || !fname || !username || !userType) {
            Swal.fire('Error', 'Please fill in all required fields', 'error');
            return;
        }

        let action = userId ? 'update_user' : 'add_user';
        let data = {
            sname: sname,
            fname: fname,
            middleinitial: middleinitial,
            suffix: suffix,
            usertype: userType,
            brgy: barangay,
            position: position,
            delete_status: deleteStatus,
            username: username
        };

        if (userId) {
            data.id = userId;
        }

        let url = userId ? '../Dashboard/ad_function/ad_function_edit_user.php' : '../Dashboard/ad_function/ad_function_add_user.php';

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.status === 200) {
                    if (!userId && response.data && response.data.generated_password) {
                        // For new users, show the generated password
                        Swal.fire({
                            title: 'User Added Successfully!',
                            html: '<p>User has been created with a temporary password:</p><p style="font-weight: bold; font-size: 1.1em; background: #f0f0f0; padding: 10px; border-radius: 5px; word-break: break-all;">' + response.data.generated_password + '</p><p style="color: #666; font-size: 0.9em;">The user will be required to change this password on first login.</p>',
                            icon: 'success',
                            confirmButtonText: 'Copy & Close',
                            allowOutsideClick: false
                        }).then(() => {
                            // Copy to clipboard
                            navigator.clipboard.writeText(response.data.generated_password).catch(err => {
                                console.log('Failed to copy password');
                            });
                            // Close modal
                            const userModal = document.getElementById('userModal');
                            const modalInstance = bootstrap.Modal.getInstance(userModal);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                            setTimeout(() => {
                                const backdrop = document.querySelector('.modal-backdrop');
                                if (backdrop) {
                                    backdrop.remove();
                                }
                                document.body.classList.remove('modal-open');
                                loadUsers();
                            }, 300);
                        });
                    } else {
                        // For updates
                        Swal.fire('Success', response.message, 'success').then(() => {
                            const userModal = document.getElementById('userModal');
                            const modalInstance = bootstrap.Modal.getInstance(userModal);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                            setTimeout(() => {
                                const backdrop = document.querySelector('.modal-backdrop');
                                if (backdrop) {
                                    backdrop.remove();
                                }
                                document.body.classList.remove('modal-open');
                                loadUsers();
                            }, 300);
                        });
                    }
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'An error occurred while saving user', 'error');
            }
        });
    });

    // Save Profile Changes
    $(document).on('click', '#saveProfileBtn', function() {
        let sname = $('#profileSname').val().trim();
        let fname = $('#profileFname').val().trim();
        let middleinitial = $('#profileMI').val().trim();
        let suffix = $('#profileSuffix').val().trim();
        let username = $('#profileUsername').val().trim();
        let barangay = $('#profileBarangay').val().trim();
        let position = $('#profilePosition').val().trim();

        if (!sname || !fname || !username) {
            Swal.fire('Error', 'Surname, first name and username are required', 'error');
            return;
        }

        let data = {
            id: window.currentUID,
            sname: sname,
            fname: fname,
            middleinitial: middleinitial,
            suffix: suffix,
            username: username,
            brgy: barangay,
            position: position
        };

        $.ajax({
            url: '../Dashboard/ad_function/ad_function_update_profile.php',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.status === 200) {
                    Swal.fire('Success', 'Profile updated successfully', 'success').then(() => {
                        let profileModal = bootstrap.Modal.getInstance(document.getElementById('profileModal'));
                        profileModal.hide();
                        // Refresh the page after successful profile update
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'An error occurred', 'error');
            }
        });
    });

    // Change Password
    $(document).on('click', '#savePasswordBtn', function() {
        let currentPassword = $('#currentPassword').val().trim();
        let newPassword = $('#newPassword').val().trim();
        let confirmPassword = $('#confirmPassword').val().trim();

        if (!currentPassword || !newPassword || !confirmPassword) {
            Swal.fire('Error', 'All fields are required', 'error');
            return;
        }

        if (newPassword !== confirmPassword) {
            $('#passwordMismatchError').removeClass('d-none');
            $('#passwordIncorrectError').addClass('d-none');
            return;
        }

        $('#passwordMismatchError').addClass('d-none');
        $('#passwordIncorrectError').addClass('d-none');

        // Password strength validation
        if (newPassword.length < 12) {
            Swal.fire('Error', 'Password must be at least 12 characters long', 'error');
            return;
        }

        if (!/[A-Z]/.test(newPassword)) {
            Swal.fire('Error', 'Password must contain at least 1 uppercase letter', 'error');
            return;
        }

        if (!/[a-z]/.test(newPassword)) {
            Swal.fire('Error', 'Password must contain at least 1 lowercase letter', 'error');
            return;
        }

        if (!/[0-9]/.test(newPassword)) {
            Swal.fire('Error', 'Password must contain at least 1 number', 'error');
            return;
        }

        if (!/[!@#$%^&*()_+\-=\[\]{};:'",.< >?\/\\|`~]/.test(newPassword)) {
            Swal.fire('Error', 'Password must contain at least 1 symbol (!@#$%^&*() etc)', 'error');
            return;
        }

        $.ajax({
            url: '../Dashboard/ad_function/ad_function_update_password.php',
            type: 'POST',
            data: {
                currentPassword: currentPassword,
                newPassword: newPassword
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 200) {
                    Swal.fire('Success', 'Password updated successfully. You will be logged out.', 'success').then(() => {
                        let changePasswordModal = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
                        if (changePasswordModal) {
                            changePasswordModal.hide();
                        }
                        // Call logout to clear session and redirect to login
                        $.ajax({
                            url: '../assets/php/logout.php',
                            type: 'POST',
                            data: {},
                            success: function() {
                                setTimeout(() => {
                                    window.location.href = '../index.php';
                                }, 500);
                            },
                            error: function() {
                                // Even if logout fails, redirect to login
                                setTimeout(() => {
                                    window.location.href = '../index.php';
                                }, 500);
                            }
                        });
                    });
                } else if (response.status === 401) {
                    $('#passwordMismatchError').addClass('d-none');
                    $('#passwordIncorrectError').removeClass('d-none');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'An error occurred', 'error');
            }
        });
    });

    // Initial Password Change (For users who just had password reset by admin)
    $(document).on('click', '#saveInitialPasswordBtn', function() {
        let newPassword = $('#initialNewPassword').val().trim();
        let confirmPassword = $('#initialConfirmPassword').val().trim();

        if (!newPassword || !confirmPassword) {
            Swal.fire('Error', 'All fields are required', 'error');
            return;
        }

        if (newPassword !== confirmPassword) {
            $('#initialPasswordMismatchError').removeClass('d-none');
            return;
        }

        $('#initialPasswordMismatchError').addClass('d-none');

        // Password strength validation
        if (newPassword.length < 12) {
            Swal.fire('Error', 'Password must be at least 12 characters long', 'error');
            return;
        }

        if (!/[A-Z]/.test(newPassword)) {
            Swal.fire('Error', 'Password must contain at least 1 uppercase letter', 'error');
            return;
        }

        if (!/[a-z]/.test(newPassword)) {
            Swal.fire('Error', 'Password must contain at least 1 lowercase letter', 'error');
            return;
        }

        if (!/[0-9]/.test(newPassword)) {
            Swal.fire('Error', 'Password must contain at least 1 number', 'error');
            return;
        }

        if (!/[^a-zA-Z0-9]/.test(newPassword)) {
            Swal.fire('Error', 'Password must contain at least 1 symbol', 'error');
            return;
        }

        $.ajax({
            url: '../Dashboard/ad_function/ad_function_update_password.php',
            type: 'POST',
            data: {
                currentPassword: '', // Empty for initial password change (no current password to verify)
                newPassword: newPassword,
                isInitialChange: true
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 200) {
                    Swal.fire('Success', 'Password set successfully!', 'success').then(() => {
                        // Reload the page to update session and hide modal
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'An error occurred', 'error');
            }
        });
    });

    // Reset User Form
    function resetUserForm() {
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#username').prop('disabled', false);
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        if (!text) return '';
        let map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
});