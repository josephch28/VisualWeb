<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Universidad Técnica de Ambato</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/estilo.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <?php
    require_once "Controllers/controller.php";
    ?>
</head>
<body>
    <header>
        <img src="img/Banner.jpg" alt="Universidad Técnica de Ambato">
    </header>

    <nav class="primary-menu">
        <ul>
            <li><a href="?accion=Inicio">Inicio</a></li>
            <li><a href="?accion=Nosotros">Nosotros</a></li>
            <li><a href="?accion=Servicios">Servicios</a></li>
            <li><a href="?accion=Contacto">Contacto</a></li>
        </ul>
    </nav>

    <article>
        <?php
        $mvc = new PageLinksController();
        $mvc -> pageLinksController();
        ?>
    </article>

    <aside>
        <div class="sidebar-widget">
            <h3 class="widget-title">Noticias Destacadas</h3>
            <ul class="news-list">
                <li>
                    <span class="date-badge">01 Dic</span>
                    <a href="#">UTA inaugura nuevos laboratorios de investigación.</a>
                </li>
                <li>
                    <span class="date-badge">28 Nov</span>
                    <a href="#">Estudiantes destacan en concurso nacional de robótica.</a>
                </li>
                <li>
                    <span class="date-badge">25 Nov</span>
                    <a href="#">Abiertas las inscripciones para posgrados 2026.</a>
                </li>
            </ul>
        </div>

        <div class="sidebar-widget">
            <h3 class="widget-title">Eventos Próximos</h3>
            <ul class="event-list">
                <li>
                    <strong>Conferencia de Innovación</strong><br>
                    <small>📍 Auditorio Central - 10 Dic</small>
                </li>
                <li>
                    <strong>Feria de Emprendimiento</strong><br>
                    <small>📍 Campus Huachi - 15 Dic</small>
                </li>
            </ul>
        </div>
        
    </aside>

    <footer>
        <p class="footer-text">&copy; <?php echo date("Y"); ?> Universidad Técnica de Ambato. Todos los derechos reservados.</p>
        <p class="footer-text">Campus Huachi, Av. Los Chasquis y Río Payamino</p>
    </footer>
</body>
</html>