<?php require'config.php';auth();need('caja');chk();$d=$_GET['d']??date('Y-m-d');$m='';
$q=function($sql,$p)use($db){$s=$db->prepare($sql);$s->execute($p);return $s->fetchAll();};
if(isset($_POST['contado'])){$d=$_POST['d'];$e=(float)$_POST['esperado'];$db->prepare("INSERT INTO cierres VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE esperado=VALUES(esperado),contado=VALUES(contado),usuario=VALUES(usuario)")->execute([$d,$e,(float)$_POST['contado'],$_SESSION['u']]);$m='Cierre guardado';}
$pm=$q("SELECT COALESCE(medio,'-') medio,SUM(monto) t,COUNT(*) n FROM recibos WHERE estado='pagado' AND DATE(fecha_pago)=? GROUP BY medio",[$d]);
$ef=0;$tot=0;foreach($pm as $r){$tot+=$r['t'];if($r['medio']=='Efectivo')$ef=$r['t'];}
$eg=(float)$q("SELECT COALESCE(SUM(monto),0) x FROM egresos WHERE fecha=?",[$d])[0]['x'];$esp=$ef-$eg;$ci=$q("SELECT * FROM cierres WHERE fecha=?",[$d])[0]??null;
top('Caja y arqueo');msg($m);?>
<form method="get"><input type="date" name="d" value="<?=h($d)?>"><button>Ver día</button></form>
<table><tr><th>Medio</th><th>Pagos</th><th>Total</th></tr><?php foreach($pm as $r)echo'<tr><td>'.h($r['medio']).'</td><td>'.$r['n'].'</td><td>S/ '.number_format($r['t'],2).'</td></tr>';?></table>
<div class="box">Ingresos del día: S/ <?=number_format($tot,2)?><br>Egresos del día (se asumen en efectivo): S/ <?=number_format($eg,2)?><br>Efectivo esperado en caja: <b>S/ <?=number_format($esp,2)?></b>
<?php if($ci)echo'<br>Contado: S/ '.number_format($ci['contado'],2).' - Diferencia: <b>S/ '.number_format($ci['contado']-$ci['esperado'],2).'</b> ('.h($ci['usuario']).')';?></div>
<form method="post" class="np"><?=tk()?><input type="hidden" name="d" value="<?=h($d)?>"><input type="hidden" name="esperado" value="<?=$esp?>"><input type="number" step="0.01" name="contado" placeholder="Efectivo contado S/" required><button>Cerrar caja</button></form>
<button class="np" onclick="print()">Imprimir</button>
<?php bot();
