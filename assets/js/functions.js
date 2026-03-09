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
        if (currentPage === 'user-management') {
            let fullName = $('p', '.user-details').text();
            let position = $('small', '.user-details').text();
            $('#profileFullName').val(fullName);
            $('#profilePosition').val(position);
            let profileModal = new bootstrap.Modal(document.getElementById('profileModal'));
            profileModal.show();
        } else {
            // Get current user data from session
            $.ajax({
                url: '../Dashboard/ad_function/ad_function_get_current_user.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 200 && response.data) {
                        let user = response.data;
                        // Populate display fields
                        $('#displayUsername').text(user.username || '-');
                        $('#displayBarangay').text(user.brgy || '-');
                        $('#profileFullName').val(user.full_name);
                        $('#profileUsername').val(user.username);
                        $('#profileBarangay').val(user.brgy || '');
                        $('#profilePosition').val(user.position || '');
                        $('#profilePassword').val('');
                        $('#profileConfirmPassword').val('');
                    }
                    let profileModal = new bootstrap.Modal(document.getElementById('profileModal'));
                    profileModal.show();
                },
                error: function() {
                    Swal.fire('Error', 'Could not load profile data', 'error');
                }
            });
        }
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

            // Rebind menu toggle
            $(document).on('click', '#menuToggle', function() {
                $('.sidebar').toggleClass('show');
            });

            // Load and populate users
            loadUsers();

            // Add New User Button
            $(document).on('click', '#addUserBtn', function() {
                resetUserForm();
                $('#userModalLabel').html('<i class="fas fa-user-plus"></i> Add New User');
                $('#deleteStatus').val('0').show();
                $('#password').prop('required', true).prop('placeholder', 'Min. 12 chars: 1 uppercase, 1 lowercase, 1 number, 1 symbol');
                $('#confirmPassword').prop('required', true).prop('placeholder', 'Min. 12 chars: 1 uppercase, 1 lowercase, 1 number, 1 symbol');
                $('#username').prop('disabled', false);
                $('#saveUserBtn').text('Add User');
                let userModal = new bootstrap.Modal(document.getElementById('userModal'));
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

            let actions = user.delete_status === 0
                ? `<div class="action-buttons-container">
                    <button class="action-btn-sm edit-user-btn" data-id="${user.id}" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="action-btn-sm delete-user-btn" data-id="${user.id}" title="Delete"><i class="fas fa-trash"></i></button>
                   </div>`
                : `<div class="action-buttons-container">
                    <button class="action-btn-sm edit-user-btn" data-id="${user.id}" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="action-btn-sm restore-user-btn" data-id="${user.id}" title="Restore"><i class="fas fa-undo"></i></button>
                   </div>`;

            let createdDate = user['date created'] ? new Date(user['date created']).toLocaleDateString() : 'N/A';

            let row = `<tr>
                <td data-label="Full Name">${escapeHtml(user.full_name)}</td>
                <td data-label="Username">${escapeHtml(user.username)}</td>
                <td data-label="Type">${escapeHtml(user.usertype)}</td>
                <td data-label="Barangay">${escapeHtml(user.brgy || 'N/A')}</td>
                <td data-label="Position">${escapeHtml(user.position || 'N/A')}</td>
                <td data-label="Status">${statusBadge}</td>
                <td data-label="Created Date">${createdDate}</td>
                <td data-label="Actions">${actions}</td>
            </tr>`;

            tableBody.append(row);
        });

        // Rebind event handlers
        bindUserTableEvents();
    }

    // Bind User Table Events
    function bindUserTableEvents() {
        // Edit User
        $(document).on('click', '.edit-user-btn', function() {
            let userId = $(this).data('id');
            let row = $(this).closest('tr');
            
            $('#userId').val(userId);
            $('#fullName').val(row.find('td:eq(0)').text());
            $('#username').val(row.find('td:eq(1)').text()).prop('disabled', false);
            $('#userType').val(row.find('td:eq(2)').text());
            $('#barangay').val(row.find('td:eq(3)').text());
            $('#position').val(row.find('td:eq(4)').text());
            
            // Determine delete_status from badge
            let statusText = row.find('td:eq(5)').text();
            let deleteStatus = statusText.includes('Inactive') ? 1 : 0;
            $('#deleteStatus').val(deleteStatus);
            
            $('#password').val('').prop('required', false).prop('placeholder', 'Leave blank to keep current password');
            $('#confirmPassword').val('').prop('required', false).prop('placeholder', 'Leave blank to keep current password');
            $('#deleteStatus').show();

            $('#userModalLabel').html('<i class="fas fa-user-edit"></i> Edit User');
            $('#saveUserBtn').text('Update User');
            let userModal = new bootstrap.Modal(document.getElementById('userModal'));
            userModal.show();
        });

        // Delete User (Soft Delete)
        $(document).on('click', '.delete-user-btn', function() {
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
        $(document).on('click', '.restore-user-btn', function() {
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
    }

    // Save User (Add or Update)
    $(document).on('click', '#saveUserBtn', function() {
        let userId = $('#userId').val();
        let fullName = $('#fullName').val().trim();
        let username = $('#username').val().trim();
        let password = $('#password').val();
        let confirmPassword = $('#confirmPassword').val();
        let userType = $('#userType').val();
        let barangay = $('#barangay').val().trim();
        let position = $('#position').val().trim();
        let deleteStatus = $('#deleteStatus').val() || 0;

        if (!fullName || !username || !userType) {
            Swal.fire('Error', 'Please fill in all required fields', 'error');
            return;
        }

        if (!userId && (!password || !confirmPassword)) {
            Swal.fire('Error', 'Password is required for new users', 'error');
            return;
        }

        if (password && password !== confirmPassword) {
            Swal.fire('Error', 'Passwords do not match', 'error');
            return;
        }

        // Password strength validation for new users and edit (only if password provided)
        if (password) {
            if (password.length < 12) {
                Swal.fire('Error', 'Password must be at least 12 characters long', 'error');
                return;
            }

            if (!/[A-Z]/.test(password)) {
                Swal.fire('Error', 'Password must contain at least 1 uppercase letter', 'error');
                return;
            }

            if (!/[a-z]/.test(password)) {
                Swal.fire('Error', 'Password must contain at least 1 lowercase letter', 'error');
                return;
            }

            if (!/[0-9]/.test(password)) {
                Swal.fire('Error', 'Password must contain at least 1 number', 'error');
                return;
            }

            if (!/[!@#$%^&*()_+\-=\[\]{};:'",.< >?\/\\|`~]/.test(password)) {
                Swal.fire('Error', 'Password must contain at least 1 symbol (!@#$%^&*() etc)', 'error');
                return;
            }
        } else if (!userId) {
            // Password is required for new users
            Swal.fire('Error', 'Password is required for new users', 'error');
            return;
        }

        let action = userId ? 'update_user' : 'add_user';
        let data = {
            full_name: fullName,
            usertype: userType,
            brgy: barangay,
            position: position,
            delete_status: deleteStatus
        };

        if (userId) {
            data.id = userId;
            data.username = username;
            if (password) {
                data.password = password;
            }
        } else {
            data.username = username;
            data.password = password;
        }

        let url = userId ? '../Dashboard/ad_function/ad_function_edit_user.php' : '../Dashboard/ad_function/ad_function_add_user.php';

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.status === 200) {
                    Swal.fire('Success', response.message, 'success').then(() => {
                        // Close modal properly
                        const userModal = document.getElementById('userModal');
                        const modalInstance = bootstrap.Modal.getInstance(userModal);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                        // Remove backdrop manually if it persists
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
        let fullName = $('#profileFullName').val().trim();
        let username = $('#profileUsername').val().trim();
        let barangay = $('#profileBarangay').val().trim();
        let position = $('#profilePosition').val().trim();
        let password = $('#profilePassword').val();
        let confirmPassword = $('#profileConfirmPassword').val();

        if (!fullName || !username) {
            Swal.fire('Error', 'Full name and username are required', 'error');
            return;
        }

        if (password && password !== confirmPassword) {
            Swal.fire('Error', 'Passwords do not match', 'error');
            return;
        }

        // Password strength validation (only if password provided)
        if (password) {
            if (password.length < 12) {
                Swal.fire('Error', 'Password must be at least 12 characters long', 'error');
                return;
            }

            if (!/[A-Z]/.test(password)) {
                Swal.fire('Error', 'Password must contain at least 1 uppercase letter', 'error');
                return;
            }

            if (!/[a-z]/.test(password)) {
                Swal.fire('Error', 'Password must contain at least 1 lowercase letter', 'error');
                return;
            }

            if (!/[0-9]/.test(password)) {
                Swal.fire('Error', 'Password must contain at least 1 number', 'error');
                return;
            }

            if (!/[!@#$%^&*()_+\-=\[\]{};:'",.< >?\/\\|`~]/.test(password)) {
                Swal.fire('Error', 'Password must contain at least 1 symbol (!@#$%^&*() etc)', 'error');
                return;
            }
        }

        let data = {
            id: window.currentUID,
            full_name: fullName,
            username: username,
            brgy: barangay,
            position: position
        };

        if (password) {
            data.password = password;
        }

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
                        // Logout the user after successful profile update
                        setTimeout(() => {
                            window.location.href = '../assets/php/logout.php';
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

    // Reset User Form
    function resetUserForm() {
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#username').prop('disabled', false);
        $('#password').prop('required', true);
        $('#confirmPassword').prop('required', true);
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