<?php
include "db.php";
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include "header.php";

$success = '';
$error = '';

// Add new category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    $sql = "INSERT INTO category (name) VALUES ('$category_name')";
    if (mysqli_query($conn, $sql)) {
        $success = "✅ Category added successfully!";
    } else {
        $error = "❌ Error: " . mysqli_error($conn);
    }
}

// Delete category
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    $sql = "DELETE FROM category WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        $success = "✅ Category deleted successfully!";
    } else {
        $error = "❌ Error: " . mysqli_error($conn);
    }
}

$categories = mysqli_query($conn, "SELECT * FROM category ORDER BY name ASC");
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
    
    .container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 2rem;
    }
    
    .card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .section-title {
        color: var(--primary);
        margin-bottom: 1.5rem;
        font-size: 1.75rem;
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
    
    .btn-danger {
        background: var(--danger);
    }
    
    .btn-danger:hover {
        background: #d91a60;
    }
    
    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
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
    
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }
    
    .table th, .table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    
    .table th {
        background-color: var(--light);
        font-weight: 600;
        color: var(--dark);
    }
    
    .table tr:hover {
        background-color: rgba(67, 97, 238, 0.05);
    }
    
    .action-links a {
        color: var(--danger);
        text-decoration: none;
        transition: var(--transition);
    }
    
    .action-links a:hover {
        text-decoration: underline;
    }
    
    .confirmation-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    
    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: var(--border-radius);
        max-width: 500px;
        width: 90%;
    }
    
    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 1.5rem;
    }
</style>

<div class="container">
    <?php if($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="card">
        <h2 class="section-title">Add New Category</h2>
        <form method="post">
            <div class="form-group">
                <label class="form-label" for="category_name">Category Name</label>
                <input type="text" class="form-control" id="category_name" name="category_name" 
                       placeholder="Enter category name" required>
            </div>
            <button type="submit" name="add_category" class="btn">Add Category</button>
        </form>
    </div>
    
    <div class="card">
        <h2 class="section-title">Existing Categories</h2>
        <?php if(mysqli_num_rows($categories) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['id']); ?></td>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td class="action-links">
                                <a href="#" onclick="confirmDelete(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars(addslashes($category['name'])); ?>')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No categories found. Add your first category above.</p>
        <?php endif; ?>
    </div>
</div>

<div id="confirmationModal" class="confirmation-modal">
    <div class="modal-content">
        <h3>Confirm Deletion</h3>
        <p>Are you sure you want to delete the category "<span id="categoryName"></span>"?</p>
        <div class="modal-actions">
            <button onclick="cancelDelete()" class="btn btn-sm">Cancel</button>
            <button onclick="proceedDelete()" class="btn btn-sm btn-danger">Delete</button>
        </div>
    </div>
</div>

<script>
    let categoryToDelete = null;
    
    function confirmDelete(id, name) {
        categoryToDelete = id;
        document.getElementById('categoryName').textContent = name;
        document.getElementById('confirmationModal').style.display = 'flex';
    }
    
    function cancelDelete() {
        categoryToDelete = null;
        document.getElementById('confirmationModal').style.display = 'none';
    }
    
    function proceedDelete() {
        if (categoryToDelete) {
            window.location.href = 'category.php?delete=' + categoryToDelete;
        }
    }
</script>