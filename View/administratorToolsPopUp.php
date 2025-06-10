<?php
include "../Controller/UserController.php";
include "../Controller/EventController.php";
$redirect = $_SERVER["REQUEST_URI"];

$events = [];
$resultsCount = 0;
$eventController = new EventController();

$events = $eventController->readAll();

$resultsCount = count($events);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Eventos</title>
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

    <!-- Popup Overlay -->
    <button onclick="openPopup()">➕ Crear Evento</button>

    <div class="popup-overlay" id="popupOverlay">
        <div class="popup">
            <button type="button" class="close-btn" onclick="closePopup()">&times;</button>

            <div class="popup-header">
                <h2>🎯 Crear Nuevo Evento</h2>
            </div>
            <form action="../Controller/EventController.php" method="GET">
                <!-- Campo oculto para indicar la acción -->
                <input type="hidden" name="create" value="1">

                <div class="message" id="message"></div>

                <!-- FORMULARIO SIMPLE - Se envía directamente al controlador -->
                <form action="../Controller/EventController.php" method="POST">

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
                        <label for="eventDate">📅 Fecha del Evento</label>
                        <input type="date" id="eventDate" name="eventDate" required>
                    </div>

                    <div class="form-group">
                        <label for="trailerVideo">🎬 Video</label>
                        <input type="url" id="trailerVideo" name="trailerVideo" placeholder="https://youtube.com/watch?v=...">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">✨ Crear Evento</button>
                        <button><a type="submit" href="./events.php" class="btn btn-secondary" onclick="closePopup(); return false;">❌ Cancelar</a></button>
                    </div>
                </form>
        </div>
    </div>

    <?php foreach ($events as $event): ?>
        <div class="event">
            <h3><?= htmlspecialchars($event['title']) ?></h3>
            <p><?= htmlspecialchars($event['genre']) ?> - <?= htmlspecialchars($event['eventDate']) ?></p>
        </div>
    <?php endforeach; ?>

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

</html>