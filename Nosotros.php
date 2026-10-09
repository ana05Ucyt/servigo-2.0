<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/Nosotros.css">
    <title>Document</title>
</head>
<body>

<header class="navbar">

    <div class="logo">
        Servi<span>Go</span>
        <i class="fa-solid fa-screwdriver-wrench"></i>
    </div>


    <nav>
        <a href="Principal.php">
            Inicio
        </a>

        <a href="Trabajos.php">
            Trabajos
        </a>

        <a href="PerfilTecnico.php">
            Técnicos
        </a>

    </nav>

<div class="acciones">

        <?php if (isset($_SESSION["usuario_id"])): ?>

            <?php if ($_SESSION["usuario_tipo"] === "tecnico"): ?>

                <a href="PerfilTecnico.php" class="usuario">
                    <i class="fa-solid fa-user"></i>
                    <?php echo htmlspecialchars($_SESSION["usuario_nombre"]); ?>
                </a>

            <?php else: ?>

                <a href="Perfilcliente.php" class="usuario">
                    <i class="fa-solid fa-user"></i>
                    <?php echo htmlspecialchars($_SESSION["usuario_nombre"]); ?>
                </a>

            <?php endif; ?>


            <a href="Cerrarsesion.php" class="cerrar">
                <i class="fa-solid fa-right-from-bracket"></i>
                Cerrar sesión
            </a>


        <?php else: ?>

            <a href="Iniciarsesion.php" class="iniciar">
                Iniciar sesión
            </a>

            <a href="Registro.php" class="registrarse">
                Registrarse
            </a>

        <?php endif; ?>

    </div>

</header>


<main>

    <section class="hero-nosotros">
        <div class="hero-contenido">
            <span class="etiqueta">Sobre nosotros</span>

            <h1>
                Conectamos personas con
                técnicos en quienes confiar
            </h1>

            <p>
                ServiGo nace para que encontrar un profesional
                confiable sea tan simple como buscarlo, comparar
                y decidir.
            </p>

        </div>
    </section>


    <section class="mision">

        <div class="mision-texto">

            <h2>
                Nuestra misión
            </h2>

            <p>
                Creemos que contratar un servicio técnico no
                debería ser un salto de fe. Por eso construimos
                una plataforma donde cada técnico muestra su
                trabajo real, sus valoraciones y su experiencia,
                para que tú decidas con información y no con
                incertidumbre.
            </p>

            <p>
                Desde instalaciones eléctricas hasta reparaciones
                del hogar, conectamos a quienes necesitan un
                servicio con quienes saben hacerlo bien.
            </p>
        </div>
    </section>


    <section class="valores">

        <div class="titulo-seccion">
            <h2>
                Lo que nos define
            </h2>
        </div>

        <div class="valores-grid">

            <div class="valor">
                <h3>
                    Confianza
                </h3>
                <p>
                    Perfiles verificados y valoraciones reales
                    de clientes, para que sepas con quién trabajas.
                </p>
            </div>

            <div class="valor">
                <h3>
                    Transparencia
                </h3>
                <p>
                    Trabajos, precios y experiencia a la vista
                    antes de contratar, sin sorpresas.
                </p>
            </div>

            <div class="valor">
                <h3>
                    Comunidad
                </h3>
                <p>
                    Impulsamos técnicos locales y ayudamos a
                    familias a resolver sus problemas del día a día.
                </p>
            </div>
        </div>
    </section>
</main>


<footer>
    <div class="footer-contenido">
        <div>

            <strong>
                ServiGo
            </strong>

            <p>Conectando clientes con técnicos confiables.</p>
        </div>


        <span>
            © 2026 ServiGo. Todos los derechos reservados.
        </span>
    </div>
</footer>

</body>
</html>