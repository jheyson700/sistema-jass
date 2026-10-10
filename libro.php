<?php require'config.php';auth();need('libro');
$a=$_GET['a']??date('Y-m-01');$b=$_GET['b']??date('Y-m-t');
$s=$db->prepare("SELECT DATE(fecha_pago) f,'Ingreso' t,CONCAT('Pago recibo ',LPAD(id,6,'0'),' (',medio,')') d,monto FROM recibos WHERE estado='pagado' AND DATE(fecha_pago) BETWEEN ? AND ? UNION ALL SELECT fecha,'Egreso',CONCAT(categoria,': ',descripcion),monto FROM egresos WHERE fecha BETWEEN ? AND ? ORDER BY f");$s->execute([$a,$b,$a,$b]);
top('Libro de caja');?>
<form method="get"><input type="date" name="a" value="<?=h($a)?>"><input type="date" name="b" value="<?=h($b)?>"><button>Ver</button></form>
<table><tr><th>Fecha</th><th>Detalle</th><th>Ingreso</th><th>Egreso</th><th>Saldo</th></tr>
<?php $i=$e=0;foreach($s as $r){$in=$r['t']=='Ingreso';$in?$i+=$r['monto']:$e+=$r['monto'];echo'<tr><td>'.h($r['f']).'</td><td>'.h($r['d']).'</td><td>'.($in?number_format($r['monto'],2):'').'</td><td>'.($in?'':number_format($r['monto'],2)).'</td><td>'.number_format($i-$e,2).'</td></tr>';}
echo'<tr><th colspan=2>Totales</th><th>'.number_format($i,2).'</th><th>'.number_format($e,2).'</th><th>S/ '.number_format($i-$e,2).'</th></tr>';?></table>
<button class="np" onclick="print()">Imprimir para asamblea</button>
<?php bot();
