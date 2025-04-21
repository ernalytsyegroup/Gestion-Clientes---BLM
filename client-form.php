<?php
// Include database and required files
include_once 'config/database.php';
include_once 'models/Client.php';
include_once 'models/Plan.php';
include_once 'models/Empresa.php';
include_once 'utils/session.php';

// Require login and admin
requireLogin();
requireAdmin(); // Solo administradores pueden acceder a este formulario

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize objects
$client = new Client($db);
$plan = new Plan($db);
$empresa = new Empresa($db);

// Set page title and action
$page_title = "Nuevo Cliente";
$action = "create";

// Check if editing
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $client->id_cliente = $_GET['id'];
    
    // Check if client exists and user has access
    if($client->readOne(getCurrentUserId(), isAdmin())) {
        $page_title = "Editar Cliente";
        $action = "update";
    } else {
        // Redirect if client not found or no access
        header("Location: clients.php");
        exit();
    }
}

// Process form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set client properties
    $client->nombre_cliente = $_POST['nombre_cliente'];
    $client->fecha_inicio = $_POST['fecha_inicio'];
    $client->cumpleaños = $_POST['cumpleaños'];
    $client->fecha_pago = $_POST['fecha_pago'];
    $client->id_plan = $_POST['id_plan'];
    $client->id_empresa = $_POST['id_empresa'];
    
    // Create or update client
    if($action === "create") {
        if($client->create()) {
            header("Location: clients.php");
            exit();
        }
    } else {
        if($client->update()) {
            header("Location: clients.php");
            exit();
        }
    }
}

// Get all plans
$planes_stmt = $plan->read();

// Get all empresas
$empresas_stmt = $empresa->read();

// Include header
include 'includes/layout_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><?php echo $page_title; ?></h2>
    </div>
    
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . ($action === "update" ? "?id=" . $client->id_cliente : "")); ?>">
        <div class="p-4">
            <div class="form-group">
                <label for="nombre_cliente" class="form-label">Nombre del Cliente</label>
                <input type="text" id="nombre_cliente" name="nombre_cliente" required 
                       value="<?php echo $action === "update" ? $client->nombre_cliente : ''; ?>"
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required 
                       value="<?php echo $action === "update" ? $client->fecha_inicio : ''; ?>"
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="cumpleaños" class="form-label">Cumpleaños</label>
                <input type="date" id="cumpleaños" name="cumpleaños" required 
                       value="<?php echo $action === "update" ? $client->cumpleaños : ''; ?>"
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="fecha_pago" class="form-label">Fecha de Pago</label>
                <input type="date" id="fecha_pago" name="fecha_pago" required 
                       value="<?php echo $action === "update" ? $client->fecha_pago : ''; ?>"
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="id_plan" class="form-label">Tipo de Plan</label>
                <select id="id_plan" name="id_plan" required class="form-select">
                    <option value="">Seleccionar Plan</option>
                    <?php 
                    while($row = $planes_stmt->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);
                        $selected = ($action === "update" && $client->id_plan == $id_plan) ? "selected" : "";
                        echo "<option value='{$id_plan}' {$selected}>{$nombre_plan} - $" . number_format($precio, 2) . "</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="id_empresa" class="form-label">Empresa</label>
                <select id="id_empresa" name="id_empresa" class="form-select">
                    <option value="">Seleccionar Empresa</option>
                    <?php 
                    while($row = $empresas_stmt->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);
                        $selected = ($action === "update" && $client->id_empresa == $id_empresa) ? "selected" : "";
                        echo "<option value='{$id_empresa}' {$selected}>{$nombre_empresa} - {$rubro}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="flex justify-between mt-6">
                <a href="clients.php" class="btn btn-secondary">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    Guardar
                </button>
            </div>
        </div>
    </form>
</div>

<?php
// Include footer
include 'includes/layout_footer.php';
?>
