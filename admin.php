<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

require_once 'koneksi.php';

// Get stats
$query = "SELECT COUNT(*) as total FROM users";
$users_result = mysqli_query($conn, $query);
$total_users = mysqli_fetch_assoc($users_result)['total'];

$query = "SELECT COUNT(*) as total FROM passengers";
$bookings_result = mysqli_query($conn, $query);
$total_bookings = mysqli_fetch_assoc($bookings_result)['total'];

$query = "SELECT COUNT(*) as total FROM cargo";
$cargo_result = mysqli_query($conn, $query);
$total_cargo = mysqli_fetch_assoc($cargo_result)['total'];

$query = "SELECT SUM(total_harga) as total FROM passengers";
$revenue_result = mysqli_query($conn, $query);
$total_revenue = mysqli_fetch_assoc($revenue_result)['total'] ?? 0;

// Get recent bookings
$query = "SELECT p.*, u.name as customer_name 
          FROM passengers p 
          JOIN users u ON p.user_id = u.id 
          ORDER BY p.tanggal DESC 
          LIMIT 5";
$recent_bookings = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet">
    <title>Admin Dashboard - NauticaBook</title>
    <style>
        .admin-container {
            max-width: var(--max-width);
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card h3 {
            color: var(--text-light);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-card .number {
            color: var(--text-dark);
            font-size: 1.5rem;
            font-weight: 600;
        }

        .admin-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--extra-light);
            padding-bottom: 1rem;
        }

        .tab-btn {
            padding: 0.5rem 1rem;
            background: none;
            border: none;
            color: var(--text-light);
            font-weight: 500;
            cursor: pointer;
            position: relative;
        }

        .tab-btn.active {
            color: var(--primary-color);
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -1rem;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--primary-color);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--white);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 1rem;
        }

        .data-table th,
        .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--extra-light);
        }

        .data-table th {
            background: var(--extra-light);
            font-weight: 600;
            color: var(--text-dark);
        }

        .btn-edit {
            padding: 0.5rem 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-delete {
            padding: 0.5rem 1rem;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .logout-btn {
            padding: 0.5rem 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 1rem;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav__logo">NauticaBook Admin</div>
        <div style="display: flex; align-items: center;">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <form action="logout.php" method="POST" style="margin: 0;">
                <button type="submit" class="logout-btn">
                    <i class="ri-logout-box-line"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-tabs">
            <button class="tab-btn active" onclick="showTab('overview')">Overview</button>
            <button class="tab-btn" onclick="showTab('bookings')">Bookings</button>
            <button class="tab-btn" onclick="showTab('routes')">Routes</button>
            <button class="tab-btn" onclick="showTab('admin_management')">Admin Management</button>
            <button class="tab-btn" onclick="showTab('settings')">Settings</button>
        </div>

        <!-- Overview Tab -->
        <div id="overview" class="tab-content active">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="number"><?php echo number_format($total_users); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Ferry Bookings</h3>
                    <div class="number"><?php echo number_format($total_bookings); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Cargo Shipments</h3>
                    <div class="number"><?php echo number_format($total_cargo); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <div class="number">Rp <?php echo number_format($total_revenue); ?></div>
                </div>
            </div>

            <h2 class="section__header">Recent Bookings</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer</th>
                        <th>Route</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = mysqli_fetch_assoc($recent_bookings)): ?>
                    <tr>
                        <td>#<?php echo $booking['id']; ?></td>
                        <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['asal'] . ' - ' . $booking['tujuan']); ?></td>
                        <td><?php echo date('d M Y', strtotime($booking['tanggal'])); ?></td>
                        <td>
                            <button class="btn-edit" onclick="viewBooking(<?php echo $booking['id']; ?>)">View</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Admin Management Tab -->
        <div id="admin_management" class="tab-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 class="section__header">Admin Management</h2>
                <button class="btn-edit" onclick="openAddAdminModal()">Add New Admin</button>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM admin ORDER BY id";
                    $admin_result = mysqli_query($conn, $query);
                    while ($admin = mysqli_fetch_assoc($admin_result)): 
                    ?>
                    <tr>
                        <td><?php echo $admin['id']; ?></td>
                        <td><?php echo htmlspecialchars($admin['username']); ?></td>
                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                        <td><?php echo $admin['last_login']; ?></td>
                        <td>
                            <button class="btn-edit" onclick="editAdmin(<?php echo $admin['id']; ?>)">Edit</button>
                            <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                            <button class="btn-delete" onclick="deleteAdmin(<?php echo $admin['id']; ?>)">Delete</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Modal untuk Add/Edit Admin -->
            <div id="adminModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <h2 id="modalTitle">Add New Admin</h2>
                    <form id="adminForm" method="POST" action="admin_handlers.php">
                        <input type="hidden" name="action" id="formAction" value="add_admin">
                        <input type="hidden" name="admin_id" id="adminId">
                        
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" id="adminUsername" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="adminEmail" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" id="adminPassword">
                            <small>(Leave blank to keep current password when editing)</small>
                        </div>
                        
                        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                            <button type="submit" class="btn-edit">Save</button>
                            <button type="button" class="btn-delete" onclick="closeModal()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Other tabs -->
        <div id="bookings" class="tab-content">
            <h2 class="section__header">All Bookings</h2>
            <!-- Add bookings content -->
        </div>

        <div id="routes" class="tab-content">
            <h2 class="section__header">Route Management</h2>
            <!-- Add routes content -->
        </div>

        <div id="settings" class="tab-content">
            <h2 class="section__header">Admin Settings</h2>
            <!-- Add settings content -->
        </div>
    </div>

    <script>
        function showTab(tabId) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        function openAddAdminModal() {
            document.getElementById('modalTitle').textContent = 'Add New Admin';
            document.getElementById('formAction').value = 'add_admin';
            document.getElementById('adminId').value = '';
            document.getElementById('adminUsername').value = '';
            document.getElementById('adminEmail').value = '';
            document.getElementById('adminPassword').value = '';
            document.getElementById('adminModal').style.display = 'block';
        }

        function editAdmin(adminId) {
            // Fetch admin data dan tampilkan di modal
            document.getElementById('modalTitle').textContent = 'Edit Admin';
            document.getElementById('formAction').value = 'edit_admin';
            document.getElementById('adminId').value = adminId;
            // Tambahkan AJAX untuk mengambil data admin
            document.getElementById('adminModal').style.display = 'block';
        }

        function deleteAdmin(adminId) {
            if (confirm('Are you sure you want to delete this admin?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'admin_handlers.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_admin';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'admin_id';
                idInput.value = adminId;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function closeModal() {
            document.getElementById('adminModal').style.display = 'none';
        }
    </script>
</body>
</html>
