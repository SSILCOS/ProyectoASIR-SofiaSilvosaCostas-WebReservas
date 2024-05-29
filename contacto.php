<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "sofiasilvosacostas@gmail.com";
    $subject = "Usuario ID: " . $_SESSION['user_id']; // Asunto del correo con el número de usuario
    $message = $_POST['message'];
    $headers = "From: no-reply@centrodeportivo.com\r\n";

    if (mail($to, $subject, $message, $headers)) {
        $success = "Correo enviado correctamente.";
    } else {
        $error = "Hubo un error al enviar el correo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contacto</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
        }

        .button-container button {
            margin: 10px;
            padding: 15px 30px;
            font-size: 16px;
            cursor: pointer;
        }

        .home-button-container {
            margin-top: 20px;
            text-align: center;
        }

        .home-button-container img {
            width: 50px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="reservation-container">
        <h2>Contacto</h2>
        <?php 
        if (isset($success)) {
            echo "<p style='color: green;'>$success</p>";
        } elseif (isset($error)) {
            echo "<p style='color: red;'>$error</p>";
        }
        ?>
        <form action="contacto.php" method="post">
            <div class="form-group">
                <label for="message">Mensaje:</label>
                <textarea id="message" name="message" rows="4" required></textarea>
            </div>
            <button type="submit">Enviar Mensaje</button>
        </form>
        <div class="home-button-container">
            <a href="index.php">
                <img src="home-icon.png" alt="Home">
            </a>
        </div>
    </div>
</body>
</html>
