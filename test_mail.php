<?php
include_once 'config/database.php';
include_once 'utils/mail_functions.php';
include_once 'models/Client.php';
include_once 'utils/session.php';

requireLogin();
requireAdmin();

$database = new Database();
$db = $database->getConnection();

$page_title = "Prueba de Correo";

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['test_type'])) {
        $test_type = $_POST['test_type'];
        
        $test_client = [
            'id_cliente' => 1,
            'nombre_cliente' => 'Cliente de Prueba',
            'fecha_inicio' => date('Y-m-d'),
            'cumpleaños' => date('Y-m-d'),
            'fecha_pago' => date('Y-m-d'),
            'nombre_plan' => 'Plan de Prueba',
            'precio' => 199.99,
            'nombre_empresa' => 'Empresa de Prueba',
            'rubro_empresa' => 'Tecnología'
        ];
        
        $test_users = [
            [
                'id_usuario' => 1,
                'nombre_usuario' => 'Administrador',
                'correo_usuario' => $_POST['test_email']
            ]
        ];
        
        if ($test_type === 'birthday') {
            if (sendBirthdayReminder($test_client, $test_users)) {
                $message = 'Correo de prueba de cumpleaños enviado correctamente a ' . $_POST['test_email'];
                $success = true;
            } else {
                $message = 'Error al enviar el correo de prueba de cumpleaños.';
            }
        } else if ($test_type === 'payment_before') {
            $payment_date = date('d/m/Y', strtotime('+1 day'));
            if (sendPaymentReminderBefore($test_client, $test_users, $payment_date)) {
                $message = 'Correo de prueba de pago (previo) enviado correctamente a ' . $_POST['test_email'];
                $success = true;
            } else {
                $message = 'Error al enviar el correo de prueba de pago (previo).';
            }
        } else if ($test_type === 'payment_after') {
            $payment_date = date('d/m/Y', strtotime('-1 day'));
            if (sendPaymentReminderAfter($test_client, $test_users, $payment_date)) {
                $message = 'Correo de prueba de pago vencido enviado correctamente a ' . $_POST['test_email'];
                $success = true;
            } else {
                $message = 'Error al enviar el correo de prueba de pago vencido.';
            }
        }
    }
}

include 'includes/layout_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Prueba de Envío de Correos</h2>
    </div>
    
    <?php if(!empty($message)): ?>
    <div class="alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?> m-4">
        <?php echo $message; ?>
    </div>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="p-4">
            <div class="form-group">
                <label for="test_email" class="form-label">Correo para Prueba</label>
                <input type="email" id="test_email" name="test_email" required 
                       class="form-control" placeholder="Ingrese su correo para recibir la prueba">
            </div>
            
            <div class="form-group">
                <label class="form-label">Tipo de Correo</label>
                <div class="mt-2">
                    <label class="inline-flex items-center">
                        <input type="radio" name="test_type" value="birthday" checked class="form-radio">
                        <span class="ml-2">Recordatorio de Cumpleaños (mismo día)</span>
                    </label>
                    <label class="inline-flex items-center ml-6">
                        <input type="radio" name="test_type" value="payment_before" class="form-radio">
                        <span class="ml-2">Recordatorio de Pago (1 día antes)</span>
                    </label>
                    <label class="inline-flex items-center ml-6">
                        <input type="radio" name="test_type" value="payment_after" class="form-radio">
                        <span class="ml-2">Recordatorio de Pago Vencido (1 día después)</span>
                    </label>
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="btn btn-primary">
                    Enviar Correo de Prueba
                </button>
            </div>
        </div>
    </form>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h2 class="card-title">Información</h2>
    </div>
    
    <div class="p-4">
        <p>Esta página le permite probar el sistema de envío de correos electrónicos para asegurarse de que los recordatorios funcionan correctamente.</p>
        
        <h3 class="text-lg font-semibold mt-4 mb-2">Recordatorios Automáticos</h3>
        <p>El sistema está configurado para enviar automáticamente:</p>
        <ul class="list-disc ml-6 mt-2">
            <li>Recordatorios de cumpleaños de clientes (días configurables de anticipación)</li>
            <li>Recordatorios de pagos mensuales (días configurables de anticipación)</li>
        </ul>
        
        <h3 class="text-lg font-semibold mt-4 mb-2">Configuración</h3>
        <p>Puede ajustar la configuración de correo electrónico en la sección <a href="admin/email_settings.php" class="text-blue-600 hover:underline">Configuración de Correo</a>.</p>
    </div>
</div>

<?php
include 'includes/layout_footer.php';
?>
