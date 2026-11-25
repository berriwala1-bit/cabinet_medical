<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/fonctions.php';
require_role('administrateur');
$titre_page = 'Rendez-vous';

$sql = "SELECT r.*, p.nom as p_nom, p.prenom as p_prenom, m_u.nom as m_nom, m_u.prenom as m_prenom FROM rendez_vous r JOIN utilisateurs p ON r.id_patient = p.id JOIN medecins m ON r.id_medecin = m.id JOIN utilisateurs m_u ON m.id_utilisateur = m_u.id WHERE 1=1";
$params = [];
if (!empty($_GET['date'])) { $sql .= " AND r.date_rdv = :d"; $params[':d'] = $_GET['date']; }
if (!empty($_GET['statut'])) { $sql .= " AND r.statut = :s"; $params[':s'] = $_GET['statut']; }
$sql .= " ORDER BY r.date_rdv DESC, r.heure_rdv DESC";
$stmt = $pdo->prepare($sql); $stmt->execute($params); $rdvs = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>
<h2 class="fw-bold text-primary mb-4">Tous les rendez-vous</h2>
<form method="GET" class="card shadow-sm border-0 p-4 mb-4 bg-white">
    <div class="row align-items-end">
        <div class="col-md-4"><label class="form-label">Date</label><input type="date" name="date" class="form-control" value="<?= e($_GET['date'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Statut</label><select name="statut" class="form-select"><option value="">Tous les statuts</option><?php foreach (STATUTS_RDV as $k=>$v): ?><option value="<?= $k ?>" <?= (($_GET['statut'] ?? '') === $k) ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4"><button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-2"></i>Filtrer</button></div>
    </div>
</form>
<div class="card shadow-sm border-0">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Date & Heure</th><th>Patient</th><th>Médecin</th><th>Motif</th><th>Statut</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
            <?php foreach ($rdvs as $rdv): ?>
            <tr>
                <td><span class="fw-bold"><?= format_date_fr($rdv['date_rdv']) ?></span><br><small class="text-muted">à <?= substr($rdv['heure_rdv'],0,5) ?></small></td>
                <td><?= e($rdv['p_prenom'].' '.$rdv['p_nom']) ?></td>
                <td>Dr. <?= e($rdv['m_prenom'].' '.$rdv['m_nom']) ?></td>
                <td><?= e($rdv['motif']) ?></td>
                <td><span class="badge bg-<?= $rdv['statut']==='confirme'?'success':($rdv['statut']==='en_attente'?'warning':'danger') ?>"><?= STATUTS_RDV[$rdv['statut']] ?></span></td>
                <td class="text-end">
                    <?php if ($rdv['statut'] === 'en_attente'): ?>
                        <a href="traiter.php?id=<?= $rdv['id'] ?>&action=confirmer" class="btn btn-sm btn-success" title="Confirmer"><i class="bi bi-check-lg"></i> Confirmer</a>
                        <a href="traiter.php?id=<?= $rdv['id'] ?>&action=refuser" class="btn btn-sm btn-danger" title="Refuser"><i class="bi bi-x-lg"></i> Refuser</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($rdvs)): ?><tr><td colspan="6" class="text-center text-muted py-4">Aucun rendez-vous trouvé.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>