<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/fonctions.php';
require_role('patient');
$titre_page = 'Mon Profil';
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id"); $stmt->execute([':id'=>$_SESSION['user_id']]); $user = $stmt->fetch();
$erreurs = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']); $prenom = trim($_POST['prenom']); $tel = trim($_POST['telephone']); $mdp = $_POST['nouveau_mdp'] ?? '';
    if (empty($nom) || empty($prenom)) $erreurs[] = "Nom et prénom requis.";
    if (empty($erreurs)) {
        if (!empty($mdp)) {
            if (strlen($mdp) < 8) $erreurs[] = "Le mot de passe doit faire 8 caractères.";
            else {
                $hash = password_hash($mdp, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE utilisateurs SET nom=:n, prenom=:p, telephone=:t, mot_de_passe=:m WHERE id=:id")->execute([':n'=>$nom, ':p'=>$prenom, ':t'=>$tel, ':m'=>$hash, ':id'=>$_SESSION['user_id']]);
                set_flash('success', 'Profil mis à jour.'); redirect('profil.php');
            }
        } else {
            $pdo->prepare("UPDATE utilisateurs SET nom=:n, prenom=:p, telephone=:t WHERE id=:id")->execute([':n'=>$nom, ':p'=>$prenom, ':t'=>$tel, ':id'=>$_SESSION['user_id']]);
            set_flash('success', 'Profil mis à jour.'); redirect('profil.php');
        }
    }
}
include __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-8"><div class="card shadow-sm p-4 mt-4">
    <h2 class="mb-4">Mon Profil</h2>
    <?php if (!empty($erreurs)): ?><div class="alert alert-danger"><?= implode('<br>', array_map('e', $erreurs)) ?></div><?php endif; ?>
    <form method="POST">
        <div class="row"><div class="col-md-6 mb-3"><label>Nom</label><input type="text" name="nom" class="form-control" required value="<?= e($user['nom']) ?>"></div><div class="col-md-6 mb-3"><label>Prénom</label><input type="text" name="prenom" class="form-control" required value="<?= e($user['prenom']) ?>"></div></div>
        <div class="mb-3"><label>Email (non modifiable)</label><input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled></div>
        <div class="mb-3"><label>Téléphone</label><input type="tel" name="telephone" class="form-control" value="<?= e($user['telephone']) ?>"></div>
        <hr class="my-4"><h5 class="text-muted mb-3">Changer de mot de passe</h5>
        <div class="mb-4"><label>Nouveau mot de passe (optionnel)</label><input type="password" name="nouveau_mdp" class="form-control" minlength="8"></div>
        <button type="submit" class="btn btn-primary w-100">Enregistrer</button>
    </form>
</div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>