<?php

session_start();

include("../conexion.php");

if (isset($_SESSION['loggedin'])  && isset($_SESSION['id_usuario'])) {

    include_once "../conexion.php"; // Asegúrate de que este archivo use MySQLi

    $mi_usuario = $_GET['mi_usuario'];

    if (isset($mi_usuario)) {

        $estado = 'desconectado';

        // Usar MySQLi para la consulta
        $sql = "UPDATE tbl_usuarios SET estado = ? WHERE id_usuario = ?";
        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "si", $estado, $mi_usuario);

        // Ejecutar la consulta
        if (mysqli_stmt_execute($stmt)) {
            // Comprobar si se afectaron filas
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                session_unset();
                session_destroy();
                header("Location: ./login.php");
                exit(); // Asegúrate de salir después de redirigir
            }
        } else {
            echo "Error al ejecutar la consulta: " . mysqli_error($conn);
        }

        // Cerrar la declaración
        mysqli_stmt_close($stmt);

    } else {
        header("Location: ../index.php");
        exit(); // Asegúrate de salir después de redirigir
    }
} else {
    header("Location: ./login.php");
    exit(); // Asegúrate de salir después de redirigir
}
?>
