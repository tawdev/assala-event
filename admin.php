<?php
// admin.php - Dashboard admin complet avec base de données

require_once 'config.php';

// Démarrer la session pour les messages flash
session_start();

// Gestion des actions
$action = $_GET['action'] ?? '';
$message = $_SESSION['admin_message'] ?? '';
$error = $_SESSION['admin_error'] ?? '';

// Effacer les messages après affichage
unset($_SESSION['admin_message']);
unset($_SESSION['admin_error']);

// Suppression d'une réservation
if ($action === 'delete_reservation' && isset($_GET['id'])) {
    $section = $_GET['section'] ?? 'reservations';
    try {
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $_SESSION['admin_message'] = "Réservation supprimée avec succès.";
        header("Location: admin.php?section=$section");
        exit;
    } catch (PDOException $e) {
        $_SESSION['admin_error'] = "Erreur lors de la suppression : " . $e->getMessage();
        header("Location: admin.php?section=$section");
        exit;
    }
}

// Upload d'une ou plusieurs nouvelles images
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_image'])) {
    if (!isset($_FILES['images'])) {
        $_SESSION['admin_error'] = "Aucun fichier sélectionné.";
        header("Location: admin.php?section=gallery");
        exit;
    }

    $files       = $_FILES['images'];
    $totalFiles  = is_array($files['name']) ? count($files['name']) : 0;
    $categoryId  = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;

    if ($totalFiles === 0) {
        $_SESSION['admin_error'] = "Aucun fichier sélectionné.";
        header("Location: admin.php?section=gallery");
        exit;
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $uploadedCount     = 0;
    $errorCount        = 0;

    for ($i = 0; $i < $totalFiles; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            $errorCount++;
            continue;
        }

        $name     = $files['name'][$i];
        $tmpName  = $files['tmp_name'][$i];
        $size     = $files['size'][$i];

        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        // Vérifier l'extension
        if (!in_array($extension, $allowedExtensions)) {
            $errorCount++;
            continue;
        }

        // Vérifier la taille
        if ($size > MAX_FILE_SIZE) {
            $errorCount++;
            continue;
        }

        // Validation MIME type si disponible
        if (function_exists('finfo_open')) {
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $tmpName);
            finfo_close($finfo);

            if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
                $errorCount++;
                continue;
            }
        }

        // Générer un nom unique
        $newFileName = uniqid('img_', true) . '.' . $extension;
        $targetPath  = UPLOAD_DIR . $newFileName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            $imagePath = UPLOAD_URL . $newFileName;

            try {
                $stmt = $pdo->prepare("INSERT INTO gallery (image_path, category_id) VALUES (?, ?)");
                $stmt->execute([$imagePath, $categoryId]);
                $uploadedCount++;
            } catch (PDOException $e) {
                @unlink($targetPath); // Supprimer le fichier si erreur DB
                $errorCount++;
            }
        } else {
            $errorCount++;
        }
    }

    if ($uploadedCount > 0) {
        $_SESSION['admin_message'] = $uploadedCount . " image(s) uploadée(s) avec succès." . ($errorCount ? " $errorCount fichier(s) n'ont pas pu être traités." : "");
    } else {
        $_SESSION['admin_error'] = "Aucune image n'a pu être uploadée. Vérifiez les fichiers sélectionnés.";
    }

    header("Location: admin.php?section=gallery");
    exit;
}

// Mise à jour de la catégorie d'une image de la galerie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_image_category'])) {
    $imageId    = isset($_POST['image_id']) ? intval($_POST['image_id']) : 0;
    $categoryId = isset($_POST['category_id']) && $_POST['category_id'] !== '' ? intval($_POST['category_id']) : null;
    $section    = $_GET['section'] ?? 'gallery';

    if ($imageId > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE gallery SET category_id = :category_id WHERE id = :id");
            $stmt->bindValue(':id', $imageId, PDO::PARAM_INT);
            if ($categoryId === null) {
                $stmt->bindValue(':category_id', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
            }
            $stmt->execute();

            $_SESSION['admin_message'] = "Catégorie de l'image mise à jour avec succès.";
        } catch (PDOException $e) {
            $_SESSION['admin_error'] = "Erreur lors de la mise à jour de la catégorie : " . $e->getMessage();
        }
    } else {
        $_SESSION['admin_error'] = "Image invalide pour la mise à jour de la catégorie.";
    }

    header("Location: admin.php?section=$section");
    exit;
}

