<?php
session_start();

$connect = mysqli_connect("localhost", "root", "", "sim");
if ($_SESSION['user_type'] != 'M') {
    header("Location: login_final.php");
    exit();
}

$treatment_id = $_GET['treatment_id'];

// Fetch treatment details
$treatment_query = $connect->prepare("SELECT t.*, u.NAME AS PATIENT_NAME FROM treatments t JOIN users u ON t.PATIENT_ID = u.ID WHERE t.ID = ?");
$treatment_query->bind_param("i", $treatment_id);
$treatment_query->execute();
$treatment_result = $treatment_query->get_result();
$treatment = $treatment_result->fetch_assoc();
$treatment_query->close();

// Fetch measurements related to this treatment
$measurements_query = $connect->prepare("SELECT * FROM measurements WHERE ID_TREATMENT = ? ORDER BY DATETIME ASC");
$measurements_query->bind_param("i", $treatment_id);
$measurements_query->execute();
$measurements_result = $measurements_query->get_result();
?>

    <!DOCTYPE html>
    <html lang="en">
    <?php include 'header_doc.php'; ?><br>
    <h2 class="text-center">Detalhes do Tratamento</h2>

    <div class="card mb-3">
        <div class="card-header">Paciente: <?= htmlspecialchars($treatment['PATIENT_NAME'] ?? '') ?> (ID: <?= htmlspecialchars($treatment['PATIENT_ID'] ?? '') ?>)</div>
        <div class="card-body">
            <p>Descrição: <?= htmlspecialchars($treatment['COMMENTS'] ?? '') ?></p>
            <p>Data de Início: <?= htmlspecialchars($treatment['START_DATE'] ?? '') ?></p>
            <p>Data de Término: <?= htmlspecialchars($treatment['END_DATE'] ?? '') ?></p>
            <p>Periodicidade: <?= htmlspecialchars($treatment['PERIODICITY'] ?? '') ?></p>
        </div>
    </div>

    <h3 class="text-center">Medições</h3>
    <?php if ($measurements_result->num_rows > 0): ?>
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
                <th>Visto em</th>
                <th>Tipo de Alerta do Médico</th>

            </tr>
            </thead>
            <tbody>
            <?php while ($measurement = $measurements_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($measurement['DATETIME'] ?? '') ?></td>
                    <td><?= htmlspecialchars($measurement['PH'] ?? '') ?></td>
                    <td><?= htmlspecialchars($measurement['TEMPERATURE'] ?? '') ?></td>
                    <td><?= htmlspecialchars($measurement['CONDUCTIVITY'] ?? '') ?></td>
                    <td><?= htmlspecialchars($measurement['VISUAL'] ?? '') ?></td>
                    <td><?= htmlspecialchars($measurement['ODOR'] ?? '') ?></td>
                    <td><?= htmlspecialchars($measurement['ALERT_TYPE'] ?? '') ?></td>
                    <td><?= htmlspecialchars($measurement['SEEN_DATETIME'] ?? '') ?></td>
                    <td><?= htmlspecialchars($measurement['DOCTOR_ALERT_TYPE'] ?? '') ?>
                        <form method="post" action="">
                            <input type="hidden" name="updateDoctorAlert" value="1">
                            <input type="hidden" name="measurement_id" value="<?= $measurement['ID'] ?>">
                            <div class="input-group">
                                <input type="text" class="form-control" name="doctor_alert_type" value="<?= htmlspecialchars($measurement['DOCTOR_ALERT_TYPE'] ?? '') ?>" placeholder="Tipo de Alerta do Médico">
                                <button type="submit" class="btn btn-primary">Atualizar</button>
                            </div>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center">Nenhuma medição encontrada.</p>
    <?php endif; ?>
    </body>
    </html>
<?php
$measurements_query->close();
?>