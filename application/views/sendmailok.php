<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Bienvenidos to CodeIgniter</title>

	<style type="text/css">

	::selection { background-color: #E13300; color: white; }
	::-moz-selection { background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body {
		margin: 0 15px 0 15px;
	}

	p.footer {
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}

	#container {
		margin: 10px;
		border: 1px solid #D0D0D0;
		box-shadow: 0 0 8px #D0D0D0;
	}
	</style>
</head>
<body>



<div id="container">

<?php
header('Content-type: application/vnd.ms-excel');//para pasar a word solo se cambia a ms-word y la extension a .doc
header("Content-Disposition: attachment; filename=Trailers Liberados.xls");//filename es el nombre con el que se va a exportar
header("Pragma: no-cache");
header("Expires: 0");


$cond = "";
/*
$cond = " and a.fecha>='".$rpfecha1."' and a.fecha<='".$rpfecha2."' ";
if ($rpcliid>0){
    $cond .= " and a.cliid=".$rpcliid;
}
if ($rptraid>0){
    $cond .= " and a.traid=".$rptraid;
}
*/

$rnombre="Raul Hernandez";

$xsql="select a.doctraid, a.traid, b.nombre as nombretrans from doctrailers a, transportistas b where a.traid=b.traid ".$cond." and a.estatus<>'Pend' group by b.traid ";
    ?>
							<table border="1" class="t1">
                                <thead>
                                <tr><h1>Transportista: <?php echo $rnombre ?></h1>
                                    
                                         Trailers Liberados
                            
                                <th>Id</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Caja</th>
                                <th>Material</th>
                                <th>Cantidad</th>
                                <th>UMedida</th>
                                <th>Peso</th>
                                <th>UPeso</th>
                                <th>Pedimento</th>
                                <th>Chofer</th>
                                <th>Placas</th>
                                </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                          
                                        </tr>
									
                                    </tbody>
                                </table>
                                <br>


</div>

</body>
</html>