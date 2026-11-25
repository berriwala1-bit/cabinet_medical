<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/fonctions.php';
require_role('administrateur');
$titre_page = 'Disponibilités';
$id_med = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT u.nom, u.prenom FROM medecins m JOIN utilisateurs u ON m.id_utilisateur = u.id WHERE m.id = :id"); $stmt->execute([':id'=>$id_med]); $med = $stmt->fetch();
if (!$med) redirect('index.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $pdo->prepare("INSERT INTO disponibilites (id_medecin, jour_semaine, heure_debut, heure_fin) VALUES (:im, :j, :hd, :hf)")->execute([':im'=>$id_med, ':j'=>$_POST['jour'], ':hd'=>$_POST['debut'], ':hf'=>$_POST['fin']]);
    set_flash('success', 'Disponibilité ajoutée.'); redirect("disponibilites.php?id=$id_med");
}
if (isset($_GET['supprimer'])) {
    $pdo->prepare("DELETE FROM disponibilites WHERE id = :id AND id_medecin = :im")->execute([':id'=>$_GET['supprimer'], ':im'=>$id_med]);
    set_flash('success', 'Disponibilité supprimée.'); redirect("disponibilites.php?id=$id_med");
}
$stmt = $pdo->prepare("SELECT * FROM disponibilites WHERE id_medecin = :im ORDER BY jour_semaine, heure_debut"); $stmt->execute([':im'=>$id_med]); $dispos = $stmt->fetchAll();
$jours = [1=>'Lundi', 2=>'Mardi', 3=>'Mercredi', 4=>'Jeudi', 5=>'Vendredi', 6=>'Samedi', 7=>'Dimanche'];
include __DIR__ . '/../../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary">Disponibilités du Dr. <?= e($med['prenom'] . ' ' . $med['nom']) ?></h2>
    <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Retour</a>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm border-0"><div class="card-body p-4">
            <h5 class="mb-3">Ajouter un créneau</h5>
            <form method="POST">
                <div class="mb-3"><label class="form-label">Jour</label><select name="jour" class="form-select"><?php foreach ($jours as $n=>$j): ?><option value="<?= $n ?>"><?= $j ?></option><?php endforeach; ?></select></div>
                <div class="mb-3"><label class="form-label">Heure de début</label><input type="time" name="debut" class="form-control" required></div>
                <div class="mb-4"><label class="form-label">Heure de fin</label><input type="time" name="fin" class="form-control" required></div>
                <button type="submit" name="ajouter" class="btn btn-primary w-100">Ajouter</button>
            </form>
        </div></div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Jour</th><th>Heures</th><th class="text-end">Action</th></tr></thead>
                <tbody><?php foreach ($dispos as $d): ?><tr>
                    <td class="fw-bold"><?= $jours[$d['jour_semaine']] ?></td>
                    <td><?= substr($d['heure_debut'],0,5) ?> - <?= substr($d['heure_fin'],0,5) ?></td>
                    <td class="text-end"><a href="disponibilites.php?id=<?= $id_med ?>&supprimer=<?= $d['id'] ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a></td>
                </tr><?php endforeach; ?></tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>