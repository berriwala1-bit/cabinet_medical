<?php
require_once __DIR__ . '/fonctions.php';
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - <?= e($titre_page ?? 'Accueil') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="<?= APP_URL ?>/index.php">
            <i class="bi bi-heart-pulse-fill me-2"></i><?= APP_NAME ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (est_connecte()): ?>
                    <?php if ($_SESSION['role'] === 'administrateur'): ?>
                        <li class="nav-item"><a class="nav-link nav-link-custom" href="<?= APP_URL ?>/admin/dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link nav-link-custom" href="<?= APP_URL ?>/admin/medecins/index.php">Médecins</a></li>
                        <li class="nav-item"><a class="nav-link nav-link-custom" href="<?= APP_URL ?>/admin/specialites/index.php">Spécialités</a></li>
                        <li class="nav-item"><a class="nav-link nav-link-custom" href="<?= APP_URL ?>/admin/rendez_vous/index.php">Rendez-vous</a></li>
                    <?php elseif ($_SESSION['role'] === 'medecin'): ?>
                        <li class="nav-item"><a class="nav-link nav-link-custom" href="<?= APP_URL ?>/medecin/planning.php">Mon Planning</a></li>
                    <?php elseif ($_SESSION['role'] === 'patient'): ?>
                        <li class="nav-item"><a class="nav-link nav-link-custom" href="<?= APP_URL ?>/patient/prendre_rdv.php">Prendre RDV</a></li>
                        <li class="nav-item"><a class="nav-link nav-link-custom" href="<?= APP_URL ?>/patient/mes_rendez_vous.php">Mes RDV</a></li>
                        <li class="nav-item"><a class="nav-link nav-link-custom" href="<?= APP_URL ?>/patient/profil.php">Profil</a></li>
                    <?php endif; ?>
                    <li class="nav-item ms-lg-3"><a class="btn btn-danger btn-sm mt-1" href="<?= APP_URL ?>/logout.php">Déconnexion</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="<?= APP_URL ?>/index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="<?= APP_URL ?>/login.php">Connexion</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="<?= APP_URL ?>/inscription.php">S'inscrire</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <?php if ($flash = get_flash()): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
</div>

<main class="container my-4">