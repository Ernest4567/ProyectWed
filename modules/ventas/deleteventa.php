<?php
require_once dirname(__DIR__, 2) . '/config/db.php';
session_start();

// Verificar permisos
if (!isset($_SESSION['usuario']['tipo']) || $_SESSION['usuario']['tipo'] != 'Admin') {
    echo "<script>
            alert('No tienes permisos para esta acción.');
            window.location.href = 'readventas.php';
          </script>";
    exit();
}

try {
    if (isset($_GET['action']) && $_GET['action'] == 'clear_all') {
        // Obtener todas las ventas para devolver stock
        $stmt = $conn->prepare("SELECT producto_id, cantidad FROM ventas");
        $stmt->execute();
        $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Devolver stock de todas las ventas
        foreach ($ventas as $venta) {
            $stmtUpdate = $conn->prepare("
                UPDATE productos SET stock = stock + :cantidad WHERE id = :id
            ");
            $stmtUpdate->bindParam(":cantidad", $venta['cantidad']);
            $stmtUpdate->bindParam(":id", $venta['producto_id']);
            $stmtUpdate->execute();
        }
        
        // Eliminar todas las ventas
        $conn->exec("DELETE FROM ventas");
        
        echo "<script>
                alert('Todas las ventas han sido eliminadas. Stock devuelto a los productos.');
                window.location.href = 'readventas.php';
              </script>";
        exit();
    }
    
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = intval($_GET['id']);
        $producto_id = isset($_GET['producto_id']) ? intval($_GET['producto_id']) : 0;
        $cantidad = isset($_GET['cantidad']) ? intval($_GET['cantidad']) : 0;
        
        if ($producto_id > 0 && $cantidad > 0) {
            // 1. Devolver stock al producto
            $stmtUpdate = $conn->prepare("
                UPDATE productos SET stock = stock + :cantidad WHERE id = :id
            ");
            $stmtUpdate->bindParam(":cantidad", $cantidad);
            $stmtUpdate->bindParam(":id", $producto_id);
            $stmtUpdate->execute();
        }
        
        // 2. Eliminar la venta
        $stmtDelete = $conn->prepare("DELETE FROM ventas WHERE id = :id LIMIT 1");
        $stmtDelete->bindParam(":id", $id, PDO::PARAM_INT);
        
        if ($stmtDelete->execute()) {
            if ($stmtDelete->rowCount() > 0) {
                echo "<script>
                        alert('Venta eliminada correctamente. Stock devuelto al producto.');
                        window.location.href = 'readventas.php';
                      </script>";
            } else {
                echo "<script>
                        alert('No se encontró la venta a eliminar.');
                        window.location.href = 'readventas.php';
                      </script>";
            }
        } else {
            echo "<script>
                    alert('Error al eliminar la venta.');
                    window.location.href = 'readventas.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('ID de venta no válido.');
                window.location.href = 'readventas.php';
              </script>";
    }
} catch (PDOException $e) {
    echo "<script>
            alert('Error: " . addslashes($e->getMessage()) . "');
            window.location.href = 'readventas.php';
          </script>";
}
?>