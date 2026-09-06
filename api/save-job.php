<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

include '../config/db.php';


if (!isset($conn)) {
    echo json_encode([
        "success" => false,
        "message" => "DB connection failed"
    ]);
    exit;
}


$company_name = $_POST['company_name'] ?? '';
$position = $_POST['position'] ?? '';
$location = $_POST['location'] ?? '';
$start_date = $_POST['start_date'] ?? '';
$status = $_POST['status'] ?? 'active';


if (!$company_name || !$position || !$location || !$start_date) {
    echo json_encode([
        "success" => false,
        "message" => "Missing fields"
    ]);
    exit;
}


/* LOGO UPLOAD */

$logo = null;


if(isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === 0){

    $allowed = [
        "image/jpeg",
        "image/png",
        "image/webp"
    ];


    if(!in_array($_FILES['company_logo']['type'], $allowed)){
        echo json_encode([
            "success" => false,
            "message" => "Invalid logo type"
        ]);
        exit;
    }


    $extension = pathinfo(
        $_FILES['company_logo']['name'],
        PATHINFO_EXTENSION
    );


    $newName = uniqid() . "." . $extension;


    // adjust path depending where this file is
    $uploadFolder = "../uploads/logos/";


    if(!is_dir($uploadFolder)){
        mkdir($uploadFolder, 0777, true);
    }


    move_uploaded_file(
        $_FILES['company_logo']['tmp_name'],
        $uploadFolder . $newName
    );


    $logo = "uploads/logos/" . $newName;
}




$stmt = $conn->prepare("
    INSERT INTO jobs 
    (company_name, position, location, start_date, status, company_logo)
    VALUES (?, ?, ?, ?, ?, ?)
");


if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);
    exit;
}


$stmt->bind_param(
    "ssssss",
    $company_name,
    $position,
    $location,
    $start_date,
    $status,
    $logo
);


if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Vendi i Punes u Ruajt"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => $stmt->error
    ]);
}

?>