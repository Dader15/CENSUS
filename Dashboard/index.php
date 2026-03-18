<?php
session_start();
include_once '../assets/php/retrieve.php';
// Check if user is logged in
if (!isset($_SESSION['UID'])) {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RBIM Analytics Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/census_ui.css" rel="stylesheet">
    <link href="../assets/css/form_builder.css" rel="stylesheet">
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <div class="user-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <span>RBIM</span>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-item active">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </div>
                <?php if ($_SESSION['usertype'] === 'ADMIN' || $_SESSION['usertype'] === 'SUPERADMIN'): ?>
                <!-- <div class="nav-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Barangay Management</span>
                </div> -->
                <?php endif; ?>
                

                <?php if ($_SESSION['usertype'] === 'ADMIN' || $_SESSION['usertype'] === 'SUPERADMIN'): ?>
                <div class="nav-item" style="cursor: pointer;">
                    <i class="fas fa-cog"></i>
                    <span>User Management</span>
                </div>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar" style="cursor: pointer;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-details">
                        <p><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Guest'); ?></p>
                        <small><?php echo htmlspecialchars($_SESSION['position'] ?? 'User'); ?></small>
                        <div id="sidebarInterviewerInfo" style="margin-top: 6px; font-size: 11px; color: #ccc; display: none;">
                            <div><i class="fas fa-user-tie" style="width: 14px;"></i> <span id="sidebarInterviewerName"></span></div>
                            <div><i class="fas fa-user-shield" style="width: 14px;"></i> <span id="sidebarSupervisorName"></span></div>
                        </div>
                    </div>
                </div>
                <button class="logout-btn" id="submitLogout" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2>Dashboard</h2>
                </div>
                <!-- <div class="header-right">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="globalSearch" placeholder="Search...">
                    </div>
                    <button class="icon-btn">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </button>
                    <button class="icon-btn">
                        <i class="fas fa-envelope"></i>
                    </button>
                </div> -->
            </header>

            <!-- Page Content -->
            <div class="page-content">
                <!-- Content Header -->
                <div class="content-header" id="contentHeader">
                    <div class="header-info">
                        <h1>RBIM Data</h1>
                        <p>Manage and track registry entries across all barangays of Caloocan City</p>
                    </div>
                    <button class="btn btn-primary btn-lg" id="createNewBtn">
                        <i class="fas fa-plus"></i>
                        Create New Entry
                    </button>
                </div>

                <!-- Form Container (Hidden by default) -->
                <div id="censusFormContainer" style="display: none;">
                    <!-- Census Form will be loaded here -->
                </div>

                <!-- Stats and Table Container -->
                <div id="mainContentContainer">

                <!-- Stats -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div style="flex: 1;">
                                <p class="stat-label">Total Barangays</p>
                                <h3 class="stat-value" id="totalBarangays">193</h3>
                            </div>
                            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        </div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> <span>All covered</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div style="flex: 1;">
                                <p class="stat-label">Total Entries</p>
                                <h3 class="stat-value" id="totalEntries">0</h3>
                            </div>
                            <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <i class="fas fa-list"></i>
                            </div>
                        </div>
                        <div class="stat-change neutral">
                            <span id="entriesTrend">0 new</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div style="flex: 1;">
                                <p class="stat-label">Population</p>
                                <h3 class="stat-value" id="totalPopulation">0</h3>
                            </div>
                            <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> <span id="populationTrend">0%</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div style="flex: 1;">
                                <p class="stat-label">Households</p>
                                <h3 class="stat-value" id="totalHouseholds">0</h3>
                            </div>
                            <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                <i class="fas fa-home"></i>
                            </div>
                        </div>
                        <div class="stat-change neutral">
                            <span id="householdsTrend">0 new</span>
                        </div>
                    </div>
                </div>

                <!-- Filter and Table -->
                <div class="filter-and-table">
                    <!-- Filter Panel -->
                    <div class="filter-panel">
                        <div class="filter-title">
                            <h3><i class="fas fa-filter"></i> Filters</h3>
                            <button class="filter-reset" id="resetFilters">Reset</button>
                        </div>

                        <div class="filter-controls">
                            <div class="filter-item">
                                <label>Search by Name</label>
                                <div class="input-group">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search entries...">
                                </div>
                            </div>

                            <div class="filter-item">
                                <label>Select Barangay</label>
                                <select id="barangayFilter" class="form-control">
                                    <option value="">All Barangays</option>
                                </select>
                            </div>

                            <div class="filter-item">
                                <label>Status</label>
                                <select id="statusFilter" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="Completed">Completed</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Pending">Pending</option>
                                </select>
                            </div>

                            <div class="filter-item">
                                <label>Quick Search</label>
                                <button class="btn btn-sm btn-primary w-100" style="margin-top: 8px;">
                                    <i class="fas fa-search"></i> Advanced Search
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table Panel -->
                    <div class="table-panel">
                        <div class="table-title">
                            <h3>RBIM Records</h3>
                            <span class="entry-count" id="recordCount">0 entries</span>
                        </div>

                        <div class="table-wrapper">
                            <table class="modern-table" id="censusTable">
                                <thead>
                                    <tr>
                                        <th>Barangay</th>
                                        <th>Entry Name</th>
                                        <th>Population</th>
                                        <th>Households</th>
                                        <th>Status</th>
                                        <th>Recorded By</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <!-- Rows will have: <td data-label="Barangay">Value</td> -->
                                </tbody>
                            </table>
                            <div class="empty-state" id="emptyState" style="display: none;">
                                <div class="empty-placeholder">
                                    <i class="fas fa-inbox"></i>
                                    <p>No RBIM records found</p>
                                    <small>Try adjusting your filters or create a new entry</small>
                                </div>
                            </div>
                        </div>

                        <!-- Table Footer -->
                        <div class="table-footer">
                            <div class="pagination-wrapper">
                                <ul class="pagination" id="pagination"></ul>
                            </div>
                            <div class="rows-control">
                                <label for="rowsPerPage">Show:</label>
                                <select id="rowsPerPage" class="form-control-sm">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                                <span>entries</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Modals -->
    <?php include 'index_modal/index_modal_census_form.php'; ?>
    <?php include 'index_modal/index_modal_user.php'; ?>
    <?php include 'index_modal/index_modal_edit_profile.php'; ?>
    <?php include 'index_modal/index_modal_changepass.php'; ?>
    <?php include 'index_modal/index_modal_change_user_password.php'; ?>
    <?php include 'index_modal/index_modal_initial_password_change.php'; ?>
    <?php include 'index_modal/index_modal_interviewer_supervisor.php'; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/ajax/ajax_login_logout.js"></script>
    <script src="../assets/js/functions.js"></script>
    <script src="../assets/js/form_builder.js?v=1.0.1"></script>
    <script>
        // Set current user ID for client-side filtering
        window.currentUID = <?php echo isset($_SESSION['UID']) ? intval($_SESSION['UID']) : '0'; ?>;
        window.currentUserName = "<?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : 'User'; ?>";
        window.changedPassword = <?php echo isset($_SESSION['changedpassword']) ? intval($_SESSION['changedpassword']) : '1'; ?>;
        
        // Set encoder name and user barangay for form
        document.body.dataset.encoderName = "<?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : 'User'; ?>";
        window.userBrgy = "<?php echo isset($_SESSION['brgy']) ? htmlspecialchars($_SESSION['brgy']) : ''; ?>";

        // Load interviewer/supervisor data from session (persisted across page reloads)
        <?php if (!empty($_SESSION['interviewer_surname'])): ?>
        window.interviewerData = {
            surname: "<?php echo htmlspecialchars($_SESSION['interviewer_surname'] ?? ''); ?>",
            firstname: "<?php echo htmlspecialchars($_SESSION['interviewer_firstname'] ?? ''); ?>",
            mi: "<?php echo htmlspecialchars($_SESSION['interviewer_mi'] ?? ''); ?>",
            suffix: "<?php echo htmlspecialchars($_SESSION['interviewer_suffix'] ?? ''); ?>"
        };
        <?php endif; ?>
        <?php if (!empty($_SESSION['supervisor_surname'])): ?>
        window.supervisorData = {
            surname: "<?php echo htmlspecialchars($_SESSION['supervisor_surname'] ?? ''); ?>",
            firstname: "<?php echo htmlspecialchars($_SESSION['supervisor_firstname'] ?? ''); ?>",
            mi: "<?php echo htmlspecialchars($_SESSION['supervisor_mi'] ?? ''); ?>",
            suffix: "<?php echo htmlspecialchars($_SESSION['supervisor_suffix'] ?? ''); ?>"
        };
        <?php endif; ?>
        
        $(document).ready(function() {
            // Pre-fill interviewer/supervisor modal if session data exists
            if (window.interviewerData) {
                $('#isInterviewerSurname').val(window.interviewerData.surname);
                $('#isInterviewerFirstname').val(window.interviewerData.firstname);
                $('#isInterviewerMI').val(window.interviewerData.mi);
                $('#isInterviewerSuffix').val(window.interviewerData.suffix);
            }
            if (window.supervisorData) {
                $('#isSupervisorSurname').val(window.supervisorData.surname);
                $('#isSupervisorFirstname').val(window.supervisorData.firstname);
                $('#isSupervisorMI').val(window.supervisorData.mi);
                $('#isSupervisorSuffix').val(window.supervisorData.suffix);
            }

            // Show initial password change modal if user needs to change password
            if (window.changedPassword == 0) {
                let initialPasswordModal = new bootstrap.Modal(document.getElementById('initialPasswordChangeModal'));
                initialPasswordModal.show();
            } else {
                // Only show interviewer/supervisor prompt if no session data exists
                if (!window.interviewerData || !window.supervisorData) {
                    showInterviewerSupervisorModal();
                } else {
                    updateSidebarNames();
                }
            }

            // Handle Interviewer/Supervisor modal save
            $('#saveInterviewerSupervisorBtn').on('click', function() {
                const iSurname = $('#isInterviewerSurname').val().trim();
                const iFirstname = $('#isInterviewerFirstname').val().trim();
                const sSurname = $('#isSupervisorSurname').val().trim();
                const sFirstname = $('#isSupervisorFirstname').val().trim();

                if (!iSurname || !iFirstname) {
                    Swal.fire({ icon: 'warning', title: 'Missing Information', text: 'Please fill in the Interviewer Surname and First Name.', confirmButtonColor: '#2caf33' });
                    return;
                }
                if (!sSurname || !sFirstname) {
                    Swal.fire({ icon: 'warning', title: 'Missing Information', text: 'Please fill in the Supervisor Surname and First Name.', confirmButtonColor: '#2caf33' });
                    return;
                }

                // Store in window variables for census form auto-fill
                window.interviewerData = {
                    surname: iSurname,
                    firstname: iFirstname,
                    mi: $('#isInterviewerMI').val().trim(),
                    suffix: $('#isInterviewerSuffix').val().trim()
                };
                window.supervisorData = {
                    surname: sSurname,
                    firstname: sFirstname,
                    mi: $('#isSupervisorMI').val().trim(),
                    suffix: $('#isSupervisorSuffix').val().trim()
                };

                // Save to PHP session so data persists across page reloads
                fetch('../assets/php/save_interviewer_supervisor.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        interviewer: window.interviewerData,
                        supervisor: window.supervisorData
                    })
                }).catch(err => console.error('Failed to save interviewer/supervisor to session:', err));

                // Update sidebar display
                updateSidebarNames();

                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('interviewerSupervisorModal')).hide();
            });

            // After initial password change succeeds, show interviewer/supervisor modal
            $(document).on('initialPasswordChanged', function() {
                if (!window.interviewerData || !window.supervisorData) {
                    showInterviewerSupervisorModal();
                } else {
                    updateSidebarNames();
                }
            });
            
            // Create New Entry button handler
            $('#createNewBtn').on('click', function() {
                // Load form content into container
                const modalContent = document.getElementById('censusFormModal');
                if (modalContent) {
                    const formHTML = modalContent.innerHTML;
                    $('#censusFormContainer').html(formHTML);
                }
                
                // Show census form
                showCensusForm();
                // Disable button during form entry
                $(this).prop('disabled', true).css('opacity', '0.6');
            });
        });
        
        function showCensusForm() {
            // Show the form container
            $('#mainContentContainer').fadeOut(300, function() {
                $('#censusFormContainer').fadeIn(300);
            });
            
            // Reset and reinitialize the form
            window.censusFormBuilder = new CensusFormBuilder();
        }
        
        function hideCensusForm() {
            // Hide the form container
            $('#censusFormContainer').fadeOut(300, function() {
                $('#mainContentContainer').fadeIn(300);
            });
            
            // Re-enable create button
            $('#createNewBtn').prop('disabled', false).css('opacity', '1');
        }

        function showInterviewerSupervisorModal() {
            // If session data already loaded, update sidebar immediately
            if (window.interviewerData && window.supervisorData) {
                updateSidebarNames();
            }
            let isModal = new bootstrap.Modal(document.getElementById('interviewerSupervisorModal'));
            isModal.show();
        }

        function updateSidebarNames() {
            if (window.interviewerData && window.supervisorData) {
                const iName = [window.interviewerData.surname, window.interviewerData.firstname, window.interviewerData.mi, window.interviewerData.suffix].filter(Boolean).join(' ');
                const sName = [window.supervisorData.surname, window.supervisorData.firstname, window.supervisorData.mi, window.supervisorData.suffix].filter(Boolean).join(' ');
                $('#sidebarInterviewerName').text(iName);
                $('#sidebarSupervisorName').text(sName);
                $('#sidebarInterviewerInfo').show();
            }
        }
    </script>
    <!-- <script src="census_ui.js"></script> -->
</body>
</html>
