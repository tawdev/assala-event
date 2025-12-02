<?php
// gallery.php - Page galerie complète avec toutes les images de la base de données

require_once 'config.php';

// Filtre par catégorie
$categoryFilter = isset($_GET['category']) ? intval($_GET['category']) : null;

// Récupération des catégories
try {
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

// Préparer les catégories : 5 premières en boutons, le reste dans un select
$primaryCategories = [];
$extraCategories   = [];
if (!empty($categories)) {
    $primaryCategories = array_slice($categories, 0, 5);
    if (count($categories) > 5) {
        $extraCategories = array_slice($categories, 5);
    }
}

// Récupération des images depuis la base de données
try {
    if ($categoryFilter) {
        $stmt = $pdo->prepare("
            SELECT g.*, c.name as category_name 
            FROM gallery g 
            LEFT JOIN categories c ON g.category_id = c.id 
            WHERE g.category_id = ?
            ORDER BY g.created_at DESC
        ");
        $stmt->execute([$categoryFilter]);
        $images = $stmt->fetchAll();
    } else {
        $images = $pdo->query("
            SELECT g.*, c.name as category_name 
            FROM gallery g 
            LEFT JOIN categories c ON g.category_id = c.id 
            ORDER BY g.created_at DESC
        ")->fetchAll();
    }
} catch (PDOException $e) {
    $images = [];
    $error = "Erreur lors de la récupération des images : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Galerie – AL ASSALA EVENT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Découvrez notre galerie d'événements marocains : mariages, fiançailles, aqiqah et plus encore.">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Styles spécifiques pour la page galerie */
        .gallery-page {
            margin-top: 70px;
            padding: 3rem 0;
        }
        
        .gallery-page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .gallery-page-header h1 {
            font-family: "Playfair Display", serif;
            font-size: clamp(2rem, 4vw, 2.8rem);
            margin-bottom: 0.8rem;
            color: var(--color-text-main);
        }
        
        .gallery-page-header p {
            color: rgba(27, 27, 27, 0.7);
            font-size: 1.05rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .gallery-page-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .gallery-page-item {
            position: relative;
            border-radius: var(--radius-md);
            overflow: hidden;
            cursor: pointer;
            background: #ffffff;
            border: 1px solid rgba(11, 37, 69, 0.15);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            aspect-ratio: 4 / 3;
        }
        
        .gallery-page-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .gallery-page-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
        }
        
        .gallery-page-item:hover img {
            transform: scale(1.1);
        }
        
        .gallery-page-item::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.5), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .gallery-page-item:hover::after {
            opacity: 1;
        }
        
        .gallery-empty {
            text-align: center;
            padding: 4rem 2rem;
            color: rgba(27, 27, 27, 0.6);
        }
        
        .gallery-empty h2 {
            font-family: "Playfair Display", serif;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        
        /* Lightbox pour voir les images en grand */
        .lightbox {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(10px);
        }
        
        .lightbox.active {
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .lightbox-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
            margin: auto;
        }
        
        .lightbox-content img {
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }
        
        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 30px;
            color: #fff;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            z-index: 10000;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            transition: background 0.3s;
        }
        
        .lightbox-close:hover {
            background: rgba(0, 0, 0, 0.8);
        }
        
        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #fff;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
            padding: 15px 20px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 8px;
            transition: background 0.3s;
            z-index: 10000;
        }
        
        .lightbox-nav:hover {
            background: rgba(0, 0, 0, 0.8);
        }
        
        .lightbox-prev {
            left: 20px;
        }
        
        .lightbox-next {
            right: 20px;
        }
        
        @media (max-width: 768px) {
            .gallery-page-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 1rem;
            }
            
            .lightbox-close {
                top: 10px;
                right: 15px;
                font-size: 30px;
                width: 40px;
                height: 40px;
            }
            
            .lightbox-nav {
                font-size: 24px;
                padding: 10px 15px;
            }
            
            .lightbox-prev {
                left: 10px;
            }
            
            .lightbox-next {
                right: 10px;
            }
        }
    </style>
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
                    <li><a href="index.php#home">Accueil</a></li>
                    <li><a href="index.php#services">Nos Services</a></li>
                    <li><a href="gallery.php" class="active">Galerie</a></li>
                    <li><a href="reserver.php" class="btn btn-primary nav-reserver">Réserver</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Page Galerie -->
    <main class="gallery-page">
        <section class="section section-light">
            <div class="container">
                <div class="gallery-page-header">
                    <h1>Notre Galerie</h1>
                    <p>Découvrez l'élégance et le raffinement de nos événements marocains à travers nos réalisations.</p>
                    
                    <?php if (!empty($categories)): ?>
                        <div style="margin-top: 2rem; display: flex; justify-content: center; flex-wrap: wrap; gap: 0.5rem;">
                            <!-- Bouton Toutes -->
                            <a href="gallery.php" 
                               class="btn <?php echo !$categoryFilter ? 'btn-primary' : 'btn-outline'; ?>" 
                               style="text-decoration: none;">
                                Toutes
                            </a>

                            <!-- 5 premières catégories en boutons -->
                            <?php foreach ($primaryCategories as $cat): ?>
                                <a href="gallery.php?category=<?php echo $cat['id']; ?>" 
                                   class="btn <?php echo $categoryFilter == $cat['id'] ? 'btn-primary' : 'btn-outline'; ?>" 
                                   style="text-decoration: none;">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a>
                            <?php endforeach; ?>

                            <!-- 6ème élément : select pour les autres catégories -->
                            <?php if (!empty($extraCategories)): ?>
                                <select
                                    onchange="if(this.value){ window.location='gallery.php?category=' + this.value; }"
                                    class="btn btn-outline"
                                    style="min-width: 190px; padding: 0.6rem 1rem; border-radius: 999px; cursor: pointer;">
                                    <option value="">
                                        <?php
                                        // Texte par défaut : si une catégorie « extra » est active, afficher son nom
                                        $activeExtraName = '';
                                        foreach ($extraCategories as $extraCat) {
                                            if ($categoryFilter == $extraCat['id']) {
                                                $activeExtraName = $extraCat['name'];
                                                break;
                                            }
                                        }
                                        echo $activeExtraName
                                            ? 'Autres : ' . htmlspecialchars($activeExtraName)
                                            : 'Autres catégories';
                                        ?>
                                    </option>
                                    <?php foreach ($extraCategories as $extraCat): ?>
                                        <option value="<?php echo $extraCat['id']; ?>" <?php echo $categoryFilter == $extraCat['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($extraCat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (empty($images)): ?>
                    <div class="gallery-empty">
                        <h2>Galerie vide</h2>
                        <p>La galerie sera bientôt mise à jour avec nos plus belles réalisations.</p>
                        <a href="index.php#home" class="btn btn-primary" style="margin-top: 1.5rem;">Retour à l'accueil</a>
                    </div>
                <?php else: ?>
                    <div class="gallery-page-grid">
                        <?php foreach ($images as $index => $img): ?>
                            <div class="gallery-page-item" data-index="<?php echo $index; ?>" onclick="openLightbox(<?php echo $index; ?>)">
                                <img src="<?php echo htmlspecialchars($img['image_path']); ?>" 
                                     alt="Événement marocain - Image <?php echo $index + 1; ?>"
                                     loading="lazy">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="gallery-cta">
                        <a href="reserver.php" class="btn btn-primary">Réserver votre événement</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Lightbox -->
    <div id="lightbox" class="lightbox">
        <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
        <span class="lightbox-nav lightbox-prev" onclick="changeImage(-1)">&#10094;</span>
        <span class="lightbox-nav lightbox-next" onclick="changeImage(1)">&#10095;</span>
        <div class="lightbox-content">
            <img id="lightbox-img" src="" alt="Image en grand">
        </div>
    </div>

    <!-- Footer -->
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

    <script>
        // Données des images pour le lightbox
        const images = <?php echo json_encode(array_column($images, 'image_path')); ?>;
        let currentImageIndex = 0;

        // Ouvrir le lightbox
        function openLightbox(index) {
            currentImageIndex = index;
            const lightbox = document.getElementById('lightbox');
            const lightboxImg = document.getElementById('lightbox-img');
            lightboxImg.src = images[index];
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // Fermer le lightbox
        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Changer d'image dans le lightbox
        function changeImage(direction) {
            currentImageIndex += direction;
            
            if (currentImageIndex < 0) {
                currentImageIndex = images.length - 1;
            } else if (currentImageIndex >= images.length) {
                currentImageIndex = 0;
            }
            
            document.getElementById('lightbox-img').src = images[currentImageIndex];
        }

        // Navigation au clavier
        document.addEventListener('keydown', function(e) {
            const lightbox = document.getElementById('lightbox');
            if (lightbox.classList.contains('active')) {
                if (e.key === 'Escape') {
                    closeLightbox();
                } else if (e.key === 'ArrowLeft') {
                    changeImage(-1);
                } else if (e.key === 'ArrowRight') {
                    changeImage(1);
                }
            }
        });

        // Fermer en cliquant sur le fond
        document.getElementById('lightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });

        // Menu burger
        const navToggle = document.querySelector('.nav-toggle');
        const navLinks = document.querySelector('.nav-links');

        if (navToggle) {
            navToggle.addEventListener('click', () => {
                navLinks.classList.toggle('nav-open');
                navToggle.classList.toggle('nav-open');
            });

            // Fermer le menu après clic sur un lien
            document.querySelectorAll('.nav-links a').forEach(link => {
                link.addEventListener('click', () => {
                    navLinks.classList.remove('nav-open');
                    navToggle.classList.remove('nav-open');
                });
            });
        }
    </script>
</body>
</html>

