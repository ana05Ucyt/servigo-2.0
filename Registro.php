<?php

$conexion = new mysqli("localhost", "root", "", "Servigo");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$mensaje = "";
$tipoMensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $correo = trim($_POST["correo"]);
    $numero = trim($_POST["numero"]);
    $tipo = $_POST["tipo"];

    $password = $_POST["password"];
    $confirmarPassword = $_POST["confirmar_password"];

    $foto = $_FILES["foto"]["name"] ?? "";
    $especialidad = $_POST["especialidad"] ?? "";
    $experiencia = $_POST["experiencia"] ?? "";
    $departamento = $_POST["departamento"] ?? "";
    $municipio = trim($_POST["municipio"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");


    if ($password !== $confirmarPassword) {

        $mensaje = "Las contraseñas no coinciden.";
        $tipoMensaje = "error";

    } else {

        // Comprobar si el correo ya existe
        $sql = "SELECT id FROM usuarios WHERE correo = ?";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $correo);
        $stmt->execute();

        $resultado = $stmt->get_result();


        if ($resultado->num_rows > 0) {

            $mensaje = "Este correo ya está registrado.";
            $tipoMensaje = "error";

        } else {

            // Encriptar contraseña
            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            // Insertar usuario
            $sql = "INSERT INTO usuarios
                    (nombre, apellido, correo, numero, password, tipo)
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $conexion->prepare($sql);

            $stmt->bind_param(
                "ssssss",
                $nombre,
                $apellido,
                $correo,
                $numero,
                $passwordHash,
                $tipo
            );


            if ($stmt->execute()) {

                header("Location: Iniciarsesion.php");
                exit;

            } else {

                $mensaje = "No se pudo crear la cuenta.";
                $tipoMensaje = "error";
            }
        }

        $stmt->close();
    }
}

$conexion->close();

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="stylesheet" href="css/Registro.css">

    <title>Crear cuenta</title>

</head>

<body>

