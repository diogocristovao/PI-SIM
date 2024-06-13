<?php
session_start();

$connect = mysqli_connect("localhost", "root", "", "pi-sim");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $contacts = $_POST['contacts'];
    $username = $_POST['username'];
    $password = hash("sha256", $_POST['password']);
    $user_type = $_POST['user_type'];
    $photo = addslashes(file_get_contents($_FILES['photo']['tmp_name']));

    $stmt = $connect->prepare("INSERT INTO users (NAME, ADRESS, PHONE_NUMBER, USERNAME, PASSWORD, USER_TYPE, PHOTO, CREATION_DATE) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssssb", $name, $address, $contacts, $username, $password, $user_type, $photo);
    $stmt->execute();

    $user_id = $stmt->insert_id;

    $stmt->close();

    if ($user_type == 'M') {
        $speciality = $_POST['speciality'];
        $username = $_POST['username'];

        $stmt = $connect->prepare("INSERT INTO doctors (ID, SPECIALITY, USERNAME) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $speciality, $username);
        if (!$stmt->execute()) {
            die("Error inserting doctor: " . $stmt->error);
        }
        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search = $_POST['search'];
    $users = $connect->query("SELECT ID, NAME, ADRESS, PHONE_NUMBER, USERNAME, USER_TYPE, PHOTO, CREATION_DATE FROM users WHERE NAME LIKE '%$search%' AND END_DATE IS NULL");
} else {
    $users = $connect->query("SELECT ID, NAME, ADRESS, PHONE_NUMBER, USERNAME, USER_TYPE, PHOTO, CREATION_DATE FROM users WHERE END_DATE IS NULL");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteUser'])) {
    $userId = $_POST['userId'];
    $stmt = $connect->prepare("UPDATE users SET END_DATE = NOW() WHERE ID = ?");
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
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'header_admin.html'; ?>
<h2 class="text-center">Gestão de Utilizadores</h2>
<div class="card my-4">
    <div class="card-header">
        <h4>Criar Novo Utilizador</h4>
    </div>
    <div class="card-body">
        <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="createUser" value="1">
            <div class="mb-3">
                <label for="name" class="form-label">Nome:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Morada:</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="mb-3">
                <label for="contacts" class="form-label">Contactos:</label>
                <input type="text" class="form-control" id="contacts" name="contacts" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="user_type" class="form-label">Tipo de Utilizador:</label>
                <select class="form-select" id="user_type" name="user_type" onchange="toggleSpecialityField()" required>
                    <option value="ADMIN">Administrador</option>
                    <option value="M">Médico</option>
                    <option value="P">Paciente</option>
                </select>
            </div>
            <div class="mb-3" id="specialityField" style="display: none;">
                <label for="speciality" class="form-label">Especialidade:</label>
                <input type="text" class="form-control" id="speciality" name="speciality">
            </div>
            <div class="mb-3">
                <label for="photo" class="form-label">Fotografia:</label>
                <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Criar Utilizador</button>
        </form>
    </div>
</div>

<script>
    function toggleSpecialityField() {
        var userType = document.getElementById("user_type").value;
        var specialityField = document.getElementById("specialityField");

        if (userType === "M") {
            specialityField.style.display = "block";
        } else {
            specialityField.style.display = "none";
        }
    }
</script>

<div class="card my-4">
    <div class="card-header">
        <h4>Pesquisar Utilizador</h4>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <div class="mb-3">
                <label for="search" class="form-label">Pesquisar por Nome:</label>
                <input type="text" class="form-control" id="search" name="search">
            </div>
            <button type="submit" class="btn btn-primary">Pesquisar</button>
        </form>
    </div>
</div>

<h3 class="text-center">Lista de Utilizadores</h3>
<table class="table table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Username</th>
        <th>Tipo</th>
        <th>Ações</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($user = $users->fetch_assoc()): ?>
        <tr>
            <td><?= $user['ID'] ?></td>
            <td><?= $user['NAME'] ?></td>
            <td><?= $user['USERNAME'] ?></td>
            <td><?= $user['USER_TYPE'] ?></td>
            <td>
                <form method="post" action="" style="display:inline-block">
                    <input type="hidden" name="userId" value="<?= $user['ID'] ?>">
                    <button type="submit" name="deleteUser" class="btn btn-danger" onclick="return confirm('Tem a certeza que pretende desativar?');">Desativar</button>
                </form>
                <button class="btn btn-secondary" onclick="openUpdateForm(<?= $user['ID'] ?>)">Atualizar</button>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<div id="updateFormModal" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Atualizar Utilizador</h5>
                <button type="button" class="btn-close" aria-label="Close" onclick="closeUpdateForm()"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="updateUser" value="1">
                    <input type="hidden" name="userId" id="updateUserId">
                    <div class="mb-3">
                        <label for="updateName" class="form-label">Nome:</label>
                        <input type="text" class="form-control" id="updateName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="updateAddress" class="form-label">Morada:</label>
                        <input type="text" class="form-control" id="updateAddress" name="address" required>
                    </div>
                    <div class="mb-3">
                        <label for="updateContacts" class="form-label">Contactos:</label>
                        <input type="text" class="form-control" id="updateContacts" name="contacts" required>
                    </div>
                    <div class="mb-3">
                        <label for="updateUsername" class="form-label">Username:</label>
                        <input type="text" class="form-control" id="updateUsername" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="updatePassword" class="form-label">Password (deixe em branco para não alterar):</label>
                        <input type="password" class="form-control" id="updatePassword" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="updateUserType" class="form-label">Tipo de Utilizador:</label>
                        <select class="form-select" id="updateUserType" name="user_type" onchange="toggleUpdateSpecialityField()" required>
                            <option value="ADMIN">Administrador</option>
                            <option value="M">Médico</option>
                            <option value="P">Paciente</option>
                        </select>
                    </div>
                    <div class="mb-3" id="updateSpecialityField" style="display: none;">
                        <label for="updateSpeciality" class="form-label">Especialidade:</label>
                        <input type="text" class="form-control" id="updateSpeciality" name="speciality">
                    </div>
                    <div class="mb-3">
                        <label for="updatePhoto" class="form-label">Fotografia (deixe em branco para não alterar):</label>
                        <input type="file" class="form-control" id="updatePhoto" name="photo" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Atualizar Utilizador</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openUpdateForm(userId) {
        var userRow = document.querySelector('tr[data-user-id="' + userId + '"]');
        document.getElementById('updateUserId').value = userId;
        document.getElementById('updateName').value = userRow.querySelector('.name').innerText;
        document.getElementById('updateAddress').value = userRow.querySelector('.address').innerText;
        document.getElementById('updateContacts').value = userRow.querySelector('.contacts').innerText;
        document.getElementById('updateUsername').value = userRow.querySelector('.username').innerText;
        document.getElementById('updateUserType').value = userRow.querySelector('.user_type').innerText;
        toggleUpdateSpecialityField();
        document.getElementById('updateFormModal').style.display = 'block';
    }

    function closeUpdateForm() {
        document.getElementById('updateFormModal').style.display = 'none';
    }

    function toggleUpdateSpecialityField() {
        var userType = document.getElementById("updateUserType").value;
        var specialityField = document.getElementById("updateSpecialityField");

        if (userType === "M") {
            specialityField.style.display = "block";
        } else {
            specialityField.style.display = "none";
        }
    }
</script>

</body>
</html>
