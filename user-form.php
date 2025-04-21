<?php
// Include database and required files
include_once 'config/database.php';
include_once 'models/User.php';
include_once 'models/Role.php';
include_once 'utils/session.php';

// Require login and admin
requireLogin();
requireAdmin();

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize user and role objects
$user = new User($db);
$role = new Role($db);

// Set page title and action
$page_title = "Nuevo Usuario";
$action = "create";

// Check if editing
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $user->id_usuario = $_GET['id'];
    
    // Check if user exists
    if($user->readOne()) {
        $page_title = "Editar Usuario";
        $action = "update";
    } else {
        // Redirect if user not found
        header("Location: users.php");
        exit();
    }
}

// Process form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set user properties
    $user->nombre_usuario = $_POST['nombre_usuario'];
    $user->correo_usuario = $_POST['correo_usuario'];
    $user->id_rol = $_POST['id_rol'];
    
    // Create or update user
    if($action === "create") {
        $user->contrasena = $_POST['contrasena'];
        if($user->create()) {
            header("Location: users.php");
            exit();
        }
    } else {
        if($user->update()) {
            // Update password if provided
            if(!empty($_POST['contrasena'])) {
                $user->contrasena = $_POST['contrasena'];
                $user->updatePassword();
            }
            header("Location: users.php");
            exit();
        }
    }
}

// Get all roles
$roles_stmt = $role->read();

// Include header
include 'includes/layout_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><?php echo $page_title; ?></h2>
    </div>
    
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . ($action === "update" ? "?id=" . $user->id_usuario : "")); ?>">
        <div class="p-4">
            <div class="form-group">
                <label for="nombre_usuario" class="form-label">Nombre</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" required 
                       value="<?php echo $action === "update" ? $user->nombre_usuario : ''; ?>"
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="correo_usuario" class="form-label">Correo Electrónico</label>
                <input type="email" id="correo_usuario" name="correo_usuario" required 
                       value="<?php echo $action === "update" ? $user->correo_usuario : ''; ?>"
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="contrasena" class="form-label">
                    <?php echo $action === "create" ? "Contraseña" : "Contraseña (Opcional)"; ?>
                </label>
                <input type="password" id="contrasena" name="contrasena" 
                       <?php echo $action === "create" ? "required" : ""; ?>
                       class="form-control">
                <?php if($action === "update"): ?>
                <p class="text-xs text-gray-500 mt-1">Dejar en blanco para mantener la contraseña actual.</p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="id_rol" class="form-label">Rol</label>
                <select id="id_rol" name="id_rol" required class="form-select">
                    <option value="">Seleccionar Rol</option>
                    <?php 
                    while($row = $roles_stmt->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);
                        $selected = ($action === "update" && $user->id_rol == $id_rol) ? "selected" : "";
                        echo "<option value='{$id_rol}' {$selected}>{$nombre_rol}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="flex justify-between mt-6">
                <a href="users.php" class="btn btn-secondary">
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
