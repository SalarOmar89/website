<?php
// image.php
include "db.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT image FROM carousel WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($imageData);
        $stmt->fetch();

        header("Content-Type: image/jpeg");
        echo $imageData;
    } else {
        http_response_code(404);
        echo "Image not found.";
    }
    $stmt->close();
} else {
    http_response_code(400);
    echo "Invalid request.";
}
?>
