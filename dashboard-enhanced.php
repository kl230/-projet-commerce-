<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$clients = [];
if($_SESSION['role'] == 'admin') {
    try {
        $stmt = $pdo->prepare("SELECT id, username, email, created_at FROM users WHERE role = 'client' ORDER BY created_at DESC");
        $stmt->execute();
        $clients = $stmt->fetchAll();
    } catch(PDOException $e) {
        $clients_error = "Erreur lors de la récupération des clients : " . $e->getMessage();
    }
}

$stats = [];
if($_SESSION['role'] == 'admin') {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total_products FROM products");
        $stmt->execute();
        $stats['total_products'] = $stmt->fetch()['total_products'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as products_in_stock FROM products WHERE quantity > 0");
        $stmt->execute();
        $stats['products_in_stock'] = $stmt->fetch()['products_in_stock'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as total_clients FROM users WHERE role = 'client'");
        $stmt->execute();
        $stats['total_clients'] = $stmt->fetch()['total_clients'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as total_orders FROM orders");
        $stmt->execute();
        $stats['total_orders'] = $stmt->fetch()['total_orders'];
        
    } catch(PDOException $e) {
        $stats_error = "Erreur lors de la récupération des statistiques : " . $e->getMessage();
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE quantity > 0");
    $stmt->execute();
    $products = $stmt->fetchAll();
} catch(PDOException $e) {
    $products_error = "Une erreur s'est produite lors de la récupération des produits : " . $e->getMessage();
}

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Kaoutar Boutique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style-enhanced-original.css">
    <link href="https://unpkg.com/splitting@1.0.6/dist/splitting.css" rel="stylesheet">
    <style>
        .product-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .no-image {
            height: 200px;
            background-color: #f8f9faab;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
        .card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .badge-cart {
            position: relative;
            top: -10px;
            left: -5px;
        }
        .quick-add-btn {
            margin-top: 10px;
        }
        .nav-tabs .nav-link {
            color: #495057;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            font-weight: 600;
        }
        .stats-card {
            border-left: 4px solid #9c9fa4ff;
            background-color: #e5d1c3;
        }
        .stats-card .card-body .bi {
            color: #6c757d;
        }
        .client-table th {
            background-color: #f8f9fa7f;
        }
        .order-status-delivered { background-color: #d4edda; color: #155724; }
        .order-status-pending { background-color: #fff3cd; color: #856404; }
        .order-status-cancelled { background-color: #f8d7da; color: #721c24; }
        .modal-xl-custom { max-width: 95%; }
        .btn-loading {
            position: relative;
            pointer-events: none;
        }
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Background Effects -->
    <div class="hero-background"></div>
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top glass">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <img src="resources/logo.jpg" alt="Kaoutar Logo">
                <span>Kaoutar</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">
                            <i class="bi bi-person-circle"></i> Bonjour, <?php echo $_SESSION['username']; ?>
                        </span>
                    </li>
                    
                    <?php if($_SESSION['role'] == 'client'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="bi bi-cart"></i> Panier
                            <?php if(!empty($_SESSION['cart'])): ?>
                                <span class="badge bg-danger badge-cart"><?php echo count($_SESSION['cart']); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if($_SESSION['role'] == 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Gestion des produits</a>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-5 mt-5">
        <div class="container">
            <!-- Welcome Section -->
            <div class="row mb-5 reveal">
                <div class="col-12 text-center">
                    <h1 class="mb-3" data-splitting>Bienvenue dans votre boutique</h1>
                    <p class="lead">Découvrez nos produits exclusifs et profitez d'une expérience d'achat unique</p>
                </div>
            </div>

            <?php if($_SESSION['role'] == 'admin'): ?>
            <!-- Stats Section (Admin View) -->
            <div class="row mb-5 reveal">
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-0"><?php echo $stats['total_products'] ?? 0; ?></h5>
                                    <p class="card-text">Produits disponibles</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-box-seam" style="font-size: 2rem; color: var(--primary-color);"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-0"><?php echo $stats['products_in_stock'] ?? 0; ?></h5>
                                    <p class="card-text">En stock</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-check-circle" style="font-size: 2rem; color: var(--success-color);"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-0"><?php echo $stats['total_clients'] ?? 0; ?></h5>
                                    <p class="card-text">Clients satisfaits</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-people" style="font-size: 2rem; color: var(--accent-color);"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-0"><?php echo $stats['total_orders'] ?? 0; ?></h5>
                                    <p class="card-text">Commandes traitées</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-cart-check" style="font-size: 2rem; color: var(--warning-color);"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Search and Filter Section -->
            <div class="row mb-4 reveal">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-glass border-glass">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control bg-glass border-glass" placeholder="Rechercher des produits..." id="searchInput">
                        <button class="btn btn-primary" type="button" onclick="searchProducts()">
                            <i class="bi bi-search"></i> Rechercher
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select bg-glass border-glass" id="categoryFilter" onchange="filterByCategory()">
                        <option value="">Toutes les catégories</option>
                        <option value="Sacs">Sacs à main</option>
                        <option value="Montres">Montres</option>
                        <option value="Bijoux">Bijoux</option>
                        <option value="Accessoires">Accessoires</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row product-grid">
                <?php if($products && count($products) > 0): ?>
                    <?php foreach($products as $product): ?>
                <div class="col-md-4 mb-4 reveal">
                    <div class="card h-100 shadow-custom">
                        <a href="product_details.php?id=<?php echo $product['id']; ?>" style="text-decoration: none; color: inherit;">
                            <?php if(!empty($product['image_path']) && file_exists($product['image_path'])): ?>
                                <img src="<?php echo $product['image_path']; ?>" class="product-image" alt="<?php echo $product['name']; ?>">
                            <?php else: ?>
                                <div class="no-image">
                                    <i class="bi bi-image" style="font-size: 3rem;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                <p class="card-text text-primary fw-bold">Prix : <?php echo $product['price']; ?> €</p>
                                <p class="card-text">
                                    <small class="text-<?php echo $product['quantity'] > 5 ? 'success' : ($product['quantity'] > 0 ? 'warning' : 'danger'); ?>">
                                        Quantité disponible : <?php echo $product['quantity']; ?>
                                    </small>
                                </p>
                                <p class="card-text">
                                    <span class="badge bg-secondary"><?php echo $product['category']; ?></span>
                                    <span class="badge bg-info"><?php echo $product['brand']; ?></span>
                                </p>
                                
                                <?php if(!empty($product['description'])): ?>
                                    <p class="card-text text-muted small">
                                        <?php echo strlen($product['description']) > 100 ? substr($product['description'], 0, 100) . '...' : $product['description']; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </a>
                        
                        <?php if($_SESSION['role'] == 'client'): ?>
                        <div class="card-footer bg-transparent">
                            <div class="d-grid gap-2">
                                <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> Voir les détails
                                </a>
                                
                                <?php if($product['quantity'] > 0): ?>
                                <form action="cart.php" method="POST" class="d-grid">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" name="add_to_cart" class="btn btn-success btn-sm quick-add-btn" onclick="addToCartAnimation(this)">
                                        <i class="bi bi-cart-plus"></i> Ajouter au panier
                                    </button>
                                </form>
                                <?php else: ?>
                                <button class="btn btn-danger btn-sm" disabled>
                                    <i class="bi bi-x-circle"></i> Rupture de stock
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                        <h4>Aucun produit disponible pour le moment</h4>
                        <p class="mb-0">Revenez plus tard pour découvrir nos nouveaux produits.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-4 bg-glass border-glass mt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">&copy; 2024 Kaoutar Boutique. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="social-links">
                        <a href="#" class="text-muted me-3"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-muted me-3"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-muted me-3"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-muted"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/splitting@1.0.6/dist/splitting.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    
    <script>
        // Initialize Splitting.js for text animations
        Splitting();
        
        // Scroll reveal animation
        function revealOnScroll() {
            const reveals = document.querySelectorAll('.reveal');
            
            reveals.forEach(element => {
                const windowHeight = window.innerHeight;
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < windowHeight - elementVisible) {
                    element.classList.add('active');
                }
            });
        }
        
        window.addEventListener('scroll', revealOnScroll);
        
        // Add to cart animation
        function addToCartAnimation(button) {
            const originalText = button.innerHTML;
            
            // Show loading state
            button.classList.add('btn-loading');
            button.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Ajout...';
            button.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                // Show success
                button.classList.remove('btn-loading');
                button.classList.remove('btn-success');
                button.classList.add('btn-success');
                button.innerHTML = '<i class="bi bi-check"></i> Ajouté !';
                
                // Update cart badge
                const badge = document.querySelector('.badge-cart');
                if (badge) {
                    const currentCount = parseInt(badge.textContent) || 0;
                    badge.textContent = currentCount + 1;
                }
                
                // Show notification
                showNotification('Produit ajouté au panier avec succès !', 'success');
                
                // Reset button after delay
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 2000);
                
            }, 1000);
        }
        
        // Search products function
        function searchProducts() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const products = document.querySelectorAll('.product-grid .col-md-4');
            
            products.forEach(product => {
                const productName = product.querySelector('.card-title').textContent.toLowerCase();
                const productDescription = product.querySelector('.card-text').textContent.toLowerCase();
                
                if (productName.includes(searchTerm) || productDescription.includes(searchTerm)) {
                    product.style.display = 'block';
                    anime({
                        targets: product,
                        opacity: [0, 1],
                        translateY: [30, 0],
                        duration: 500,
                        easing: 'easeOutExpo'
                    });
                } else {
                    anime({
                        targets: product,
                        opacity: [1, 0],
                        translateY: [0, -30],
                        duration: 300,
                        easing: 'easeInExpo',
                        complete: function() {
                            product.style.display = 'none';
                        }
                    });
                }
            });
        }
        
        // Filter by category
        function filterByCategory() {
            const selectedCategory = document.getElementById('categoryFilter').value;
            const products = document.querySelectorAll('.product-grid .col-md-4');
            
            products.forEach(product => {
                const productCategory = product.querySelector('.badge.bg-secondary').textContent;
                
                if (!selectedCategory || productCategory === selectedCategory) {
                    product.style.display = 'block';
                    anime({
                        targets: product,
                        opacity: [0, 1],
                        scale: [0.8, 1],
                        duration: 500,
                        easing: 'easeOutExpo'
                    });
                } else {
                    anime({
                        targets: product,
                        opacity: [1, 0],
                        scale: [1, 0.8],
                        duration: 300,
                        easing: 'easeInExpo',
                        complete: function() {
                            product.style.display = 'none';
                        }
                    });
                }
            });
        }
        
        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 100px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }
        
        // Initialize animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate main title
            anime({
                targets: 'h1 .char',
                translateY: [100, 0],
                opacity: [0, 1],
                easing: 'easeOutExpo',
                duration: 1400,
                delay: (el, i) => 30 * i
            });
            
            // Animate stats cards
            anime({
                targets: '.stats-card',
                translateY: [50, 0],
                opacity: [0, 1],
                easing: 'easeOutExpo',
                duration: 1000,
                delay: (el, i) => 200 * i
            });
            
            // Animate product cards
            anime({
                targets: '.product-card',
                translateY: [30, 0],
                opacity: [0, 1],
                easing: 'easeOutExpo',
                duration: 800,
                delay: (el, i) => 100 * i
            });
        });
    </script>
</body>
</html>