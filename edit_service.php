<?php
include "db.php";
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include "header.php";

// Validate ID
if (!isset($_GET['id'])) {
    echo "<p>❌ No service ID provided.</p>";
    exit();
}

$id = intval($_GET['id']);

// Fetch current service
$result = mysqli_query($conn, "SELECT * FROM service WHERE id = $id");
if (mysqli_num_rows($result) == 0) {
    echo "<p>❌ Service not found.</p>";
    exit();
}

$service = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $inscription = mysqli_real_escape_string($conn, $_POST['inscription']);

    $update = "UPDATE service SET title = '$title', inscription = '$inscription' WHERE id = $id";
    if (mysqli_query($conn, $update)) {
        echo "<p>✅ Service updated successfully.</p>";
    } else {
        echo "<p>❌ Error: " . mysqli_error($conn) . "</p>";
    }

    // Refresh service data
    $result = mysqli_query($conn, "SELECT * FROM service WHERE id = $id");
    $service = mysqli_fetch_assoc($result);
}
?>

<h2>Edit Service</h2>
<form method="post">
    <label>Title:</label><br>
    <input type="text" name="title" value="<?= htmlspecialchars($service['title']) ?>" required><br><br>

    <label>Inscription:</label><br>
    <textarea name="inscription" rows="4" cols="50"><?= htmlspecialchars($service['inscription']) ?></textarea><br><br>

    <input type="submit" value="Update Service">
</form>
