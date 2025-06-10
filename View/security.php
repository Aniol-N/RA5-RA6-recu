<?php
include "../Controller/UserController.php";
$redirect = $_SERVER["REQUEST_URI"]; 
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineFest Catalunya // Perfil</title>
    <link rel="stylesheet" href="./files/style/profile.css">
    <link rel="stylesheet" href="./files/style/navbar.css">
</head>

<body>
    <header>
        <ul id="navbar">
            <h1 id="logo">CFC</h1>
            <input type="checkbox" id="check">
            <label for="check" class="menubtn">
                <i class="fas fa-bars"></i>
            </label>
            <div id="nav-left">
                <a href="../index.php" id="home">Home</a>
                <a href="./events.php" id="events">Eventos</a>
                <a href="./calendar.php" id="calendar">Calendario</a>
                <a href="#" id="news">Noticias</a>
                <a href="#" id="forums">Foros</a>
            </div>
            <input type="checkbox" id="showprofile">
            <label for="showprofile" id="profilebtn" class="navbar-right">
                <i class="fa-solid fa-user" style="font-size: 24px;"></i>
            </label>
            <div id="search-container">
                <input type="text" placeholder="Search...">
                <button type="submit"><i class="fa fa-search" style="color:white"></i></button>
            </div>
            <div id="user-info">
                <h1 id="profile">Perfil</h1>
                <?php if (isset($_SESSION["email"])) {
                    echo '
                    <h3 id="usr-email">' . $_SESSION['email'] . '</h3>
                    <img src="./files/img/usr_test.png" id="user-pfp">
                    <h1 id="usr-name">Bienvenido, ' . $_SESSION['username'] . '!</h1>
                    <a href="#"><button class="user-action" id="prof-redirect">Perfil</button></a>
                    <a href="./update_password.php"><button class="user-action" id="passwd-redirect">Cambiar Contraseña</button></a>
                    <!--placeholders-->
                    <a href="#"><button class="user-action" id="useraction2">Lorem ipsum</button></a>
                    <!--placeholders-->
                    <a href="../Controller/logout.php"><button class="user-action" id="logout">Cerrar sesión</button></a>';
                } else {
                    echo '<h1 id="not-logged">No has iniciado sesión</h1>
                    <a href="./login.php"><button class="user-action" id="login">Login</button></a>';
                } ?>
            </div>
        </ul>
    </header>
    <div id="container">
        <?php if (isset($_SESSION["email"])) {
            echo '<div id="profile-container">
            <div id="sidebar">
                <h2>Configuración</h2>
                <ul>
                    <li><a href="./profile.php">Datos Personales</a></li>
                    <li><a href="./update_password.php">Cambiar Contraseña</a></li>
                    <li><a href="#">Notificaciones</a></li>
                    <li><a href="#" class="active">Seguridad</a></li>
                    <li><a href="../Controller/logout.php">Cerrar sesión</a></li>
                </ul>
            </div>

            <div id="content">
                <div id="welcome-section">
                    <h1>Bienvenido, ' . $_SESSION["username"] . '</h1>
                </div>

                <div class="profile-section">
                    <h2>Seguridad - Eliminar Perfil</h2>
                    <h4>Especifíca estos datos para confirmar que de verdad quieres borrar tu perfil.</h4><br>
                    ';
                    if (isset($_SESSION["error"])) {
                        echo '<div id="error">'.$_SESSION["error"].'</div>';
                        unset($_SESSION["error"]);
                    }
                    if (isset($_SESSION["success"])) {
                        echo '<div id="success">'.$_SESSION["success"].'</div>';
                        unset($_SESSION["success"]);
                    }
                    echo '
                    <form id="profile-form" method="GET" action="../Controller/UserController.php">
                        <div class="form-group">
                            <label for="nombre">E-Mail:</label>
                            <input type="email" id="nombre" class="inputbox" name="email">
                        </div>

                        <div class="form-group">
                            <label for="password">Contraseña:</label>
                            <input type="password" id="passwd" class="inputbox" name="passwd">
                        </div>

                        <div class="form-group">
                            <label for="confirmation">Como una última confirmación, especifica lo siguiente: "QUIERO ELIMINAR MI PERFIL '.$_SESSION["username"].'"</label>
                            <input type="text" id="confirmation" class="inputbox" name="confirmation">
                        </div>

                        <button type="submit" id="del-btn" name="delete">ELIMINAR PERFIL</button>
                    </form>
                </div>
            </div>
        </div>';
        } else {
            header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        } ?>
    </div>
</body>
<script src="https://kit.fontawesome.com/e1205d9581.js" crossorigin="anonymous"></script>

</html>