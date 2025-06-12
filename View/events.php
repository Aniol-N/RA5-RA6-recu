<?php
include "../Controller/UserController.php";
include "../Controller/EventController.php";
$redirect = $_SERVER["REQUEST_URI"];

$events = [];
$resultsCount = 0;
$eventController = new EventController();

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["read-filters"])) {
	$events = $eventController->read_filters();
} else {
	$events = $eventController->readAll();
}

$resultsCount = count($events);
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CineFest Catalunya // Eventos</title>
	<link rel="stylesheet" href="./files/style/navbar.css">
	<link rel="stylesheet" href="./files/style/events.css">

	<script src="https://kit.fontawesome.com/e1205d9581.js" crossorigin="anonymous"></script>
	<style>
		/* Oculta el popup por defecto */
		#popupOverlay {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			width: 100vw;
			height: 100vh;
			background-color: rgba(0, 0, 0, 0.5);
			justify-content: center;
			align-items: center;
			z-index: 9999;
		}

		/* Muestra el popup cuando tiene la clase active */
		#popupOverlay.active {
			display: flex;
		}

		/* Estilo del cuadro del popup */
		.popup {
			background: #fff;
			padding: 2rem;
			border-radius: 10px;
			width: 500px;
			max-width: 90%;
			box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
			position: relative;
		}

		/* Botón de cierre (la X) */
		.close-btn {
			position: absolute;
			top: 10px;
			right: 15px;
			font-size: 24px;
			background: none;
			border: none;
			cursor: pointer;
		}
	</style>

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
				<a href="#" id="events">Eventos</a>
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

	<!-- Contenido de eventos -->
	<div id="events-container">
		<div id="sidebar">
			<h2>Preferencias</h2>
			<form id="filters-form" action="./events.php" method="GET">
				<div class="filter-group">
					<label for="genero">Género</label>
					<select id="genero" name="genre" class="inputbox">
						<option value="">Todos los géneros</option>
						<option value="drama">Drama</option>
						<option value="comedia">Comedia</option>
						<option value="accion">Acción</option>
						<option value="ciencia ficcion">Ciencia Ficción</option>
						<option value="terror">Terror</option>
					</select>
				</div>
				<div class="filter-group">
					<label for="fecha">Fechas</label>
					<input type="date" id="fecha" name="date" class="inputbox">
				</div>

				<button type="submit" name="read-filters" id="filter-btn">Aplicar Filtros</button>
			</form>
			<div id="adminUtils">
				<button type="button" id="create" onclick="openPopup()">Add event</button>

				<form id="updateEventForm" action="../Controller/EventController.php" method="POST" style="margin-top: 10px;">
					<input type="hidden" name="updateEvent" value="1">
					<!-- Agregar campo para eventId si es necesario -->
					<button type="submit" id="update">Update event</button>
				</form>

				<form id="deleteEventForm" action="../Controller/EventController.php" method="POST" style="margin-top: 10px;">
					<input type="hidden" name="deleteEvent" value="1">
					<!-- Agregar campo para eventId si es necesario -->
					<button type="submit" id="delete">Delete event</button>
				</form>
			</div>
		</div>

		<div id="content">
			<div id="events-header">
				<h1>EVENTOS</h1>
				<div id="results-count">Mostrando <?php echo $resultsCount; ?> resultados</div>
			</div>

			<div id="events-grid">
				<?php if (!empty($events)): ?>
					<?php foreach ($events as $event): ?>
						<div class="event-card">
							<div class="event-poster">
								<?php if (!empty($event['poster_image'])): ?>
									<img src="<?php echo htmlspecialchars($event['poster_image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
								<?php else: ?>
									<div style="background-color: #ddd; height: 300px; display: flex; align-items: center; justify-content: center;">
										Sin imagen
									</div>
								<?php endif; ?>
							</div>
							<div class="event-info">
								<h3><?php echo htmlspecialchars($event['title']); ?></h3>
								<p class="event-genre">Género: <?php echo htmlspecialchars($event['genre']); ?></p>
								<p class="event-date">Fecha: <?php echo htmlspecialchars($event['eventDate']); ?></p>
								<?php if (!empty($event['synopsis'])): ?>
									<p class="event-synopsis"><?php echo htmlspecialchars(substr($event['synopsis'], 0, 100)); ?></p>
								<?php endif; ?>
								<a class="more-info" href="./event.php?id=<?php echo $event['id']; ?>">Más información</a>
							</div>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="no-events">
						<p>No se encontraron eventos que coincidan con los filtros seleccionados.</p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- Popup Crear Evento -->
	<div class="popup-overlay" id="popupOverlay">
		<div class="popup">
			<button type="button" class="close-btn" onclick="closePopup()">&times;</button>
			<div class="popup-header">
				<h2>🎯 Crear Nuevo Evento</h2>
			</div>

			<form action="../Controller/EventController.php" method="POST">
				<input type="hidden" name="create" value="1">

				<div class="form-group">
					<label for="title">📝 Título del Evento *</label>
					<input type="text" id="title" name="title" required placeholder="Ej: Titanic">
				</div>

				<div class="form-group">
					<label for="genre">🎭 Género</label>
					<input type="text" id="genre" name="genre" required placeholder="Ej: Drama">
				</div>

				<div class="form-group">
					<label for="synopsis">📄 Synopsis</label>
					<textarea id="synopsis" name="synopsis" placeholder="Descripción evento/synopsis..."></textarea>
				</div>

				<div class="form-group">
					<label for="crew">👥 Crew</label>
					<input type="text" id="crew" name="crew" required placeholder="Ej: Soren Madsen">
				</div>

				<div class="form-group">
					<label for="boxOffice">🧾 Box Office</label>
					<input type="number" id="boxOffice" name="boxOffice" required placeholder="Only numbers">
				</div>

				<div class="form-group">
					<label for="eventDate">📅 Fecha del Evento</label>
					<input type="date" id="eventDate" name="eventDate" required>
				</div>

				<div class="form-group">
					<label for="trailerVideo">🎬 Video</label>
					<input type="url" id="trailerVideo" name="trailerVideo" placeholder="https://youtube.com/watch?v=...">
				</div>

				<div class="form-group">
					<label for="trailerVideo">Estado</label>
					<input type="text" id="estado" name="estado">
				</div>

				<div class="form-actions">
					<button type="submit" class="btn btn-primary">✨ Crear Evento</button>
					<button type="button" class="btn btn-secondary" onclick="closePopup()">❌ Cancelar</button>
				</div>
			</form>
		</div>
	</div>

	<script>
		// JavaScript mínimo solo para abrir/cerrar popup
		function openPopup() {
			document.getElementById('popupOverlay').classList.add('active');
			document.body.style.overflow = 'hidden';
		}

		function closePopup() {
			document.getElementById('popupOverlay').classList.remove('active');
			document.body.style.overflow = 'auto';
		}

		// Cerrar popup al hacer clic fuera
		document.getElementById('popupOverlay').addEventListener('click', function(e) {
			if (e.target === this) {
				closePopup();
			}
		});

		// Cerrar popup con ESC
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape') {
				closePopup();
			}
		});
	</script>
</body>

<script src="https://kit.fontawesome.com/e1205d9581.js" crossorigin="anonymous"></script>

</html>