// Suppression d'une image
if ($action === 'delete_image' && isset($_GET['id'])) {
    $section = $_GET['section'] ?? 'gallery';
    try {
        $stmt = $pdo->prepare("SELECT image_path FROM gallery WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $image = $stmt->fetch();
        
        if ($image) {
            $filePath = __DIR__ . '/' . $image['image_path'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        
        $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $_SESSION['admin_message'] = "Image supprimée avec succès.";
        header("Location: admin.php?section=$section");
        exit;
    } catch (PDOException $e) {
        $_SESSION['admin_error'] = "Erreur lors de la suppression : " . $e->getMessage();
        header("Location: admin.php?section=$section");
        exit;
    }
}

// Ajout d'une catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $categoryName = trim($_POST['category_name'] ?? '');
    if (empty($categoryName)) {
        $_SESSION['admin_error'] = "Le nom de la catégorie est requis.";
        header("Location: admin.php?section=categories");
        exit;
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$categoryName]);
            $_SESSION['admin_message'] = "Catégorie ajoutée avec succès.";
            header("Location: admin.php?section=categories");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['admin_error'] = "Cette catégorie existe déjà.";
            } else {
                $_SESSION['admin_error'] = "Erreur lors de l'ajout : " . $e->getMessage();
            }
            header("Location: admin.php?section=categories");
            exit;
        }
    }
}

// Suppression d'une catégorie
if ($action === 'delete_category' && isset($_GET['id'])) {
    $section = $_GET['section'] ?? 'categories';
    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $_SESSION['admin_message'] = "Catégorie supprimée avec succès.";
        header("Location: admin.php?section=$section");
        exit;
    } catch (PDOException $e) {
        $_SESSION['admin_error'] = "Erreur lors de la suppression : " . $e->getMessage();
        header("Location: admin.php?section=$section");
        exit;
    }
}

