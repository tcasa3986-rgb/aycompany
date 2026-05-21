<?php
class ConfiguracionController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        // Solo el administrador debe poder cambiar configuración general de la botica
        if ($_SESSION['rol_id'] != 1) {
            header('Location: ' . BASE_URL . 'dashboard/index');
            exit;
        }
    }

    public function index() {
        $configModel = $this->model('Configuracion');
        $configs = $configModel->getAll();
        
        $data = [
            'title' => 'Configuración de Empresa',
            'configs' => $configs
        ];
        
        $this->view('configuracion/index', $data);
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $configModel = $this->model('Configuracion');
            
            $updates = [
                'nombre_botica' => trim($_POST['nombre_botica']),
                'ruc' => trim($_POST['ruc']),
                'direccion' => trim($_POST['direccion']),
                'telefono' => trim($_POST['telefono']),
                'moneda' => trim($_POST['moneda']),
                'igv' => trim($_POST['igv'])
            ];
            
            // Upload Logo
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                if (in_array(strtolower($ext), $allowed)) {
                    $newFileName = 'logo_botica_' . time() . '.' . strtolower($ext);
                    $destPath = 'img/' . $newFileName;
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $destPath)) {
                        $updates['logo'] = BASE_URL . $destPath;
                    }
                }
            }
            
            if ($configModel->updateMultiples($updates)) {
                $_SESSION['mensaje'] = "Parámetros actualizados correctamente.";
            } else {
                $_SESSION['error'] = "Hubo un error al actualizar los datos de la botica en BD.";
            }
        }
        header('Location: ' . BASE_URL . 'configuracion/index');
    }
}
