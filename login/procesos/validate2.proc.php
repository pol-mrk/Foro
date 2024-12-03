<?php

$error="";
function validaCampoVacio($campo) {

    if(empty($campo)){
        $error= true; //Hay un error
    }else{
        $error= false; //No hay un error
    }
    return $error;

}


if (!filter_has_var(INPUT_POST, 'login')) {
    header('Location: '.'../login.php');
    exit();
} else {

$errores="";

$username = $_POST['username'];
$contraseña = $_POST['contraseña'];

include("../../conexion.php");

if (validaCampoVacio($username)){
    if (!$errores){
        $errores .="?usernameVacio=true";
    } else {
        $errores .="&usernameVacio=true";        
    }
}

if (validaCampoVacio($contraseña)){
    if (!$errores){
        $errores .="?contrasenaVacio=true";
     } else {
        $errores .="&contrasenaVacio=true";        
     }
}

// Preparar la conexión y la consulta
$sqlUsuario = "SELECT * FROM usuario WHERE username = :username";
$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bindParam(':username', $username);
$stmtUsuario->execute();

$resultado = $stmtUsuario->fetchAll(PDO::FETCH_ASSOC);


// Verificar si hay resultados
if (count($resultado) > 0) {

    /* Se crea la variable '$datos' que obtendrá la siguiente fila de la consulta anterior) */
    $obtenerUsuario = $resultado[0];

    // Comprobar que la contraseña sea válida
    if (password_verify($contraseña, $obtenerUsuario['contraseña'])) {
        // Iniciar sesión
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['id_usuario'] = $obtenerUsuario['id_usuario'];

        // Redirigir al usuario
        header("location: ../../pagina_principal.php");
    } else {
        // Manejar el error de contraseña incorrecta
        if ($contraseña != "") {
            if (!$errores) {
                $errores .= "?contrasenaMal=true";
            } else {
                $errores .= "&contrasenaMal=true";
            }
        }
    }
}


else {
    if ($username != "") {
            if (!$errores){
                $errores .="?usernameMal=true";
            } else {
                $errores .="&usernameMal=true";        
            }
    }
        
}

if ($errores!=""){

    $datosRecibidos = array(
        'username' => $username,
        'contraseña' => $contraseña,
    );

    $datosDevueltos=http_build_query($datosRecibidos);
    header("Location: ../login.php". $errores. "&". $datosDevueltos);
    exit();
}else{
    echo"<form id='EnvioCheck' action='../../pagina_principal.php' method='POST'>";
    echo"<input type='hidden' id='username' name='username' value='".$username."'>";
    echo"<input type='hidden' id='contraseña' name='contraseña' value='".$contraseña."'>";
    echo"</form>";
    echo "<script>document.getElementById('EnvioCheck').submit();</script>";
 }
}