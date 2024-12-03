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
                WHERE pregunta.id_usuario = :id_usuario";  // Solo preguntas del usuario logueado
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

        // Código para manejar la solicitud de amistad y el chat
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
            // Redireccionamos a la página de solicitudes
            header("Location: " . "./solicitudes_chat_y_amigos/solicitudes.php");
            exit();
        }

        if (isset($_POST['Chat'])) {
            // Redireccionamos a la página de chat
            header("Location: " . "./solicitudes_chat_y_amigos/chat.php?receptor=". urlencode($emisor));
            exit();
        }

        // Si hay filtros, los añadimos a la consulta
        if (!empty($filtros)) {
            $sqlPreguntas .= " AND " . implode(" AND ", $filtros);
        }

        // Ejecutamos la consulta
        $stmtPreguntas = $conn->prepare($sqlPreguntas);
        if ($parametros) {
            foreach ($parametros as $identificador => $valor) {
                $stmtPreguntas->bindParam($identificador, $valor);
            }
        }

        $stmtPreguntas->execute();
        $resultado = $stmtPreguntas->fetchAll(PDO::FETCH_ASSOC);
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página principal</title>
    <!-- Incluir Bootstrap CSS -->
    <link rel="stylesheet" href="principal.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <!-- Botón para pantallas pequeñas -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Enlace para cambiar de página entre "preguntas personales" y "todas las preguntas" -->
                <?php if ($paginas != 'preguntas_personales') : ?>
                    <a href="?pagina=preguntas_personales" class="nav-link">Preguntas personales</a>
                <?php else : ?>
                    <a href="?pagina=default" class="nav-link">Todas las preguntas</a>
                <?php endif; ?>

                <a href="./solicitudes_chat_y_amigos/amigos.php" class="nav-link">Amigos</a>
                <a href="./solicitudes_chat_y_amigos/solicitudes.php" class="nav-link">Solicitudes</a>

                <!-- Formulario de búsqueda -->
                <form class="d-flex" method="GET">
                    <input class="form-control me-2" type="search" name="buscarNombre" placeholder="Buscar Nombre" value="<?= $buscar_nombre ?>">
                    <input class="form-control me-2" type="search" name="buscarTitulo" placeholder="Buscar Título" value="<?= $buscar_titulo ?>">
                    <input type="hidden" name="pagina" value="<?= $paginas ?>">
                    <button class="btn btn-outline-success" type="submit">Buscar</button>
                </form>

                <!-- Botón de cierre de sesión -->
                <form method="POST" action="login/logout.php" class="ms-3">
                    <button type="submit" class="btn btn-outline-danger">Cerrar sesión</button>
                </form>
            </ul>
        </div>
    </div>
</nav>
    <!-- Botón para crear una pregunta (debajo del navbar) -->
    <div class="container mt-4 text-end">
        <a href="crear_pregunta.php" class="btn btn-outline-primary">Crear pregunta</a>
    </div>

    <div class="container mt-4">
        <div class="row">
            <!-- Mostrar resultados de la consulta -->
            <?php foreach ($resultado as $row) : ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                        <a href="./respuestas.php?pregunta=<?php echo urlencode($row['id_pregunta']); ?>">
    <h5 class="card-title"><?= htmlspecialchars($row['titulo']); ?></h5>
</a>                            <p class="card-text"><?= htmlspecialchars($row['descripcion']); ?></p>
                            <p class="card-text">
                                <small class="text-muted"><?= htmlspecialchars($row['nombre']); ?></small>
                            </p>
                            <?php if ($paginas != 'preguntas_personales') : ?>
                    <form method="POST">
                        <input type="hidden" name="idUsuario" value="<?= htmlspecialchars($row['id_usuario']); ?>">

                        <?php if ($row['estado'] == 'solicitado') : ?>
                            <button type="submit" name="Cancelar" class="btn btn-danger">Cancelar solicitud</button>
                        <?php elseif ($row['estado'] == 'aceptado') : ?>
                            <button type="submit" name="Chat" class="btn btn-success">Iniciar chat</button>
                        <?php else : ?>
                            <button type="submit" name="Solicitar" class="btn btn-primary">Solicitar amistad</button>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Incluir Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

