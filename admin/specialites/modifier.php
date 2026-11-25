<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/fonctions.php';
require_role('administrateur');
$titre_page = 'Modifier spécialité';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM specialites WHERE id = :id"); $stmt->execute([':id'=>$id]); $spec = $stmt->fetch();
if (!$spec) redirect('index.php');
$erreurs = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libelle = trim($_POST['libelle'] ?? ''); $description = trim($_POST['description'] ?? '');
    if (empty($libelle)) $erreurs[] = "Le libellé est obligatoire.";
    if (empty($erreurs)) {
        $pdo->prepare("UPDATE specialites SET libelle=:l, description=:d WHERE id=:id")->execute([':l'=>$libelle, ':d'=>$description, ':id'=>$id]);
        set_flash('success', 'Spécialité modifiée.'); redirect('index.php');
    }
}
include __DIR__ . '/../../includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-6"><div class="card shadow-sm mt-4"><div class="card-body p-5">
    <h2 class="fw-bold text-primary mb-4">Modifier la spécialité</h2>
    <?php if (!empty($erreurs)): ?><div class="alert alert-danger"><?= implode('<br>', array_map('e', $erreurs)) ?></div><?php endif; ?>
    <form method="POST">
        <div class="mb-3"><label class="form-label">Libellé</label><input type="text" name="libelle" class="form-control" required value="<?= e($spec['libelle']) ?>"></div>
        <div class="mb-4"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"><?= e($spec['description']) ?></textarea></div>
        <button type="submit" class="btn btn-warning w-100">Mettre à jour</button>
    </form>
</div></div></div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>