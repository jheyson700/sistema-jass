<?php require'config.php';auth();
$n=fn($q)=>$db->query($q)->fetchColumn();
$mes=date('Y-m');
top('Panel');?>
<div class="cards">
<div class="box">Socios activos<br><b><?=$n("SELECT COUNT(*) FROM socios WHERE estado='activo'")?></b></div>
<div class="box">Recibos pendientes<br><b><?=$n("SELECT COUNT(*) FROM recibos WHERE estado='pendiente'")?></b></div>
<div class="box">Deuda total<br><b>S/ <?=number_format($n("SELECT COALESCE(SUM(monto),0) FROM recibos WHERE estado='pendiente'"),2)?></b></div>
<div class="box">Cobrado este mes<br><b>S/ <?=number_format($n("SELECT COALESCE(SUM(monto),0) FROM recibos WHERE estado='pagado' AND DATE_FORMAT(fecha_pago,'%Y-%m')='$mes'"),2)?></b></div>
</div>
<?php bot();
