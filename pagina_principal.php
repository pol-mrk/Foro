<?php

    include_once("./conexion.php");

    session_start();

    if (!isset($_SESSION['loggedin']) && !isset($_SESSION['id_usuario'])) {

        header('Location: ' . './login/login.php');
        exit();

    } else {

        $mi_usuario = htmlspecialchars($_SESSION['id_usuario']);

        $buscar_nombre = isset($_GET['buscarNombre']) ? htmlspecialchars($_GET['buscarNombre']) : '';
        $buscar_titulo = isset($_GET['buscarTitulo']) ? htmlspecialchars($_GET['buscarTitulo']) : '';    
        
        $paginas = isset($_GET['pagina']) ? htmlspecialchars($_GET['pagina']) : 'default';
        
        // Creamos una variable (array) para los filtros y otra para los parametros
        // (filtrará todas las letras/números que estén en los filtros)
        $filtros = [];
        $parametros = [];

        // CAMBIAR ENTRE PÁGINAS (OCUPACIONES / SALAS / SALAS MÁS USADAS / HISTORIAL MESAS)
        switch ($paginas) {

            case 'preguntas_personales':
                
                $sqlPreguntas = "SELECT pregunta.id_pregunta, pregunta.titulo, pregunta.descripcion, pregunta.id_usuario,
                usuario.id_usuario, usuario.username, usuario.nombre,
                amigo.emisor AS amigo1, amigo.receptor AS amigo2, amigo.estado
                FROM pregunta
                INNER JOIN usuario ON usuario.id_usuario = pregunta.id_usuario
                LEFT JOIN amigo ON (amigo.emisor = usuario.id_usuario OR amigo.receptor = usuario.id_usuario) AND (amigo.emisor = :id_usuario OR amigo.receptor = :id_usuario)
                WHERE pregunta.id_usuario != :id_usuario";
                $parametros[':id_usuario'] = $mi_usuario;
                break;

            default:

                $sqlPreguntas = "SELECT pregunta.id_pregunta, pregunta.titulo, pregunta.descripcion, pregunta.id_usuario,
                usuario.id_usuario, usuario.username, usuario.nombre,
                amigo.emisor AS amigo1, amigo.receptor AS amigo2, amigo.estado
                FROM pregunta
                INNER JOIN usuario ON usuario.id_usuario = pregunta.id_usuario
                LEFT JOIN amigo ON (amigo.emisor = usuario.id_usuario OR amigo.receptor = usuario.id_usuario) AND (amigo.emisor = :id_usuario OR amigo.receptor = :id_usuario)
                WHERE pregunta.id_usuario != :id_usuario";
                $parametros[':id_usuario'] = $mi_usuario;
                break;
        }


        // Si hemos introducido texto en el input 'buscarNombre'
        if ($buscar_nombre != "") {
            $filtros[] = "usuario.nombre LIKE :nombre OR usuario.username LIKE :username";
            $parametros[':nombre'] = '%' . $buscar_nombre . '%';
            $parametros[':username'] = '%' . $buscar_nombre . '%';
        }

        // Si hemos introducido texto en el input 'buscarTitulo'
        if ($buscar_titulo != "") {
            $filtros[] = "pregunta.titulo LIKE :titulo";
            $parametros[':titulo'] = '%' . $buscar_titulo . '%';
        }

        if (isset($_POST['Solicitar'])) {

            $idUsuario = $_POST['idUsuario'];
            $estadoAmigo = 'solicitado';
    
            $sqlRelacion = "INSERT INTO amigo (emisor, receptor, estado) VALUES (:emisor, :receptor, :estado)";
            $stmtRelacion = $conn->prepare($sqlRelacion);
            $stmtRelacion->bindParam(':emisor', $mi_usuario);
            $stmtRelacion->bindParam(':receptor', $idUsuario);
            $stmtRelacion->bindParam(':estado', $estadoAmigo);
            $stmtRelacion->execute();
    
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
    
        }
    
        if (isset($_POST['Cancelar'])) {
            $idUsuario = $_POST['idUsuario'];
    
            $sqlRelacion = "DELETE FROM amigo WHERE emisor = :emisor AND receptor = :receptor";
            $stmtRelacion = $conn->prepare($sqlRelacion);
            $stmtRelacion->bindParam(':emisor', $mi_usuario);
            $stmtRelacion->bindParam(':receptor', $idUsuario);
            $stmtRelacion->execute();
    
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        if (isset($_POST['Solicitudes'])) {

            // Redireccionamos a la página
            header("Location: " . "./solicitudes_chat_y_amigos/solicitudes.php");
            exit();
            
        }

        if (isset($_POST['Chat'])) {

            // Redireccionamos a la página
            header("Location: " . "./solicitudes_chat_y_amigos/chat.php?receptor=". urlencode($emisor));
            exit();
            
        }

        ?>

        <?php

        // Si hay filtros, los añadimos a la consulta
        if (!empty($filtros)) {
            $sqlPreguntas .= " AND " . implode(" AND ", $filtros);
        }

        // echo "Consulta SQL: " . $sqlPreguntas . "<br>";
        // echo "Parámetros: <pre>";
        // print_r($parametros);
        // echo "</pre>";

        $stmtPreguntas = $conn->prepare($sqlPreguntas);

        // Si los parámetros no están vacíos (si contiene valores),
        // Hacemos un foreach que recorrerá este array ($parametros),
        // (recorrerá los ':identificadores' y sus '%valores%')
        if ($parametros) {
            foreach ($parametros as $identificador => $valor) {
                $stmtPreguntas->bindParam($identificador, $valor);
            }
        }

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
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">

            <!-- Botón para hacer el navbar responsive (mete todos los elementos en un responsive) -->
            <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button> -->

            <!-- FILTROS Y PÁGINAS -->
            <div class="collapse navbar-collapse divNavbar" id="navbarContent">

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                    <!-- Navegación entre páginas -->
                    <?php if ($paginas != 'preguntas_personales') : ?>
                        
                        <a href="?pagina=preguntas_personales">Preguntas personales</a>

                    <?php else : ?>
                        
                        <a href="?pagina=default">Todas las preguntas</a>
                        
                    <?php endif; ?>

                    <a href="./solicitudes_chat_y_amigos/amigos.php">Amigos</a>
                    <a href="./solicitudes_chat_y_amigos/solicitudes.php">Solicitudes</a>

                    <?php if ($paginas != 'preguntas_personales') : ?>

                        <form class="form-inline my-2 my-lg-0 d-flex align-items-center" method="GET">
                            <input class="form-control mr-sm-2" type="search" name="buscarNombre" placeholder="Buscar Nombre" aria-label="Buscar Nombre" value="<?php if(isset($_GET['buscarNombre'])) {echo $_GET['buscarNombre'];} ?>">
                            <input class="form-control mr-sm-2" style="margin-left: 10px;" type="search" name="buscarTitulo" placeholder="Buscar Título" aria-label="Buscar Título" value="<?php if(isset($_GET['buscarTitulo'])) {echo $_GET['buscarTitulo'];} ?>">
                            <input type="hidden" name="pagina" value="<?= $paginas ?>">
                            <button type="submit" class="btn btn-primary" style="padding-top: 4px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor" class="bi bi-search" viewBox="0 0 16 21">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                </svg>
                            </button>
                            <button type="submit" class="btn btn-danger" name="limpiar_filtros" style="margin-left: 10px; margin-right: 10px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="26" fill="currentColor" class="bi bi-eraser-fill" viewBox="0 0 16 21">
                                    <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828zm.66 11.34L3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293z"/>
                                </svg>
                            </button>
                        </form>

                    <?php else : ?>

                        <form class="form-inline my-2 my-lg-0 d-flex align-items-center" method="GET">
                            <input type="hidden" name="pagina" value="<?= $paginas ?>">
                            <input class="form-control mr-sm-2" style="margin-left: 10px;" type="search" name="buscarTitulo" placeholder="Buscar Título" aria-label="Buscar Título" value="<?php if(isset($_GET['buscarTitulo'])) {echo $_GET['buscarTitulo'];} ?>">
                            <button type="submit" class="btn btn-primary" style="padding-top: 4px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor" class="bi bi-search" viewBox="0 0 16 21">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                </svg>
                            </button>
                            <button type="submit" class="btn btn-danger" name="limpiar_filtros" style="margin-left: 10px; margin-right: 10px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="26" fill="currentColor" class="bi bi-eraser-fill" viewBox="0 0 16 21">
                                    <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828zm.66 11.34L3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293z"/>
                                </svg>
                            </button>
                        </form>
                    
                    <?php endif; ?>

                    
                    <?php

                        if (isset($_GET['limpiar_filtros'])) {
                            // Redirigir a la misma página sin parámetros
                            header("Location: ?pagina=".$paginas);
                            exit();
                        }

                    ?>
                    <li>
                        <a href="../CerrarSesion.php" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white" class="bi bi-box-arrow-right" viewBox="0 0 16 18">
                                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php

        if (count($resultado) > 0) {

            foreach ($resultado as $fila) {

                $idUsuario = htmlspecialchars($fila['id_usuario']);
                $nombre = htmlspecialchars($fila['nombre']);
                $emisor = isset($fila['amigo1']) ? htmlspecialchars($fila['amigo1']) : null;
                $receptor = isset($fila['amigo2']) ? htmlspecialchars($fila['amigo2']) : null;
                $estadoAmistad = isset($fila['estado']) ? htmlspecialchars($fila['estado']) : null;

                if ($paginas == 'preguntas_personales') {
                    echo "<div>";
                    echo "<h2>".$fila["titulo"]."</h2>";
                    echo "<p>".$fila["descripcion"]."</p>";
                    echo "</div>";
                }
                else {

                    echo "<div>";

                    if ($estadoAmistad == 'amigo') {

                        echo "<h3 id='usuario'>".$fila["username"]."</h3>";
                        echo "<div>";
                        echo "<p> Ya es tu amigo. </p>";
                        echo "<form method='POST'>
                                <input type='hidden' name='emisor' value='$emisor'>
                                <input type='submit' value='Enviar un mensaje' name='Chat'>
                            </form>";
                        echo "</div>";
    
                    } elseif ($estadoAmistad == 'solicitado' && $emisor == $mi_usuario) {
    
                        echo "<h3 id='usuario'>".$fila["username"]."</h3>";
                        echo "<p> Ya le has solicitado. </p>";
                        echo "<form method='POST'>
                                <input type='hidden' name='idUsuario' value='$idUsuario'>
                                <input type='submit' value='Cancelar Solicitud' name='Cancelar'>
                            </form>";
    
                    } elseif ($estadoAmistad == 'solicitado' && $receptor == $mi_usuario) {
    
                        echo "<h3 id='usuario'>".$fila["username"]."</h3>";
                        echo "<p> Te ha solicitado. </p>";
                        echo "<form method='POST'>
                                <input type='submit' value='Ir a solicitudes' name='Solicitudes'>
                            </form>";
    
                    } else {

                        echo "<h3 id='usuario'>".$fila["username"]."</h3>";
                        echo "<form method='POST'>
                                <input type='hidden' name='idUsuario' value='$idUsuario'>
                                <input type='submit' value='Solicitar' name='Solicitar'>
                                </form>";
                    
                    }

                    echo "<h2>".$fila["titulo"]."</h2>";
                    echo "<p>".$fila["descripcion"]."</p>";
                    echo "</div>";
                }
            }

            

        } else {

            echo '<div>No hay preguntas disponibles</div>';

        }
    ?>

    <script src="script.js"></script>

</body>
</html>
<?php
    }
?> 