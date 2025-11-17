<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaoutar - Boutique de Luxe en Ligne</title>
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
            <a class="navbar-brand" href="index.php">
                <img src="resources/logo.jpg" alt="Kaoutar Logo">
                <span class="animate-glow">Kaoutar</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="bi bi-shop"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="bi bi-box-arrow-right"></i> Déconnexion
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="bi bi-person"></i> Connexion
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="bi bi-person-plus"></i> Inscription
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-md-10 mx-auto text-center hero-content">
                    <h1 class="mb-4 animate-float" data-splitting>Bienvenue chez Kaoutar</h1>
                    <p class="lead mb-5 animate-pulse-slow">
                        Découvrez notre collection exclusive de produits de luxe soigneusement sélectionnés pour vous offrir une expérience d'achat exceptionnelle.
                    </p>
                    
                    <div class="d-flex justify-content-center gap-4 flex-wrap mt-5">
                        <?php if(!isset($_SESSION['user_id'])): ?>
                            <a href="register.php" class="btn btn-primary btn-lg animate-float">
                                <i class="bi bi-star"></i> Commencer l'expérience
                            </a>
                        <?php else: ?>
                            <a href="dashboard.php" class="btn btn-primary btn-lg animate-pulse-slow">
                                <i class="bi bi-shop"></i> Accéder à la boutique
                            </a>
                        <?php endif; ?>
                        <a href="dashboard.php" class="btn btn-outline-light btn-lg animate-pulse-slow">
                            <i class="bi bi-eye"></i> Explorer la boutique
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 reveal">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 text-center shadow-custom">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="bi bi-gem text-gradient" style="font-size: 3rem;"></i>
                            </div>
                            <h3 class="card-title">Qualité Premium</h3>
                            <p class="card-text">
                                Chaque produit est soigneusement sélectionné pour sa qualité exceptionnelle et son caractère unique.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 text-center shadow-custom">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="bi bi-truck text-gradient" style="font-size: 3rem;"></i>
                            </div>
                            <h3 class="card-title">Livraison Express</h3>
                            <p class="card-text">
                                Service de livraison rapide et sécurisé pour une expérience d'achat sans souci.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 text-center shadow-custom">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="bi bi-headset text-gradient" style="font-size: 3rem;"></i>
                            </div>
                            <h3 class="card-title">Support 24/7</h3>
                            <p class="card-text">
                                Notre équipe de support client est disponible 24h/24 et 7j/7 pour vous assister.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Preview Section -->
    <section class="py-5 reveal">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="text-gradient">Nos Produits Vedettes</h2>
                <p class="lead">Découvrez une sélection de nos produits les plus populaires</p>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card product-card">
                        <div class="no-image">
                            <i class="bi bi-handbag"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Sac à main en cuir</h5>
                            <p class="card-text text-primary fw-bold">299 €</p>
                            <p class="card-text">
                                <span class="badge bg-success">En stock</span>
                                <span class="badge bg-secondary">Cuir</span>
                            </p>
                            <a href="dashboard.php" class="btn btn-primary w-100">
                                <i class="bi bi-cart-plus"></i> Voir le produit
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card product-card">
                        <div class="no-image">
                            <i class="bi bi-watch"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Montre de luxe</h5>
                            <p class="card-text text-primary fw-bold">599 €</p>
                            <p class="card-text">
                                <span class="badge bg-success">En stock</span>
                                <span class="badge bg-info">Suisse</span>
                            </p>
                            <a href="dashboard.php" class="btn btn-primary w-100">
                                <i class="bi bi-cart-plus"></i> Voir le produit
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card product-card">
                        <div class="no-image">
                            <i class="bi bi-gem"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Collier en or</h5>
                            <p class="card-text text-primary fw-bold">899 €</p>
                            <p class="card-text">
                                <span class="badge bg-warning">Stock limité</span>
                                <span class="badge bg-secondary">Or 18k</span>
                            </p>
                            <a href="dashboard.php" class="btn btn-primary w-100">
                                <i class="bi bi-cart-plus"></i> Voir le produit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="dashboard.php" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-arrow-right"></i> Voir tous les produits
                </a>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-5 bg-glass reveal">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="mb-4">Rejoignez notre communauté</h2>
                    <p class="lead mb-4">
                        Inscrivez-vous dès maintenant et profitez de nos offres exclusives, réductions spéciales et bien plus encore.
                    </p>
                    <a href="register.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-rocket"></i> S'inscrire maintenant
                    </a>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="animate-float">
                        <i class="bi bi-gift text-gradient" style="font-size: 8rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
        
        // Initialize animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate hero title
            anime({
                targets: 'h1 .char',
                translateY: [100, 0],
                opacity: [0, 1],
                easing: 'easeOutExpo',
                duration: 1400,
                delay: (el, i) => 30 * i
            });
            
            // Smooth scroll for navigation links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
            
            // Add loading animation to buttons
            document.querySelectorAll('.btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (this.type === 'submit' || this.href) {
                        this.classList.add('btn-loading');
                        const icon = this.querySelector('i');
                        if (icon) {
                            icon.style.display = 'none';
                        }
                    }
                });
            });
        });
        
        // Parallax effect for hero background
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const heroBackground = document.querySelector('.hero-background');
            if (heroBackground) {
                heroBackground.style.transform = `translateY(${scrolled * 0.5}px)`;
            }
        });
    </script>
</body>
</html>