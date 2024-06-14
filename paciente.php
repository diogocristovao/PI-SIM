<?php
session_start();
if ($_SESSION['user_type'] != 'P') {
    header("Location: login.php");
    exit();
}
$connect = new mysqli("localhost", "root", "", "sim");
include 'sad.php';

// Function to fetch patient readings
function fetchReadings($patient_id, $connect) {
    $stmt = $connect->prepare("SELECT * FROM measurements WHERE ID_TREATMENT IN (SELECT ID FROM treatments WHERE PATIENT_ID = ?)");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to calculate age from birth date
function calculateAge($birth_date) {
    $birthDate = new DateTime($birth_date);
    $currentDate = new DateTime();
    $age = $birthDate->diff($currentDate)->y;
    return $age;
}

// Add Reading functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_reading'])) {
    $id_treatment = $_POST['id_treatment'];
    $datetime = $_POST['datetime'];
    $ph = $_POST['ph'];
    $temperature = $_POST['temperature'];
    $conductivity = $_POST['conductivity'];
    $visual = $_POST['visual'];
    $odor = $_POST['odor'];

    // Fetch birth date and calculate age
    $stmt = $connect->prepare("SELECT BIRTH_DATE FROM patients WHERE ID = (SELECT PATIENT_ID FROM treatments WHERE ID = ?)");
    $stmt->bind_param("i", $id_treatment);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $birth_date = $result['BIRTH_DATE'];
    $age = calculateAge($birth_date);

    $alert_type = sad($odor, $visual, $temperature, $conductivity, $ph, $age); // Default value, should be calculated

    // Insert the new reading
    $stmt = $connect->prepare("INSERT INTO measurements (ID_TREATMENT, DATETIME, PH, TEMPERATURE, CONDUCTIVITY, VISUAL, ODOR, ALERT_TYPE) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isddddds", $id_treatment, $datetime, $ph, $temperature, $conductivity, $visual, $odor, $alert_type);
    $stmt->execute();
}

// Update Patient Data functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_patient'])) {
    $id = $_POST['id'];
    $locality = $_POST['locality'];
    $district = $_POST['district'];
    $email = $_POST['email'];
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $nif = $_POST['nif'];
    $allergies = $_POST['allergies'];

    // Update patient data
    $stmt = $connect->prepare("UPDATE patients SET LOCALITY = ?, DISTRICT = ?, EMAIL = ?, BIRTH_DATE = ?, GENDER = ?, NIF = ?, ALLERGIES = ? WHERE ID = ?");
    $stmt->bind_param("sssssssi", $locality, $district, $email, $birth_date, $gender, $nif, $allergies, $id);
    $stmt->execute();
}

