<?php
require __DIR__ . '/vendor/autoload.php';

$client = new Google_Client();
$client->setAuthConfig('credentials.json');
$client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
$client->addScope(Google_Service_Gmail::GMAIL_SEND);
$client->setAccessType('offline');
$client->setPrompt('select_account consent');

// Obtener la URL de autorización
$authUrl = $client->createAuthUrl();
printf("1. Abre este enlace en tu navegador:\n%s\n", $authUrl);

// Pide el código de autorización
print '2. Introduce el código de verificación: ';
$authCode = trim(fgets(STDIN));

// Intercambiar código por token de acceso
$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

// Mostrar token
echo "\nAccess Token generado:\n";
print_r($accessToken);

// Opcional: guardar token en un archivo
file_put_contents('token.json', json_encode($accessToken));
