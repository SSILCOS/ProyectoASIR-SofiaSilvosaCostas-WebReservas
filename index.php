<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : ''; // Manejar el caso en que tipo_usuario no esté definido
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página de Inicio</title>
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

        #secretaria-button {
            display: none; /* Inicialmente oculto */
        }
    </style>
</head>
<body>
    <div class="button-container">
        <button onclick="location.href='reserva.php'">Reservar</button>
        <button onclick="location.href='mis-reservas.php'">Mis Reservas</button>
        <button onclick="location.href='contacto.php'">Contacto</button>
        <button id="secretaria-button" onclick="location.href='secretaria.php'">Secretaría</button>
        <button onclick="location.href='logout.php'">Salir</button>
    </div>

    <script>
        // Determinar si el usuario es de secretaría
        var esSecretaria = "<?php echo $tipo_usuario; ?>" === "secretaria";

        if (esSecretaria) {
            document.getElementById('secretaria-button').style.display = 'block';
        }
    </script>
</body>
</html>
