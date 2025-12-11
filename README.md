# ProyectWed
Protyeto Taller base de dato

       Tecnologías Aplicadas
Backend: PHP 7.4
Base de Datos: MySQL 5.7
Frontend:
    Bootstrap 5.3.0
    Bootstrap Icons
Servidor Web: Apache 2.4+
Control de Sesiones: PHP Sessions
Validaciones: JavaScript y PHP


 Requisitos Previos
Software Requerido:
XAMPP 8.2 o WAMP 3.3.0 
MySQL 5.7 
phpMyAdmin 5.2
Navegador Web

 Requisitos del Sistema:
Sistema Operativo: Windows 10/11
Espacio en disco: 500MB libres
Permisos: Escritura en carpeta imagen/

 Instalación Paso a Paso
 Configurar el Entorno Local
Instalar XAMPP:
Descargar desde Apache Friends
Instalar en C:\xampp\ 
Ejecutar el panel de control XAMPP
Iniciar Servicios:
Iniciar Apache
Iniciar MySQL
Abrir phpMyAdmin en: http://localhost/phpmyadmin

Clonar Repositorio
cd C:\xampp\htdocs\
git clone [URL_DEL_REPOSITORIO] ProyectWed

Opción B: Descargar e Instalar Manualmente: // SEGUN CHAT
Descargar archivos del proyecto (ZIP)
Extraer en: C:\xampp\htdocs\ProyectWed\
Asegurarse de que la estructura de carpetas sea correcta

Configurar Archivo de Conexión
Editar archivo: config/db.php
Verificar configuración: 
<?php
$servername = "127.0.0.1:3307";  // Puerto de MySQL en XAMPP
$username = "root";               // Usuario por defecto
$password = "";                   // Contraseña (vacía por defecto en XAMPP)
$dbname = "cruphp";               // Nombre de tu base de datos
?>

  Si usas puerto diferente:
Verificar puerto MySQL en XAMPP 3307 si no 3306 

 Configurar Permisos de Carpetas
Dar permisos de escritura:
icacls "C:\xampp\htdocs\ProyectWed\imagen" /grant Everyone:(OI)(CI)F

Seguridad y Permisos
Roles de Usuario:
Administrador: Acceso completo (CRUD en todos los módulos)
Usuario: Solo lectura en algunos módulos
Protecciones Implementadas:
Autenticación: Sistema de login con sesiones
SQL Injection: Prevención con PDO Prepared Statements
File Upload: Validación de tipos y tamaños de imágenes


Solución de Problemas Comunes //SEGUN CHAT 📈📈📈📈📈📈📈📈📈
Error 1: "No se puede conectar a la base de datos"
// Solución: Verificar config/db.php
$servername = "127.0.0.1:3306"; // Cambiar puerto si es necesario
Error 2: "Permiso denegado" al subir imágenes
Verificar que la carpeta imagen/ tenga permisos de escritura
En XAMPP: C:\xampp\htdocs\ProyectWed\imagen\
Error 3: "Página no encontrada" (404)
Verificar que Apache esté ejecutándose
Confirmar que los archivos estén en htdocs/ProyectWed/
Revisar rutas en header.php y dashboard.php
Error 4: "Sesión no iniciada"
Verificar que session_start() esté en cada archivo necesario
Comprobar que las cookies estén habilitadas en el navegador
