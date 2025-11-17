<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'client') {
    header("Location: login.php");
    exit();
}

// Handle cart operations
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['add_to_cart'])) {
        $product_id = $_POST['product_id'];
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            
            if($product) {
                if(!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }
                
                if(isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                } else {
                    $_SESSION['cart'][$product_id] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'quantity' => $quantity,
                        'image_path' => $product['image_path'] ?? ''
                    ];
                }
                
                $_SESSION['success'] = "Produit ajouté au panier avec succès !";
            }
        } catch(PDOException $e) {
            $_SESSION['error'] = "Erreur lors de l'ajout au panier : " . $e->getMessage();
        }
    }
    
    if(isset($_POST['update_cart'])) {
        $product_id = $_POST['product_id'];
        $quantity = intval($_POST['quantity']);
        
        if($quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
        } else {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        }
        
        $_SESSION['success'] = "Panier mis à jour avec succès !";
    }
    
    if(isset($_POST['remove_from_cart'])) {
        $product_id = $_POST['product_id'];
        unset($_SESSION['cart'][$product_id]);
        $_SESSION['success'] = "Produit retiré du panier avec succès !";
    }
}

// Calculate totals
$subtotal = 0;
$total_items = 0;
if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $item) {
        $subtotal += $item['price'] * $item['quantity'];
        $total_items += $item['quantity'];
    }
}
$tax = $subtotal * 0.20; // 20% VAT
$total = $subtotal + $tax;
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier - Kaoutar Boutique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style-enhanced-original.css">
    <link href="https://unpkg.com/splitting@1.0.6/dist/splitting.css" rel="stylesheet">
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
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-shop"></i> Boutique
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_orders.php">
                            <i class="bi bi-box"></i> Mes commandes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Déconnexion
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-5 mt-5">
        <div class="container">
            <!-- Header -->
            <div class="row mb-5 reveal">
                <div class="col-12">
                    <h1 class="mb-3" data-splitting>Mon Panier</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-glass border-glass p-3 rounded">
                            <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none">Accueil</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Panier</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show reveal">
                    <i class="bi bi-check-circle"></i> <?php echo $_SESSION['success']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show reveal">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Cart Content -->
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8 mb-4">
                    <div class="card bg-glass border-glass">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-cart4"></i> Produits dans votre panier (<?php echo $total_items; ?>)
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                                <?php foreach($_SESSION['cart'] as $product_id => $item): ?>
                            <div class="cart-item border-bottom p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <?php if(!empty($item['image_path']) && file_exists($item['image_path'])): ?>
                                            <img src="<?php echo $item['image_path']; ?>" class="img-fluid rounded" alt="<?php echo $item['name']; ?>" style="height: 80px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="no-image" style="height: 80px; font-size: 1.5rem;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="mb-1"><?php echo $item['name']; ?></h6>
                                        <p class="text-muted small mb-1">Réf. #<?php echo $product_id; ?></p>
                                        <p class="text-success small mb-0">
                                            <i class="bi bi-check-circle"></i> En stock
                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                            <div class="input-group input-group-sm">
                                                <button type="button" class="btn btn-outline-secondary" onclick="updateQuantity(<?php echo $product_id; ?>, -1)">
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                                <input type="number" class="form-control text-center" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="99" onchange="this.form.submit()">
                                                <button type="button" class="btn btn-outline-secondary" onclick="updateQuantity(<?php echo $product_id; ?>, 1)">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                            <input type="hidden" name="update_cart" value="1">
                                        </form>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <p class="fw-bold mb-1"><?php echo number_format($item['price'] * $item['quantity'], 2); ?> €</p>
                                        <p class="text-muted small mb-0"><?php echo $item['price']; ?> €/unité</p>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                            <input type="hidden" name="remove_from_cart" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                                                <i class="bi bi-trash"></i> Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <div class="text-center p-5">
                                <i class="bi bi-cart-x" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3">Votre panier est vide</h4>
                                <p class="text-muted">Ajoutez des produits pour commencer vos achats</p>
                                <a href="dashboard.php" class="btn btn-primary">
                                    <i class="bi bi-shop"></i> Continuer mes achats
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="card bg-glass border-glass">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-receipt"></i> Résumé de la commande
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Sous-total (<?php echo $total_items; ?> articles)</span>
                                <span class="fw-bold"><?php echo number_format($subtotal, 2); ?> €</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Livraison</span>
                                <span class="text-success">Gratuite</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>TVA (20%)</span>
                                <span><?php echo number_format($tax, 2); ?> €</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="h5 mb-0">Total</span>
                                <span class="h5 mb-0 text-primary"><?php echo number_format($total, 2); ?> €</span>
                            </div>
                            
                            <!-- Promo Code -->
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Code promo" id="promoCode">
                                    <button class="btn btn-outline-primary" type="button" onclick="applyPromoCode()">Appliquer</button>
                                </div>
                            </div>
                            
                            <!-- Checkout Button -->
                            <div class="d-grid gap-2">
                                <?php if($total_items > 0): ?>
                                <a href="checkout.php" class="btn btn-primary btn-lg">
                                    <i class="bi bi-credit-card"></i> Passer la commande
                                </a>
                                <?php else: ?>
                                <button class="btn btn-primary btn-lg" disabled>
                                    <i class="bi bi-credit-card"></i> Passer la commande
                                </button>
                                <?php endif; ?>
                                <a href="dashboard.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Continuer mes achats
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Trust Badges -->
                    <div class="card bg-glass border-glass mt-4">
                        <div class="card-body text-center">
                            <h6 class="mb-3">Achat sécurisé</h6>
                            <div class="row">
                                <div class="col-4">
                                    <i class="bi bi-shield-check text-success" style="font-size: 2rem;"></i>
                                    <p class="small mt-1">Paiement sécurisé</p>
                                </div>
                                <div class="col-4">
                                    <i class="bi bi-truck text-primary" style="font-size: 2rem;"></i>
                                    <p class="small mt-1">Livraison gratuite</p>
                                </div>
                                <div class="col-4">
                                    <i class="bi bi-arrow-return-left text-warning" style="font-size: 2rem;"></i>
                                    <p class="small mt-1">Retour 30 jours</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
        
        // Update quantity function
        function updateQuantity(productId, change) {
            const input = document.querySelector(`input[name="quantity"][value="${productId}"]`) || 
                         document.querySelector(`input[value="${productId}"]`).parentNode.querySelector('input[name="quantity"]');
            const currentValue = parseInt(input.value);
            const newValue = currentValue + change;
            
            if (newValue >= 1) {
                input.value = newValue;
                input.form.submit();
            }
        }
        
        // Apply promo code
        function applyPromoCode() {
            const promoCode = document.getElementById('promoCode').value;
            const button = event.target;
            const originalText = button.innerHTML;
            
            if (!promoCode) {
                showNotification('Veuillez entrer un code promo', 'warning');
                return;
            }
            
            // Show loading state
            button.classList.add('btn-loading');
            button.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
            button.disabled = true;
            
            // Simulate promo code validation
            setTimeout(() => {
                button.classList.remove('btn-loading');
                button.disabled = false;
                button.innerHTML = originalText;
                
                if (promoCode.toUpperCase() === 'WELCOME10') {
                    showNotification('Code promo appliqué avec succès ! -10%', 'success');
                    // Here you would update the total price
                } else {
                    showNotification('Code promo invalide ou expiré', 'danger');
                }
            }, 1500);
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
            
            // Animate cart items
            anime({
                targets: '.cart-item',
                translateX: [-50, 0],
                opacity: [0, 1],
                easing: 'easeOutExpo',
                duration: 800,
                delay: (el, i) => 200 * i
            });
            
            // Animate cards
            anime({
                targets: '.card',
                translateY: [30, 0],
                opacity: [0, 1],
                easing: 'easeOutExpo',
                duration: 1000,
                delay: (el, i) => 100 * i
            });
        });
    </script>
</body>
</html>