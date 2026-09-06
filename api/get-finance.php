<?php

include '../config/db.php';

$id = (int)$_GET['id'];

$result = $conn->query("
    SELECT *
    FROM finances
    WHERE id = $id
");

echo json_encode($result->fetch_assoc());