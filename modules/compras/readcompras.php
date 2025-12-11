<?php
require_once dirname(__DIR__, 2) . '/config/db.php';
require_once dirname(__DIR__, 2) . '/header.php';

// Obtener todos los productos disponibles (con stock > 0)
try {
    $stmt = $conn->prepare("SELECT * FROM productos WHERE stock > 0 ORDER BY nombre");
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}

// Obtener clientes
$clientes = $conn->query("SELECT id, nombre FROM clientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>
<h5><b><i class="bi bi-cart-plus"></i> Comprar Productos</b></h5>
</header>

<div class="w3-row-padding w3-margin-bottom">
<div class="row">
    <div class="col-md-12">
        <h4>Realizar Compra</h4>
        <p class="text-muted">Selecciona un cliente y los productos a comprar.</p>
    </div>
</div>

<?php if (empty($productos)): ?>
    <div class="alert alert-warning">
        No hay productos disponibles para comprar.
    </div>
<?php elseif (empty($clientes)): ?>
    <div class="alert alert-warning">
        No hay clientes registrados. Primero registra clientes en el módulo de Clientes.
    </div>
<?php else: ?>
    
<div class="row">
    <div class="col-md-8">
        <!-- Formulario de Compra -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-cart-check"></i> Carrito de Compra
            </div>
            <div class="card-body">
                <form method="post" action="" id="formCompra">
                    <div class="mb-3">
                        <label for="cliente_id" class="form-label">Cliente *</label>
                        <select class="form-control" id="cliente_id" name="cliente_id" required>
                            <option value="" selected disabled>-- Selecciona un cliente --</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente['id']; ?>">
                                    <?php echo htmlspecialchars($cliente['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="producto_id" class="form-label">Producto *</label>
                        <select class="form-control" id="producto_id" name="producto_id" required onchange="actualizarInfoProducto()">
                            <option value="" selected disabled>-- Selecciona un producto --</option>
                            <?php foreach ($productos as $producto): 
                                $imagenPath = dirname(__DIR__, 2) . '/imagen/' . $producto['imagen'];
                                $imagenURL = '../../imagen/' . $producto['imagen'];
                            ?>
                                <option value="<?php echo $producto['id']; ?>" 
                                        data-stock="<?php echo $producto['stock']; ?>"
                                        data-precio="<?php echo $producto['precio']; ?>"
                                        data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                    <?php echo htmlspecialchars($producto['nombre']) . " - Stock: " . $producto['stock'] . " - $" . $producto['precio']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cantidad" class="form-label">Cantidad *</label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" 
                                   min="1" value="1" required onchange="calcularTotal()">
                            <small class="text-muted" id="stockInfo">Stock disponible: --</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total a Pagar</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" id="total" readonly 
                                       value="0.00" style="font-weight: bold; color: #198754;">
                                <input type="hidden" name="total" id="total_hidden" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning d-none" id="stockAlert">
                        <i class="bi bi-exclamation-triangle"></i> La cantidad supera el stock disponible
                    </div>
                    
                    <button type="submit" name="comprar" class="btn btn-success btn-lg" id="btnComprar">
                        <i class="bi bi-check-circle"></i> Confirmar Compra
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Resumen del Producto -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-info-circle"></i> Información del Producto
            </div>
            <div class="card-body">
                <div id="productoInfo" class="text-center">
                    <i class="bi bi-box-seam display-1 text-muted"></i>
                    <p class="text-muted mt-2">Selecciona un producto para ver su información</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Productos Disponibles -->
<div class="card mt-4">
    <div class="card-header bg-secondary text-white">
        <i class="bi bi-box-seam"></i> Productos Disponibles
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Stock</th>
                        <th>Precio</th>
                        <th>Proveedor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): 
                        $imagenPath = dirname(__DIR__, 2) . '/imagen/' . $producto['imagen'];
                        $imagenURL = '../../imagen/' . $producto['imagen'];
                        
                        // Obtener nombre del proveedor
                        $stmtProv = $conn->prepare("SELECT nombre FROM proveedores WHERE id = :id");
                        $stmtProv->bindValue(":id", $producto['proveedor_id'], PDO::PARAM_INT);
                        $stmtProv->execute();
                        $proveedor = $stmtProv->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <tr>
                        <td>
                            <?php if (!empty($producto['imagen']) && file_exists($imagenPath)): ?>
                                <img src="<?php echo $imagenURL; ?>" alt="Imagen" width="50" height="50" class="rounded">
                            <?php else: ?>
                                <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($producto['nombre']); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars(substr($producto['descripcion'], 0, 50)) . '...'; ?></td>
                        <td>
                            <span class="badge bg-<?php echo ($producto['stock'] > 10 ? 'success' : ($producto['stock'] > 0 ? 'warning' : 'danger')); ?>">
                                <?php echo $producto['stock']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold text-success">$<?php echo number_format($producto['precio'], 2); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($proveedor['nombre'] ?? 'N/A'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function actualizarInfoProducto() {
    const select = document.getElementById('producto_id');
    const selectedOption = select.options[select.selectedIndex];
    const stock = selectedOption.getAttribute('data-stock') || 0;
    const precio = selectedOption.getAttribute('data-precio') || 0;
    const nombre = selectedOption.getAttribute('data-nombre') || '';
    
    // Actualizar información de stock
    document.getElementById('stockInfo').textContent = 'Stock disponible: ' + stock;
    
    // Actualizar cantidad máxima
    const cantidadInput = document.getElementById('cantidad');
    cantidadInput.max = stock;
    
    // Si la cantidad actual supera el nuevo stock, ajustarla
    if (parseInt(cantidadInput.value) > stock) {
        cantidadInput.value = stock;
    }
    
    // Actualizar información del producto
    const productoInfo = document.getElementById('productoInfo');
    productoInfo.innerHTML = `
        <h5>${nombre}</h5>
        <p class="text-muted">Precio: <strong>$${parseFloat(precio).toFixed(2)}</strong></p>
        <p class="text-muted">Stock: <strong>${stock}</strong></p>
    `;
    
    // Calcular total inicial
    calcularTotal();
}

function calcularTotal() {
    const select = document.getElementById('producto_id');
    const selectedOption = select.options[select.selectedIndex];
    const precio = parseFloat(selectedOption.getAttribute('data-precio') || 0);
    const cantidad = parseInt(document.getElementById('cantidad').value || 0);
    const stock = parseInt(selectedOption.getAttribute('data-stock') || 0);
    
    // Validar stock
    const stockAlert = document.getElementById('stockAlert');
    const btnComprar = document.getElementById('btnComprar');
    
    if (cantidad > stock) {
        stockAlert.classList.remove('d-none');
        btnComprar.disabled = true;
        btnComprar.classList.add('disabled');
    } else {
        stockAlert.classList.add('d-none');
        btnComprar.disabled = false;
        btnComprar.classList.remove('disabled');
    }
    
    // Calcular total
    const total = precio * cantidad;
    document.getElementById('total').value = total.toFixed(2);
    document.getElementById('total_hidden').value = total.toFixed(2);
}

// Inicializar eventos
document.addEventListener('DOMContentLoaded', function() {
    // Validar formulario antes de enviar
    document.getElementById('formCompra').addEventListener('submit', function(e) {
        const cliente = document.getElementById('cliente_id').value;
        const producto = document.getElementById('producto_id').value;
        const cantidad = document.getElementById('cantidad').value;
        
        if (!cliente || !producto || !cantidad) {
            e.preventDefault();
            alert('Por favor, completa todos los campos requeridos.');
            return;
        }
        
        if (parseInt(cantidad) < 1) {
            e.preventDefault();
            alert('La cantidad debe ser al menos 1.');
            return;
        }
        
        if (parseInt(cantidad) > parseInt(document.getElementById('cantidad').max)) {
            e.preventDefault();
            alert('No hay suficiente stock disponible.');
            return;
        }
    });
});
</script>

<?php
// PROCESAR LA COMPRA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comprar'])) {
    $cliente_id = $_POST['cliente_id'];
    $producto_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];
    $total = $_POST['total'];
    
    try {
        // 1. Verificar stock actual
        $stmtStock = $conn->prepare("SELECT stock, precio, nombre FROM productos WHERE id = :id");
        $stmtStock->bindParam(":id", $producto_id);
        $stmtStock->execute();
        $producto = $stmtStock->fetch(PDO::FETCH_ASSOC);
        
        if (!$producto || $producto['stock'] < $cantidad) {
            echo '<div class="alert alert-danger mt-3">
                    Error: Stock insuficiente. Stock disponible: ' . ($producto['stock'] ?? 0) . '
                  </div>';
        } else {
            // 2. Obtener información del cliente
            $stmtCliente = $conn->prepare("SELECT nombre FROM clientes WHERE id = :id");
            $stmtCliente->bindParam(":id", $cliente_id);
            $stmtCliente->execute();
            $cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);
            
            // Calcular precio unitario y total
            $precio_unitario = $producto['precio'];
            $total_calculado = $precio_unitario * $cantidad;
            
            // 3. Insertar en tabla ventas
            $stmtVenta = $conn->prepare("
                INSERT INTO ventas (cliente_id, producto_id, cantidad, precio_unitario, total) 
                VALUES (:cliente_id, :producto_id, :cantidad, :precio_unitario, :total)
            ");
            $stmtVenta->bindParam(":cliente_id", $cliente_id);
            $stmtVenta->bindParam(":producto_id", $producto_id);
            $stmtVenta->bindParam(":cantidad", $cantidad);
            $stmtVenta->bindParam(":precio_unitario", $precio_unitario);
            $stmtVenta->bindParam(":total", $total_calculado);
            
            if ($stmtVenta->execute()) {
                // 4. Actualizar stock del producto
                $stmtUpdate = $conn->prepare("UPDATE productos SET stock = stock - :cantidad WHERE id = :id");
                $stmtUpdate->bindParam(":cantidad", $cantidad);
                $stmtUpdate->bindParam(":id", $producto_id);
                $stmtUpdate->execute();
                
                // Obtener ID de la venta insertada
                $venta_id = $conn->lastInsertId();
                
                echo '<div class="alert alert-success mt-3">
                        <h5><i class="bi bi-check-circle"></i> Compra realizada exitosamente!</h5>
                        <div class="card mt-3">
                            <div class="card-body">
                                <h6>Detalles de la Compra</h6>
                                <hr>
                                <p><strong>ID Venta:</strong> ' . $venta_id . '</p>
                                <p><strong>Cliente:</strong> ' . htmlspecialchars($cliente['nombre']) . '</p>
                                <p><strong>Producto:</strong> ' . htmlspecialchars($producto['nombre']) . '</p>
                                <p><strong>Cantidad:</strong> ' . $cantidad . '</p>
                                <p><strong>Precio Unitario:</strong> $' . number_format($precio_unitario, 2) . '</p>
                                <p><strong>Total Pagado:</strong> <span class="fw-bold text-success">$' . number_format($total_calculado, 2) . '</span></p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="../ventas/readventas.php" class="btn btn-info">
                                <i class="bi bi-receipt"></i> Ver en Historial de Ventas
                            </a>
                            <a href="readcompras.php" class="btn btn-secondary">
                                <i class="bi bi-cart-plus"></i> Realizar Otra Compra
                            </a>
                        </div>
                      </div>';
            } else {
                echo '<div class="alert alert-danger mt-3">
                        Error al registrar la venta en la base de datos.
                      </div>';
            }
        }
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger mt-3">
                Error al procesar la compra: ' . $e->getMessage() . '
              </div>';
    }
}

include "../../footer.php";
?>