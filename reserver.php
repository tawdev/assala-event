<?php
// reserver.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réserver un événement – Assala Events</title>
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
    <section class="section section-light">
        <div class="container" style="max-width: 720px;">
            <div class="section-header" style="text-align:left;">
                <h2>Réserver votre événement</h2>
                <p>Remplissez ce formulaire et nous vous contacterons rapidement pour finaliser les détails de votre événement.</p>
            </div>

            <form action="send.php" method="POST" class="booking-form">
                <div class="form-group">
                    <label for="nom">Nom complet</label>
                    <input type="text" id="nom" name="nom" required>
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="type_evenement">Type d'événement</label>
                    <select id="type_evenement" name="type_evenement" required>
                        <option value="">-- Sélectionnez un type --</option>
                        <option value="Mariage">Mariage</option>
                        <option value="Fiançailles">Fiançailles</option>
                        <option value="Khotoba">Khotoba</option>
                        <option value="Aqiqah">Aqiqah</option>
                        <option value="Anniversaire">Anniversaire</option>
                        <option value="Autre">Autre événement</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="number_of_guests">Nombre d'invités</label>
                    <input type="number" id="number_of_guests" name="number_of_guests" min="1" max="10000" placeholder="Ex: 100" required>
                </div>

                <div class="form-group">
                    <label for="message">Message / Détails</label>
                    <textarea id="message" name="message" rows="5" placeholder="Date souhaitée, lieu, style d'événement…"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Envoyer la demande</button>
            </form>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="container footer-content">
        <p>© <?php echo date('Y'); ?> Assala Events – Événementiel Marocain. Tous droits réservés.</p>
    </div>
</footer>

<script>
// Burger menu sur la page réservation
const navToggle = document.querySelector('.nav-toggle');
const navLinks = document.querySelector('.nav-links');

navToggle.addEventListener('click', () => {
    navLinks.classList.toggle('nav-open');
    navToggle.classList.toggle('nav-open');
});

// Scroll smooth vers ancres internes éventuelles
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const targetId = this.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);
        if (targetElement) {
            e.preventDefault();
            window.scrollTo({
                top: targetElement.offsetTop - 80,
                behavior: 'smooth'
            });
        }
    });
});
</script>
</body>
</html>


