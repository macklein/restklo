
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
