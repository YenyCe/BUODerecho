<!DOCTYPE html>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sistema</title>
    <style>
        /* Reset y tipografía */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; min-height: 100vh; background: #f4f6f8; }

    /* Sidebar */
    .sidebar {
        width: 220px;
        background: #2c3e50;
        color: #fff;
        display: flex;
        flex-direction: column;
        padding-top: 20px;
    }
    .sidebar h2 { text-align: center; margin-bottom: 20px; font-size: 20px; }
    .sidebar ul { list-style: none; padding-left: 0; }
    .sidebar ul li { margin: 10px 0; }
    .sidebar ul li a {
        color: #fff;
        text-decoration: none;
        padding: 10px 20px;
        display: block;
        transition: background 0.3s;
    }
    .sidebar ul li a:hover { background: #1f2d3d; border-radius: 5px; }

    /* Contenido principal */
    .main {
        flex: 1;
        padding: 30px;
    }
    .main h3 { margin-bottom: 20px; color: #333; }
    .cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 20px;
    }
    .card {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        text-align: center;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .card a {
        text-decoration: none;
        color: #2980b9;
        font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 768px) {
        body { flex-direction: column; }
        .sidebar { width: 100%; flex-direction: row; overflow-x: auto; }
        .sidebar ul { display: flex; }
        .sidebar ul li { margin: 0 5px; }
    }
</style>

</head>
<body>

<div class="sidebar">
    <img src="../img/logo1.png" alt="Logo" />
    <h2>Sistema Académico</h2>
    <ul>
        <li><a href="../views/dashboard.php">Inicio</a></li>
        <li><a href="../views/docentes.php">Docentes</a></li>
        <li><a href="../views/materias.php">Materias</a></li>
        <li><a href="../views/alumnos.php">Alumnos</a></li>
        <li><a href="../views/semestres_grupos.php">Semestre y Grupos</a></li>
        <li><a href="../views/parciales.php">Parciales</a></li>
        <li><a href="../views/generar_listas.php">Lista</a></li>
        <li><a href="../views/horarios.php">Horario</a></li>
        <li><a href="../views/salir.php">Cerrar Sesión</a></li>
    </ul>
</div>

<div class="main">
    <h3>Bienvenido al sistema</h3>
    <p>Accede rápidamente a cada sección desde las tarjetas o el menú lateral.</p>

    <div class="cards">
        <div class="card"><a href="views/docentes.php">Registrar Docente</a></div>
        <div class="card"><a href="views/materias.php">Registrar Materias</a></div>
        <div class="card"><a href="views/semestres_grupos.php">Registrar Semestres y Grupos</a></div>
        <div class="card"><a href="views/parciales.php">Parciales</a></div>
        <div class="card"><a href="views/generar_listas.php">Generar Listas</a></div>
        <div class="card"><a href="views/horarios.php">Horarios</a></div>
    </div>
</div>
</body>
</html>
