<?php
require_once dirname(__DIR__, 2) . '/config/db.php';
require_once dirname(__DIR__, 2) . '/header.php';

try {
    // Obtener todas las ventas con información completa
    $stmt = $conn->prepare("
        SELECT 
            v.*,
            c.nombre as cliente_nombre,
            c.email as cliente_email,
            c.telefono as cliente_telefono,
            p.nombre as producto_nombre,
            p.descripcion as producto_descripcion,
            p.imagen as producto_imagen,
            pr.nombre as proveedor_nombre
        FROM ventas v
        LEFT JOIN clientes c ON v.cliente_id = c.id
        LEFT JOIN productos p ON v.producto_id = p.id
        LEFT JOIN proveedores pr ON p.proveedor_id = pr.id
        ORDER BY v.id DESC
    ");
    $stmt->execute();
    $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>
<h5><b><i class="bi bi-cart-check"></i> Historial de Ventas</b></h5>
</header>

<div class="w3-row-padding w3-margin-bottom">
<div class="row">
    <div class="col-md-12">
        <h4>Registro de Compras Realizadas</h4>
        <p class="text-muted">Lista de todas las ventas registradas en el sistema.</p>
    </div>
</div>

<?php if (empty($ventas)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No hay ventas registradas aún.
    </div>
<?php else: ?>
    
<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h6 class="card-title">Total Ventas</h6>
                <h4><?php echo count($ventas); ?></h4>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h6 class="card-title">Productos Vendidos</h6>
                <h4>
                    <?php 
                    $totalProductos = 0;
                    foreach ($ventas as $venta) {
                        $totalProductos += $venta['cantidad'];
                    }
                    echo $totalProductos;
                    ?>
                </h4>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h6 class="card-title">Ingreso Total</h6>
                <h4>
                    <?php 
                    $ingresoTotal = 0;
                    foreach ($ventas as $venta) {
                        $ingresoTotal += $venta['total'];
                    }
                    echo '$' . number_format($ingresoTotal, 2);
                    ?>
                </h4>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h6 class="card-title">Venta Promedio</h6>
                <h4>
                    <?php 
                    $promedio = count($ventas) > 0 ? $ingresoTotal / count($ventas) : 0;
                    echo '$' . number_format($promedio, 2);
                    ?>
                </h4>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Ventas -->
<div class="card">
    <div class="card-header bg-secondary text-white">
        <i class="bi bi-receipt"></i> Detalle de Ventas
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr class="text-center">
                        <th># Venta</th>
                        <th>Cliente</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ventas as $venta): 
                        $imagenURL = '../../imagen/' . ($venta['producto_imagen'] ?? '');
                        $imagenPath = dirname(__DIR__, 2) . '/imagen/' . ($venta['producto_imagen'] ?? '');
                    ?>
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-dark">#<?php echo $venta['id']; ?></span>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($venta['cliente_nombre']); ?></strong>
                                <br>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars($venta['cliente_email']); ?><br>
                                    <?php echo htmlspecialchars($venta['cliente_telefono']); ?>
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($venta['producto_imagen']) && file_exists($imagenPath)): ?>
                                        <img src="<?php echo $imagenURL; ?>" alt="Imagen" width="50" height="50" class="rounded me-2">
                                    <?php endif; ?>
                                    <div>
                                        <strong><?php echo htmlspecialchars($venta['producto_nombre']); ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars(substr($venta['producto_descripcion'], 0, 50)) . '...'; ?>
                                            <br>
                                            <i>Proveedor: <?php echo htmlspecialchars($venta['proveedor_nombre'] ?? 'N/A'); ?></i>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary"><?php echo $venta['cantidad']; ?></span>
                            </td>
                            <td class="text-end">
                                $<?php echo number_format($venta['precio_unitario'], 2); ?>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-success">$<?php echo number_format($venta['total'], 2); ?></span>
                            </td>
                            <td class="text-center">
                                <?php if (isset($_SESSION['usuario']['tipo']) && $_SESSION['usuario']['tipo'] == 'Admin'): ?>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="if(confirm('¿Eliminar esta venta?\\nSe devolverá el stock al producto.')) {
                                                window.location.href='deleteventa.php?id=<?php echo $venta['id']; ?>&producto_id=<?php echo $venta['producto_id']; ?>&cantidad=<?php echo $venta['cantidad']; ?>';
                                            }">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-danger disabled">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-dark">
                        <td colspan="5" class="text-end"><strong>TOTAL GENERAL:</strong></td>
                        <td class="text-end">
                            <span class="fw-bold text-white">$<?php 
                                $totalGeneral = 0;
                                foreach ($ventas as $venta) {
                                    $totalGeneral += $venta['total'];
                                }
                                echo number_format($totalGeneral, 2);
                            ?></span>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <?php if (isset($_SESSION['usuario']['tipo']) && $_SESSION['usuario']['tipo'] == 'Admin'): ?>
            <div class="mt-3 text-end">
                <button type="button" class="btn btn-danger" 
                        onclick="if(confirm('¿Eliminar TODAS las ventas?\\nEsta acción NO se puede deshacer.')) {
                            window.location.href='deleteventa.php?action=clear_all';
                        }">
                    <i class="bi bi-trash"></i> Eliminar Todas las Ventas
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Resumen por Cliente -->
<div class="card mt-4">
    <div class="card-header bg-info text-white">
        <i class="bi bi-people"></i> Resumen por Cliente
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Compras Realizadas</th>
                        <th>Productos Comprados</th>
                        <th>Total Gastado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Agrupar por cliente
                    $clientesResumen = [];
                    foreach ($ventas as $venta) {
                        $clienteId = $venta['cliente_id'];
                        if (!isset($clientesResumen[$clienteId])) {
                            $clientesResumen[$clienteId] = [
                                'nombre' => $venta['cliente_nombre'],
                                'compras' => 0,
                                'productos' => 0,
                                'total' => 0
                            ];
                        }
                        $clientesResumen[$clienteId]['compras']++;
                        $clientesResumen[$clienteId]['productos'] += $venta['cantidad'];
                        $clientesResumen[$clienteId]['total'] += $venta['total'];
                    }
                    
                    foreach ($clientesResumen as $cliente):
                    ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($cliente['nombre']); ?></strong></td>
                            <td><span class="badge bg-primary"><?php echo $cliente['compras']; ?></span></td>
                            <td><span class="badge bg-success"><?php echo $cliente['productos']; ?></span></td>
                            <td><span class="fw-bold text-success">$<?php echo number_format($cliente['total'], 2); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include "../../footer.php"; ?>