<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['tipo_usuario'] !== 'secretaria') {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Obtener lista de usuarios
$sql_usuarios = "SELECT * FROM usuarios";
$result_usuarios = $conn->query($sql_usuarios);

$usuarios = [];
if ($result_usuarios->num_rows > 0) {
    while ($row = $result_usuarios->fetch_assoc()) {
        $usuarios[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="home-button-container">
        <img src="home-icon.png" alt="Home" onclick="location.href='index.php'">
    </div>
    <div class="container">
        <h2>Gestión de Usuarios</h2>

        <div class="section">
            <h3>Usuarios Existentes</h3>
            <div class="form-group">
                <label for="filterUser">Filtrar por Usuario:</label>
                <input type="text" id="filterUser" onkeyup="filterUsers()">
            </div>
            <table class="users-table" id="usersTable">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Tipo de Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr data-username="<?php echo htmlspecialchars($usuario['username']); ?>">
                            <td><?php echo htmlspecialchars($usuario['username']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['tipo_usuario']); ?></td>
                            <td>
                                <button onclick="deleteUser('<?php echo $usuario['username']; ?>')">Eliminar</button>
                                <button onclick="changePassword('<?php echo $usuario['username']; ?>')">Cambiar Contraseña</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h3>Crear Usuarios Masivos</h3>
            <div class="form-group">
                <label for="numUsers">Número de Usuarios a Crear:</label>
                <input type="number" id="numUsers" min="1">
            </div>
            <button type="button" onclick="generateBulkUserTable()">Generar Tabla</button>
            <div id="bulkUserSection" class="hidden">
                <table class="bulk-users-table" id="bulkUsersTable">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Contraseña</th>
                            <th>Tipo de Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Filas generadas dinámicamente -->
                    </tbody>
                </table>
                <button type="button" onclick="createBulkUsers()">Crear Usuarios</button>
            </div>
        </div>

        <div class="section">
            <h3>Crear Nuevo Usuario</h3>
            <form id="createUserForm">
                <div class="form-group">
                    <label for="newUsername">Usuario:</label>
                    <input type="text" id="newUsername" name="newUsername" required>
                </div>
                <div class="form-group">
                    <label for="newPassword">Contraseña:</label>
                    <input type="password" id="newPassword" name="newPassword" required>
                </div>
                <div class="form-group">
                    <label for="newUserType">Tipo de Usuario:</label>
                    <select id="newUserType" name="newUserType" required>
                        <option value="usuario">Usuario</option>
                        <option value="secretaria">Secretaría</option>
                    </select>
                </div>
                <div class="btn-container">
                    <button type="button" onclick="createUser()">Crear Usuario</button>
                    <button type="button" onclick="location.href='secretaria.php'" class="secondary">Atrás</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function filterUsers() {
            const filter = document.getElementById('filterUser').value.toLowerCase();
            const rows = document.querySelectorAll('#usersTable tbody tr');

            rows.forEach(row => {
                const username = row.getAttribute('data-username').toLowerCase();
                if (username.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function deleteUser(username) {
            if (confirm(`¿Está seguro de que desea eliminar al usuario ${username}?`)) {
                fetch('delete_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Usuario eliminado con éxito.');
                        location.reload();
                    } else {
                        alert('Error al eliminar el usuario.');
                    }
                });
            }
        }

        function changePassword(username) {
            const newPassword = prompt(`Ingrese la nueva contraseña para ${username}:`);
            if (newPassword) {
                fetch('change_password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username, newPassword })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Contraseña cambiada con éxito.');
                    } else {
                        alert('Error al cambiar la contraseña.');
                    }
                });
            }
        }

        function generateBulkUserTable() {
            const numUsers = document.getElementById('numUsers').value;
            const bulkUserSection = document.getElementById('bulkUserSection');
            const bulkUsersTable = document.getElementById('bulkUsersTable').getElementsByTagName('tbody')[0];

            bulkUsersTable.innerHTML = '';

            for (let i = 0; i < numUsers; i++) {
                const row = bulkUsersTable.insertRow();
                row.innerHTML = `
                    <td><input type="text" name="bulkUsername[]" required></td>
                    <td><input type="password" name="bulkPassword[]" required></td>
                    <td>
                        <select name="bulkUserType[]" required>
                            <option value="usuario">Usuario</option>
                            <option value="secretaria">Secretaría</option>
                        </select>
                    </td>
                `;
            }

            bulkUserSection.classList.remove('hidden');
            bulkUserSection.classList.add('visible');
        }

        function createBulkUsers() {
            const bulkUserInputs = document.querySelectorAll('#bulkUsersTable tbody tr');
            const users = [];

            bulkUserInputs.forEach(row => {
                const username = row.querySelector('input[name="bulkUsername[]"]').value.trim();
                const password = row.querySelector('input[name="bulkPassword[]"]').value.trim();
                const userType = row.querySelector('select[name="bulkUserType[]"]').value.trim();
                users.push({ username, password, userType });
            });

            fetch('create_bulk_users.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ users })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Usuarios creados con éxito.');
                    location.reload();
                } else {
                    alert('Error al crear usuarios: ' + data.message);
                }
            });
        }

        function createUser() {
            const username = document.getElementById('newUsername').value;
            const password = document.getElementById('newPassword').value;
            const userType = document.getElementById('newUserType').value;

            fetch('create_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password, userType })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Usuario creado con éxito.');
                    location.reload();
                } else {
                    alert('Error al crear usuario: ' + data.message);
                }
            });
        }
    </script>
</body>
</html>
