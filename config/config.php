
<?php
// C:\xampp\htdocs\ProyectWed\config\config.php
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('BASE_URL', 'http://localhost/ProyectWed/');

// Configuración de base de datos
$servername = "127.0.0.1:3307";
$username = "root";
$password = "";
$dbname = "cruphp";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES 'utf8'");
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
