<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT reservas.id, servicios.nombre AS servicio, reservas.fecha, reservas.hora, reservas.subtipo
        FROM reservas
        JOIN servicios ON reservas.servicio_id = servicios.id
        WHERE reservas.usuario_id='$user_id'";
$result = $conn->query($sql);

$reservas = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $reservas[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Reservas</title>
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
    <div class="reservation-container">
        <h2>Mis Reservas</h2>
        <table id="reservationsTable">
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>Subtipo</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservas)): ?>
                    <tr>
                        <td colspan="5">No tienes reservas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reservas as $reserva): ?>
                        <?php
                            $reservationDateTime = new DateTime($reserva['fecha'] . ' ' . $reserva['hora']);
                            $currentDateTime = new DateTime();
                            $interval = $currentDateTime->diff($reservationDateTime);
                            $canCancel = $interval->invert == 0 && ($interval->days > 0 || $interval->h >= 2);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reserva['servicio']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['subtipo'] ? $reserva['subtipo'] : 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($reserva['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['hora']); ?></td>
                            <td>
                                <?php if ($canCancel): ?>
                                    <button onclick="confirmCancel(<?php echo $reserva['id']; ?>)">Anular</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="scripts/mis-reservas.js"></script>
</body>
</html>
