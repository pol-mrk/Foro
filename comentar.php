<?php

session_start();

// Conexión a la base de datos
require_once 'conexion.php';

// Verificar si el formulario ha sido enviado
if (isset($_POST['comentar']) && !empty($_POST['comentar'])) {
    // Obtener el comentario y la pregunta desde la sesión
    $comentario = trim($_POST['comentar']);
    $pregunta = $_SESSION['pregunta'];

    // Preparar la consulta SQL para insertar el comentario
    $sqlInsert = "INSERT INTO respuesta (descripcion, id_pregunta, id_usuario) VALUES (:comentario, :pregunta, :id_usuario)";
    $stmt = $conn->prepare($sqlInsert);

    // Enlazar parámetros
    $stmt->bindParam(':comentario', $comentario);
    $stmt->bindParam(':pregunta', $pregunta);
    $stmt->bindParam(':id_usuario',$_SESSION['id_usuario']);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        header('Location: ' . './respuestas.php');
    }
} else {
    header('Location: ' . './respuestas.php?comentarioVacio=true');
}

?>
