<?php
session_start();

if (!$_SESSION['usuarioLogeado']) {
    header("Location:login.php");
}

include("conexion.php");

function obtenerTodasLasPropiedades()
{
    global $conn;
    $query = "SELECT * FROM propiedades  ORDER BY fecha_alta DESC";
    $result = mysqli_query($conn, $query);
    return $result;
}

function obtenerTipo($id_tipo)
{
    global $conn;
    $query = "SELECT * FROM tipos WHERE id='$id_tipo'";
    $resultado_tipo = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($resultado_tipo);
    return $row['nombre_tipo'];
}

// Verificar si se ha enviado el formulario de búsqueda
if (isset($_GET['buscar'])) {
    // Obtener el término de búsqueda ingresado por el usuario
    $busqueda = $_GET['busqueda'];

    // Escapar caracteres especiales para prevenir SQL Injection
    $busqueda = mysqli_real_escape_string($conn, $busqueda);

    // Construir la consulta SQL dinámicamente para buscar en varios campos
    $query = "SELECT * FROM propiedades 
              WHERE id LIKE '%$busqueda%' 
              OR titulo LIKE '%$busqueda%' 
              OR tipo IN (SELECT id FROM tipos WHERE nombre_tipo LIKE '%$busqueda%') 
              OR estado LIKE '%$busqueda%' 
              OR ubicacion LIKE '%$busqueda%' 
              OR fecha_alta LIKE '%$busqueda%'
              ORDER BY fecha_alta DESC";
    $result = mysqli_query($conn, $query);
} else {
    // Si no se ha enviado el formulario de búsqueda, obtener todas las propiedades por defecto
    $result = obtenerTodasLasPropiedades();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="estilo.css">
    <title>SAWPI - Admin</title>
</head>

<body>
    <?php include("header.php"); ?>

    <div id="contenedor-admin">
        <?php include("contenedor-menu.php"); ?>

        <div class="contenedor-principal">
            <div id="listado-propiedades">
                <h2>Listado de Propiedades</h2>
                <hr>
                <div class="contenedor-busqueda">
                    <form id="form-busqueda" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                        <input type="text" id="busqueda" name="busqueda" placeholder="Buscar...">
                        <button type="submit" name="buscar"><i class="fas fa-search"></i> Buscar</button>
                    </form>
                </div>
                <div class="contenedor-tabla">
                    <table>
                        <tr>
                            <th>#ID</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Ubicación</th>
                            <th>Fecha de Publicación</th>
                            <th>Acciones</th>
                        </tr>
                        <?php while ($propiedad = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td><?php echo $propiedad['id'] ?></td>
                                <td><?php echo $propiedad['titulo'] ?></td>
                                <td><?php echo obtenerTipo($propiedad['tipo']) ?></td>
                                <td><?php echo $propiedad['estado'] ?></td>
                                <td><?php echo $propiedad['ubicacion'] ?></td>
                                <td><?php echo $propiedad['fecha_alta'] ?></td>
                                <td>
                                    <form action="ver-detalle-propiedad.php" method="get" class="form-acciones">
                                        <input type="hidden" name="id" value="<?php echo $propiedad['id'] ?>">
                                        <input type="submit" value="Ver Detalle" name="detalle">
                                    </form>
                                    <form action="actualizar-propiedad.php" method="get" class="form-acciones">
                                        <input type="hidden" name="id" value="<?php echo $propiedad['id'] ?>">
                                        <input type="submit" value="Actualizar" name="actualizar">
                                    </form>
                                    <a href="#" id="<?php echo $propiedad['id'] ?>" onclick="abrirModal(<?php echo $propiedad['id'] ?>)" class="btn-eliminar">Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile ?>
                    </table>
                </div>
                <?php if (isset($_GET['buscar'])) : ?>
                    <div class="contenedor-busqueda">
                    <button class="boton-atras" onclick="window.history.back();">Atrás</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        $('#link-listado-propiedades').addClass('pagina-activa');
    </script>
    <script src="script.js"></script>
</body>

</html>
