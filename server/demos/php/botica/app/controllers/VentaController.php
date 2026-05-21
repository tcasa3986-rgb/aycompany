<?php
class VentaController extends Controller {

    public function pos() {
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_URL . 'auth/login'); exit; }
        
        $cajaModel = $this->model('Caja');
        $cajaAbierta = $cajaModel->getCajaAbiertaPorUsuario($_SESSION['user_id']);
        if (!$cajaAbierta) {
            $_SESSION['error'] = "Debe aperturar su caja antes de poder realizar ventas.";
            header('Location: ' . BASE_URL . 'caja/apertura');
            exit;
        }
        
        $cliModel = $this->model('Cliente');
        $prodModel = $this->model('Producto');
        
        $configModel = $this->model('Configuracion');
        
        $data = [
            'title' => 'Punto de Venta',
            'clientes' => $cliModel->getAll(),
            'productos' => $prodModel->getAll(),
            'igv' => $configModel->get('igv')
        ];
        
        // Vista directa para el POS (usa layout de main)
        $this->view('ventas/pos', $data);
    }
    
    public function index() {
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_URL . 'auth/login'); exit; }
        $modelo = $this->model('Venta');
        $this->view('ventas/index', ['title' => 'Historial de Ventas', 'ventas' => $modelo->getAll()]);
    }

    public function ticket($id) {
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_URL . 'auth/login'); exit; }
        
        $modelo = $this->model('Venta');
        $ventas = $modelo->getAll();
        
        $venta_actual = null;
        foreach($ventas as $v) {
            if($v['id'] == $id) {
                $venta_actual = $v; break;
            }
        }
        
        if(!$venta_actual) die("Ticket no encontrado.");
        
        $detalles = $modelo->getDetalles($id);
        $configModel = $this->model('Configuracion');
        
        $data = [
            'venta' => $venta_actual,
            'detalles' => $detalles,
            'config' =>  $configModel->getAll()
        ];
        
        // Cargar vista HTML plana (sin layout)
        require_once '../app/views/ventas/ticket.php';
    }

    public function save() {
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_URL . 'auth/login'); exit; }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_cliente'])) {
            $modelo = $this->model('Venta');
            
            $cajaModel = $this->model('Caja');
            $cajaAbierta = $cajaModel->getCajaAbiertaPorUsuario($_SESSION['user_id']);
            if(!$cajaAbierta) {
                $_SESSION['error_pos'] = "Error: La caja no está abierta.";
                header('Location: ' . BASE_URL . 'venta/pos');
                exit;
            }
            
            // Generar un número ficticio correlativo de ticket
            $numero_t = str_pad(rand(1000, 99999), 6, '0', STR_PAD_LEFT);
            $total = (float)$_POST['total_venta'];
            $id_cliente = (int)$_POST['id_cliente'];
            
            $puntos_ganados = 0;
            if($id_cliente != 1) {
                // 1 Sol = 1 Punto (basado en el total final)
                $puntos_ganados = floor($total);
            }
            $puntos_usados = isset($_POST['puntos_usados']) ? (int)$_POST['puntos_usados'] : 0;
            $descuento = isset($_POST['descuento_venta']) ? (float)$_POST['descuento_venta'] : 0.00;
            
            $cabecera = [
                'caja_id' => $cajaAbierta['id'],
                'id_cliente' => $id_cliente,
                'tipo_comprobante' => $_POST['tipo_comprobante'],
                'serie_comprobante' => 'T001',
                'num_comprobante' => $numero_t,
                'subtotal' => (float)$_POST['subtotal_venta'],
                'descuento' => $descuento,
                'igv' => (float)$_POST['igv_venta'],
                'total' => $total,
                'metodo_pago' => $_POST['metodo_pago'],
                'pago_recibido' => (float)($_POST['pago_recibido'] ?: $total),
                'vuelto' => (float)($_POST['vuelto_venta'] ?: 0.00),
                'puntos_ganados' => $puntos_ganados,
                'puntos_usados' => $puntos_usados,
                'medico_cmp' => $_POST['medico_cmp'] ?? null
            ];
            
            // Detalles paralelos por arrays
            $detalles = [];
            $productos = $_POST['producto_id'] ?? [];
            foreach ($productos as $i => $id_prod) {
                if(empty($id_prod) || empty($_POST['cantidad'][$i])) continue;
                $detalles[] = [
                    'id_producto' => (int)$id_prod,
                    'cantidad' => (int)$_POST['cantidad'][$i],
                    'precio_unitario' => (float)$_POST['precio_d'][$i],
                    'subtotal' => (float)$_POST['subtotal_d'][$i],
                    'tipo_unidad' => $_POST['tipo_unidad'][$i] ?? 'CAJA'
                ];
            }
            
            if (count($detalles) > 0) {
                $id_venta = $modelo->registrarVenta($cabecera, $detalles, $_SESSION['user_id']);
                if ($id_venta) {
                    if($id_cliente != 1) {
                        $cliModel = $this->model('Cliente');
                        $delta = $puntos_ganados - $puntos_usados;
                        $cliModel->actualizarPuntos($id_cliente, $delta);
                    }
                    $_SESSION['mensaje_pos'] = "Venta Procesada Exitosamente. Ticket #T001-{$numero_t}";
                    $_SESSION['last_ticket'] = $id_venta;
                } else {
                    $_SESSION['error_pos'] = "Error de Servidor: Stock Insuficiente o Lote en conflicto FEFO.";
                }
            } else {
                $_SESSION['error_pos'] = "Carrito de compras vacío.";
            }
        }
        header('Location: ' . BASE_URL . 'venta/pos');
    }

    public function anular($id) {
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_URL . 'auth/login'); exit; }
        $modelo = $this->model('Venta');
        
        if($modelo->anularVenta($id, $_SESSION['user_id'])) {
            $this->logAccion('Ventas', 'ANULAR', "Anulación de venta ID #$id por el usuario.");
            $_SESSION['success'] = "Venta anulada correctamente. El stock ha sido devuelto al inventario.";
        } else {
            $_SESSION['error'] = "No se pudo anular la venta. Verifique que no esté ya anulada.";
        }
        header('Location: ' . BASE_URL . 'venta/index');
        exit;
    }
}
