<?php
// Iniciar sesión
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

            // Ejecutar la consulta
            $stmt->execute();

            // Redirigir a la página principal de preguntas después de crearla
            header('Location: ./pagina_principal'); 
            exit();
        } else {
            // En caso de error, mostrar un mensaje
            header('Location: ./crear_pregunta.php?Vacio=true'); // Redirigir al login si no está autenticado
        }
    } else {
        header('Location: ./login/login.php'); // Redirigir al login si no está autenticado
    }

?>