<?php
session_start();
$db=new PDO('mysql:host=localhost;dbname=jass;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
if(!$db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn())$db->prepare("INSERT INTO usuarios(usuario,clave,nombre,rol) VALUES('admin',?,'Administrador','admin')")->execute([password_hash('admin123',PASSWORD_DEFAULT)]);
function h($s){return htmlspecialchars((string)$s,ENT_QUOTES);}
function auth(){if(empty($_SESSION['u'])){header('Location: index.php');exit;}}
function perm($m){$p=['admin'=>'*','presidente'=>'*','tesorero'=>'socios,recibos,caja,egresos,libro,reportes,morosos,tarifas','operador'=>'socios,conexiones,lecturas,operacion','cajero'=>'socios,lecturas,recibos'];$x=$p[$_SESSION['rol']??'']??'';return $x==='*'||in_array($m,explode(',',$x));}
function need($m){if(!perm($m)){http_response_code(403);die('Sin permiso para este módulo');}}
function admin(){if(($_SESSION['rol']??'')!=='admin'){http_response_code(403);die('Acceso solo para administradores');}}
function csrf(){return $_SESSION['t']??=bin2hex(random_bytes(16));}
function tk(){return '<input type="hidden" name="t" value="'.csrf().'">';}
function chk(){if($_SERVER['REQUEST_METHOD']==='POST'&&!hash_equals(csrf(),$_POST['t']??''))die('Token inválido');}
function msg($m){if($m)echo'<p class="'.(str_starts_with($m,'Error')?'err':'ok').'">'.h($m).'</p>';}
function top($t){echo '<!doctype html><html lang="es"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>JASS - '.h($t).'</title><link rel="stylesheet" href="assets/pixel.css"><body>';
if(!empty($_SESSION['u'])){echo '<nav><b>■ JASS</b><a href="dashboard.php">Inicio</a>';
foreach(['socios'=>'Socios','conexiones'=>'Medidores','lecturas'=>'Lecturas','recibos'=>'Recibos','caja'=>'Caja','morosos'=>'Morosos','egresos'=>'Egresos','libro'=>'Libro','operacion'=>'Operación','reportes'=>'Reportes','tarifas'=>'Tarifas','usuarios'=>'Usuarios'] as $k=>$v)if($k==='usuarios'?($_SESSION['rol']??'')==='admin':perm($k))echo'<a href="'.$k.'.php">'.$v.'</a>';
echo '<a href="logout.php">Salir ('.h($_SESSION['rol']).')</a></nav>';}
echo '<main><h1>'.h($t).'</h1>';}
function bot(){echo '</main></body></html>';}
