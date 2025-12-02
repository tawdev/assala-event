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
    <title>Événementiel Marocain – Mariage, Fiançailles, Aqiqah &amp; Plus</title>
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
                <span class="logo-main">Assala Events</span>
                <span class="logo-sub">Événementiel Marocain</span>
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
                            <span>🎤</span>
                        </div>
                        <h3>Location matériel</h3>
                        <p>
                            Sonorisation, éclairage, mobilier, trônes, vaisselle, estrades, structures lumineuses
                            pour tous types d’événements.
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
                    <p>Un aperçu de l’ambiance et du raffinement de nos événements marocains.</p>
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
        <div class="container footer-content">
            <p>© <?php echo date('Y'); ?> Assala Events – Événementiel Marocain. Tous droits réservés.</p>
            <p class="footer-small">Mariages, fiançailles, khotoba, aqiqah, anniversaires &amp; événements privés.</p>
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


