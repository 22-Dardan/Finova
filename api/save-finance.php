<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

include __DIR__ . '/../config/db.php';

try {

    $job_id = $_POST['job_id'] ?? null;
    $amount = $_POST['amount'] ?? null;
    $date = $_POST['date'] ?? null;

    if (!$job_id || !$amount || !$date) {
        echo json_encode([
            "success" => false,
            "message" => "Diqka Mungon"
        ]);
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO finances (job_id, amount, date)
        VALUES (?, ?, ?)
    ");

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param("ids", $job_id, $amount, $date);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Te Ardhurat u Ruajten"
        ]);
    } else {
        throw new Exception($stmt->error);
    }

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}