// Fetch current user details
$user_id = $_SESSION['user_id'];
$stmt = $connect->prepare("SELECT * FROM users WHERE ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$current_user = $stmt->get_result()->fetch_assoc();

// Fetch patient data if user is a patient
if ($current_user['USER_TYPE'] == 'P') {
    $stmt = $connect->prepare("SELECT * FROM patients WHERE ID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $patient_data = $stmt->get_result()->fetch_assoc();
}

// Fetch treatment data for dropdown
$treatments = $connect->query("SELECT * FROM treatments WHERE PATIENT_ID = $user_id");

// Fetch all users for administrators
if ($current_user['USER_TYPE'] == 'ADMIN') {
    $all_users = $connect->query("SELECT * FROM users");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            margin-bottom: 20px;
        }
        .navbar {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Health Monitoring</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <?php if ($current_user['USER_TYPE'] == 'P'): ?>
                <li class="nav-item"><a class="nav-link" href="#addReading">Add Reading</a></li>
                <li class="nav-item"><a class="nav-link" href="#viewReadings">View Readings</a></li>
                <li class="nav-item"><a class="nav-link" href="#viewPatientData">View and Alter Data</a></li>
            <?php elseif ($current_user['USER_TYPE'] == 'ADMIN'): ?>
                <li class="nav-item"><a class="nav-link" href="#manageUsers">Manage Users</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div class="container">
    <?php if ($current_user['USER_TYPE'] == 'P'): ?>
        <!-- Add Reading Section -->
        <div class="card" id="addReading">
            <div class="card-header">
                Add Reading
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <input type="hidden" name="add_reading" value="1">
                    <div class="mb-3">
                        <label for="id_treatment" class="form-label">Treatment</label>
                        <select class="form-select" id="id_treatment" name="id_treatment" required>
                            <?php while ($treatment = $treatments->fetch_assoc()): ?>
                                <option value="<?= $treatment['ID'] ?>"><?= $treatment['ID'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="datetime" class="form-label">Datetime</label>
                        <input type="datetime-local" class="form-control" id="datetime" name="datetime" required>
                    </div>
                    <div class="mb-3">
                        <label for="ph" class="form-label">pH</label>
                        <input type="number" step="0.01" class="form-control" id="ph" name="ph" required>
                    </div>
                    <div class="mb-3">
                        <label for="temperature" class="form-label">Temperature</label>
                        <input type="number" step="0.1" class="form-control" id="temperature" name="temperature" required>
                    </div>
                    <div class="mb-3">
                        <label for="conductivity" class="form-label">Conductivity</label>
                        <input type="number" step="0.1" class="form-control" id="conductivity" name="conductivity" required>
                    </div>
                    <div class="mb-3">
                        <label for="visual" class="form-label">Visual</label>
                        <select class="form-select" id="visual" name="visual" required>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="odor" class="form-label">Odor</label>
                        <select class="form-select" id="odor" name="odor" required>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Reading</button>
                </form>
            </div>
        </div>

        <!-- View Readings Section -->
        <div class="card" id="viewReadings">
            <div class="card-header">
                View Readings
            </div>
            <div class="card-body">
                <?php
                $readings = fetchReadings($user_id, $connect);
                if ($readings->num_rows > 0):
                    ?>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Treatment ID</th>
                            <th>Datetime</th>
                            <th>pH</th>
                            <th>Temperature</th>
                            <th>Conductivity</th>
                            <th>Visual</th>
                            <th>Odor</th>
                            <th>Alert Type</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php while ($reading = $readings->fetch_assoc()): ?>
                            <tr>
                                <td><?= $reading['ID'] ?></td>
                                <td><?= $reading['ID_TREATMENT'] ?></td>
                                <td><?= $reading['DATETIME'] ?></td>
                                <td><?= $reading['PH'] ?></td>
                                <td><?= $reading['TEMPERATURE'] ?></td>
                                <td><?= $reading['CONDUCTIVITY'] ?></td>
                                <td><?= $reading['VISUAL'] ? 'Yes' : 'No' ?></td>
                                <td><?= $reading['ODOR'] ? 'Yes' : 'No' ?></td>
                                <td><?= $reading['ALERT_TYPE'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No readings found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- View and Alter Data Section -->
        <div class="card" id="viewPatientData">
            <div class="card-header">
                View and Alter Data
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <input type="hidden" name="update_patient" value="1">
                    <input type="hidden" name="id" value="<?= $patient_data['ID'] ?>">
                    <div class="mb-3">
                        <label for="locality" class="form-label">Locality</label>
                        <input type="text" class="form-control" id="locality" name="locality" value="<?= $patient_data['LOCALITY'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="district" class="form-label">District</label>
                        <input type="text" class="form-control" id="district" name="district" value="<?= $patient_data['DISTRICT'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= $patient_data['EMAIL'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="birth_date" class="form-label">Birth Date</label>
                        <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?= $patient_data['BIRTH_DATE'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="M" <?= $patient_data['GENDER'] == 'M' ? 'selected' : '' ?>>Male</option>
                            <option value="F" <?= $patient_data['GENDER'] == 'F' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nif" class="form-label">NIF</label>
                        <input type="text" class="form-control" id="nif" name="nif" value="<?= $patient_data['NIF'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="allergies" class="form-label">Allergies</label>
                        <input type="text" class="form-control" id="allergies" name="allergies" value="<?= $patient_data['ALLERGIES'] ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Data</button>
                </form>
            </div>
        </div>
    <?php elseif ($current_user['USER_TYPE'] == 'ADMIN'): ?>
        <!-- Manage Users Section -->
        <div class="card" id="manageUsers">
            <div class="card-header">
                Manage Users
            </div>
            <div class="card-body">
                <?php if ($all_users->num_rows > 0): ?>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>User Type</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php while ($user = $all_users->fetch_assoc()): ?>
                            <tr>
                                <td><?= $user['ID'] ?></td>
                                <td><?= $user['USERNAME'] ?></td>
                                <td><?= $user['USER_TYPE'] ?></td>
                                <td><?= $user['EMAIL'] ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?= $user['ID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="delete_user.php?id=<?= $user['ID'] ?>" class="btn btn-sm btn-danger">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No users found.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
