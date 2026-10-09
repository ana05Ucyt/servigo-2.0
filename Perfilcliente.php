<?php

session_start();

/*Si no existe la sesión,significa que el usuario no ha iniciado sesión.*/
if (!isset($_SESSION["usuario_id"])) {

    header("Location: Iniciarsesion.php");
    exit;
}


/* Obtener datos de la sesión*/
$nombre = $_SESSION["usuario_nombre"];
$apellido = $_SESSION["usuario_apellido"];
$correo = $_SESSION["usuario_correo"];
$numero = $_SESSION["usuario_numero"];

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    />
    <link rel="stylesheet" href="css/Perfilcliente.css" />
    <title>Document</title>
  </head>
  <body>
    <header>
      <div class="logo">
        Servi<span>Go</span>

        <i class="fa-solid fa-screwdriver-wrench"></i>
      </div>

      <nav>
        <a href="Principal.php"> Inicio </a>

        <a href="Trabajos.php"> Trabajos </a>

        <a href="Nosotros.php"> Nosotros </a>
      </nav>

      <a href="Cerrarsesion.php" class="cerrar">
        <i class="fa-solid fa-right-from-bracket"></i>

        Cerrar sesión
      </a>
    </header>

    <main>
      <section class="perfil">
        <div class="perfil-superior">
          <div class="foto">
            <i class="fa-solid fa-user"></i>
          </div>

          <div class="informacion">
            <span class="tipo">
              <i class="fa-solid fa-user"></i>

              Cliente
            </span>

            <h1>
              <?php echo htmlspecialchars( $nombre . " " . $apellido ); ?>
            </h1>

            <p class="dato">
              <i class="fa-solid fa-envelope"></i>

              <?php echo htmlspecialchars($correo); ?>
            </p>

            <p class="dato">
              <i class="fa-solid fa-phone"></i>

              <?php echo htmlspecialchars($numero); ?>
            </p>
          </div>
        </div>

        <div class="separador"></div>

        <div class="informacion-perfil">
          <h2>Información de mi cuenta</h2>

          <div class="datos">
            <div class="dato-box">
              <span> Nombre </span>

              <strong> <?php echo htmlspecialchars($nombre); ?> </strong>
            </div>

            <div class="dato-box">
              <span> Apellido </span>

              <strong> <?php echo htmlspecialchars($apellido); ?> </strong>
            </div>

            <div class="dato-box">
              <span> Correo electrónico </span>

              <strong> <?php echo htmlspecialchars($correo); ?> </strong>
            </div>

            <div class="dato-box">
              <span> Número de teléfono </span>

              <strong> <?php echo htmlspecialchars($numero); ?> </strong>
            </div>
          </div>
        </div>
      </section>
    </main>
  </body>
</html>
