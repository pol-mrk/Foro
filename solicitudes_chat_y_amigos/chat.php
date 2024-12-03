<?php
include_once("../conexion.php");

session_start();

if (!isset($_SESSION['loggedin']) && !isset($_SESSION['id_usuario'])) {
    header('Location: ' . './login/login.php');
    exit();
} elseif (!isset($_GET['receptor'])) {
    header('Location: ' . './index.php');
    exit();
} else {
    $emisor = htmlspecialchars($_SESSION['id_usuario']);
    $receptor = isset($_GET['receptor']) ? htmlspecialchars($_GET['receptor']) : "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header with Navigation -->
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
                            <a class="nav-link" href="solicitudes.php">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="black" class="bi bi-person-check-fill" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                                    <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
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

    <!-- Chat Container -->
    <div class="container mt-4">
        <h2 class="text-center mb-4">Chat</h2>

        <div class="chat-box mb-4" style="max-height: 400px; overflow-y: auto;">
            <?php
                // Fetch and display messages
                $sqlMostrar = "SELECT emisor, receptor, mensaje_chat, fecha_chat, usuario_emisor.nombre AS nombre_emisor, usuario_receptor.nombre AS nombre_receptor 
                               FROM mensaje
                               INNER JOIN usuario AS usuario_emisor ON usuario_emisor.id_usuario = mensaje.emisor
                               INNER JOIN usuario AS usuario_receptor ON usuario_receptor.id_usuario = mensaje.receptor
                               WHERE (mensaje.emisor = :emisor AND mensaje.receptor = :receptor) 
                               OR (mensaje.emisor = :receptor AND mensaje.receptor = :emisor)
                               ORDER BY fecha_chat DESC";
                
                $stmtMostrar = $conn->prepare($sqlMostrar);
                $stmtMostrar->bindParam(':emisor', $emisor);
                $stmtMostrar->bindParam(':receptor', $receptor);
                $stmtMostrar->execute();

                $resultado = $stmtMostrar->fetchAll(PDO::FETCH_ASSOC);

                if (count($resultado) > 0) {
                    // Display the messages
                    foreach ($resultado as $fila) {
                        $usuarioEnvia = $fila['emisor'];
                        $usuarioRecibe = $fila['receptor'];
                        $mensaje_chat = $fila['mensaje_chat'];
                        $nombreEmisor = $fila['nombre_emisor'];
                        $fechaMensaje = $fila['fecha_chat'];

                        // Determine if the message is from the sender or the receiver
                        $claseMensaje = ($usuarioEnvia == $emisor) ? 'alert alert-primary' : 'alert alert-secondary';
                        echo "<p class='$claseMensaje'><strong>" . htmlspecialchars($nombreEmisor) . ":</strong> " . htmlspecialchars($mensaje_chat) . " <small class='text-muted'>" . htmlspecialchars($fechaMensaje) . "</small></p>";
                    }
                } else {
                    echo "<p>No messages yet.</p>";
                }
            ?>
        </div>

        <!-- Message Input Form -->
        <form method="POST">
            <div class="input-group">
                <input type="text" name="mensaje" class="form-control" id="mensaje" placeholder="Escribe un mensaje...">
                <button type="submit" class="btn btn-primary" name="Enviar">Enviar</button>
            </div>
        </form>
        
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $error = 0;
                $mensaje = htmlspecialchars($_POST["mensaje"]);

                if (empty($mensaje)) {
                    $error = 1;
                } else {
                    $error = 0;
                    // Insert message into the database
                    $sqlEnviar = "INSERT INTO mensaje (emisor, receptor, mensaje_chat) VALUES (:emisor, :receptor, :mensaje)";
                    $stmtEnviar = $conn->prepare($sqlEnviar);
                    $stmtEnviar->bindParam(':emisor', $emisor);
                    $stmtEnviar->bindParam(':receptor', $receptor);
                    $stmtEnviar->bindParam(':mensaje', $mensaje);
                    $stmtEnviar->execute();

                    // Redirect to prevent resubmission
                    header("Location: " . $_SERVER['PHP_SELF'] . "?receptor=" . urlencode($receptor));
                    exit();
                }
            }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
}
?>
