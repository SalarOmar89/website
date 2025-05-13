<?php
include "db.php";
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include "header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    
    // Handle file upload
    $image = "";

if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $target_dir = "uploads/";
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate it's an actual image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = mysqli_real_escape_string($conn, $target_file);
        } else {
            $error = "❌ Failed to upload the image.";
        }
    } else {
        $error = "❌ Uploaded file is not a valid image.";
    }
} else {
    $error = "❌ No image uploaded or upload error.";
}

    $sql = "INSERT INTO products (name, price, description, category_id, image)
            VALUES ('$name', '$price', '$description', '$category_id', '$image')";
    if (mysqli_query($conn, $sql)) {
        $success = "✅ Product added successfully!";
    } else {
        $error = "❌ Error: " . mysqli_error($conn);
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
    
    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }
    
    .select-wrapper {
        position: relative;
    }
    
    .select-wrapper::after {
        content: "▼";
        font-size: 0.8rem;
        color: var(--dark);
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }
    
    select.form-control {
        appearance: none;
        padding-right: 2.5rem;
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
    
    .preview-image {
        max-width: 200px;
        max-height: 200px;
        margin-top: 1rem;
        border-radius: var(--border-radius);
        display: none;
    }
</style>

<div class="form-container">
    <h2 class="form-title">Add New Product</h2>
    
    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label class="form-label" for="name">Product Name</label>
            <input type="text" class="form-control" id="name" name="name" required placeholder="Enter product name">
        </div>
        
        <div class="form-group">
            <label class="form-label" for="price">Price</label>
            <input type="text" class="form-control" id="price" name="price" required placeholder="0.00">
        </div>
        
        <div class="form-group">
            <label class="form-label" for="category_id">Category</label>
            <div class="select-wrapper">
                <select class="form-control" id="category_id" name="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php
                    $cats = mysqli_query($conn, "SELECT * FROM category");
                    while ($cat = mysqli_fetch_assoc($cats)) {
                        echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="description">Description</label>
            <textarea class="form-control" id="description" name="description" required placeholder="Enter detailed product description"></textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">Product Image</label>
            <label class="file-upload">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Click to upload product image</span>
                <input type="file" id="image" name="image" accept="image/*">
                <img id="preview" class="preview-image" src="#" alt="Preview">
            </label>
        </div>
        
        <button type="submit" class="btn btn-block">Add Product</button>
    </form>
</div>

<script>
    // Image preview functionality
    document.getElementById('image').addEventListener('change', function(e) {
        const preview = document.getElementById('preview');
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