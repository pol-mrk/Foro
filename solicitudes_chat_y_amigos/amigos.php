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
    <title>Menú principal (Amigos)</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <header style="display: flex; justify-content: space-between;">
        <div>
            <h1>Mis Amigos</h1>
        </div>
        <div>
            <a href="solicitudes.php" style="color: white;">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="black" class="bi bi-person-check-fill" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                    <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                </svg>
            </a>
            <a href="../pagina_principal.php" style="color: white;">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="blacks" class="bi bi-house-fill" viewBox="0 0 16 16">
                    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                    <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
                </svg>
            </a>
        </div>
        
    </header>
    <div class="container">
        <?php
            $estado = 'amigo';

            // Consulta para obtener los amigos
            $sqlAmigos = "SELECT usuario1, usuario2, amigo.estado, usuario_amigo1.nombre AS amigo1, usuario_amigo2.nombre AS amigo2 
                          FROM amigo
                          INNER JOIN usuario AS usuario_amigo1 ON usuario_amigo1.id_usuario = amigo.usuario1
                          INNER JOIN usuario AS usuario_amigo2 ON usuario_amigo2.id_usuario = amigo.usuario2
                          WHERE (amigo.usuario1 = :usuario OR amigo.usuario2 = :usuario) AND amigo.estado = :estado";

            $stmtAmigos = $conn->prepare($sqlAmigos);
            $stmtAmigos->bindParam(':usuario', $mi_usuario);
            $stmtAmigos->bindParam(':estado', $estado);
            $stmtAmigos->execute();

            if ($stmtAmigos->rowCount() > 0) {
                // Mostrar la lista de amigos
                while ($row = $stmtAmigos->fetch(PDO::FETCH_ASSOC)) {
                    $usuario1 = $row['usuario1'];
                    $usuario2 = $row['usuario2'];
                    $estadoAmistad = $row['estado'];
                    $nombreAmigo1 = $row['amigo1'];
                    $nombreAmigo2 = $row['amigo2'];

                    // Mostrar el nombre del amigo y el estado de la conexión
                    if ($usuario1 != $mi_usuario) {
                        ?>
                        <div class="amigo">
                            <p><?php echo htmlspecialchars($nombreAmigo1); ?></p>
                            <div>
                                <a href="./chat.php?receptor=<?php echo urlencode(htmlspecialchars($usuario1)); ?>" style="color: white;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-chat-dots-fill" viewBox="0 0 16 16">
                                        <path d="M16 8c0 3.866-3.582 7-8 7a9 9 0 0 1-2.347-.306c-.584.296-1.925.864-4.181 1.234-.2.032-.352-.176-.273-.362.354-.836.674-1.95.77-2.966C.744 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7M5 8a1 1 0 1 0-2 0 1 1 0 0 0 2 0m4 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0m3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <?php
                    } elseif ($usuario2 != $mi_usuario) {
                        ?>
                        <div class="amigo">
                            <p><?php echo htmlspecialchars($nombreAmigo2); ?></p>
                            <div>
                                <a href="./chat.php?receptor=<?php echo urlencode(htmlspecialchars($usuario2)); ?>" style="color: white;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-chat-dots-fill" viewBox="0 0 16 16">
                                        <path d="M16 8c0 3.866-3.582 7-8 7a9 9 0 0 1-2.347-.306c-.584.296-1.925.864-4.181 1.234-.2.032-.352-.176-.273-.362.354-.836.674-1.95.77-2.966C.744 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7M5 8a1 1 0 1 0-2 0 1 1 0 0 0 2 0m4 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0m3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                }
            } else {
                echo "<p>No tienes amigos xd</p>";
            }
        ?>
    </div>
</body>
</html>
<?php
    }
?>
