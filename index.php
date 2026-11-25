<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/fonctions.php';
$titre_page = 'Accueil';
$specialites = $pdo->query("SELECT * FROM specialites ORDER BY libelle")->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<div class="p-5 mb-5 bg-white rounded-3 shadow-sm text-center border-top border-primary border-5">
    <h1 class="display-5 fw-bold text-primary mb-3">Bienvenue au Cabinet Médical</h1>
    <p class="fs-5 text-muted mb-4">Prenez rendez-vous en ligne facilement avec nos médecins spécialistes.</p>
    <?php if (!est_connecte()): ?><a href="inscription.php" class="btn btn-primary btn-lg px-4">Créer un compte</a><?php else: ?><a href="<?= APP_URL ?>/patient/prendre_rdv.php" class="btn btn-primary btn-lg px-4">Prendre un rendez-vous</a><?php endif; ?>
</div>
<h2 class="text-center mb-4">Nos Spécialités</h2>
<div class="row g-4">
    <?php foreach ($specialites as $spec): ?>
    <div class="col-md-4"><div class="card h-100 shadow-sm text-center p-3"><div class="card-body"><i class="bi bi-clipboard2-pulse text-primary fs-1 mb-3"></i><h5 class="fw-bold"><?= e($spec['libelle']) ?></h5><p class="text-muted"><?= e($spec['description']) ?></p></div></div></div>
    <?php endforeach; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>