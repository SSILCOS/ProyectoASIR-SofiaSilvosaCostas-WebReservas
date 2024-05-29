<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $service = $conn->real_escape_string($data['service']);
    $subOption = isset($data['subOption']) ? $conn->real_escape_string($data['subOption']) : null;
    $dates = $data['dates'];
    $hour = $conn->real_escape_string($data['hour']);
    $user_id = $_SESSION['user_id'];

    foreach ($dates as $date) {
        $date = $conn->real_escape_string($date);

        // Obtener el servicio_id
        $sql_service = "SELECT id FROM servicios WHERE nombre='$service' AND (subtipo='$subOption' OR subtipo IS NULL)";
        $result_service = $conn->query($sql_service);

        if ($result_service->num_rows > 0) {
            $row_service = $result_service->fetch_assoc();
            $service_id = $row_service['id'];

            // Insertar la nueva reserva
            $sql = "INSERT INTO reservas (usuario_id, servicio_id, subtipo, fecha, hora) VALUES ('$user_id', '$service_id', '$subOption', '$date', '$hour')";
            $conn->query($sql);
        }
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
