<?php
session_start();

if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$login_error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    try {
        $stmt = $pdo->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            header("Location: dashboard.php");
            exit();
        } else {
            $login_error = "Email ou mot de passe incorrect";
        }
    } catch(PDOException $e) {
        $login_error = "Erreur lors de la connexion : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Kaoutar Boutique</title>
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
                <span>Kaoutar</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-house"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="login.php">
                            <i class="bi bi-person"></i> Connexion
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">
                            <i class="bi bi-person-plus"></i> Inscription
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php if($login_error): ?>
    <div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 100px; right: 20px; z-index: 9999; min-width: 300px;">
        <i class="bi bi-exclamation-triangle"></i> <?php echo $login_error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="pt-5 mt-5 min-vh-100 d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <!-- Login Card -->
                    <div class="card bg-glass border-glass shadow-custom reveal">
                        <div class="card-body p-5">
                            <!-- Logo -->
                            <div class="text-center mb-4">
                                <div class="mb-3">
                                    <img src="resources/logo.jpg" alt="Kaoutar Logo" style="width: 80px; height: 80px; border-radius: 50%;" class="animate-float">
                                </div>
                                <h2 class="mb-2" data-splitting>Connexion</h2>
                                <p class="text-muted">Bienvenue dans votre boutique de luxe</p>
                            </div>

                            <!-- Login Form -->
                            <form id="loginForm" method="POST">
                                <div class="mb-4">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope"></i> Email
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Entrez votre email" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="password" class="form-label">
                                        <i class="bi bi-lock"></i> Mot de passe
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Entrez votre mot de passe" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                                        <label class="form-check-label" for="rememberMe">
                                            Se souvenir de moi
                                        </label>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mb-4">
                                    <button type="submit" name="login" class="btn btn-primary btn-lg">
                                        <i class="bi bi-box-arrow-in-right"></i> Se connecter
                                    </button>
                                </div>

                                <div class="text-center mb-4">
                                    <a href="#" class="text-decoration-none text-muted small" onclick="showForgotPassword()">
                                        Mot de passe oublié ?
                                    </a>
                                </div>

                                <hr class="my-4">

                                <div class="text-center">
                                    <p class="text-muted mb-3">Pas encore de compte ?</p>
                                    <a href="register.php" class="btn btn-outline-primary">
                                        <i class="bi bi-person-plus"></i> Créer un compte
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Social Login -->
                    <div class="card bg-glass border-glass mt-4 reveal">
                        <div class="card-body text-center">
                            <h6 class="mb-3">Ou connectez-vous avec</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-danger" onclick="socialLogin('google')">
                                    <i class="bi bi-google"></i> Google
                                </button>
                                <button class="btn btn-outline-primary" onclick="socialLogin('facebook')">
                                    <i class="bi bi-facebook"></i> Facebook
                                </button>
                                <button class="btn btn-outline-dark" onclick="socialLogin('apple')">
                                    <i class="bi bi-apple"></i> Apple
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Trust Badges -->
                    <div class="card bg-glass border-glass mt-4 reveal">
                        <div class="card-body text-center">
                            <h6 class="mb-3">Sécurité garantie</h6>
                            <div class="row">
                                <div class="col-4">
                                    <i class="bi bi-shield-check text-success" style="font-size: 1.5rem;"></i>
                                    <p class="small mt-1">Sécurisé</p>
                                </div>
                                <div class="col-4">
                                    <i class="bi bi-lock text-primary" style="font-size: 1.5rem;"></i>
                                    <p class="small mt-1">SSL</p>
                                </div>
                                <div class="col-4">
                                    <i class="bi bi-check-circle text-warning" style="font-size: 1.5rem;"></i>
                                    <p class="small mt-1">Vérifié</p>
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
        
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
        
        // Login form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const button = document.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            
            // Show loading state
            button.classList.add('btn-loading');
            button.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Connexion...';
            button.disabled = true;
            
            // Simulate login process
            setTimeout(() => {
                // Show success
                button.classList.remove('btn-loading');
                button.classList.remove('btn-primary');
                button.classList.add('btn-success');
                button.innerHTML = '<i class="bi bi-check"></i> Connecté !';
                
                // Show success notification
                showNotification('Connexion réussie ! Redirection...', 'success');
                
                // Submit the form
                setTimeout(() => {
                    this.submit();
                }, 1000);
                
            }, 2000);
        });
        
        // Social login function
        function socialLogin(provider) {
            showNotification(`Connexion avec ${provider}...`, 'info');
            
            // Simulate social login process
            setTimeout(() => {
                showNotification(`Connexion ${provider} réussie !`, 'success');
                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 1500);
            }, 2000);
        }
        
        // Show forgot password modal
        function showForgotPassword() {
            showNotification('Fonctionnalité en cours de développement', 'info');
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
                targets: 'h2 .char',
                translateY: [100, 0],
                opacity: [0, 1],
                easing: 'easeOutExpo',
                duration: 1400,
                delay: (el, i) => 30 * i
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
            
            // Animate form elements
            anime({
                targets: '.form-control, .form-check, .btn',
                translateX: [-50, 0],
                opacity: [0, 1],
                easing: 'easeOutExpo',
                duration: 800,
                delay: (el, i) => 50 * i
            });
        });
    </script>
</body>
</html>