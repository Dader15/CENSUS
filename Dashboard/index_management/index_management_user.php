<header class="top-header">
    <div class="header-left">
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
        <h2>User Management</h2>
    </div>
</header>

<div class="page-content">
    <div class="content-header">
        <div class="header-info">
            <h1>Manage Users</h1>
            <p>Add, edit, or manage user accounts and permissions</p>
        </div>
        <button class="btn btn-primary btn-lg" id="addUserBtn">
            <i class="fas fa-plus"></i>
            Add New User
        </button>
    </div>

    <div class="filter-and-table">
        <div class="table-panel">
            <div class="table-title">
                <h3>User Accounts</h3>
                <span class="entry-count" id="userCount">0 users</span>
            </div>

            <div class="table-wrapper">
                <table class="modern-table" id="usersTable">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Type</th>
                            <th>Barangay</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                    </tbody>
                </table>
                <div class="empty-state" id="emptyState" style="display: none;">
                    <div class="empty-placeholder">
                        <i class="fas fa-inbox"></i>
                        <p>No users found</p>
                        <small>Create a new user to get started</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
