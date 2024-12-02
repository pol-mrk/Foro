<?php

include_once("../conexion.php");

session_start();

if (!isset($_SESSION['loggedin']) && !isset($_SESSION['id_usuario'])) {
    header('Location: ' . '../login/login.php');
    exit();
} else {
    $mi_usuario = htmlspecialchars($_SESSION['id_usuario']);        
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar amigos</title>
</head>
<body>
    <form method="post">
        <input type="text" name="buscar_usuario" id="" placeholder="Buscar usuarios...">
        <input type="submit" value="Buscar" name="Buscar">
    </form>
    <?php

    if (isset($_POST['Buscar'])) {

        $buscar_usuario = $_POST['buscar_usuario'];
        $inputBuscarUsuarios = '%' . $buscar_usuario . '%';

        $sqlBuscador = "SELECT usuario.id_usuario, username, nombre, apellidos, correo,
            amigo.usuario1 AS amigo1, amigo.usuario2 AS amigo2, amigo.estado 
            FROM usuario
            LEFT JOIN amigo 
            ON (amigo.usuario1 = usuario.id_usuario OR amigo.usuario2 = usuario.id_usuario) 
            AND (amigo.usuario1 = :mi_usuario OR amigo.usuario2 = :mi_usuario)
            WHERE (usuario.id_usuario != :mi_usuario) AND (nombre LIKE :inputBuscarUsuarios)
            GROUP BY usuario.id_usuario";

        $stmtBuscador = $conn->prepare($sqlBuscador);
        $stmtBuscador->bindParam(':mi_usuario', $mi_usuario);
        $stmtBuscador->bindParam(':inputBuscarUsuarios', $inputBuscarUsuarios);
        $stmtBuscador->execute();

        $resultado = $stmtBuscador->fetchAll(PDO::FETCH_ASSOC);

        if (count($resultado) > 0) {

            foreach ($resultado as $fila) {

                $idUsuario = htmlspecialchars($fila['id_usuario']);
                $nombre = htmlspecialchars($fila['nombre']);
                $usuario1 = htmlspecialchars($fila['amigo1']);
                $usuario2 = htmlspecialchars($fila['amigo2']);
                $estadoAmistad = htmlspecialchars($fila['estado']);

                // Lógica para mostrar usuarios
                if ($estadoAmistad == 'amigo') {

                    echo "<p><strong>$nombre</strong></p>";
                    echo "<p> Ya es tu amigo. </p>";

                } elseif ($estadoAmistad == 'solicitado' && $usuario1 == $mi_usuario) {

                    echo "<p><strong>$nombre</strong></p>";
                    echo "<p> Ya le has solicitado. </p>";
                    echo "<form method='POST'>
                            <input type='hidden' name='idUsuario' value='$idUsuario'>
                            <input type='submit' value='Cancelar Solicitud' name='Cancelar'>
                            </form>";

                } elseif ($estadoAmistad == 'solicitado' && $usuario2 == $mi_usuario) {

                    echo "<p><strong>$nombre</strong></p>";
                    echo "<p> Te ha solicitado. </p>";
                    echo "<form method='POST'>
                            <input type='submit' value='Ir a solicitudes' name='Solicitudes'>
                            </form>";

                } elseif ($estadoAmistad == 'rechazado' && $usuario1 == $mi_usuario) {

                    echo "<p><strong>$nombre</strong></p>";
                    echo "<form method='POST'>
                            <input type='hidden' name='idUsuario' value='$idUsuario'>
                            <input type='submit' value='Volver a solicitar' name='VolverASolicitar'>
                            </form>";

                } else {

                    echo "<p><strong>$nombre</strong></p>";
                    echo "<form method='POST'>
                            <input type='hidden' name='idUsuario' value='$idUsuario'>
                            <input type='submit' value='Solicitar' name='Solicitar'>
                            </form>";
                
                }
            }

            if (isset($_POST['Solicitar'])) {

                $idUsuario = $_POST['idUsuario'];
                $estadoAmigo = 'solicitado';
        
                $sqlRelacion = "INSERT INTO amigo (usuario1, usuario2, estado) VALUES (:usuario1, :usuario2, :estado)";
                $stmtRelacion = $conn->prepare($sqlRelacion);
                $stmtRelacion->bindParam(':usuario1', $mi_usuario);
                $stmtRelacion->bindParam(':usuario2', $idUsuario);
                $stmtRelacion->bindParam(':estado', $estadoAmigo);
                $stmtRelacion->execute();
        
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
        
            }
        
            if (isset($_POST['VolverASolicitar'])) {
                $idUsuario = $_POST['idUsuario'];
                $estadoAmigo = 'solicitado';
        
                $sqlRelacion = "UPDATE amigo SET estado = :estado WHERE usuario1 = :usuario1 AND usuario2 = :usuario2";
                $stmtRelacion = $conn->prepare($sqlRelacion);
                $stmtRelacion->bindParam(':estado', $estadoAmigo);
                $stmtRelacion->bindParam(':usuario1', $mi_usuario);
                $stmtRelacion->bindParam(':usuario2', $idUsuario);
                $stmtRelacion->execute();
        
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        
            if (isset($_POST['Cancelar'])) {
                $idUsuario = $_POST['idUsuario'];
        
                $sqlRelacion = "DELETE FROM amigo WHERE usuario1 = :usuario1 AND usuario2 = :usuario2";
                $stmtRelacion = $conn->prepare($sqlRelacion);
                $stmtRelacion->bindParam(':usuario1', $mi_usuario);
                $stmtRelacion->bindParam(':usuario2', $idUsuario);
                $stmtRelacion->execute();
        
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }

        } else {
            echo "<p>No hay usuarios disponibles.</p>";
        }
    }

    else {

        $sqlBuscarAmigos = "SELECT usuario.id_usuario, username, nombre, apellidos, correo, amigo.usuario1 AS amigo1, amigo.usuario2 AS amigo2, amigo.estado FROM usuario
            LEFT JOIN amigo ON (amigo.usuario1 = usuario.id_usuario OR amigo.usuario2 = usuario.id_usuario) AND (amigo.usuario1 = :mi_usuario OR amigo.usuario2 = :mi_usuario)
            WHERE (usuario.id_usuario != :mi_usuario)
            GROUP BY usuario.id_usuario";

        $stmtBuscarAmigos = $conn->prepare($sqlBuscarAmigos);
        $stmtBuscarAmigos->bindParam(':mi_usuario', $mi_usuario);
        $stmtBuscarAmigos->execute();

        $resultado = $stmtBuscarAmigos->fetchAll(PDO::FETCH_ASSOC);

        if (count($resultado) > 0) {

            foreach ($resultado as $fila) {

                $idUsuario = htmlspecialchars($fila['id_usuario']);
                $nombre = htmlspecialchars($fila['nombre']);
                $usuario1 = htmlspecialchars($fila['amigo1']);
                $usuario2 = htmlspecialchars($fila['amigo2']);
                $estadoAmistad = htmlspecialchars($fila['estado']);

                // Lógica para mostrar usuarios
                if ($estadoAmistad == 'amigo') {

                    echo "<p><strong>$nombre</strong></p>";
                    echo "<p> Ya es tu amigo. </p>";

                } elseif ($estadoAmistad == 'solicitado' && $usuario1 == $mi_usuario) {

                    echo "<p><strong>$nombre</strong></p>";
                    echo "<p> Ya le has solicitado. </p>";
                    echo "<form method='POST'>
                            <input type='hidden' name='idUsuario' value='$idUsuario'>
                            <input type='submit' value='Cancelar Solicitud' name='Cancelar'>
                            </form>";

                } elseif ($estadoAmistad == 'solicitado' && $usuario2 == $mi_usuario) {

                    echo "<p><strong>$nombre</strong></p>";
                    echo "<p> Te ha solicitado. </p>";
                    echo "<form method='POST'>
                            <input type='submit' value='Ir a solicitudes' name='Solicitudes'>
                            </form>";

                } elseif ($estadoAmistad == 'rechazado' && $usuario1 == $mi_usuario) {

                    echo "<p><strong>$nombre</strong></p>";
                    echo "<form method='POST'>
                            <input type='hidden' name='idUsuario' value='$idUsuario'>
                            <input type='submit' value='Volver a solicitar' name='VolverASolicitar'>
                            </form>";

                } else {

                    echo "<p><strong>$nombre</strong></p>";
                    echo "<form method='POST'>
                            <input type='hidden' name='idUsuario' value='$idUsuario'>
                            <input type='submit' value='Solicitar' name='Solicitar'>
                            </form>";
                
                }
            }

            if (isset($_POST['Solicitar'])) {

                $idUsuario = $_POST['idUsuario'];
                $estadoAmigo = 'solicitado';
        
                $sqlRelacion = "INSERT INTO amigo (usuario1, usuario2, estado) VALUES (:usuario1, :usuario2, :estado)";
                $stmtRelacion = $conn->prepare($sqlRelacion);
                $stmtRelacion->bindParam(':usuario1', $mi_usuario);
                $stmtRelacion->bindParam(':usuario2', $idUsuario);
                $stmtRelacion->bindParam(':estado', $estadoAmigo);
                $stmtRelacion->execute();
        
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
        
            }
        
            if (isset($_POST['VolverASolicitar'])) {
                $idUsuario = $_POST['idUsuario'];
                $estadoAmigo = 'solicitado';
        
                $sqlRelacion = "UPDATE amigo SET estado = :estado WHERE usuario1 = :usuario1 AND usuario2 = :usuario2";
                $stmtRelacion = $conn->prepare($sqlRelacion);
                $stmtRelacion->bindParam(':estado', $estadoAmigo);
                $stmtRelacion->bindParam(':usuario1', $mi_usuario);
                $stmtRelacion->bindParam(':usuario2', $idUsuario);
                $stmtRelacion->execute();
        
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        
            if (isset($_POST['Cancelar'])) {
                $idUsuario = $_POST['idUsuario'];
        
                $sqlRelacion = "DELETE FROM amigo WHERE usuario1 = :usuario1 AND usuario2 = :usuario2";
                $stmtRelacion = $conn->prepare($sqlRelacion);
                $stmtRelacion->bindParam(':usuario1', $mi_usuario);
                $stmtRelacion->bindParam(':usuario2', $idUsuario);
                $stmtRelacion->execute();
        
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }

        } else {
            echo "<p>No hay usuarios disponibles.</p>";
        }
    }
    ?>
</body>
</html>
<?php
    }
?>