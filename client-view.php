<?php
include_once 'config/database.php';
include_once 'models/Client.php';
include_once 'models/SocialNetwork.php';
include_once 'utils/session.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();

$client = new Client($db);
$social_network = new SocialNetwork($db);

if(!isset($_GET['id']) || empty($_GET['id'])) {
  header("Location: clients.php");
  exit();
}

$client->id_cliente = $_GET['id'];

if(!$client->readOne(getCurrentUserId(), isAdmin())) {
  header("Location: clients.php");
  exit();
}

$social_networks = $client->getSocialNetworks();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
  if(isset($_POST['action'])) {
      $action = $_POST['action'];
      
      if($action === 'create_instagram') {
          $usuario = $_POST['usuario_instagram'];
          $correo = $_POST['correo_instagram'];
          
          if($social_network->createInstagram($client->id_cliente, $usuario, $correo)) {
              header("Location: client-view.php?id=" . $client->id_cliente);
              exit();
          }
      }
      
      else if($action === 'create_facebook') {
          $usuario = $_POST['usuario_facebook'];
          $correo = $_POST['correo_facebook'];
          
          if($social_network->createFacebook($client->id_cliente, $usuario, $correo)) {
              header("Location: client-view.php?id=" . $client->id_cliente);
              exit();
          }
      }
      
      else if($action === 'create_youtube') {
          $usuario = $_POST['usuario_youtube'];
          $correo = $_POST['correo_youtube'];
          
          if($social_network->createYoutube($client->id_cliente, $usuario, $correo)) {
              header("Location: client-view.php?id=" . $client->id_cliente);
              exit();
          }
      }
      
      else if($action === 'update_instagram') {
          $id = $_POST['id_instagram'];
          $usuario = $_POST['usuario_instagram'];
          $correo = $_POST['correo_instagram'];
          
          if($social_network->updateInstagram($id, $usuario, $correo)) {
              header("Location: client-view.php?id=" . $client->id_cliente);
              exit();
          }
      }
      
      else if($action === 'update_facebook') {
          $id = $_POST['id_facebook'];
          $usuario = $_POST['usuario_facebook'];
          $correo = $_POST['correo_facebook'];
          
          if($social_network->updateFacebook($id, $usuario, $correo)) {
              header("Location: client-view.php?id=" . $client->id_cliente);
              exit();
          }
      }
      
      else if($action === 'update_youtube') {
          $id = $_POST['id_youtube'];
          $usuario = $_POST['usuario_youtube'];
          $correo = $_POST['correo_youtube'];
          
          if($social_network->updateYoutube($id, $usuario, $correo)) {
              header("Location: client-view.php?id=" . $client->id_cliente);
              exit();
          }
      }
  }
}

$page_title = "Detalles del Cliente";

