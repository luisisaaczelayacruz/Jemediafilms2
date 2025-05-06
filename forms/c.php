<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv; // Asegúrate de importar Dotenv también aquí

require_once __DIR__ . '/../vendor/autoload.php';

// === CARGA .env en este archivo también ===
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function enviarCorreoConfirmacion($email, $name) {
    // Verifica si las variables de entorno están cargadas correctamente
    $mailconfirmacion = new PHPMailer(true);
    
    try {
        $mailconfirmacion->isSMTP();
        $mailconfirmacion->Host       = $_ENV['SMTP_HOST'];
        $mailconfirmacion->SMTPAuth   = true;
        $mailconfirmacion->Username   = $_ENV['SMTP_USERNAME'];
        $mailconfirmacion->Password   = $_ENV['SMTP_PASSWORD'];
        $mailconfirmacion->SMTPSecure = 'tls';
        $mailconfirmacion->Port       = $_ENV['SMTP_PORT'];

        $mailconfirmacion->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
        $mailconfirmacion->addAddress($email, $name);
        $mailconfirmacion->isHTML(true);
        $mailconfirmacion->CharSet = 'UTF-8'; // Cambiado de $mail a $mailconfirmacion
        $mailconfirmacion->Subject = "Hola, $name hemos recibido tu mensaje!";
        
        $mailconfirmacion->Body = '
            <div style="font-family: Arial, sans-serif; font-size: 16px; color: #333; padding: 30px; border: 1px solid #ddd; border-radius: 10px; max-width: 600px; margin: 0 auto; text-align: center; background-color: #f9f9f9;">
<<<<<<< HEAD
                <img src="https://68.183.120.77/img/Je Media fils.png" alt="Logo" style="max-width: 150px; margin-bottom: 20px;">
=======
                <img src="https://tu-servidor.com/ruta-del-logo.png" alt="Logo" style="max-width: 150px; margin-bottom: 20px;">
>>>>>>> d9cd8d261be632e2e66c9c650f4c8e60099f3189
                <h1 style="color: #0052cc; margin-bottom: 20px;">¡Hola, ' . htmlspecialchars($name) . '!</h1>
                <p style="margin-bottom: 15px;">Gracias por escribirnos. Hemos recibido tu mensaje y nos pondremos en contacto contigo lo más pronto posible.</p>
                <p style="margin-bottom: 25px;">Mientras tanto, estate pendiente a tu correo.</p>
                <hr style="border: none; border-top: 1px solid #ccc; margin: 20px 0;">
                <p style="font-size: 12px; color: #888;">Atentamente, Equipo de JE Media Films</p>
                
                <p style="font-size: 12px; color: #999;">Mensaje enviado el ' . date('Y-m-d H:i:s') . '</p>
            </div>';
    
    

        
        
        $mailconfirmacion->send();
        $mailconfirmacion->smtpClose();

        return true;
    } catch (Exception $e) {
        error_log("Error al enviar el correo de confirmación: {$mailconfirmacion->ErrorInfo}");
        return false;
    }
}
