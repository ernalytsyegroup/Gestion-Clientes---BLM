<?php
// Include database and required files
include_once 'config/database.php';
include_once 'models/Empresa.php';
include_once 'utils/session.php';

// Require login and admin
requireLogin();
requireAdmin();

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize empresa object
$empresa = new Empresa($db);

// Set page title and action
$page_title = "Nueva Empresa";
$action = "create";

// Check if editing
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $empresa->id_empresa = $_GET['id'];
    
    // Check if empresa exists
    if($empresa->readOne()) {
        $page_title = "Editar Empresa";
        $action = "update";
    } else {
        // Redirect if empresa not found
        header("Location: empresas.php");
        exit();
    }
}

// Process form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set empresa properties
    $empresa->nombre_empresa = $_POST['nombre_empresa'];
    $empresa->rubro = $_POST['rubro'];
    
    // Create or update empresa
    if($action === "create") {
        if($empresa->create()) {
            header("Location: empresas.php");
            exit();
        }
    } else {
        if($empresa->update()) {
            header("Location: empresas.php");
            exit();
        }
    }
}

// Include header
include 'includes/layout_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><?php echo $page_title; ?></h2>
    </div>
    
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . ($action === "update" ? "?id=" . $empresa->id_empresa : "")); ?>">
        <div class="p-4">
            <div class="form-group">
                <label for="nombre_empresa" class="form-label">Nombre de la Empresa</label>
                <input type="text" id="nombre_empresa" name="nombre_empresa" required 
                       value="<?php echo $action === "update" ? $empresa->nombre_empresa : ''; ?>"
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="rubro" class="form-label">Rubro</label>
                <input type="text" id="rubro" name="rubro" required 
                       value="<?php echo $action === "update" ? $empresa->rubro : ''; ?>"
                       class="form-control">
            </div>
            
            <div class="flex justify-between mt-6">
                <a href="empresas.php" class="btn btn-secondary">
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
