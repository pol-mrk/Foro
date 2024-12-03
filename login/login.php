<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Whatsapp2 - Login</title>
    <link rel="shortcut icon" href="../src/LOGO/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="./login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500&display=swap" rel="stylesheet">
</head>

<body>
    <div class="flex" id="oscuro">
        <div class="container">
            <h2 class="flex" id="titulo">INICIO DE SESION</h2>
            <br>

            <form action="./procesos/validate2.proc.php" method="POST">
                <div class="inputs">
                    <label for="username">Usuario:</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php if(isset($_GET['username'])) {echo $_GET['username'];} ?>">
                    <?php if (isset($_GET['usernameVacio'])) {echo "<br><br><p class='editaNombre'>Falta tu nombre</p>"; } ?>
                </div>
                <div class="inputs">
                    <label for="password">Contraseña:</label>
                    <input type="password" class="form-control" id="contraseña" name="contraseña">
                    <?php if (isset($_GET['contrasenaVacio'])) {echo "<br><br><p class='editaContraseña'>Escribe tu contraseña</p>"; } ?>
                    <?php if (isset($_GET['contrasenaMal']) || isset($_GET['usernameMal'])) {echo "<br><br><p class='editaContraseña'>Usuario o contraseña incorrecta</p>"; } ?>
                </div>
                <br>
                <br>
                <button type="submit" name="login" value="login" class="boton">Iniciar sesión</button>
                <br>
                <p id="registrarse">No tienes cuenta?
                    <a href="../register/register.php" id="registrarse">Registrate</p></a>
            </form>
        </div>
    </div>
</body>

</html>