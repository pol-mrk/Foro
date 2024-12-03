<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['id_usuario'])) {
    header('Location: ./login/login.php'); // Redirigir al login si no está autenticado
    exit();
}

include_once("./conexion.php");

$mi_usuario = $_SESSION['id_usuario'];

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : '';
    $descripcion = isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : '';

    // Validar los campos del formulario
    if ($titulo != '' && $descripcion != '') {
        // Insertar la nueva pregunta en la base de datos
        $sql = "INSERT INTO pregunta (titulo, descripcion, id_usuario) VALUES (:titulo, :descripcion, :id_usuario)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':id_usuario', $mi_usuario);

        if ($stmt->execute()) {
            // Redirigir a la página de todas las preguntas después de crearla
            header('Location: ./index.php'); 
            exit();
        } else {
            $error = "Hubo un problema al crear la pregunta.";
        }
    } else {
        $error = "Por favor, completa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Pregunta</title>
    <link rel="stylesheet" href="error.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Barra de navegación -->
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

    <!-- Contenedor del formulario de creación de pregunta -->
    <div class="container mt-4">
        <h2>Crear una nueva pregunta</h2>
        
        <!-- Mostrar errores si existen -->
        <p><php? if(isset) ?></p>
        
        <form method="POST" action="proceso_crearPregunta.php">
            <div class="mb-3">
                <label for="titulo" class="form-label">Título</label>
                <input type="text" class="form-control" id="titulo" name="titulo">
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4"></textarea>
            </div>
            <p class="error">

            <?php

    if (isset($_GET['Vacio']) && $_GET['Vacio'] == 'true') {
        echo "Rellena todos los campos.";
    }
    ?>   </p>         <button type="submit" class="btn btn-primary">Crear pregunta</button>
        </form>
    </div>

    <!-- Incluir Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
