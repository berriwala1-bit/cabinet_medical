<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/fonctions.php';
require_role('medecin');
$titre_page = 'Mon Planning';
$stmt = $pdo->prepare("SELECT id FROM medecins WHERE id_utilisateur = :iu"); $stmt->execute([':iu'=>$_SESSION['user_id']]); $med = $stmt->fetch();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_rdv'])) {
    $pdo->prepare("UPDATE rendez_vous SET statut = :s WHERE id = :id AND id_medecin = :im")->execute([':s'=>$_POST['statut'], ':id'=>$_POST['id_rdv'], ':im'=>$med['id']]);
    set_flash('success', 'Statut mis à jour.'); redirect('planning.php');
}
$sql = "SELECT r.*, u.nom, u.prenom FROM rendez_vous r JOIN utilisateurs u ON r.id_patient = u.id WHERE r.id_medecin = :im AND r.date_rdv >= CURDATE() ORDER BY r.date_rdv, r.heure_rdv";
$stmt = $pdo->prepare($sql); $stmt->execute([':im'=>$med['id']]); $rdvs = $stmt->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h2 class="mb-4 fw-bold text-primary">Mon Planning</h2>
<div class="card shadow-sm">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Date</th><th>Patient</th><th>Motif</th><th>Statut</th><th>Actions</th></tr></thead>
        <tbody><?php foreach ($rdvs as $rdv): ?><tr>
            <td><?= format_date_fr($rdv['date_rdv']) ?> à <?= substr($rdv['heure_rdv'],0,5) ?></td>
            <td><?= e($rdv['prenom'].' '.$rdv['nom']) ?></td>
            <td><?= e($rdv['motif']) ?></td>
            <td><span class="badge bg-<?= $rdv['statut']==='confirme'?'success':'secondary' ?>"><?= STATUTS_RDV[$rdv['statut']] ?></span></td>
            <td><?php if ($rdv['statut'] === 'confirme'): ?><form method="POST" class="d-inline"><input type="hidden" name="id_rdv" value="<?= $rdv['id'] ?>"><button type="submit" name="statut" value="termine" class="btn btn-sm btn-outline-success">Terminé</button><button type="submit" name="statut" value="absent" class="btn btn-sm btn-outline-danger">Absent</button></form><?php endif; ?></td>
        </tr><?php endforeach; ?></tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>