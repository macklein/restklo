<?php
class GeneraFactDocCarro extends CI_Model {

    function __construct() {
        parent::__construct();
    }
    
  public function genfactura($folid,$tipodoc){
    //    $this->datap = $this->post();
        $facid=0;
        $cliid=0;
        $carid=0;
        date_default_timezone_set('America/Chihuahua'); 
        $x = "insert into datos set dato='Simon  Gan FacCarro'";
        $querydat = $this->db->query($x);
     //   $carid=intval($this->datap['carid']);
     //   $file=$this->datap['file'];  //cartaporte, entrada, otrosmovs, fleteloc
                                    // la Entrada si genera cartaporte, otrosmovs y fletelocal no 
      $descmat="MATERIAL";
      //************************************************************** */
      if ($tipodoc=='doccarro'){
          $docid = $folid;
          $carid = $folid;
          $doccarid = $folid;
          $x = "insert into datos set dato='1'";
          $querydat = $this->db->query($x);
          $xsql = "select * from doccarros where doccarid=".$folid;  

        $x = "insert into datos set dato='$xsql'";
        $querydat = $this->db->query($x);

          $querycarta = $this->db->query($xsql);
          $rowcarta = $querycarta->row();
          $cliid = $rowcarta->CliId;
          $traid = $rowcarta->TraId;
         
          $xsql = "select nombre,rfc,precio,serie,factdescrip,tipocp,metpago,codigosat,unidadsat,formap,ivaporcen from transportistas where traid=".$traid;  
          $querytrans = $this->db->query($xsql);
          $rowtrans = $querytrans->row();
          $x = "insert into datos set dato='2'";
        $querydat = $this->db->query($x);
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
          $tipocp='DocCarroN';
          $t='DocCarroN';
          $n="16";   //Aqui el n no sirve por que no jala de cliente sino de transportista
          
  //        $destino = $rowcarta->Destino;
          $destino = 'NoSe';
          $empreid = $rowcarta->EmpreId;
          $tipofactcp = "F"; // $rowcarta->TipoFactCp;
    //      $carid = $rowcarta->CarId;
    //      $entid = $rowcarta->EntId;

          $xsql = "select * from doccarrosdet where doccarid=".$folid;  
          $querycartadet = $this->db->query($xsql);
          $rowcartadet = $querycartadet->row();
          $matid=0;
          $descmat="Descripcion";

      }
      $x = "insert into datos set dato='3'";
      $querydat = $this->db->query($x);
      $xsql = "select tipocambio from control where conid=1";  
      $querycontrol = $this->db->query($xsql);
      $rowcontrol = $querycontrol->row();
      $tipcam = $rowcontrol->tipocambio;

    if ($tipodoc=='doccarro'){
      $nada=0; 
      $porceniva = $rowtrans->ivaporcen;
      $clirfc = $rowtrans->rfc;
      $clinombre = $rowtrans->nombre;
      $cliformap = $rowtrans->formap;
      $x = "insert into datos set dato='4'";
      $querydat = $this->db->query($x);
      // Todos los datos se genraron Arriba en el transportista
    }
    //********************** APARTIR DE AQUI TODO DEBE SER IGUAL */ 
      $subtot = $precio;
      $iva = $subtot * ($porceniva/100);
      $retiva = $subtot * ($porret/100);
      $total = $subtot + $iva - $retiva;
      if ($tipodoc=='doccarro'){
        $empreid=$traid;
        if ($tipofactcp=='F'){
          $file = "trafacturas";
          $filedet = "trafacturasdet";
         
          $file = "facturas";
          $filedet = "facturasdet";
         

          $tcfdi = "I";
        }
        if ($tipofactcp=='T'){
          $file = "trafacturast";
          $filedet = "trafacturastdet";
          $tcfdi = "T";
        }
      }
      $x = "insert into datos set dato='5'";
      $querydat = $this->db->query($x);
      $sql="Select Max(NumFac) as maxid from $file";
      $querym = $this->db->query($sql);
      $rowm = $querym->row();
      $maxid=$rowm->maxid;

      $s = "File: $file det: $filedet ripo: $tipofactcp aquita";
      $x = "insert into datos set dato='$s'";
      $querydat = $this->db->query($x);

  //    $ruta="rtuyu";
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
          if ($tipodoc=='doccarro'){
              if ($tipodoc=='doccarro'){
                $xsql = "Update doccarros set Timbrada='X', NumFac=$facid, Serie='$serie' Where doccarid=$folid";
                $query = $this->db->query($xsql);
                if ($query) {
                  $this->facidcp=$facid;
                  $error=false;
                }
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