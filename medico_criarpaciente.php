<?php
global $users, $patients, $connect, $doctor_id;
include 'medico_content.php';
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'header_doc.php'; ?><br>
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

</html>