<?php require'config.php';chk();$m='';
if($_SERVER['REQUEST_METHOD']==='POST'){$s=$db->prepare("SELECT * FROM usuarios WHERE usuario=?");$s->execute([$_POST['usuario']]);$u=$s->fetch();
if($u&&password_verify($_POST['clave'],$u['clave'])){session_regenerate_id(true);$_SESSION['u']=$u['nombre'];$_SESSION['rol']=$u['rol'];$_SESSION['id']=$u['id'];header('Location: dashboard.php');exit;}$m='Usuario o clave incorrectos';}
top('Ingreso');if($m)echo'<p class="err">'.h($m).'</p>';?>
<form method="post"><?=tk()?>Usuario<br><input name="usuario" required autofocus><br>Clave<br><input name="clave" type="password" required><br><button>Entrar</button></form>
<?php bot();
