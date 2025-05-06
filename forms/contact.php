<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/c.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = htmlspecialchars(trim($_POST["name"]));
    $email   = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST["message"]));
    $subject = htmlspecialchars(trim($_POST["subject"]));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($name) || empty($message)) {
        echo json_encode(["success" => false, "message" => "Por favor, completa todos los campos correctamente."]);
        exit;
    }

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME'];
        $mail->Password   = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = 'tls';
        $mail->Port       = $_ENV['SMTP_PORT'];

        $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
        $mail->addAddress($_ENV['SMTP_TO_EMAIL'], $_ENV['SMTP_TO_NAME']);

        $mail->isHTML(true);
        $mail->Subject = "Nuevo mensaje de $name";
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; font-size: 16px; color: #333; padding: 20px; border: 1px solid #ccc; border-radius: 5px; text-align: center;'>
            <h1 style='margin-bottom: 20px;'>Nuevo mensaje recibido</h1>
            <table style='width: 100%; border-collapse: collapse; margin: 0 auto; text-align: left;'>
                  <tr>
                    <td style='padding: 10px; font-weight: bold;'>Asunto:</td>
                    <td style='padding: 10px;'>" . htmlspecialchars($subject) . "</td>
                </tr>
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

        $mail->send();
        $mail->smtpClose();

        if (enviarCorreoConfirmacion($email, $name)) {
            echo json_encode(["success" => true, "message" => "Mensaje enviado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "El mensaje fue enviado, pero no se pudo enviar la confirmación."]);
        }
    } catch (Exception $e) {
        error_log("Error al enviar el correo: {$mail->ErrorInfo}");
        echo json_encode(["success" => false, "message" => "Error al enviar el mensaje: {$mail->ErrorInfo}"]);
    }
}
