<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/fonctions.php';
require_role('administrateur');
$titre_page = 'Nouveau médecin';
$erreurs = [];
$specialites = $pdo->query("SELECT * FROM specialites ORDER BY libelle")->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? ''); $prenom = trim($_POST['prenom'] ?? ''); $email = trim($_POST['email'] ?? '');
    $tel = trim($_POST['telephone'] ?? ''); $id_spec = (int)($_POST['id_specialite'] ?? 0); $desc = trim($_POST['description'] ?? ''); $mdp = $_POST['mot_de_passe'] ?? '';
    if (empty($nom) || empty($prenom) || empty($email) || empty($mdp) || empty($id_spec)) $erreurs[] = "Champs obligatoires manquants.";
    if (empty($erreurs)) {
        try {
            $pdo->beginTransaction();
            $hash = password_hash($mdp, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role) VALUES (:n, :p, :e, :t, :m, 'medecin')");
            $stmt->execute([':n'=>$nom, ':p'=>$prenom, ':e'=>$email, ':t'=>$tel, ':m'=>$hash]);
            $id_user = $pdo->lastInsertId();
            $pdo->prepare("INSERT INTO medecins (id_utilisateur, id_specialite, description, actif) VALUES (:iu, :is, :d, 1)")->execute([':iu'=>$id_user, ':is'=>$id_spec, ':d'=>$desc]);
            $pdo->commit(); set_flash('success', 'Médecin ajouté avec succès.'); redirect('index.php');
        } catch (Exception $e) { $pdo->rollBack(); $erreurs[] = "Erreur lors de l'ajout."; }
    }
}
include __DIR__ . '/../../includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-8"><div class="card shadow-sm mt-4"><div class="card-body p-5">
    <h2 class="fw-bold text-primary mb-4">Ajouter un médecin</h2>
    <?php if (!empty($erreurs)): ?><div class="alert alert-danger"><?= implode('<br>', array_map('e', $erreurs)) ?></div><?php endif; ?>
    <form method="POST">
        <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Nom</label><input type="text" name="nom" class="form-control" required></div><div class="col-md-6 mb-3"><label class="form-label">Prénom</label><input type="text" name="prenom" class="form-control" required></div></div>
        <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div><div class="col-md-6 mb-3"><label class="form-label">Téléphone</label><input type="tel" name="telephone" class="form-control"></div></div>
        <div class="mb-3"><label class="form-label">Spécialité</label><select name="id_specialite" class="form-select" required><option value="">-- Choisir --</option><?php foreach ($specialites as $s): ?><option value="<?= $s['id'] ?>"><?= e($s['libelle']) ?></option><?php endforeach; ?></select></div>
        <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
        <div class="mb-4"><label class="form-label">Mot de passe</label><input type="password" name="mot_de_passe" class="form-control" required minlength="8"></div>
        <button type="submit" class="btn btn-primary w-100">Créer le médecin</button>
    </form>
</div></div></div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>