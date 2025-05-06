<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/c.php'; // Importa el archivo de confirmación

// Carga las variables de entorno desde el archivo .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

header('Content-Type: application/json; charset=utf-8');
// Establece el encabezado para devolver JSON

try {
    // Crear una nueva instancia de PHPMailer para el correo principal
    $mail = new PHPMailer(true);

    // Configuración del correo principal
    $mail->isSMTP();
    $mail->Host       = $_ENV['SMTP_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['SMTP_USERNAME']; // equipo@jemediafilms.com
    $mail->Password   = $_ENV['SMTP_PASSWORD']; // Contraseña del correo
    $mail->SMTPSecure = 'tls';
    $mail->Port       = $_ENV['SMTP_PORT'];

    // Enviar el mensaje del cliente al equipo
    $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']); // equipo@jemediafilms.com
    $mail->addAddress($_ENV['SMTP_TO_EMAIL'], $_ENV['SMTP_TO_NAME']); // equipo@jemediafilms.com

    $mail->isHTML(true);
    $mail->Subject = "Nuevo mensaje de $name";
    $mail->Body = "
        <div style='font-family: Arial, sans-serif; font-size: 16px; color: #333; padding: 20px; border: 1px solid #ccc; border-radius: 5px; text-align: center;'>
            <h1 style='margin-bottom: 20px;'>Nuevo mensaje recibido</h1>
            <table style='width: 100%; border-collapse: collapse; margin: 0 auto; text-align: left;'>
                <tr>
                    <td style='padding: 10px; font-weight: bold;'>Nombre:</td>
                    <td style='padding: 10px;'>" . htmlspecialchars($name) . "</td>
                </tr>
                <tr>
                    <td style='padding: 10px; font-weight: bold;'>Correo:</td>
                    <td style='padding: 10px;'>" . htmlspecialchars($email) . "</td>
                </tr>
                <tr>
                    <td style='padding: 10px; font-weight: bold;'>Mensaje:</td>
                    <td style='padding: 10px;'>" . nl2br(htmlspecialchars($message)) . "</td>
                </tr>
            </table>
        </div>
    ";

    // Enviar el correo principal
    $mail->send();
    $mail->smtpClose(); // Cierra la conexión SMTP después del envío

    // Llamar a la función para enviar el correo de confirmación
    
// Llamar a la función para enviar el correo de confirmación
if (enviarCorreoConfirmacion($email, $name)) {
    http_response_code(200); // Asegura que el código de estado sea 200
    echo json_encode(["status" => "success", "message" => "Mensaje enviado correctamenteeee."]);
} else {
    http_response_code(500); // Código de error si la confirmación falla
    echo json_encode(["status" => "error", "message" => "El mensaje fue enviado, pero no se pudo enviar la confirmación."]);
}
} 
catch (Exception $e) {
    http_response_code(500); // Código de error para excepciones
    error_log("Error al enviar el correo: {$mail->ErrorInfo}");
    echo json_encode(["status" => "error", "message" => "Error al enviar el mensaje: {$mail->ErrorInfo}"]);
}