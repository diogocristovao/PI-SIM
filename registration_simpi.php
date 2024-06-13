<?php ?>

<h3>Registar Novo Utilizador</h3>
<FORM id="registrationForm" method="post" action="insertion_simpi.php" enctype="multipart/form-data">
    <p>Introduce your full name:
        <input type="text" id="name" name="name" required></p>
    <p>Introduce your username:
        <input type="text" id="username" name="username" required></p>
    <p>Introduce your password:
        <input type="password" id="password" name="password" required></p>
    <p>Reintroduce your password:
        <input type="password" id="repassword" name="repassword" required></p>
    <p>Introduce your usertype:
    <select id="usertype" name="usertype" onchange="toggleAdminCode()">
        <option disabled selected value> -- select an option -- </option>
        <option value="M">Médico</option>
        <option value="P">Paciente</option>
        <option value="ADMIN">Administrador</option>
    </select></p>
    <div id="admin-code-section" style="display:none;">
        <label for="admin-code">Código de Administrador:</label>
        <input type="text" id="admin-code" name="admin-code">
    </div>
    <div id="error-message" style="color:red;"></div>
    <p>Introduce your address:
        <input type="text" id="address" name="address" required></p>
    <p>Introduce your phone number:
        <input type="number" id="phone" name="phone" required></p>
    <p>Introduce your email:
        <input type="text" id="email" name="email" required></p>
    <p>Upload a photograph of yourself:
        <input type="file" id="photo" name="photo" required></p>
    <button type="submit">Registrar</button>
</FORM>

<script>
    function toggleAdminCode() {
        var usertype = document.getElementById('usertype').value;
        var adminCodeSection = document.getElementById('admin-code-section');

        if (usertype === 'Adm') {
            adminCodeSection.style.display = 'block';
        } else {
            adminCodeSection.style.display = 'none';
        }
    }

    document.getElementById('registrationForm').addEventListener('submit', function(event) {
        var password = document.getElementById('password').value;
        var repassword = document.getElementById('repassword').value;
        var usertype = document.getElementById('role').value;
        var errorMessage = document.getElementById('error-message');
        var adminCode = document.getElementById('admin-code').value;

        // Clear previous error messages
        errorMessage.textContent = '';

        if (password !== repassword) {
            errorMessage.textContent = 'As senhas não correspondem. Por favor, tente novamente.';
            event.preventDefault(); // Impede o envio do formulário
        } else if (usertype !== 'Adm') {
            alert('Somente administradores podem ser registrados.');
            event.preventDefault(); // Impede o envio do formulário
        } else if (usertype === 'Adm' && adminCode.trim() === '') {
            errorMessage.textContent = 'Por favor, insira o código de administrador.';
            event.preventDefault(); // Impede o envio do formulário
        }
    });
</script>
