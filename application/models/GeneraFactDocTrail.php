<?php
class GeneraFactDocTrail extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function genfacturaNo($carid){
        $facid=0;
        $cliid=0;
        $x = "insert into datos set dato='Simon $carid'";
        $querydat = $this->db->query($x);

    }


    
  public function genfactura($folid,$tipodoc){
    //    $this->datap = $this->post();
        $facid=0;
        $cliid=0;
        $carid=0;
        date_default_timezone_set('America/Chihuahua'); 
        $x = "insert into datos set dato='Simon'";
        $querydat = $this->db->query($x);
     //   $carid=intval($this->datap['carid']);
     //   $file=$this->datap['file'];  //cartaporte, entrada, otrosmovs, fleteloc
                                    // la Entrada si genera cartaporte, otrosmovs y fletelocal no 
      $descmat="MATERIAL";
      if ($tipodoc=='fleteloc'){
          $docid = $folid;
          $carid = $folid;
          
          $xsql = "select * from fletelocal where fletid=".$folid;  
          $querycarta = $this->db->query($xsql);
          $rowcarta = $querycarta->row();
          $cliid = $rowcarta->CliId;
          $ruta = $rowcarta->Ruta;
        // $tipo = $rowcarta->Tipo; En carta porte define si es entrada, carro, almembarque, etc
          $tipocp='FleteLoc';
          $t='FleteLocal';
          $n="16";
          
  //        $destino = $rowcarta->Destino;
          $destino = 'NoSe';
          $empreid = $rowcarta->EmpreId;
          $tipofactcp = $rowcarta->TipoFactCp;
    //      $carid = $rowcarta->CarId;
    //      $entid = $rowcarta->EntId;

          $xsql = "select * from fletelocaldet where fletid=".$folid;  
          $querycartadet = $this->db->query($xsql);
          $rowcartadet = $querycartadet->row();
          $matid=0;
          $descmat="Descripcion";

      }
      //************************************************************** */
      if ($tipodoc=='doctrail'){
          $docid = $folid;
          $carid = $folid;
          
          $xsql = "select * from doctrailers where doctraid=".$folid;  
          $querycarta = $this->db->query($xsql);
          $rowcarta = $querycarta->row();
          $cliid = $rowcarta->CliId;
          $traid = $rowcarta->TraId;
          $xsql = "select nombre,rfc,precio,serie,factdescrip,tipocp,metpago,codigosat,unidadsat,formap,ivaporcen from transportistas where traid=".$traid;  
          $querytrans = $this->db->query($xsql);
          $rowtrans = $querytrans->row();

          $precio = $rowtrans->precio;
          $transrfc = $rowtrans->rfc;
          $serie = $rowtrans->serie;
          $precio  = $rowtrans->precio;
          $descrip = $rowtrans->factdescrip;
          $conret  = 1;
          $porret  = 4;
          $metpago = $rowtrans->metpago;
          $moneda  = 'P';
          $codigo  = $rowtrans->codigosat;
          $unidad  = $rowtrans->unidadsat;
          
          $ruta = $rowcarta->Ruta;
        // $tipo = $rowcarta->Tipo; En carta porte define si es entrada, carro, almembarque, etc
          $tipocp='DocTrail';
          $t='DocTrailer';
          $n="16";   //Aqui el n no sirve por que no jala de cliente sino de transportista
          
  //        $destino = $rowcarta->Destino;
          $destino = 'NoSe';
          $empreid = $rowcarta->EmpreId;
          $tipofactcp = $rowcarta->TipoFactCp;
    //      $carid = $rowcarta->CarId;
    //      $entid = $rowcarta->EntId;

          $xsql = "select * from doctrailersdet where doctraid=".$folid;  
          $querycartadet = $this->db->query($xsql);
          $rowcartadet = $querycartadet->row();
          $matid=0;
          $descmat="Descripcion";

      }
      if ($tipodoc=='cartacros' || $tipodoc=='cartaalm' || $tipodoc=='entrada'){
          $xsql = "select * from cartaporte where carid=".$folid;  
          $querycarta = $this->db->query($xsql);
          $rowcarta = $querycarta->row();
          $cliid = $rowcarta->CliId;
          $ruta = $rowcarta->Ruta;
          $tipo = $rowcarta->Tipo;
          $destino = $rowcarta->Destino;
          $conflete = $rowcarta->ConFlete;
          $empreid = $rowcarta->EmpreId;
          $tipofactcp = $rowcarta->TipoFactCp;
          $carid = $rowcarta->CarId;
          $entid = $rowcarta->EntId;
          


          $tipocp='Malo';
          $t='Nada2';
          $n='0';
          $tcp='Nada3';
          $mon='Nada4';
          $codigo='Nada5';
          $unidad='Nada6';
    
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
          
    
          $xsql = "select * from cartaportedet where carid=".$folid;  
          $querycartadet = $this->db->query($xsql);
          $rowcartadet = $querycartadet->row();
          $matid=$rowcartadet->MatCliId;
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
          $descrip = 'CARRO #'.$rowcartadet->CarDocId;





      //Solo para generar la Descripcion
          $xsql = "select * from clientesmat where MatCliId=".$matid; 
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


      }

      $xsql = "select tipocambio from control where conid=1";  
      $querycontrol = $this->db->query($xsql);
      $rowcontrol = $querycontrol->row();
      $tipcam = $rowcontrol->tipocambio;

    if ($tipodoc=='doctrail'){
      $nada=0; 
      $porceniva = $rowtrans->ivaporcen;
      $clirfc = $rowtrans->rfc;
      $clinombre = $rowtrans->nombre;
      $cliformap = $rowtrans->formap;
      // Todos los datos se genraron Arriba en el transportista
    }else{
        $porceniva = $rowcliente->IvaPorcen;
        $clirfc = $rowcliente->Rfc;
        $clinombre = $rowcliente->Nombre;
        $cliformap = $rowcliente->FormaP;

        $serie = ($empreid==1?'KA':($empreid==2?'KL':($empreid==3?'AR':($empreid==4?'IN':($empreid==1?'BR':'NO')))));
        $flag=0;
        $cargo = 'Cargo'.$t;
        $descr = 'Desc'.$t;
        $conret= 'Chk'.$t;
        $tcp="TipoCp".$n;
        $mon='Mon'.$n;
        $cod='Codigo'.$n;
        $unid='Unid'.$n;
        $metodo='MetPago'.$n;
        $precio = 0;
        $descrip = 'Nada1';
        $x = "insert into datos set dato='$xsql'";
        $querydat = $this->db->query($x);

        $xsql = "select * from clientes where cliid=".$cliid;  
        $querycliente = $this->db->query($xsql);
        $rowcliente = $querycliente->row();
        foreach ($querycliente->result_array() as $rowcli)
        {
            $precio = $rowcli[$cargo];
            $descrip = $rowcli[$descr];
            $conret = $rowcli[$conret];
            $metpago = $rowcli[$metodo];
            if($conret==1){
              $porret=4;
            }else{$porret=0;}
            $moneda = 'P';
            if ($rowcli[$mon]=='D' || $rowcli[$mon]=='2'){
              $moneda = 'D';
            }
            $codigo=$rowcli[$cod];
            $unidad=$rowcli[$unid];
        }
    }

    //********************** APARTIR DE AQUI TODO DEBE SER IGUAL */ 
      $subtot = $precio;
      $iva = $subtot * ($porceniva/100);
      $retiva = $subtot * ($porret/100);
      $total = $subtot + $iva - $retiva;
      if ($tipodoc=='doctrail'){
        $empreid=$traid;
        if ($tipofactcp=='F'){
          $file = "trafacturas";
          $filedet = "trafacturasdet";
          $tcfdi = "I";
        }
        if ($tipofactcp=='T'){
          $file = "trafacturast";
          $filedet = "trafacturastdet";
          $tcfdi = "T";
        }
      }else{
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
      }
$sql="Select Max(NumFac) as maxid from $file";
$querym = $this->db->query($sql);
$rowm = $querym->row();
$maxid=$rowm->maxid;



      $s = "File: $file det: $filedet ripo: $tipofactcp";
      $x = "insert into datos set dato='$s'";
      $querydat = $this->db->query($x);

    
      //$x = "insert into datos set dato='$t'";
      //$querydat = $this->db->query($x);
      $fecha=date("Y-m-d");           
      $data = array(
        'fecha'=>$fecha,
        'serie'=>$serie,
        'facid'=>'SinNum',    
        'ivaporcen'=>$porceniva,            
        'cliid'=>$cliid,                    
        'empreid'=>$empreid,  
        'carid'=>$carid,
        'Rfc'=>$clirfc,
        'Nombre'=>$clinombre,              
        'MatCliId'=>$matid,
        'FormaPago'=>$cliformap, 
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
      $x = "insert into datos set dato='$da'";
      $querydat = $this->db->query($x);
      if ($this->db->insert($file,$data)){
        $facid=$this->db->insert_id();
      }
      $x = "insert into datos set dato='$tipofactcp'";
      $querydat = $this->db->query($x);

      if ($facid>$maxid){
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
            'ArticId'   => $matid);
            $da=implode(",", $datadet);
            $x = "insert into datos set dato='$da'";
            $querydat = $this->db->query($x);
                if($this->db->insert($filedet,$datadet)){
                  $xerr='todo Bien';
                  $flag=1;
                }
        } 
        $error=true;
        if ($facid>$maxid && $flag==1){
          if ($tipodoc=='fleteloc' || $tipodoc=='doctrail'){
              if ($tipodoc=='fleteloc'){
                $xsql = "Update fletelocal set Timbrada='X', NumFac=$facid, Serie='$serie' Where fletid=$folid";
                $query = $this->db->query($xsql);
                if ($query) {
                  $this->facidcp=$facid;
                  $error=false;
                }
              }
              if ($tipodoc=='doctrail'){
                $xsql = "Update doctrailers set Timbrada='X', NumFac=$facid, Serie='$serie' Where doctraid=$folid";
                $query = $this->db->query($xsql);
                if ($query) {
                  $this->facidcp=$facid;
                  $error=false;
                }
              }
          }else{
            $xsql = "Update cartaporte set Timbrada='X', NumFac=$facid, Serie='$serie' Where carid=$carid";
            $query = $this->db->query($xsql);
            if ( $tipodoc=='entrada' && $entid>0){
              $xsql = "Update entradas set Timbrada='X', NumFac=$facid, Serie='$serie' Where entid=$entid";
              $query = $this->db->query($xsql);
            }
            if ($query) {
              $this->facidcp=$facid;
              $error=false;
            }
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