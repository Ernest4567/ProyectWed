<?php
require_once dirname(__DIR__, 2) . '/config/db.php';
session_start();

if (isset($_GET['id']) && !empty($_GET['id']) && $_GET['id'] > 0) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM proveedores WHERE id = :id");
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $nombre = $result['nombre'];
        $email = $result['email'];
        $telefono = $result['telefono'];
        $direccion = $result['direccion'];
        $empresa = $result['empresa'];
        $imagenActual = $result['imagen'];
    } else {
        echo "<script>
                alert('Proveedor no encontrado.');
                window.location.href = 'readproveedores.php';
              </script>";
        exit();
    }
} else {
    echo "<script>
            alert('ID de proveedor inválido.');
            window.location.href = 'readproveedores.php';
          </script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $empresa = $_POST['empresa'];

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
        UPDATE proveedores 
        SET nombre = :nombre, email = :email, telefono = :telefono, 
            direccion = :direccion, empresa = :empresa, imagen = :imagen
        WHERE id = :id
    ");
    $stmt->bindValue(":nombre", $nombre);
    $stmt->bindValue(":email", $email);
    $stmt->bindValue(":telefono", $telefono);
    $stmt->bindValue(":direccion", $direccion);
    $stmt->bindValue(":empresa", $empresa);
    $stmt->bindValue(":imagen", $imagen);
    $stmt->bindValue(":id", $id);

    if ($stmt->execute()) {
        echo "<script>
                alert('Proveedor actualizado correctamente.');
                window.location.href = 'readproveedores.php';
              </script>";
    } else {
        echo "<script>
                alert('Error al actualizar el proveedor.');
                window.location.href = 'readproveedores.php';
              </script>";
    }
}

require_once dirname(__DIR__, 2) . '/header.php';
?>
<h5><b><i class="fa fa-pencil"></i> Actualizar Proveedor</b></h5>
</header>
<div class="row">
    <div class="col-md-2 text-right">
        <h1><a href="readproveedores.php" class="btn btn-info">Regresar</a></h1>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" required>
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <textarea class="form-control" id="direccion" name="direccion" required><?php echo htmlspecialchars($direccion); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="empresa" class="form-label">Empresa</label>
            <input type="text" class="form-control" id="empresa" name="empresa" value="<?php echo htmlspecialchars($empresa); ?>" required>
        </div>
        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen Actual</label>
            <?php if (!empty($imagenActual)): 
                $imagenPath = dirname(__DIR__, 2) . '/imagen/' . $imagenActual;
            ?>
                <?php if (file_exists($imagenPath)): ?>
                    <div>
                        <img src="../../imagen/<?php echo htmlspecialchars($imagenActual); ?>" alt="Imagen del Proveedor" style="max-width: 200px;">
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