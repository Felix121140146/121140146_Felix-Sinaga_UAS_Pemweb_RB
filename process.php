<?php
session_start();
require_once 'config.php';
require_once 'User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    if (strlen($_POST['nama']) < 3) {
        $errors[] = "Nama minimal 3 karakter";
    }
    
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid";
    }
    
    if (empty($errors)) {
        $hobi = isset($_POST['hobi']) ? implode(', ', $_POST['hobi']) : '';
        
        $data = [
            'nama' => $_POST['nama'],
            'email' => $_POST['email'],
            'hobi' => $hobi,
            'gender' => $_POST['gender'],
            'browser' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ];
        
        try {
            $user = new User($pdo);
            if ($user->simpanData($data)) {
                $_SESSION['success'] = true;
                header('Location: index.php?success=1');
                exit;
            }
        } catch (Exception $e) {
            error_log("Error in process.php: " . $e->getMessage());
            $errors[] = "Terjadi kesalahan saat menyimpan data";
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: index.php?error=1');
        exit;
    }
}
?>