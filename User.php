<?php
class User {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function simpanData($data) {
        $sql = "INSERT INTO users (nama, email, hobi, gender, browser, ip_address) 
                VALUES (:nama, :email, :hobi, :gender, :browser, :ip_address)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function ambilSemuaData() {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>