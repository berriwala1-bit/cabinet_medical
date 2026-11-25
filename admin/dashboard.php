<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/fonctions.php';
require_role('administrateur');
$titre_page = 'Dashboard Admin';

$nb_patients = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'patient'")->fetchColumn();
$nb_medecins = $pdo->query("SELECT COUNT(*) FROM medecins WHERE actif = 1")->fetchColumn();
$nb_attente = $pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE statut = 'en_attente'")->fetchColumn();
$nb_confirmes = $pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE statut = 'confirme'")->fetchColumn();
$nb_rdv_jour = $pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE date_rdv = CURDATE()")->fetchColumn();

$sql = "SELECT r.id, r.date_rdv, r.heure_rdv, p.nom as p_nom, p.prenom as p_prenom, m_u.nom as m_nom 
        FROM rendez_vous r JOIN utilisateurs p ON r.id_patient = p.id JOIN medecins m ON r.id_medecin = m.id 
        JOIN utilisateurs m_u ON m.id_utilisateur = m_u.id WHERE r.statut = 'en_attente' ORDER BY r.date_creation DESC LIMIT 5";
$dernieres = $pdo->query($sql)->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="bi bi-speedometer2 me-2"></i>Tableau de bord</h2>
    <span class="badge bg-light text-dark fs-6 p-2 shadow-sm">Bienvenue, <?= e($_SESSION['prenom']) ?> !</span>
</div>

<div class="row g-4 mb-5">
    <!-- Carte Patients (Bleu) -->
    <div class="col-md-4 col-lg-2">
        <div class="card card-stat text-center h-100 p-3 border-0" style="background-color: #0d6efd !important;">
            <i class="bi bi-people fs-1 mb-2"></i>
            <h3 class="mt-2 mb-0 fw-bold"><?= $nb_patients ?></h3>
            <small>Patients</small>
        </div>
    </div>
    
    <!-- Carte Médecins (Vert) -->
    <div class="col-md-4 col-lg-2">
        <div class="card card-stat text-center h-100 p-3 border-0" style="background-color: #198754 !important;">
            <i class="bi bi-heart-pulse fs-1 mb-2"></i>
            <h3 class="mt-2 mb-0 fw-bold"><?= $nb_medecins ?></h3>
            <small>Médecins</small>
        </div>
    </div>
    
    <!-- Carte En attente (Jaune avec texte noir) -->
    <div class="col-md-4 col-lg-2">
        <div class="card text-center h-100 p-3 border-0" style="background-color: #ffc107 !important; color: #212529 !important;">
            <i class="bi bi-hourglass-split fs-1 mb-2" style="color: #212529 !important;"></i>
            <h3 class="mt-2 mb-0 fw-bold" style="color: #212529 !important;"><?= $nb_attente ?></h3>
            <small style="color: #212529 !important;">En attente</small>
        </div>
    </div>
    
    <!-- Carte Confirmés (Cyan) -->
    <div class="col-md-4 col-lg-3">
        <div class="card card-stat text-center h-100 p-3 border-0" style="background-color: #0dcaf0 !important;">
            <i class="bi bi-check-circle fs-1 mb-2"></i>
            <h3 class="mt-2 mb-0 fw-bold"><?= $nb_confirmes ?></h3>
            <small>Confirmés</small>
        </div>
    </div>
    
    <!-- Carte RDV du jour (Noir) -->
    <div class="col-md-4 col-lg-3">
        <div class="card card-stat text-center h-100 p-3 border-0" style="background-color: #212529 !important;">
            <i class="bi bi-calendar-event fs-1 mb-2"></i>
            <h3 class="mt-2 mb-0 fw-bold"><?= $nb_rdv_jour ?></h3>
            <small>RDV aujourd'hui</small>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-clock-history me-2"></i>Dernières demandes</h5>
        <a href="<?= APP_URL ?>/admin/rendez_vous/index.php" class="btn btn-sm btn-outline-primary">Voir tout</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Médecin</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dernieres as $rdv): ?>
                <tr>
                    <td><?= format_date_fr($rdv['date_rdv']) ?> à <?= substr($rdv['heure_rdv'],0,5) ?></td>
                    <td><?= e($rdv['p_prenom'].' '.$rdv['p_nom']) ?></td>
                    <td>Dr. <?= e($rdv['m_nom']) ?></td>
                    <td class="text-end">
                        <a href="<?= APP_URL ?>/admin/rendez_vous/traiter.php?id=<?= $rdv['id'] ?>&action=confirmer" class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i></a>
                        <a href="<?= APP_URL ?>/admin/rendez_vous/traiter.php?id=<?= $rdv['id'] ?>&action=refuser" class="btn btn-sm btn-danger"><i class="bi bi-x-lg"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($dernieres)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">Aucune nouvelle demande pour le moment.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>