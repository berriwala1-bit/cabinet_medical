<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function e(?string $data): string { return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8'); }
function redirect(string $url): void { header("Location: " . $url); exit(); }
function est_connecte(): bool { return isset($_SESSION['user_id']) && isset($_SESSION['role']); }

function require_role(string|array $roles): void {
    if (!est_connecte()) { set_flash('danger', 'Veuillez vous connecter.'); redirect(APP_URL . '/login.php'); }
    $roles_autorises = is_array($roles) ? $roles : [$roles];
    if (!in_array($_SESSION['role'], $roles_autorises, true)) { set_flash('danger', 'Accès refusé.'); redirect(APP_URL . '/index.php'); }
}

function set_flash(string $type, string $message): void { $_SESSION['flash'] = ['type' => $type, 'message' => $message]; }
function get_flash(): ?array {
    if (isset($_SESSION['flash'])) { $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f; }
    return null;
}
function format_date_fr(string $date): string { return (new DateTime($date))->format('d/m/Y'); }

function creneau_disponible(PDO $pdo, int $id_medecin, string $date, string $heure, int $duree = 30): bool {
    $debut = new DateTime("$date $heure");
    $fin = clone $debut; $fin->modify("+$duree minutes");
    $sql = "SELECT COUNT(*) as total FROM rendez_vous WHERE id_medecin = :id AND date_rdv = :date AND statut IN ('en_attente', 'confirme') AND (heure_rdv < :fin AND ADDTIME(heure_rdv, SEC_TO_TIME(duree_minutes * 60)) > :debut)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id'=>$id_medecin, ':date'=>$date, ':fin'=>$fin->format('H:i:s'), ':debut'=>$debut->format('H:i:s')]);
    return ((int)$stmt->fetch()['total'] === 0);
}