<?php

session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: Iniciarsesion.php");
    exit;
}

if ($_SESSION["usuario_tipo"] !== "tecnico") {
    header("Location: Principal.php");
    exit;
}


/* ==========================================
   CONEXIÓN
========================================== */

$conexion = new mysqli(
    "localhost",
    "root",
    "",
    "Servigo"
);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");

$id = $_SESSION["usuario_id"];

$mensaje = "";
$error = "";


/* ==========================================
   GUARDAR CAMBIOS
========================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $especialidad = trim($_POST["especialidad"] ?? "");
    $experiencia = trim($_POST["experiencia"] ?? "");
    $departamento = trim($_POST["departamento"] ?? "");
    $municipio = trim($_POST["municipio"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");

    $foto = null;


    /* ======================================
       SUBIR FOTO
    ====================================== */

    if (
        isset($_FILES["foto"]) &&
        $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES["foto"]["error"] !== UPLOAD_ERR_OK) {

            $error = "Error al subir la imagen.";

        } else {

            $extension = strtolower(
                pathinfo(
                    $_FILES["foto"]["name"],
                    PATHINFO_EXTENSION
                )
            );

            $extensionesPermitidas = [
                "jpg",
                "jpeg",
                "png",
                "webp"
            ];


            if (!in_array(
                $extension,
                $extensionesPermitidas,
                true
            )) {

                $error =
                    "Solo se permiten imágenes JPG, JPEG, PNG o WEBP.";

            } else {


                /* ==================================
                   CARPETA QUE YA CREASTE
                ================================== */

                $carpeta = __DIR__ .
                           DIRECTORY_SEPARATOR .
                           "Img" .
                           DIRECTORY_SEPARATOR .
                           "Perfiles";


                /* ==================================
                   COMPROBAR QUE EXISTE
                ================================== */

                if (!is_dir($carpeta)) {

                    $error =
                        "No existe la carpeta Img/Perfiles.";

                }


                /*COMPROBAR PERMISOS*/

                elseif (!is_writable($carpeta)) {

                    $error =
                        "La carpeta Img/Perfiles no tiene permisos de escritura.";

                }


                /*GUARDAR FOTO*/

                else {

                    $nombreArchivo =
                        "usuario_" .
                        $id .
                        "_" .
                        time() .
                        "." .
                        $extension;


                    $rutaFisica =
                        $carpeta .
                        DIRECTORY_SEPARATOR .
                        $nombreArchivo;


                    $rutaBD =
                        "Img/Perfiles/" .
                        $nombreArchivo;


                    if (
                        move_uploaded_file(
                            $_FILES["foto"]["tmp_name"],
                            $rutaFisica
                        )
                    ) {

                        $foto = $rutaBD;

                    } else {

                        $error =
                            "No se pudo guardar la imagen. Revisa los permisos de la carpeta Img/Perfiles.";

                    }

                }

            }

        }

    }


    /* ACTUALIZAR BASE DE DATOS*/

    if ($error === "") {


        if ($foto !== null) {

            $sql = "UPDATE usuarios SET
                        foto = ?,
                        especialidad = ?,
                        experiencia = ?,
                        departamento = ?,
                        municipio = ?,
                        descripcion = ?
                    WHERE id = ?";


            $stmt = $conexion->prepare($sql);


            if (!$stmt) {

                $error =
                    "Error al preparar la consulta.";

            } else {

                $stmt->bind_param(
                    "ssssssi",
                    $foto,
                    $especialidad,
                    $experiencia,
                    $departamento,
                    $municipio,
                    $descripcion,
                    $id
                );

            }


        } else {

            $sql = "UPDATE usuarios SET
                        especialidad = ?,
                        experiencia = ?,
                        departamento = ?,
                        municipio = ?,
                        descripcion = ?
                    WHERE id = ?";


            $stmt = $conexion->prepare($sql);


            if (!$stmt) {

                $error =
                    "Error al preparar la consulta.";

            } else {

                $stmt->bind_param(
                    "sssssi",
                    $especialidad,
                    $experiencia,
                    $departamento,
                    $municipio,
                    $descripcion,
                    $id
                );

            }

        }


        if ($error === "") {

            if ($stmt->execute()) {

                $mensaje =
                    "Perfil actualizado correctamente.";

            } else {

                $error =
                    "No se pudieron guardar los cambios: " .
                    $stmt->error;

            }

            $stmt->close();

        }

    }

}


/* ==========================================
   OBTENER DATOS
========================================== */

$sql = "SELECT
            nombre,
            apellido,
            correo,
            numero,
            foto,
            especialidad,
            experiencia,
            departamento,
            municipio,
            descripcion
        FROM usuarios
        WHERE id = ?";


$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$resultado = $stmt->get_result();

$usuario = $resultado->fetch_assoc();

$stmt->close();

$conexion->close();

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
        href="css/EditarTecnico.css"
    >

    <title>Editar mi perfil</title>

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

        <a href="PerfilTecnico.php">

            <i class="fa-solid fa-user"></i>

            <?= htmlspecialchars(
                $usuario["nombre"] ?? ""
            ) ?>

        </a>


        <a
            href="Cerrarsesion.php"
            class="cerrar"
        >

            Cerrar sesión

        </a>

    </div>


</header>



<main>


