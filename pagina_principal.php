<?php

    include_once("./conexion.php");

    session_start();

    if (!isset($_SESSION['loggedin']) && !isset($_SESSION['id_usuario'])) {

        header('Location: ' . './login/login.php');
        exit();

    } else {
        $mi_usuario = htmlspecialchars($_SESSION['id_usuario']);

        $paginas = isset($_GET['pagina']) ? $_GET['pagina'] : 'default';

        $sqlPreguntas = "SELECT id_pregunta, titulo, descripcion, id_usuario FROM pregunta;";

        // CAMBIAR ENTRE PÁGINAS (OCUPACIONES / SALAS / SALAS MÁS USADAS / HISTORIAL MESAS)
        switch ($paginas) {

            case 'preguntas_personales':
                
                $sqlPreguntas = "SELECT id_pregunta, titulo, descripcion, id_usuario FROM pregunta WHERE id_usuario = :id_usuario;";
                break;

            default:

                $sqlPreguntas = "SELECT pregunta.id_pregunta, pregunta.titulo, pregunta.descripcion, pregunta.id_usuario, usuario.username
                FROM pregunta INNER JOIN usuario ON usuario.id_usuario = pregunta.id_usuario
                WHERE pregunta.id_usuario != :id_usuario";
                break;
        }

        $stmtPreguntas = $conn->prepare($sqlPreguntas);
        $stmtPreguntas->bindParam(':id_usuario', $mi_usuario);
        $stmtPreguntas->execute();

        $resultado = $stmtPreguntas->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página principal</title>
</head>
<body>
    <?php

        if (count($resultado) > 0) {

            foreach ($resultado as $fila) {

                if ($paginas == 'preguntas_personales') {
                    echo "<div>";
                    echo "<h2>".$fila["titulo"]."</h2>";
                    echo "<p>".$fila["descripcion"]."</p>";
                    echo "</div>";
                }
                else {
                    echo "<div>";
                    echo "<h3>".$fila["username"]."</h3>";
                    echo "<h2>".$fila["titulo"]."</h2>";
                    echo "<p>".$fila["descripcion"]."</p>";
                    echo "</div>";
                }
            }

        } else {

            echo '<div>No hay preguntas disponibles</div>';

        }
    ?>

    <?php if ($paginas != 'preguntas_personales') : ?>

        <a href="?pagina=preguntas_personales">Preguntas personales</a>

    <?php else : ?>

        <a href="?pagina=default">Todas las preguntas</a>

    <?php endif; ?>


</body>
</html>
<?php
    }
?>