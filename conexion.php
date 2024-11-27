<?php
try{

    $conn =  new PDO('mysql:host=localhost; dbname=foro', 'root', '');

} catch (Exception $e) {

    echo '
    <div class="error-container">
        <p>Error en la conexión con la base de datos: ' . $e->getMessage() . '</p>
        <div class="volver-container">
            <a href="./pagina_principal.php" class="volver-btn">Volver</a>
        </div>
    </div>';
    die();

}