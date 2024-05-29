<?php
include 'db.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No estás autenticado.']);
    exit;
}

$reservation_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$reservation_id) {
    echo json_encode(['success' => false, 'message' => 'ID de reserva no válido.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$tipo_usuario = $_SESSION['tipo_usuario'];

// Obtener la fecha y hora de la reserva
$sql = "SELECT fecha, hora FROM reservas WHERE id='$reservation_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $reservationDateTime = new DateTime($row['fecha'] . ' ' . $row['hora']);
    $currentDateTime = new DateTime();
    $interval = $currentDateTime->diff($reservationDateTime);

    // Verificar si el usuario es un usuario regular y si quedan menos de 2 horas para la reserva
    if ($tipo_usuario !== 'secretaria' && ($interval->invert == 1 || ($interval->days == 0 && $interval->h < 2))) {
        echo json_encode(['success' => false, 'message' => 'No puedes anular la reserva con menos de 2 horas de antelación.']);
        exit;
    }

    // Eliminar la reserva
    $sql_delete = "DELETE FROM reservas WHERE id='$reservation_id'";
    if ($conn->query($sql_delete) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Reserva anulada con éxito.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al anular la reserva.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Reserva no encontrada o no pertenece al usuario.']);
}
?>
