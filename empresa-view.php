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

// Check if ID is set
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: empresas.php");
    exit();
}

// Set empresa ID
$empresa->id_empresa = $_GET['id'];

// Check if empresa exists
if(!$empresa->readOne()) {
    header("Location: empresas.php");
    exit();
}

// Get clients for this empresa
$clients = $empresa->getClients();

// Set page title
$page_title = "Detalles de Empresa";

// Include header
include 'includes/layout_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Detalles de Empresa</h2>
        <div>
            <a href="empresa-form.php?id=<?php echo $empresa->id_empresa; ?>" class="btn btn-warning">
                <i class="fas fa-edit mr-2"></i> Editar
            </a>
        </div>
    </div>
    
    <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-semibold mb-4">Información General</h3>
                <div class="bg-gray-50 p-4 rounded">
                    <p class="mb-2"><span class="font-bold">Nombre:</span> <?php echo $empresa->nombre_empresa; ?></p>
                    <p><span class="font-bold">Rubro:</span> <?php echo $empresa->rubro; ?></p>
                </div>
            </div>
        </div>
        
        <h3 class="text-lg font-semibold mb-4">Clientes Asociados</h3>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if($clients->rowCount() > 0) {
                    while($row = $clients->fetch(PDO::FETCH_ASSOC)) {
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
                        </div>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                ?>
                <tr>
                    <td colspan="2" class="text-center">No hay clientes asociados a esta empresa.</td>
                </tr>
                <?php 
                }
                ?>
            </tbody>
        </table>
        
        <div class="flex justify-between mt-6">
            <a href="empresas.php" class="btn btn-secondary">
                Volver
            </a>
        </div>
    </div>
</div>

<?php
// Include footer
include 'includes/layout_footer.php';
?>
