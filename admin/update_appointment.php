<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

$id     = (int) ($_GET['id'] ?? 0);
$status = $_GET['status'] ?? '';

if ($id && in_array($status, ['Pending', 'Confirmed', 'Completed', 'Cancelled'], true)) {
    $stmt = $pdo->prepare('UPDATE appointments SET status = ? WHERE appointment_id = ?');
    $stmt->execute([$status, $id]);
}

redirect('index.php');
