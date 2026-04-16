<?php
header('Content-Type: application/json');

$specialty_id = intval($_GET['specialty_id'] ?? 0);

if ($specialty_id == 0) {
    echo json_encode(['groups' => []]);
    exit;
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=university_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT 
            g.id,
            g.name,
            g.capacity,
            COUNT(s.id) as enrolled
        FROM groups g
        LEFT JOIN students s ON g.id = s.group_id
        WHERE g.specialty_id = ?
        GROUP BY g.id
        HAVING (g.capacity - COUNT(s.id)) > 0
    ");
    
    $stmt->execute([$specialty_id]);
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['groups' => $groups]);
    
} catch(PDOException $e) {
    echo json_encode(['groups' => [], 'error' => $e->getMessage()]);
}
?>