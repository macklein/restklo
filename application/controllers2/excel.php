<?php

header('Content-type: application/vnd.ms-excel');//para pasar a word solo se cambia a ms-word y la extension a .doc
header("Content-Disposition: attachment; filename=Trailers Liberados.xls");//filename es el nombre con el que se va a exportar
header("Pragma: no-cache");
header("Expires: 0");


$rpfecha1 = $_SESSION['rpfecha1'];
$rpfecha2 = $_SESSION['rpfecha2'];
$rpcliid = $_SESSION['rpcliid'];
$rptraid = $_SESSION['rptraid'];

$cond = " and a.fecha>='".$rpfecha1."' and a.fecha<='".$rpfecha2."' ";
if ($rpcliid>0){
    $cond .= " and a.cliid=".$rpcliid;
}
if ($rptraid>0){
    $cond .= " and a.traid=".$rptraid;
}

$xsql="select a.doctraid, a.traid, b.nombre as nombretrans from doctrailers a, transportistas b where a.traid=b.traid ".$cond." and a.estatus<>'Pend' group by b.traid ";
//echo $xsql;
$query1=mysqli_query($con,$xsql);
if (!$query1){
  echo "Error ".mysqli_error($con) ;           
}
while($row = mysqli_fetch_array($query1)){ 
    $rnombre = $row["nombretrans"];
    $traid = $row["traid"];
    $xsql="select a.doctraid, a.fecha, b.nombre as nombretrans, c.nombre, d.descripcion, e.nombrechofer, f.placas, a.pedimento, a.piezas, a.peso, a.caja, a.umedida, a.upeso from doctrailers a, transportistas b, clientes c, clientesmat d, transchofer e, transplacas f where a.cliid=c.cliid ".$cond." and a.traid=b.traid and a.matid=d.matcliid and a.choid=e.choid and a.plaid=f.plaid and a.estatus<>'Pend' and a.traid=".$traid;
        $query=mysqli_query($con,$xsql);
        if (!$query){
          echo "Error ".mysqli_error($con) ;           
        }
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
									<?php

                                        while($row = mysqli_fetch_array($query)){   
                                        $id=$row['doctraid'];
                                        $fecha1=$row['fecha'];
                                        $fecha=date("d/m/Y", strtotime($fecha1));
                                        $umed=$row['umedida'];
                                        $upes=$row['upeso'];
                                        $cantidad=$row['piezas'];
                                        $caja=$row['caja'];
                                        $nombre=$row['nombre'];
                                        $material=$row['descripcion'];
                                        $chofer=$row['nombrechofer'];
                                        $placas=$row['placas'];
                                        $pedimento=$row['pedimento'];
                                        $peso=$row['peso'];
                                                                             
                                        $esnon=0;
                                        	?>
                                        <tr>
                                
                                            <td><?php echo $id;?></td>
                                            <td><?php echo $fecha?></td>
                                            <td><?php echo $nombre?></td>
                                            <td><?php echo $caja?></td>
                                            <td><?php echo $material?></td>
                                            <td><?php echo $cantidad?></td>
                                            <td><?php echo $umed?></td>
                                            <td><?php echo $peso?></td>
                                            <td><?php echo $upes?></td>
                                            <td><?php echo $pedimento?></td>
                                            <td><?php echo $chofer?></td>
                                            <td><?php echo $placas?></td>
                                          
                                        </tr>
                                     <?php };?>
									
                                    </tbody>
                                </table>
                                <br>
   
<?php } ?>                     

