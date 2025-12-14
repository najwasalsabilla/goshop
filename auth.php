<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . "/config.php";
require_once __DIR__ . "/goshop_functions.php";

function current_user(): array {
  $email = "budi@example.com";

  $u = get_customer_by_email($email);
  if (!$u) {
    die("User demo ($email) tidak ditemukan di tabel customers. Silakan insert dulu data customer.");
  }
  return $u;
}
