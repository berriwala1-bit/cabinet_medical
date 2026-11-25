<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/fonctions.php';
require_role('administrateur');
$titre_page = 'Modifier médecin';
$id = (int)($_GET['id'] ?? 0);
$sql = "SELECT m.*, u.nom, u.prenom, u.email, u.telephone FROM medecins m JOIN utilisateurs u ON m.id_utilisateur = u.id WHERE m.id = :id";
$stmt = $pdo->prepare($sql); $stmt->execute([':id'=>$id]); $med = $stmt->fetch();
if (!$med) redirect('index.php');
$specialites = $pdo->query("SELECT * FROM specialites ORDER BY libelle")->fetchAll();
$erreurs = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? ''); $prenom = trim($_POST['prenom'] ?? ''); $email = trim($_POST['email'] ?? '');
    $tel = trim($_POST['telephone'] ?? ''); $id_spec = (int)($_POST['id_specialite'] ?? 0); $desc = trim($_POST['description'] ?? ''); $actif = isset($_POST['actif']) ? 1 : 0;
    if (empty($nom) || empty($prenom) || empty($email)) $erreurs[] = "Champs obligatoires manquants.";
    if (empty($erreurs)) {
        $pdo->beginTransaction();
        try {
            $pdo->prepare("UPDATE utilisateurs SET nom=:n, prenom=:p, email=:e, telephone=:t WHERE id=:id")->execute([':n'=>$nom, ':p'=>$prenom, ':e'=>$email, ':t'=>$tel, ':id'=>$med['id_utilisateur']]);
            $pdo->prepare("UPDATE medecins SET id_specialite=:is, description=:d, actif=:a WHERE id=:id")->execute([':is'=>$id_spec, ':d'=>$desc, ':a'=>$actif, ':id'=>$id]);
            $pdo->commit(); set_flash('success', 'Médecin modifié.'); redirect('index.php');
        } catch (Exception $e) { $pdo->rollBack(); $erreurs[] = "Erreur."; }
    }
}
include __DIR__ . '/../../includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-8"><div class="card shadow-sm mt-4"><div class="card-body p-5">
    <h2 class="fw-bold text-primary mb-4">Modifier le médecin</h2>
    <?php if (!empty($erreurs)): ?><div class="alert alert-danger"><?= implode('<br>', array_map('e', $erreurs)) ?></div><?php endif; ?>
    <form method="POST">
        <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Nom</label><input type="text" name="nom" class="form-control" required value="<?= e($med['nom']) ?>"></div><div class="col-md-6 mb-3"><label class="form-label">Prénom</label><input type="text" name="prenom" class="form-control" required value="<?= e($med['prenom']) ?>"></div></div>
        <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required value="<?= e($med['email']) ?>"></div><div class="col-md-6 mb-3"><label class="form-label">Téléphone</label><input type="tel" name="telephone" class="form-control" value="<?= e($med['telephone']) ?>"></div></div>
        <div class="mb-3"><label class="form-label">Spécialité</label><select name="id_specialite" class="form-select" required><?php foreach ($specialites as $s): ?><option value="<?= $s['id'] ?>" <?= $s['id'] == $med['id_specialite'] ? 'selected' : '' ?>><?= e($s['libelle']) ?></option><?php endforeach; ?></select></div>
        <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"><?= e($med['description']) ?></textarea></div>
        <div class="mb-4 form-check"><input type="checkbox" name="actif" class="form-check-input" id="actif" <?= $med['actif'] ? 'checked' : '' ?>><label class="form-check-label" for="actif">Médecin actif (décocher pour désactiver)</label></div>
        <button type="submit" class="btn btn-warning w-100">Mettre à jour</button>
    </form>
</div></div></div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>