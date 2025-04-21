<?php
// Include database and required files
include_once 'config/database.php';
include_once 'models/User.php';
include_once 'models/Client.php';
include_once 'utils/session.php';

// Require login
requireLogin();

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize client object
$client = new Client($db);

// Set page title
$page_title = "Clientes";

// Get clients for current user
$stmt = $client->read(getCurrentUserId(), isAdmin());

// Include header
include 'includes/layout_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Lista de Clientes</h2>
        <?php if(isAdmin()): ?>
        <a href="client-form.php" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i> Nuevo Cliente
        </a>
        <?php endif; ?>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Empresa</th>
                <th>Fecha Inicio</th>
                <th>Cumpleaños</th>
                <th>Fecha Pago</th>
                <th>Plan</th>
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
                    <?php echo $nombre_cliente; ?>
                </td>
                <td><?php echo $nombre_empresa ? $nombre_empresa : 'No asignada'; ?></td>
                <td><?php echo date('d/m/Y', strtotime($fecha_inicio)); ?></td>
                <td><?php echo date('d/m/Y', strtotime($cumpleaños)); ?></td>
                <td><?php echo date('d/m/Y', strtotime($fecha_pago)); ?></td>
                <td><?php echo $nombre_plan ? $nombre_plan : 'No asignado'; ?></td>
                <td>
                    <div class="action-buttons">
                        <a href="client-view.php?id=<?php echo $id_cliente; ?>" class="btn btn-icon btn-secondary" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <?php if(isAdmin()): ?>
                        <a href="client-form.php?id=<?php echo $id_cliente; ?>" class="btn btn-icon btn-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="client-delete.php?id=<?php echo $id_cliente; ?>" class="btn btn-icon btn-danger" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este cliente?')">
                            <i class="fas fa-trash"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php 
                }
            } else {
            ?>
            <tr>
                <td colspan="7" class="text-center">No hay clientes disponibles.</td>
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
