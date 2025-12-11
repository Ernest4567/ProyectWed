<?php
require_once dirname(__DIR__, 2) . '/config/db.php';
session_start();

if (isset($_GET['id']) && !empty($_GET['id']) && $_GET['id'] > 0) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id = :id");
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $nombre = $result['nombre'];
        $descripcion = $result['descripcion'];
        $precio = $result['precio'];
        $stock = $result['stock'];
        $proveedor_id = $result['proveedor_id'];
        $imagenActual = $result['imagen'];
        
        // Obtener proveedores para el select
        $stmtProv = $conn->query("SELECT id, nombre FROM proveedores");
        $proveedores = $stmtProv->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "<script>
                alert('Producto no encontrado.');
                window.location.href = 'readproducto.php';
              </script>";
        exit();
    }
} else {
    echo "<script>
            alert('ID de producto inválido.');
            window.location.href = 'readproducto.php';
          </script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $proveedor_id = $_POST['proveedor_id'];

    $imagen = $imagenActual;
    
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $carpetaImagen = dirname(__DIR__, 2) . '/imagen';
        if (!file_exists($carpetaImagen)) {
            mkdir($carpetaImagen, 0777, true);
        }
        
        $nombreOriginal = $_FILES['imagen']['name'];
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $nombreLimpio = time() . '_' . uniqid() . '.' . $extension;
        
        $directorioDestino = $carpetaImagen . "/" . $nombreLimpio;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $directorioDestino)) {
            $imagen = $nombreLimpio;
            
            if (!empty($imagenActual) && file_exists($carpetaImagen . "/" . $imagenActual)) {
                unlink($carpetaImagen . "/" . $imagenActual);
            }
        }
    }

    $stmt = $conn->prepare("
        UPDATE productos 
        SET nombre = :nombre, descripcion = :descripcion, precio = :precio, 
            stock = :stock, proveedor_id = :proveedor_id, imagen = :imagen
        WHERE id = :id
    ");
    $stmt->bindValue(":nombre", $nombre);
    $stmt->bindValue(":descripcion", $descripcion);
    $stmt->bindValue(":precio", $precio);
    $stmt->bindValue(":stock", $stock);
    $stmt->bindValue(":proveedor_id", $proveedor_id);
    $stmt->bindValue(":imagen", $imagen);
    $stmt->bindValue(":id", $id);

    if ($stmt->execute()) {
        echo "<script>
                alert('Producto actualizado correctamente.');
                window.location.href = 'readproducto.php';
              </script>";
    } else {
        echo "<script>
                alert('Error al actualizar el producto.');
                window.location.href = 'readproducto.php';
              </script>";
    }
}

require_once dirname(__DIR__, 2) . '/header.php';
?>
<h5><b><i class="fa fa-pencil"></i> Actualizar Producto</b></h5>
</header>
<div class="row">
    <div class="col-md-2 text-right">
        <h1><a href="readproducto.php" class="btn btn-info">Regresar</a></h1>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?php echo htmlspecialchars($descripcion); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="precio" class="form-label">Precio</label>
            <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="<?php echo $precio; ?>" required>
        </div>
        <div class="mb-3">
            <label for="stock" class="form-label">Stock</label>
            <input type="number" class="form-control" id="stock" name="stock" value="<?php echo $stock; ?>" required>
        </div>
        <div class="mb-3">
            <label for="proveedor_id" class="form-label">Proveedor</label>
            <select class="form-control" id="proveedor_id" name="proveedor_id" required>
                <option value="">Seleccione un proveedor</option>
                <?php foreach ($proveedores as $proveedor): ?>
                    <option value="<?php echo $proveedor['id']; ?>" 
                        <?php echo ($proveedor_id == $proveedor['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($proveedor['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen Actual</label>
            <?php if (!empty($imagenActual)): 
                $imagenPath = dirname(__DIR__, 2) . '/imagen/' . $imagenActual;
            ?>
                <?php if (file_exists($imagenPath)): ?>
                    <div>
                        <img src="../../imagen/<?php echo htmlspecialchars($imagenActual); ?>" alt="Imagen del Producto" style="max-width: 200px;">
                    </div>
                <?php else: ?>
                    <p class="text-muted">Imagen no encontrada</p>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-muted">Sin imagen actual</p>
            <?php endif; ?>
            <label class="form-label mt-2">Cambiar imagen (opcional)</label>
            <input type="file" class="form-control" name="imagen" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
<?php require_once dirname(__DIR__, 2) . '/footer.php'; ?>