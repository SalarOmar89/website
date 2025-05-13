<?php
include "db.php";
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location:login.php");
    exit();
}

$id = $_GET['id'];
$sql = "DELETE FROM carousel WHERE id = $id";
if (mysqli_query($conn, $sql)) {
    header("Location:dashboard.php");
} else {
    echo "Error deleting carousel item: " . mysqli_error($conn);
}
?>
