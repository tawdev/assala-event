<?php
// config.php - Configuration de la base de données

// Paramètres de connexion MySQL
define('DB_HOST', 'localhost');
define('DB_NAME', 'assala_events');
define('DB_USER', 'root');
define('DB_PASS', ''); // Mot de passe vide par défaut pour XAMPP
define('DB_CHARSET', 'utf8mb4');

// Dossier pour les uploads d'images
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', 'uploads/');

// Taille max des images (3MB)
define('MAX_FILE_SIZE', 3 * 1024 * 1024);

// Types d'images autorisés
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']);

// Connexion PDO
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES    => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Si la base de données n'existe pas, donner des instructions claires
    if ($e->getCode() == 1049) {
        die("
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <title>Base de données manquante</title>
            <style>
                body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
                .error-box { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                h1 { color: #d32f2f; margin-top: 0; }
                .steps { background: #e3f2fd; padding: 20px; border-radius: 6px; margin: 20px 0; }
                .steps ol { margin: 10px 0; padding-left: 25px; }
                .steps li { margin: 8px 0; }
                code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New', monospace; }
                .btn { display: inline-block; margin-top: 15px; padding: 10px 20px; background: #0B2545; color: white; text-decoration: none; border-radius: 5px; }
                .btn:hover { background: #0d3a5f; }
            </style>
        </head>
        <body>
            <div class='error-box'>
                <h1>⚠️ Base de données non trouvée</h1>
                <p>La base de données <strong>" . DB_NAME . "</strong> n'existe pas encore.</p>
                
                <div class='steps'>
                    <h3>📋 Instructions pour créer la base de données :</h3>
                    <ol>
                        <li>Ouvrez <strong>phpMyAdmin</strong> : <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>
                        <li>Cliquez sur l'onglet <strong>SQL</strong> en haut</li>
                        <li>Copiez-collez le contenu du fichier <code>database.sql</code> dans votre projet</li>
                        <li>Cliquez sur <strong>Exécuter</strong></li>
                        <li>Rafraîchissez cette page</li>
                    </ol>
                    
                    <p><strong>OU</strong> importez directement le fichier <code>database.sql</code> via l'onglet <strong>Importer</strong> dans phpMyAdmin.</p>
                </div>
                
                <a href='index.php' class='btn'>Retour à l'accueil</a>
            </div>
        </body>
        </html>
        ");
    } else {
        die("Erreur de connexion à la base de données : " . $e->getMessage() . "<br><br><a href='index.php'>Retour à l'accueil</a>");
    }
}

// Créer le dossier uploads s'il n'existe pas
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
?>

