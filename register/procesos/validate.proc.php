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

    function validarContrasena_8caracteres($contraseña) {

        // Verifica que la longitud sea al menos de 8 caracteres
        if (strlen($contraseña) < 8) {
            $error=true; // Hay un error
        } else {
            $error=false; // No hay un error
        }

    return $error;
    }

    function validarContrasena_letraMayus($contraseña) {

        // Verifica que contenga al menos una letra mayúscula
        if (preg_match("/[A-Z]/", $contraseña)) {
            $error=true; // Hay un error
        } else {
            $error=false; // No hay un error
        }

    return $error;
    }

    function validarContrasena_caractSpecial($contraseña) {

        // Verifica que contenga al menos un carácter especial
        if (preg_match('/[\W]/', $contraseña)) {
            $error=true; // Hay un error
        } else {
            $error=false; // No hay un error
        }

    return $error;
    }

    function validarFormatoEmail($correo) {

        // Patrón para verificar el email con al menos 3 caracteres después del @ y al menos 2 caracteres después del último .
        $verificacion = "/^[\w\.-]+@[a-zA-Z\d-]{3,}\.[a-zA-Z]{2,}$/";
    
        // Verificar si el correo coincide con el patrón
        if (preg_match($verificacion, $correo)) {
            return false; // No hay error
        } else {
            return true; // Hay un error
        }
    }

?>

<?php
if (!filter_has_var(INPUT_POST, 'register')) {
    header('Location: '.'./register.proc.php');
    exit();
} else {

$errores="";

$username = $_POST['username'];
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$correo = $_POST['correo'];
$contraseña = $_POST['contraseña'];


if (validaCampoVacio($username)){
    if (!$errores){
        $errores .="?nicknameVacio=true";
     } else {
        $errores .="&nicknameVacio=true";        
     }
  } else {
    if(!preg_match("/^[a-zA-Z0-9]*$/",$username)){
        if (!$errores){
            $errores .="?nicknameMal=true";
         } else {
            $errores .="&nicknameMal=true";        
         }
    }
}

if (validaCampoVacio($nombre)){
    if (!$errores){
        $errores .="?usernameVacio=true";
     } else {
        $errores .="&usernameVacio=true";        
     }
  } else {
    if(!preg_match("/^[a-zA-Z0-9]*$/",$nombre)){
        if (!$errores){
            $errores .="?usernameMal=true";
         } else {
            $errores .="&usernameMal=true";        
         }
    }
}

if (validaCampoVacio($apellidos)){
    if (!$errores){
        $errores .="?apellidoVacio=true";
     } else {
        $errores .="&apellidoVacio=true";        
     }
  } else {
    if(!preg_match("/^[A-Z][a-z]+ [A-Z][a-z]+$/",$apellidos)){
        if (!$errores){
            $errores .="?apellidoMal=true";
        } else {
            $errores .="&apellidoMal=true";        
        }
    }
}

if (validaCampoVacio($correo)){

    if (!$errores){
        $errores .="?emailVacio=true";
    } else {
        $errores .="&emailVacio=true";        
    }

} else {

    if(validarFormatoEmail($correo)){
        if (!$errores){
            $errores .="?emailMal=true";
        } else {
            $errores .="&emailMal=true";        
        }
    }

}

if (validaCampoVacio($contraseña)){

    if (!$errores){
        $errores .="?contrasenaVacio=true";
    } else {
        $errores .="&contrasenaVacio=true";        
    }

} elseif (validarContrasena_8caracteres($contraseña)) {

    if (!$errores) {
        $errores .= "?passwordMal8car=true";
    } else {
        $errores .= "&passwordMal8car=true";
    }

} elseif (!validarContrasena_letraMayus($contraseña)) {

    if (!$errores) {
        $errores .= "?passwordMalMayus=true";
    } else {
        $errores .= "&passwordMalMayus=true";
    }

} else {

    if (!validarContrasena_caractSpecial($contraseña)) {
        if (!$errores) {
            $errores .= "?passwordMalSpecCar=true";
        } else {
            $errores .= "&passwordMalSpecCar=true";
        }
    }

} 


if ($errores!=""){

    $datosRecibidos = array(
        'username' => $username,
        'nombre' => $nombre,
        'apellidos'=> $apellidos,
        'correo' => $correo,
        'contraseña' => $contraseña
    );
    
    $datosDevueltos=http_build_query($datosRecibidos);
    header("Location: ../register.php". $errores. "&". $datosDevueltos);
    exit();
}else{
    echo"<form id='EnvioCheck' action='register.proc.php' method='POST'>";
    echo"<input type='hidden' id='username' name='username' value='".$username."'>";
    echo"<input type='hidden' id='nombre' name='nombre' value='".$nombre."'>";
    echo"<input type='hidden' id='apellidos' name='apellidos' value='".$apellidos."'>";
    echo"<input type='hidden' id='correo' name='correo' value='".$correo."'>";
    echo"<input type='hidden' id='contraseña' name='contraseña' value='".$contraseña."'>";
    echo"</form>";
    echo "<script>document.getElementById('EnvioCheck').submit();</script>";
 }
}

