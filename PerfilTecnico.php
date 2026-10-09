<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$conexion = new mysqli("localhost", "root", "", "Servigo");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

/*
|--------------------------------------------------------------------------
| Determinar qué técnico mostrar
|--------------------------------------------------------------------------
*/

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {

    $id_tecnico = (int) $_GET["id"];

} elseif (
    isset($_SESSION["usuario_id"]) &&
    isset($_SESSION["usuario_tipo"]) &&
    $_SESSION["usuario_tipo"] === "tecnico"
) {

    $id_tecnico = (int) $_SESSION["usuario_id"];

} else {

    header("Location: Tecnicos.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Buscar técnico
|--------------------------------------------------------------------------
*/

$sql = "SELECT 
            id,
            nombre,
            apellido,
            correo,
            numero,
            especialidad,
            departamento,
            municipio,
            descripcion,
            foto
        FROM usuarios
        WHERE id = ?
        AND tipo = 'tecnico'
        LIMIT 1";

$stmt = $conexion->prepare($sql);

if ($stmt === false) {
    die("Error en la consulta del técnico: " . $conexion->error);
}

$stmt->bind_param("i", $id_tecnico);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows !== 1) {
    die("Técnico no encontrado.");
}

$tecnico = $resultado->fetch_assoc();

$stmt->close();

/*
|--------------------------------------------------------------------------
| Buscar trabajos del técnico
|--------------------------------------------------------------------------
*/

$sql_trabajos = "SELECT
                    id,
                    id_tecnico,
                    titulo,
                    categoria,
                    descripcion,
                    imagen,
                    fecha
                 FROM trabajos
                 WHERE id_tecnico = ?
                 ORDER BY fecha DESC";

$stmt_trabajos = $conexion->prepare($sql_trabajos);

if ($stmt_trabajos === false) {
    die("Error en la consulta de trabajos: " . $conexion->error);
}

$stmt_trabajos->bind_param("i", $id_tecnico);
$stmt_trabajos->execute();

$resultado_trabajos = $stmt_trabajos->get_result();

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
        href="css/PerfilTecnico.css"
    >

    <title>

        <?php
        echo htmlspecialchars(
            $tecnico["nombre"] . " " . $tecnico["apellido"]
        );
        ?>

        | ServiGo

    </title>

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

<main>

<section class="perfil">

    <div class="perfil-superior">

        <!-- FOTO -->

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

        <!-- INFORMACIÓN -->

        <div class="informacion">

            <div class="nombre">

                <h1>

                    <?php
                    echo htmlspecialchars(
                        $tecnico["nombre"] . " " . $tecnico["apellido"]
                    );
                    ?>

                </h1>

                <span class="verificado">

                    <i class="fa-solid fa-check"></i>

                    Verificado

                </span>

            </div>

            <p class="especialidad">

                <?php

                if (!empty($tecnico["especialidad"])) {

                    echo htmlspecialchars(
                        $tecnico["especialidad"]
                    );

                } else {

                    echo "Técnico profesional";

                }

                ?>

            </p>

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

            <!-- BOTONES DEL PERFIL -->

            <div class="accion">

                <?php if (
                    isset($_SESSION["usuario_id"]) &&
                    isset($_SESSION["usuario_tipo"]) &&
                    $_SESSION["usuario_tipo"] === "tecnico" &&
                    (int)$_SESSION["usuario_id"] === $id_tecnico
                ): ?>

                    <div class="botones-perfil">

                        <a
                            href="EditarTecnico.php"
                            class="btn-editar"
                        >

                            <i class="fa-solid fa-pen"></i>

                            Editar perfil

                        </a>

                        <a
                            href="AgregarTrabajo.php"
                            class="btn-editar"
                        >

                            <i class="fa-solid fa-plus"></i>

                            Agregar trabajo

                        </a>

                    </div>

                <?php else: ?>

                    <a
                        href="tel:<?php echo htmlspecialchars($tecnico["numero"]); ?>"
                        class="btn-contactar"
                    >

                        <i class="fa-solid fa-phone"></i>

                        Contactar

                    </a>

                <?php endif; ?>

            </div>

        </div>

    </div>

    <!-- INFORMACIÓN -->

    <div class="contenido">

        <section class="sobre">

            <h2>
                Sobre mí
            </h2>

            <p>

                <?php

                if (!empty($tecnico["descripcion"])) {

                    echo nl2br(
                        htmlspecialchars(
                            $tecnico["descripcion"]
                        )
                    );

                } else {

                    echo "Este técnico todavía no ha agregado una descripción.";

                }

                ?>

            </p>

        </section>

        <section class="servicios">

            <h2>
                Información de contacto
            </h2>

            <div class="lista-servicios">

                <span>

                    <i class="fa-solid fa-envelope"></i>

                    <?php
                    echo htmlspecialchars(
                        $tecnico["correo"]
                    );
                    ?>

                </span>

                <span>

                    <i class="fa-solid fa-phone"></i>

                    <?php
                    echo htmlspecialchars(
                        $tecnico["numero"]
                    );
                    ?>

                </span>

                <span>

                    <i class="fa-solid fa-location-dot"></i>

                    <?php
                    echo htmlspecialchars(
                        $tecnico["departamento"]
                    );
                    ?>

                </span>

            </div>

        </section>

    </div>

</section>

<!-- ================================================= -->
<!-- TRABAJOS DINÁMICOS -->
<!-- ================================================= -->

<section class="trabajos">

    <div class="titulo-seccion">

        <div>

            <h2>
                Trabajos realizados
            </h2>

            <p>
                Algunos de los trabajos realizados por este técnico.
            </p>

        </div>

        <a href="Trabajos.php">

            Ver todos

            <i class="fa-solid fa-arrow-right"></i>

        </a>

    </div>

    <div class="trabajos-grid">

        <?php if ($resultado_trabajos->num_rows > 0): ?>

            <?php while ($trabajo = $resultado_trabajos->fetch_assoc()): ?>

                <article class="trabajo">

                    <!-- IMAGEN -->

                    <div class="imagen-trabajo">

                        <?php if (!empty($trabajo["imagen"])): ?>

                            <img
                                src="<?php echo htmlspecialchars($trabajo["imagen"]); ?>"
                                alt="<?php echo htmlspecialchars($trabajo["titulo"]); ?>"
                            >

                        <?php else: ?>

                            <i class="fa-solid fa-image"></i>

                        <?php endif; ?>

                    </div>

                    <!-- INFORMACIÓN DEL TRABAJO -->

                    <div class="trabajo-info">

                        <span class="categoria">

                            <?php
                            echo htmlspecialchars(
                                $trabajo["categoria"]
                            );
                            ?>

                        </span>

                        <h3>

                            <?php
                            echo htmlspecialchars(
                                $trabajo["titulo"]
                            );
                            ?>

                        </h3>

                        <p>

                            <?php

                            if (!empty($trabajo["descripcion"])) {

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

                        <!-- ACCIONES DEL TRABAJO -->

                        <?php if (
                            isset($_SESSION["usuario_id"]) &&
                            isset($_SESSION["usuario_tipo"]) &&
                            $_SESSION["usuario_tipo"] === "tecnico" &&
                            (int)$_SESSION["usuario_id"] === $id_tecnico
                        ): ?>

                            <div class="acciones-trabajo">

                                <a
                                    href="EditarTrabajo.php?id=<?php echo (int)$trabajo["id"]; ?>"
                                    class="btn-editar-trabajo"
                                >

                                    <i class="fa-solid fa-pen"></i>

                                    Editar

                                </a>

                                <a
                                    href="EliminarTrabajo.php?id=<?php echo (int)$trabajo["id"]; ?>"
                                    class="btn-eliminar-trabajo"
                                    onclick="return confirm('¿Estás seguro de eliminar este trabajo?');"
                                >

                                    <i class="fa-solid fa-trash"></i>

                                    Eliminar

                                </a>

                            </div>

                        <?php endif; ?>

                    </div>

                </article>

            <?php endwhile; ?>

        <?php else: ?>

            <div
                style="
                    grid-column: 1 / -1;
                    text-align: center;
                    padding: 40px 20px;
                "
            >

                <i
                    class="fa-solid fa-briefcase"
                    style="
                        font-size: 45px;
                        margin-bottom: 15px;
                    "
                ></i>

                <h3>
                    Este técnico todavía no tiene trabajos publicados.
                </h3>

                <?php if (
                    isset($_SESSION["usuario_id"]) &&
                    isset($_SESSION["usuario_tipo"]) &&
                    $_SESSION["usuario_tipo"] === "tecnico" &&
                    (int)$_SESSION["usuario_id"] === $id_tecnico
                ): ?>

                    <p>
                        Puedes agregar tu primer trabajo usando el botón
                        "Agregar trabajo".
                    </p>

                <?php endif; ?>

            </div>

        <?php endif; ?>

    </div>

</section>

</main>

<footer>

    <div class="logo">

        Servi<span>Go</span>

    </div>

    <p>
        Conectando clientes con técnicos confiables.
    </p>

</footer>

</body>

</html>

<?php

$stmt_trabajos->close();

$conexion->close();

?>