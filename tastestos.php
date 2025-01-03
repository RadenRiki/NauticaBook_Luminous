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

$query = "SELECT COUNT(*) as total FROM cargo WHERE status = 'aktif'";
$cargo_result = mysqli_query($conn, $query);
$total_cargo = mysqli_fetch_assoc($cargo_result)['total'];

// Update query Total Revenue
$query = "SELECT 
            (SELECT COALESCE(SUM(total_harga), 0) FROM passengers) +
            (SELECT COALESCE(SUM(tc.harga_per_kg * c.berat_kg), 0)
             FROM cargo c
             JOIN tarif_cargo tc ON tc.rute = CONCAT(LOWER(c.asal), '-', LOWER(c.tujuan))
             AND tc.jenis_barang = c.jenis
             WHERE c.status = 'aktif') as total";
$revenue_result = mysqli_query($conn, $query);
$total_revenue = mysqli_fetch_assoc($revenue_result)['total'] ?? 0;

// Update query Recent Bookings
$query = "SELECT 'ferry' as type, p.id, p.user_id, p.asal, p.tujuan, p.tanggal, p.barcode, u.name as customer_name, p.total_harga 
          FROM passengers p 
          JOIN users u ON p.user_id = u.id 
          UNION ALL
          SELECT 'cargo' as type, c.id, c.user_id, c.asal, c.tujuan, c.tanggal, c.barcode, u.name as customer_name, 
                 (SELECT harga_per_kg * c.berat_kg FROM tarif_cargo tc 
                  WHERE tc.rute = CONCAT(LOWER(c.asal), '-', LOWER(c.tujuan)) 
                  AND tc.jenis_barang = c.jenis LIMIT 1) as total_harga
          FROM cargo c 
          JOIN users u ON c.user_id = u.id 
          WHERE c.status = 'aktif'
          ORDER BY tanggal DESC 
          LIMIT 5";
$recent_bookings = mysqli_query($conn, $query);

// Get all bookings for bookings tab
$query = "SELECT p.*, u.name as customer_name 
          FROM passengers p 
          JOIN users u ON p.user_id = u.id 
          ORDER BY p.tanggal DESC";
