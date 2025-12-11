<?php
require_once dirname(__DIR__, 2) . '/config/db.php';
session_start();

if (isset($_GET['id']) && !empty($_GET['id']) && $_GET['id'] > 0) {
    $id = intval($_GET['id']);
    
    // Primero obtener la imagen para eliminarla
    $stmt = $conn->prepare("SELECT imagen FROM productos WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Eliminar imagen si existe
    if ($producto && !empty($producto['imagen'])) {
        $imagenPath = dirname(__DIR__, 2) . '/imagen/' . $producto['imagen'];
        if (file_exists($imagenPath)) {
            unlink($imagenPath);
        }
    }

    // Eliminar el producto
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = :id LIMIT 1");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo "<script>
                    alert('Producto eliminado correctamente.');
                    window.location.href = 'readproducto.php';
                  </script>";
        } else {
            echo "<script>
                    alert('No se encontró el producto a eliminar.');
                    window.location.href = 'readproducto.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Error al eliminar el producto.');
                window.location.href = 'readproducto.php';
              </script>";
    }
} else {
    echo "<script>
            alert('ID de producto no válido.');
            window.location.href = 'readproducto.php';
          </script>";
}
?>