<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['tipo_usuario'] !== 'secretaria') {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Obtener la lista de servicios
$sql_servicios = "SELECT DISTINCT nombre FROM servicios";
$result_servicios = $conn->query($sql_servicios);

$servicios = [];
if ($result_servicios->num_rows > 0) {
    while ($row = $result_servicios->fetch_assoc()) {
        $servicios[] = $row['nombre'];
    }
}

// Obtener todas las reservas
$sql_reservas = "SELECT reservas.id, usuarios.username, servicios.nombre AS servicio, reservas.subtipo, reservas.fecha, reservas.hora 
                 FROM reservas
                 JOIN usuarios ON reservas.usuario_id = usuarios.id
                 JOIN servicios ON reservas.servicio_id = servicios.id";
$result_reservas = $conn->query($sql_reservas);

$reservas = [];
if ($result_reservas->num_rows > 0) {
    while ($row = $result_reservas->fetch_assoc()) {
        $reservas[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calendario de Reservas</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
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

        .filter-container {
            display: flex;
            justify-content: center;
            margin: 20px;
        }

        .filter-container select, .filter-container input {
            margin: 0 10px;
            padding: 10px;
            font-size: 16px;
        }

        .reservation-table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        .reservation-table th, .reservation-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .reservation-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="home-button-container">
        <img src="home-icon.png" alt="Home" onclick="location.href='index.php'">
    </div>
    <div class="button-container">
        <h1>Calendario de Reservas</h1>
        <div class="filter-container">
            <select id="filterService">
                <option value="">Todos los Servicios</option>
                <?php foreach ($servicios as $servicio): ?>
                    <option value="<?php echo htmlspecialchars($servicio); ?>"><?php echo htmlspecialchars($servicio); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" id="filterDate">
            <button onclick="filterReservations()">Filtrar</button>
        </div>
        <table class="reservation-table" id="reservationsTable">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Servicio</th>
                    <th>Subservicio</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservas)): ?>
                    <tr>
                        <td colspan="6">No hay reservas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reservas as $reserva): ?>
                        <tr data-service="<?php echo htmlspecialchars($reserva['servicio']); ?>" data-date="<?php echo htmlspecialchars($reserva['fecha']); ?>">
                            <td><?php echo htmlspecialchars($reserva['username']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['servicio']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['subtipo'] ? $reserva['subtipo'] : 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($reserva['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['hora']); ?></td>
                            <td>
                                <button onclick="confirmCancel(<?php echo $reserva['id']; ?>)">Anular</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <button onclick="location.href='secretaria.php'">Atrás</button>
    </div>

    <script>
        function filterReservations() {
            const filterService = document.getElementById('filterService').value.toLowerCase();
            const filterDate = document.getElementById('filterDate').value;

            const rows = document.querySelectorAll('#reservationsTable tbody tr');
            rows.forEach(row => {
                const service = row.getAttribute('data-service').toLowerCase();
                const date = row.getAttribute('data-date');

                const serviceMatch = !filterService || service.includes(filterService);
                const dateMatch = !filterDate || date === filterDate;

                if (serviceMatch && dateMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function confirmCancel(reservationId) {
            if (confirm('¿Estás seguro de que deseas anular esta reserva?')) {
                fetch(`cancel_reservation.php?id=${reservationId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Reserva anulada con éxito.');
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Hubo un error al anular la reserva. Por favor, intenta de nuevo.');
                    });
            }
        }
    </script>
</body>
</html>
