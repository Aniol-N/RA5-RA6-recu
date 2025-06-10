<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineFest Catalunya // Registro</title>
    <link rel="stylesheet" href="files/style/sign_in.css">
</head>

<body>
    <div id="RegisterCentred">
        <div id="T_Registro">
            <h1>CREA TU CUENTA</h1>
        </div>
        <div id="T_Instrucciones">
            <h2>Completa todos los campos para registrarte en CFC.</h2>
        </div>
        <div id="rectangle">
            <?php
            session_start();
            if (isset($_SESSION['error'])) {
                echo '<div id="alert">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']); // Eliminar el mensaje después de mostrarlo
            }
            ?>
            <form action="../Controller/UserController.php" method="POST">
                <!-- NOMBRE DE USUARIO -->
                <label for="usuario">
                    <h2>Nombre de usuario:</h2>
                </label>
                <div class="TextBox">
                    <input class="inputbox" type="text" name="usuario" required>
                </div>

                <!-- EMAIL -->
                <label for="email">
                    <h2>Correo electrónico:</h2>
                </label>
                <div class="TextBox">
                    <input class="inputbox" type="email" name="email" required>
                </div>

                <!-- CONTRASEÑA -->
                <label for="password">
                    <h2>Contraseña:</h2>
                </label>
                <div class="TextBox">
                    <input class="inputbox" type="password" name="password" required>
                </div>

                <!-- BOTÓN REGISTRO -->
                <input type="submit" id="register" name="register" value="Registrarse">

                <!-- ENLACE A LOGIN -->
                <h3>
                    ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
                </h3>
            </form>
        </div>
    </div>
</body>

</html>