<section class="contenedor">


    <div class="titulo">

        <h1>
            Editar mi perfil
        </h1>

        <p>
            Completa tu información para que los clientes
            puedan conocerte mejor.
        </p>

    </div>



    <?php if ($mensaje !== ""): ?>

        <div class="mensaje">

            <i class="fa-solid fa-circle-check"></i>

            <?= htmlspecialchars($mensaje) ?>

        </div>

    <?php endif; ?>



    <?php if ($error !== ""): ?>

        <div class="error">

            <i class="fa-solid fa-circle-exclamation"></i>

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>



    <form
        method="POST"
        enctype="multipart/form-data"
    >


        <div class="tarjeta">


            <h2>

                <i class="fa-solid fa-user"></i>

                Información personal

            </h2>


            <div class="informacion-basica">


                <div class="foto">


                    <?php

                    $fotoUsuario =
                        $usuario["foto"] ?? "";

                    $rutaFoto =
                        __DIR__ .
                        DIRECTORY_SEPARATOR .
                        str_replace(
                            "/",
                            DIRECTORY_SEPARATOR,
                            $fotoUsuario
                        );

                    ?>


                    <?php if (
                        !empty($fotoUsuario) &&
                        file_exists($rutaFoto)
                    ): ?>

                        <img
                            src="<?= htmlspecialchars($fotoUsuario) ?>"
                            alt="Foto de perfil"
                        >

                    <?php else: ?>

                        <img
                            src="Img/Tecnico.png"
                            alt="Foto de perfil"
                        >

                    <?php endif; ?>


                </div>


                <div class="datos">


                    <h3>

                        <?= htmlspecialchars(
                            ($usuario["nombre"] ?? "") .
                            " " .
                            ($usuario["apellido"] ?? "")
                        ) ?>

                    </h3>


                    <p>

                        <i class="fa-solid fa-envelope"></i>

                        <?= htmlspecialchars(
                            $usuario["correo"] ?? ""
                        ) ?>

                    </p>


                    <p>

                        <i class="fa-solid fa-phone"></i>

                        <?= htmlspecialchars(
                            $usuario["numero"] ?? ""
                        ) ?>

                    </p>


                </div>


            </div>


            <div class="campo">

                <label>
                    Foto de perfil
                </label>

                <input
                    type="file"
                    name="foto"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <small>
                    Formatos permitidos: JPG, JPEG, PNG y WEBP.
                </small>

            </div>


        </div>



        <div class="tarjeta">


            <h2>

                <i class="fa-solid fa-briefcase"></i>

                Información profesional

            </h2>


            <div class="campo">

                <label>
                    Especialidad
                </label>


                <select name="especialidad">

                    <option value="">
                        Selecciona tu especialidad
                    </option>


                    <?php

                    $especialidades = [
                        "Electricidad",
                        "Informática",
                        "Refrigeración",
                        "Plomería",
                        "Reparaciones",
                        "Mantenimiento",
                        "Mecánica",
                        "Pintura",
                        "Limpieza",
                        "Jardinería",
                        "Cerrajería"
                    ];


                    foreach (
                        $especialidades
                        as $especialidad
                    ):

                    ?>

                        <option
                            value="<?= htmlspecialchars($especialidad) ?>"
                            <?= ($usuario["especialidad"] ?? "") === $especialidad
                                ? "selected"
                                : "" ?>
                        >

                            <?= htmlspecialchars(
                                $especialidad
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="campo">

                <label>
                    Años de experiencia
                </label>


                <select name="experiencia">

                    <option value="">
                        Selecciona tu experiencia
                    </option>


                    <?php

                    $experiencias = [
                        "Menos de 1 año",
                        "1 - 3 años",
                        "4 - 6 años",
                        "7 - 10 años",
                        "Más de 10 años"
                    ];


                    foreach (
                        $experiencias
                        as $experiencia
                    ):

                    ?>

                        <option
                            value="<?= htmlspecialchars($experiencia) ?>"
                            <?= ($usuario["experiencia"] ?? "") === $experiencia
                                ? "selected"
                                : "" ?>
                        >

                            <?= htmlspecialchars(
                                $experiencia
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="fila">


                <div class="campo">

                    <label>
                        Departamento
                    </label>


                    <select name="departamento">

                        <option value="">
                            Selecciona tu departamento
                        </option>


                        <?php

                        $departamentos = [
                            "Managua",
                            "Masaya",
                            "Granada",
                            "León",
                            "Estelí",
                            "Matagalpa",
                            "Chinandega",
                            "Rivas"
                        ];


                        foreach (
                            $departamentos
                            as $departamento
                        ):

                        ?>

                            <option
                                value="<?= htmlspecialchars($departamento) ?>"
                                <?= ($usuario["departamento"] ?? "") === $departamento
                                    ? "selected"
                                    : "" ?>
                            >

                                <?= htmlspecialchars(
                                    $departamento
                                ) ?>

                            </option>

                        <?php endforeach; ?>


                    </select>

                </div>


                <div class="campo">

                    <label>
                        Municipio
                    </label>


                    <input
                        type="text"
                        name="municipio"
                        value="<?= htmlspecialchars(
                            $usuario["municipio"] ?? ""
                        ) ?>"
                        placeholder="Ej. Managua"
                    >

                </div>


            </div>


            <div class="campo">

                <label>
                    Sobre mí
                </label>


                <textarea
                    name="descripcion"
                    rows="6"
                    placeholder="Cuéntales a los clientes sobre tu experiencia y los servicios que realizas..."
                ><?= htmlspecialchars(
                    $usuario["descripcion"] ?? ""
                ) ?></textarea>

            </div>


        </div>



        <div class="botones">


            <a
                href="PerfilTecnico.php"
                class="cancelar"
            >
                Cancelar
            </a>


            <button
                type="submit"
                class="guardar"
            >

                <i class="fa-solid fa-floppy-disk"></i>

                Guardar cambios

            </button>


        </div>


    </form>


</section>


</main>


</body>

</html>