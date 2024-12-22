<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Ambil data pengguna dari session
$name = $_SESSION['name'];
$email = $_SESSION['email'];
$referral = $_SESSION['referral'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles.css">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet">
  <title>Profile</title>
</head>

<body>
  <!-- Navigation -->
  <nav>
    <div class="nav__logo">NauticaBook</div>
    <ul class="nav__links">
      <li class="link"><a href="home.html">Home</a></li>
      <li class="link"><a href="#">MyTickets</a></li>
      <li class="link"><a href="offers.html">Offers</a></li>
    </ul>
    <div class="profile-icon">
      <i class="ri-user-3-line"></i>
    </div>
  </nav>

  <!-- Profile Section -->
  <section class="section__container profile__container">
    <h1>Profil Pengguna</h1>
    <div class="profile__details">
      <!-- Informasi Pengguna -->
      <div class="profile__info">
        <h2>Informasi Pengguna</h2>
        <p><strong>Nama:</strong> <?php echo htmlspecialchars($name); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        <p><strong>Referral Code:</strong> <?php echo htmlspecialchars($referral); ?></p>
        <button class="edit-btn" onclick="openEditModal()">Edit Profil</button>
        <button class="password-btn" onclick="openPasswordModal()">Ubah Password</button>
      </div>
    </div>
  </section>

  <!-- Modal Edit Profil -->
  <div class="modal" id="editModal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeEditModal()">&times;</span>
      <h2>Edit Profil</h2>
      <form id="editProfileForm" action="update_profile.php" method="POST">
        <div class="form-group">
          <label for="editName">Nama</label>
          <input type="text" id="editName" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>
        <div class="form-group">
          <label for="editEmail">Email</label>
          <input type="email" id="editEmail" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <button type="submit" class="btn">Simpan Perubahan</button>
      </form>
    </div>
  </div>

  <!-- Modal Ubah Password -->
  <div class="modal" id="passwordModal">
    <div class="modal-content">
      <span class="close-btn" onclick="closePasswordModal()">&times;</span>
      <h2>Ubah Password</h2>
      <form id="changePasswordForm" action="update_password.php" method="POST">
        <div class="form-group">
          <label for="currentPassword">Password Lama</label>
          <input type="password" id="currentPassword" name="current_password" placeholder="Masukkan password lama"
            required>
        </div>
        <div class="form-group">
          <label for="newPassword">Password Baru</label>
          <input type="password" id="newPassword" name="new_password" placeholder="Masukkan password baru" required>
        </div>
        <div class="form-group">
          <label for="confirmPassword">Konfirmasi Password Baru</label>
          <input type="password" id="confirmPassword" name="confirm_password" placeholder="Konfirmasi password baru"
            required>
        </div>
        <button type="submit" class="btn">Ubah Password</button>
      </form>
    </div>
  </div>

  <!-- Logout Button -->
  <div class="logout-container">
    <button class="logout-btn" onclick="logout()">Logout</button>
  </div>

  <footer class="footer">
    <div class="section__container footer__container">
      <div class="footer__col">
        <h3>NauticaBook</h3>
        <p>
          NauticaBook adalah platform untuk booking ferry dan cargo secara cepat dan terpercaya.
          Pengguna dapat mencari jadwal, membandingkan harga, dan memesan tiket dengan mudah dalam beberapa langkah.
        </p>
        <p>
          NauticaBook menawarkan antarmuka ramah pengguna untuk pengalaman pemesanan nyaman,
          baik untuk perjalanan pribadi, bisnis, maupun logistik. Dengan mitra terpercaya,
          layanan pelanggan responsif, dan sistem pembayaran aman, NauticaBook memenuhi kebutuhan transportasi laut
          Anda.
        </p>
      </div>
      <div class="footer__col">
        <h4>Tim Luminous</h4>
        <p>Raden Riki Hilviastari Saputra (23523217)</p>
        <p>Arvindra Ahmad Ramadhan (23523128)</p>
        <p> Taqya Maritsa Hakim (23523108)</p>
        <p>-</p>
      </div>
      <div class="footer__col">
        <h4>Legal</h4>
        <p>FAQs</p>
        <p>Terms & Conditions</p>
        <p>Privacy Policy</p>
      </div>
      <div class="footer__col">
        <h4>Resources</h4>
        <p>Social Media</p>
        <p>Help Center</p>
        <p>Partnerships</p>
      </div>
    </div>
    </div>
    <div class="footer__bar">
      Copyright © 2024 Webs by Luminous. All rights reserved.
    </div>
  </footer>

  <script>
    // Edit Profil Modal
    function openEditModal() {
      document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
      document.getElementById('editModal').style.display = 'none';
    }
    document.getElementById('editProfileForm').addEventListener('submit', function(e) {
      closeEditModal();
    });
    // Password Modal
    function openPasswordModal() {
      document.getElementById('passwordModal').style.display = 'block';
    }

    function closePasswordModal() {
      document.getElementById('passwordModal').style.display = 'none';
    }
    document.getElementById('changePasswordForm').addEventListener('submit', function() {
      closePasswordModal();
    });
    // Logout
    // Di profile.php
    function logout() {
    // Hapus session storage
    sessionStorage.clear();
    // Redirect ke logout.php untuk menghapus PHP session
    window.location.href = "logout.php";
    }
  </script>
</body>

</html>