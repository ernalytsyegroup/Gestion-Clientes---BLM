<?php
// Este script debe ejecutarse diariamente mediante un cron job
// Ejemplo de cron job: 0 8 * * * TZ=America/Caracas php /ruta/a/tu/proyecto/scripts/send_reminders.php

// Incluir archivos necesarios usando rutas absolutas
$root_path = realpath(dirname(__FILE__) . '/..');
include_once $root_path . '/config/database.php';
include_once $root_path . '/models/Client.php';
include_once $root_path . '/models/User.php';
include_once $root_path . '/utils/mail_functions.php';
include_once $root_path . '/config/mail_config.php';

// Inicializar conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Inicializar objetos
$client = new Client($db);
$user = new User($db);

// Función para obtener usuarios asignados a un cliente
function getAssignedUsers($db, $client_id) {
    $query = "SELECT u.id_usuario, u.nombre_usuario, u.correo_usuario 
              FROM usuarios u
              JOIN relaciones r ON u.id_usuario = r.id_usuario
              WHERE r.id_cliente = ?";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $client_id);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Registrar inicio del proceso
error_log("Iniciando proceso de envío de recordatorios: " . date('Y-m-d H:i:s'));

// 1. Verificar cumpleaños del día actual
echo "Verificando cumpleaños de hoy...\n";
error_log("Verificando cumpleaños de hoy...");

// Obtener clientes con cumpleaños hoy
$query = "SELECT c.*, p.nombre_plan, p.precio, e.nombre_empresa, e.rubro as rubro_empresa
          FROM clientes c
          LEFT JOIN planes p ON c.id_plan = p.id_plan
          LEFT JOIN empresas e ON c.id_empresa = e.id_empresa
          WHERE (
              MONTH(c.cumpleaños) = MONTH(CURDATE())
              AND
              DAY(c.cumpleaños) = DAY(CURDATE())
          )";

$stmt = $db->prepare($query);
$stmt->execute();

while ($client = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Enviando recordatorio de cumpleaños para: " . $client['nombre_cliente'] . "\n";
    error_log("Enviando recordatorio de cumpleaños para: " . $client['nombre_cliente']);
    
    // Obtener usuarios asignados
    $assigned_users = getAssignedUsers($db, $client['id_cliente']);
    
    if (empty($assigned_users)) {
        echo "No hay usuarios asignados a este cliente. Enviando solo a administradores.\n";
        error_log("No hay usuarios asignados al cliente " . $client['nombre_cliente'] . ". Enviando solo a administradores.");
    } else {
        echo "Enviando recordatorio a " . count($assigned_users) . " usuario(s) asignado(s).\n";
        error_log("Enviando recordatorio a " . count($assigned_users) . " usuario(s) asignado(s) para el cliente " . $client['nombre_cliente']);
    }
    
    // Enviar recordatorio
    if (sendBirthdayReminder($client, $assigned_users)) {
        echo "Recordatorio enviado con éxito.\n";
        error_log("Recordatorio de cumpleaños enviado con éxito para: " . $client['nombre_cliente']);
    } else {
        echo "Error al enviar recordatorio.\n";
        error_log("Error al enviar recordatorio de cumpleaños para: " . $client['nombre_cliente']);
    }
}

// 2. Verificar pagos para mañana (recordatorio previo)
echo "\nVerificando pagos para mañana...\n";
error_log("Verificando pagos para mañana...");

// Calcular fecha de mañana
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$tomorrow_day = date('d', strtotime($tomorrow));
$tomorrow_month = date('m', strtotime($tomorrow));

// Obtener clientes con fecha de pago mañana
$query = "SELECT c.*, p.nombre_plan, p.precio, e.nombre_empresa, e.rubro as rubro_empresa
          FROM clientes c
          LEFT JOIN planes p ON c.id_plan = p.id_plan
          LEFT JOIN empresas e ON c.id_empresa = e.id_empresa
          WHERE DAY(c.fecha_pago) = ? AND MONTH(NOW()) = ?";

$stmt = $db->prepare($query);
$stmt->bindParam(1, $tomorrow_day);
$stmt->bindParam(2, $tomorrow_month);
$stmt->execute();

while ($client = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Enviando recordatorio de pago (previo) para: " . $client['nombre_cliente'] . "\n";
    error_log("Enviando recordatorio de pago (previo) para: " . $client['nombre_cliente']);
    
    // Obtener usuarios asignados
    $assigned_users = getAssignedUsers($db, $client['id_cliente']);
    
    if (empty($assigned_users)) {
        echo "No hay usuarios asignados a este cliente. Enviando solo a administradores.\n";
        error_log("No hay usuarios asignados al cliente " . $client['nombre_cliente'] . ". Enviando solo a administradores.");
    } else {
        echo "Enviando recordatorio a " . count($assigned_users) . " usuario(s) asignado(s).\n";
        error_log("Enviando recordatorio a " . count($assigned_users) . " usuario(s) asignado(s) para el cliente " . $client['nombre_cliente']);
    }
    
    // Calcular la fecha de pago del mes actual
    $payment_day = date('d', strtotime($client['fecha_pago']));
    $current_month = date('m');
    $current_year = date('Y');
    $payment_date = date('d/m/Y', strtotime($current_year . '-' . $current_month . '-' . $payment_day));
    
    // Enviar recordatorio
    if (sendPaymentReminderBefore($client, $assigned_users, $payment_date)) {
        echo "Recordatorio previo enviado con éxito.\n";
        error_log("Recordatorio de pago previo enviado con éxito para: " . $client['nombre_cliente']);
    } else {
        echo "Error al enviar recordatorio previo.\n";
        error_log("Error al enviar recordatorio de pago previo para: " . $client['nombre_cliente']);
    }
}

// 3. Verificar pagos de ayer (recordatorio posterior)
echo "\nVerificando pagos vencidos de ayer...\n";
error_log("Verificando pagos vencidos de ayer...");

// Calcular fecha de ayer
$yesterday = date('Y-m-d', strtotime('-1 day'));
$yesterday_day = date('d', strtotime($yesterday));
$yesterday_month = date('m', strtotime($yesterday));

// Obtener clientes con fecha de pago ayer
$query = "SELECT c.*, p.nombre_plan, p.precio, e.nombre_empresa, e.rubro as rubro_empresa
          FROM clientes c
          LEFT JOIN planes p ON c.id_plan = p.id_plan
          LEFT JOIN empresas e ON c.id_empresa = e.id_empresa
          WHERE DAY(c.fecha_pago) = ? AND MONTH(NOW()) = ?";

$stmt = $db->prepare($query);
$stmt->bindParam(1, $yesterday_day);
$stmt->bindParam(2, $yesterday_month);
$stmt->execute();

while ($client = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Enviando recordatorio de pago vencido para: " . $client['nombre_cliente'] . "\n";
    error_log("Enviando recordatorio de pago vencido para: " . $client['nombre_cliente']);
    
    // Obtener usuarios asignados
    $assigned_users = getAssignedUsers($db, $client['id_cliente']);
    
    if (empty($assigned_users)) {
        echo "No hay usuarios asignados a este cliente. Enviando solo a administradores.\n";
        error_log("No hay usuarios asignados al cliente " . $client['nombre_cliente'] . ". Enviando solo a administradores.");
    } else {
        echo "Enviando recordatorio a " . count($assigned_users) . " usuario(s) asignado(s).\n";
        error_log("Enviando recordatorio a " . count($assigned_users) . " usuario(s) asignado(s) para el cliente " . $client['nombre_cliente']);
    }
    
    // Calcular la fecha de pago del mes actual
    $payment_day = date('d', strtotime($client['fecha_pago']));
    $current_month = date('m');
    $current_year = date('Y');
    $payment_date = date('d/m/Y', strtotime($current_year . '-' . $current_month . '-' . $payment_day));
    
    // Enviar recordatorio
    if (sendPaymentReminderAfter($client, $assigned_users, $payment_date)) {
        echo "Recordatorio de pago vencido enviado con éxito.\n";
        error_log("Recordatorio de pago vencido enviado con éxito para: " . $client['nombre_cliente']);
    } else {
        echo "Error al enviar recordatorio de pago vencido.\n";
        error_log("Error al enviar recordatorio de pago vencido para: " . $client['nombre_cliente']);
    }
}

echo "\nProceso de envío de recordatorios completado.\n";
error_log("Proceso de envío de recordatorios completado: " . date('Y-m-d H:i:s'));
?>

