<?php
// Include database connection
require_once 'db.php';

// Function to get company information
function getCompanyInfo() {
    global $conn;
    $sql = "SELECT * FROM company_info LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Function to get carousel items
function getCarouselItems() {
    global $conn;
    $sql = "SELECT id, caption FROM carousel";
    $result = $conn->query($sql);
    
    $items = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
    return $items;
}

// Function to get product image based on ID
function getProductImage($productId) {
    global $conn;
    $imageQuery = "SELECT image FROM products WHERE id = ?";
    $stmt = $conn->prepare($imageQuery);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $imageData = $result->fetch_assoc();
    
    if ($imageData && $imageData['image']) {
        return 'data:image/jpeg;base64,' . base64_encode($imageData['image']);
    }
    return null;
}

// Function to get carousel image based on ID
function getCarouselImage($carouselId) {
    global $conn;
    $imageQuery = "SELECT image FROM carousel WHERE id = ?";
    $stmt = $conn->prepare($imageQuery);
    $stmt->bind_param("i", $carouselId);
    $stmt->execute();
    $result = $stmt->get_result();
    $imageData = $result->fetch_assoc();
    
    if ($imageData && $imageData['image']) {
        return 'data:image/jpeg;base64,' . base64_encode($imageData['image']);
    }
    return null;
}

// Function to get categories
function getCategories() {
    global $conn;
    $sql = "SELECT * FROM category";
    $result = $conn->query($sql);
    
    $categories = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    return $categories;
}

// Function to get products
function getProducts($categoryId = null) {
    global $conn;
    
    if ($categoryId) {
        $sql = "SELECT * FROM products WHERE category_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT p.*, c.name as category_name FROM products p 
                LEFT JOIN category c ON p.category_id = c.id";
        $result = $conn->query($sql);
    }
    
    $products = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    return $products;
}

// Function to get services
function getServices() {
    global $conn;
    $sql = "SELECT * FROM service";
    $result = $conn->query($sql);
    
    $services = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
    }
    return $services;
}

