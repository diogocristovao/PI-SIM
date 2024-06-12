<?php
session_start();
if ($_SESSION['user_type'] != 'ADMIN') {
    header("Location: login.php");
    exit();
}

$connect = mysqli_connect("localhost", "root", "", "pi-sim");

// Verificar se a conexão foi bem sucedida
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = hash("sha256", $_POST['password']);
    $creation_date = date("Y-m-d H:i:s");
    $user_type = $_POST['user_type'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    // Assuming photo upload is handled elsewhere and $photo is set
    $photo = $_FILES['photo']['tmp_name'];

    // Inserir o novo usuário na tabela users
    $stmt = $connect->prepare("INSERT INTO users (NAME, USERNAME, PASSWORD, CREATION_DATE, USER_TYPE, ADRESS, PHONE_NUMBER, EMAIL, PHOTO) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssiss", $name, $username, $password, $creation_date, $user_type, $address, $phone_number, $email, $photo_data);

    $photo_data = null;
    if ($photo) {
        $photo_data = file_get_contents($photo);
    }

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id; // Obter o ID do novo usuário inserido

        // Inserir na tabela patients se o user type for 'P'
        if ($user_type == 'P') {
            $locality = $_POST['locality'];
            $district = $_POST['district'];
            $birth_date = $_POST['birth_date'];
            $gender = $_POST['gender'];
            $nif = $_POST['nif'];
            $allergies = $_POST['allergies'];

            $stmt = $connect->prepare("INSERT INTO patients (ID, LOCALITY, DISTRICT, EMAIL, BIRTH_DATE, GENDER, NIF, ALLERGIES) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssss", $user_id, $locality, $district, $email, $birth_date, $gender, $nif, $allergies);
            if (!$stmt->execute()) {
                die("Error inserting patient: " . $stmt->error);
            }
        }

        // Inserir na tabela doctors se o user type for 'M'
        if ($user_type == 'M') {
            $speciality = $_POST['speciality'];

            $stmt = $connect->prepare("INSERT INTO doctors (ID, SPECIALITY) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $speciality);
            if (!$stmt->execute()) {
                die("Error inserting doctor: " . $stmt->error);
            }
        }

        echo "User successfully added.";
    } else {
        die("Error inserting user: " . $stmt->error);
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add User</title>
    <script>
        function toggleFields() {
            var userType = document.getElementById("user_type").value;
            document.getElementById("patient_fields").style.display = userType === 'P' ? 'block' : 'none';
            document.getElementById("doctor_fields").style.display = userType === 'M' ? 'block' : 'none';
        }
    </script>
</head>
<body>
<h2 class="text-center">Add User</h2>
<form method="post" action="" enctype="multipart/form-data">
    <div>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <label for="user_type">User Type:</label>
        <select id="user_type" name="user_type" required onchange="toggleFields()">
            <option value="">Select User Type</option>
            <option value="P">Patient</option>
            <option value="M">Doctor</option>
            <option value="ADMIN">Administrator</option>
        </select>
    </div>
    <div>
        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required>
    </div>
    <div>
        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" required>
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="photo">Photo:</label>
        <input type="file" id="photo" name="photo">
    </div>

    <!-- Patient-specific fields -->
    <div id="patient_fields" style="display:none;">
        <div>
            <label for="locality">Locality:</label>
            <input type="text" id="locality" name="locality">
        </div>
        <div>
            <label for="district">District:</label>
            <input type="text" id="district" name="district">
        </div>
        <div>
            <label for="birth_date">Birth Date:</label>
            <input type="date" id="birth_date" name="birth_date">
        </div>
        <div>
            <label for="gender">Gender:</label>
            <select id="gender" name="gender">
                <option value="M">Male</option>
                <option value="F">Female</option>
            </select>
        </div>
        <div>
            <label for="nif">NIF:</label>
            <input type="text" id="nif" name="nif">
        </div>
        <div>
            <label for="allergies">Allergies:</label>
            <textarea id="allergies" name="allergies"></textarea>
        </div>
    </div>

    <!-- Doctor-specific fields -->
    <div id="doctor_fields" style="display:none;">
        <label for="speciality">Speciality:</label>
        <input type="text" id="speciality" name="speciality">
    </div>

    <button type="submit" name="add_user">Add User</button>
</form>
</body>
</html>
