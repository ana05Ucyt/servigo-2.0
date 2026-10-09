<?php

session_start();

/*
|--------------------------------------------------------------------------
| CONEXIÓN A LA BASE DE DATOS
|--------------------------------------------------------------------------
*/

$conexion = new mysqli("localhost", "root", "", "Servigo");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}


/*
|--------------------------------------------------------------------------
| TRABAJOS RECIENTES
|--------------------------------------------------------------------------
*/

$sql = "SELECT
            trabajos.id,
            trabajos.id_tecnico,
            trabajos.titulo,
            trabajos.categoria,
            trabajos.descripcion,
            trabajos.imagen,
            trabajos.fecha,

            usuarios.id AS tecnico_id,
            usuarios.nombre,
            usuarios.apellido,
            usuarios.departamento,
            usuarios.municipio

        FROM trabajos

        INNER JOIN usuarios
            ON trabajos.id_tecnico = usuarios.id

        WHERE usuarios.tipo = 'tecnico'

        ORDER BY trabajos.fecha DESC

        LIMIT 3";


$resultado = $conexion->query($sql);

if (!$resultado) {
    die("Error al obtener los trabajos: " . $conexion->error);
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="css/Principal.css"
    >

    <title>ServiGo</title>

</head>


<body>


<!-- =========================================================
     HEADER
========================================================= -->

<header>

    <div class="logo">

        Servi<span>Go</span>

        <i class="fa-solid fa-screwdriver-wrench"></i>

    </div>


    <nav>

        <a href="Tecnicos.php">
            Técnicos
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

                <a
                    href="PerfilTecnico.php"
                    class="usuario"
                >

                    <i class="fa-solid fa-user"></i>

                    <?php

                    echo htmlspecialchars(
                        $_SESSION["usuario_nombre"]
                    );

                    ?>

                </a>


            <?php else: ?>


                <a
                    href="Perfilcliente.php"
                    class="usuario"
                >

                    <i class="fa-solid fa-user"></i>

                    <?php

                    echo htmlspecialchars(
                        $_SESSION["usuario_nombre"]
                    );

                    ?>

                </a>


            <?php endif; ?>


            <a
                href="Cerrarsesion.php"
                class="cerrar"
            >

                <i class="fa-solid fa-right-from-bracket"></i>

                Cerrar sesión

            </a>


        <?php else: ?>


            <a
                href="Iniciarsesion.php"
                class="iniciar"
            >

                Iniciar sesión

            </a>


            <a
                href="Registro.php"
                class="registrarse"
            >

                Registrarse

            </a>


        <?php endif; ?>


    </div>

</header>



<!-- =========================================================
     HERO
========================================================= -->

<section class="hero">

    <div class="hero-contenido">


        <h1>

            Encuentra técnicos confiables
            para tus trabajos

        </h1>


        <p>

            Conoce sus trabajos realizados,
            experiencia y valoraciones
            antes de contratar.

        </p>


        <form
            class="buscador"
            action="Trabajos.php"
            method="GET"
        >


            <input
                type="text"
                name="buscar"
                placeholder="¿Qué servicio necesitas?"
            >


            <input
                type="text"
                name="ubicacion"
                placeholder="📍 Ubicación"
            >


            <button type="submit">

                <i class="fa-solid fa-magnifying-glass"></i>

                Buscar

            </button>


        </form>

    </div>

</section>



<!-- =========================================================
     CATEGORÍAS
========================================================= -->

<section>

    <div class="titulo-seccion">

        <h2>
            Explora servicios
        </h2>


        <a href="Trabajos.php">
            Ver todos →
        </a>

    </div>


    <div class="categorias">


        <a
            href="Trabajos.php?categoria=Electricidad"
            class="categoria"
        >

            <i class="fa-solid fa-bolt"></i>

            <h3>
                Electricidad
            </h3>

        </a>


        <a
            href="Trabajos.php?categoria=Informática"
            class="categoria"
        >

            <i class="fa-solid fa-laptop-code"></i>

            <h3>
                Informática
            </h3>

        </a>


        <a
            href="Trabajos.php?categoria=Reparación"
            class="categoria"
        >

            <i class="fa-solid fa-screwdriver-wrench"></i>

            <h3>
                Reparaciones
            </h3>

        </a>


        <a
            href="Trabajos.php?categoria=Plomería"
            class="categoria"
        >

            <i class="fa-solid fa-faucet-drip"></i>

            <h3>
                Plomería
            </h3>

        </a>


        <a
            href="Trabajos.php?categoria=Refrigeración"
            class="categoria"
        >

            <i class="fa-solid fa-snowflake"></i>

            <h3>
                Refrigeración
            </h3>

        </a>


        <a
            href="Trabajos.php?categoria=Mantenimiento"
            class="categoria"
        >

            <i class="fa-solid fa-gears"></i>

            <h3>
                Mantenimiento
            </h3>

        </a>


        <a
            href="Trabajos.php?categoria=Limpieza"
            class="categoria"
        >

            <i class="fa-solid fa-broom"></i>

            <h3>
                Limpieza
            </h3>

        </a>


        <a
            href="Trabajos.php?categoria=Jardinería"
            class="categoria"
        >

            <i class="fa-solid fa-seedling"></i>

            <h3>
                Jardinería
            </h3>

        </a>


        <a
            href="Trabajos.php?categoria=Mecánica"
            class="categoria"
        >

            <i class="fa-solid fa-car"></i>

            <h3>
                Mecánica
            </h3>

        </a>


        <a
            href="Trabajos.php?categoria=Cerrajería"
            class="categoria"
        >

            <i class="fa-solid fa-key"></i>

            <h3>
                Cerrajería
            </h3>

        </a>


        <a
            href="Trabajos.php?categoria=Pintura"
            class="categoria"
        >

            <i class="fa-solid fa-paint-roller"></i>

            <h3>
                Pintura
            </h3>

        </a>


    </div>

</section>



<!-- =========================================================
     TRABAJOS RECIENTES
========================================================= -->

<section>

    <div class="titulo-seccion">


        <div>

            <h2>
                Trabajos recientes
            </h2>

        </div>


        <a href="Trabajos.php">
            Ver todos →
        </a>


    </div>


    <div class="trabajos">


        <?php if ($resultado->num_rows > 0): ?>


            <?php while ($trabajo = $resultado->fetch_assoc()): ?>


                <div class="trabajo">


                    <!-- IMAGEN -->

                    <div class="imagen-trabajo">


                        <?php if (!empty($trabajo["imagen"])): ?>


                            <img
                                src="<?php echo htmlspecialchars($trabajo["imagen"]); ?>"
                                alt="<?php echo htmlspecialchars($trabajo["titulo"]); ?>"
                            >


                        <?php else: ?>


                            <?php

                            $icono = "fa-screwdriver-wrench";

                            switch (
                                strtolower(
                                    $trabajo["categoria"]
                                )
                            ) {

                                case "electricidad":

                                    $icono = "fa-bolt";

                                    break;


                                case "informática":
                                case "informatica":

                                    $icono = "fa-laptop-code";

                                    break;


                                case "plomería":
                                case "plomeria":

                                    $icono = "fa-faucet-drip";

                                    break;


                                case "refrigeración":
                                case "refrigeracion":

                                    $icono = "fa-snowflake";

                                    break;


                                case "mantenimiento":

                                    $icono = "fa-gears";

                                    break;


                                case "reparación":
                                case "reparacion":

                                    $icono = "fa-wrench";

                                    break;


                                case "limpieza":

                                    $icono = "fa-broom";

                                    break;


                                case "jardinería":
                                case "jardineria":

                                    $icono = "fa-seedling";

                                    break;


                                case "mecánica":
                                case "mecanica":

                                    $icono = "fa-car";

                                    break;


                                case "cerrajería":
                                case "cerrajeria":

                                    $icono = "fa-key";

                                    break;


                                case "pintura":

                                    $icono = "fa-paint-roller";

                                    break;

                            }

                            ?>


                            <i
                                class="fa-solid <?php echo $icono; ?>"
                            ></i>


                        <?php endif; ?>


                    </div>



                    <!-- CONTENIDO -->

                    <div class="contenido-trabajo">


                        <h3>

                            <?php

                            echo htmlspecialchars(
                                $trabajo["titulo"]
                            );

                            ?>

                        </h3>


                        <div class="info">

                            <i class="fa-solid fa-location-dot"></i>

                            <?php

                            if (
                                !empty(
                                    $trabajo["municipio"]
                                )
                            ) {

                                echo htmlspecialchars(
                                    $trabajo["municipio"]
                                );

                                echo ", ";

                            }

                            echo htmlspecialchars(
                                $trabajo["departamento"]
                            );

                            ?>

                        </div>


                        <div class="rating">

                            <i class="fa-solid fa-star"></i>

                            Técnico verificado

                            <span class="verificado">

                                ✓ Verificado

                            </span>

                        </div>



                        <!-- TECNICO -->

                        <div class="perfil">


                            <div class="tecnico">


                                <div class="avatar">

                                    <?php

                                    $inicial1 =
                                        strtoupper(
                                            substr(
                                                $trabajo["nombre"],
                                                0,
                                                1
                                            )
                                        );

                                    $inicial2 =
                                        strtoupper(
                                            substr(
                                                $trabajo["apellido"],
                                                0,
                                                1
                                            )
                                        );

                                    echo htmlspecialchars(
                                        $inicial1 . $inicial2
                                    );

                                    ?>

                                </div>


                                <div>


                                    <div class="nombre">

                                        <?php

                                        echo htmlspecialchars(
                                            $trabajo["nombre"] .
                                            " " .
                                            $trabajo["apellido"]
                                        );

                                        ?>

                                    </div>


                                    <div class="especialidad">

                                        <?php

                                        echo htmlspecialchars(
                                            $trabajo["categoria"]
                                        );

                                        ?>

                                    </div>


                                </div>


                            </div>


                            <a
                                href="PerfilTecnico.php?id=<?php echo (int)$trabajo["id_tecnico"]; ?>"
                                class="ver"
                            >

                                Ver →

                            </a>


                        </div>


                    </div>


                </div>


            <?php endwhile; ?>


        <?php else: ?>


            <div class="sin-trabajos">

                <i class="fa-solid fa-briefcase"></i>

                <h3>
                    No hay trabajos publicados todavía
                </h3>

                <p>
                    Los trabajos agregados por los técnicos aparecerán aquí.
                </p>

            </div>


        <?php endif; ?>


    </div>

</section>



<!-- =========================================================
     CONFIANZA
========================================================= -->

<section class="confianza">


    <div class="titulo-seccion">

        <h2>
            ¿Por qué confiar en ServiGo?
        </h2>

    </div>


    <div class="beneficios">


        <div class="beneficio">

            <h3>
                Perfiles verificados
            </h3>

            <p>
                Información de los técnicos revisada
                por la plataforma.
            </p>

        </div>


        <div class="beneficio">

            <h3>
                Valoraciones reales
            </h3>

            <p>
                Consulta las opiniones y calificaciones
                de otros clientes.
            </p>

        </div>


        <div class="beneficio">

            <h3>
                Historial de trabajos
            </h3>

            <p>
                Mira los trabajos que el técnico
                ha realizado.
            </p>

        </div>


        <div class="beneficio">

            <h3>
                Profesionales registrados
            </h3>

            <p>
                Encuentra especialistas organizados
                por categoría.
            </p>

        </div>


    </div>

</section>



<!-- =========================================================
     CTA
========================================================= -->

<div class="cta">


    <h2>
        ¿Eres técnico?
    </h2>


    <p>
        Registra tus servicios y muestra tus trabajos
        a nuevos clientes.
    </p>


    <a
        href="Registro.php"
        class="registrarse"
    >

        Registrarme como técnico

    </a>


</div>



<!-- =========================================================
     FOOTER
========================================================= -->

<footer>


    <div class="footer-contenido">


        <div>

            <strong>
                ServiGo
            </strong>

            <p>
                Conectando clientes con técnicos confiables.
            </p>

        </div>


        <span>

            © 2026 ServiGo.
            Todos los derechos reservados.

        </span>


    </div>


</footer>


</body>

</html>


<?php

$resultado->free();

$conexion->close();

?>