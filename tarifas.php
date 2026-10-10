<?php require'config.php';auth();need('tarifas');chk();$m='';
if($_SERVER['REQUEST_METHOD']==='POST'){
if(isset($_POST['f'])){foreach($_POST['f'] as $c=>$v)$db->prepare("UPDATE tarifas SET cuota_fija=?,m3_incluidos=?,precio_m3=? WHERE categoria=?")->execute([(float)$v,(float)$_POST['i'][$c],(float)$_POST['p'][$c],$c]);$m='Tarifas actualizadas';}
elseif(isset($_POST['delc'])){$db->prepare("DELETE FROM cuotas WHERE id=?")->execute([$_POST['delc']]);$m='Cuota eliminada';}
else{$db->prepare("INSERT INTO cuotas(periodo,descripcion,monto) VALUES(?,?,?)")->execute([$_POST['periodo'],trim($_POST['desc']),(float)$_POST['monto']]);$m='Cuota extraordinaria registrada';}}
top('Tarifas y cuotas');msg($m);?>
<form method="post"><?=tk()?><table><tr><th>Categoría</th><th>Cuota fija</th><th>m³ incluidos</th><th>S/ por m³ extra</th></tr>
<?php foreach($db->query("SELECT * FROM tarifas") as $r){$c=h($r['categoria']);echo"<tr><td>$c</td><td><input type=number step=0.01 name=\"f[$c]\" value=\"{$r['cuota_fija']}\"></td><td><input type=number step=0.01 name=\"i[$c]\" value=\"{$r['m3_incluidos']}\"></td><td><input type=number step=0.01 name=\"p[$c]\" value=\"{$r['precio_m3']}\"></td></tr>";}?></table><button>Guardar tarifas</button></form>
<form method="post"><?=tk()?><b>Cuota extraordinaria</b> (se suma a los recibos que se generen en ese periodo)<br><input type="month" name="periodo" value="<?=date('Y-m')?>"><input name="desc" placeholder="Descripción" required><input type="number" step="0.01" name="monto" placeholder="Monto S/" required><button>Agregar</button></form>
<table><tr><th>Periodo</th><th>Descripción</th><th>Monto</th><th></th></tr>
<?php foreach($db->query("SELECT * FROM cuotas ORDER BY periodo DESC") as $r)echo'<tr><td>'.h($r['periodo']).'</td><td>'.h($r['descripcion']).'</td><td>S/ '.number_format($r['monto'],2).'</td><td><form method="post" style="border:0;box-shadow:none;padding:0;margin:0;background:none">'.tk().'<button class="red" name="delc" value="'.$r['id'].'">X</button></form></td></tr>';?></table>
<?php bot();
