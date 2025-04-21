<?php
// Configuración de correo electrónico
class MailConfig {
    // Remitente del correo
    public static $from_email = "ernalytsyegroup@gmail.com";
    public static $from_name = "Recordatorios Saberes y Emociones Group";
    
    // Destinatarios de las notificaciones (administradores)
    public static $admin_emails = [
        "bi"
    ];
    
    // Configuración de recordatorios
    public static $birthday_reminder_same_day = true; // Recordar cumpleaños el mismo día
    public static $payment_reminder_before = 1; // Días antes para recordar pagos
    public static $payment_reminder_after = 1; // Días después para recordar pagos pendientes
    
    // Configuración SMTP (si es necesario)
    public static $smtp_host = "smtp.tudominio.com";
    public static $smtp_port = 587;
    public static $smtp_username = "usuario_smtp";
    public static $smtp_password = "contraseña_smtp";
    public static $smtp_secure = "tls"; // tls o ssl
}
?>