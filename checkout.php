<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/goshop_functions.php";

$user = current_user();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: cart.php");
  exit;
}

$res = checkout_cart((int)$user["id"]);

if ($res["ok"]) {
  header("Location: cart.php?checkout=success");
  exit;
}

header("Location: cart.php?checkout=failed&msg=" . urlencode($res["message"]));
exit;