<div class="contenedor">

    <div class="presentacion">

        <div class="logo">
            Servi<span>Go</span>
            <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>

        <div class="icono-grande">
            👨‍🔧
        </div>

        <h1>
            Únete a nuestra comunidad
        </h1>

        <p>
            Encuentra Tecnicos confiables o
            muestra tus servicios y trabajos a nuevos
            clientes.
        </p>

        <ul class="ventajas">

            <li>
                ✓ Registro de trabajos
            </li>

            <li>
                ✓ Valoraciones de clientes
            </li>

            <li>
                ✓ Técnicos verificados
            </li>

        </ul>

    </div>


    <div class="formulario">

        <h2>
            Crear una cuenta
        </h2>

        <p class="descripcion">
            Completa tus datos para comenzar.
        </p>


        <form method="POST"
              action=""
              enctype="multipart/form-data">


            <div class="tipos">

                <div class="tipo">

                    <input
                        type="radio"
                        name="tipo"
                        id="cliente"
                        value="cliente"
                        checked
                    >

                    <label for="cliente">

                        <span class="tipo-icono">
                            <img src="Img/Cliente.png" alt="">
                        </span>

                        <span class="tipo-nombre">
                            Cliente
                        </span>

                    </label>

                </div>


                <div class="tipo">

                    <input
                        type="radio"
                        name="tipo"
                        id="tecnico"
                        value="tecnico"
                    >

                    <label for="tecnico">

                        <span class="tipo-icono">
                            <img src="Img/Tecnico.png" alt="">
                        </span>

                        <span class="tipo-nombre">
                            Técnico
                        </span>

                    </label>

                </div>

            </div>


            <?php if ($mensaje != ""): ?>

                <div class="<?php echo $tipoMensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>

            <?php endif; ?>


            <div class="fila">

                <div class="campo">

                    <label>
                        Nombre
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        placeholder="Ej. Carlos"
                        required
                    >

                </div>


                <div class="campo">

                    <label>
                        Apellido
                    </label>

                    <input
                        type="text"
                        name="apellido"
                        placeholder="Ej. Martínez"
                        required
                    >

                </div>

            </div>


            <div class="campo">

                <label>
                    Correo electrónico
                </label>

                <input
                    type="email"
                    name="correo"
                    placeholder="correo@ejemplo.com"
                    required
                >

            </div>


            <div class="campo">

                <label>
                    Número de teléfono
                </label>

                <input
                    type="tel"
                    name="numero"
                    placeholder="8888-8888"
                    required
                >

            </div>


            <div
                class="datos-tecnico"
                id="datosTecnico"
            >

                <div class="nota">

                    Como técnico podrás publicar tus trabajos,
                    indicar tu especialidad y recibir valoraciones
                    de los clientes.

                </div>


                <div class="campo">

                    <label>
                        Foto de perfil
                    </label>

                    <input
                        type="file"
                        name="foto"
                        accept="image/png, image/jpeg, image/webp"
                    >

                </div>


                <div class="campo">

                    <label>
                        Especialidad
                    </label>

                    <select name="especialidad">

                        <option value="">
                            Selecciona tu especialidad
                        </option>

                        <option value="Electricidad">
                            Electricidad
                        </option>

                        <option value="Informática">
                            Informática
                        </option>

                        <option value="Refrigeración">
                            Refrigeración
                        </option>

                        <option value="Plomería">
                            Plomería
                        </option>

                        <option value="Reparaciones">
                            Reparaciones
                        </option>

                        <option value="Mantenimiento">
                            Mantenimiento
                        </option>

                    </select>

                </div>


                <div class="campo">

                    <label>
                        Años de experiencia
                    </label>

                    <select name="experiencia">

                        <option value="">
                            Selecciona tus años de experiencia
                        </option>

                        <option value="Menos de 1 año">
                            Menos de 1 año
                        </option>

                        <option value="1 - 3 años">
                            1 - 3 años
                        </option>

                        <option value="4 - 6 años">
                            4 - 6 años
                        </option>

                        <option value="7 - 10 años">
                            7 - 10 años
                        </option>

                        <option value="Más de 10 años">
                            Más de 10 años
                        </option>

                    </select>

                </div>


                <div class="campo">

                    <label>
                        Ubicación
                    </label>

                    <select name="departamento">

                        <option value="">
                            Selecciona tu departamento
                        </option>

                        <option value="Managua">
                            Managua
                        </option>

                        <option value="Masaya">
                            Masaya
                        </option>

                        <option value="Granada">
                            Granada
                        </option>

                        <option value="León">
                            León
                        </option>

                        <option value="Estelí">
                            Estelí
                        </option>

                        <option value="Matagalpa">
                            Matagalpa
                        </option>

                        <option value="Chinandega">
                            Chinandega
                        </option>

                        <option value="Rivas">
                            Rivas
                        </option>

                    </select>

                </div>


                <div class="campo">

                    <label>
                        Municipio
                    </label>

                    <input
                        type="text"
                        name="municipio"
                        placeholder="Ej. Managua"
                    >

                </div>


                <div class="campo">

                    <label>
                        Descripción de tus servicios
                    </label>

                    <textarea
                        name="descripcion"
                        placeholder="Cuéntales a los clientes sobre tu experiencia y los servicios que realizas..."
                    ></textarea>

                </div>

            </div>


            <div class="fila">

                <div class="campo">

                    <label>
                        Contraseña
                    </label>

                    <input
                        type="password"
                        name="password"
                        placeholder="••••••••"
                        required
                    >

                </div>


                <div class="campo">

                    <label>
                        Confirmar contraseña
                    </label>

                    <input
                        type="password"
                        name="confirmar_password"
                        placeholder="••••••••"
                        required
                    >

                </div>

            </div>


            <button
                type="submit"
                class="boton"
            >
                Crear mi cuenta
            </button>

        </form>


        <div class="login">

            ¿Ya tienes una cuenta?

            <a href="Iniciarsesion.php">
                Iniciar sesión
            </a>

        </div>

    </div>

</div>

</body>

</html>