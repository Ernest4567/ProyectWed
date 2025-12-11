<?php
require_once dirname(__DIR__, 2) . '/config/db.php';
require_once dirname(__DIR__, 2) . '/header.php';

if (!isset($conn)) {
    die("Error: No hay conexión a la base de datos");
}

try {
    $stmt = $conn->prepare("SELECT * FROM proveedores");
    $stmt->execute();
    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>
<h5><b><i class="fa fa-truck"></i> Proveedores</b></h5>
</header>

<div class="w3-row-padding w3-margin-bottom">
<div class="row">
    <div class="col-md-2 text-right">
        <h1><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create">
        <i class="bi bi-person-plus"></i> Nuevo
        </button></h1>
    </div>
</div>

<?php if (empty($proveedores)): ?>
    <div class="alert alert-info">
        No hay proveedores registrados.
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
            <th>Empresa</th>
            <th>Imagen</th>
            <th width="100">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($proveedores as $proveedor) { ?>
            <tr>
                <td><?php echo htmlspecialchars($proveedor['id']); ?></td>
                <td><?php echo htmlspecialchars($proveedor['nombre']); ?></td>
                <td><?php echo htmlspecialchars($proveedor['email']); ?></td>
                <td><?php echo htmlspecialchars($proveedor['telefono']); ?></td>
                <td><?php echo htmlspecialchars($proveedor['direccion']); ?></td>
                <td><?php echo htmlspecialchars($proveedor['empresa']); ?></td>
                <td>
                    <?php if (!empty($proveedor['imagen']) && file_exists(dirname(__DIR__, 2) . '/imagen/' . $proveedor['imagen'])): ?>
                        <img src="../../imagen/<?php echo htmlspecialchars($proveedor['imagen']); ?>" alt="Imagen proveedor" class="img-rounded" width="80px" height="80px" style="object-fit: cover;">
                    <?php else: ?>
                        <span class="text-muted">
                            <i class="bi bi-image" style="font-size: 2rem;"></i><br>
                            Sin imagen
                        </span>
                    <?php endif; ?>
                </td>
                <td>
                <?php 
                    if (isset($_SESSION['usuario']['tipo']) && $_SESSION['usuario']['tipo'] == 'Admin') {
                        ?>
                        <a href="updateprovedor.php?id=<?php echo $proveedor['id']; ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil-fill"></i></a>
                        <a onclick="return confirm_delete()" href="deleteprovedor.php?id=<?php echo $proveedor['id']; ?>" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
                        <?php
                    } else {
                        ?>
                        <a href="updateprovedor.php?id=<?php echo $proveedor['id']; ?>" class="btn btn-warning btn-sm disabled"><i class="bi bi-pencil-fill"></i></a>
                        <a onclick="return confirm_delete()" href="deleteprovedor.php?id=<?php echo $proveedor['id']; ?>" class="btn btn-danger btn-sm disabled"><i class="bi bi-trash"></i></a>
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
include "createeprovedor.php";
include "../../footer.php";
?>