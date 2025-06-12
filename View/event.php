<?php
include "../Controller/EventController.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineFest Catalunya // Detalles del Evento</title>
    <link rel="stylesheet" href="./files/style/navbar.css">
    <link rel="stylesheet" href="./files/style/event-details.css">
    <script src="https://kit.fontawesome.com/e1205d9581.js" crossorigin="anonymous"></script>
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
                    <a href="./profile.php"><button class="user-action" id="prof-redirect">Perfil</button></a>
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

    <div id="event-details-container">
        <?php
        if (isset($_GET['id'])) {
            $eventController = new EventController();
            $event = $eventController->getEventById($_GET["id"]);

            if (isset($event['title'])) {
                echo '
                <div class="event-header">
                <h1>' . htmlspecialchars($event['title']) . '</h1>
                <div class="event-meta">
                    <span class="event-genre">' . htmlspecialchars($event['genre']) . '</span>
                    <span class="event-date">' . htmlspecialchars($event['eventDate']) . '</span>
                </div>
                </div>
        
                <div class="event-content">
                <div class="event-media">
                    <div class="event-trailer">
                    <iframe width="560" height="315" src="' . $event["trailerVideo"] . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                </div>
          
                <div class="event-info">
                    <h2>Sinopsis</h2>
                    <p>' . nl2br(htmlspecialchars($event['synopsis'])) . '</p>
                    
                    <h2>Equipo</h2>
                    <p>' . nl2br(htmlspecialchars($event['crew'])) . '</p>
                    
                    <h2>Box Office</h2>
                    <p>' . nl2br(htmlspecialchars($event['boxOffice'])) . '</p>
                               
                    <h2>Estado</h2>
                    <p>' . nl2br(htmlspecialchars($event['estado'])) . '</p>
                           
                    <button class="buy-ticket">Comprar entrada</button>
                </div>
                </div>';
                unset($_SESSION['eventDate']);
            } else {
                echo '<p class="error-message">Evento no encontrado.</p>';
            }
        } else {
            echo '<p class="error-message">No se ha especificado un evento</p>';
        }
        ?>
    </div>
</body>

</html>