// Récupération des données
try {
    $reservations = $pdo->query("SELECT * FROM reservations ORDER BY created_at DESC")->fetchAll();
    // Récupérer les images avec leurs catégories
    $gallery = $pdo->query("
        SELECT g.*, c.name as category_name 
        FROM gallery g 
        LEFT JOIN categories c ON g.category_id = c.id 
        ORDER BY g.created_at DESC
    ")->fetchAll();
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des données : " . $e->getMessage();
    $reservations = [];
    $gallery = [];
    $categories = [];
}

// Section active (pour la navigation)
$activeSection = $_GET['section'] ?? 'reservations';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard – AL ASSALA EVENT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Dashboard Admin Styles - Modern Design */
        * {
            box-sizing: border-box;
        }
        
        .admin-dashboard {
            display: flex;
            min-height: calc(100vh - 70px);
            margin-top: 70px;
            background: linear-gradient(135deg, #F6F5F3 0%, #ffffff 100%);
        }
        
        .admin-sidebar {
            width: 280px;
            background: linear-gradient(180deg, #0B2545 0%, #1a3a5f 100%);
            color: var(--color-text-light);
            padding: 2.5rem 0;
            position: fixed;
            height: calc(100vh - 70px);
            overflow-y: auto;
            z-index: 100;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .admin-sidebar h2 {
            font-family: "Playfair Display", serif;
            font-size: 1.5rem;
            padding: 0 2rem;
            margin-bottom: 2rem;
            color: var(--color-primary);
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        
        .admin-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .admin-nav li {
            margin: 0.25rem 0;
        }
        
        .admin-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2rem;
            color: rgba(246, 245, 243, 0.85);
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent;
            font-weight: 500;
            position: relative;
        }
        
        .admin-nav a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 0;
            background: linear-gradient(90deg, rgba(212, 175, 55, 0.2), transparent);
            transition: width 0.3s ease;
        }
        
        .admin-nav a:hover::before,
        .admin-nav a.active::before {
            width: 100%;
        }
        
        .admin-nav a:hover,
        .admin-nav a.active {
            background: rgba(212, 175, 55, 0.1);
            color: var(--color-primary);
            border-left-color: var(--color-primary);
            transform: translateX(4px);
        }
        
        .admin-nav-icon {
            width: 20px;
            height: 20px;
            fill: currentColor;
            opacity: 0.8;
        }
        
        .admin-content {
            flex: 1;
            margin-left: 280px;
            padding: 2.5rem;
            background: transparent;
        }
        
        .admin-header {
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .admin-header h1 {
            font-family: "Playfair Display", serif;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: var(--color-text-main);
            font-weight: 700;
            background: linear-gradient(135deg, #0B2545 0%, #1a3a5f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .admin-header p {
            color: rgba(27, 27, 27, 0.65);
            font-size: 1.05rem;
        }
        
        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }
        
        .admin-stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .admin-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--color-primary), var(--color-primary-dark));
        }
        
        .admin-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
        }
        
        .admin-stat-card h3 {
            font-size: 0.9rem;
            color: rgba(27, 27, 27, 0.6);
            margin: 0 0 0.5rem 0;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .admin-stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-bg-dark);
            margin: 0;
        }
        
        .admin-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .admin-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--color-primary), var(--color-primary-dark));
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .admin-card:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        }
        
        .admin-card:hover::before {
            opacity: 1;
        }
        
        .admin-card h3 {
            font-family: "Playfair Display", serif;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--color-text-main);
            font-weight: 600;
        }
        
        .admin-alert {
            padding: 1.2rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            animation: slideIn 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .admin-alert.success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: 1px solid #b8dacc;
        }
        
        .admin-alert.error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 1px solid #f1b0b7;
        }
        
        .admin-table-wrapper {
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        }
        
        .admin-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.95rem;
            background: #ffffff;
        }
        
        .admin-table thead {
            background: linear-gradient(135deg, #0B2545 0%, #1a3a5f 100%);
            color: var(--color-text-light);
        }
        
        .admin-table thead th {
            padding: 1.2rem 1rem;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        .admin-table thead th:first-child {
            border-top-left-radius: 12px;
        }
        
        .admin-table thead th:last-child {
            border-top-right-radius: 12px;
        }
        
        .admin-table tbody tr {
            transition: all 0.2s ease;
        }
        
        .admin-table tbody tr:hover {
            background: linear-gradient(90deg, #F6F5F3 0%, #ffffff 100%);
            transform: scale(1.01);
        }
        
        .admin-table td {
            padding: 1.2rem 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            vertical-align: middle;
        }
        
        .admin-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .admin-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.4rem 0.9rem;
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(11, 37, 69, 0.1) 0%, rgba(11, 37, 69, 0.15) 100%);
            color: var(--color-bg-dark);
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        
        .btn-delete:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220, 53, 69, 0.4);
        }
        
        .btn-delete:active {
            transform: translateY(0);
        }
        
        .admin-form {
            display: grid;
            gap: 1.5rem;
        }
        
        .admin-form-group {
            display: flex;
            gap: 0.75rem;
            align-items: flex-end;
        }
        
        .admin-form-group input {
            flex: 1;
        }
        
        .admin-form .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .admin-form .form-group label {
            font-weight: 600;
            color: var(--color-text-main);
            font-size: 0.9rem;
        }
        
        .admin-form .form-group input[type="file"],
        .admin-form .form-group select {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            border: 2px solid rgba(0, 0, 0, 0.1);
            background: #ffffff;
            color: var(--color-text-main);
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.3s ease;
        }
        
        .admin-form .form-group input[type="file"]:focus,
        .admin-form .form-group select:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }
        
        .admin-form .form-group select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%230B2545' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
        }
        
        .gallery-grid-admin {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .gallery-item-admin {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            border: 2px solid rgba(0, 0, 0, 0.08);
            background: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        .gallery-item-admin:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
            border-color: var(--color-primary);
        }
        
        .gallery-item-admin img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
        }
        
        .gallery-item-admin:hover img {
            transform: scale(1.1);
        }
        
        .gallery-item-admin .gallery-item-info {
            padding: 1rem;
            background: #ffffff;
        }
        
        .gallery-item-admin .gallery-item-info .category-badge {
            display: inline-block;
            padding: 0.3rem 0.7rem;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(212, 175, 55, 0.25) 100%);
            color: var(--color-bg-dark);
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        
        .gallery-item-admin .btn-delete {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            padding: 0.5rem;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            line-height: 1;
        }
        
        .category-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            background: linear-gradient(90deg, #F6F5F3 0%, #ffffff 100%);
            border-radius: 12px;
            margin-bottom: 0.75rem;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .category-item:hover {
            border-color: var(--color-primary);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .category-item strong {
            font-size: 1.1rem;
            color: var(--color-text-main);
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                width: 260px;
            }
            
            .admin-sidebar.open {
                transform: translateX(0);
            }
            
            .admin-content {
                margin-left: 0;
                padding: 1.5rem;
            }
            
            .admin-stats {
                grid-template-columns: 1fr;
            }
            
            .admin-table {
                font-size: 0.85rem;
            }
            
            .admin-table th,
            .admin-table td {
                padding: 0.75rem 0.5rem;
            }
            
            .gallery-grid-admin {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 1rem;
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
            <ul class="nav-links">
                <li><a href="index.php#home">Retour au site</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="admin-dashboard">
    <aside class="admin-sidebar">
        <h2>Administration</h2>
        <ul class="admin-nav">
            <li>
                <a href="?section=reservations" class="<?php echo $activeSection === 'reservations' ? 'active' : ''; ?>">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>
                    </svg>
                    Réservations
                </a>
            </li>
            <li>
                <a href="?section=gallery" class="<?php echo $activeSection === 'gallery' ? 'active' : ''; ?>">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <path d="M21 15l-5-5L5 21"/>
                    </svg>
                    Galerie
                </a>
            </li>
            <li>
                <a href="?section=categories" class="<?php echo $activeSection === 'categories' ? 'active' : ''; ?>">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                    Catégories
                </a>
            </li>
        </ul>
    </aside>

    <main class="admin-content">
        <?php if ($message): ?>
            <div class="admin-alert success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="admin-alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Section Réservations -->
        <?php if ($activeSection === 'reservations'): ?>
            <div class="admin-header">
                <div>
                    <h1>Réservations récentes</h1>
                    <p>Gestion des demandes de réservation</p>
                </div>
            </div>

            <div class="admin-stats">
                <div class="admin-stat-card">
                    <h3>Total Réservations</h3>
                    <p class="stat-value"><?php echo count($reservations); ?></p>
                </div>
                <div class="admin-stat-card">
                    <h3>Ce mois</h3>
                    <p class="stat-value"><?php 
                        $thisMonth = array_filter($reservations, function($r) {
                            return date('Y-m', strtotime($r['created_at'])) === date('Y-m');
                        });
                        echo count($thisMonth);
                    ?></p>
                </div>
                <div class="admin-stat-card">
                    <h3>En attente</h3>
                    <p class="stat-value"><?php echo count($reservations); ?></p>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-table-wrapper">
                    <?php if (empty($reservations)): ?>
                        <p>Aucune réservation pour le moment.</p>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom complet</th>
                                    <th>Téléphone</th>
                                    <th>Email</th>
                                    <th>Type d'événement</th>
                                    <th>Nombre d'invités</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $res): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($res['id']); ?></td>
                                        <td><?php echo htmlspecialchars($res['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($res['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($res['email']); ?></td>
                                        <td><span class="admin-badge"><?php echo htmlspecialchars($res['event_type']); ?></span></td>
                                        <td><?php echo isset($res['number_of_guests']) && $res['number_of_guests'] > 0 ? htmlspecialchars($res['number_of_guests']) : '-'; ?></td>
                                        <td style="max-width: 300px; white-space: pre-wrap;"><?php echo htmlspecialchars($res['message']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($res['created_at'])); ?></td>
                                        <td>
                                            <a href="?section=reservations&action=delete_reservation&id=<?php echo $res['id']; ?>" 
                                               class="btn-delete" 
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?')">Supprimer</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Section Galerie -->
        <?php if ($activeSection === 'gallery'): ?>
            <div class="admin-header">
                <h1>Gestion de la galerie</h1>
                <p>Ajouter et supprimer des images</p>
            </div>

            <div class="admin-card">
                <h3>Ajouter de nouvelles images</h3>
                <form method="POST" enctype="multipart/form-data" class="admin-form">
                    <div class="form-group">
                        <label for="images">Sélectionner une ou plusieurs images (JPG, PNG, WEBP - Max 3MB par fichier)</label>
                        <input type="file" id="images" name="images[]" accept="image/jpeg,image/jpg,image/png,image/webp" multiple required>
                    </div>
                    <div class="form-group">
                        <label for="category_id">Catégorie appliquée à toutes les images (optionnel)</label>
                        <select id="category_id" name="category_id" class="form-group input">
                            <option value="">-- Aucune catégorie --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="upload_image" class="btn btn-primary">Uploader les images</button>
                </form>
            </div>

            <div class="admin-card">
                <h3 style="margin-bottom: 1rem;">Images de la galerie (<?php echo count($gallery); ?>)</h3>
                <?php if (empty($gallery)): ?>
                    <p>Aucune image dans la galerie.</p>
                <?php else: ?>
                    <div class="gallery-grid-admin">
                        <?php foreach ($gallery as $img): ?>
                            <div class="gallery-item-admin">
                                <img src="<?php echo htmlspecialchars($img['image_path']); ?>" alt="Galerie">
                                <div class="gallery-item-info">
                                    <form method="POST" style="display:flex; flex-direction: column; gap:0.5rem;">
                                        <input type="hidden" name="image_id" value="<?php echo $img['id']; ?>">
                                        <label style="font-size:0.8rem; color:rgba(27,27,27,0.6);">Catégorie</label>
                                        <select name="category_id" style="width:100%; padding:0.45rem 0.7rem; border-radius:8px; border:1px solid rgba(0,0,0,0.15); font-size:0.85rem;">
                                            <option value="">Aucune catégorie</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?php echo $cat['id']; ?>" <?php echo (!empty($img['category_name']) && $img['category_name'] === $cat['name']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($cat['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" name="update_image_category" class="btn btn-primary" style="padding:0.35rem 0.7rem; font-size:0.8rem; align-self:flex-start;">Enregistrer</button>
                                    </form>
                                </div>
                                <a href="?section=gallery&action=delete_image&id=<?php echo $img['id']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Supprimer cette image ?')" 
                                   title="Supprimer">×</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Section Catégories -->
        <?php if ($activeSection === 'categories'): ?>
            <div class="admin-header">
                <h1>Gestion des catégories</h1>
                <p>Ajouter et supprimer des catégories d'événements</p>
            </div>

            <div class="admin-stats">
                <div class="admin-stat-card">
                    <h3>Total Catégories</h3>
                    <p class="stat-value"><?php echo count($categories); ?></p>
                </div>
            </div>

            <div class="admin-card">
                <h3>Ajouter une nouvelle catégorie</h3>
                <form method="POST" class="admin-form">
                    <div class="admin-form-group">
                        <input type="text" name="category_name" placeholder="Nom de la catégorie" required>
                        <button type="submit" name="add_category" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>

            <div class="admin-card">
                <h3>Catégories existantes</h3>
                <?php if (empty($categories)): ?>
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 7h16M4 12h16M4 17h16"/>
                        </svg>
                        <h3>Aucune catégorie</h3>
                        <p>Ajoutez votre première catégorie ci-dessus</p>
                    </div>
                <?php else: ?>
                    <div style="margin-top: 1.5rem;">
                        <?php foreach ($categories as $cat): ?>
                            <div class="category-item">
                                <div>
                                    <strong><?php echo htmlspecialchars($cat['name']); ?></strong>
                                    <span style="display: block; font-size: 0.85rem; color: rgba(27, 27, 27, 0.5); margin-top: 0.25rem;">
                                        Créée le <?php echo date('d/m/Y', strtotime($cat['created_at'])); ?>
                                    </span>
                                </div>
                                <a href="?section=categories&action=delete_category&id=<?php echo $cat['id']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Supprimer cette catégorie ?')">Supprimer</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
// Menu mobile pour sidebar
if (window.innerWidth <= 768) {
    const sidebar = document.querySelector('.admin-sidebar');
    const toggleBtn = document.createElement('button');
    toggleBtn.textContent = '☰ Menu';
    toggleBtn.className = 'btn btn-primary';
    toggleBtn.style.position = 'fixed';
    toggleBtn.style.top = '80px';
    toggleBtn.style.left = '10px';
    toggleBtn.style.zIndex = '101';
    toggleBtn.onclick = () => sidebar.classList.toggle('open');
    document.body.appendChild(toggleBtn);
}
</script>
</body>
</html>
