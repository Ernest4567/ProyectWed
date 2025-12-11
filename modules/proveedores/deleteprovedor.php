<?php
require_once dirname(__DIR__, 2) . '/config/db.php';
session_start();

if (isset($_GET['id']) && !empty($_GET['id']) && $_GET['id'] > 0) {
    $id = intval($_GET['id']);
    
    // Primero obtener la imagen para eliminarla
    $stmt = $conn->prepare("SELECT imagen FROM proveedores WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Eliminar imagen si existe
    if ($proveedor && !empty($proveedor['imagen'])) {
        $imagenPath = dirname(__DIR__, 2) . '/imagen/' . $proveedor['imagen'];
        if (file_exists($imagenPath)) {
            unlink($imagenPath);
        }
    }

    // Eliminar el proveedor
    $stmt = $conn->prepare("DELETE FROM proveedores WHERE id = :id LIMIT 1");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo "<script>
                    alert('Proveedor eliminado correctamente.');
                    window.location.href = 'readproveedores.php';
                  </script>";
        } else {
            echo "<script>
                    alert('No se encontró el proveedor a eliminar.');
                    window.location.href = 'readproveedores.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Error al eliminar el proveedor.');
                window.location.href = 'readproveedores.php';
              </script>";
    }
} else {
    echo "<script>
            alert('ID de proveedor no válido.');
            window.location.href = 'readproveedores.php';
          </script>";
}
?>