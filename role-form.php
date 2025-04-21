<?php
// Include database and required files
include_once 'config/database.php';
include_once 'models/Role.php';
include_once 'utils/session.php';

// Require login and admin
requireLogin();
requireAdmin();

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize role object
$role = new Role($db);

// Set page title and action
$page_title = "Nuevo Rol";
$action = "create";

// Check if editing
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $role->id_rol = $_GET['id'];
    
    // Check if role exists
    if($role->readOne()) {
        $page_title = "Editar Rol";
        $action = "update";
    } else {
        // Redirect if role not found
        header("Location: roles.php");
        exit();
    }
}

// Process form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set role properties
    $role->nombre_rol = $_POST['nombre_rol'];
    $role->descripcion_rol = $_POST['descripcion_rol'];
    
    // Create or update role
    if($action === "create") {
        if($role->create()) {
            header("Location: roles.php");
            exit();
        }
    } else {
        if($role->update()) {
            header("Location: roles.php");
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
    
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . ($action === "update" ? "?id=" . $role->id_rol : "")); ?>">
        <div class="p-4">
            <div class="form-group">
                <label for="nombre_rol" class="form-label">Nombre del Rol</label>
                <input type="text" id="nombre_rol" name="nombre_rol" required 
                       value="<?php echo $action === "update" ? $role->nombre_rol : ''; ?>"
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="descripcion_rol" class="form-label">Descripción</label>
                <textarea id="descripcion_rol" name="descripcion_rol" rows="4" 
                          class="form-control"><?php echo $action === "update" ? $role->descripcion_rol : ''; ?></textarea>
            </div>
            
            <div class="flex justify-between mt-6">
                <a href="roles.php" class="btn btn-secondary">
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
