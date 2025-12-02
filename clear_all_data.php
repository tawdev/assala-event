<?php
// clear_all_data.php - Script pour supprimer toutes les données mais garder la structure

require_once 'config.php';

// Confirmation de sécurité
$confirmed = isset($_GET['confirm']) && $_GET['confirm'] === 'yes';

if (!$confirmed) {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Vider les tables</title>
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
            <p class="danger">Vous êtes sur le point de supprimer <strong>TOUTES</strong> les données des tables.</p>
            <p>Cette action est <strong>irréversible</strong> et supprimera :</p>
            <ul>
                <li>Toutes les réservations</li>
                <li>Toutes les images de la galerie</li>
                <li>Toutes les catégories</li>
            </ul>
            <p><strong>Note :</strong> Les tables seront conservées (structure seulement).</p>
            <p><strong>Êtes-vous absolument sûr de vouloir continuer ?</strong></p>
            <a href="?confirm=yes" class="btn btn-danger">Oui, vider toutes les données</a>
            <a href="admin.php" class="btn btn-cancel">Annuler</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Suppression confirmée
try {
    // Vider la table reservations
    $pdo->exec("TRUNCATE TABLE reservations");
    echo "✅ Table 'reservations' vidée.<br>";
    
    // Vider la table gallery
    $pdo->exec("TRUNCATE TABLE gallery");
    echo "✅ Table 'gallery' vidée.<br>";
    
    // Vider la table categories
    $pdo->exec("TRUNCATE TABLE categories");
    echo "✅ Table 'categories' vidée.<br>";
    
    // Réinsérer les catégories par défaut
    $pdo->exec("INSERT INTO categories (name) VALUES
        ('Mariage'),
        ('Fiançailles'),
        ('Khotoba'),
        ('Aqiqah'),
        ('Anniversaire')
    ON DUPLICATE KEY UPDATE name=name");
    echo "✅ Catégories par défaut réinsérées.<br>";
    
    echo "<br><strong>✅ Toutes les données ont été supprimées.</strong><br><br>";
    echo "<a href='admin.php' style='display:inline-block;margin-top:15px;padding:10px 20px;background:#0B2545;color:white;text-decoration:none;border-radius:5px;'>Retour à l'admin</a> ";
    echo "<a href='index.php' style='display:inline-block;margin-top:15px;padding:10px 20px;background:#D4AF37;color:#0B2545;text-decoration:none;border-radius:5px;'>Retour à l'accueil</a>";
    
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>

