<?php require'config.php';auth();need('operacion');chk();$m='';
if($_SERVER['REQUEST_METHOD']==='POST'){
if(isset($_POST['cloro']))$db->prepare("INSERT INTO calidad(fecha,cloro,ph,turbidez,obs) VALUES(?,?,?,?,?)")->execute([$_POST['fecha'],(float)$_POST['cloro'],(float)$_POST['ph'],(float)$_POST['tur'],trim($_POST['obs'])]);
elseif(isset($_POST['ok']))$db->prepare("UPDATE mantenimiento SET estado='atendido' WHERE id=?")->execute([$_POST['ok']]);
else $db->prepare("INSERT INTO mantenimiento(fecha,tipo,descripcion,responsable) VALUES(?,?,?,?)")->execute([$_POST['fecha'],$_POST['tipo'],trim($_POST['desc']),trim($_POST['resp'])]);$m='Registrado';}
top('Calidad y mantenimiento');msg($m);?>
<form method="post"><?=tk()?><b>Calidad del agua</b><br><input type="date" name="fecha" value="<?=date('Y-m-d')?>"><input type="number" step="0.01" name="cloro" placeholder="Cloro mg/L" required><input type="number" step="0.1" name="ph" placeholder="pH" required><input type="number" step="0.1" name="tur" placeholder="Turbidez UNT" required><input name="obs" placeholder="Observación"><button>Registrar</button></form>
<table><tr><th>Fecha</th><th>Cloro</th><th>pH</th><th>Turbidez</th><th>Obs.</th><th>Alerta</th></tr>
<?php foreach($db->query("SELECT * FROM calidad ORDER BY fecha DESC,id DESC LIMIT 15") as $r){$al=$r['cloro']<0.5||$r['ph']<6.5||$r['ph']>8.5||$r['turbidez']>5;echo'<tr><td>'.h($r['fecha']).'</td><td>'.$r['cloro'].'</td><td>'.$r['ph'].'</td><td>'.$r['turbidez'].'</td><td>'.h($r['obs']).'</td><td class="'.($al?'err':'ok').'">'.($al?'FUERA DE RANGO':'OK').'</td></tr>';}?></table>
<p>Rangos de referencia: cloro residual desde 0.5 mg/L, pH 6.5 a 8.5, turbidez hasta 5 UNT. Confírmalos con tu normativa vigente.</p>
<form method="post"><?=tk()?><b>Mantenimiento y averías</b><br><input type="date" name="fecha" value="<?=date('Y-m-d')?>"><select name="tipo"><option>avería</option><option>red</option><option>reservorio</option><option>otro</option></select><input name="desc" placeholder="Descripción" required><input name="resp" placeholder="Responsable"><button>Registrar</button></form>
<table><tr><th>Fecha</th><th>Tipo</th><th>Descripción</th><th>Responsable</th><th>Estado</th><th></th></tr>
<?php foreach($db->query("SELECT * FROM mantenimiento ORDER BY estado,fecha DESC LIMIT 30") as $r)echo'<tr><td>'.h($r['fecha']).'</td><td>'.h($r['tipo']).'</td><td>'.h($r['descripcion']).'</td><td>'.h($r['responsable']).'</td><td>'.h($r['estado']).'</td><td>'.($r['estado']=='pendiente'?'<form method="post" style="border:0;box-shadow:none;padding:0;margin:0;background:none">'.tk().'<button name="ok" value="'.$r['id'].'">Atendido</button></form>':'').'</td></tr>';?></table>
<?php bot();
