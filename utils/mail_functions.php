<?php
// Incluir la configuración de correo usando ruta absoluta
$root_path = realpath(dirname(__FILE__) . '/..');
include_once $root_path . '/config/mail_config.php';

/**
 * Función para enviar correos electrónicos
 * 
 * @param string|array $to Destinatario o destinatarios
 * @param string $subject Asunto
 * @param string $message Mensaje (HTML)
 * @param array $cc Destinatarios en copia (opcional)
 * @return bool Éxito o fracaso
 */
function sendMail($to, $subject, $message, $cc = []) {
    // Si $to es un array, convertirlo a string
    if (is_array($to)) {
        $to = implode(",", $to);
    }
    
    // Cabeceras del correo
    $headers = "From: " . MailConfig::$from_name . " <" . MailConfig::$from_email . ">\r\n";
    $headers .= "Reply-To: " . MailConfig::$from_email . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Agregar destinatarios en copia
    if (!empty($cc)) {
        $headers .= "Cc: " . implode(",", $cc) . "\r\n";
    }
    
    // Verificar si se debe usar SMTP
    if (!empty(MailConfig::$smtp_host) && !empty(MailConfig::$smtp_username)) {
        return sendMailSMTP($to, $subject, $message, $cc);
    }
    
    // Enviar correo usando la función mail() de PHP
    $result = mail($to, $subject, $message, $headers);
    
    // Registrar el resultado para depuración
    error_log("Enviando correo a: " . $to . " - Resultado: " . ($result ? "Éxito" : "Error"));
    
    return $result;
}

/**
 * Función para enviar correos usando SMTP (para hosting)
 * 
 * @param string $to Destinatario
 * @param string $subject Asunto
 * @param string $message Mensaje (HTML)
 * @param array $cc Destinatarios en copia (opcional)
 * @return bool Éxito o fracaso
 */
function sendMailSMTP($to, $subject, $message, $cc = []) {

    error_log("Intentando usar SMTP para enviar correo a: " . $to);
    
    // Cabeceras del correo
    $headers = "From: " . MailConfig::$from_name . " <" . MailConfig::$from_email . ">\r\n";
    $headers .= "Reply-To: " . MailConfig::$from_email . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Agregar destinatarios en copia
    if (!empty($cc)) {
        $headers .= "Cc: " . implode(",", $cc) . "\r\n";
    }
    
    return mail($to, $subject, $message, $headers);
}

/**
 * Función para enviar recordatorio de cumpleaños
 * 
 * @param array $client Datos del cliente
 * @param array $users Usuarios asignados al cliente
 * @return bool Éxito o fracaso
 */
