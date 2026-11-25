<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/fonctions.php';
require_role('administrateur');
$titre_page = 'Spécialités';
$specialites = $pdo->query("SELECT * FROM specialites ORDER BY libelle")->fetchAll();
include __DIR__ . '/../../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Spécialités</h2>
    <a href="creer.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nouvelle</a>
</div>
<div class="card shadow-sm">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Libellé</th><th>Description</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
            <?php foreach ($specialites as $spec): ?>
            <tr>
                <td class="fw-bold"><?= e($spec['libelle']) ?></td>
                <td><?= e($spec['description']) ?></td>
                <td class="text-end">
                    <a href="modifier.php?id=<?= $spec['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                    <a href="supprimer.php?id=<?= $spec['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>