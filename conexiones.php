<?php require'config.php';auth();need('conexiones');chk();$m='';
if($_SERVER['REQUEST_METHOD']==='POST'){
if(isset($_POST['del'])){if($_SESSION['rol']==='admin'){$db->prepare("DELETE FROM medidores WHERE id=?")->execute([$_POST['del']]);$m='Medidor eliminado';}else $m='Error: solo el administrador elimina';}
else{$db->prepare("INSERT INTO medidores(socio_id,nro,predio,categoria,estado) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE socio_id=VALUES(socio_id),predio=VALUES(predio),categoria=VALUES(categoria),estado=VALUES(estado)")->execute([(int)$_POST['socio'],trim($_POST['nro']),trim($_POST['predio']),$_POST['cat'],$_POST['estado']]);$m='Conexión guardada (si el N° ya existía, se actualizó)';}}
top('Predios y medidores');msg($m);?>
<form method="post"><?=tk()?><select name="socio" required><option value="">Socio...</option><?php foreach($db->query("SELECT id,nombre FROM socios ORDER BY nombre") as $r)echo'<option value="'.$r['id'].'">'.h($r['nombre']).'</option>';?></select>
<input name="nro" placeholder="N° medidor" required><input name="predio" placeholder="Predio / dirección">
<select name="cat"><?php foreach($db->query("SELECT categoria FROM tarifas") as $r)echo'<option>'.h($r['categoria']).'</option>';?></select>
<select name="estado"><option>activo</option><option>malogrado</option><option>cortado</option></select><button>Guardar conexión</button></form>
<table><tr><th>Medidor</th><th>Socio</th><th>Predio</th><th>Categoría</th><th>Estado</th><th></th></tr>
<?php foreach($db->query("SELECT m.*,s.nombre FROM medidores m JOIN socios s ON s.id=m.socio_id ORDER BY s.nombre") as $r){echo'<tr><td>'.h($r['nro']).'</td><td>'.h($r['nombre']).'</td><td>'.h($r['predio']).'</td><td>'.h($r['categoria']).'</td><td>'.h($r['estado']).'</td><td>';
if($_SESSION['rol']==='admin')echo'<form method="post" style="border:0;box-shadow:none;padding:0;margin:0;background:none" onsubmit="return confirm(\'¿Eliminar?\')">'.tk().'<button class="red" name="del" value="'.$r['id'].'">X</button></form>';echo'</td></tr>';}?></table>
<?php bot();
