<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/fonctions.php';
require_role('patient');
$titre_page = 'Prendre RDV';
$erreurs = [];
$medecins = $pdo->query("SELECT m.id, u.nom, u.prenom, s.libelle as specialite FROM medecins m JOIN utilisateurs u ON m.id_utilisateur = u.id JOIN specialites s ON m.id_specialite = s.id WHERE m.actif = 1 ORDER BY u.nom")->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_med = (int)$_POST['id_medecin']; $date = $_POST['date_rdv']; $heure = $_POST['heure_rdv']; $motif = trim($_POST['motif']);
    if (empty($id_med) || empty($date) || empty($heure) || empty($motif)) $erreurs[] = "Champs obligatoires.";
    if (empty($erreurs) && !creneau_disponible($pdo, $id_med, $date, $heure)) $erreurs[] = "Ce créneau est déjà réservé.";
    if (empty($erreurs)) {
        $pdo->prepare("INSERT INTO rendez_vous (id_patient, id_medecin, date_rdv, heure_rdv, motif, statut) VALUES (:ip, :im, :d, :h, :m, 'en_attente')")->execute([':ip'=>$_SESSION['user_id'], ':im'=>$id_med, ':d'=>$date, ':h'=>$heure, ':m'=>$motif]);
        set_flash('success', 'Demande envoyée.'); redirect('mes_rendez_vous.php');
    }
}
include __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm mt-4">
            <div class="card-body p-5">
                <h2 class="text-primary fw-bold mb-4">Prendre un rendez-vous</h2>
                <?php if (!empty($erreurs)): ?><div class="alert alert-danger"><?= implode('<br>', array_map('e', $erreurs)) ?></div><?php endif; ?>
                <form method="POST">
                    <div class="mb-3"><label class="form-label">Médecin</label><select name="id_medecin" class="form-select" required><option value="">-- Choisir --</option><?php foreach ($medecins as $m): ?><option value="<?= $m['id'] ?>">Dr. <?= e($m['prenom'].' '.$m['nom']) ?> (<?= e($m['specialite']) ?>)</option><?php endforeach; ?></select></div>
                    <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Date</label><input type="date" name="date_rdv" class="form-control" required min="<?= date('Y-m-d') ?>"></div><div class="col-md-6 mb-3"><label class="form-label">Heure</label><input type="time" name="heure_rdv" class="form-control" required></div></div>
                    <div class="mb-4"><label class="form-label">Motif</label><textarea name="motif" class="form-control" rows="3" required></textarea></div>
                    <button type="submit" class="btn btn-primary w-100">Envoyer la demande</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>