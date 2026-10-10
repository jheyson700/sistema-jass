<?php require'config.php';auth();need('socios');chk();$m='';
if($_SERVER['REQUEST_METHOD']==='POST'){
if(isset($_POST['del'])){if($_SESSION['rol']==='admin'){$db->prepare("DELETE FROM socios WHERE id=?")->execute([$_POST['del']]);$m='Socio eliminado';}else $m='Error: solo el administrador elimina socios';}
else{$d=[trim($_POST['dni']),trim($_POST['nombre']),trim($_POST['direccion']),trim($_POST['telefono']),trim($_POST['cod']),$_POST['estado']];
try{if($_POST['id'])$db->prepare("UPDATE socios SET dni=?,nombre=?,direccion=?,telefono=?,cod_conexion=?,estado=? WHERE id=?")->execute(array_merge($d,[$_POST['id']]));
else $db->prepare("INSERT INTO socios(dni,nombre,direccion,telefono,cod_conexion,estado) VALUES(?,?,?,?,?,?)")->execute($d);$m='Socio guardado';}
catch(PDOException $e){$m='Error: el DNI ya existe';}}}
$e=['id'=>'','dni'=>'','nombre'=>'','direccion'=>'','telefono'=>'','cod_conexion'=>'','estado'=>'activo'];
if(!empty($_GET['e'])){$s=$db->prepare("SELECT * FROM socios WHERE id=?");$s->execute([$_GET['e']]);$e=$s->fetch()?:$e;}
$q='%'.($_GET['q']??'').'%';$s=$db->prepare("SELECT s.*,(SELECT COALESCE(SUM(monto),0) FROM recibos r WHERE r.socio_id=s.id AND r.estado='pendiente') deuda FROM socios s WHERE nombre LIKE ? OR dni LIKE ? ORDER BY nombre");$s->execute([$q,$q]);
top('Socios');if($m)echo'<p class="ok">'.h($m).'</p>';?>
<form method="post"><?=tk()?><input type="hidden" name="id" value="<?=h($e['id'])?>">
<input name="dni" placeholder="DNI" value="<?=h($e['dni'])?>" required>
<input name="nombre" placeholder="Nombre completo" value="<?=h($e['nombre'])?>" required>
<input name="direccion" placeholder="Dirección" value="<?=h($e['direccion'])?>">
<input name="telefono" placeholder="Teléfono" value="<?=h($e['telefono'])?>">
<input name="cod" placeholder="Cód. conexión" value="<?=h($e['cod_conexion'])?>">
<select name="estado"><?php foreach(['activo','cortado'] as $o)echo"<option".($e['estado']==$o?' selected':'').">$o</option>";?></select>
<button>Guardar socio</button></form>
<form method="get"><input name="q" placeholder="Buscar nombre o DNI" value="<?=h($_GET['q']??'')?>"><button>Buscar</button></form>
<table><tr><th>DNI</th><th>Nombre</th><th>Conexión</th><th>Estado</th><th>Deuda</th><th></th></tr>
<?php foreach($s as $r){?><tr><td><?=h($r['dni'])?></td><td><?=h($r['nombre'])?></td><td><?=h($r['cod_conexion'])?></td><td><?=h($r['estado'])?></td><td>S/ <?=number_format($r['deuda'],2)?></td>
<td><a href="?e=<?=$r['id']?>">Editar</a> <a href="recibos.php?s=<?=$r['id']?>">Recibos</a> <?php if($_SESSION['rol']==='admin'){?><form method="post" style="display:inline;border:0;box-shadow:none;padding:0;margin:0;background:none" onsubmit="return confirm('¿Eliminar socio y sus recibos?')"><?=tk()?><button class="red" name="del" value="<?=$r['id']?>">X</button></form><?php }?></td></tr><?php }?></table>
<?php bot();
