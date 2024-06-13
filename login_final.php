<?php
session_start();
$connect = mysqli_connect("localhost", "root", "", "sim");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE USERNAME = '$username' AND PASSWORD = '$password'";
    $result = mysqli_query($connect, $query);
    $row = mysqli_fetch_array($result);

    if ($row) { // Verifica se a consulta retornou um usuário
        $_SESSION['user_id'] = $row['ID'];
        $_SESSION['user_type'] = $row['USER_TYPE'];

        switch ($row['USER_TYPE']) {
            case 'ADMIN':
                header("Location: admin_final.php");
                exit();
            case 'M':
                header("Location: medico.php");
                exit();
            case 'P':
                header("Location: paciente.php");
                exit();
        }
    } else {
        $error = "Username ou password inválidos.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff; /* azul claro */
            color: #000;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
            box-sizing: border-box;
        }
        .container {
            background-color: #fff; /* branco */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
            margin-bottom: 1cm;
        }
        h2 {
            color: #4682b4; /* azul */
        }
        input[type="text"], input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #4682b4; /* azul */
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #5a9bd4; /* azul mais claro */
        }
        .error {
            color: red;
        }
        .info {
            background-color: #4682b4; /* azul */
            color: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .info h2 {
            margin: 0;
        }
        .info p {
            margin: 10px 0;
        }
        .bold-center {
            font-weight: bold;
            text-align: center;
            margin: 0;
        }
        a {
            color: #4682b4;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .footer {
            width: 100%;
            height: 1cm;
            background-color: #4682b4; /* azul */
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            bottom: 0;
        }
    </style>
</head>
<body>
<div class="info">
    <p class="bold-center">PI-SIM - Penso Inteligente</p>
    <h2>Quem somos nós?</h2>
    <p> PI-SIM é uma plataforma para monitorização de pacientes através da utilização de um penso ativo, capaz de monitorizar feridas pós-operatórias fazendo a medição de parâmetros como condutividade da pele, temperatura, pH, entre outros.</p>
</div>
<div class="container">
    <h2>Login</h2>
    <form method="POST" action="">
        <p><input type="text" name="username" placeholder="Username"></p>
        <p><input type="password" name="password" placeholder="Password"></p>
        <p><input type="submit" name="login" value="Login"></p>
    </form>
    <p>Ainda não tem uma conta? <a href="registration.php">Registe-se</a></p>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
</div>
<div class="footer">
    &copy; SISTEMAS DE INFORMAÇÃO MÉDICA - 2023-2024
</div>
</body>
</html>
