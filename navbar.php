<?php
function mclass($key, $active) {
  return $key === $active ? "menu-link active" : "menu-link";
}
?>
<nav class="menu">
  <a href="dashboard.php" class="<?= mclass("dashboard", $active ?? "") ?>">Dashboard</a>
  <a href="redeem.php" class="<?= mclass("redeem", $active ?? "") ?>">Redeem Poin</a>
  <a href="products.php" class="<?= mclass("products", $active ?? "") ?>">Produk & Stok</a>
  <a href="cart.php" class="<?= mclass("cart", $active ?? "") ?>">Cart</a>
  <a href="profile.php" class="<?= mclass("profile", $active ?? "") ?>">Profil Saya</a>
  <a href="reviews.php" class="<?= mclass("reviews", $active ?? "") ?>">Review Pelanggan</a>
</nav>
