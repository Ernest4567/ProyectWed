<?php
require_once dirname(__DIR__, 2) . '/config/db.php';

if (isset($_GET['id']) && !empty($_GET['id']) && $_GET['id'] > 0) {
    $id = intval($_GET['id']);
    
    // Primero obtener la imagen para eliminarla
    $stmt = $conn->prepare("SELECT imagen FROM clientes WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Eliminar imagen si existe
    if ($cliente && !empty($cliente['imagen'])) {
        $imagenPath = dirname(__DIR__, 2) . '/imagen/' . $cliente['imagen'];
        if (file_exists($imagenPath)) {
            unlink($imagenPath);
        }
    }

    // Eliminar el cliente
    $stmt = $conn->prepare("DELETE FROM clientes WHERE id = :id LIMIT 1");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo "<script>
                    alert('Cliente eliminado correctamente.');
                    window.location.href = 'readcliente.php';
                  </script>";
        } else {
            echo "<script>
                    alert('No se encontró el cliente a eliminar.');
                    window.location.href = 'readcliente.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Error al eliminar el cliente.');
                window.location.href = 'readcliente.php';
              </script>";
    }
} else {
    echo "<script>
            alert('ID de cliente no válido.');
            window.location.href = 'readcliente.php';
          </script>";
}
?>