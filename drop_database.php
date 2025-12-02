<?php
// drop_database.php - Script pour supprimer toutes les tables et la base de données

// Configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; // Mot de passe vide par défaut pour XAMPP
$db_name = 'assala_events';

// Confirmation de sécurité
$confirmed = isset($_GET['confirm']) && $_GET['confirm'] === 'yes';

if (!$confirmed) {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Supprimer la base de données</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
            .warning-box { background: #fff3cd; border: 2px solid #ffc107; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h1 { color: #856404; margin-top: 0; }
            .danger { color: #dc3545; font-weight: bold; }
            .btn { display: inline-block; margin: 10px 5px 0 0; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; }
            .btn-danger { background: #dc3545; color: white; }
            .btn-danger:hover { background: #c82333; }
            .btn-cancel { background: #6c757d; color: white; }
            .btn-cancel:hover { background: #5a6268; }
        </style>
    </head>
    <body>
        <div class="warning-box">
            <h1>⚠️ Attention !</h1>
            <p class="danger">Vous êtes sur le point de supprimer <strong>TOUTES</strong> les données de la base de données <strong><?php echo $db_name; ?></strong>.</p>
            <p>Cette action est <strong>irréversible</strong> et supprimera :</p>
            <ul>
                <li>Toutes les réservations</li>
                <li>Toutes les images de la galerie</li>
                <li>Toutes les catégories</li>
                <li>La base de données elle-même</li>
            </ul>
            <p><strong>Êtes-vous absolument sûr de vouloir continuer ?</strong></p>
            <a href="?confirm=yes" class="btn btn-danger">Oui, supprimer tout</a>
            <a href="index.php" class="btn btn-cancel">Annuler</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Suppression confirmée
try {
    // Connexion
    $pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier si la base existe
    $stmt = $pdo->query("SHOW DATABASES LIKE '$db_name'");
    $db_exists = $stmt->rowCount() > 0;
    
    if ($db_exists) {
        // Supprimer la base de données (supprime automatiquement toutes les tables)
        $pdo->exec("DROP DATABASE IF EXISTS $db_name");
        echo "✅ Base de données '$db_name' supprimée avec succès.<br>";
        echo "Toutes les tables et données ont été effacées.<br><br>";
    } else {
        echo "ℹ️ La base de données '$db_name' n'existe pas.<br><br>";
    }
    
    echo "<strong>✅ Opération terminée.</strong><br><br>";
    echo "<a href='install_database.php' style='display:inline-block;margin-top:15px;padding:10px 20px;background:#0B2545;color:white;text-decoration:none;border-radius:5px;'>Recréer la base de données</a> ";
    echo "<a href='index.php' style='display:inline-block;margin-top:15px;padding:10px 20px;background:#D4AF37;color:#0B2545;text-decoration:none;border-radius:5px;'>Retour à l'accueil</a>";
    
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>

