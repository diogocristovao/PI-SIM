<?php
session_start();

$connect = mysqli_connect("localhost", "root", "", "sim");
if ($_SESSION['user_type'] != 'M') {
    header("Location: login_final.php");
    exit();}
// Supondo que o ID do médico está disponível na sessão
$doctor_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $contacts = $_POST['contacts'];
    $username = $_POST['username'];
    $password = hash("sha256", $_POST['password']);
    $user_type = 'P';
    $photo = addslashes(file_get_contents($_FILES['photo']['tmp_name']));
    $email = $_POST['email'];
    $locality = $_POST['locality'];
    $district = $_POST['district'];
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $nif = $_POST['nif'];
    $allergies = $_POST['allergies'];

    // Insere o novo usuário na tabela users
    $stmt = $connect->prepare("INSERT INTO users (NAME, ADRESS, PHONE_NUMBER, USERNAME, PASSWORD, USER_TYPE, PHOTO, EMAIL, CREATION_DATE) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssisssbs", $name, $address, $contacts, $username, $password, $user_type, $photo, $email);
    $stmt->execute();
    $new_user_id = $stmt->insert_id; // Obtém o ID do novo usuário inserido
    $stmt->close();

    $stmt = $connect->prepare("INSERT INTO patients (ID, LOCALITY, DISTRICT, EMAIL, BIRTH_DATE, GENDER, NIF, ALLERGIES) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $new_user_id, $locality, $district, $email, $birth_date, $gender, $nif, $allergies);
    $stmt->execute();
    $stmt->close();

    // Associar o paciente ao médico atual
    $stmt = $connect->prepare("INSERT INTO patient_doctor_relation (PATIENT_ID, DOCTOR_ID) VALUES (?, ?)");
    $stmt->bind_param("ii", $new_user_id, $doctor_id);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search = $_POST['search'];
    // Consulta SQL para buscar pacientes associados ao médico da sessão
    $users = $connect->query("SELECT u.ID, u.NAME, u.ADRESS, u.PHONE_NUMBER, u.USERNAME, u.USER_TYPE, u.PHOTO, u.CREATION_DATE FROM users u INNER JOIN patient_doctor_relation pdr ON u.ID = pdr.patient_id WHERE u.USER_TYPE = 'P' AND pdr.doctor_id = $_SESSION[user_id] AND u.NAME LIKE '%$search%'");
} else {
    // Consulta SQL para buscar todos os pacientes associados ao médico da sessão
    $users = $connect->query("SELECT users.ID, users.NAME, users.ADRESS, users.PHONE_NUMBER, users.USERNAME, users.USER_TYPE, users.PHOTO, users.CREATION_DATE FROM users INNER JOIN patient_doctor_relation ON users.ID = patient_doctor_relation.PATIENT_ID WHERE patient_doctor_relation.DOCTOR_ID = $doctor_id");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteUser'])) {
    $userId = $_POST['userId'];
    // Deletar o paciente e suas relações com médicos
    $stmt = $connect->prepare("DELETE FROM users WHERE ID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();

    // Deletar as relações deste paciente com médicos
    $stmt = $connect->prepare("DELETE FROM patient_doctor_relation WHERE patient_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateUser'])) {
    $userId = $_POST['userId'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $contacts = $_POST['contacts'];
    $username = $_POST['username'];
    $user_type = $_POST['user_type'];
    $password = !empty($_POST['password']) ? hash("sha256", $_POST['password']) : null;
    $photo = !empty($_FILES['photo']['tmp_name']) ? addslashes(file_get_contents($_FILES['photo']['tmp_name'])) : null;

    $stmt = $connect->prepare("UPDATE users SET NAME = ?, ADRESS = ?, PHONE_NUMBER = ?, USERNAME = ?, USER_TYPE = ?" . ($password ? ", PASSWORD = ?" : "") . ($photo ? ", PHOTO = ?" : "") . " WHERE ID = ?");
    if ($password && $photo) {
        $stmt->bind_param("ssssssbi", $name, $address, $contacts, $username, $user_type, $password, $photo, $userId);
    } elseif ($password) {
        $stmt->bind_param("ssssssi", $name, $address, $contacts, $username, $user_type, $password, $userId);
    } elseif ($photo) {
        $stmt->bind_param("sssssbi", $name, $address, $contacts, $username, $user_type, $photo, $userId);
    } else {
        $stmt->bind_param("sssssi", $name, $address, $contacts, $username, $user_type, $userId);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['startTreatment'])) {
    $patientId = $_POST['patientId'];
    $treatmentDescription = $_POST['comments'];
    $startDate = $_POST['startDate'];
    $periodicity = $_POST['periodicity'];

    // Insert the new treatment into the treatments table
    $stmt = $connect->prepare("INSERT INTO treatments (PATIENT_ID, DOCTOR_ID, COMMENTS, START_DATE,PERIODICITY) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $patientId, $doctor_id, $treatmentDescription, $startDate, $periodicity );
    $stmt->execute();
    $stmt->close();
}

// Fetch patients for dropdown
$patients = $connect->query("SELECT users.ID, users.NAME FROM users INNER JOIN patient_doctor_relation ON users.ID = patient_doctor_relation.PATIENT_ID WHERE patient_doctor_relation.DOCTOR_ID = $doctor_id");
?>
