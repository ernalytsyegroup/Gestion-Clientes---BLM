<?php
// Include database and required files
include_once 'config/database.php';
include_once 'models/User.php';
include_once 'utils/session.php';

// Require login and admin
requireLogin();
requireAdmin();

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize user object
$user = new User($db);

// Set page title
$page_title = "Usuarios";

// Get all users
$stmt = $user->read();

// Include header
include 'includes/layout_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Lista de Usuarios</h2>
        <a href="user-form.php" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i> Nuevo Usuario
        </a>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if($stmt->rowCount() > 0) {
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
            ?>
            <tr>
                <td class="user-cell">
                    <div class="avatar" style="background-color: #d1fae5; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user" style="color: #23D950;"></i>
                    </div>
                    <?php echo $nombre_usuario; ?>
                </td>
                <td><?php echo $correo_usuario; ?></td>
                <td><?php echo $nombre_rol; ?></td>
                <td>
                    <div class="action-buttons">
                        <a href="user-view.php?id=<?php echo $id_usuario; ?>" class="btn btn-icon btn-secondary" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="user-form.php?id=<?php echo $id_usuario; ?>" class="btn btn-icon btn-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="user-delete.php?id=<?php echo $id_usuario; ?>" class="btn btn-icon btn-danger" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este usuario?')">
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
                <td colspan="4" class="text-center">No hay usuarios disponibles.</td>
            </tr>
            <?php 
            }
            ?>
        </tbody>
    </table>
</div>

<?php
// Include footer
include 'includes/layout_footer.php';
?>
