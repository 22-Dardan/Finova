<?php

header('Content-Type: application/json');
include '../config/db.php';

$job_id = $_POST['job_id'] ?? null;

if (!$job_id) {
    echo json_encode([
        "success" => false,
        "message" => "Missing job ID"
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| Get existing job (IMPORTANT for safe updates)
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();

if (!$current) {
    echo json_encode([
        "success" => false,
        "message" => "Job not found"
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| Use new values OR keep old ones
|--------------------------------------------------------------------------
*/
$company_name = $_POST['company_name'] ?? $current['company_name'];
$position     = $_POST['position'] ?? $current['position'];
$location     = $_POST['location'] ?? $current['location'];
$start_date   = $_POST['start_date'] ?? $current['start_date'];
$status       = $_POST['status'] ?? $current['status'];

/*
|--------------------------------------------------------------------------
| END DATE (ONLY IF SENT)
|--------------------------------------------------------------------------
*/
$end_date = $current['end_date'];

if (isset($_POST['end_date']) && $_POST['end_date'] !== '') {
    $end_date = $_POST['end_date'];
}

/*
|--------------------------------------------------------------------------
| LOGO (ONLY IF UPLOADED)
|--------------------------------------------------------------------------
*/
$logo = $current['company_logo'];

if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === 0) {

    $allowed = ["image/jpeg", "image/png", "image/webp"];

    if (!in_array($_FILES['company_logo']['type'], $allowed)) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid logo type"
        ]);
        exit;
    }

    $extension = pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION);
    $newName = uniqid() . "." . $extension;

    $uploadFolder = "../uploads/logos/";

    if (!is_dir($uploadFolder)) {
        mkdir($uploadFolder, 0777, true);
    }

    move_uploaded_file(
        $_FILES['company_logo']['tmp_name'],
        $uploadFolder . $newName
    );

    $logo = "uploads/logos/" . $newName;
}

/*
|--------------------------------------------------------------------------
| UPDATE QUERY
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    UPDATE jobs
    SET
        company_name = ?,
        position = ?,
        location = ?,
        start_date = ?,
        status = ?,
        end_date = ?,
        company_logo = ?
    WHERE id = ?
");

$stmt->bind_param(
    "sssssssi",
    $company_name,
    $position,
    $location,
    $start_date,
    $status,
    $end_date,
    $logo,
    $job_id
);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Puna u përditësua me sukses"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => $stmt->error
    ]);
}