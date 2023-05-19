<?php 
require __DIR__ ."/vendor/autoload.php";
use Illuminate\Database\Capsule\Manager as DB;

// CONFIGURAR AQUI EL NOMBRE DEL NEGOCIO
define('NAME_NEGOCIO', 'RESTOBAR');
define('MENSAJE_WHATSAPP', 'Su comprobante de pago electrónico ha sido generado correctamente, puede revisarlo en el siguiente enlace:');
//configuracion del logo print 
define('L_DIMENSION','30'); // dimensiona en largo como alto 
define('L_CENTER', '20'); // DE IZQUIERDA A DERECHA PARA PODER CENTRARL LA IMAGEN 
define('L_ESPACIO', '10'); // DARA EL ESPACIO ENTRE EL LOGO Y EL NOMBRE COMERCIAL 
define('L_FORMATO' , 'png'); // png, jpg, gif
// define();
//constants
define('HASH_GENERAL_KEY', 'MixitUp200');
define('HASH_PASSWORD_KEY', 'catsFLYhigh2000miles');
//database
define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
// NOMBRE DE LA BASE DE DATOS
define('DB_NAME', 'restobar');
// NOMBRE DEL  USUARIO DE LA  BASE DE DATOS
define('DB_USER', 'ardentia');
define('DB_PASS', '1234');
define('DB_CHARSET', 'utf8');
define('KB', 1024);
define('MB', 1048576);
define('GB', 1073741824);
define('TB', 1099511627776);
//path
define('URL', 'http://amazonico.test/');
define('LIBS', 'libs/');

$db = new DB;

$db->addConnection([
	"driver" => "mysql",
	"host" => DB_HOST,
	"database" => DB_NAME,
	"username" => DB_USER,
	"password" => DB_PASS
 ]);
 $db->setAsGlobal();
 $db->bootEloquent();
