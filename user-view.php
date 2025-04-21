<?php
// Include database and required files
include_once 'config/database.php';
include_once 'models/User.php';
include_once 'models/Relation.php';
include_once 'models/Client.php';
include_once 'utils/session.php';

// Require login and admin
requireLogin();
requireAdmin();

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize objects
$user = new User($db);
$relation = new Relation($db);
$client = new Client($db);

// Check if ID is set
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: users.php");
    exit();
}

// Set user ID
$user->id_usuario = $_GET['id'];
$relation->id_usuario = $_GET['id'];

// Check if user exists
if(!$user->readOne()) {
    header("Location: users.php");
    exit();
}

// Process client assignment
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_client'])) {
    $relation->id_cliente = $_POST['client_id'];
    
    // Check if relation already exists
    if(!$relation->exists()) {
        if($relation->create()) {
            header("Location: user-view.php?id=" . $user->id_usuario);
            exit();
        }
    }
}

// Get assigned clients
$assigned_clients = $relation->readByUser();

// Get all clients for dropdown
$all_clients = $client->read(getCurrentUserId(), true);

// Set page title
$page_title = "Detalles del Usuario";

// Include header
include 'includes/layout_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Detalles del Usuario</h2>
        <a href="user-form.php?id=<?php echo $user->id_usuario; ?>" class="btn btn-warning">
            <i class="fas fa-edit mr-2"></i> Editar
        </a>
    </div>
    
    <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-semibold mb-4">Información General</h3>
                <div class="bg-gray-50 p-4 rounded">
                    <p class="mb-2"><span class="font-bold">Nombre:</span> <?php echo $user->nombre_usuario; ?></p>
                    <p class="mb-2"><span class="font-bold">Correo:</span> <?php echo $user->correo_usuario; ?></p>
                    <p><span class="font-bold">Rol:</span> <?php echo $user->nombre_rol; ?></p>
                </div>
            </div>
        </div>
        
        <h3 class="text-lg font-semibold mb-4">Clientes Asignados</h3>
        
        <!-- Assign Client Form -->
        <div class="bg-gray-50 p-4 rounded mb-4">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $user->id_usuario); ?>" class="flex items-end gap-4">
                <input type="hidden" name="assign_client" value="1">
                
                <div class="flex-1">
                    <label for="client_id" class="form-label">Asignar Cliente</label>
                    <select id="client_id" name="client_id" required class="form-select">
                        <option value="">Seleccionar Cliente</option>
                        <?php 
                        while($row = $all_clients->fetch(PDO::FETCH_ASSOC)) {
                            extract($row);
                            echo "<option value='{$id_cliente}'>{$nombre_cliente}</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    Asignar
                </button>
            </form>
        </div>
        
        <!-- Assigned Clients List -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if($assigned_clients->rowCount() > 0) {
                    while($row = $assigned_clients->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);
                ?>
                <tr>
                    <td class="user-cell">
                        <div class="avatar" style="background-color: #d1fae5; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user" style="color: #23D950;"></i>
                        </div>
                        <?php echo $nombre_cliente; ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="client-view.php?id=<?php echo $id_cliente; ?>" class="btn btn-icon btn-secondary" title="Ver">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="relation-delete.php?id=<?php echo $id_relacion; ?>&user_id=<?php echo $user->id_usuario; ?>" class="btn btn-icon btn-danger" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta asignación?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                ?>
                <tr>
                    <td colspan="2" class="text-center">No hay clientes asignados.</td>
                </tr>
                <?php 
                }
                ?>
            </tbody>
        </table>
        
        <div class="flex justify-between mt-6">
            <a href="users.php" class="btn btn-secondary">
                Volver
            </a>
        </div>
    </div>
</div>

<?php
// Include footer
include 'includes/layout_footer.php';
?>
