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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

    <header style="display: flex; justify-content: space-between;">

        <div>
            <h1>Solicitudes</h1>
        </div>

        <div>
            
            <a href="amigos.php" style="color: white;">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="black" class="bi bi-people-fill" viewBox="0 0 16 16">
                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
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


    <?php

        $estado = 'solicitado';

        // Consulta para obtener las solicitudes de amistad
        $sqlAmigos = "SELECT usuario1, usuario2, amigo.estado, usuario_amigo1.username AS amigo1, usuario_amigo2.username AS amigo2 
                      FROM amigo
                      INNER JOIN usuario AS usuario_amigo1 ON usuario_amigo1.id_usuario = amigo.usuario1
                      INNER JOIN usuario AS usuario_amigo2 ON usuario_amigo2.id_usuario = amigo.usuario2
                      WHERE amigo.usuario2 = :usuario AND amigo.estado = :estado";

        $stmtAmigos = $conn->prepare($sqlAmigos);
        $stmtAmigos->bindParam(':usuario', $mi_usuario);
        $stmtAmigos->bindParam(':estado', $estado);
        $stmtAmigos->execute();

        if ($stmtAmigos->rowCount() > 0) {

            // Mostrar la lista de solicitudes de amistad
            while ($row = $stmtAmigos->fetch(PDO::FETCH_ASSOC)) {
                $usuario1 = $row['usuario1'];
                $usuario2 = $row['usuario2'];
                $estadoAmistad = $row['estado'];
                $nombreAmigo1 = $row['amigo1'];
                $nombreAmigo2 = $row['amigo2'];

                echo "<p><strong>" . htmlspecialchars($nombreAmigo1) . "</strong></p>";
                echo '<form method="POST">
                        <input type="hidden" name="id_amigo" value="' . htmlspecialchars($usuario2) . '">
                        <input type="submit" value="Aceptar" name="Aceptar">
                        <input type="submit" value="Rechazar" name="Rechazar">
                    </form>';
            }

            // Procesar el formulario de Aceptar
            if (isset($_POST['Aceptar'])) {

                $idAmigo = $_POST['id_amigo'];
                $estadoAmigo = 'amigo';
                $sqlRelacion = "UPDATE amigo SET estado = :estado WHERE usuario2 = :id_amigo";
                $stmtRelacion = $conn->prepare($sqlRelacion);
                $stmtRelacion->bindParam(':estado', $estadoAmigo);
                $stmtRelacion->bindParam(':id_amigo', $idAmigo);
                $stmtRelacion->execute();

                // Redireccionar después de procesar el formulario para evitar reenvío
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
                
            } elseif (isset($_POST['Rechazar'])) {

                $idAmigo = $_POST['id_amigo'];
                $estadoRechazado = 'rechazado';
                $sqlRelacion = "UPDATE amigo SET estado = :estado WHERE usuario2 = :id_amigo";
                $stmtRelacion = $conn->prepare($sqlRelacion);
                $stmtRelacion->bindParam(':estado', $estadoRechazado);
                $stmtRelacion->bindParam(':id_amigo', $idAmigo);
                $stmtRelacion->execute();

                // Redireccionar después de procesar el formulario para evitar reenvío
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }

        } else {
            echo "<p>No tienes solicitudes</p>";
        }

    ?>
</body>
</html>
<?php
}
?>
