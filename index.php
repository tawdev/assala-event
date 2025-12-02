<?php
// Page d'accueil principale en PHP
require_once 'config.php';

// Récupération des images depuis la base de données (limiter à 9 pour l'aperçu)
try {
    $galleryImages = $pdo->query("
        SELECT g.*, c.name as category_name 
        FROM gallery g 
        LEFT JOIN categories c ON g.category_id = c.id 
        ORDER BY g.created_at DESC 
        LIMIT 9
    ")->fetchAll();
} catch (PDOException $e) {
    $galleryImages = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AL ASSALA EVENT – Événementiel Marocain</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Société d’événementiel spécialisée dans les événements marocains : mariage, fiançailles, khotoba, aqiqah, anniversaires.">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&amp;family=Inter:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <div class="container nav-container">
            <div class="logo">
                <a href="index.php#home" class="logo-link">
                    <img src="images/logo/image.png" alt="AL ASSALA EVENT" class="navbar-logo-img">
                    <span class="logo-text">AL ASSALA EVENT</span>
                </a>
            </div>

            <nav class="nav">
                <button class="nav-toggle" aria-label="Ouvrir le menu">
                    <span class="burger"></span>
                    <span class="burger"></span>
                    <span class="burger"></span>
                </button>

                <ul class="nav-links">
                    <li><a href="#home">Accueil</a></li>
                    <li><a href="#services">Nos Services</a></li>
                    <li><a href="gallery.php">Galerie</a></li>
                    <li><a href="reserver.php" class="btn btn-primary nav-reserver">Réserver</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero / Accueil -->
    <main>
        <section id="home" class="hero">
            <div class="hero-background">
                <img src="images/logo/main photo.jpeg" alt="Salle de mariage marocaine">
            </div>
            <div class="hero-overlay"></div>
            <div class="container hero-content">
                <div class="hero-logo">
                    <div class="logo hero-logo-box">
                        <img src="images/logo/image.png" alt="Al Assala Event - Logo">
                    </div>
                </div>
                <div class="hero-text">
                    <p class="hero-tagline">Des événements marocains authentiques et mémorables</p>
                </div>
            </div>
        </section>

        <!-- Nos Services -->
        <section id="services" class="section section-light">
            <div class="container">
                <div class="section-header">
                    <h2>Nos Services</h2>
                    <p>Une prise en charge complète de vos événements marocains, avec élégance et professionnalisme.</p>
                </div>

                <div class="services-grid">
                    <!-- Card 1 -->
                    <article class="service-card">
                        <div class="service-icon">
                            <span>💍</span>
                        </div>
                        <h3>Organisation de mariage</h3>
                        <p>
                            Cérémonie marocaine complète : négafa, décoration, musique, coordination du jour J
                            pour un mariage inoubliable.
                        </p>
                    </article>

                    <!-- Card 2 -->
                    <article class="service-card">
                        <div class="service-icon">
                            <span>💖</span>
                        </div>
                        <h3>Organisation de fiançailles</h3>
                        <p>
                            Mise en scène raffinée de vos fiançailles avec table de henné, plateaux traditionnels
                            et décoration florale.
                        </p>
                    </article>

                    <!-- Card 3 -->
                    <article class="service-card">
                        <div class="service-icon">
                            <span>👶</span>
                        </div>
                        <h3>Aqiqah</h3>
                        <p>
                            Organisation de la cérémonie d’Aqiqah dans le respect des traditions marocaines,
                            avec accueil de vos invités et service traiteur.
                        </p>
                    </article>

                    <!-- Card 4 -->
                    <article class="service-card">
                        <div class="service-icon">
                            <span>🎀</span>
                        </div>
                        <h3>Décoration événementielle</h3>
                        <p>
                            Scénographie complète : chaises royales, lanternes, tapis, compositions florales
                            et ambiance lumineuse chaleureuse.
                        </p>
                    </article>

                    <!-- Card 5 -->
                    <article class="service-card">
                        <div class="service-icon">
                            <span>🌙</span>
                        </div>
                        <h3>eid</h3>
                        <p>
                        Saveurs authentiques : méchoui, tajines de fête, rfissa, gâteaux marocains et thé à la menthe.
Un service raffiné pour célébrer l’Aïd en beauté.

                        </p>
                    </article>

                    <!-- Card 6 -->
                    <article class="service-card">
                        <div class="service-icon">
                            <span>🍽️</span>
                        </div>
                        <h3>Traiteur marocain</h3>
                        <p>
                            Menus traditionnels : pastilla, méchoui, tajines, pâtisseries marocaines et thé à la menthe,
                            servis avec élégance.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <!-- Galerie -->
        <section id="gallery" class="section section-dark">
            <div class="container">
                <div class="section-header">
                    <h2>Galerie</h2>
                    <p>Un aperçu de l'ambiance et du raffinement de nos événements marocains.</p>
                </div>

                <?php if (empty($galleryImages)): ?>
                    <div style="text-align: center; padding: 3rem 2rem; color: rgba(246, 245, 243, 0.7);">
                        <p>La galerie sera bientôt mise à jour avec nos plus belles réalisations.</p>
                    </div>
                <?php else: ?>
                    <div class="gallery-grid">
                        <?php foreach ($galleryImages as $img): ?>
                            <div class="gallery-item">
                                <img src="<?php echo htmlspecialchars($img['image_path']); ?>" 
                                     alt="<?php echo !empty($img['category_name']) ? 'Événement ' . htmlspecialchars($img['category_name']) : 'Événement marocain'; ?>"
                                     loading="lazy">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="gallery-cta">
                    <a href="gallery.php" class="btn btn-primary">Voir toute la galerie</a>
                    <a href="reserver.php" class="btn btn-outline" style="margin-left: 0.5rem;">Réserver votre événement</a>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer dynamique -->
    <footer class="footer">
        <div class="container">
            <div class="footer-main">
                <!-- Logo & Description -->
                <div class="footer-section footer-about">
                    <div class="footer-logo">
                        <div class="footer-logo-container">
                            <img src="images/logo/image.png" alt="AL ASSALA EVENT" class="footer-logo-img">
                            <span class="footer-logo-text">AL ASSALA EVENT</span>
                        </div>
                    </div>
                    <p class="footer-description">
                        Votre partenaire de confiance pour des événements marocains inoubliables. 
                        Mariages, fiançailles, khotoba, aqiqah et anniversaires.
                    </p>
                </div>

                <!-- Contact Information -->
                <div class="footer-section footer-contact">
                    <h3 class="footer-title">Contact</h3>
                    <ul class="footer-contact-list">
                        <li>
                            <svg class="footer-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                            <a href="tel:+212524308038">0524308038</a>
                        </li>
                        <li>
                            <svg class="footer-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                            <a href="mailto:contact@assalaevents.ma">contact@assalaevents.ma</a>
                        </li>
                        <li>
                            <svg class="footer-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span>Lot Iguider N48 Av Allal El Fassi, Marrakech</span>
                        </li>
                    </ul>
                </div>

                <!-- Social Media -->
                <div class="footer-section footer-social">
                    <h3 class="footer-title">Suivez-nous</h3>
                    <div class="footer-social-links">
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.98-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.98-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                        <a href="https://tiktok.com" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="TikTok">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <p>© <?php echo date('Y'); ?> AL ASSALA EVENT. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- JS: burger + scroll smooth amélioré -->
    <script>
        // Menu burger
        const navToggle = document.querySelector('.nav-toggle');
        const navLinks = document.querySelector('.nav-links');

        navToggle.addEventListener('click', () => {
            navLinks.classList.toggle('nav-open');
            navToggle.classList.toggle('nav-open');
        });

        // Fermer le menu après clic sur un lien (en mobile)
        document.querySelectorAll('.nav-links a[href^="#"]').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('nav-open');
                navToggle.classList.remove('nav-open');
            });
        });

        // Scroll smooth JS (pour compatibilité maximale)
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    e.preventDefault();
                    window.scrollTo({
                        top: targetElement.offsetTop - 80, // hauteur de la navbar
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>


