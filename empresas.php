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

// Set page title
$page_title = "Empresas";

// Get all empresas
$stmt = $empresa->read();

// Include header
include 'includes/layout_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Lista de Empresas</h2>
        <a href="empresa-form.php" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i> Nueva Empresa
        </a>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Empresa</th>
                <th>Rubro</th>
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
                        <i class="fas fa-building" style="color: #23D950;"></i>
                    </div>
                    <?php echo $nombre_empresa; ?>
                </td>
                <td><?php echo $rubro; ?></td>
                <td>
                    <div class="action-buttons">
                        <a href="empresa-view.php?id=<?php echo $id_empresa; ?>" class="btn btn-icon btn-secondary" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="empresa-form.php?id=<?php echo $id_empresa; ?>" class="btn btn-icon btn-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="empresa-delete.php?id=<?php echo $id_empresa; ?>" class="btn btn-icon btn-danger" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta empresa?')">
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
                <td colspan="3" class="text-center">No hay empresas disponibles.</td>
            </tr>
            <?php 
            }
            ?>
        </tbody>
    </table>
</div>

<?php

include 'includes/layout_footer.php';
?>
