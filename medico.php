<?php
session_start();

$connect = mysqli_connect("localhost", "root", "", "pi-sim");
if ($_SESSION['user_type'] != 'M') {
header("Location: login.php");
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

<!DOCTYPE html>
<html lang="en">
<?php include 'header_admin.html'; ?><br>
<h2 class="text-center">Gestão de Pacientes</h2>
<div class="card my-4">
    <div class="card-header">
        <h4>Criar Novo Paciente</h4>
    </div>
    <div class="card-body">
        <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="createUser" value="1">
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Nome:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="contacts" class="form-label">Nº Telemóvel:</label>
                <input type="text" class="form-control" id="contacts" name="contacts" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>
            <div class="mb-3">
                <label for="birth_date" class="form-label">Data de Nascimento:</label>
                <input type="date" class="form-control" id="birth_date" name="birth_date">
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Sexo:</label>
                <select class="form-select" id="gender" name="gender">
                    <option value="M">Masculino</option>
                    <option value="F">Feminino</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Morada:</label>
                <input type="text" class="form-control" id="address" name="address">
            </div>
            <div class="mb-3">
                <label for="locality" class="form-label">Localidade:</label>
                <input type="text" class="form-control" id="locality" name="locality">
            </div>
            <div class="mb-3">
                <label for="district" class="form-label">Distrito:</label>
                <input type="text" class="form-control" id="district" name="district">
            </div>
            <div class="mb-3">
                <label for="nif" class="form-label">NIF:</label>
                <input type="text" class="form-control" id="nif" name="nif">
            </div>
            <div class="mb-3">
                <label for="allergies" class="form-label">Alergias:</label>
                <input type="text" class="form-control" id="allergies" name="allergies">
            </div>
            <div class="mb-3">
                <label for="photo" class="form-label">Foto:</label>
                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Criar Paciente</button>
        </form>
    </div>
</div>

<!-- Adicionar a seção para iniciar tratamentos -->
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
                <label for="startDate" class="form-label">Periodicidade:</label>
                <input type="text" class="form-control" id="periodicity" name="periodicity" required>
            </div>
            <div class="mb-3">
                <label for="startDate" class="form-label">Descrição do Tratamento:</label>
                <input type="text" class="form-control" id="comments" name="comments" required>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar Tratamento</button>
        </form>
    </div>
</div>

<h2 class="text-center">Lista de Pacientes</h2>
<form method="post" action="">
    <div class="input-group mb-3">
        <input type="text" class="form-control" name="search" placeholder="Procurar pacientes por nome">
        <button class="btn btn-outline-secondary" type="submit">Procurar</button>
    </div>
</form>
<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Endereço</th>
        <th>Contatos</th>
        <th>Nome de Usuário</th>
        <th>Foto</th>
        <th>Ações</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($row = $users->fetch_assoc()): ?>
    <tr>
        <td><?= $row['ID'] ?></td>
        <td><?= $row['NAME'] ?></td>
        <td><?= $row['ADRESS'] ?></td>
        <td><?= $row['PHONE_NUMBER'] ?></td>
        <td><?= $row['USERNAME'] ?></td>
        <td><img src="data:image/jpeg;base64,<?= base64_encode($row['PHOTO']) ?>" alt="User Photo" style="width: 50px; height: 50px;"></td>
        <td>
            <form method="post" action="" class="d-inline">
                <input type="hidden" name="deleteUser" value="1">
                <input type="hidden" name="userId" value="<?= $row['ID'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
            </form>
            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $row['ID'] ?>">Editar</button>

            <!-- Modal para editar paciente -->
            <div class="modal fade" id="editUserModal<?= $row['ID'] ?>" tabindex="-1" aria-labelledby="editUserModalLabel<?= $row['ID'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel<?= $row['ID'] ?>">Editar Paciente</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="updateUser" value="1">
                                <input type="hidden" name="userId" value="<?= $row['ID'] ?>">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username:</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?= $row['USERNAME'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password:</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nome:</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= $row['NAME'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="contacts" class="form-label">Nº Telemóvel:</label>
                                    <input type="text" class="form-control" id="contacts" name="contacts" value="<?= $row['PHONE_NUMBER'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Morada:</label>
                                    <input type="text" class="form-control" id="address" name="address" value="<?= $row['ADRESS'] ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="user_type" class="form-label">Tipo de Usuário:</label>
                                    <select class="form-select" id="user_type" name="user_type" required>
                                        <option value="P" <?= $row['USER_TYPE'] == 'P' ? 'selected' : '' ?>>Paciente</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="photo" class="form-label">Foto:</label>
                                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                </div>
                                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<!-- Listar tratamentos para cada paciente -->
<h2 class="text-center">Tratamentos</h2>
<?php
$treatments_query = $connect->query("SELECT * FROM treatments WHERE DOCTOR_ID = $doctor_id");
while ($treatment = $treatments_query->fetch_assoc()): ?>
    <div>
        <h5>Tratamento para Paciente ID <?= $treatment['PATIENT_ID'] ?>:</h5>
        <p>Data de Início: <?= htmlspecialchars($treatment['START_DATE']) ?></p>
        <p>Periodicidade: <?= htmlspecialchars($treatment['PERIODICITY']) ?></p>
        <p>Descrição do Tratamento: <?= htmlspecialchars($treatment['COMMENTS']) ?></p>
    </div>
<?php endwhile; ?>
</html>