<?php require'config.php';auth();need('egresos');chk();$m='';
if($_SERVER['REQUEST_METHOD']==='POST'){if(isset($_POST['del'])){$db->prepare("DELETE FROM egresos WHERE id=?")->execute([$_POST['del']]);$m='Egreso eliminado';}
else{$db->prepare("INSERT INTO egresos(fecha,categoria,descripcion,monto) VALUES(?,?,?,?)")->execute([$_POST['fecha'],$_POST['cat'],trim($_POST['desc']),(float)$_POST['monto']]);$m='Egreso registrado';}}
top('Egresos');msg($m);?>
<form method="post"><?=tk()?><input type="date" name="fecha" value="<?=date('Y-m-d')?>"><select name="cat"><?php foreach(['cloro','repuestos','mantenimiento','personal','otros'] as $o)echo"<option>$o</option>";?></select>
<input name="desc" placeholder="Descripción" required><input type="number" step="0.01" min="0" name="monto" placeholder="Monto S/" required><button>Registrar egreso</button></form>
<table><tr><th>Fecha</th><th>Categoría</th><th>Descripción</th><th>Monto</th><th></th></tr>
<?php foreach($db->query("SELECT * FROM egresos ORDER BY fecha DESC,id DESC LIMIT 100") as $r)echo'<tr><td>'.h($r['fecha']).'</td><td>'.h($r['categoria']).'</td><td>'.h($r['descripcion']).'</td><td>S/ '.number_format($r['monto'],2).'</td><td><form method="post" style="border:0;box-shadow:none;padding:0;margin:0;background:none" onsubmit="return confirm(\'¿Eliminar?\')">'.tk().'<button class="red" name="del" value="'.$r['id'].'">X</button></form></td></tr>';?></table>
<?php bot();
