<?php
class GeneraFact extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function genfacturaNo($carid){
        $facid=0;
        $cliid=0;
        $x = "insert into datos set dato='Simon $carid'";
        $querydat = $this->db->query($x);

    }


    
  public function genfactura($carid){
    //    $this->datap = $this->post();
        $facid=0;
        $cliid=0;
        $x = "insert into datos set dato='Simon'";
        $querydat = $this->db->query($x);
     //   $carid=intval($this->datap['carid']);
     //   $file=$this->datap['file'];  //cartaporte, entrada, otrosmovs, fleteloc
                                    // la Entrada si genera cartaporte, otrosmovs y fletelocal no 
    //    echo "La CARID".$carid."<br>";
    // $carid=44914;
    // $carid=intval($carid);  
        $file="cartaporte";
        if ($file='cartaporte'){
      //    echo "La sql1 ".$xsql."<br>";
          $xsql = "select tipocambio from control where conid=1";  
          $querycontrol = $this->db->query($xsql);
          $rowcontrol = $querycontrol->row();
          $tipcam = $rowcontrol->tipocambio;
          
      //    echo "La sql2 ".$xsql."<br>";    
          $xsql = "select * from cartaporte where carid=".$carid;  

        $x = "insert into datos set dato='$xsql'";
          $querydat = $this->db->query($x);

          $querycarta = $this->db->query($xsql);
          if ($querycarta) {
       //     $x = "insert into datos set dato='entro'";
      //      $querydat = $this->db->query($x);
            $rowcarta = $querycarta->row();
            $cliid = $rowcarta->CliId;
            $ruta = $rowcarta->Ruta;
            $tipocp='Malo';
            $t='Nada';
            $n='0';
            $tcp='Nada';
            $mon='Nada';
            $codigo='Nada';
            $unidad='Nada';
            $tipo = $rowcarta->Tipo;
            $destino = $rowcarta->Destino;
            $conflete = $rowcarta->ConFlete;
       //    $x = "insert into datos set dato='llego'";
       //     $querydat = $this->db->query($x);
        //    echo "La tipo ".$tipo."<br>";
        //    echo "La dest ".$destino."<br>";
        //    $t="tipo=$tipo  dest=$destino";
        //    $x = "insert into datos set dato='$t'";
        //    $querydat = $this->db->query($x);
            if ($tipo=='Carr' || $tipo=='Pend'){
              $tipocp='Carro';
              $t='Carro';
              $n="1";
            }
            if (($tipo=='Cros' || $tipo=='PendC') && $destino=='Embarque'){
              $tipocp='CrosEmb';
              $t='CartaCrosEmb';
              $n="3";
            }
            if (($tipo=='Cros' || $tipo=='PendC') && ($destino=='Almacen' || $destino=='Almacen2')){
              $tipocp='CrosAlm';
              $t='CartaCrosAlm';
              $n="4";
            }
            if ($tipo=='Alma' || $tipo=='PendA'){
              $tipocp='AlmEmb';
              $t='CartaAlm';
              $n="5";
            }
            if ($tipo=='Ent'){
              if ($conflete=='Si'){
                $tipocp='EntFlet';
                $t='EntradaFlete';
                $n="7";
              }else{            
                $tipocp='Entrada';
                $t='Entrada';
                $n="6";
              }
            }
            $cargo = 'Cargo'.$t;
            $descr = 'Desc'.$t;
            $conret= 'Chk'.$t;
            $tcp="TipoCp".$n;
            $mon='Mon'.$n;
            $cod='Codigo'.$n;
            $unid='Unid'.$n;
            $metodo='MetPago'.$n;
            $precio = 0;
            $descrip = 'Nada';
            $xsql = "select * from clientes where cliid=".$cliid;  
       //    $t="n=$n  tipo=$tcp";
       //     $x = "insert into datos set dato='$t'";
        //    $querydat = $this->db->query($x);
        //    echo "La xsql3 ".$xsql."<br>";
       //     echo "La xsql3 ".$tcp."<br>";
           
       //     $x = "insert into datos set dato='$xsql'";
       //     $querydat = $this->db->query($x);
            $querycliente = $this->db->query($xsql);
            $rowcliente = $querycliente->row();
            $e = $rowcarta->EmpreId;
            foreach ($querycliente->result_array() as $row)
            {
               $precio = $row[$cargo];
               $descrip = $row[$descr];
               $conret = $row[$conret];
               $metpago = $row[$metodo];
               
               if($conret==1){
                $porret=4;
               }else{$porret=0;}  
               $moneda = 'P';
               if ($row[$mon]=='D'){
                $moneda = 'P';
               }
               $codigo=$row[$cod];
               $unidad=$row[$unid];
            }
            $subtot = $precio;
            $iva = $subtot * ($rowcliente->IvaPorcen/100);
            $retiva = $subtot * ($porret/100);
            $total = $subtot + $iva - $retiva;
       //     $xsql = "select * from clientes where cliid=".$cliid;  
       //     $querycliente = $this->db->query($xsql);
       //     $rowcliente = $querycliente->row();
            $e = $rowcarta->EmpreId;
            $serie = ($e==1?'KA':($e==2?'KL':($e==3?'AR':($e==4?'IN':($e==1?'BR':'NO')))));
            $tipofactcp = $rowcarta->TipoFactCp;
        //    echo "La tipofactcp ".$tipofactcp."<br>";
    
        //    $t="serie=$serie  tipo=$tipofactcp";
        //    $x = "insert into datos set dato='$t'";
        //    $querydat = $this->db->query($x);
            if ($tipofactcp=='F'){
              $file = "facturas";
              $filedet = "facturasdet";
              $tcfdi = "I";
            }
            if ($tipofactcp=='T'){
              $file = "facturast";
              $filedet = "facturastdet";
              $tcfdi = "T";
            }
    $s = "File: $file det: $filedet ripo: $tipofactcp";
            $x = "insert into datos set dato='$s'";
            $querydat = $this->db->query($x);
    
            $descmat="MATERIAL";
            $xsql = "select * from cartaportedet where carid=".$carid;  
    
        //    $x = "insert into datos set dato='$xsql'";
        //    $querydat = $this->db->query($x);
    
            $querycartadet = $this->db->query($xsql);
            if ($querycartadet){
              if ($tipofactcp=='F' or $tipofactcp='T'){
                $rowcartadet = $querycartadet->row();
                $mat=$rowcartadet->MatCliId;
                $xsql = "select * from clientesmat where MatCliId=".$mat; 
            //    echo  $xsql;
    
            $x = "insert into datos set dato='$xsql'";
            $querydat = $this->db->query($x);
    
                $querymat = $this->db->query($xsql);
                $rowmat = $querymat->row();
                $descmat = "MATERIAL-".$rowmat->Descrip1;
                if (!empty($rowmat->Descrip2)){
                  $descmat=$descmat." - ".$rowmat->Descrip2;
                }
                if (!empty($rowmat->Descrip3)){
                  $descmat=$descmat." - ".$rowmat->Descrip3;
                }
                if (!empty($rowmat->Descrip4)){
                  $descmat=$descmat." - ".$rowmat->Descrip4;
                } 
    
                $x = "insert into datos set dato='$t'";
                $querydat = $this->db->query($x);
                $fecha=date("Y-m-d");           
                $data = array(
                  'fecha'=>$fecha,
                  'serie'=>$serie,
                  'facid'=>'SinNum',    
                  'ivaporcen'=>$rowcliente->IvaPorcen,            
                  'cliid'=>$rowcarta->CliId,                    
                  'empreid'=>$rowcarta->EmpreId,  
                  'carid'=>$rowcarta->CarId,
                  'Rfc'=>$rowcliente->Rfc,
                  'Nombre'=>$rowcliente->Nombre,              
                  'MatCliId'=>$rowcartadet->MatCliId,
                  'FormaPago'=>$rowcliente->FormaP, 
                  'Version'=>'3.3',
                  'MetodoPago'=>$metpago,
                  'TipoCfdi'=>$tcfdi,
                  'UsoCfdi'=>'G03',
                  'Pagada'=>'N',
                  'Timbrada'=>'N',
                  'PdfOnLine'=>'N',
                  'XmlOnLine'=>'N',
                  'TipoFac'=>$tcfdi,
                  'TipoCp'=>$t,
                  'Cargo'=>$precio,
                  'Tickets'=>$descrip,
                  'RetIvaPor'=>$porret,
                  'SubTotal'=>$subtot, 
                  'Iva'=>$iva,
                  'RetIva'=>$retiva,
                  'Total'=>$total,  
                  'Saldo'=>$total,              
                  'Moneda'=>$moneda,  
                  'Atencion'=>$codigo,  
                  'Horario'=>$unidad,  
                  'Nvo'=>'W',
                  'Ruta'      => $ruta,  
                  'DescripMat' => $descmat,
                  'NumCp'      => $n,  
                  'TipoCambio'=>$tipcam,                
                );            
                $da=implode(",", $data);
                $x = "insert into datos set dato='$filedet'";
                $querydat = $this->db->query($x);
    
                   if ($this->db->insert($file,$data)){
                    $facid=$this->db->insert_id();
                   }
    
    
                  $x = "insert into datos set dato='$tipofactcp'";
                  $querydat = $this->db->query($x);
    
                
        
                if ($facid>0){
                  if ($t=='Carro'){
                    $docid = $rowcartadet->CarDocId;
                  }
                  if ($t=='CartaCrosEmb' || $t=='CartaCrosAlm'){
                    $docid = $rowcartadet->CrosId;
                  }
                  if ($t=='CartaAlm'){
                    $docid = $rowcartadet->CarId;
                  }
                  if ($t=='Entrada' || $t=='EntradaFlete'){
                    $docid = $rowcartadet->EntId;
                  }
                  $decrip = 'CARRO #'.$rowcartadet->CarDocId;
                  $datadet = array(
                    'ArtId'     => $tipocp.$carid."-".$docid,
                    'NumFac'    => $facid,
                    'Descrip'   => $descrip.' '.$tipocp.$docid,
                    'DocId'     => $docid,
                    'TipoDoc'   => $t,
                    'Cantidad'  => 1,
                    'Precio'    => $precio,
                    'CarNum'    => $carid,
                    'CarId'     => $carid,
                    'Fecha'     => $fecha,
                    'CodigoSat' => $codigo,
                    'UnidSat'   => $unidad,
                    'PesoLib'   => 0,
                    'Moneda'    => $moneda, 
                    'Empaque'   => '',
                    'Articulo'  => $tipocp,
                    'Nvo'       =>'W',
                    'ArticId'   => $rowcartadet->MatCliId);
                    $da=implode(",", $datadet);
                $x = "insert into datos set dato='$da'";
                $querydat = $this->db->query($x);
                    if($this->db->insert($filedet,$datadet)){
                      $xerr='todo Bien';
                      $flag=1;
                    }
                } 
              } 
            } 
          }
        }
        $error=true;
        if ($facid>0 && $flag==1){
          $xsql = "Update cartaporte set Timbrada='X', NumFac=$facid, Serie='$serie' Where carid=$carid";
          $query = $this->db->query($xsql);
          if ($query) {
            $this->facidcp=$facid;
            $error=false;
          }
        }
        $respuesta = array(
          'error' => $error,
          'facidcp' => $facid
        );
        return $facid;
     //   $this->response( $respuesta );
      }
    
}