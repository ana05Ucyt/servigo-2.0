<?php

session_start();

if (
    !isset($_SESSION["usuario_id"]) ||
    !isset($_SESSION["usuario_tipo"]) ||
    $_SESSION["usuario_tipo"] !== "tecnico"
) {
    header("Location: Iniciarsesion.php");
    exit;
}

$conexion = new mysqli("localhost", "root", "", "Servigo");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$mensaje = "";
$error = "";

$titulo = "";
$categoria = "";
$descripcion = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_tecnico = (int) $_SESSION["usuario_id"];

    $titulo = trim($_POST["titulo"] ?? "");
    $categoria = trim($_POST["categoria"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");

    if ($titulo === "" || $categoria === "") {

        $error = "Debes completar el título y la categoría.";

    } else {

        $imagen = "";

        /*
        |--------------------------------------------------------------------------
        | SUBIR IMAGEN
        |--------------------------------------------------------------------------
        */

        if (
            isset($_FILES["imagen"]) &&
            $_FILES["imagen"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES["imagen"]["error"] !== UPLOAD_ERR_OK) {

                $error = "Ocurrió un error al seleccionar la imagen.";

            } else {

                $carpeta = "Img/Trabajos/";

                /*
                | La carpeta ya existe.
                */

                if (!is_dir($carpeta)) {

                    $error = "No existe la carpeta Img/trabajos.";

                } elseif (!is_writable($carpeta)) {

                    $error = "La carpeta Img/trabajos no tiene permisos de escritura.";

                } else {

                    $nombre_original = $_FILES["imagen"]["name"];

                    $extension = strtolower(
                        pathinfo(
                            $nombre_original,
                            PATHINFO_EXTENSION
                        )
                    );

                    $permitidas = [
                        "jpg",
                        "jpeg",
                        "png",
                        "webp"
                    ];

                    if (!in_array($extension, $permitidas)) {

                        $error = "Solo se permiten imágenes JPG, JPEG, PNG o WEBP.";

                    } elseif (
                        $_FILES["imagen"]["size"] > 5 * 1024 * 1024
                    ) {

                        $error = "La imagen no puede superar los 5 MB.";

                    } else {

                        $nombre_archivo =
                            "trabajo_" .
                            $id_tecnico .
                            "_" .
                            time() .
                            "_" .
                            uniqid() .
                            "." .
                            $extension;

                        $ruta = $carpeta . $nombre_archivo;

                        if (
                            move_uploaded_file(
                                $_FILES["imagen"]["tmp_name"],
                                $ruta
                            )
                        ) {

                            $imagen = $ruta;

                        } else {

                            $error = "No se pudo guardar la imagen en Img/Trabajos.";

                        }
                    }
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | GUARDAR TRABAJO
        |--------------------------------------------------------------------------
        */

        if ($error === "") {

            /*
            | Tu tabla usa:
            |
            | id
            | id_tecnico
            | titulo
            | descripcion
            | categoria
            | imagen
            | fecha
            |
            */

            $sql = "INSERT INTO trabajos
                    (
                        id_tecnico,
                        titulo,
                        descripcion,
                        categoria,
                        imagen,
                        fecha
                    )
                    VALUES (?, ?, ?, ?, ?, NOW())";

            $stmt = $conexion->prepare($sql);

            if ($stmt === false) {

                $error =
                    "Error en la consulta: " .
                    $conexion->error;

            } else {

                $stmt->bind_param(
                    "issss",
                    $id_tecnico,
                    $titulo,
                    $descripcion,
                    $categoria,
                    $imagen
                );

                if ($stmt->execute()) {

                    $mensaje = "Trabajo agregado correctamente.";

                    $titulo = "";
                    $categoria = "";
                    $descripcion = "";

                } else {

                    $error =
                        "No se pudo guardar el trabajo: " .
                        $stmt->error;
                }

                $stmt->close();
            }
        }
    }
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
        href="css/AgregarTrabajo.css"
    >

    <title>
        Agregar trabajo | ServiGo
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


        <a
            href="Cerrarsesion.php"
            class="cerrar"
        >

            <i class="fa-solid fa-right-from-bracket"></i>

            Cerrar sesión

        </a>

    </div>

</header>


<main class="contenedor">


    <div class="titulo">

        <h1>
            Agregar trabajo
        </h1>

        <p>
            Agrega un trabajo realizado para mostrarlo en tu perfil.
        </p>

    </div>


    <?php if ($mensaje !== ""): ?>

        <div class="mensaje">

            <i class="fa-solid fa-circle-check"></i>

            <?php
            echo htmlspecialchars($mensaje);
            ?>

        </div>

    <?php endif; ?>


    <?php if ($error !== ""): ?>

        <div class="error">

            <i class="fa-solid fa-circle-exclamation"></i>

            <?php
            echo htmlspecialchars($error);
            ?>

        </div>

    <?php endif; ?>


    <form
        method="POST"
        enctype="multipart/form-data"
        class="tarjeta"
    >


        <h2>

            <i class="fa-solid fa-briefcase"></i>

            Información del trabajo

        </h2>


        <div class="campo">

            <label for="titulo">
                Título del trabajo
            </label>

            <input
                type="text"
                id="titulo"
                name="titulo"
                placeholder="Ej. Instalación eléctrica"
                value="<?php echo htmlspecialchars($titulo); ?>"
                required
            >

        </div>


        <div class="campo">

            <label for="categoria">
                Categoría
            </label>

            <select
                id="categoria"
                name="categoria"
                required
            >

                <option value="">
                    Selecciona una categoría
                </option>

                <option value="Electricidad">
                    Electricidad
                </option>

                <option value="Plomería">
                    Plomería
                </option>

                <option value="Carpintería">
                    Carpintería
                </option>

                <option value="Reparación">
                    Reparación
                </option>

                <option value="Mantenimiento">
                    Mantenimiento
                </option>

                <option value="Construcción">
                    Construcción
                </option>

                <option value="Informática">
                    Informática
                </option>

                <option value="Refrigeración">
                    Refrigeración
                </option>

                <option value="Otro">
                    Otro
                </option>

            </select>

        </div>


        <div class="campo">

            <label for="descripcion">
                Descripción
            </label>

            <textarea
                id="descripcion"
                name="descripcion"
                rows="5"
                placeholder="Describe brevemente el trabajo realizado..."
            ><?php echo htmlspecialchars($descripcion); ?></textarea>

        </div>


        <div class="campo">

            <label for="imagen">
                Imagen del trabajo
            </label>

            <input
                type="file"
                id="imagen"
                name="imagen"
                accept=".jpg,.jpeg,.png,.webp"
            >

            <small>
                Formatos permitidos: JPG, JPEG, PNG y WEBP. Máximo 5 MB.
            </small>

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

                Guardar trabajo

            </button>

        </div>


    </form>


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

$conexion->close();

?>