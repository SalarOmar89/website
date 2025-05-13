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

// Handle delete
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    $sql = "DELETE FROM service WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        $success = "✅ Service deleted successfully!";
    } else {
        $error = "❌ Error: " . mysqli_error($conn);
    }
}

// Handle add
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $inscription = mysqli_real_escape_string($conn, $_POST['inscription']);

    $sql = "INSERT INTO service (title, inscription) VALUES ('$title', '$inscription')";
    if (mysqli_query($conn, $sql)) {
        $success = "✅ Service added successfully!";
    } else {
        $error = "❌ Error: " . mysqli_error($conn);
    }
}

// Fetch services
$services = mysqli_query($conn, "SELECT * FROM service ORDER BY title ASC");
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
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
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
    
    .subtitle {
        color: var(--secondary);
        margin: 1.5rem 0 1rem;
        font-size: 1.25rem;
        font-weight: 500;
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
    
    .actions {
        white-space: nowrap;
    }
    
    .action-link {
        color: var(--primary);
        text-decoration: none;
        margin-right: 0.75rem;
        transition: var(--transition);
    }
    
    .action-link:hover {
        text-decoration: underline;
    }
    
    .action-link.delete {
        color: var(--danger);
    }
    
    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #666;
    }
    
    @media (max-width: 768px) {
        .container {
            padding: 0 0.5rem;
        }
        
        .card {
            padding: 1rem;
        }
        
        .table {
            display: block;
            overflow-x: auto;
        }
    }
</style>

<div class="container">
    <h2 class="section-title">Manage Services</h2>
    
    <?php if($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="card">
        <h3 class="subtitle">Add New Service</h3>
        <form method="post">
            <div class="form-group">
                <label class="form-label" for="title">Service Title</label>
                <input type="text" class="form-control" id="title" name="title" 
                       placeholder="Enter service title" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="inscription">Service Description</label>
                <textarea class="form-control" id="inscription" name="inscription" 
                          placeholder="Enter service description"></textarea>
            </div>
            
            <button type="submit" name="add" class="btn btn-block">Add Service</button>
        </form>
    </div>
    
    <div class="card">
        <h3 class="subtitle">Existing Services</h3>
        <?php if(mysqli_num_rows($services) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th class="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($services)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['inscription']); ?></td>
                            <td class="actions">
                                <a href="edit_service.php?id=<?php echo $row['id']; ?>" class="action-link">Edit</a>
                                <a href="services.php?delete=<?php echo $row['id']; ?>" 
                                   class="action-link delete"
                                   onclick="return confirm('Are you sure you want to delete this service?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>No services found. Add your first service above.</p>
            </div>
        <?php endif; ?>
    </div>
</div>