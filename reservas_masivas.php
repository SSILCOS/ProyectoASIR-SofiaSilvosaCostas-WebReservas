<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['tipo_usuario'] !== 'secretaria') {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Obtener la lista de servicios y subservicios
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
    <title>Reservas Masivas</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group select, .form-group input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-group input[type="date"] {
            padding-right: 40px;
        }

        .btn-group {
            text-align: center;
            margin-top: 20px;
        }

        .btn-group button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }

        .btn-group button.primary {
            background: #007bff;
            color: white;
        }

        .btn-group button.secondary {
            background: #6c757d;
            color: white;
        }

        .selected-dates {
            margin-top: 20px;
            text-align: center;
        }

        .selected-dates span {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            margin: 2px;
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
    <div class="container">
        <h2>Realizar Reserva</h2>
        <div class="form-group">
            <label for="service">Servicio:</label>
            <select id="service" onchange="updateSubOptions()">
                <option value="">Seleccione un servicio...</option>
                <?php foreach ($servicios as $nombre => $subtipos): ?>
                    <option value="<?php echo htmlspecialchars($nombre); ?>"><?php echo htmlspecialchars($nombre); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="subOption">Subtipo:</label>
            <select id="subOption">
                <option value="">Seleccione un subservicio...</option>
            </select>
        </div>
        <div class="form-group">
            <label for="date">Fecha:</label>
            <input type="date" id="date">
        </div>
        <div class="btn-group">
            <button class="primary" onclick="addDate()">Añadir Día</button>
            <button class="secondary" onclick="clearDates()">Cerrar</button>
        </div>
        <div class="selected-dates" id="selectedDates"></div>
        <div class="form-group">
            <label for="time">Hora:</label>
            <select id="time">
                <!-- Horarios se llenarán dinámicamente -->
            </select>
        </div>
        <div class="btn-group">
            <button class="primary" onclick="confirmReservations()">Añadir Reserva Masiva</button>
            <button class="secondary" onclick="location.href='secretaria.php'">Atrás</button>
        </div>
    </div>

    <script>
        const servicios = <?php echo json_encode($servicios); ?>;
        let selectedDates = [];

        function updateSubOptions() {
            const service = document.getElementById('service').value;
            const subOptionSelect = document.getElementById('subOption');
            const timeSelect = document.getElementById('time');
            subOptionSelect.innerHTML = '<option value="">Seleccione un subservicio...</option>';
            timeSelect.innerHTML = '';

            if (servicios[service]) {
                servicios[service].forEach(subOption => {
                    const option = document.createElement('option');
                    option.value = subOption;
                    option.text = subOption;
                    subOptionSelect.appendChild(option);
                });

                updateAvailableTimes(service);
            }
        }

        function updateAvailableTimes(service) {
            const timeSelect = document.getElementById('time');
            timeSelect.innerHTML = '';

            let times = [];
            if (service === 'Área de Barbacoa' || service === 'Mesa de Picnic') {
                times = [
                    '12:00-18:00', '18:00-23:00'
                ];
            } else {
                times = [
                    '08:00', '09:30', '11:00', '12:30', '14:00', '15:30',
                    '17:00', '18:30', '20:00', '21:30'
                ];
            }

            times.forEach(time => {
                const option = document.createElement('option');
                option.value = time;
                option.text = time;
                timeSelect.appendChild(option);
            });
        }

        function addDate() {
            const dateInput = document.getElementById('date');
            const date = dateInput.value;

            if (date && !selectedDates.includes(date)) {
                selectedDates.push(date);
                updateSelectedDates();
            }
        }

        function clearDates() {
            selectedDates = [];
            updateSelectedDates();
        }

        function updateSelectedDates() {
            const selectedDatesContainer = document.getElementById('selectedDates');
            selectedDatesContainer.innerHTML = '';
            selectedDates.forEach(date => {
                const dateSpan = document.createElement('span');
                dateSpan.textContent = date;
                selectedDatesContainer.appendChild(dateSpan);
            });
        }

        function confirmReservations() {
            const service = document.getElementById('service').value;
            const subOption = document.getElementById('subOption').value;
            const hour = document.getElementById('time').value;

            if (!service || !hour) {
                alert('Debe seleccionar un servicio y una hora válida.');
                return;
            }

            if (selectedDates.length === 0) {
                alert('Debe seleccionar al menos una fecha.');
                return;
            }

            fetch('process_mass_reservation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    service: service,
                    subOption: subOption,
                    dates: selectedDates,
                    hour: hour
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Reservas realizadas con éxito.');
                    window.location.href = 'reservas_masivas.php';
                } else {
                    alert('Error al realizar las reservas.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un error al realizar las reservas. Por favor, intenta de nuevo.');
            });
        }
    </script>
</body>
</html>
