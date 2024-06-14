<?php
session_start();

$connect = new mysqli("localhost", "root", "", "sim");
if ($_SESSION['user_type'] != 'M') {
    header("Location: login_final.php");
    exit();
}

// Verificar a conexão
if ($connect->connect_error) {
    die("Conexão falhou: " . $connect->connect_error);
}

// Fetch unseen measurements
$query = "
SELECT m.*, t.COMMENTS AS TREATMENT_COMMENTS, u.NAME AS PATIENT_NAME 
FROM measurements m 
JOIN treatments t ON m.ID_TREATMENT = t.ID 
JOIN users u ON t.PATIENT_ID = u.ID 
WHERE m.SEEN_DATETIME IS NULL
ORDER BY m.DATETIME ASC
";

$result = $connect->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alertas de Medições</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'header_doc.php'; ?>
<br>
<h2 class="text-center">Alertas de Medições</h2>

<?php if ($result->num_rows > 0): ?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Data</th>
            <th>pH</th>
            <th>Temperatura</th>
            <th>Condutividade</th>
            <th>Visual</th>
            <th>Odor</th>
            <th>Tipo de Alerta</th>
            <th>Paciente</th>
            <th>Tratamento</th>
            <th>Ação</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['DATETIME']) ?></td>
                <td><?= htmlspecialchars($row['PH']) ?></td>
                <td><?= htmlspecialchars($row['TEMPERATURE']) ?></td>
                <td><?= htmlspecialchars($row['CONDUCTIVITY']) ?></td>
                <td><?= htmlspecialchars($row['VISUAL'] ? 'Sim' : 'Não') ?></td>
                <td><?= htmlspecialchars($row['ODOR'] ? 'Sim' : 'Não') ?></td>
                <td><?= htmlspecialchars($row['ALERT_TYPE']) ?></td>
                <td><?= htmlspecialchars($row['PATIENT_NAME']) ?></td>
                <td><?= htmlspecialchars($row['TREATMENT_COMMENTS']) ?></td>
                <td>
                    <form method="post" action="marked_as_view.php">
                        <input type="hidden" name="measurement_id" value="<?= $row['ID'] ?>">
                        <button type="submit" class="btn btn-primary">Marcar como Visto</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="text-center">Nenhum alerta de medição não visto.</p>
<?php endif; ?>
</body>
</html>
