<?php
// Asegúrate de que los errores de PHP no se muestren directamente en producción,
// pero para desarrollo, tenerlos visibles puede ayudar a diagnosticar problemas
// como el que estamos viendo. Sin embargo, un echo directo como el del catch es problemático para AJAX.
// ini_set('display_errors', 1); // Descomenta solo para depuración profunda si es necesario
// error_reporting(E_ALL);    // Descomenta solo para depuración profunda si es necesario

// Credenciales de InfinityFree
if (!defined('SERVIDOR')) define('SERVIDOR','sql211.infinityfree.com');
if (!defined('USUARIO')) define('USUARIO','if0_39097460');
if (!defined('PASSWORD')) define('PASSWORD','y1khcORTS3Ib'); // Contraseña de MySQL
if (!defined('BD')) define('BD','if0_39097460_sistemadeventas');

$servidor = "mysql:dbname=".BD.";host=".SERVIDOR;

try {
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // ¡MUY IMPORTANTE!
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Opcional, pero útil
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    ];
    $pdo = new PDO($servidor, USUARIO, PASSWORD, $options);
} catch(PDOException $e) {
    error_log("Error de conexión a la BD en config.php: " . $e->getMessage());
    die("Error crítico: No se pudo conectar a la base de datos. Por favor, contacte al administrador. Detalles: " . $e->getMessage());
}

// URL base actualizada para tu dominio en InfinityFree
$URL = "https://sistemadeventas.infinityfreeapp.com"; 

date_default_timezone_set('America/Guayaquil');
$fechaHora = date('Y-m-d H:i:s');


// ✅ CONFIGURACIÓN DE ROLES DEL SISTEMA
define('ROL_REGISTRO_PUBLICO', 7); // ID del rol para usuarios que se registran públicamente
define('ROL_ADMINISTRADOR', 1);    // ID del rol de administrador (para validaciones)
define('ROL_VENDEDOR', 7);         // ID del rol vendedor (alias para claridad)

// Función para obtener el rol por defecto para registro público
function obtenerRolRegistroPublico(): int {
    return ROL_REGISTRO_PUBLICO;
}

// Función para validar que un rol existe antes de asignarlo
function validarRolExiste(PDO $pdo, int $id_rol): bool {
    try {
        $sql = "SELECT COUNT(*) FROM tb_roles WHERE id_rol = :id_rol";
        $query = $pdo->prepare($sql);
        $query->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchColumn() > 0;
    } catch (PDOException $e) {
        error_log("Error al validar rol: " . $e->getMessage());
        return false;
    }
}