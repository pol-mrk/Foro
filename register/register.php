<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Whatsapp2 - Register</title>
    <link rel="shortcut icon" href="../src/LOGO/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="./register.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500&display=swap" rel="stylesheet">
</head>

<body>
    <div class="flex" id="oscuro">
        <div class="container">
            <h2 class="flex" id="titulo">REGISTRO</h2>
            <br>
            <form action="./procesos/validate.proc.php" method="POST">
                <div class="inputs">
                    <label for="username">Apodo:</label>
                    <input type="text" class="form-control" name="username" id="username" value="<?php if(isset($_GET['username'])) {echo $_GET['username'];} ?>">
                    <?php if (isset($_GET['nicknameVacio'])) {echo "<br><br><p class='editaNombre'>Falta tu apodo</p>"; } ?>
                    <?php if (isset($_GET['nicknameMal'])) {echo "<br><br><p class='editaNombre'>Tu apodo solo puede contener letras y números</p>"; } ?>
                </div>
                <div class="inputs">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" value="<?php if(isset($_GET['nombre'])) {echo $_GET['nombre'];} ?>">
                    <?php if (isset($_GET['usernameVacio'])) {echo "<br><br><p class='editaNombre'>Falta tu nombre</p>"; } ?>
                    <?php if (isset($_GET['usernameMal'])) {echo "<br><br><p class='editaNombre'>Tu nombre solo puede contener letras y números</p>"; } ?>
                </div>
                <div class="inputs">
                    <label for="apellidos">Apellidos:</label>
                    <input type="text" class="form-control" name="apellidos" id="apellidos" value="<?php if(isset($_GET['apellidos'])) {echo $_GET['apellidos'];} ?>">
                    <?php if (isset($_GET['apellidoVacio'])) {echo "<br><br><p class='editaApellido'>Falta tu apellido</p>"; } ?>
                    <?php if (isset($_GET['apellidoMal'])) {echo "<br><br><p class='editaApellido'>Tus apellidos deben ser dos palabras con la primera letra mayúscula, separadas por un espacio</p>"; } ?>
                </div>
                <div class="inputs">
                    <label for="email">Email:</label>
                    <input type="text" class="form-control" name="correo" id="correo" value="<?php if(isset($_GET['correo'])) {echo $_GET['correo'];} ?>">
                    <?php if (isset($_GET['emailVacio'])) {echo "<br><br><p class='editaCorreo'>Falta tu correo</p>"; } ?>
                    <?php if (isset($_GET['emailMal'])) {echo "<br><br><p class='editaCorreo'>Tu correo debe contener un @, más de 3 letras después, un punto y al menos 2 letras después de este.</p>"; } ?>
                </div>
                <div class="inputs">
                    <label for="contraseña">Contraseña:</label>
                    <input type="contraseña" class="form-control" name="contraseña" id="contraseña">
                    <?php
                    if (isset($_GET['contrasenaVacio'])) {echo "<br><br><p class='editaContraseña'>Escribe tu contraseña</p>"; }
                    if (isset($_GET['passwordMal8car'])) {echo "<br><br><p class='editaContraseña'>La contraseña debe tener al menos 8 caracteres.</p>";}
                    if (isset($_GET['passwordMalMayus'])) {echo "<br><br><p class='editaContraseña'>La contraseña debe contener al menos una letra mayúscula.</p>";}
                    if (isset($_GET['passwordMalSpecCar'])) {echo "<br><br><p class='editaContraseña'>La contraseña debe contener al menos un carácter especial.</p>";}
                    ?>
                </div>
                <br>
                <button type="submit" name="register" value="register" class="boton">Registrarse</button>
                <br>
                <p id="registrarse">Ya tienes cuenta?
                    <a href="../login/login.php" id="registrarse">Inicia Sesión</a>
                </p>
            </form>
</body>

</html>