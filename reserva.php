<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Obtener la lista de servicios y subtipos
$sql_servicios = "SELECT * FROM servicios";
$result_servicios = $conn->query($sql_servicios);

$servicios = [];
if ($result_servicios->num_rows > 0) {
    while ($row = $result_servicios->fetch_assoc()) {
        $servicios[$row['nombre']][] = $row['subtipo'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reserva de Servicios</title>
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
        <h2>Reservar Servicio</h2>
        <form id="reservationForm" action="process_reservation.php" method="post">
            <label for="service">Servicio:</label>
            <select name="service" id="service" required>
                <option value="">Seleccione un servicio...</option>
                <?php foreach ($servicios as $nombre => $subtipos): ?>
                    <option value="<?php echo htmlspecialchars($nombre); ?>"><?php echo htmlspecialchars($nombre); ?></option>
                <?php endforeach; ?>
            </select>

            <div id="additionalOptions" style="display:none;">
                <label for="subOption">Subservicio:</label>
                <select name="subOption" id="subOption" required>
                    <!-- Las opciones de subservicio se llenarán dinámicamente con JavaScript -->
                </select>
            </div>

            <div id="dateTimeFields" style="display:none;">
                <label for="date">Fecha:</label>
                <input type="date" id="date" name="date" required min="">

                <label for="time">Hora:</label>
                <input type="time" id="time" name="time" required list="time-options">
                <datalist id="time-options">
                </datalist>
            </div>

            <button type="submit">Confirmar Reserva</button>
        </form>
    </div>

    <script>
        const servicios = <?php echo json_encode($servicios); ?>;
    </script>
    <script src="scripts/realizar_reserva.js"></script>
</body>
</html>
