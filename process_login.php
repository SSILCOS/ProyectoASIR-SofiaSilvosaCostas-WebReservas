<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['contrasena'];

        // Verificar la contraseña
        if (password_verify($password, $hashed_password)) {
            // Guardar información del usuario en la sesión
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $username;
            $_SESSION['loggedin'] = true;
            $_SESSION['tipo_usuario'] = $row['tipo_usuario'];

            // Redirigir al usuario a la página principal
            header("Location: index.php");
            exit;
        } else {
            echo "Usuario o contraseña inválidos";
        }
    } else {
        echo "Usuario o contraseña inválidos";
    }
}
?>
