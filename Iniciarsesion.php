<?php

session_start();

$conexion = new mysqli("localhost", "root", "", "Servigo");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = trim($_POST["correo"]);
    $password = $_POST["password"];

    $sql = "SELECT id, nombre, apellido, correo, numero, password, tipo
            FROM usuarios
            WHERE correo = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {

        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario["password"])) {

            // Crear sesión
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["usuario_nombre"] = $usuario["nombre"];
            $_SESSION["usuario_apellido"] = $usuario["apellido"];
            $_SESSION["usuario_correo"] = $usuario["correo"];
            $_SESSION["usuario_numero"] = $usuario["numero"];
            $_SESSION["usuario_tipo"] = $usuario["tipo"];

            // Redirigir según el tipo
            if ($usuario["tipo"] === "tecnico") {

                header("Location: PerfilTecnico.php");
                exit;

            } else {

                header("Location: Perfilcliente.php");
                exit;
            }

        } else {

            $error = "Correo o contraseña incorrectos.";
        }

    } else {

        $error = "Correo o contraseña incorrectos.";
    }

    $stmt->close();
}

$conexion->close();

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/Iniciarsesion.css">

    <title>Iniciar sesión</title>
</head>

<body>

    <div class="contenedor">

        <div class="presentacion">

            <div class="logo">
                Servi<span>Go</span>
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>

            <div class="icono-grande">
                <i class="fa-solid fa-user-lock"></i>
            </div>

            <h1>Bienvenido de nuevo</h1>

            <p>
                Inicia sesión para acceder a tu cuenta,
                consultar tus trabajos y disfrutar de
                nuestros servicios.
            </p>

            <ul class="ventajas">
                <li>✓ Gestiona tu perfil</li>
                <li>✓ Consulta tus trabajos</li>
                <li>✓ Encuentra técnicos confiables</li>
            </ul>

        </div>


        <div class="formulario">

            <h2>Iniciar sesión</h2>

            <p class="descripcion">
                Ingresa tus datos para continuar.
            </p>

            <form method="POST" action="">

                <div class="campo">

                    <label for="correo">
                        Correo electrónico
                    </label>

                    <div class="input-icono">

                        <i class="fa-solid fa-envelope"></i>

                        <input
                            type="email"
                            id="correo"
                            name="correo"
                            placeholder="correo@ejemplo.com"
                            required
                        >

                    </div>

                </div>


                <div class="campo">

                    <label for="password">
                        Contraseña
                    </label>

                    <div class="input-icono">

                        <i class="fa-solid fa-lock"></i>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                        >

                        <button
                            type="button"
                            class="mostrar-password"
                            id="mostrarPassword"
                            aria-label="Mostrar contraseña"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </button>

                    </div>

                </div>


                <div class="opciones">

                    <label class="recordar">

                        <input
                            type="checkbox"
                            name="recordar"
                        >

                        <span>Recordarme</span>

                    </label>

                    <a href="#">
                        ¿Olvidaste tu contraseña?
                    </a>

                </div>


                <button
                    type="submit"
                    class="boton"
                >
                    Iniciar sesión
                </button>

            </form>


            <div class="registro">

                ¿No tienes una cuenta?

                <a href="Registro.php">
                    Crear una cuenta
                </a>

            </div>

        </div>

    </div>


    <script>
        const mostrarPassword = document.getElementById("mostrarPassword");
        const password = document.getElementById("password");

        mostrarPassword.addEventListener("click", function () {

            if (password.type === "password") {

                password.type = "text";

                this.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';

            } else {

                password.type = "password";

                this.innerHTML = '<i class="fa-solid fa-eye"></i>';

            }

        });
    </script>

</body>
</html>