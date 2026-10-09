<?php

session_start();

$conexion = new mysqli("localhost", "root", "", "Servigo");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}


/* =========================================================
   FILTROS
========================================================= */

$buscar = trim($_GET["buscar"] ?? "");
$categoria = trim($_GET["categoria"] ?? "");
$departamento = trim($_GET["departamento"] ?? "");


/* =========================================================
   CONSULTA
========================================================= */

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

        WHERE usuarios.tipo = 'tecnico'";


/* =========================================================
   BUSCADOR
========================================================= */

if ($buscar !== "") {

    $sql .= " AND (
        trabajos.titulo LIKE ?
        OR trabajos.descripcion LIKE ?
        OR trabajos.categoria LIKE ?
        OR usuarios.nombre LIKE ?
        OR usuarios.apellido LIKE ?
    )";
}


/* =========================================================
   FILTRO CATEGORÍA
========================================================= */

if ($categoria !== "") {

    $sql .= " AND trabajos.categoria = ?";
}


/* =========================================================
   FILTRO DEPARTAMENTO
========================================================= */

if ($departamento !== "") {

    $sql .= " AND usuarios.departamento = ?";
}


/* =========================================================
   ORDENAR
========================================================= */

$sql .= " ORDER BY trabajos.fecha DESC";


$stmt = $conexion->prepare($sql);

if ($stmt === false) {

    die(
        "Error en la consulta: " .
        $conexion->error
    );

}


/* =========================================================
   PARÁMETROS
========================================================= */

$tipos = "";
$parametros = [];


if ($buscar !== "") {

    $buscar_sql = "%" . $buscar . "%";

    $tipos .= "sssss";

    $parametros[] = $buscar_sql;
    $parametros[] = $buscar_sql;
    $parametros[] = $buscar_sql;
    $parametros[] = $buscar_sql;
    $parametros[] = $buscar_sql;
}


if ($categoria !== "") {

    $tipos .= "s";

    $parametros[] = $categoria;
}


if ($departamento !== "") {

    $tipos .= "s";

    $parametros[] = $departamento;
}


if (!empty($parametros)) {

    $stmt->bind_param(
        $tipos,
        ...$parametros
    );

}


$stmt->execute();

$resultado_trabajos = $stmt->get_result();

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
        href="css/Trabajos.css"
    >

    <title>
        Trabajos | ServiGo
    </title>

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

        <a href="Tecnicos.php">
            Técnicos
        </a>

        <a href="Trabajos.php" class="activo">
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


<main>


<!-- ======================================================
     HERO
====================================================== -->

<section class="hero">

    <div class="hero-contenido">

        <span class="etiqueta">

            <i class="fa-solid fa-screwdriver-wrench"></i>

            Trabajos realizados

        </span>


        <h1>

            Conoce el trabajo de
            nuestros técnicos

        </h1>


        <p>

            Explora trabajos realizados por técnicos
            de nuestra comunidad y encuentra profesionales
            confiables para tu próximo proyecto.

        </p>

    </div>

</section>


<!-- ======================================================
     CONTENEDOR
====================================================== -->

