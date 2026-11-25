<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/fonctions.php';
require_role('administrateur');
$titre_page = 'Nouvelle spécialité';
$erreurs = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libelle = trim($_POST['libelle'] ?? ''); $description = trim($_POST['description'] ?? '');
    if (empty($libelle)) $erreurs[] = "Le libellé est obligatoire.";
    if (empty($erreurs)) {
        $pdo->prepare("INSERT INTO specialites (libelle, description) VALUES (:l, :d)")->execute([':l'=>$libelle, ':d'=>$description]);
        set_flash('success', 'Spécialité ajoutée.'); redirect('index.php');
    }
}
include __DIR__ . '/../../includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-6"><div class="card shadow-sm mt-4"><div class="card-body p-5">
    <h2 class="fw-bold text-primary mb-4">Ajouter une spécialité</h2>
    <?php if (!empty($erreurs)): ?><div class="alert alert-danger"><?= implode('<br>', array_map('e', $erreurs)) ?></div><?php endif; ?>
    <form method="POST">
        <div class="mb-3"><label class="form-label">Libellé</label><input type="text" name="libelle" class="form-control" required></div>
        <div class="mb-4"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
        <button type="submit" class="btn btn-primary w-100">Enregistrer</button>
    </form>
</div></div></div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>