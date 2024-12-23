<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT id, nama, email, hobi, gender FROM users ORDER BY id DESC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($data);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>