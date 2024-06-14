<?php


// Query to count unseen measurements
global $connect;
$alert_query = "
SELECT COUNT(*) AS alert_count
FROM measurements m
JOIN treatments t ON m.ID_TREATMENT = t.ID
WHERE m.SEEN_DATETIME IS NULL
";

$alert_result = $connect->query($alert_query);
$alert_count = 0;

if ($alert_result) {
    $alert_row = $alert_result->fetch_assoc();
    $alert_count = $alert_row['alert_count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <title>Sistema de Gestão</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Sistema de Gestão</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="medico_criarpaciente.php">Registar Paciente</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="treatment_final.php">Tratamentos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="paciente.php">Perfil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="alerts.php" style="<?= $alert_count > 0 ? 'color: red; font-weight: bold;' : '' ?>">
                        Alertas<?= $alert_count > 0 ? " ($alert_count)" : '' ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>