<?php
// Include database and required files
include_once '../config/database.php';
include_once '../utils/session.php';
include_once '../config/mail_config.php';

// Require login and admin
requireLogin();
requireAdmin();

// Set page title
$page_title = "Configuración de Correo";

// Process form submission
$message = '';
$success = false;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Actualizar el archivo de configuración
    $config_file = '../config/mail_config.php';
    
    // Procesar el checkbox
    $birthday_reminder_same_day = isset($_POST['birthday_reminder_same_day']) ? 'true' : 'false';
    
    $config_content = '<?php
// Configuración de correo electrónico
class MailConfig {
    // Remitente del correo
    public static $from_email = "' . $_POST['from_email'] . '";
    public static $from_name = "' . $_POST['from_name'] . '";
    
    // Destinatarios de las notificaciones (administradores)
    public static $admin_emails = [
        "' . str_replace(',', '", "', $_POST['admin_emails']) . '"
    ];
    
    // Configuración de recordatorios
    public static $birthday_reminder_same_day = ' . $birthday_reminder_same_day . '; // Recordar cumpleaños el mismo día
    public static $payment_reminder_before = ' . $_POST['payment_reminder_before'] . '; // Días antes para recordar pagos
    public static $payment_reminder_after = ' . $_POST['payment_reminder_after'] . '; // Días después para recordar pagos pendientes
    
    // Configuración SMTP (si es necesario)
    public static $smtp_host = "' . $_POST['smtp_host'] . '";
    public static $smtp_port = ' . $_POST['smtp_port'] . ';
    public static $smtp_username = "' . $_POST['smtp_username'] . '";
    public static $smtp_password = "' . $_POST['smtp_password'] . '";
    public static $smtp_secure = "' . $_POST['smtp_secure'] . '"; // tls o ssl
}
?>';

    // Guardar el archivo
    if (file_put_contents($config_file, $config_content)) {
        $message = 'Configuración de correo actualizada correctamente.';
        $success = true;
    } else {
        $message = 'Error al guardar la configuración. Verifique los permisos del archivo.';
    }
}

