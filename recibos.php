<?php require'config.php';auth();need('recibos');chk();
$M=['Efectivo','Yape','Plin','Transferencia','Depósito'];
if(isset($_POST['pagar'])&&in_array($_POST['medio'],$M))$db->prepare("UPDATE recibos SET estado='pagado',fecha_pago=NOW(),medio=? WHERE id=? AND estado='pendiente'")->execute([$_POST['medio'],$_POST['pagar']]);
if(!empty($_GET['masivo'])){$s=$db->prepare("SELECT r.*,s.nombre,s.dni,s.direccion FROM recibos r JOIN socios s ON s.id=r.socio_id WHERE r.periodo=? ORDER BY s.nombre");$s->execute([$_GET['masivo']]);top('Recibos '.$_GET['masivo']);
foreach($s as $r)echo'<div class="box" style="page-break-inside:avoid"><b>RECIBO '.str_pad($r['id'],6,'0',STR_PAD_LEFT).'</b> '.h($r['periodo']).'<br>'.h($r['nombre']).' DNI '.h($r['dni']).'<br>'.h($r['direccion']).'<br>Consumo '.$r['consumo'].' m³ - Total <b>S/ '.number_format($r['monto'],2).'</b> ('.h($r['estado']).')</div>';
echo'<button class="np" onclick="print()">Imprimir / guardar PDF</button>';bot();exit;}
if(!empty($_GET['ver'])){$s=$db->prepare("SELECT r.*,s.nombre,s.dni,s.direccion,s.cod_conexion,l.anterior,l.actual FROM recibos r JOIN socios s ON s.id=r.socio_id LEFT JOIN lecturas l ON l.socio_id=r.socio_id AND l.periodo=r.periodo WHERE r.id=?");$s->execute([$_GET['ver']]);
if($r=$s->fetch()){top('Boleta de pago JASS');?>
<div class="box"<?=isset($_GET['ticket'])?' style="max-width:280px;font-size:8px"':''?>><b>BOLETA N° <?=str_pad($r['id'],6,'0',STR_PAD_LEFT)?></b><br>Socio: <?=h($r['nombre'])?> (DNI <?=h($r['dni'])?>)<br>Dirección: <?=h($r['direccion'])?><br>Conexión: <?=h($r['cod_conexion'])?><br>Periodo: <?=h($r['periodo'])?><br>Lectura: <?=$r['anterior']?> → <?=$r['actual']?> = <?=$r['consumo']?> m³<br>Total: <b>S/ <?=number_format($r['monto'],2)?></b><br>Estado: <?=h($r['estado'])?><?php if($r['estado']=='pagado')echo' - '.h($r['medio']).' - '.h($r['fecha_pago']);?><br>Atendió: <?=h($_SESSION['u'])?></div>
<button class="np" onclick="print()">Imprimir boleta</button> <a class="np" href="?ver=<?=$r['id']?>&ticket=1">Formato ticket</a> <a class="np" href="recibos.php">Volver</a><?php bot();exit;}}
$f=in_array($_GET['f']??'',['pendiente','pagado'])?$_GET['f']:'';$sid=(int)($_GET['s']??0);
$s=$db->prepare("SELECT r.*,s.nombre FROM recibos r JOIN socios s ON s.id=r.socio_id WHERE (?='' OR r.estado=?) AND (?=0 OR r.socio_id=?) ORDER BY r.periodo DESC,s.nombre");$s->execute([$f,$f,$sid,$sid]);
top('Recibos');?><form method="get" class="np"><input type="month" name="masivo" value="<?=date('Y-m')?>"><button>Imprimir recibos del periodo</button></form>
<p><a href="?">Todos</a> | <a href="?f=pendiente">Pendientes</a> | <a href="?f=pagado">Pagados</a></p>
<table><tr><th>Periodo</th><th>Socio</th><th>m³</th><th>Monto</th><th>Estado</th><th>Medio</th><th></th></tr>
<?php foreach($s as $r){?><tr><td><?=h($r['periodo'])?></td><td><?=h($r['nombre'])?></td><td><?=$r['consumo']?></td><td>S/ <?=number_format($r['monto'],2)?></td><td><?=h($r['estado'])?></td><td><?=h($r['medio'])?></td>
<td><a href="?ver=<?=$r['id']?>">Boleta</a> <?php if($r['estado']=='pendiente'){?><form method="post" style="display:inline;border:0;box-shadow:none;padding:0;margin:0;background:none"><?=tk()?><select name="medio"><?php foreach($M as $o)echo"<option>$o</option>";?></select><button name="pagar" value="<?=$r['id']?>">Cobrar</button></form><?php }?></td></tr><?php }?></table>
<?php bot();
