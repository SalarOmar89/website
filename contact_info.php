<?php
include "db.php";
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include "header.php";

// Ensure a row with id = 1 exists
$check = mysqli_query($conn, "SELECT * FROM contact_info WHERE id = 1");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "INSERT INTO contact_info (id, email, phone, address, description) VALUES (1, '', '', '', '')");
}

$success = '';
$error = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $sql = "UPDATE contact_info SET email='$email', phone='$phone', address='$address', description='$description' WHERE id = 1";
    if (mysqli_query($conn, $sql)) {
        $success = "✅ Contact information updated successfully!";
        // Refresh the data
        $result = mysqli_query($conn, "SELECT * FROM contact_info WHERE id = 1");
        $contact = mysqli_fetch_assoc($result);
    } else {
        $error = "❌ Error: " . mysqli_error($conn);
    }
}

// Fetch current data
$result = mysqli_query($conn, "SELECT * FROM contact_info WHERE id = 1");
$contact = mysqli_fetch_assoc($result);
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
        max-width: 800px;
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
    
    .info-card {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: var(--border-radius);
        margin-bottom: 1.5rem;
    }
    
    .info-card h3 {
        margin-top: 0;
        color: var(--primary);
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }
    
    .info-item {
        margin-bottom: 0.5rem;
    }
    
    .info-label {
        font-weight: 600;
        color: var(--dark);
    }
    
    @media (max-width: 768px) {
        .form-container {
            padding: 1rem;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="form-container">
    <h2 class="form-title">Update Contact Information</h2>
    
    <?php if($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="info-card">
        <h3>Current Contact Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Email:</span>
                <p><?php echo !empty($contact['email']) ? htmlspecialchars($contact['email']) : 'Not set'; ?></p>
            </div>
            <div class="info-item">
                <span class="info-label">Phone:</span>
                <p><?php echo !empty($contact['phone']) ? htmlspecialchars($contact['phone']) : 'Not set'; ?></p>
            </div>
            <div class="info-item">
                <span class="info-label">Address:</span>
                <p><?php echo !empty($contact['address']) ? htmlspecialchars($contact['address']) : 'Not set'; ?></p>
            </div>
        </div>
        <div class="info-item">
            <span class="info-label">Description:</span>
            <p><?php echo !empty($contact['description']) ? nl2br(htmlspecialchars($contact['description'])) : 'Not set'; ?></p>
        </div>
    </div>
    
    <form method="post">
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" 
                   value="<?php echo htmlspecialchars($contact['email']); ?>" 
                   placeholder="Enter contact email" required>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="phone">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" 
                   value="<?php echo htmlspecialchars($contact['phone']); ?>" 
                   placeholder="Enter contact phone number" required>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="address">Physical Address</label>
            <input type="text" class="form-control" id="address" name="address" 
                   value="<?php echo htmlspecialchars($contact['address']); ?>" 
                   placeholder="Enter physical address" required>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="description">Description</label>
            <textarea class="form-control" id="description" name="description" 
                      placeholder="Enter additional contact information"><?php 
                echo htmlspecialchars($contact['description']); 
            ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-block">Update Contact Information</button>
    </form>
</div>