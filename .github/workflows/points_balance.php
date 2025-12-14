<?php
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../goshop_functions.php";

$user = current_user();
if (!$user || !isset($user["id"])) {
  echo json_encode(["ok" => false, "message" => "Unauthorized"]);
  exit;
}

function pick_balance($u) {
  foreach (["points_balance", "points", "point_balance", "saldo_poin"] as $k) {
    if (isset($u[$k]) && $u[$k] !== null && $u[$k] !== "") return (int)$u[$k];
  }
  return null;
}

$balance = pick_balance($user);

if ($balance === null && function_exists("get_customer_by_id")) {
  $fresh = get_customer_by_id((int)$user["id"]);
  $balance = $fresh ? pick_balance($fresh) : null;
}

echo json_encode([
  "ok" => true,
  "balance" => (int)($balance ?? 0),
]);
