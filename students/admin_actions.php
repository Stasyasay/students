<?php
$pdo = new PDO('mysql:host=localhost;dbname=university_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$action = $_POST['action'] ?? '';
$tab = $_POST['tab'] ?? 'students';
$redirect = "admin.php?tab=$tab&msg=success";

try {
    if ($action == 'delete_student') {
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$_POST['id']]);
    } 
    elseif ($action == 'add_specialty') {
        $stmt = $pdo->prepare("INSERT INTO specialties (name) VALUES (?)");
        $stmt->execute([$_POST['name']]);
    } 
    elseif ($action == 'delete_specialty') {
        // CASCADE удалит привязанные группы автоматически
        $stmt = $pdo->prepare("DELETE FROM specialties WHERE id = ?");
        $stmt->execute([$_POST['id']]);
    } 
    elseif ($action == 'add_group') {
        $stmt = $pdo->prepare("INSERT INTO groups (specialty_id, name, capacity) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['specialty_id'], $_POST['name'], $_POST['capacity']]);
    } 
    elseif ($action == 'delete_group') {
        $stmt = $pdo->prepare("DELETE FROM groups WHERE id = ?");
        $stmt->execute([$_POST['id']]);
    }
} catch (PDOException $e) {
    die("❌ Ошибка базы данных: " . $e->getMessage());
}

header("Location: $redirect");
exit;
?>