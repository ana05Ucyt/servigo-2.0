<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

$id_tecnico = (int) $_SESSION["usuario_id"];


/* =========================
   COMPROBAR ID DEL TRABAJO
========================= */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: PerfilTecnico.php");
    exit;
}

$id_trabajo = (int) $_GET["id"];


/* =========================
   BUSCAR TRABAJO
========================= */

$sql = "SELECT *
        FROM trabajos
        WHERE id = ?
        AND id_tecnico = ?
        LIMIT 1";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_trabajo, $id_tecnico);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows !== 1) {
    die("Trabajo no encontrado o no tienes permiso para editarlo.");
}

$trabajo = $resultado->fetch_assoc();

$stmt->close();


/* =========================
   ACTUALIZAR TRABAJO
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titulo = trim($_POST["titulo"]);
    $categoria = trim($_POST["categoria"]);
    $descripcion = trim($_POST["descripcion"]);

    if ($titulo === "" || $categoria === "") {
        $error = "El título y la categoría son obligatorios.";
    } else {

        /* Mantener imagen anterior */
        $imagen = $trabajo["imagen"];

        /* Nueva imagen */
        if (
            isset($_FILES["imagen"]) &&
            $_FILES["imagen"]["error"] === UPLOAD_ERR_OK
        ) {

            $nombre_original = $_FILES["imagen"]["name"];

            $extension = strtolower(
                pathinfo($nombre_original, PATHINFO_EXTENSION)
            );

            $extensiones_permitidas = [
                "jpg",
                "jpeg",
                "png",
                "webp"
            ];

            if (!in_array($extension, $extensiones_permitidas)) {

                $error = "Formato de imagen no permitido.";

            } else {

                $nombre_archivo =
                    "trabajo_" .
                    $id_tecnico .
                    "_" .
                    time() .
                    "." .
                    $extension;

                $carpeta = "uploads/trabajos/";

                if (!is_dir($carpeta)) {
                    mkdir($carpeta, 0777, true);
                }

                $ruta = $carpeta . $nombre_archivo;

                if (move_uploaded_file(
                    $_FILES["imagen"]["tmp_name"],
                    $ruta
                )) {

                    $imagen = $ruta;
                }
            }
        }


        if (!isset($error)) {

            $sql_update = "UPDATE trabajos
                           SET titulo = ?,
                               categoria = ?,
                               descripcion = ?,
                               imagen = ?
                           WHERE id = ?
                           AND id_tecnico = ?";

            $stmt_update = $conexion->prepare($sql_update);

            $stmt_update->bind_param(
                "ssssii",
                $titulo,
                $categoria,
                $descripcion,
                $imagen,
                $id_trabajo,
                $id_tecnico
            );

            if ($stmt_update->execute()) {

                header(
                    "Location: PerfilTecnico.php?id=" .
                    $id_tecnico
                );

                exit;

            } else {

                $error = "No se pudo actualizar el trabajo.";
            }

            $stmt_update->close();
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

    <link rel="stylesheet" href="css/EditarTrabajo.css">

    <title>Editar trabajo | ServiGo</title>

</head>

<body>

<header>

    <div class="logo">
        Servi<span>Go</span>
        <i class="fa-solid fa-screwdriver-wrench"></i>
    </div>

    <nav>

        <a href="Principal.php">Inicio</a>
        <a href="Tecnicos.php">Técnicos</a>
        <a href="Trabajos.php">Trabajos</a>
        <a href="Nosotros.php">Nosotros</a>

    </nav>

    <div class="acciones">

        <a href="PerfilTecnico.php" class="usuario">
            <i class="fa-solid fa-user"></i>
            <?php echo htmlspecialchars($_SESSION["usuario_nombre"]); ?>
        </a>

    </div>

</header>


<main>

    <div class="formulario">

        <h1>
            Editar trabajo
        </h1>

        <p class="subtitulo">
            Modifica la información de tu trabajo publicado.
        </p>


        <?php if (isset($error)): ?>

            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>


        <form method="POST" enctype="multipart/form-data">


            <div class="campo">

                <label>
                    Título del trabajo
                </label>

                <input
                    type="text"
                    name="titulo"
                    value="<?php echo htmlspecialchars($trabajo["titulo"]); ?>"
                    required
                >

            </div>


            <div class="campo">

                <label>
                    Categoría
                </label>

                <input
                    type="text"
                    name="categoria"
                    value="<?php echo htmlspecialchars($trabajo["categoria"]); ?>"
                    required
                >

            </div>


            <div class="campo">

                <label>
                    Descripción
                </label>

                <textarea
                    name="descripcion"
                ><?php echo htmlspecialchars($trabajo["descripcion"]); ?></textarea>

            </div>


            <div class="campo">

                <label>
                    Imagen actual
                </label>

                <?php if (!empty($trabajo["imagen"])): ?>

                    <div class="imagen-actual">

                        <img
                            src="<?php echo htmlspecialchars($trabajo["imagen"]); ?>"
                            alt="Imagen actual"
                        >

                    </div>

                <?php endif; ?>


                <label>
                    Cambiar imagen
                </label>

                <input
                    type="file"
                    name="imagen"
                    accept=".jpg,.jpeg,.png,.webp"
                >

            </div>


            <div class="botones">

                <button
                    type="submit"
                    class="btn-guardar"
                >

                    <i class="fa-solid fa-floppy-disk"></i>
                    Guardar cambios

                </button>


                <a
                    href="PerfilTecnico.php?id=<?php echo $id_tecnico; ?>"
                    class="btn-cancelar"
                >

                    Cancelar

                </a>

            </div>


        </form>

    </div>

</main>

</body>

</html>

<?php

$conexion->close();

?>