<?php require'config.php';auth();admin();chk();$m='';
if($_SERVER['REQUEST_METHOD']==='POST'){
if(isset($_POST['del'])){if($_POST['del']==$_SESSION['id'])$m='Error: no puedes eliminar tu propio usuario';else{$db->prepare("DELETE FROM usuarios WHERE id=?")->execute([$_POST['del']]);$m='Usuario eliminado';}}
else{$r=in_array($_POST['rol'],['admin','presidente','tesorero','operador','cajero'])?$_POST['rol']:'operador';
$db->prepare("INSERT INTO usuarios(usuario,clave,nombre,rol) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE clave=VALUES(clave),nombre=VALUES(nombre),rol=VALUES(rol)")->execute([trim($_POST['usuario']),password_hash($_POST['clave'],PASSWORD_DEFAULT),trim($_POST['nombre']),$r]);$m='Usuario guardado (si ya existía, se actualizó su clave y rol)';}}
top('Usuarios');if($m)echo'<p class="'.(str_starts_with($m,'Error')?'err':'ok').'">'.h($m).'</p>';?>
<form method="post"><?=tk()?><input name="usuario" placeholder="Usuario" required><input name="nombre" placeholder="Nombre" required><input name="clave" type="password" placeholder="Clave" required>
<select name="rol"><?php foreach(['operador','tesorero','presidente','cajero','admin'] as $o)echo"<option>$o</option>";?></select><button>Guardar usuario</button>
<p>Operador: lecturas, medidores, calidad. Tesorero: cobros, caja, egresos, libro, reportes, tarifas. Presidente: ve todo. Admin: además usuarios y eliminar.</p></form>
<table><tr><th>Usuario</th><th>Nombre</th><th>Rol</th><th></th></tr>
<?php foreach($db->query("SELECT * FROM usuarios ORDER BY usuario") as $r){?><tr><td><?=h($r['usuario'])?></td><td><?=h($r['nombre'])?></td><td><?=h($r['rol'])?></td><td>
<form method="post" style="border:0;box-shadow:none;padding:0;margin:0;background:none" onsubmit="return confirm('¿Eliminar usuario?')"><?=tk()?><button class="red" name="del" value="<?=$r['id']?>">X</button></form></td></tr><?php }?></table>
<?php bot();
