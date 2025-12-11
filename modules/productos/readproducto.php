<?php
require_once dirname(__DIR__, 2) . '/config/db.php';
require_once dirname(__DIR__, 2) . '/header.php';

$stmt = $conn->prepare("SELECT * FROM productos");
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h5><b><i class="fa fa-box"></i> Productos</b></h5>
</header>

<div class="w3-row-padding w3-margin-bottom">
<div class="row">
    <div class="col-md-2 text-right">
        <h1><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create">
        <i class="bi bi-box-seam"></i> Nuevo
        </button></h1>
    </div>
</div>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th width="20">No</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>stock</th>
            <th>Descripción</th>
            <th>Imagen</th>
            <th width="100">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($productos as $producto) { ?>
            <tr>
                <td><?php echo $producto['id']; ?></td>
                <td><?php echo $producto['nombre']; ?></td>
                <td><?php echo $producto['precio']; ?></td>
                <td><?php echo $producto['stock']; ?></td>
                <td><?php echo $producto['descripcion']; ?></td>
                <td>
                    <?php if (!empty($producto['imagen']) && file_exists(dirname(__DIR__, 2) . '/imagen/' . $producto['imagen'])): ?>
                        <img src="../../imagen/<?php echo $producto['imagen']; ?>" alt="" class="img-rounded" width="150px" height="150px" style="object-fit: cover;">
                    <?php else: ?>
                        <span class="text-muted">
                            <i class="bi bi-image" style="font-size: 2rem;"></i><br>
                            Sin imagen
                        </span>
                    <?php endif; ?>
                </td>
                <td>
                <?php 
                    if ($_SESSION['usuario']['tipo'] == 'Admin') {
                        ?>
                        <a href="updateproducto.php?id=<?php echo $producto['id']; ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil-fill"></i></a>
                        <a onclick="return confirm_delete()" href="deleteproducto.php?id=<?php echo $producto['id']; ?>" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
                        <?php
                    } else {
                        ?>
                        <a href="updateproducto.php?id=<?php echo $producto['id']; ?>" class="btn btn-warning btn-sm disabled"><i class="bi bi-pencil-fill"></i></a>
                        <a onclick="return confirm_delete()" href="deleteproducto.php?id=<?php echo $producto['id']; ?>" class="btn btn-danger btn-sm disabled"><i class="bi bi-trash"></i></a>
                    <?php
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<script type="text/javascript">
function confirm_delete() {
    return confirm('¿Está seguro de eliminarlo?');
}
</script>
<script>
    
  var mySidebar = document.getElementById("mySidebar");


  var overlayBg = document.getElementById("myOverlay");


  function w3_open() {
    if (mySidebar.style.display === 'block') {
      mySidebar.style.display = 'none';
      overlayBg.style.display = "none";
    } else {
      mySidebar.style.display = 'block';
      overlayBg.style.display = "block";
    }
  }

  
  function w3_close() {
    mySidebar.style.display = "none";
    overlayBg.style.display = "none";
  }
</script>
<script src="../bootstrap/js/bootstrap.min.js"></script>

<?php
include "createproducto.php";
include "../../footer.php";
?>