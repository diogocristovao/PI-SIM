<?php
session_start();

// Verificar se o usuário está autenticado e é um médico
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'M') {
    header("Location: login_final.php");
    exit();
}

// Conectar ao banco de dados
$connect = new mysqli("localhost", "root", "", "sim");

// Verificar a conexão
if ($connect->connect_error) {
    die("Conexão falhou: " . $connect->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $measurement_id = intval($_POST['measurement_id']);

    // Atualizar SEEN_DATETIME para o timestamp atual
    $query = "UPDATE measurements SET SEEN_DATETIME = NOW() WHERE ID = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("i", $measurement_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: alerts.php");
    } else {
        echo "Erro ao marcar a medição como vista.";
    }

    $stmt->close();
}

$connect->close();
?>