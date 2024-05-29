<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['tipo_usuario'] !== 'secretaria') {
    header("Location: login.php");
    exit;
}

$tipo_usuario = $_SESSION['tipo_usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Secretaría</title>
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
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .home-button-container img {
            width: 50px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="home-button-container">
        <img src="home-icon.png" alt="Home" onclick="location.href='index.php'">
    </div>
    <div class="button-container">
        <h1>Bienvenido al Panel de Secretaría</h1>
        <button onclick="location.href='calendario.php'">Calendario</button>
        <button onclick="location.href='reservas_masivas.php'">Reservas Masivas</button>
        <button onclick="location.href='gestion_usuarios.php'">Gestión de Usuarios</button>
    </div>
</body>
</html>
