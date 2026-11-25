<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/fonctions.php';
require_role('administrateur');
$id = (int)($_GET['id'] ?? 0); $action = $_GET['action'] ?? '';
if ($id > 0 && in_array($action, ['confirmer', 'refuser'])) {
    $statut = ($action === 'confirmer') ? 'confirme' : 'refuse';
    $pdo->prepare("UPDATE rendez_vous SET statut = :s WHERE id = :id AND statut = 'en_attente'")->execute([':s'=>$statut, ':id'=>$id]);
    set_flash('success', 'Le rendez-vous a été mis à jour avec succès.');
}
redirect('index.php');