$all_bookings = mysqli_query($conn, $query);
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
            margin-right: 0.5rem;
        }

        .btn-delete {
            padding: 0.5rem 1rem;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-edit:hover {
            opacity: 0.9;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--extra-light);
            border-radius: 4px;
        }

        .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--extra-light);
            border-radius: 4px;
            background-color: white;
        }

        .form-group input[type="number"] {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--extra-light);
            border-radius: 4px;
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
            background-color: rgba(0, 0, 0, 0.5);
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

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            background: #dbeafe;
            color: #1e40af;
        }

        .filter-controls {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .filter-controls select,
        .filter-controls input {
            padding: 0.5rem;
            border: 1px solid var(--extra-light);
            border-radius: 4px;
            min-width: 200px;
        }

        .booking-details {
            margin: 1.5rem 0;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 4px;
        }

        .booking-details p {
            margin: 0.5rem 0;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .settings-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .settings-card h3 {
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--extra-light);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
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
            <button class="tab-btn" onclick="showTab('cargo')">Cargo Management</button>
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

        <!-- Bookings Tab -->
        <div id="bookings" class="tab-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 class="section__header">All Bookings</h2>
                <div class="filter-controls">
                    <input type="date" id="filterDate" placeholder="Filter by date">
                    <select id="filterRoute">
                        <option value="">All Routes</option>
                        <option value="merak-bakauheni">Merak - Bakauheni</option>
                        <option value="bakauheni-merak">Bakauheni - Merak</option>
                        <option value="ketapang-gilimanuk">Ketapang - Gilimanuk</option>
                        <option value="gilimanuk-ketapang">Gilimanuk - Ketapang</option>
                    </select>
                </div>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer</th>
                        <th>Route</th>
                        <th>Service</th>
                        <th>Type</th>
                        <th>Passengers</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = mysqli_fetch_assoc($all_bookings)): ?>
                    <tr>
                        <td>#<?php echo $booking['id']; ?></td>
                        <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['asal'] . ' - ' . $booking['tujuan']); ?></td>
                        <td><?php echo htmlspecialchars($booking['layanan']); ?></td>
                        <td><?php echo htmlspecialchars($booking['tipe']); ?></td>
                        <td><?php echo $booking['jumlah_penumpang']; ?></td>
                        <td><?php echo date('d M Y', strtotime($booking['tanggal'])); ?></td>
                        <td>
                            <span class="status-badge">Active</span>
                        </td>
                        <td>
                            <button class="btn-edit" onclick="viewBooking(<?php echo $booking['id']; ?>)">View</button>
                            <button class="btn-delete"
                                onclick="deleteBooking(<?php echo $booking['id']; ?>)">Cancel</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Routes Tab -->
        <div id="routes" class="tab-content">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 class="section__header">Route Management</h2>
                <button class="btn-edit" onclick="openAddRouteModal()">Add New Route</button>
            </div>

            <!-- Tab Controls -->
            <div class="filter-controls">
                <button class="tab-btn active" onclick="switchRouteTab('ferry')">Ferry Routes</button>
                <button class="tab-btn" onclick="switchRouteTab('cargo')">Cargo Tariffs</button>
            </div>

            <!-- Ferry Routes Table -->
            <div id="ferryRoutes" class="route-content">
                <div class="filter-controls">
                    <select id="filterRouteService" onchange="filterRoutes()">
                        <option value="">All Services</option>
                        <option value="regular">Regular</option>
                        <option value="express">Express</option>
                    </select>
                    <select id="filterRouteType" onchange="filterRoutes()">
                        <option value="">All Types</option>
                        <option value="Pejalan Kaki">Pejalan Kaki</option>
                        <option value="Sepeda">Sepeda</option>
                        <option value="Motor Kecil">Motor Kecil</option>
                        <option value="Motor Besar">Motor Besar</option>
                        <option value="Mobil">Mobil</option>
                    </select>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Route</th>
                            <th>Service</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                    $query = "SELECT * FROM tarif_layanan ORDER BY rute, layanan, tipe_tiket";
                    $result = mysqli_query($conn, $query);
                    while ($tarif = mysqli_fetch_assoc($result)):
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tarif['rute']); ?></td>
                            <td><?php echo htmlspecialchars($tarif['layanan']); ?></td>
                            <td><?php echo htmlspecialchars($tarif['tipe_tiket']); ?></td>
                            <td><?php echo $tarif['kategori'] ?? '-'; ?></td>
                            <td>Rp <?php echo number_format($tarif['harga']); ?></td>
                            <td>
                                <button class="btn-edit" onclick="editTarif(<?php echo $tarif['id']; ?>)">Edit</button>
                                <button class="btn-delete"
                                    onclick="deleteTarif(<?php echo $tarif['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Cargo Tariffs Table -->
            <div id="cargoTariffs" class="route-content" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h2 class="section__header">Cargo Tariff Management</h2>
                    <button class="btn-edit" onclick="openAddCargoTariffModal()">Add New Tariff</button>
                </div>

                <div class="filter-controls">
                    <select id="filterCargoRoute" onchange="filterCargoTariffs()">
                        <option value="">All Routes</option>
                        <option value="merak-bakauheni">Merak - Bakauheni</option>
                        <option value="bakauheni-merak">Bakauheni - Merak</option>
                        <option value="ketapang-gilimanuk">Ketapang - Gilimanuk</option>
                        <option value="gilimanuk-ketapang">Gilimanuk - Ketapang</option>
                    </select>
                    <select id="filterCargoType" onchange="filterCargoTariffs()">
                        <option value="">All Types</option>
                        <option value="Umum">Umum</option>
                        <option value="Kosmetik dan Kecantikan">Kosmetik dan Kecantikan</option>
                        <option value="Kendaraan">Kendaraan</option>
                        <option value="Peralatan Rumah Tangga">Peralatan Rumah Tangga</option>
                        <option value="Mesin ukuran besar">Mesin ukuran besar</option>
                        <option value="Elektronik">Elektronik</option>
                        <option value="Furniture">Furniture</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Route</th>
                            <th>Type</th>
                            <th>Price per Kg</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                    $query = "SELECT * FROM tarif_cargo WHERE aktif = true ORDER BY rute, jenis_barang";
                    $result = mysqli_query($conn, $query);
                    while ($tarif = mysqli_fetch_assoc($result)):
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tarif['rute']); ?></td>
                            <td><?php echo htmlspecialchars($tarif['jenis_barang']); ?></td>
                            <td>Rp <?php echo number_format($tarif['harga_per_kg']); ?></td>
                            <td>
                                <button class="btn-edit"
                                    onclick="editCargoTariff(<?php echo $tarif['id']; ?>)">Edit</button>
                                <button class="btn-delete"
                                    onclick="deleteCargoTariff(<?php echo $tarif['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Cargo Management Tab -->
        <div id="cargo" class="tab-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 class="section__header">Cargo Management</h2>
                <button class="btn-edit" onclick="openAddCargoTarifModal()">Add New Tariff</button>
            </div>

            <div class="filter-controls">
                <input type="date" id="filterCargoDate" placeholder="Filter by date">
                <select id="filterCargoRoute">
                    <option value="">All Routes</option>
                    <option value="merak-bakauheni">Merak - Bakauheni</option>
                    <option value="bakauheni-merak">Bakauheni - Merak</option>
                    <option value="ketapang-gilimanuk">Ketapang - Gilimanuk</option>
                    <option value="gilimanuk-ketapang">Gilimanuk - Ketapang</option>
                </select>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Route</th>
                        <th>Type</th>
                        <th>Weight</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                $query = "SELECT c.*, u.name as customer_name 
                          FROM cargo c 
                          JOIN users u ON c.user_id = u.id 
                          ORDER BY c.tanggal DESC";
                $result = mysqli_query($conn, $query);
                while ($cargo = mysqli_fetch_assoc($result)): 
                ?>
                    <tr>
                        <td>#<?php echo $cargo['id']; ?></td>
                        <td><?php echo htmlspecialchars($cargo['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($cargo['asal'] . ' - ' . $cargo['tujuan']); ?></td>
                        <td><?php echo htmlspecialchars($cargo['jenis']); ?></td>
                        <td><?php echo $cargo['berat_kg']; ?> kg</td>
                        <td><?php echo date('d M Y', strtotime($cargo['tanggal'])); ?></td>
                        <td>
                            <span class="status-badge"><?php echo ucfirst($cargo['status']); ?></span>
                        </td>
                        <td>
                            <button class="btn-edit" onclick="viewCargo(<?php echo $cargo['id']; ?>)">View</button>
                            <button class="btn-delete"
                                onclick="cancelCargo(<?php echo $cargo['id']; ?>)">Cancel</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Settings Tab -->
        <div id="settings" class="tab-content">
            <h2 class="section__header">Admin Settings</h2>

            <div class="grid-2">
                <!-- Change Password Card -->
                <div class="settings-card">
                    <h3>Change Password</h3>
                    <form id="changePasswordForm" method="POST" action="admin_handlers.php">
                        <input type="hidden" name="action" value="change_password">

                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required>
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" required>
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn-edit">Update Password</button>
                    </form>
                </div>

                <!-- Profile Settings Card -->
                <div class="settings-card">
                    <h3>Profile Settings</h3>
                    <?php
                // Get admin data
                $admin_id = $_SESSION['admin_id'];
                $query = "SELECT * FROM admin WHERE id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $admin_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $admin_data = mysqli_fetch_assoc($result);
                ?>
                    <form id="profileForm" method="POST" action="admin_handlers.php">
                        <input type="hidden" name="action" value="update_profile">

                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username"
                                value="<?php echo htmlspecialchars($admin_data['username']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email"
                                value="<?php echo htmlspecialchars($admin_data['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Notification Preferences</label>
                            <div class="checkbox-group">
                                <input type="checkbox" id="emailNotif" name="email_notifications">
                                <label for="emailNotif">Email Notifications</label>
                            </div>
                        </div>

                        <button type="submit" class="btn-edit">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Route Modal -->
    <div id="routeModal" class="modal">
        <div class="modal-content">
            <h2 id="routeModalTitle">Add New Route</h2>
            <form id="routeForm" method="POST" action="route_handlers.php">
                <input type="hidden" name="action" id="routeFormAction" value="add_route">
                <input type="hidden" name="tarif_id" id="tarifId">

                <div class="form-group">
                    <label>Route</label>
                    <select name="rute" required>
                        <option value="merak-bakauheni">Merak - Bakauheni</option>
                        <option value="bakauheni-merak">Bakauheni - Merak</option>
                        <option value="ketapang-gilimanuk">Ketapang - Gilimanuk</option>
                        <option value="gilimanuk-ketapang">Gilimanuk - Ketapang</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Service</label>
                    <select name="layanan" required>
                        <option value="regular">Regular</option>
                        <option value="express">Express</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Type</label>
                    <select name="tipe_tiket" required>
                        <option value="Pejalan Kaki">Pejalan Kaki</option>
                        <option value="Sepeda">Sepeda</option>
                        <option value="Motor Kecil">Motor Kecil</option>
                        <option value="Motor Besar">Motor Besar</option>
                        <option value="Mobil">Mobil</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select name="kategori">
                        <option value="">-</option>
                        <option value="dewasa">Dewasa</option>
                        <option value="anak">Anak</option>
                        <option value="bayi">Bayi</option>
                        <option value="lansia">Lansia</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Price (Rp)</label>
                    <input type="number" name="harga" required>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="submit" class="btn-edit">Save</button>
                    <button type="button" class="btn-delete" onclick="closeRouteModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cargo Tariff Modal -->
    <div id="cargoTariffModal" class="modal">
        <div class="modal-content">
            <h2 id="cargoTariffModalTitle">Add New Cargo Tariff</h2>
            <form id="cargoTariffForm" method="POST" action="cargo_tariff_handlers.php">
                <input type="hidden" name="action" id="cargoTariffFormAction" value="add_tariff">
                <input type="hidden" name="tariff_id" id="cargoTariffId">

                <div class="form-group">
                    <label>Route</label>
                    <select name="rute" required>
                        <option value="merak-bakauheni">Merak - Bakauheni</option>
                        <option value="bakauheni-merak">Bakauheni - Merak</option>
                        <option value="ketapang-gilimanuk">Ketapang - Gilimanuk</option>
                        <option value="gilimanuk-ketapang">Gilimanuk - Ketapang</option>
                        <option value="tanjungpriok-merak">Tanjung Priok - Merak</option>
                        <option value="tanjungpriok-bakauheni">Tanjung Priok - Bakauheni</option>
                        <option value="tanjungpriok-tanjungperak">Tanjung Priok - Tanjung Perak</option>
                        <option value="tanjungperak-tanjungpriok">Tanjung Perak - Tanjung Priok</option>
                        <option value="tanjungperak-merak">Tanjung Perak - Merak</option>
                        <option value="tanjungperak-bakauheni">Tanjung Perak - Bakauheni</option>
                        <option value="tanjungperak-ketapang">Tanjung Perak - Ketapang</option>
                        <option value="tanjungperak-gilimanuk">Tanjung Perak - Gilimanuk</option>
                        <option value="merak-tanjungpriok">Merak - Tanjung Priok</option>
                        <option value="merak-tanjungperak">Merak - Tanjung Perak</option>
                        <option value="bakauheni-tanjungpriok">Bakauheni - Tanjung Priok</option>
                        <option value="bakauheni-tanjungperak">Bakauheni - Tanjung Perak</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Type</label>
                    <select name="jenis_barang" required>
                        <option value="Umum">Umum</option>
                        <option value="Kosmetik dan Kecantikan">Kosmetik dan Kecantikan</option>
                        <option value="Kendaraan">Kendaraan</option>
                        <option value="Peralatan Rumah Tangga">Peralatan Rumah Tangga</option>
                        <option value="Mesin ukuran besar">Mesin ukuran besar</option>
                        <option value="Elektronik">Elektronik</option>
                        <option value="Furniture">Furniture</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Price per Kg (Rp)</label>
                    <input type="number" name="harga_per_kg" required>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="submit" class="btn-edit">Save</button>
                    <button type="button" class="btn-delete" onclick="closeCargoTariffModal()">Cancel</button>
                </div>
            </form>
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

        function viewBooking(bookingId) {
            // Create modal for booking details
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.style.display = 'block';
            // Fetch booking details
            fetch(`get_booking_details.php?id=${bookingId}`)
                .then(response => response.json())
                .then(booking => {
                    modal.innerHTML = `
                        <div class="modal-content">
                            <h2>Booking Details #${bookingId}</h2>
                            <div class="booking-details">
                                <p><strong>Customer:</strong> ${booking.customer_name}</p>
                                <p><strong>Route:</strong> ${booking.asal} - ${booking.tujuan}</p>
                                <p><strong>Date:</strong> ${booking.tanggal}</p>
                                <p><strong>Service:</strong> ${booking.layanan}</p>
                                <p><strong>Type:</strong> ${booking.tipe}</p>
                                <p><strong>Passengers:</strong> ${booking.jumlah_penumpang}</p>
                                <p><strong>Time:</strong> ${booking.jam}</p>
                                <p><strong>Barcode:</strong> ${booking.barcode}</p>
                                <p><strong>Total Price:</strong> Rp ${Number(booking.total_harga).toLocaleString()}</p>
                            </div>
                            <div class="booking-details">
                                <h3>Booking Contact</h3>
                                <p><strong>Name:</strong> ${booking.nama_pemesan}</p>
                                <p><strong>Email:</strong> ${booking.email_pemesan}</p>
                                <p><strong>Phone:</strong> ${booking.nomor_hp}</p>
                            </div>
                            <button onclick="closeModal()" class="btn-delete">Close</button>
                        </div>
                    `;
                    document.body.appendChild(modal);
                })
                .catch(error => {
                    console.error('Error fetching booking details:', error);
                    alert('Failed to load booking details');
                });
        }

        function closeModal() {
            const modal = document.querySelector('.modal');
            if (modal) {
                modal.remove();
            }
        }

        function deleteBooking(bookingId) {
            if (confirm('Are you sure you want to cancel this booking?')) {
                fetch('cancel_booking.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `booking_id=${bookingId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Booking cancelled successfully');
                            location.reload();
                        } else {
                            alert('Failed to cancel booking: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to cancel booking');
                    });
            }
        }
        // Filter functionality for bookings
        const filterDate = document.getElementById('filterDate');
        const filterRoute = document.getElementById('filterRoute');

        function applyFilters() {
            const rows = document.querySelectorAll('#bookings .data-table tbody tr');
            const dateValue = filterDate.value;
            const routeValue = filterRoute.value.toLowerCase();
            rows.forEach(row => {
                const date = row.querySelector('td:nth-child(7)').textContent;
                const route = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const dateMatch = !dateValue || date.includes(dateValue);
                const routeMatch = !routeValue || route.includes(routeValue);
                if (dateMatch && routeMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        if (filterDate) filterDate.addEventListener('change', applyFilters);
        if (filterRoute) filterRoute.addEventListener('change', applyFilters);
        // Route Management Functions
        function openAddRouteModal() {
            document.getElementById('routeModalTitle').textContent = 'Add New Route';
            document.getElementById('routeFormAction').value = 'add_route';
            document.getElementById('tarifId').value = '';
            document.getElementById('routeForm').reset();
            document.getElementById('routeModal').style.display = 'block';
        }

        function closeRouteModal() {
            document.getElementById('routeModal').style.display = 'none';
        }

        function editTarif(id) {
            fetch(`get_tarif_details.php?id=${id}`)
                .then(response => response.json())
                .then(tarif => {
                    if (tarif.error) {
                        alert(tarif.error);
                        return;
                    }
                    document.getElementById('routeModalTitle').textContent = 'Edit Route';
                    document.getElementById('routeFormAction').value = 'edit_route';
                    document.getElementById('tarifId').value = id;
                    document.querySelector('select[name="rute"]').value = tarif.rute;
                    document.querySelector('select[name="layanan"]').value = tarif.layanan;
                    document.querySelector('select[name="tipe_tiket"]').value = tarif.tipe_tiket;
                    document.querySelector('select[name="kategori"]').value = tarif.kategori || '';
                    document.querySelector('input[name="harga"]').value = tarif.harga;
                    document.getElementById('routeModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load route details');
                });
        }

        function deleteTarif(id) {
            if (confirm('Are you sure you want to delete this route? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'route_handlers.php';
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_route';
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'tarif_id';
                idInput.value = id;
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function filterRoutes() {
            const serviceValue = document.getElementById('filterRouteService').value.toLowerCase();
            const typeValue = document.getElementById('filterRouteType').value;
            const rows = document.querySelectorAll('#routes .data-table tbody tr');
            rows.forEach(row => {
                const service = row.cells[1].textContent.toLowerCase();
                const type = row.cells[2].textContent;
                const serviceMatch = !serviceValue || service === serviceValue;
                const typeMatch = !typeValue || type === typeValue;
                if (serviceMatch && typeMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        // Form validation listeners
        document.getElementById('routeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const harga = this.querySelector('input[name="harga"]').value;
            if (harga <= 0) {
                alert('Price must be greater than 0');
                return;
            }
            this.submit();
        });
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const newPass = this.querySelector('input[name="new_password"]').value;
            const confirmPass = this.querySelector('input[name="confirm_password"]').value;
            if (newPass !== confirmPass) {
                alert('New password and confirmation do not match');
                return;
            }
            this.submit();
        });
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[name="email"]').value;
            if (!email.includes('@')) {
                alert('Please enter a valid email address');
                return;
            }
            const username = this.querySelector('input[name="username"]').value;
            if (username.length < 3) {
                alert('Username must be at least 3 characters long');
                return;
            }
            this.submit();
        });
        // Modal event listeners
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                closeModal();
                if (document.getElementById('routeModal')) {
                    closeRouteModal();
                }
            }
        };
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
                if (document.getElementById('routeModal')) {
                    closeRouteModal();
                }
            }
        });
        // Tambahkan fungsi ini di bagian script
        function viewCargo(cargoId) {
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.style.display = 'block';
            fetch(`get_cargo_details.php?id=${cargoId}`)
                .then(response => response.json())
                .then(result => {
                    if (!result.success) {
                        throw new Error(result.message || 'Failed to load cargo details');
                    }
                    const cargo = result.data;
                    modal.innerHTML = `
                <div class="modal-content">
                    <h2>Cargo Details #${cargoId}</h2>
                    <div class="booking-details">
                        <p><strong>Customer:</strong> ${cargo.customer}</p>
                        <p><strong>Route:</strong> ${cargo.route}</p>
                        <p><strong>Type:</strong> ${cargo.type}</p>
                        <p><strong>Weight:</strong> ${cargo.weight}</p>
                        <p><strong>Date:</strong> ${cargo.date}</p>
                        <p><strong>Status:</strong> ${cargo.status}</p>
                        <p><strong>Total Price:</strong> Rp${cargo.total_harga}</p>
                        <p><strong>Barcode:</strong> ${cargo.barcode}</p>
                    </div>
                    <div class="booking-details">
                        <h3>Sender Details</h3>
                        <p><strong>Name:</strong> ${cargo.pengirim.nama}</p>
                        <p><strong>Address:</strong> ${cargo.pengirim.alamat}</p>
                        <p><strong>City:</strong> ${cargo.pengirim.kota}</p>
                        <p><strong>Postal Code:</strong> ${cargo.pengirim.kodepos}</p>
                        <p><strong>Phone:</strong> ${cargo.pengirim.telepon}</p>
                    </div>
                    <div class="booking-details">
                        <h3>Recipient Details</h3>
                        <p><strong>Name:</strong> ${cargo.penerima.nama}</p>
                        <p><strong>Address:</strong> ${cargo.penerima.alamat}</p>
                        <p><strong>City:</strong> ${cargo.penerima.kota}</p>
                        <p><strong>Postal Code:</strong> ${cargo.penerima.kodepos}</p>
                        <p><strong>Phone:</strong> ${cargo.penerima.telepon}</p>
                    </div>
                    ${cargo.catatan ? `
                    <div class="booking-details">
                        <h3>Notes</h3>
                        <p>${cargo.catatan}</p>
                    </div>` : ''}
                    <button onclick="closeModal()" class="btn-delete">Close</button>
                </div>
            `;
                    document.body.appendChild(modal);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load cargo details');
                });
        }

        function cancelCargo(cargoId) {
            if (confirm('Are you sure you want to cancel this cargo shipment?')) {
                fetch('cancel_cargo.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `cargo_id=${cargoId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Cargo shipment cancelled successfully');
                            location.reload();
                        } else {
                            alert('Failed to cancel cargo shipment: ' + data.message);
                        }
                    });
            }
        }
        // Tambahkan fungsi ini di bagian script
        const filterCargoDate = document.getElementById('filterCargoDate');
        const filterCargoRoute = document.getElementById('filterCargoRoute');

        function applyCargoFilters() {
            const rows = document.querySelectorAll('#cargo .data-table tbody tr');
            const dateValue = filterCargoDate.value;
            const routeValue = filterCargoRoute.value.toLowerCase();
            rows.forEach(row => {
                const date = row.querySelector('td:nth-child(6)').textContent;
                const route = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const dateMatch = !dateValue || date.includes(dateValue);
                const routeMatch = !routeValue || route.includes(routeValue);
                row.style.display = (dateMatch && routeMatch) ? '' : 'none';
            });
        }
        if (filterCargoDate) filterCargoDate.addEventListener('change', applyCargoFilters);
        if (filterCargoRoute) filterCargoRoute.addEventListener('change', applyCargoFilters);
    </script>
</body>

</html>