<section class="contenedor">


    <div class="barra-superior">


        <div>

            <h2>
                Trabajos publicados
            </h2>

            <p>
                Descubre proyectos realizados por nuestros técnicos.
            </p>

        </div>


        <!-- ==================================================
             FILTROS
        ================================================== -->

        <form
            method="GET"
            action="Trabajos.php"
            class="filtros"
        >


            <!-- BUSCAR -->

            <div class="buscar">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="search"
                    name="buscar"
                    placeholder="Buscar trabajo..."
                    value="<?php echo htmlspecialchars($buscar); ?>"
                >

            </div>


            <!-- CATEGORÍA -->

            <select name="categoria">

                <option value="">
                    Todas las categorías
                </option>


                <option
                    value="Electricidad"
                    <?php
                    echo ($categoria === "Electricidad")
                        ? "selected"
                        : "";
                    ?>
                >
                    Electricidad
                </option>


                <option
                    value="Informática"
                    <?php
                    echo ($categoria === "Informática")
                        ? "selected"
                        : "";
                    ?>
                >
                    Informática
                </option>


                <option
                    value="Refrigeración"
                    <?php
                    echo ($categoria === "Refrigeración")
                        ? "selected"
                        : "";
                    ?>
                >
                    Refrigeración
                </option>


                <option
                    value="Plomería"
                    <?php
                    echo ($categoria === "Plomería")
                        ? "selected"
                        : "";
                    ?>
                >
                    Plomería
                </option>


                <option
                    value="Carpintería"
                    <?php
                    echo ($categoria === "Carpintería")
                        ? "selected"
                        : "";
                    ?>
                >
                    Carpintería
                </option>


                <option
                    value="Reparación"
                    <?php
                    echo ($categoria === "Reparación")
                        ? "selected"
                        : "";
                    ?>
                >
                    Reparación
                </option>


                <option
                    value="Mantenimiento"
                    <?php
                    echo ($categoria === "Mantenimiento")
                        ? "selected"
                        : "";
                    ?>
                >
                    Mantenimiento
                </option>


                <option
                    value="Construcción"
                    <?php
                    echo ($categoria === "Construcción")
                        ? "selected"
                        : "";
                    ?>
                >
                    Construcción
                </option>


                <option
                    value="Otro"
                    <?php
                    echo ($categoria === "Otro")
                        ? "selected"
                        : "";
                    ?>
                >
                    Otro
                </option>

            </select>


            <!-- DEPARTAMENTO -->

            <select name="departamento">

                <option value="">
                    Todos los departamentos
                </option>


                <option
                    value="Managua"
                    <?php
                    echo ($departamento === "Managua")
                        ? "selected"
                        : "";
                    ?>
                >
                    Managua
                </option>


                <option
                    value="Masaya"
                    <?php
                    echo ($departamento === "Masaya")
                        ? "selected"
                        : "";
                    ?>
                >
                    Masaya
                </option>


                <option
                    value="Granada"
                    <?php
                    echo ($departamento === "Granada")
                        ? "selected"
                        : "";
                    ?>
                >
                    Granada
                </option>


                <option
                    value="León"
                    <?php
                    echo ($departamento === "León")
                        ? "selected"
                        : "";
                    ?>
                >
                    León
                </option>


                <option
                    value="Estelí"
                    <?php
                    echo ($departamento === "Estelí")
                        ? "selected"
                        : "";
                    ?>
                >
                    Estelí
                </option>


                <option
                    value="Matagalpa"
                    <?php
                    echo ($departamento === "Matagalpa")
                        ? "selected"
                        : "";
                    ?>
                >
                    Matagalpa
                </option>


                <option
                    value="Chinandega"
                    <?php
                    echo ($departamento === "Chinandega")
                        ? "selected"
                        : "";
                    ?>
                >
                    Chinandega
                </option>


                <option
                    value="Jinotega"
                    <?php
                    echo ($departamento === "Jinotega")
                        ? "selected"
                        : "";
                    ?>
                >
                    Jinotega
                </option>


                <option
                    value="Carazo"
                    <?php
                    echo ($departamento === "Carazo")
                        ? "selected"
                        : "";
                    ?>
                >
                    Carazo
                </option>


                <option
                    value="Rivas"
                    <?php
                    echo ($departamento === "Rivas")
                        ? "selected"
                        : "";
                    ?>
                >
                    Rivas
                </option>

            </select>


            <!-- BOTÓN BUSCAR -->

            <button
                type="submit"
                class="btn-buscar"
            >

                <i class="fa-solid fa-magnifying-glass"></i>

                Buscar

            </button>


            <!-- LIMPIAR -->

            <?php if (
                $buscar !== "" ||
                $categoria !== "" ||
                $departamento !== ""
            ): ?>

                <a
                    href="Trabajos.php"
                    class="btn-limpiar"
                >

                    <i class="fa-solid fa-xmark"></i>

                    Limpiar

                </a>

            <?php endif; ?>


        </form>


    </div>


    <!-- ==================================================
         TRABAJOS
    ================================================== -->

    <div class="grid-trabajos">


        <?php if ($resultado_trabajos->num_rows > 0): ?>


            <?php while ($trabajo = $resultado_trabajos->fetch_assoc()): ?>


                <article class="tarjeta-trabajo">


                    <!-- IMAGEN -->

                    <div class="imagen">


                        <?php if (!empty($trabajo["imagen"])): ?>


                            <img
                                src="<?php echo htmlspecialchars($trabajo["imagen"]); ?>"
                                alt="<?php echo htmlspecialchars($trabajo["titulo"]); ?>"
                            >


                        <?php else: ?>


                            <div
                                style="
                                    width:100%;
                                    height:100%;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    background:#e9ecef;
                                    color:#adb5bd;
                                    font-size:45px;
                                "
                            >

                                <i class="fa-solid fa-image"></i>

                            </div>


                        <?php endif; ?>


                        <!-- CATEGORÍA -->

                        <span class="categoria">


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

                                    $icono = "fa-computer";

                                    break;


                                case "refrigeración":
                                case "refrigeracion":

                                    $icono = "fa-snowflake";

                                    break;


                                case "plomería":
                                case "plomeria":

                                    $icono = "fa-faucet";

                                    break;


                                case "carpintería":
                                case "carpinteria":

                                    $icono = "fa-hammer";

                                    break;


                                case "mantenimiento":

                                    $icono = "fa-gears";

                                    break;


                                case "reparación":
                                case "reparacion":

                                    $icono = "fa-wrench";

                                    break;


                                case "construcción":
                                case "construccion":

                                    $icono = "fa-building";

                                    break;

                            }

                            ?>


                            <i
                                class="fa-solid <?php echo $icono; ?>"
                            ></i>


                            <?php

                            echo htmlspecialchars(
                                $trabajo["categoria"]
                            );

                            ?>


                        </span>


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


                        <p class="descripcion">

                            <?php

                            if (
                                !empty(
                                    $trabajo["descripcion"]
                                )
                            ) {

                                echo nl2br(
                                    htmlspecialchars(
                                        $trabajo["descripcion"]
                                    )
                                );

                            } else {

                                echo "Trabajo realizado por este técnico.";

                            }

                            ?>

                        </p>


                        <!-- TÉCNICO -->

                        <div class="tecnico">


                            <div class="avatar">

                                <?php

                                $inicialNombre =
                                    strtoupper(
                                        substr(
                                            $trabajo["nombre"],
                                            0,
                                            1
                                        )
                                    );

                                $inicialApellido =
                                    strtoupper(
                                        substr(
                                            $trabajo["apellido"],
                                            0,
                                            1
                                        )
                                    );

                                echo htmlspecialchars(
                                    $inicialNombre .
                                    $inicialApellido
                                );

                                ?>

                            </div>


                            <div>


                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $trabajo["nombre"] .
                                        " " .
                                        $trabajo["apellido"]
                                    );

                                    ?>

                                </strong>


                                <span>

                                    <i class="fa-solid fa-location-dot"></i>

                                    <?php

                                    if (
                                        !empty(
                                            $trabajo["municipio"]
                                        )
                                    ) {

                                        echo htmlspecialchars(
                                            $trabajo["municipio"] .
                                            ", " .
                                            $trabajo["departamento"]
                                        );

                                    } else {

                                        echo htmlspecialchars(
                                            $trabajo["departamento"]
                                        );

                                    }

                                    ?>

                                </span>


                            </div>


                        </div>


                        <!-- FECHA Y PERFIL -->

                        <div class="parte-inferior">


                            <span class="fecha">

                                <i class="fa-regular fa-calendar"></i>

                                <?php

                                if (!empty($trabajo["fecha"])) {

                                    echo date(
                                        "d/m/Y",
                                        strtotime(
                                            $trabajo["fecha"]
                                        )
                                    );

                                } else {

                                    echo "Sin fecha";

                                }

                                ?>

                            </span>


                            <a
                                href="PerfilTecnico.php?id=<?php echo (int)$trabajo["id_tecnico"]; ?>"
                            >

                                Ver técnico

                                <i class="fa-solid fa-arrow-right"></i>

                            </a>


                        </div>


                    </div>


                </article>


            <?php endwhile; ?>


        <?php else: ?>


            <div
                style="
                    grid-column:1/-1;
                    text-align:center;
                    padding:60px 20px;
                "
            >

                <i
                    class="fa-solid fa-magnifying-glass"
                    style="
                        font-size:50px;
                        color:#adb5bd;
                        margin-bottom:15px;
                    "
                ></i>


                <h3>
                    No se encontraron trabajos
                </h3>


                <p>
                    Intenta cambiar los filtros o realizar otra búsqueda.
                </p>


                <a href="Trabajos.php">

                    Ver todos los trabajos

                </a>


            </div>


        <?php endif; ?>


    </div>


</section>


</main>


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

$stmt->close();

$conexion->close();

?>