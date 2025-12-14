<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo "STEP 1 OK<br>";

require_once __DIR__ . "/config.php";
echo "STEP 2 OK (config loaded)<br>";

require_once __DIR__ . "/goshop_functions.php";
echo "STEP 3 OK (functions loaded)<br>";

require_once __DIR__ . "/auth.php";
echo "STEP 4 OK (auth loaded)<br>";

$user = current_user();
echo "STEP 5 OK (current_user)<br>";

echo "AUTH OK: " . htmlspecialchars($user["email"]) . " | poin=" . (int)$user["points_balance"];