include 'includes/layout_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Detalles del Cliente</h2>
        <?php if(isAdmin()): ?>
        <a href="client-form.php?id=<?php echo $client->id_cliente; ?>" class="btn btn-warning">
            <i class="fas fa-edit mr-2"></i> Editar
        </a>
        <?php endif; ?>
    </div>
    
    <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-semibold mb-4">Información General</h3>
                <div class="bg-gray-50 p-4 rounded">
                    <p class="mb-2"><span class="font-bold">Nombre:</span> <?php echo $client->nombre_cliente; ?></p>
                    <p class="mb-2"><span class="font-bold">Fecha de Inicio:</span> <?php echo date('d/m/Y', strtotime($client->fecha_inicio)); ?></p>
                    <p class="mb-2"><span class="font-bold">Cumpleaños:</span> <?php echo date('d/m/Y', strtotime($client->cumpleaños)); ?></p>
                    <p class="mb-2"><span class="font-bold">Fecha de Pago:</span> <?php echo date('d/m/Y', strtotime($client->fecha_pago)); ?></p>
                    <p class="mb-2"><span class="font-bold">Plan:</span> <?php echo $client->nombre_plan ? $client->nombre_plan : 'No asignado'; ?></p>
                    <p><span class="font-bold">Empresa:</span> <?php echo $client->nombre_empresa ? $client->nombre_empresa . ' (' . $client->rubro_empresa . ')' : 'No asignada'; ?></p>
                </div>
            </div>
        </div>
        
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Instagram</h3>
                <button onclick="toggleForm('instagram-form')" class="btn btn-primary">
                    <i class="fab fa-instagram mr-2"></i> Agregar Instagram
                </button>
            </div>
            
            <div id="instagram-form" class="hidden bg-gray-50 p-4 rounded mb-4">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $client->id_cliente); ?>">
                    <input type="hidden" name="action" value="create_instagram">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="usuario_instagram" class="form-label">Usuario o Correo</label>
                            <input type="text" id="usuario_instagram" name="usuario_instagram" required class="form-control">
                        </div>
                        <div>
                            <label for="correo_instagram" class="form-label">Contraseña</label>
                            <input type="text" id="correo_instagram" name="correo_instagram" required class="form-control">
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
            
            <?php if(!empty($social_networks['instagram'])): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Usuario o Correo</th>
                        <th>Contraseña</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($social_networks['instagram'] as $instagram): ?>
                    <tr>
                        <td><?php echo $instagram['usuario_instagram']; ?></td>
                        <td><?php echo $instagram['correo_instagram']; ?></td>
                        <td>
                            <div class="action-buttons">
                                <button onclick="editInstagram(<?php echo $instagram['id_instagram']; ?>, '<?php echo $instagram['usuario_instagram']; ?>', '<?php echo $instagram['correo_instagram']; ?>')" class="btn btn-icon btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="social-delete.php?type=instagram&id=<?php echo $instagram['id_instagram']; ?>&client_id=<?php echo $client->id_cliente; ?>" class="btn btn-icon btn-danger" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta cuenta?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="text-gray-500">No hay cuentas de Instagram registradas.</p>
            <?php endif; ?>
            
            <div id="instagram-edit-form" class="hidden bg-gray-50 p-4 rounded mt-4">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $client->id_cliente); ?>">
                    <input type="hidden" name="action" value="update_instagram">
                    <input type="hidden" id="edit_id_instagram" name="id_instagram">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="edit_usuario_instagram" class="form-label">Usuario o Correo</label>
                            <input type="text" id="edit_usuario_instagram" name="usuario_instagram" required class="form-control">
                        </div>
                        <div>
                            <label for="edit_correo_instagram" class="form-label">Contraseña</label>
                            <input type="text" id="edit_correo_instagram" name="correo_instagram" required class="form-control">
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="button" onclick="hideEditForm('instagram-edit-form')" class="btn btn-secondary mr-2">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Facebook</h3>
                <button onclick="toggleForm('facebook-form')" class="btn btn-primary">
                    <i class="fab fa-facebook mr-2"></i> Agregar Facebook
                </button>
            </div>
            
            <div id="facebook-form" class="hidden bg-gray-50 p-4 rounded mb-4">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $client->id_cliente); ?>">
                    <input type="hidden" name="action" value="create_facebook">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="usuario_facebook" class="form-label">Usuario o Correo</label>
                            <input type="text" id="usuario_facebook" name="usuario_facebook" required class="form-control">
                        </div>
                        <div>
                            <label for="correo_facebook" class="form-label">Contraseña</label>
                            <input type="text" id="correo_facebook" name="correo_facebook" required class="form-control">
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
            
            <?php if(!empty($social_networks['facebook'])): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Usuario o Correo</th>
                        <th>Contraseña</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($social_networks['facebook'] as $facebook): ?>
                    <tr>
                        <td><?php echo $facebook['usuario_facebook']; ?></td>
                        <td><?php echo $facebook['correo_facebook']; ?></td>
                        <td>
                            <div class="action-buttons">
                                <button onclick="editFacebook(<?php echo $facebook['id_facebook']; ?>, '<?php echo $facebook['usuario_facebook']; ?>', '<?php echo $facebook['correo_facebook']; ?>')" class="btn btn-icon btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="social-delete.php?type=facebook&id=<?php echo $facebook['id_facebook']; ?>&client_id=<?php echo $client->id_cliente; ?>" class="btn btn-icon btn-danger" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta cuenta?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="text-gray-500">No hay cuentas de Facebook registradas.</p>
            <?php endif; ?>
            
            <div id="facebook-edit-form" class="hidden bg-gray-50 p-4 rounded mt-4">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $client->id_cliente); ?>">
                    <input type="hidden" name="action" value="update_facebook">
                    <input type="hidden" id="edit_id_facebook" name="id_facebook">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="edit_usuario_facebook" class="form-label">Usuario o Correo</label>
                            <input type="text" id="edit_usuario_facebook" name="usuario_facebook" required class="form-control">
                        </div>
                        <div>
                            <label for="edit_correo_facebook" class="form-label">Contraseña</label>
                            <input type="text" id="edit_correo_facebook" name="correo_facebook" required class="form-control">
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="button" onclick="hideEditForm('facebook-edit-form')" class="btn btn-secondary mr-2">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">YouTube</h3>
                <button onclick="toggleForm('youtube-form')" class="btn btn-primary">
                    <i class="fab fa-youtube mr-2"></i> Agregar YouTube
                </button>
            </div>
            
            <div id="youtube-form" class="hidden bg-gray-50 p-4 rounded mb-4">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $client->id_cliente); ?>">
                    <input type="hidden" name="action" value="create_youtube">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="usuario_youtube" class="form-label">Usuario o Correo</label>
                            <input type="text" id="usuario_youtube" name="usuario_youtube" required class="form-control">
                        </div>
                        <div>
                            <label for="correo_youtube" class="form-label">Contraseña</label>
                            <input type="text" id="correo_youtube" name="correo_youtube" required class="form-control">
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
            
            <?php if(!empty($social_networks['youtube'])): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Usuario o Correo</th>
                        <th>Contraseña</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($social_networks['youtube'] as $youtube): ?>
                    <tr>
                        <td><?php echo $youtube['usuario_youtube']; ?></td>
                        <td><?php echo $youtube['correo_youtube']; ?></td>
                        <td>
                            <div class="action-buttons">
                                <button onclick="editYoutube(<?php echo $youtube['id_youtube']; ?>, '<?php echo $youtube['usuario_youtube']; ?>', '<?php echo $youtube['correo_youtube']; ?>')" class="btn btn-icon btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="social-delete.php?type=youtube&id=<?php echo $youtube['id_youtube']; ?>&client_id=<?php echo $client->id_cliente; ?>" class="btn btn-icon btn-danger" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta cuenta?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="text-gray-500">No hay cuentas de YouTube registradas.</p>
            <?php endif; ?>
            
            <div id="youtube-edit-form" class="hidden bg-gray-50 p-4 rounded mt-4">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $client->id_cliente); ?>">
                    <input type="hidden" name="action" value="update_youtube">
                    <input type="hidden" id="edit_id_youtube" name="id_youtube">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="edit_usuario_youtube" class="form-label">Usuario o Correo</label>
                            <input type="text" id="edit_usuario_youtube" name="usuario_youtube" required class="form-control">
                        </div>
                        <div>
                            <label for="edit_correo_youtube" class="form-label">Contraseña</label>
                            <input type="text" id="edit_correo_youtube" name="correo_youtube" required class="form-control">
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="button" onclick="hideEditForm('youtube-edit-form')" class="btn btn-secondary mr-2">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="flex justify-between mt-6">
            <a href="clients.php" class="btn btn-secondary">
                Volver
            </a>
        </div>
    </div>
</div>

<script>
    function toggleForm(formId) {
        const form = document.getElementById(formId);
        form.classList.toggle('hidden');
    }
    
    function hideEditForm(formId) {
        const form = document.getElementById(formId);
        form.classList.add('hidden');
    }
    
    function editInstagram(id, usuario, correo) {
        document.getElementById('edit_id_instagram').value = id;
        document.getElementById('edit_usuario_instagram').value = usuario;
        document.getElementById('edit_correo_instagram').value = correo;
        document.getElementById('instagram-edit-form').classList.remove('hidden');
    }
    
    function editFacebook(id, usuario, correo) {
        document.getElementById('edit_id_facebook').value = id;
        document.getElementById('edit_usuario_facebook').value = usuario;
        document.getElementById('edit_correo_facebook').value = correo;
        document.getElementById('facebook-edit-form').classList.remove('hidden');
    }
    
    function editYoutube(id, usuario, correo) {
        document.getElementById('edit_id_youtube').value = id;
        document.getElementById('edit_usuario_youtube').value = usuario;
        document.getElementById('edit_correo_youtube').value = correo;
        document.getElementById('youtube-edit-form').classList.remove('hidden');
    }
</script>

<?php

include 'includes/layout_footer.php';
?>
