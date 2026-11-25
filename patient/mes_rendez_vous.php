<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/fonctions.php';
require_role('patient');
$titre_page = 'Mes RDV';
$sql = "SELECT r.*, u.nom, u.prenom, s.libelle as specialite FROM rendez_vous r JOIN medecins m ON r.id_medecin = m.id JOIN utilisateurs u ON m.id_utilisateur = u.id JOIN specialites s ON m.id_specialite = s.id WHERE r.id_patient = :ip ORDER BY r.date_rdv DESC";
$stmt = $pdo->prepare($sql); $stmt->execute([':ip'=>$_SESSION['user_id']]); $rdvs = $stmt->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h2 class="mb-4 fw-bold text-primary">Mes rendez-vous</h2>
<div class="card shadow-sm">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Date</th><th>Médecin</th><th>Motif</th><th>Statut</th></tr></thead>
        <tbody><?php foreach ($rdvs as $rdv): ?><tr>
            <td><?= format_date_fr($rdv['date_rdv']) ?> à <?= substr($rdv['heure_rdv'],0,5) ?></td>
            <td>Dr. <?= e($rdv['prenom'].' '.$rdv['nom']) ?> (<?= e($rdv['specialite']) ?>)</td>
            <td><?= e($rdv['motif']) ?></td>
            <td><span class="badge bg-<?= $rdv['statut']==='confirme'?'success':($rdv['statut']==='en_attente'?'warning':'danger') ?>"><?= STATUTS_RDV[$rdv['statut']] ?></span></td>
        </tr><?php endforeach; ?></tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>