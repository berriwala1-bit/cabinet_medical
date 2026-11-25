<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/fonctions.php';
require_role('administrateur');
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    try {
        $pdo->prepare("DELETE FROM specialites WHERE id = :id")->execute([':id'=>$id]);
        set_flash('success', 'Spécialité supprimée.');
    } catch (PDOException $e) {
        set_flash('danger', 'Impossible de supprimer : des médecins utilisent cette spécialité.');
    }
}
redirect('index.php');