// Function to get contact information
function getContactInfo() {
    global $conn;
    $sql = "SELECT * FROM contact_info LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Get data for the page
$companyInfo = getCompanyInfo();
$carouselItems = getCarouselItems();
$categories = getCategories();
$products = getProducts();
$services = getServices();
$contactInfo = getContactInfo();

// Handle category filter if set
$selectedCategory = null;
if (isset($_GET['category']) && is_numeric($_GET['category'])) {
    $selectedCategory = (int)$_GET['category'];
    $products = getProducts($selectedCategory);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $companyInfo ? htmlspecialchars($companyInfo['location']) : 'Company Website'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Custom CSS */
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }
        
        .navbar {
            background-color: var(--secondary-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: #fff;
        }
        
        .navbar-dark .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            transition: color 0.3s;
        }
        
        .navbar-dark .navbar-nav .nav-link:hover {
            color: #fff;
        }
        
        .carousel-caption {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 5px;
        }
        
        .section-title {
            position: relative;
            margin-bottom: 30px;
            padding-bottom: 15px;
            text-align: center;
            font-weight: 700;
            color: var(--secondary-color);
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            display: block;
            width: 50px;
            height: 3px;
            background-color: var(--primary-color);
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }
        
        .card-img-top {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            height: 200px;
            object-fit: cover;
        }
        
        .product-img {
            height: 200px;
            object-fit: contain;
            background-color: #f8f9fa;
        }
        
        .service-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        .category-badge {
            background-color: var(--primary-color);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-bottom: 10px;
            display: inline-block;
        }
        
        .footer {
            background-color: var(--secondary-color);
            color: #fff;
            padding: 40px 0;
            margin-top: 50px;
        }
        
        .footer h5 {
            color: #fff;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .footer ul {
            list-style: none;
            padding-left: 0;
        }
        
        .footer ul li {
            margin-bottom: 10px;
        }
        
        .footer ul li a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer ul li a:hover {
            color: #fff;
        }
        
        .social-icons a {
            display: inline-block;
            width: 36px;
            height: 36px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 36px;
            margin-right: 10px;
            color: #fff;
            transition: background-color 0.3s;
        }
        
        .social-icons a:hover {
            background-color: var(--primary-color);
        }
        
        .contact-info {
            margin-bottom: 20px;
        }
        
        .contact-info i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        #contactForm .form-control {
            border-radius: 0;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        #contactForm .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-color);
        }
        
        .filter-btn {
            margin-right: 5px;
            margin-bottom: 10px;
        }
        
        .active-filter {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <?php echo $companyInfo ? htmlspecialchars($companyInfo['location']) : 'Company Name'; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#products">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Carousel Section -->
    <section id="home" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <?php foreach ($carouselItems as $index => $item): ?>
                <button type="button" data-bs-target="#home" data-bs-slide-to="<?php echo $index; ?>" <?php echo $index === 0 ? 'class="active"' : ''; ?> aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-label="Slide <?php echo $index + 1; ?>"></button>
            <?php endforeach; ?>
            <?php if (empty($carouselItems)): ?>
                <button type="button" data-bs-target="#home" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <?php endif; ?>
        </div>
        <div class="carousel-inner">
                            <?php if (!empty($carouselItems)): ?>
                <?php foreach ($carouselItems as $index => $item): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <?php 
                        $imageBase64 = getCarouselImage($item['id']);
                        if ($imageBase64): 
                        ?>
                            <img src="<?php echo $imageBase64; ?>" class="d-block w-100" alt="Carousel Image" style="height: 500px; object-fit: cover;">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/1200x500" class="d-block w-100" alt="Placeholder" style="height: 500px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="carousel-caption d-none d-md-block">
                            <h5><?php echo htmlspecialchars($item['caption']); ?></h5>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="carousel-item active">
                    <img src="https://via.placeholder.com/1200x500" class="d-block w-100" alt="Placeholder" style="height: 500px; object-fit: cover;">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Welcome to Our Company</h5>
                        <p>Add carousel items in the admin panel</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#home" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#home" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5">
        <div class="container">
            <h2 class="section-title">About Us</h2>
            <div class="row">
                <div class="col-lg-6">
                    <h3>Our Company</h3>
                    <p>
                        <?php if ($companyInfo && !empty($companyInfo['description'])): ?>
                            <?php echo nl2br(htmlspecialchars($companyInfo['description'])); ?>
                        <?php else: ?>
                            We are a company dedicated to providing high-quality products and exceptional services to our customers. 
                            With years of experience in the industry, we've built a reputation for reliability, innovation, and customer satisfaction.
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-lg-6">
                    <img src="https://via.placeholder.com/600x400" alt="About Us" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title">Our Products</h2>
            
            <!-- Category Filter -->
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <a href="index.php#products" class="btn btn-primary filter-btn <?php echo $selectedCategory === null ? 'active-filter' : ''; ?>">All</a>
                    <?php foreach ($categories as $category): ?>
                        <a href="index.php?category=<?php echo $category['id']; ?>#products" class="btn btn-primary filter-btn <?php echo $selectedCategory === (int)$category['id'] ? 'active-filter' : ''; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="row">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4">
                            <div class="card">
                                <?php if ($product['image']): ?>
                                    <?php 
                                    $imageBase64 = getProductImage($product['id']);
                                    if ($imageBase64): 
                                    ?>
                                        <img src="<?php echo $imageBase64; ?>" class="card-img-top product-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/300x200?text=Error+Loading+Image" class="card-img-top product-img" alt="Error Loading Image">
                                    <?php endif; ?>
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/300x200?text=No+Image" class="card-img-top product-img" alt="No Image">
                                <?php endif; ?>
                                <div class="card-body">
                                    <?php if (isset($product['category_name']) && !empty($product['category_name'])): ?>
                                        <span class="category-badge"><?php echo htmlspecialchars($product['category_name']); ?></span>
                                    <?php endif; ?>
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text">
                                        <?php 
                                        if (!empty($product['description'])) {
                                            echo (strlen($product['description']) > 100) 
                                                ? htmlspecialchars(substr($product['description'], 0, 100)) . '...' 
                                                : htmlspecialchars($product['description']);
                                        } else {
                                            echo 'No description available.';
                                        }
                                        ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-primary">
                                            $<?php echo number_format($product['price'], 2); ?>
                                        </strong>
                                        <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p>No products available in this category.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5">
        <div class="container">
            <h2 class="section-title">Our Services</h2>
            <div class="row">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $service): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 text-center p-4">
                                <div class="service-icon">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($service['title']); ?></h5>
                                    <p class="card-text">
                                        <?php echo !empty($service['inscription']) ? htmlspecialchars($service['inscription']) : 'No description available.'; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p>No services available yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title">Contact Us</h2>
            <div class="row">
                <div class="col-lg-6">
                    <div class="contact-info">
                        <?php if ($contactInfo): ?>
                            <?php if (!empty($contactInfo['address'])): ?>
                                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($contactInfo['address']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($contactInfo['phone'])): ?>
                                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($contactInfo['phone']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($contactInfo['email'])): ?>
                                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($contactInfo['email']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($contactInfo['description'])): ?>
                                <div class="mt-4">
                                    <h5>Additional Information</h5>
                                    <p><?php echo nl2br(htmlspecialchars($contactInfo['description'])); ?></p>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <p><i class="fas fa-map-marker-alt"></i> 123 Company Street, Business City</p>
                            <p><i class="fas fa-phone"></i> +1 (123) 456-7890</p>
                            <p><i class="fas fa-envelope"></i> info@companywebsite.com</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="social-icons mt-4">
                        <h5>Follow Us</h5>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-6 mt-4 mt-lg-0">
                    <form id="contactForm" action="process_contact.php" method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="Your Name" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control" placeholder="Your Email" required>
                            </div>
                        </div>
                        <input type="text" class="form-control" placeholder="Subject" required>
                        <textarea class="form-control" rows="5" placeholder="Your Message" required></textarea>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <h5>About Us</h5>
                    <p>
                        <?php if ($companyInfo && !empty($companyInfo['description'])): ?>
                            <?php 
                            echo (strlen($companyInfo['description']) > 150) 
                                ? htmlspecialchars(substr($companyInfo['description'], 0, 150)) . '...' 
                                : htmlspecialchars($companyInfo['description']);
                            ?>
                        <?php else: ?>
                            We are dedicated to providing high-quality products and exceptional services to our customers.
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5>Quick Links</h5>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#products">Products</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5>Contact Info</h5>
                    <ul class="contact-info">
                        <?php if ($contactInfo): ?>
                            <?php if (!empty($contactInfo['address'])): ?>
                                <li><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($contactInfo['address']); ?></li>
                            <?php endif; ?>
                            <?php if (!empty($contactInfo['phone'])): ?>
                                <li><i class="fas fa-phone"></i> <?php echo htmlspecialchars($contactInfo['phone']); ?></li>
                            <?php endif; ?>
                            <?php if (!empty($contactInfo['email'])): ?>
                                <li><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($contactInfo['email']); ?></li>
                            <?php endif; ?>
                        <?php else: ?>
                            <li><i class="fas fa-map-marker-alt"></i> 123 Company Street, Business City</li>
                            <li><i class="fas fa-phone"></i> +1 (123) 456-7890</li>
                            <li><i class="fas fa-envelope"></i> info@companywebsite.com</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <hr class="mt-4 mb-4" style="border-color: rgba(255, 255, 255, 0.1);">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo $companyInfo ? htmlspecialchars($companyInfo['location']) : 'Company Name'; ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Designed with <i class="fas fa-heart text-danger"></i> by Your Company</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 60,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Enable Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html>