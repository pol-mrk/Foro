<?php

session_start();

require_once 'conexion.php';

if (isset($_GET['pregunta'])) {
    $pregunta = $_GET['pregunta'];
    $_SESSION['pregunta'] = $pregunta;  // Guardar el ID de la pregunta en la sesión
} elseif (isset($_SESSION['pregunta'])) {
    // Si no viene por GET, obtener el valor guardado en la sesión
    $pregunta = $_SESSION['pregunta'];
}

$sqlPregunta = "SELECT pregunta.titulo, pregunta.descripcion from pregunta where pregunta.id_pregunta = :pregunta";


$sqlRespuesta = "SELECT respuesta.descripcion, usuario.username, respuesta.fecha_respuesta 
                 FROM respuesta 
                 INNER JOIN pregunta ON respuesta.id_pregunta = pregunta.id_pregunta
                 INNER JOIN usuario ON respuesta.id_usuario = usuario.id_usuario 
                 WHERE respuesta.id_pregunta = :respuesta ORDER BY respuesta.fecha_respuesta DESC";
// Preparar la consulta
$stmt1 = $conn->prepare($sqlPregunta);
// Enlazar parámetros
$stmt1->bindParam(':pregunta', $pregunta);
// Ejecutar la consulta
$stmt1->execute();


// Preparar la consulta de las respuestas
$stmt2 = $conn->prepare($sqlRespuesta);
// Enlazar el mismo parámetro :pregunta para la consulta de respuestas
$stmt2->bindParam(':respuesta', $pregunta);
// Ejecutar la consulta
$stmt2->execute();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Exo:ital,wght@0,100..900;1,100..900&family=Golos+Text:wght@400..900&family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">


</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <a href="./pagina_principal.php" class="nav-link">Volver a preguntas</a>
                    <a href="login/logout.php" class="btn btn-outline-danger ms-3">Cerrar sesión</a>
                </ul>
            </div>
        </div>
    </nav>
    <?php
// Verificar si hay resultados
if ($stmt1->rowCount() > 0) {
    // Recuperar los resultados
    while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
        echo "<div class= 'divPregunta'>";
        echo "<h1 class= 'tituloPregunta'>" . htmlspecialchars($row['titulo']) . "</h1>";
        echo "<hr>";
        echo "<p class= 'pregunta'>" . htmlspecialchars($row['descripcion']) . "</p>";
        echo "</div>";
        
    }
}
?>

<form action="./comentar.php" id="formComentar" method="post">
    <label for="comentar" id="labelComentar">Tu respuesta:</label>
    <textarea name="comentar" id="comentar" rows="10"></textarea>
    <p class="error">
    <?php
    if (isset($_GET['comentarioVacio']) && $_GET['comentarioVacio'] == 'true') {
        echo "La respuesta no puede estar vacia.";
    }
    ?>
</p>    <button type="submit" id="btnComentar" class="btn btn-primary"name="btnComentar">Enviar Respuesta</button>
</form>

<?php

// Verificar si hay resultados de respuestas
if ($stmt2->rowCount() > 0) {

    echo "<h1 id='otrasRespuestas'>Otras respuestas:</h1>";
    // Recuperar los resultados de las respuestas
    echo "<div class= 'divRespuesta'>";
    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        echo "<h2 class= 'respuesta'>" . htmlspecialchars($row['username']) . "</h2>";
        echo "<p class= 'fechaRespuesta'>" . htmlspecialchars($row['fecha_respuesta']) . "</p>";
        echo "<p class= 'respuesta2'>" . htmlspecialchars($row['descripcion']) . "</p>";
    }
    echo '</div>';
}
    ?>
    <br>
</body>
</html>