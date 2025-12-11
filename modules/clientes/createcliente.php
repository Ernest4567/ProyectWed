<?php
require_once dirname(__DIR__, 2) . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = (isset($_POST['nombre']) ? $_POST['nombre'] : "");
    $email = (isset($_POST['email']) ? $_POST['email'] : "");
    $telefono = (isset($_POST['telefono']) ? $_POST['telefono'] : "");
    $direccion = (isset($_POST['direccion']) ? $_POST['direccion'] : "");
    
    $imagen = "";
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $carpetaImagen = dirname(__DIR__, 2) . '/imagen';
        if (!file_exists($carpetaImagen)) {
            mkdir($carpetaImagen, 0777, true);
        }
        
        $nombreOriginal = $_FILES['imagen']['name'];
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $nombreLimpio = time() . '_' . uniqid() . '.' . $extension;
        
        $ruta = $carpetaImagen . "/" . $nombreLimpio;
        $resultado = move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
        
        if ($resultado) {
            $imagen = $nombreLimpio;
        } else {
            echo '<div class="alert alert-warning" role="alert">
  Advertencia: No se pudo subir la imagen, pero el cliente será creado sin imagen.
</div>';
        }
    }
    
    try {
        $stmtMaxId = $conn->query("SELECT COALESCE(MAX(id), 0) + 1 as next_id FROM clientes");
        $nextId = $stmtMaxId->fetch(PDO::FETCH_ASSOC)['next_id'];
        
        $stmt = $conn->prepare("INSERT INTO clientes(id, nombre, email, telefono, direccion, imagen) VALUES(:id, :nombre, :email, :telefono, :direccion, :imagen)");
        $stmt->bindParam(":id", $nextId);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":telefono", $telefono);
        $stmt->bindParam(":direccion", $direccion);
        $stmt->bindParam(":imagen", $imagen);

        if ($stmt->execute()) {
            echo '<div class="alert alert-success" role="alert">
  Cliente agregado correctamente <a href="readcliente.php" class="alert-link">Volver a clientes</a>.
</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">
  Error al agregar el cliente <a href="readcliente.php" class="alert-link">Intenta nuevamente</a>.
</div>';
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!-- Modal de Crear Cliente -->
<div class="modal" id="create" tabindex="-1" aria-labelledby="modalLoginLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title center" id="modalLoginLabel"><i class="bi bi-person-plus"></i> AGREGAR CLIENTE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <i class="bi bi-person"></i>
                        <label for="inputNombre" class="form-label"> Nombre del Cliente</label>
                        <input type="text" class="form-control" id="inputNombre" name="nombre" placeholder="Ingresa el nombre completo del cliente" required />
                    </div>
                    <div class="mb-3">
                        <i class="bi bi-envelope"></i>
                        <label for="inputEmail" class="form-label"> Email</label>
                        <input type="email" class="form-control" id="inputEmail" name="email" placeholder="correo@cliente.com" required />
                    </div>
                    <div class="mb-3">
                        <i class="bi bi-telephone"></i>
                        <label for="inputTelefono" class="form-label"> Teléfono</label>
                        <input type="text" class="form-control" id="inputTelefono" name="telefono" placeholder="1234567890" required />
                    </div>
                    <div class="mb-3">
                        <i class="bi bi-geo-alt"></i>
                        <label for="inputDireccion" class="form-label"> Dirección</label>
                        <textarea class="form-control" id="inputDireccion" name="direccion" rows="2" placeholder="Dirección completa" required></textarea>
                    </div>
                    <div class="mb-3">
                        <i class="bi bi-image"></i>
                        <label for="inputImagen" class="form-label"> Imagen</label>
                        <input type="file" class="form-control" name="imagen" id="imagen" accept="image/*" />
                        <small class="text-muted">Formatos: JPG, PNG, GIF (opcional)</small>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-floppy2-fill"></i>
                            Guardar
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>