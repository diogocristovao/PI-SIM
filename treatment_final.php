<?php
session_start();

$connect = mysqli_connect("localhost", "root", "", "sim");
if ($_SESSION['user_type'] != 'M') {
    header("Location: login_final.php");
    exit();
}

// Supondo que o ID do médico está disponível na sessão
$doctor_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['startTreatment'])) {
    $patientId = $_POST['patientId'];
    $treatmentDescription = $_POST['comments'];
    $startDate = $_POST['startDate'];
    $periodicity = $_POST['periodicity'];

    // Insert the new treatment into the treatments table
    $stmt = $connect->prepare("INSERT INTO treatments (PATIENT_ID, DOCTOR_ID, COMMENTS, START_DATE, PERIODICITY) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $patientId, $doctor_id, $treatmentDescription, $startDate, $periodicity);
    $stmt->execute();
    $stmt->close();
}

// Fetch patients for dropdown
$patients = $connect->query("SELECT users.ID, users.NAME FROM users INNER JOIN patient_doctor_relation ON users.ID = patient_doctor_relation.PATIENT_ID WHERE patient_doctor_relation.DOCTOR_ID = $doctor_id");
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'header_doc.php'; ?><br>
<h2 class="text-center">Tratamentos</h2>

<div class="card my-4">
    <div class="card-header">
        <h4>Iniciar Tratamento</h4>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <input type="hidden" name="startTreatment" value="1">
            <div class="mb-3">
                <label for="patientId" class="form-label">ID do Paciente:</label>
                <select class="form-select" id="patientId" name="patientId" required>
                    <option value="">Selecione o Paciente</option>
                    <?php while ($patient = $patients->fetch_assoc()): ?>
                        <option value="<?= $patient['ID'] ?>"><?= $patient['NAME'] ?> (ID: <?= $patient['ID'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="startDate" class="form-label">Data de Início:</label>
                <input type="date" class="form-control" id="startDate" name="startDate" required>
            </div>
            <div class="mb-3">
                <label for="periodicity" class="form-label">Periodicidade:</label>
                <input type="text" class="form-control" id="periodicity" name="periodicity" required>
            </div>
            <div class="mb-3">
                <label for="comments" class="form-label">Descrição do Tratamento:</label>
                <input type="text" class="form-control" id="comments" name="comments" required>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar Tratamento</button>
        </form>
    </div>
</div>

<!-- Listar tratamentos para cada paciente -->
<h2 class="text-center">Tratamentos</h2>
<?php
$treatments_query = $connect->query("SELECT t.*, u.NAME AS PATIENT_NAME FROM treatments t JOIN users u ON t.PATIENT_ID = u.ID WHERE t.DOCTOR_ID = $doctor_id ORDER BY t.START_DATE DESC");

if ($treatments_query->num_rows > 0) {
    while ($treatment = $treatments_query->fetch_assoc()) {
        echo "<div class='card mb-3'>";
        echo "<div class='card-header'>Paciente: " . htmlspecialchars($treatment['PATIENT_NAME']) . " (ID: " . htmlspecialchars($treatment['PATIENT_ID']) . ")</div>";
        echo "<div class='card-body'>";
        echo "<p>Descrição: " . htmlspecialchars($treatment['COMMENTS']) . "</p>";
        echo "<p>Data de Início: " . htmlspecialchars($treatment['START_DATE']) . "</p>";
        echo "<p>Periodicidade: " . htmlspecialchars($treatment['PERIODICITY']) . "</p>";
        echo "<a href='treatment_details.php?treatment_id=" . htmlspecialchars($treatment['ID']) . "' class='btn btn-info'>Ver Detalhes</a>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<p class='text-center'>Nenhum tratamento encontrado.</p>";
}
?>
</body>
</html>
