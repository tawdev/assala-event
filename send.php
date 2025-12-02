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
    <title>Demande envoyée – Assala Events</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>
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
                <li><a href="index.php#home">Accueil</a></li>
                <li><a href="index.php#services">Nos Services</a></li>
                <li><a href="gallery.php">Galerie</a></li>
                <li><a href="reserver.php" class="btn btn-primary nav-reserver">Réserver</a></li>
            </ul>
        </nav>
    </div>
</header>

<main style="margin-top: 80px;">
    <section class="section section-dark">
        <div class="container" style="max-width: 720px; text-align:center;">
            <h2 style="font-family:'Playfair Display',serif; margin-bottom:1rem;">
                Votre demande a été envoyée avec succès.
            </h2>
            <p style="color:rgba(240,240,240,0.85); margin-bottom:1.5rem;">
                Merci<?php echo $nom ? ' ' . $nom : ''; ?> pour votre confiance.
                Nous vous contacterons prochainement pour confirmer les détails de votre événement.
            </p>

            <?php if ($type_evenement || $message || $telephone || $email || $number_of_guests > 0): ?>
                <div style="text-align:left; margin:0 auto 1.8rem; padding:1rem 1.2rem; border-radius:12px; background:rgba(0,0,0,0.65); border:1px solid rgba(255,255,255,0.08);">
                    <?php if ($type_evenement): ?>
                        <p><strong>Type d'événement :</strong> <?php echo $type_evenement; ?></p>
                    <?php endif; ?>
                    <?php if ($number_of_guests > 0): ?>
                        <p><strong>Nombre d'invités :</strong> <?php echo $number_of_guests; ?></p>
                    <?php endif; ?>
                    <?php if ($telephone): ?>
                        <p><strong>Téléphone :</strong> <?php echo $telephone; ?></p>
                    <?php endif; ?>
                    <?php if ($email): ?>
                        <p><strong>Email :</strong> <?php echo $email; ?></p>
                    <?php endif; ?>
                    <?php if ($message): ?>
                        <p style="margin-top:0.7rem;"><strong>Votre message :</strong><br><?php echo $message; ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <a href="index.php#home" class="btn btn-outline" style="margin-right:0.4rem;">Retour à l’accueil</a>
            <a href="reserver.php" class="btn btn-primary" style="margin-top:0.6rem;">Envoyer une autre demande</a>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="container footer-content">
        <p>© <?php echo date('Y'); ?> Assala Events – Événementiel Marocain. Tous droits réservés.</p>
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


