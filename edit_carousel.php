<?php
include "db.php";
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include "header.php";

$id = mysqli_real_escape_string($conn, $_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM carousel WHERE id = $id");
$row = mysqli_fetch_assoc($result);

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $caption = mysqli_real_escape_string($conn, $_POST['caption']);
    
    // Initialize image variable with existing value
    $image = $row['image'];
    
    // Handle file upload if new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $imageData = file_get_contents($_FILES['image']['tmp_name']);
            
            // Use prepared statement for binary data
            $stmt = $conn->prepare("UPDATE carousel SET image=?, caption=? WHERE id=?");
            $stmt->bind_param("bsi", $null, $caption, $id);
            $stmt->send_long_data(0, $imageData);

            if ($stmt->execute()) {
                $success = "✅ Carousel item updated successfully!";
                // Refresh the row data
                $result = mysqli_query($conn, "SELECT * FROM carousel WHERE id = $id");
                $row = mysqli_fetch_assoc($result);
            } else {
                $error = "❌ Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "❌ Only JPG, PNG, and GIF images are allowed.";
        }
    } else {
        // Update only caption if no new image is uploaded
        $sql = "UPDATE carousel SET caption='$caption' WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            $success = "✅ Carousel caption updated successfully!";
        } else {
            $error = "❌ Error: " . mysqli_error($conn);
        }
    }
}
?>

<style>
    :root {
        --primary: #4361ee;
        --secondary: #3f37c9;
        --accent: #4895ef;
        --light: #f8f9fa;
        --dark: #212529;
        --success: #4cc9f0;
        --danger: #f72585;
        --border-radius: 8px;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }
    
    .form-container {
        max-width: 700px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
    }
    
    .form-title {
        color: var(--primary);
        text-align: center;
        margin-bottom: 1.5rem;
        font-size: 2rem;
        font-weight: 600;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--dark);
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: var(--border-radius);
        font-size: 1rem;
        transition: var(--transition);
    }
    
    .form-control:focus {
        border-color: var(--accent);
        outline: none;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    }
    
    .btn {
        display: inline-block;
        background: var(--primary);
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: var(--border-radius);
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        text-align: center;
    }
    
    .btn:hover {
        background: var(--secondary);
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }
    
    .btn-block {
        display: block;
        width: 100%;
    }
    
    .alert {
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: var(--border-radius);
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    
    .alert-danger {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    
    .file-upload {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 2rem;
        border: 2px dashed #ddd;
        border-radius: var(--border-radius);
        background: #f9fafb;
        cursor: pointer;
        transition: var(--transition);
        text-align: center;
    }
    
    .file-upload:hover {
        border-color: var(--accent);
        background: #f0f4f8;
    }
    
    .file-upload i {
        font-size: 2rem;
        color: var(--accent);
        margin-bottom: 0.5rem;
    }
    
    .file-upload input[type="file"] {
        display: none;
    }
    
    .current-image {
        max-width: 100%;
        max-height: 300px;
        margin-top: 1rem;
        border-radius: var(--border-radius);
    }
    
    .preview-image {
        max-width: 100%;
        max-height: 300px;
        margin-top: 1rem;
        border-radius: var(--border-radius);
        display: none;
    }
    
    .file-info {
        font-size: 0.9rem;
        color: #666;
        margin-top: 0.5rem;
    }
    
    .image-section {
        margin-bottom: 1.5rem;
    }
</style>

<div class="form-container">
    <h2 class="form-title">Edit Carousel Item</h2>
    
    <?php if($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data">
        <div class="image-section">
            <label class="form-label">Current Image</label>
            <?php if(!empty($row['image'])): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($row['image']); ?>" 
                     class="current-image" alt="Current Carousel Image">
            <?php else: ?>
                <p>No image currently set</p>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label class="form-label">Update Image (Optional)</label>
            <label class="file-upload" for="image-upload">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Click to upload new image</span>
                <span class="file-info">(JPG, PNG, or GIF - will replace current image)</span>
                <input type="file" id="image-upload" name="image" accept="image/*">
                <img id="image-preview" class="preview-image" src="#" alt="Image Preview">
            </label>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="caption">Caption</label>
            <input type="text" class="form-control" id="caption" name="caption" 
                   value="<?php echo htmlspecialchars($row['caption']); ?>" 
                   placeholder="Enter carousel caption (optional)">
        </div>
        
        <button type="submit" class="btn btn-block">Update Carousel Item</button>
    </form>
</div>

<script>
    // Image preview functionality
    document.getElementById('image-upload').addEventListener('change', function(e) {
        const preview = document.getElementById('image-preview');
        const file = e.target.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        if(file) {
            reader.readAsDataURL(file);
        }
    });
</script>