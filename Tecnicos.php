<?php

session_start();

$conexion = new mysqli("localhost", "root", "", "Servigo");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}


/* Obtener todos los técnicos */

$sql = "SELECT id, nombre, apellido, correo, numero, especialidad, departamento, municipio, descripcion, foto
        FROM usuarios
        WHERE tipo = 'tecnico'
        ORDER BY nombre ASC";

$resultado = $conexion->query($sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

    <link rel="stylesheet" href="css/Tecnicos.css">

    <title>Técnicos | ServiGo</title>

</head>

<body>


<header>

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

        <a href="Nosotros.php">
            Nosotros
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


    <section class="hero">

        <div>

            <span class="etiqueta">
                <i class="fa-solid fa-users"></i>
                Profesionales registrados
            </span>

            <h1>
                Encuentra técnicos confiables
            </h1>

            <p>
                Explora los técnicos registrados en ServiGo,
                conoce su especialidad, experiencia y ubicación.
            </p>

        </div>

    </section>



    <section class="contenedor">


        <div class="titulo">

            <div>

                <h2>
                    Técnicos disponibles
                </h2>

                <p>
                    Encuentra el profesional que necesitas.
                </p>

            </div>


            <div class="buscar">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    placeholder="Buscar técnico..."
                >

            </div>

        </div>



        <div class="grid-tecnicos">


            <?php if ($resultado->num_rows > 0): ?>


                <?php while ($tecnico = $resultado->fetch_assoc()): ?>


                    <article class="tarjeta">


                        <div class="foto">

                            <?php if (!empty($tecnico["foto"])): ?>

                                <img
                                    src="<?php echo htmlspecialchars($tecnico["foto"]); ?>"
                                    alt="Foto del técnico"
                                >

                            <?php else: ?>

                                <i class="fa-solid fa-user"></i>

                            <?php endif; ?>

                        </div>



                        <div class="contenido">


                            <h3>

                                <?php
                                echo htmlspecialchars(
                                    $tecnico["nombre"] . " " . $tecnico["apellido"]
                                );
                                ?>

                            </h3>


                            <span class="especialidad">

                                <i class="fa-solid fa-screwdriver-wrench"></i>

                                <?php

                                echo !empty($tecnico["especialidad"])
                                    ? htmlspecialchars($tecnico["especialidad"])
                                    : "Técnico profesional";

                                ?>

                            </span>


                            <p class="ubicacion">

                                <i class="fa-solid fa-location-dot"></i>

                                <?php

                                if (!empty($tecnico["municipio"])) {

                                    echo htmlspecialchars(
                                        $tecnico["municipio"] . ", " .
                                        $tecnico["departamento"]
                                    );

                                } else {

                                    echo htmlspecialchars(
                                        $tecnico["departamento"]
                                    );

                                }

                                ?>

                            </p>


                            <?php if (!empty($tecnico["descripcion"])): ?>

                                <p class="descripcion">

                                    <?php
                                    echo htmlspecialchars(
                                        $tecnico["descripcion"]
                                    );
                                    ?>

                                </p>

                            <?php endif; ?>


                            <a
                                href="PerfilTecnico.php?id=<?php echo $tecnico["id"]; ?>"
                                class="ver-perfil"
                            >

                                Ver perfil

                                <i class="fa-solid fa-arrow-right"></i>

                            </a>


                        </div>


                    </article>


                <?php endwhile; ?>


            <?php else: ?>


                <div class="sin-tecnicos">

                    <i class="fa-solid fa-users"></i>

                    <h3>
                        No hay técnicos registrados
                    </h3>

                    <p>
                        Todavía no hay técnicos disponibles en ServiGo.
                    </p>

                </div>


            <?php endif; ?>


        </div>


    </section>

</main>



<footer>

    <div>

        <strong>
            ServiGo
        </strong>

        <p>
            Conectando clientes con técnicos confiables.
        </p>

    </div>

    <span>
        © 2026 ServiGo. Todos los derechos reservados.
    </span>

</footer>


</body>

</html>

<?php

$conexion->close();

?>