// Include header
include '../includes/layout_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Configuración de Correo Electrónico</h2>
    </div>
    
    <?php if(!empty($message)): ?>
    <div class="alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?> m-4">
        <?php echo $message; ?>
    </div>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="p-4">
            <h3 class="text-lg font-semibold mb-4">Configuración del Remitente</h3>
            
            <div class="form-group">
                <label for="from_email" class="form-label">Correo del Remitente</label>
                <input type="email" id="from_email" name="from_email" required 
                       value="<?php echo MailConfig::$from_email; ?>"
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="from_name" class="form-label">Nombre del Remitente</label>
                <input type="text" id="from_name" name="from_name" required 
                       value="<?php echo MailConfig::$from_name; ?>"
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="admin_emails" class="form-label">Correos de Administradores (separados por comas)</label>
                <input type="text" id="admin_emails" name="admin_emails" required 
                       value="<?php echo implode(',', MailConfig::$admin_emails); ?>"
                       class="form-control">
            </div>
            
            <h3 class="text-lg font-semibold mb-4 mt-6">Configuración de Recordatorios</h3>
            
            <div class="form-group">
                <label class="form-label">Recordatorios de Cumpleaños</label>
                <div class="mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="birthday_reminder_same_day" <?php echo MailConfig::$birthday_reminder_same_day ? 'checked' : ''; ?> class="form-checkbox">
                        <span class="ml-2">Enviar recordatorio el mismo día del cumpleaños</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1">Los recordatorios se enviarán a las 8:00 AM (hora de Venezuela) el día del cumpleaños.</p>
                </div>
            </div>
            
            <div class="form-group">
                <label for="payment_reminder_before" class="form-label">Días antes para recordatorio de pago</label>
                <input type="number" id="payment_reminder_before" name="payment_reminder_before" required 
                       value="<?php echo MailConfig::$payment_reminder_before; ?>"
                       min="1" max="7" class="form-control">
                <p class="text-xs text-gray-500 mt-1">Recomendado: 1 día antes</p>
            </div>
            
            <div class="form-group">
                <label for="payment_reminder_after" class="form-label">Días después para recordatorio de pago vencido</label>
                <input type="number" id="payment_reminder_after" name="payment_reminder_after" required 
                       value="<?php echo MailConfig::$payment_reminder_after; ?>"
                       min="1" max="7" class="form-control">
                <p class="text-xs text-gray-500 mt-1">Recomendado: 1 día después</p>
            </div>
            
            <h3 class="text-lg font-semibold mb-4 mt-6">Configuración SMTP (opcional)</h3>
            
            <div class="form-group">
                <label for="smtp_host" class="form-label">Servidor SMTP</label>
                <input type="text" id="smtp_host" name="smtp_host" 
                       value="<?php echo MailConfig::$smtp_host; ?>"
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="smtp_port" class="form-label">Puerto SMTP</label>
                <input type="number" id="smtp_port" name="smtp_port" 
                       value="<?php echo MailConfig::$smtp_port; ?>"
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="smtp_username" class="form-label">Usuario SMTP</label>
                <input type="text" id="smtp_username" name="smtp_username" 
                       value="<?php echo MailConfig::$smtp_username; ?>"
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="smtp_password" class="form-label">Contraseña SMTP</label>
                <input type="password" id="smtp_password" name="smtp_password" 
                       value="<?php echo MailConfig::$smtp_password; ?>"
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="smtp_secure" class="form-label">Seguridad SMTP</label>
                <select id="smtp_secure" name="smtp_secure" class="form-select">
                    <option value="tls" <?php echo MailConfig::$smtp_secure === 'tls' ? 'selected' : ''; ?>>TLS</option>
                    <option value="ssl" <?php echo MailConfig::$smtp_secure === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                    <option value="" <?php echo MailConfig::$smtp_secure === '' ? 'selected' : ''; ?>>Ninguna</option>
                </select>
            </div>
            
            <div class="flex justify-between mt-6">
                <a href="../index.php" class="btn btn-secondary">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    Guardar Configuración
                </button>
            </div>
        </div>
    </form>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h2 class="card-title">Configuración del Cron Job</h2>
    </div>
    
    <div class="p-4">
        <p class="mb-4">Para que los recordatorios se envíen automáticamente, debe configurar un cron job en su servidor que ejecute el script de recordatorios diariamente.</p>
        
        <div class="bg-gray-50 p-4 rounded">
            <h4 class="font-semibold mb-2">Ejemplo de cron job (ejecutar diariamente a las 8:00 AM hora Venezuela):</h4>
            <pre class="bg-gray-100 p-2 rounded">0 8 * * * TZ=America/Caracas php <?php echo realpath('../scripts/send_reminders.php'); ?></pre>
            
            <p class="mt-4">Para configurar el cron job:</p>
            <ol class="list-decimal ml-6 mt-2">
                <li>Acceda a su servidor mediante SSH</li>
                <li>Ejecute <code>crontab -e</code> para editar el cron job</li>
                <li>Agregue la línea anterior al final del archivo</li>
                <li>Guarde y cierre el editor</li>
            </ol>
            <p class="mt-2 text-sm text-gray-600">Nota: El parámetro <code>TZ=America/Caracas</code> asegura que el script se ejecute con la zona horaria de Venezuela.</p>
        </div>
        
        <div class="mt-4">
            <p class="font-semibold">También puede ejecutar el script manualmente para probar:</p>
            <a href="../scripts/send_reminders.php" target="_blank" class="btn btn-primary mt-2">
                Ejecutar Script de Recordatorios
            </a>
        </div>
    </div>
</div>

<?php
// Include footer
include '../includes/layout_footer.php';
?>
