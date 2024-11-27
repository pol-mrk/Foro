<?php
$username = $_POST["username"];
$nombre = $_POST["nombre"];
$apellidos = $_POST["apellidos"];
$correo = $_POST["correo"];
$contraseña = $_POST["contraseña"];
$hash = password_hash($contraseña, PASSWORD_BCRYPT);
include("../../conexion.php");

if (password_verify($contraseña, $hash)) {
    $rol = 2;

    // Preparar la consulta de inserción
    $sqlNuevoUsuario = "INSERT INTO usuario (username, nombre, apellidos, correo, contraseña, id_rol) VALUES (:username, :nombre, :apellidos, :correo, :contrasena, :id_rol)";
    $stmtNuevoUsuario = $conn->prepare($sqlNuevoUsuario);

    // Enlazar parámetros a la consulta preparada
    $stmtNuevoUsuario->bindParam(':username', $username);
    $stmtNuevoUsuario->bindParam(':nombre', $nombre);
    $stmtNuevoUsuario->bindParam(':apellidos', $apellidos);
    $stmtNuevoUsuario->bindParam(':correo', $correo);
    $stmtNuevoUsuario->bindParam(':contrasena', $hash);
    $stmtNuevoUsuario->bindParam(':id_rol', $rol);

    // Ejecutar la consulta
    $stmtNuevoUsuario->execute();

    // Redirigir al usuario después del registro
    header("location: ../../login/login.php");
}
?>