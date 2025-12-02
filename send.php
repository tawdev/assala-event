<?php
// send.php

require_once 'config.php';

// Récupération des données du formulaire
$nom_raw            = isset($_POST['nom']) ? trim($_POST['nom']) : '';
$telephone_raw      = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
$email_raw          = isset($_POST['email']) ? trim($_POST['email']) : '';
$type_evenement_raw = isset($_POST['type_evenement']) ? trim($_POST['type_evenement']) : '';
$number_of_guests   = isset($_POST['number_of_guests']) ? intval($_POST['number_of_guests']) : 0;
$message_raw        = isset($_POST['message']) ? trim($_POST['message']) : '';

// Version sécurisée pour l'affichage
$nom            = htmlspecialchars($nom_raw);
$telephone      = htmlspecialchars($telephone_raw);
$email          = htmlspecialchars($email_raw);
$type_evenement = htmlspecialchars($type_evenement_raw);
$message        = nl2br(htmlspecialchars($message_raw));

// Enregistrement dans la base de données
try {
    $stmt = $pdo->prepare("INSERT INTO reservations (full_name, phone, email, event_type, number_of_guests, message) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom_raw, $telephone_raw, $email_raw, $type_evenement_raw, $number_of_guests, $message_raw]);
} catch (PDOException $e) {
    // En cas d'erreur, on continue quand même (fallback silencieux)
    error_log("Erreur DB send.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande envoyée – AL ASSALA EVENT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Styles pour la page de confirmation */
        .confirmation-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background: linear-gradient(135deg, #0B2545 0%, #1a3a5f 100%);
            margin-top: 70px;
        }
        
        .confirmation-page .container {
            width: 100%;
            max-width: 700px;
            display: flex;
            justify-content: center;
        }
        
        .confirmation-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            border-radius: 24px;
            padding: 3rem;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
            animation: slideUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        .confirmation-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--color-primary), var(--color-primary-dark));
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 2rem;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s both;
            box-shadow: 0 8px 24px rgba(40, 167, 69, 0.3);
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        
        .success-icon svg {
            width: 45px;
            height: 45px;
            fill: #28a745;
        }
        
        .confirmation-title {
            font-family: "Playfair Display", serif;
            font-size: 2.2rem;
            color: var(--color-text-main);
            margin-bottom: 1rem;
            text-align: center;
            font-weight: 700;
        }
        
        .confirmation-message {
            text-align: center;
            color: rgba(27, 27, 27, 0.7);
            font-size: 1.1rem;
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }
        
        .reservation-details {
            background: linear-gradient(135deg, #F6F5F3 0%, #ffffff 100%);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 2px solid rgba(212, 175, 55, 0.2);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        }
        
        .reservation-details h3 {
            font-family: "Playfair Display", serif;
            font-size: 1.3rem;
            color: var(--color-text-main);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .reservation-details h3 svg {
            width: 24px;
            height: 24px;
            fill: var(--color-primary);
        }
        
        .detail-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .detail-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(212, 175, 55, 0.25) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .detail-icon svg {
            width: 20px;
            height: 20px;
            fill: var(--color-primary);
        }
        
        .detail-content {
            flex: 1;
        }
        
        .detail-label {
            font-size: 0.85rem;
            color: rgba(27, 27, 27, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }
        
        .detail-value {
            font-size: 1.05rem;
            color: var(--color-text-main);
            font-weight: 500;
        }
        
        .detail-message {
            margin-top: 0.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.06);
            color: rgba(27, 27, 27, 0.8);
            line-height: 1.6;
        }
        
        .confirmation-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        @media (max-width: 768px) {
            .confirmation-card {
                padding: 2rem 1.5rem;
                border-radius: 20px;
            }
            
            .confirmation-title {
                font-size: 1.8rem;
            }
            
            .reservation-details {
                padding: 1.5rem;
            }
            
            .confirmation-actions {
                flex-direction: column;
            }
            
            .confirmation-actions .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
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
                <li><a href="gallery.php">Galerie</a></li>
                <li><a href="reserver.php" class="btn btn-primary nav-reserver">Réserver</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="confirmation-page">
    <div class="container">
        <div class="confirmation-card">
            <!-- Icône de succès -->
            <div class="success-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            
            <!-- Titre et message -->
            <h1 class="confirmation-title">Demande envoyée avec succès !</h1>
            <p class="confirmation-message">
                Merci<?php echo $nom ? ' <strong>' . $nom . '</strong>' : ''; ?> pour votre confiance.<br>
                Nous avons bien reçu votre demande et nous vous contacterons prochainement pour finaliser les détails de votre événement.
            </p>

            <!-- Détails de la réservation -->
            <?php if ($type_evenement || $message || $telephone || $email || $number_of_guests > 0): ?>
                <div class="reservation-details">
                    <h3>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4"/>
                            <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3z"/>
                            <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3z"/>
                            <path d="M12 21c0-1-1-3-3-3s-3 2-3 3 1 3 3 3 3-2 3-3z"/>
                            <path d="M12 3c0 1-1 3-3 3S6 4 6 3s1-3 3-3 3 2 3 3z"/>
                        </svg>
                        Détails de votre réservation
                    </h3>
                    
                    <?php if ($type_evenement): ?>
                        <div class="detail-item">
                            <div class="detail-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 7h-4V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                    <path d="M12 11v6M8 14h8"/>
                                </svg>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Type d'événement</div>
                                <div class="detail-value"><?php echo $type_evenement; ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($number_of_guests > 0): ?>
                        <div class="detail-item">
                            <div class="detail-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Nombre d'invités</div>
                                <div class="detail-value"><?php echo $number_of_guests; ?> personne<?php echo $number_of_guests > 1 ? 's' : ''; ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($telephone): ?>
                        <div class="detail-item">
                            <div class="detail-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Téléphone</div>
                                <div class="detail-value"><?php echo $telephone; ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($email): ?>
                        <div class="detail-item">
                            <div class="detail-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                    <polyline points="22,6 12,13 2,6"/>
                                </svg>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Email</div>
                                <div class="detail-value"><?php echo $email; ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($message): ?>
                        <div class="detail-item">
                            <div class="detail-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                </svg>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Votre message</div>
                                <div class="detail-message"><?php echo $message; ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="confirmation-actions">
                <a href="index.php#home" class="btn btn-outline">Retour à l'accueil</a>
                <a href="reserver.php" class="btn btn-primary">Nouvelle réservation</a>
            </div>
        </div>
    </div>
</main>

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
// Burger menu sur la page de confirmation
const navToggle = document.querySelector('.nav-toggle');
const navLinks = document.querySelector('.nav-links');

navToggle.addEventListener('click', () => {
    navLinks.classList.toggle('nav-open');
    navToggle.classList.toggle('nav-open');
});
</script>
</body>
</html>


