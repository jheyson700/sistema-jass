<?php require'config.php';auth();need('reportes');
$tp=in_array($_GET['tp']??'',['dia','semana','mes','anio'])?$_GET['tp']:'mes';
$d=$_GET['d']??date('Y-m-d');$t=strtotime($d)?:time();
switch($tp){case'dia':$a=$b=date('Y-m-d',$t);break;
case'semana':$a=date('Y-m-d',strtotime('monday this week',$t));$b=date('Y-m-d',strtotime($a.' +6 days'));break;
case'mes':$a=date('Y-m-01',$t);$b=date('Y-m-t',$t);break;
default:$a=date('Y-01-01',$t);$b=date('Y-12-31',$t);}
$s=$db->prepare("SELECT r.id,r.fecha_pago,s.dni,s.nombre,r.periodo,r.consumo,r.monto,r.medio FROM recibos r JOIN socios s ON s.id=r.socio_id WHERE r.estado='pagado' AND DATE(r.fecha_pago) BETWEEN ? AND ? ORDER BY r.fecha_pago");$s->execute([$a,$b]);$rows=$s->fetchAll();
if(isset($_GET['csv'])){header('Content-Type: text/csv; charset=utf-8');header("Content-Disposition: attachment; filename=pagos_{$tp}_$a.csv");$o=fopen('php://output','w');fwrite($o,"\xEF\xBB\xBF");
fputcsv($o,['N°','Fecha pago','DNI','Socio','Periodo','m3','Monto','Medio'],';','"','\\');foreach($rows as $r)fputcsv($o,$r,';','"','\\');exit;}
$tot=0;$pm=[];foreach($rows as $r){$tot+=$r['monto'];$pm[$r['medio']]=($pm[$r['medio']]??0)+$r['monto'];}
top('Reporte de pagos');?>
<form method="get"><select name="tp"><?php foreach(['dia'=>'Por día','semana'=>'Por semana','mes'=>'Por mes','anio'=>'Por año'] as $k=>$v)echo"<option value=\"$k\"".($k==$tp?' selected':'').">$v</option>";?></select>
<input type="date" name="d" value="<?=h($d)?>"><button>Ver</button> <a href="?<?=h(http_build_query(['tp'=>$tp,'d'=>$d,'csv'=>1]))?>">Exportar CSV (Excel)</a></form>
<div class="cards"><div class="box">Del <?=$a?> al <?=$b?><br><b>S/ <?=number_format($tot,2)?></b><br><?=count($rows)?> pagos</div>
<?php foreach($pm as $k=>$v)echo'<div class="box">'.h($k).'<br><b>S/ '.number_format($v,2).'</b></div>';?></div><br>
<table><tr><th>N°</th><th>Fecha</th><th>Socio</th><th>Periodo</th><th>Monto</th><th>Medio</th></tr>
<?php foreach($rows as $r)echo'<tr><td>'.str_pad($r['id'],6,'0',STR_PAD_LEFT).'</td><td>'.h($r['fecha_pago']).'</td><td>'.h($r['nombre']).'</td><td>'.h($r['periodo']).'</td><td>S/ '.number_format($r['monto'],2).'</td><td>'.h($r['medio']).'</td></tr>';?></table>
<h1>Consumo y morosidad por periodo</h1><table><tr><th>Periodo</th><th>m³</th><th>Facturado</th><th>Cobrado</th><th>Moroso</th></tr>
<?php foreach($db->query("SELECT periodo,SUM(consumo) c,SUM(monto) f,SUM(IF(estado='pagado',monto,0)) p FROM recibos GROUP BY periodo ORDER BY periodo DESC LIMIT 12") as $r)echo'<tr><td>'.h($r['periodo']).'</td><td>'.$r['c'].'</td><td>S/ '.number_format($r['f'],2).'</td><td>S/ '.number_format($r['p'],2).'</td><td>S/ '.number_format($r['f']-$r['p'],2).'</td></tr>';?></table>
<button class="np" onclick="print()">Imprimir para asamblea</button>
<?php bot();
