<?php
header('Content-Type: application/json');

// Configurações do banco de dados (mesmas do arquivo principal)
$host = 'localhost';
$dbname = 'sistemas_clientes';
$username = 'root'; // Altere para seu usuário do MySQL
$password = ''; // Altere para sua senha do MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Erro na conexão com o banco de dados']));
}

if (!isset($_GET['id'])) {
    die(json_encode(['error' => 'ID do cliente não fornecido']));
}

$id = $_GET['id'];
$sql = "SELECT * FROM clientes WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    die(json_encode(['error' => 'Cliente não encontrado']));
}

echo json_encode($cliente);
?>