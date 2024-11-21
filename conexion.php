<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$dbserver= "localhost";
$dbusername="root";
$dbpassword="";
$dbbasedatos="foro";

try{
    $conn = @mysqli_connect($dbserver, $dbusername, $dbpassword, $dbbasedatos);
} catch (Exception $e) {
    echo '
    <div class="error-container">
        <p>Error en la conexión con la base de datos: ' . $e->getMessage() . '</p>
        <div class="volver-container">
            <a href="./index.php" class="volver-btn">Volver</a>
        </div>
    </div>';
    die();
}