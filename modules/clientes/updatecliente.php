<?php
require_once dirname(__DIR__, 2) . '/config/db.php';

if (isset($_GET['id']) && !empty($_GET['id']) && $_GET['id'] > 0) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM clientes WHERE id = :id");
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $nombre = $result['nombre'];
        $email = $result['email'];
        $telefono = $result['telefono'];
        $direccion = $result['direccion'];
        $imagenActual = $result['imagen'];
    } else {
        echo "<script>
                alert('Cliente no encontrado.');
                window.location.href = 'readcliente.php';
              </script>";
        exit();
    }
} else {
    echo "<script>
            alert('ID de cliente inválido.');
            window.location.href = 'readcliente.php';
          </script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

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
        UPDATE clientes 
        SET nombre = :nombre, email = :email, telefono = :telefono, direccion = :direccion, imagen = :imagen
        WHERE id = :id
    ");
    $stmt->bindValue(":nombre", $nombre);
    $stmt->bindValue(":email", $email);
    $stmt->bindValue(":telefono", $telefono);
    $stmt->bindValue(":direccion", $direccion);
    $stmt->bindValue(":imagen", $imagen);
    $stmt->bindValue(":id", $id);

    if ($stmt->execute()) {
        echo "<script>
                alert('Cliente actualizado correctamente.');
                window.location.href = 'readcliente.php';
              </script>";
    } else {
        echo "<script>
                alert('Error al actualizar el cliente.');
                window.location.href = 'readcliente.php';
              </script>";
    }
}

require_once dirname(__DIR__, 2) . '/header.php';
?>
<h5><b><i class="fa fa-pencil"></i> Actualizar Cliente</b></h5>
</header>
<div class="row">
    <div class="col-md-2 text-right">
        <h1><a href="readcliente.php" class="btn btn-info">Regresar</a></h1>
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
            <label for="imagen" class="form-label">Imagen Actual</label>
            <?php if (!empty($imagenActual)): 
                $imagenPath = dirname(__DIR__, 2) . '/imagen/' . $imagenActual;
            ?>
                <?php if (file_exists($imagenPath)): ?>
                    <div>
                        <img src="../../imagen/<?php echo htmlspecialchars($imagenActual); ?>" alt="Imagen del Cliente" style="max-width: 200px; display: block; margin-bottom: 10px; border: 2px solid #ddd; padding: 5px; border-radius: 5px;">
                    </div>
                <?php else: ?>
                    <p class="text-muted">
                        <i class="bi bi-person-circle" style="font-size: 3rem;"></i><br>
                        Imagen no encontrada en el servidor
                    </p>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-muted">
                    <i class="bi bi-person-circle" style="font-size: 3rem;"></i><br>
                    Sin imagen actual
                </p>
            <?php endif; ?>
            <label for="imagen" class="form-label mt-2">Cambiar imagen (opcional)</label>
            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
            <small class="text-muted">Formatos permitidos: JPG, PNG, GIF</small>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>

<?php 
require_once dirname(__DIR__, 2) . '/footer.php'; 
?>