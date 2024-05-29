<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_name = $conn->real_escape_string($_POST['service']);
    $subOption = isset($_POST['subOption']) ? $conn->real_escape_string($_POST['subOption']) : null;
    $date = $conn->real_escape_string($_POST['date']);
    $time = $conn->real_escape_string($_POST['time']);
    $user_id = $_SESSION['user_id'];

    // Obtener el servicio_id
    $sql_service = "SELECT id FROM servicios WHERE nombre='$service_name' AND (subtipo='$subOption' OR subtipo IS NULL)";
    $result_service = $conn->query($sql_service);

    if ($result_service->num_rows > 0) {
        $row_service = $result_service->fetch_assoc();
        $service_id = $row_service['id'];

        // Verificar si ya existe una reserva para el mismo servicio, fecha y hora
        $sql_check = "SELECT * FROM reservas WHERE servicio_id='$service_id' AND fecha='$date' AND hora='$time'";
        $result_check = $conn->query($sql_check);

        if ($result_check->num_rows > 0) {
            echo "Ya existe una reserva para este servicio en la fecha y hora seleccionadas.";
        } else {
            // Insertar la nueva reserva
            $sql = "INSERT INTO reservas (usuario_id, servicio_id, subtipo, fecha, hora) VALUES ('$user_id', '$service_id', '$subOption', '$date', '$time')";
            if ($conn->query($sql) === TRUE) {
                header("Location: mis-reservas.php");
                exit;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    } else {
        echo "El servicio seleccionado no es válido.";
    }
}
?>
