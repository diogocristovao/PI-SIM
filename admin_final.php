<?php
session_start();

$connect = mysqli_connect("localhost", "root", "", "sim");

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
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search = $_POST['search'];
    $users = $connect->query("SELECT ID, NAME, ADRESS, PHONE_NUMBER, USERNAME, USER_TYPE, PHOTO, CREATION_DATE FROM users WHERE NAME LIKE '%$search%'");
} else {
    $users = $connect->query("SELECT ID, NAME, ADRESS, PHONE_NUMBER, USERNAME, USER_TYPE, PHOTO, CREATION_DATE FROM users");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteUser'])) {
    $userId = $_POST['userId'];
    $stmt = $connect->prepare("DELETE FROM users WHERE ID = ?");
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
                <select class="form-select" id="user_type" name="user_type" required>
                    <option value="ADMIN">Administrador</option>
                    <option value="M">Médico</option>
                    <option value="P">Paciente</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="photo" class="form-label">Fotografia:</label>
                <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Criar Utilizador</button>
        </form>
    </div>
</div>

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
                    <button type="submit" name="deleteUser" class="btn btn-danger" onclick="return confirm('Tem a certeza que pretende eliminar?');">Apagar</button>
                </form>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateModal<?= $user['ID'] ?>">Atualizar</button>
            </td>
        </tr>

        <!-- Modal Atualizar -->
        <div class="modal fade" id="updateModal<?= $user['ID'] ?>" tabindex="-1" aria-labelledby="updateModalLabel<?= $user['ID'] ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel<?= $user['ID'] ?>">Atualizar Utilizador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            <input type="hidden" name="userId" value="<?= $user['ID'] ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome:</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['NAME']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Morada:</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($user['ADRESS']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="contacts" class="form-label">Contactos:</label>
                                <input type="text" class="form-control" id="contacts" name="contacts" value="<?= htmlspecialchars($user['PHONE_NUMBER']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username:</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['USERNAME']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <small class="form-text text-muted">Deixe em branco se não quiser alterar a senha.</small>
                            </div>
                            <div class="mb-3">
                                <label for="user_type" class="form-label">Tipo de Utilizador:</label>
                                <select class="form-select" id="user_type" name="user_type" required>
                                    <option value="ADMIN" <?= $user['USER_TYPE'] == 'ADMIN' ? 'selected' : '' ?>>Administrador</option>
                                    <option value="M" <?= $user['USER_TYPE'] == 'M' ? 'selected' : '' ?>>Médico</option>
                                    <option value="P" <?= $user['USER_TYPE'] == 'P' ? 'selected' : '' ?>>Paciente</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="photo" class="form-label">Fotografia:</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                <small class="form-text text-muted">Deixe em branco se não quiser alterar a fotografia.</small>
                            </div>
                            <button type="submit" name="updateUser" class="btn btn-primary">Atualizar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.html'; ?>
</html>