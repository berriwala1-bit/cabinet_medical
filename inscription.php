<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/fonctions.php';
if (est_connecte()) redirect('index.php');
$titre_page = 'Inscription';
$erreurs = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? ''); $prenom = trim($_POST['prenom'] ?? ''); $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? ''); $mdp = $_POST['mot_de_passe'] ?? ''; $mdp2 = $_POST['mot_de_passe_confirm'] ?? '';
    if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) || empty($mdp)) $erreurs[] = "Tous les champs sont obligatoires.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs[] = "Format d'e-mail invalide.";
    if ($mdp !== $mdp2) $erreurs[] = "Les mots de passe ne correspondent pas.";
    if (strlen($mdp) < 8) $erreurs[] = "Le mot de passe doit faire 8 caractères.";
    if (empty($erreurs)) {
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :e"); $stmt->execute([':e'=>$email]);
        if ($stmt->fetch()) $erreurs[] = "Cet e-mail est déjà utilisé.";
    }
    if (empty($erreurs)) {
        $hash = password_hash($mdp, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role) VALUES (:n, :p, :e, :t, :m, 'patient')");
        $stmt->execute([':n'=>$nom, ':p'=>$prenom, ':e'=>$email, ':t'=>$telephone, ':m'=>$hash]);
        set_flash('success', 'Compte créé avec succès.'); redirect('login.php');
    }
}
include __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm mt-4">
            <div class="card-body p-5">
                <h2 class="text-center text-primary fw-bold mb-4">Inscription</h2>
                <?php if (!empty($erreurs)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($erreurs as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Nom</label><input type="text" name="nom" class="form-control" required value="<?= e($_POST['nom'] ?? '') ?>"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Prénom</label><input type="text" name="prenom" class="form-control" required value="<?= e($_POST['prenom'] ?? '') ?>"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>"></div>
                    <div class="mb-3"><label class="form-label">Téléphone</label><input type="tel" name="telephone" class="form-control" required value="<?= e($_POST['telephone'] ?? '') ?>"></div>
                    <div class="mb-3"><label class="form-label">Mot de passe</label><input type="password" name="mot_de_passe" class="form-control" required minlength="8"></div>
                    <div class="mb-4"><label class="form-label">Confirmer</label><input type="password" name="mot_de_passe_confirm" class="form-control" required minlength="8"></div>
                    <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>