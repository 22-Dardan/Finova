<?php

include '../config/db.php';

$id     = $_POST['finance_id'];
$amount = $_POST['amount'];
$date   = $_POST['date'];

$stmt = $conn->prepare("
    UPDATE finances
    SET amount = ?, date = ?
    WHERE id = ?
");

$stmt->bind_param(
    "dsi",
    $amount,
    $date,
    $id
);

$stmt->execute();

echo json_encode([
    "success" => true,
    "message" => "Rroga u përditësua me sukses"
]);