function sendBirthdayReminder($client, $users) {
    $subject = "¡Hoy es el Cumpleaños de: " . $client['nombre_cliente'] . "!";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #10b981; color: white; padding: 10px; text-align: center; }
            .content { padding: 20px; background-color: #f9fafb; }
            .footer { font-size: 12px; text-align: center; margin-top: 20px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>¡Feliz Cumpleaños!</h2>
            </div>
            <div class='content'>
                <p>Hoy es el cumpleaños del cliente <strong>" . $client['nombre_cliente'] . "</strong>.</p>
                <p>Detalles del cliente:</p>
                <ul>
                    <li><strong>Empresa:</strong> " . ($client['nombre_empresa'] ? $client['nombre_empresa'] : 'No asignada') . "</li>
                    <li><strong>Plan:</strong> " . ($client['nombre_plan'] ? $client['nombre_plan'] : 'No asignado') . "</li>
                </ul>
                <p>Usuarios asignados:</p>
                <ul>";
    
    foreach ($users as $user) {
        $message .= "<li>" . $user['nombre_usuario'] . " (" . $user['correo_usuario'] . ")</li>";
    }
    
    $message .= "
                </ul>
                <p>Por favor, envíe una felicitación o un detalle especial al cliente en este día tan importante.</p>
            </div>
            <div class='footer'>
                <p>Este es un mensaje automático del Sistema de Gestión de Clientes.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Obtener correos de los usuarios asignados
    $user_emails = array_map(function($user) {
        return $user['correo_usuario'];
    }, $users);
    
    if (empty($user_emails)) {
        return sendMail(MailConfig::$admin_emails, $subject, $message);
    }
    
    return sendMail($user_emails, $subject, $message, MailConfig::$admin_emails);
}

/**
 * Función para enviar recordatorio de pago (antes de la fecha)
 * 
 * @param array $client Datos del cliente
 * @param array $users Usuarios asignados al cliente
 * @param string $payment_date Fecha de pago formateada
 * @return bool Éxito o fracaso
 */
function sendPaymentReminderBefore($client, $users, $payment_date) {
    $subject = "Recordatorio de Pago (Mañana): " . $client['nombre_cliente'];
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #10b981; color: white; padding: 10px; text-align: center; }
            .content { padding: 20px; background-color: #f9fafb; }
            .footer { font-size: 12px; text-align: center; margin-top: 20px; color: #666; }
            .highlight { color: #ef4444; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Recordatorio de Pago</h2>
            </div>
            <div class='content'>
                <p>El pago del cliente <strong>" . $client['nombre_cliente'] . "</strong> vence <strong>mañana</strong> (" . $payment_date . ").</p>
                <p>Detalles del cliente:</p>
                <ul>
                    <li><strong>Empresa:</strong> " . ($client['nombre_empresa'] ? $client['nombre_empresa'] : 'No asignada') . "</li>
                    <li><strong>Plan:</strong> " . ($client['nombre_plan'] ? $client['nombre_plan'] : 'No asignado') . "</li>";
    
    if (isset($client['precio']) && $client['precio']) {
        $message .= "<li><strong>Monto:</strong> $" . number_format($client['precio'], 2) . "</li>";
    }
    
    $message .= "
                </ul>
                <p>Usuarios asignados:</p>
                <ul>";
    
    foreach ($users as $user) {
        $message .= "<li>" . $user['nombre_usuario'] . " (" . $user['correo_usuario'] . ")</li>";
    }
    
    $message .= "
                </ul>
                <p class='highlight'>Por favor, asegúrese de gestionar este pago a tiempo.</p>
            </div>
            <div class='footer'>
                <p>Este es un mensaje automático del Sistema de Gestión de Clientes.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Obtener correos de los usuarios asignados
    $user_emails = array_map(function($user) {
        return $user['correo_usuario'];
    }, $users);
    
    // Si no hay usuarios asignados, enviar solo a los administradores
    if (empty($user_emails)) {
        return sendMail(MailConfig::$admin_emails, $subject, $message);
    }
    
    // Enviar a los usuarios asignados con copia a los administradores
    return sendMail($user_emails, $subject, $message, MailConfig::$admin_emails);
}

/**
 * Función para enviar recordatorio de pago (después de la fecha)
 * 
 * @param array $client Datos del cliente
 * @param array $users Usuarios asignados al cliente
 * @param string $payment_date Fecha de pago formateada
 * @return bool Éxito o fracaso
 */
function sendPaymentReminderAfter($client, $users, $payment_date) {
    $subject = "URGENTE: Pago Vencido - " . $client['nombre_cliente'];
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #ef4444; color: white; padding: 10px; text-align: center; }
            .content { padding: 20px; background-color: #f9fafb; }
            .footer { font-size: 12px; text-align: center; margin-top: 20px; color: #666; }
            .highlight { color: #ef4444; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>PAGO VENCIDO</h2>
            </div>
            <div class='content'>
                <p class='highlight'>El pago del cliente <strong>" . $client['nombre_cliente'] . "</strong> venció <strong>ayer</strong> (" . $payment_date . ") y aún no ha sido registrado.</p>
                <p>Detalles del cliente:</p>
                <ul>
                    <li><strong>Empresa:</strong> " . ($client['nombre_empresa'] ? $client['nombre_empresa'] : 'No asignada') . "</li>
                    <li><strong>Plan:</strong> " . ($client['nombre_plan'] ? $client['nombre_plan'] : 'No asignado') . "</li>";
    
    if (isset($client['precio']) && $client['precio']) {
        $message .= "<li><strong>Monto:</strong> $" . number_format($client['precio'], 2) . "</li>";
    }
    
    $message .= "
                </ul>
                <p>Usuarios asignados:</p>
                <ul>";
    
    foreach ($users as $user) {
        $message .= "<li>" . $user['nombre_usuario'] . " (" . $user['correo_usuario'] . ")</li>";
    }
    
    $message .= "
                </ul>
                <p class='highlight'>ACCIÓN URGENTE: Por favor, contacte al cliente inmediatamente para gestionar este pago vencido.</p>
            </div>
            <div class='footer'>
                <p>Este es un mensaje automático del Sistema de Gestión de Clientes.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Obtener correos de los usuarios asignados
    $user_emails = array_map(function($user) {
        return $user['correo_usuario'];
    }, $users);
    
    // Si no hay usuarios asignados, enviar solo a los administradores
    if (empty($user_emails)) {
        return sendMail(MailConfig::$admin_emails, $subject, $message);
    }
    
    // Enviar a los usuarios asignados con copia a los administradores
    return sendMail($user_emails, $subject, $message, MailConfig::$admin_emails);
}
?>
