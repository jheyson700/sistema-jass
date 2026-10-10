<?php require'config.php';auth();need('lecturas');chk();$m='';
if($_SERVER['REQUEST_METHOD']==='POST'){$p=$_POST['periodo'];$a=(float)$_POST['actual'];$inc=$_POST['inc'];
$s=$db->prepare("SELECT m.*,t.cuota_fija,t.m3_incluidos,t.precio_m3 FROM medidores m JOIN tarifas t ON t.categoria=m.categoria WHERE m.id=?");$s->execute([(int)$_POST['medidor']]);$md=$s->fetch();
$s=$db->prepare("SELECT actual FROM lecturas WHERE socio_id=? ORDER BY periodo DESC LIMIT 1");$s->execute([$md['socio_id']]);$ant=(float)$s->fetchColumn();
if(in_array($inc,['sin_acceso','malogrado']))$a=$ant;
if($a<$ant)$m='Error: la lectura es menor a la anterior ('.$ant.' m³)';
else try{$c=$a-$ant;$x=$db->prepare("SELECT COALESCE(SUM(monto),0) FROM cuotas WHERE periodo=?");$x->execute([$p]);$ex=(float)$x->fetchColumn();
$mt=$md['cuota_fija']+max(0,$c-$md['m3_incluidos'])*$md['precio_m3']+$ex;$db->beginTransaction();
$db->prepare("INSERT INTO lecturas(socio_id,periodo,anterior,actual,incidencia) VALUES(?,?,?,?,?)")->execute([$md['socio_id'],$p,$ant,$a,$inc]);
$db->prepare("INSERT INTO recibos(socio_id,periodo,consumo,monto,extra) VALUES(?,?,?,?,?)")->execute([$md['socio_id'],$p,$c,$mt,$ex]);
if($inc=='malogrado')$db->prepare("UPDATE medidores SET estado='malogrado' WHERE id=?")->execute([$md['id']]);
$db->commit();$m="Recibo generado: $c m³ = S/ ".number_format($mt,2).($ex?" (incluye cuota extra S/ $ex)":'');}
catch(PDOException $e){$db->rollBack();$m='Error: ya existe lectura de ese periodo para el socio';}}
top('Lecturas');msg($m);?>
<form method="post"><?=tk()?><select name="medidor" required><option value="">Medidor...</option>
<?php foreach($db->query("SELECT m.id,m.nro,s.nombre FROM medidores m JOIN socios s ON s.id=m.socio_id WHERE m.estado<>'cortado' ORDER BY s.nombre") as $r)echo'<option value="'.$r['id'].'">'.h($r['nro'].' - '.$r['nombre']).'</option>';?></select>
<input type="month" name="periodo" value="<?=date('Y-m')?>" required><input type="number" step="0.01" min="0" name="actual" placeholder="Lectura actual (m³)" required>
<select name="inc"><option value="normal">Sin incidencia</option><option value="fuga">Fuga</option><option value="sin_acceso">Sin acceso (cuota fija)</option><option value="malogrado">Medidor malogrado (cuota fija)</option></select>
<button>Registrar y generar recibo</button></form>
<table><tr><th>Periodo</th><th>Socio</th><th>Anterior</th><th>Actual</th><th>Incidencia</th></tr>
<?php foreach($db->query("SELECT l.*,s.nombre FROM lecturas l JOIN socios s ON s.id=l.socio_id ORDER BY l.id DESC LIMIT 20") as $r)echo'<tr><td>'.h($r['periodo']).'</td><td>'.h($r['nombre']).'</td><td>'.$r['anterior'].'</td><td>'.$r['actual'].'</td><td>'.h($r['incidencia']).'</td></tr>';?></table>
<?php bot();
