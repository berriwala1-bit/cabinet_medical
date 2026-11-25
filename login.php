<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/fonctions.php';
if (est_connecte()) redirect('index.php');
$titre_page = 'Connexion';
$erreurs = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? ''); $mdp = $_POST['mot_de_passe'] ?? '';
    if (empty($email) || empty($mdp)) { $erreurs[] = "Champs vides."; } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :e"); $stmt->execute([':e'=>$email]); $user = $stmt->fetch();
        if ($user && password_verify($mdp, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id']; $_SESSION['nom'] = $user['nom']; $_SESSION['prenom'] = $user['prenom']; $_SESSION['role'] = $user['role'];
            set_flash('success', 'Bienvenue ' . $user['prenom'] . ' !');
            if ($user['role'] === 'administrateur') redirect('admin/dashboard.php');
            elseif ($user['role'] === 'medecin') redirect('medecin/planning.php');
            else redirect('patient/prendre_rdv.php');
        } else { $erreurs[] = "Identifiants incorrects."; }
    }
}
include __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm mt-5">
            <div class="card-body p-5">
                <h2 class="text-center text-primary fw-bold mb-4">Connexion</h2>
                <?php if (!empty($erreurs)): ?>
                    <div class="alert alert-danger"><?= implode('<br>', array_map('e', $erreurs)) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" name="mot_de_passe" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>