<?php
include_once("../conexion.php");
session_start();

if (!isset($_SESSION['loggedin']) && !isset($_SESSION['id_usuario'])) {
    header('Location: ' . './login/login.php');
    exit();
} else {
    $mi_usuario = htmlspecialchars($_SESSION['id_usuario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes de Amistad</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header con barra de navegación -->
    <header class="bg-dark text-white">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="amigos.php">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="black" class="bi bi-people-fill" viewBox="0 0 16 16">
                                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                                </svg>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../pagina_principal.php">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="black" class="bi bi-house-fill" viewBox="0 0 16 16">
                                    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                                    <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
                                </svg>
                            </a>
                        </li>
                        <form method="POST" action="login/logout.php" class="ms-3">
                            <button type="submit" class="btn btn-outline-danger">Cerrar sesión</button>
                        </form>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container mt-4">
        <h2 class="text-center mb-4">Solicitudes de Amistad</h2>

        <?php
            $estado = 'solicitado';

            // Consulta para obtener las solicitudes de amistad
            $sqlSolicitudes = "SELECT emisor, receptor, amigo.estado, usuario_amigo1.nombre AS amigo1, usuario_amigo2.nombre AS amigo2 
                               FROM amigo
                               INNER JOIN usuario AS usuario_amigo1 ON usuario_amigo1.id_usuario = amigo.emisor
                               INNER JOIN usuario AS usuario_amigo2 ON usuario_amigo2.id_usuario = amigo.receptor
                               WHERE amigo.receptor = :usuario AND amigo.estado = :estado";

            $stmtSolicitudes = $conn->prepare($sqlSolicitudes);
            $stmtSolicitudes->bindParam(':usuario', $mi_usuario);
            $stmtSolicitudes->bindParam(':estado', $estado);
            $stmtSolicitudes->execute();

            if ($stmtSolicitudes->rowCount() > 0) {
                // Mostrar las solicitudes de amistad
                while ($row = $stmtSolicitudes->fetch(PDO::FETCH_ASSOC)) {
                    $emisor = $row['emisor'];
                    $receptor = $row['receptor'];
                    $estadoAmistad = $row['estado'];
                    $nombreAmigo1 = $row['amigo1'];
                    $nombreAmigo2 = $row['amigo2'];

                    // Mostrar la solicitud
                    echo "<div class='p-3 mb-3 bg-light rounded'>
                            <p><strong>" . htmlspecialchars($nombreAmigo1) . "</strong> te ha enviado una solicitud.</p>
                            <form method='POST'>
                                <input type='hidden' name='id_amigo' value='" . htmlspecialchars($receptor) . "'>
                                <button type='submit' class='btn btn-success' name='Aceptar'>Aceptar</button>
                                <button type='submit' class='btn btn-danger' name='Rechazar'>Rechazar</button>
                            </form>
                          </div>";
                }

                // Procesar la aceptación de la solicitud
                if (isset($_POST['Aceptar'])) {
                    $idAmigo = $_POST['id_amigo'];
                    $estadoAmigo = 'amigo';
                    $sqlRelacion = "UPDATE amigo SET estado = :estado WHERE receptor = :id_amigo";
                    $stmtRelacion = $conn->prepare($sqlRelacion);
                    $stmtRelacion->bindParam(':estado', $estadoAmigo);
                    $stmtRelacion->bindParam(':id_amigo', $idAmigo);
                    $stmtRelacion->execute();

                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }

                // Procesar el rechazo de la solicitud
                if (isset($_POST['Rechazar'])) {
                    $idAmigo = $_POST['id_amigo'];
                    $estadoRechazado = 'rechazado';
                    $sqlRelacion = "UPDATE amigo SET estado = :estado WHERE receptor = :id_amigo";
                    $stmtRelacion = $conn->prepare($sqlRelacion);
                    $stmtRelacion->bindParam(':estado', $estadoRechazado);
                    $stmtRelacion->bindParam(':id_amigo', $idAmigo);
                    $stmtRelacion->execute();

                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }
            } else {
                echo "<p>No tienes solicitudes de amistad.</p>";
            }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
}
?>
