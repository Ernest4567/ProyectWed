<?php
require_once dirname(__DIR__, 2) . '/config/db.php';
require_once dirname(__DIR__, 2) . '/header.php';

if (!isset($conn)) {
    die("Error: No hay conexión a la base de datos");
}

try {
    $stmt = $conn->prepare("SELECT * FROM clientes");
    $stmt->execute();
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>
<h5><b><i class="bi bi-people-fill"></i> Clientes</b></h5>
</header>

<div class="w3-row-padding w3-margin-bottom">
<div class="row">
    <div class="col-md-2 text-right">
        <h1><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create">
        <i class="bi bi-person-plus"></i> Nuevo
        </button></h1>
    </div>
</div>

<?php if (empty($clientes)): ?>
    <div class="alert alert-info">
        No hay clientes registrados.
    </div>
<?php else: ?>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th width="20">No</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Imagen</th>
            <th width="100">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes as $cliente) { 
            $imagenPath = dirname(__DIR__, 2) . '/imagen/' . $cliente['imagen'];
            $imagenURL = '../../imagen/' . $cliente['imagen'];
        ?>
            <tr>
                <td><?php echo htmlspecialchars($cliente['id']); ?></td>
                <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                <td><?php echo htmlspecialchars($cliente['direccion']); ?></td>
                <td>
                    <?php if (!empty($cliente['imagen']) && file_exists($imagenPath)): ?>
                        <img src="<?php echo $imagenURL; ?>" alt="Imagen cliente" class="img-rounded" width="80px" height="80px" style="object-fit: cover;">
                    <?php else: ?>
                        <span class="text-muted">
                            <i class="bi bi-person-circle" style="font-size: 2rem;"></i><br>
                            Sin imagen
                        </span>
                    <?php endif; ?>
                </td>
                <td>
                <?php 
                    if (isset($_SESSION['usuario']['tipo']) && $_SESSION['usuario']['tipo'] == 'Admin') {
                        ?>
                        <a href="updatecliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-warning btn-sm mb-1">
                            <i class="bi bi-pencil-fill"></i>
                        </a><br>
                        <a onclick="return confirm_delete()" href="deletecliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </a>
                        <?php
                    } else {
                        ?>
                        <a href="updatecliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-warning btn-sm disabled mb-1">
                            <i class="bi bi-pencil-fill"></i>
                        </a><br>
                        <a onclick="return confirm_delete()" href="deletecliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-danger btn-sm disabled">
                            <i class="bi bi-trash"></i>
                        </a>
                    <?php
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php endif; ?>

<script type="text/javascript">
function confirm_delete() {
    return confirm('¿Está seguro de eliminarlo?');
}
</script>

<?php
include "createcliente.php";
include "../../footer.php";
?>