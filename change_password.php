<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $conn->real_escape_string($data['username']);
    $newPassword = password_hash($conn->real_escape_string($data['newPassword']), PASSWORD_BCRYPT);

    $sql = "UPDATE usuarios SET contrasena='$newPassword' WHERE username='$username'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al cambiar la contraseña.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
