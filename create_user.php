<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $conn->real_escape_string($data['username']);
    $password = password_hash($conn->real_escape_string($data['password']), PASSWORD_BCRYPT);
    $userType = $conn->real_escape_string($data['userType']);

    // Verificar si el usuario ya existe
    $sql_check = "SELECT id FROM usuarios WHERE username='$username'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'El usuario ya existe.']);
        exit;
    }

    $sql = "INSERT INTO usuarios (username, contrasena, tipo_usuario) VALUES ('$username', '$password', '$userType')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear usuario.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
