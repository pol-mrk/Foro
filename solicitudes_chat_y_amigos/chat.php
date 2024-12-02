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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body class="cuerpo">
    <div class="caja-contenedor">
        <header class="encabezado">
            <h1>Chat</h1>
            <a href="amigos.php" style="color: #075E54;">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="white" class="bi bi-people-fill" viewBox="0 0 16 16">
                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                </svg>
            </a>
            <a href="solicitudes.php" style="color: #075E54;">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="white" class="bi bi-person-check-fill" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                    <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                </svg>
            </a>
            <a href="../pagina_principal.php" style="color: #075E54;">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="white" class="bi bi-house-fill" viewBox="0 0 16 16">
                    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                    <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
                </svg>
            </a>
        </header>
        <div class="contenedor-mensajes">
            <?php
                // Mostrar mensajes usando PDO
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

                    // Mostrar los mensajes
                    foreach ($resultado as $fila) {
                        $usuarioEnvia = $fila['emisor'];
                        $usuarioRecibe = $fila['receptor'];
                        $mensaje_chat = $fila['mensaje_chat'];
                        $nombreEmisor = $fila['nombre_emisor'];
                        $fechaMensaje = $fila['fecha_chat'];

                        // Determinar si el mensaje es del usuario emisor o del receptor
                        $claseMensaje = ($usuarioEnvia == $emisor) ? 'mensaje-mio' : 'mensaje-suyo';
                        echo "<p class='$claseMensaje'><strong>" . htmlspecialchars($nombreEmisor) . ":</strong> " . htmlspecialchars($mensaje_chat) . "</p>";
                    }

                } else {
                    echo "<p>Todavía no hay mensajes.</p>";
                }
            ?>
            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {

                    $error = 0;

                    $mensaje = htmlspecialchars($_POST["mensaje"]);

                    if (empty($mensaje)) {
                        $error = 1;
                    } else {
                        $error = 0;
                        // Insertar mensaje usando PDO
                        $sqlEnviar = "INSERT INTO mensaje (emisor, receptor, mensaje_chat) VALUES (:emisor, :receptor, :mensaje)";
                        $stmtEnviar = $conn->prepare($sqlEnviar);
                        $stmtEnviar->bindParam(':emisor', $emisor);
                        $stmtEnviar->bindParam(':receptor', $receptor);
                        $stmtEnviar->bindParam(':mensaje', $mensaje);
                        $stmtEnviar->execute();
                        $stmtEnviar = null;

                        // Redireccionar después de procesar el formulario para evitar reenvío
                        header("Location: " . $_SERVER['PHP_SELF'] . "?receptor=" . urlencode($receptor));
                        exit();
                    }
                }
            ?>
        </div>
    </div>
    <div class="caja-mensaje">
        <form method="POST">
            <input type="text" name="mensaje" class="input-mensaje" id="mensaje">
            <input type="submit" class="boton-enviar" name="Enviar" value="Enviar">           
        </form>
        <?php
            if (isset($error) && $error == 1) {
                echo '<p style="color: red;">El mensaje no puede estar vacío.</p>';
            }
        ?>
    </div>
</body>
</html>
<?php
}
?>
