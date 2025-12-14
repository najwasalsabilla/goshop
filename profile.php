<?php
require_once __DIR__ . "/auth.php";

$active = "profile";
$user   = current_user();

$flash = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name  = trim($_POST["name"] ?? "");
  $phone = trim($_POST["phone"] ?? "");
  $city  = trim($_POST["city"] ?? "");
  $address = trim($_POST["address"] ?? "");


  if ($name === "") {
    $flash = ["ok" => false, "message" => "Nama tidak boleh kosong."];
  } else {

    $avatar = $user["avatar_url"] ?? "";
    update_customer_profile((int)$user["id"], $name, $phone, $city, $address);

    $user  = get_customer_by_id((int)$user["id"]);
    $flash = ["ok" => true, "message" => "Profil berhasil disimpan."];
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GO SHOP - Profil Saya</title>

  <link rel="stylesheet" href="styles.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
    rel="stylesheet"
  />
</head>
<body>
  <div class="app">
    <!-- Topbar -->
    <header class="topbar">
      <div class="topbar-left">
        <div class="logo-circle">GS</div>
        <div>
          <h1 class="app-title">GO SHOP</h1>
          <p class="app-subtitle">Profil Pelanggan</p>
        </div>
      </div>
      <div class="topbar-right">
        <div class="user-info">
          <span class="user-name">Hai, <?= htmlspecialchars($user["name"] ?? "Budi") ?></span>
          <span class="user-role">Pelanggan GO SHOP</span>
        </div>
        <a href="profile.php" class="topbar-avatar-link">
          <img src="assets/profile-budi.jpg" alt="Foto Profil" class="topbar-avatar" />
        </a>
      </div>
    </header>

    <!-- Navbar  -->
    <?php require __DIR__ . "/navbar.php"; ?>

    <main class="main">

      <?php if ($flash): ?>
        <section class="card">
          <p style="margin:0; font-weight:600; color:<?= $flash["ok"] ? "#2ecc71" : "#e74c3c" ?>;">
            <?= htmlspecialchars($flash["message"]) ?>
          </p>
        </section>
      <?php endif; ?>

      <section class="card profile-card">
        <div class="profile-header">
          <div class="profile-avatar-wrapper">
            <img src="assets/profile-budi.jpg" alt="Foto Profil Budi" class="profile-avatar-img" />
          </div>
          <div class="profile-info">
            <h2><?= htmlspecialchars($user["name"] ?? "Budi Santoso") ?></h2>
            <p class="profile-email"><?= htmlspecialchars($user["email"] ?? "budi@example.com") ?></p>
            <p class="profile-tier">
              Tier:
              <span class="badge badge-primary">
                <?= htmlspecialchars($user["tier"] ?? "Member Silver") ?>
              </span>
            </p>
            <p class="profile-meta">
              Bergabung sejak: <strong>Jan 2024</strong>
              • Kota: <strong><?= htmlspecialchars($user["city"] ?? "Jakarta") ?></strong>
            </p>
          </div>
        </div>

        <div class="profile-stats">
          <div class="profile-stat-item">
            <span class="profile-stat-label">Saldo Poin</span>
            <span class="profile-stat-value"><?= (int)($user["points_balance"] ?? 250) ?></span>
          </div>
          <div class="profile-stat-item">
            <span class="profile-stat-label">Total Transaksi</span>
            <span class="profile-stat-value"><?= (int)($user["total_transactions"] ?? 18) ?></span>
          </div>
          <div class="profile-stat-item">
            <span class="profile-stat-label">Hadiah Ditukarkan</span>
            <span class="profile-stat-value"><?= (int)($user["total_rewards_redeemed"] ?? 5) ?></span>
          </div>
        </div>
      </section>

      <section class="card">
        <div class="card-header">
          <h2>Detail Akun</h2>
        </div>

        <form class="profile-form" method="POST" action="profile.php">
          <div class="profile-form-row">
            <label>Nama Lengkap</label>
            <input
              type="text"
              name="name"
              class="input-search"
              value="<?= htmlspecialchars($user["name"] ?? "") ?>"
            />
          </div>

          <div class="profile-form-row">
            <label>Email</label>
            <input
              type="email"
              class="input-search"
              value="<?= htmlspecialchars($user["email"] ?? "") ?>"
              readonly
            />
          </div>

          <div class="profile-form-row">
            <label>No. Telepon</label>
            <input
              type="text"
              name="phone"
              class="input-search"
              value="<?= htmlspecialchars($user["phone"] ?? "") ?>"
            />
          </div>

          <div class="profile-form-row">
            <label>Kota</label>
            <input
              type="text"
              name="city"
              class="input-search"
              value="<?= htmlspecialchars($user["city"] ?? "") ?>"
            />
          </div>

          <div class="profile-form-row">
            <label>Alamat</label>
            <textarea class="input-textarea" name="address" rows="3"><?= htmlspecialchars($user["address"] ?? "") ?></textarea>

          </div>

          <div class="profile-form-row profile-form-actions">
            <button class="btn-primary" type="submit">Simpan Perubahan</button>
          </div>
        </form>
      </section>
    </main>

    <footer class="footer">
      <p>© 2025 GO SHOP Loyalty Platform. Semua hak dilindungi.</p>
    </footer>
  </div>
</body>
</html>
