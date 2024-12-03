<?php

require_once 'conexion.php';

$pregunta = 1;

$sqlPregunta = "SELECT pregunta.titulo, pregunta.descripcion from pregunta where pregunta.id_pregunta = :pregunta";

$sqlRespuesta = "SELECT respuesta.descripcion from respuesta 
INNER JOIN pregunta ON respuesta.id_pregunta = pregunta.id_pregunta where respuesta.id_pregunta = :respuesta";

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
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Exo:ital,wght@0,100..900;1,100..900&family=Golos+Text:wght@400..900&family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
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

<form action="" id="formComentar">
    <label for="comentar" id="labelComentar">Tu respuesta:</label>
    <textarea name="comentar" id="comentar" rows="10"></textarea>
</form>

<?php

// Verificar si hay resultados de respuestas
if ($stmt2->rowCount() > 0) {
    // Recuperar los resultados de las respuestas
    echo "<div class= 'divRespuesta'>";
    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        echo "<p class= 'respuesta'>" . htmlspecialchars($row['descripcion']) . "</p>";
    }
    echo '</div>';
}
    ?>
    
</body>
</html>