<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

require_once __DIR__ . "/auth.php";
$user   = current_user();
$active = "cart";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $action = $_POST["action"] ?? "";

  // UPDATE QTY
  if ($action === "update" && isset($_POST["qty"]) && is_array($_POST["qty"])) {
    foreach ($_POST["qty"] as $product_id => $qty) {
      cart_update((int)$product_id, (int)$qty);
    }
    header("Location: cart.php");
    exit;
  }

  // REMOVE ITEM
  if ($action === "remove" && isset($_POST["product_id"])) {
    cart_remove((int)$_POST["product_id"]);
    header("Location: cart.php");
    exit;
  }

  // CLEAR CART
  if ($action === "clear") {
    cart_clear();
    header("Location: cart.php");
    exit;
  }

  // CHECKOUT 
  if ($action === "checkout") {
    cart_clear();
    header("Location: cart.php?checkout=ok");
    exit;
  }
}

$cartData = cart_items_with_details();
$items = $cartData["items"] ?? [];
$total = $cartData["total"] ?? 0;

$checkout_ok = isset($_GET["checkout"]);

// ==== AVATAR ====
$avatarSrc = $user["avatar_url"] ?: "assets/profile-budi.jpg";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>GO SHOP - Cart</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
<div class="app">

  <!-- TOPBAR -->
  <header class="topbar">
    <div class="topbar-left">
      <div class="logo-circle">GS</div>
      <div>
        <h1 class="app-title">GO SHOP</h1>
        <p class="app-subtitle">Keranjang</p>
      </div>
    </div>

    <div class="topbar-right">
      <div class="user-info">
        <span class="user-name">Hai, <?= htmlspecialchars($user["name"] ?? "User") ?></span>
        <span class="user-role">Pelanggan GO SHOP</span>
      </div>
        <a href="profile.php" class="topbar-avatar-link">
         <img src="assets/profile-budi.jpg" alt="Foto Profil" class="topbar-avatar" />
        </a>
    </div>
  </header>

  <!-- NAVBAR -->
  <?php require __DIR__ . "/navbar.php"; ?>

  <main class="main">

    <?php if ($checkout_ok): ?>
      <section class="card" style="border:1px solid rgba(46,204,113,.4);">
        <p style="margin:0;font-weight:600;">✅ Checkout berhasil! Keranjang dikosongkan.</p>
      </section>
    <?php endif; ?>

    <section class="card">
      <div class="card-header">
        <h2>Keranjang</h2>
        <p class="points-desc" style="margin:0;">Kelola produk sebelum checkout</p>
      </div>

      <?php if (!count($items)): ?>
        <p class="points-desc">Keranjang masih kosong.</p>
      <?php else: ?>

        <form method="post" style="margin-top:10px;">
          <div class="table-wrapper">
            <table class="table">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th>Harga</th>
                  <th style="width:120px;">Qty</th>
                  <th class="text-right">Subtotal</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($items as $it): ?>
                <tr>
                  <td><?= htmlspecialchars($it["name"]) ?></td>
                  <td>Rp <?= number_format((int)$it["price"], 0, ",", ".") ?></td>
                  <td>
                    <input
                      type="number"
                      name="qty[<?= (int)$it["id"] ?>]"
                      value="<?= (int)$it["qty"] ?>"
                      min="1"
                      max="<?= max(1, (int)$it["stock"]) ?>"
                      class="input-search"
                      style="width:90px;"
                    />
                  </td>
                  <td class="text-right">
                    Rp <?= number_format((int)$it["subtotal"], 0, ",", ".") ?>
                  </td>
                  <td>
                    <button
                      type="submit"
                      class="btn-small"
                      name="action"
                      value="remove"
                      onclick="document.getElementById('pid').value='<?= (int)$it["id"] ?>'"
                    >
                      Hapus
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <input type="hidden" name="product_id" id="pid" value="">

          <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin-top:14px;flex-wrap:wrap;">
            <div style="font-weight:700;color:var(--primary-dark);">
              Total: Rp <?= number_format((int)$total, 0, ",", ".") ?>
            </div>

            <div style="display:flex;gap:10px;flex-wrap:wrap;">
              <button class="btn-small" type="submit" name="action" value="update">
                Update Qty
              </button>

              <button class="btn-small" type="submit" name="action" value="clear"
                onclick="return confirm('Kosongkan keranjang?')">
                Clear
              </button>

              <button class="btn-small" type="submit" name="action" value="checkout"
                style="background:var(--primary-dark);"
                onclick="return confirm('Checkout sekarang?')">
                Checkout
              </button>
            </div>
          </div>
        </form>

      <?php endif; ?>
    </section>
  </main>

  <footer class="footer">
    <p>© 2025 GO SHOP Loyalty Platform. Semua hak dilindungi.</p>
  </footer>

</div>
</body>
</html>
