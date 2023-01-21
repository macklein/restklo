<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Cartaporte extends REST_Controller {


  var $datap,$data,$id,$empid,$facidcp;

  public function __construct(){
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    header("Access-Control-Allow-Origin: *");
    parent::__construct();
    $this->load->database();

  }

/************************************************************************/
 public function index_get(){
 }
 public function index_post(){
    $data = $this->post();
    $page = $data['page'];
    $buscar = $data['buscar'];
    $perPage = $data['perPage'];
    $bmodo = $data['bmodo'];  //No Usado
    $estatus = $data['estatus'];  
    $tipouser = $data['tipouser'];  
    $ctesids = $data['ctesids'];  
    $bcliid = $data['bcliid'];  
    $bfecha1 = $data['bfecha1']; 
    $bfecha2 = $data['bfecha2']; 

/*
  if ($pcliid>0){
    $query .=" and cartaporte.cliid=".$pcliid;  
  }
  if ($ptraid>0){
    $query .=" and cartaporte.traid=".$ptraid;  
  }
  
  if ($pfolid>0){
    $query .=" and cartaporte.carid=".$pfolid;
  }
  if ($pdesid=="Almacen"){
    $query .=" and cartaporte.destino='Almacen'"; 
  }
  if ($pdesid=="Embarque"){
    $query .=" and cartaporte.destino='Embarque'";  
  }
  if ($ptipo<>"0"){
    $query .=" and cartaporte.tipo='".$ptipo."'"; 
  }
*/


//$cond="cartaporte.carid>0 ";
    $newf1 = date('Ymd', strtotime($bfecha1));
    $newf2 = date('Ymd', strtotime($bfecha2.'+1 day'));
 

    $d1 = strtotime($bfecha1);
    $f1 = date('d-m-Y',$d1);
    $date1 = explode('-', $f1);
    $y1 = intval($date1[2]);

  
    $d2 = strtotime($bfecha2);
    $f2 = date('d-m-Y',$d2);
    $date2 = explode('-', $f2);        
    $y2 = intval($date2[2]);

    $conduser='';
    $ctes = explode(",", $ctesids);
    $n=0;

    
    if ($tipouser=="Cli"){
      if (count($ctes)==1){
        $conduser.=" and cartaporte.cliid=$ctes[0] ";
      }else{
        foreach ($ctes as $cte) {
          $n=$n+1;
          if ($n==1){
            $conduser.=" and ( cartaporte.cliid=$cte ";
          }else{
            $conduser.=" or cartaporte.cliid=$cte ";
          }
        }
        $conduser.=" ) ";
      }
    }
    if ($tipouser=="Tra"){
      if (count($trans)==1){
        $conduser.=" and cartaporte.traid=$trans[0] ";
      }else{
        foreach ($trans as $tra) {
          $n=$n+1;
          if ($n==1){
            $conduser.=" and ( cartaporte.traid=$tra ";
          }else{
            $conduser.=" or cartaporte.traid=$tra ";
          }
        }
        $conduser.=" ) ";
      }
    }
    
     
    if ($estatus=="Pendientes"){
      $cond="and cartaporte.tipo='PendC' ";
    }else{
      $cond="and (cartaporte.tipo<>'PendC' and cartaporte.tipo<>'PendA') ";
      $cond.="and cartaporte.fecha>=$newf1 ";
      $cond.="and cartaporte.fecha<$newf2 ";
    }  

    if ($bmodo=='S'){
       if ($bcliid>0){
        $cond.="and cartaporte.cliid=$bcliid ";
       }
    }else{
      if (!empty($buscar)){
        $cond .= " and (clientes.nombre LIKE '%".$buscar."%' or transportistas.nombre LIKE '%".$buscar."%' or cartaporte.destino LIKE '%".$buscar."%')"; 
      }
    }

//$tem=$cond;
//$cond='';
//$conduser='';

  $tables="cartaporte, clientes, transportistas, transchofer";
  $campos="cartaporte.carid, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha , cartaporte.destino, cartaporte.impresa, cartaporte.tipo, left(clientes.nombre,15) as nombre, left(transportistas.nombre,15) as nombretrans, transchofer.nombrechofer as chofer ";
  $sWhere="cartaporte.cliid=clientes.cliid and cartaporte.traid=transportistas.traid and cartaporte.choid=transchofer.choid $cond $conduser";
  $sWhere.=" order by cartaporte.carid desc ";
    

    $offset = ($page - 1) * $perPage;
    $xsql="SELECT count(*) AS numrows FROM $tables WHERE $sWhere";
    $query = $this->db->query($xsql);
    $row = $query->row();
    $numrows = $row->numrows;
    if ($numrows>0){
       $paginas = ceil($numrows/$perPage);
    }else{
      $paginas = 1;
    }
    $xsql = "SELECT $campos FROM  $tables where $sWhere LIMIT $offset,$perPage";

/*    $respuesta = array('error' => TRUE, "message" => $xsql); 
*/

    $query = $this->db->query($xsql);
    if ($query) {
      $respuesta = array(
        'error' => FALSE,            
        'paginas' => $paginas, 
        'numrows' => $numrows, 
        'fecha' => $newf2,
        'xsql' => $xsql,
        'items' => $query->result_array()
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query, 'nrr' => $sWhere, 'y1'=>$y1, 'y2'=>$y2);
    }



/*
      $respuesta = array(
        'error' => FALSE,            
        'page' => $query,
        'query' => $tem,
        'where' => $sWhere,
        'xsql' => $xsql
      );
*/

    $this->response( $respuesta );
  }

/************************************************************************/
 public function seleid_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
  $xsql ="Select carid, cliid, carnum, destino, fecha, dircliid, traid, equipidt, equipidr, choid, plaid, empreid, userid as empid, ruta from cartaporte where carid=$id";  
  $query = $this->db->query($xsql);
    if ($query) {
      $respuesta = array(
        'error' => FALSE,            
        'item' => $query->result_array()
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }
    $this->response( $respuesta );
  }
/************************************************************************/
 public function cartachofer_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
  $xsql ="Select a.carid, a.cliid, a.carnum, a.dircliid, a.traid, b.nombre, c.descripcion, d.nombre as nombretrans, a.equipidt, e.descripequip, a.plaid, f.placas, a.choid, g.nombrechofer from cartaporte a left join clientes b on a.cliid=b.cliid left join clientesdir c on a.dircliid=c.dircliid left join transportistas d on a.traid=d.traid left join gasequipos e on a.equipidt=e.equipid left join transplacas f on a.plaid=f.plaid left join transchofer g on a.choid=g.choid ";
  $query = $this->db->query($xsql);
  if ($query) {
    $respuesta = array(
        'error' => FALSE,            
        'item' => $query->result_array()
      );
  }


  //carnum, destino, fecha, dircliid, traid, equipidt, equipidr, choid, plaid, empreid, userid as empid from cartaporte where carid=$id";
//  $query = $this->db->query($xsql);

/*
  $xsql ="Select carid, cliid, carnum, destino, fecha, dircliid, traid, equipidt, equipidr, choid, plaid, empreid, userid as empid from cartaporte where carid=$id";
  $query = $this->db->query($xsql);
    if ($query) {
      $row = $query->row();
      $xsql = "Select cliid,nombre from clientes where cliid=$row->cliid";
      $query1 = $this->db->query($xsql);
      if ($query1) {
        foreach ( $query1->result() as $row1 ) {
            $clientes[] = array( 'label' => $row1->nombre, 'value' => $row1->cliid );
          }
      }
      $xsql = "Select dircliid,descripcion from clientesdir where dircliid=$row->dircliid";
      $query1 = $this->db->query($xsql);
      if ($query1) {
        foreach ( $query1->result() as $row1 ) {
            $direcciones[] = array( 'label' => $row1->descripcion, 'value' => $row1->dircliid );
          }
      }
      $xsql = "Select traid,nombre from transportistas where traid=$row->traid";
      $query1 = $this->db->query($xsql);
      if ($query1) {
        foreach ( $query1->result() as $row1 ) {
            $transportistas[] = array( 'label' => $row1->nombre, 'value' => $row1->traid );
          }
      }


      $respuesta = array(
        'error' => FALSE,            
        'item' => $query->result_array(),
        'clientes' => $clientes,
        'direcciones' => $direcciones,
        'transportistas' => $transportistas

      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }
    */
    $this->response( $respuesta );
  }  
/************************************************************************/
 public function cargacarta_post(){
  $this->datap = $this->post();
  $empid=$this->datap['empid'];
  $xsql ="Select choid from users where user_id=$empid";  
  $respuesta = array('error' => FALSE, 'carid' => 0);
  $query = $this->db->query($xsql);
  if ($query) {
    $row = $query->row();
    $choid = $row->choid;  
    if ($choid>0){
      $xsql ="Select vehid, carid from vehiculos where carid>0 and choid=$choid";  
      $query = $this->db->query($xsql);
      if ($choid>0){
        $row = $query->row();
        $carid = $row->carid;
        $respuesta = array(
          'error' => FALSE,
          'carid' => $carid);
      }
    } 
  } 
  $this->response( $respuesta );
  }  
/************************************************************************/
 public function cargacarta2_post(){
  $this->datap = $this->post();
  $empid=$this->datap['empid'];
  $respuesta = array('error' => TRUE, "message" => "No Encontrado");
  $xsql ="Select choid from users where user_id=$empid";  
  $respuesta = array('error' => TRUE, "message" => $xsql);
 
  $query = $this->db->query($xsql);
  if ($query) {
    $row = $query->row();
    $choid = $row->choid;  
    if ($choid>0){
      $row = $query->row();
      $carid = $row->carid;
      $respuesta = array(
        'error' => FALSE,
        'carid' => $carid
      );
    /*  $xsql ="Select vehid, carid from vehiculos where carid>0 and choid=$choid";  
      $query = $this->db->query($xsql);
      if ($query) {
        $row = $query->row();
        $carid = $row->carid;      
        $xsql ="Select carid, cliid, carnum, destino, fecha, dircliid, traid, equipidt, equipidr, choid, plaid, empreid, userid as empid from cartaporte where carid=$carid";  
        $query1 = $this->db->query($xsql);
        if ($query1) {
          $xsql ="Select crosid, proid, cliid, cantidad, descrip1, descrip2, descrip3, descrip4, peso, matcliid, carnum from cartaportedet where carid=$carid";  
          $query2 = $this->db->query($xsql);
          $respuesta = array(
            'error' => FALSE,
            'carta' => $query1->result_array(),
            'cartadet' => $query2->result_array(),
          );
        } */
    } 
  } 
  $this->response( $respuesta );
  }  

/*
  *  CargoCarro 
  *  ChkCarro  = Retencion de Iva
  *  Mon1 = Moneda
  *  Codigo1
  *  Unid1
  *  TipoCp1
       
  1  DescCarro           Tipo='Carr'
  2  DescCrossDock       no Genera Cartaporte, solo termina el crossdoc y se cobra
  3  DescCartaCrosEmb    Tipo='Cros' Destino='Embarque'
  4  DescCartaCrosAlm    Tipo='Cros' Destino='Almacen'
  5  DescCartaAlm        Tipo='Alma' Destino='Embarque'
  6  DescEntrada         Tipo='Ent'
  7  DescEntradaFlete    Tipo='Ent' con Flete se Obtiene de la entrada
  8  DescReacomodo       File='OtrosMovs'

  9  DescAlmacen         File='Inventario' no genera CartaPorte
 10  DescAlmTarim        File='Inventario' no genera CartaPorte
 11  DescAlmAtado        File='Inventario' no genera CartaPorte
 12  DescCrosTrans       Tipo='Cros' Destino='Transitorio'  ya no existe
 13  DescTransEmb        Tipo='Trans'                 ya no existe
 14  DescCartaTon        Tipo='Alma' Destino='Embarque'       FacIdTon=0 Se factura igual que Almacen-Embarque
 15  DescCrossTon        Tipo='Cros' Destino='Embarque'       FacIdTon=0 Pero que se facture por separado
 16  DescFleteLocal      File='Fletelocal'
*/



public function genfactura2(){
//  $this->datap = $this->post();
  $facid=0;
  $cliid=0;
  $carid=44908;
  $file='cartaporte';
/*  $carid=$this->datap['carid'];
  $file=$this->datap['file'];  //cartaporte, entrada, otrosmovs, fleteloc
                                // la Entrada si genera cartaporte, otrosmovs y fletelocal no 
*/
  if ($file='cartaporte'){
    $xsql = "select tipocambio from control where conid=1";  
    $querycontrol = $this->db->query($xsql);
    $rowcontrol = $querycontrol->row();
    $tipcam = $rowcontrol->tipocambio;
    $xsql = "select * from cartaporte where carid=".$carid;  
    $querycarta = $this->db->query($xsql);
    if ($querycarta) {
      $rowcarta = $querycarta->row();
      $cliid = $rowcarta->CliId;
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
      if ($tipo=='Carr'){
        $tipocp='Carro';
        $t='Carro';
        $n="1";
      }
      if ($tipo=='Cros' && $destino=='Embarque'){
        $tipocp='CrosEmb';
        $t='CartaCrosEmb';
        $n="3";
      }
      if ($tipo=='Cros' && ($destino=='Almacen' || $destino=='Almacen2')){
        $tipocp='CrosAlma';
        $t='CartaCrosAlm';
        $n="4";
      }
      if ($tipo=='Alma'){
        $tipocp='AlmaEmb';
        $t='CartaAlm';
        $n="5";
      }
      if ($tipo=='Ent'){
        if ($conflete=='Si'){
          $tipocp='EntFlete';
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

      $precio = 0;
      $descrip = 'Nada';
      $xsql = "select * from clientes where cliid=".$cliid;  
      $querycliente = $this->db->query($xsql);
      $rowcliente = $querycliente->row();
      $e = $rowcarta->EmpreId;
      foreach ($querycliente->result_array() as $row)
      {
         $precio = $row[$cargo];
         $descrip = $row[$descr];
         $conret = $row[$conret];
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
      $xsql = "select * from clientes where cliid=".$cliid;  
      $querycliente = $this->db->query($xsql);
      $rowcliente = $querycliente->row();
      $e = $rowcarta->EmpreId;
      $serie = ($e==1?'KA':($e==2?'KL':($e==3?'AR':($e==4?'IN':($e==1?'BR':'NO')))));
      $genTipoFac = $rowcarta->GenTipoFac;
      if ($genTipoFac=='F'){
        $file = "facturas";
        $filedet = "facturasdet";
      }
      if ($genTipoFac=='T'){
        $file = "facturast";
        $filedet = "facturastdet";
      }
      $xsql = "select * from cartaportedet where carid=".$carid;  
      $querycartadet = $this->db->query($xsql);
      if ($querycartadet){
        if ($genTipoFac='F' or $genTipoFac='T'){
          $rowcartadet = $querycartadet->row();
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
            'MetodoPago'=>'PPD',
            'TipoCfdi'=>'I',
            'UsoCfdi'=>'G03',
            'Pagada'=>'N',
            'Timbrada'=>'N',
            'PdfOnLine'=>'N',
            'XmlOnLine'=>'N',
            'TipoFac'=>'I',
            'TipoCp'=>$tipocp,
            'Cargo'=>$precio,
            'Tickets'=>$descrip,
            'RetIvaPor'=>$porret,
            'SubTotal'=>$subtot,
            'Iva'=>$iva,
            'RetIva'=>$retiva,
            'Total'=>$total,  
            'Moneda'=>$moneda,  
            'Atencion'=>$codigo,  
            'Horario'=>$unidad,  
            'Nvo'=>'W',
            'TipoCambio'=>$tipcam,                
          );            
          if ($this->db->insert($file,$data)){
            $facid=$this->db->insert_id();
          }
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
              'ArtId'     => $tipocp.$docid,
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
              'PesoLib'   => $carid,
              'Moneda'    => $moneda, 
              'Empaque'   => $carid,
              'Articulo'  => $carid,
              'Nvo'       =>'W',
              'ArticId'   => $rowcartadet->MatCliId);
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
  if ($facid>0 && $flag=1){
    $xsql = "Update cartaporte set facidcp=$facid, gentipofac=$genTipoFac Where carid=$carid";
    $query = $this->db->query($xsql);
    if ($query) {
      $error=false;
    }
  }
  $respuesta = array(
    'error' => $error,
    'factura' => $tipo,
    'carid' => $cargo,
    'cliid' => $destino,
    'precio' => $precio,
    'descrip' => $descrip,
    'det'=>$xerr,
    
  );
 // $this->response( $respuesta );
}


  public function genfactura_post(){
    $this->datap = $this->post();
    $facid=0;
    $cliid=0;
    $carid=$this->datap['carid'];
    $file=$this->datap['file'];  //cartaporte, entrada, otrosmovs, fleteloc
                                // la Entrada si genera cartaporte, otrosmovs y fletelocal no 

    if ($file='cartaporte'){
      $xsql = "select tipocambio from control where conid=1";  
      $querycontrol = $this->db->query($xsql);
      $rowcontrol = $querycontrol->row();
      $tipcam = $rowcontrol->tipocambio;
      $xsql = "select * from cartaporte where carid=".$carid;  
      $querycarta = $this->db->query($xsql);
      if ($querycarta) {
        $rowcarta = $querycarta->row();
        $cliid = $rowcarta->CliId;
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
        if ($tipo=='Carr'){
          $tipocp='Carro';
          $t='Carro';
          $n="1";
        }
        if ($tipo=='Cros' && $destino=='Embarque'){
          $tipocp='CrosEmb';
          $t='CartaCrosEmb';
          $n="3";
        }
        if ($tipo=='Cros' && ($destino=='Almacen' || $destino=='Almacen2')){
          $tipocp='CrosAlma';
          $t='CartaCrosAlm';
          $n="4";
        }
        if ($tipo=='Alma'){
          $tipocp='AlmaEmb';
          $t='CartaAlm';
          $n="5";
        }
        if ($tipo=='Ent'){
          if ($conflete=='Si'){
            $tipocp='EntFlete';
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

        $precio = 0;
        $descrip = 'Nada';
        $xsql = "select * from clientes where cliid=".$cliid;  
        $querycliente = $this->db->query($xsql);
        $rowcliente = $querycliente->row();
        $e = $rowcarta->EmpreId;
        foreach ($querycliente->result_array() as $row)
        {
           $precio = $row[$cargo];
           $descrip = $row[$descr];
           $conret = $row[$conret];
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
        $xsql = "select * from clientes where cliid=".$cliid;  
        $querycliente = $this->db->query($xsql);
        $rowcliente = $querycliente->row();
        $e = $rowcarta->EmpreId;
        $serie = ($e==1?'KA':($e==2?'KL':($e==3?'AR':($e==4?'IN':($e==1?'BR':'NO')))));
        $genTipoFac = $rowcarta->GenTipoFac;
        if ($genTipoFac=='F'){
          $file = "facturas";
          $filedet = "facturasdet";
        }
        if ($genTipoFac=='T'){
          $file = "facturast";
          $filedet = "facturastdet";
        }
        $xsql = "select * from cartaportedet where carid=".$carid;  
        $querycartadet = $this->db->query($xsql);
        if ($querycartadet){
          if ($genTipoFac='F' or $genTipoFac='T'){
            $rowcartadet = $querycartadet->row();
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
              'MetodoPago'=>'PPD',
              'TipoCfdi'=>'I',
              'UsoCfdi'=>'G03',
              'Pagada'=>'N',
              'Timbrada'=>'N',
              'PdfOnLine'=>'N',
              'XmlOnLine'=>'N',
              'TipoFac'=>'I',
              'TipoCp'=>$tipocp,
              'Cargo'=>$precio,
              'Tickets'=>$descrip,
              'RetIvaPor'=>$porret,
              'SubTotal'=>$subtot,
              'Iva'=>$iva,
              'RetIva'=>$retiva,
              'Total'=>$total,  
              'Moneda'=>$moneda,  
              'Atencion'=>$codigo,  
              'Horario'=>$unidad,  
              'Nvo'=>'W',
              'TipoCambio'=>$tipcam,                
            );            
            if ($this->db->insert($file,$data)){
              $facid=$this->db->insert_id();
            }
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
                'ArtId'     => $tipocp.$docid,
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
                'PesoLib'   => $carid,
                'Moneda'    => $moneda, 
                'Empaque'   => $carid,
                'Articulo'  => $carid,
                'Nvo'       =>'W',
                'ArticId'   => $rowcartadet->MatCliId);
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
    if ($facid>0 && $flag=1){
      $xsql = "Update cartaporte set facidcp=$facid, gentipofac=$genTipoFac Where carid=$carid";
      $query = $this->db->query($xsql);
      if ($query) {
        $error=false;
      }
    }
    $respuesta = array(
      'error' => $error,
      'factura' => $tipo,
      'carid' => $cargo,
      'cliid' => $destino,
      'precio' => $precio,
      'descrip' => $descrip,
      'det'=>$xerr,
      
    );
    $this->response( $respuesta );
  }

/************************************************************************/
  public function altaprueba_post(){
    $this->datap = $this->post();


    $dat=json_decode($this->datap['datosm']);
    $dirid = $dat->dircliid;
    $id = $dat->cliid;
    $nada = "0";
    if ($dat->destino=="Almacen" || $dat->destino=="Almacen2"){
      $nada="1";
    }
    $xsql = "select dircliid,descripcion from clientesdir where cliid=".$id;  
    $query = $this->db->query($xsql);
    if ($query) {
      $row = $query->row();
      $dirid = $row->dircliid;  
    }
    
 
    $respuesta = array(
          'error' => FALSE,
          'carid' => $xsql,
          'dir' => $dirid);
     $this->response( $respuesta );
  }

  public function alta_post(){
  $this->datap = $this->post();
  $ruta=$this->datap['chips'];
  $this->losdatos();
  $fecha=date("Y-m-d H:i:s");
  $this->data += [ "fecha" => $fecha ];
  $this->data += [ "ruta" => $ruta ];
  $this->data += [ "tipo" => "PendC" ]; // Pendiente Cross
  $this->data += [ "impresa" => "No" ];
  $this->data += [ "afactura" => "N" ];
  $this->data += [ "nvo" => "S" ];
  $this->data += [ "userid" => $this->empid ];  
  $this->db->insert('cartaporte',$this->data);
  $id=$this->db->insert_id();
  if ($id>0){
      $respuesta = array(
        'error' => FALSE,
        'ruta' => $ruta,
        'id' => $id);
    }else{
        $respuesta = array(
          'error' => TRUE);
   }
  $this->response( $respuesta ); 
 }

/************************************************************************/
 public function modif_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
  $this->losdatos();
  $this->db->where( 'carid', $id );
  $hecho = $this->db->update( 'cartaporte', $this->data);
  if ($hecho){
      $respuesta = array(
        'error' => FALSE,
        'id' => $this->id);
    }else{
        $respuesta = array(
          'error' => TRUE);
   }
  $this->response( $respuesta );
  }
/************************************************************************/
  private function losdatos(){
    $dat=json_decode($this->datap['datosm']);
    $dirid = $dat->dircliid;
    $id = $dat->cliid;
    if ($dat->destino=="Almacen" || $dat->destino=="Almacen2"){
      $xsql = "select dircliid,descripcion from clientesdir where cliid=".$id;  
      $query = $this->db->query($xsql);
      if ($query) {
        $row = $query->row();
        $dirid = $row->dircliid;  
      }
    }
    $this->empid=$dat->empid;
    $this->data = array(
        'cliid'=>$dat->cliid,
        'empreid'=>$dat->empreid,
        'traid'=>$dat->traid,
        'dircliid'=>$dirid,
        'equipidt'=>$dat->equipidt,
        'equipidr'=>$dat->equipidr,
        'destino'=>$dat->destino,
        'choid'=>$dat->choid,
        'plaid'=>$dat->plaid
    );
  }
 
/************************************************************************/
  public function enviarcarta_post(){
    $data = $this->post();
    
    $xsql="Prueba xxxxxx";
   $respuesta = array('error' => FALSE, 'carid' => 25, 'xsql' => $xsql); 
 // $this->response( $respuesta );

    $dat=json_decode($data['datosm']);
    $tipouser = $data['tipouser'];
    $tipodocs = $data['estatus']; // Pendientes o Liberados
    $carid = $dat->carid;
    $dircliid = intval($dat->dircliid);
    $cliid    = $dat->cliid; 
    $destino  = $dat->destino;

    $dirid = $dircliid;
    $id = $dat->cliid;
    if ($destino=="Almacen" || $destino=="Almacen2"){
      $xsql = "select dircliid,descripcion from clientesdir where cliid=".$id;  
      $query = $this->db->query($xsql);
      if ($query) {
        $row = $query->row();
        $dirid = $row->dircliid;  
        $dircliid = $row->dircliid;
      }
    }
    
    $empreid  = $dat->empreid;
    $empid    = $dat->empid;
    $traid    = $dat->traid;   
    $equipidt = $dat->equipidt;
    $equipidr = $dat->equipidr;
    $choid    = $dat->choid;
    $plaid    = $dat->plaid;

    $tipoc="Cros";
    $tipocar="CrosDock";
    $tipocarta=1;
  //  $respuesta = array('error' => TRUE, 'carid' => 0);
    $pasa = 1;
    $xsql = "select year(fechaenv) as anio from cartaporte WHERE carid='".$carid."'";
    $query = $this->db->query($xsql);
    $row = $query->row();
    $anio = intval($row->anio);

    $xsql = "select vehid from vehiculos WHERE equipidt=$equipidt";
    $query = $this->db->query($xsql);
    $row = $query->row();
    $vehid = intval($row->vehid);
// $this->response( $respuesta );
    $xsql = "select latitude, longitude from clientesdir WHERE dircliid=$dircliid";
    $lat=0;
    $long=0;
    if ($dircliid>0){
      $query = $this->db->query($xsql);
      $row = $query->row();
      $lat = $row->latitude;
      $long = $row->longitude;
    }
    $error=0;
    $fecha=date("Y-m-d H:i:s");
    if ($anio==0){  
      $this->db->trans_begin();
      $xsql = "UPDATE cartaporte SET dircliid='$dircliid', destino='$destino', traid='$traid', choid='$choid', plaid='$plaid', fechaenv='$fecha', tipo='$tipoc', equipidt='$equipidt', equipidr='$equipidr' WHERE 
      carid='".$carid."'"; 

      $query = $this->db->query($xsql);
      if ($query){
        if ($tipocarta==1){ 
   //Rul para que no cancele estatus       $xsql = "UPDATE cartaportedet SET estatus='Cros', fechaenv='$fecha', destino='$destino' WHERE carid='".$carid."'";
          $xsql = "UPDATE cartaportedet SET estatus='Cros', fechaenv='$fecha', destino='$destino' WHERE carid='".$carid."'";       
          $query = $this->db->query($xsql);
          if ($query){
            if ($destino=="Almacen" || $destino=="Almacen2" || $destino=="Transitorio"){
              $xsql = "select * from cartaportedet where carid=".$carid;
              $query1 = $this->db->query($xsql);
              foreach ( $query1->result() as $rw0 ) {
                $cardetid = $rw0->CarDetId;
               // $carid = $rw0->CarId;
                $crosid = $rw0->CrosId;
                $crosdetid = $rw0->CrosDetId;
                $proid = $rw0->ProId;
                $cliid = $rw0->CliId;
                $cantidad = $rw0->Cantidad;
                $descrip1 = $rw0->Descrip1;
                $descrip2 = $rw0->Descrip2;
                $descrip3 = $rw0->Descrip3;
                $descrip4 = $rw0->Descrip4;
                $peso = $rw0->Peso;
                $matcliid = $rw0->MatCliId;
                $empreid = $rw0->EmpreId;
                $userid = $rw0->UserId;
                $carnum = $rw0->CarNum;
                $capturadas = $rw0->Capturadas;
                $dest = strtolower($destino);
                if ($destino=="Almacen"){
                  $xsql = "INSERT INTO almacen ";  
                }
                if ($destino=="Almacen2"){
                  $xsql = "INSERT INTO almacen2 ";  
                }
                if ($destino=="Transitorio"){
                  $xsql = "INSERT INTO almacentrans ";  
                } 
                $xsql .= " (cardetid, carid, fecha, crosdetid, cantidad, peso, descrip1, descrip2, descrip3, descrip4, cliid, proid, crosid, matcliid, empreid, userid, estatus, carnum, tipo, nvo) VALUES ('$cardetid', '$carid', '$fecha', '$crosdetid', '$cantidad', '$peso', '$descrip1', '$descrip2', '$descrip3', '$descrip4', '$cliid', '$proid', '$crosid', '$matcliid', '$empreid', '$userid', 'Pend', '$carnum', 'Cros', 'S')"; 
                $query0 = $this->db->query($xsql);  
              } 
            }
            $xsql   = "select crosid from cartaportedet where carid='".$carid."' group by crosid";
            $query1 = $this->db->query($xsql);
            foreach ( $query1->result() as $rw1 ) {
              $crosid = $rw1->crosid;
              $xsql   = "select cantidad from cartaportedet where crosid='".$crosid."' and estatus='Cros'";
              $query = $this->db->query($xsql);
              $count = 0;
              foreach ( $query->result() as $rw ) {
                $cant=$rw->cantidad;
                $count=$count+$cant;
              }
              $xsql   = "select * from crossdock where crosid='".$crosid."'";
              $query = $this->db->query($xsql);
              $rw = $query->row();
              $cantidad = $rw->Cantidad;
              if ($count>=$cantidad){
                $xsql = "Update crossdock set enviados='$count', estatus='Term' where crosid='".$crosid."'";
              }else{
                $xsql = "Update crossdock set enviados='$count' where crosid='".$crosid."'";
              }
              $query = $this->db->query($xsql);
            } 
          }
        } 
        if ($tipocarta==2){ 
          $xsql = "UPDATE crossdockdet SET estatus2='Alma', fechaalma='$fecha', destino='$destino' WHERE carid2='".$carid."'";
          $query = $this->db->query($xsql);
        }
      }
      $xsql = "Update vehiculos set CarId=$carid, Estatus='EnRuta', DirCliId=$dircliid, latitude='$lat', longitude='$long' Where VehId=$vehid ";
      $query = $this->db->query($xsql); 

    if ($this->db->trans_status() === FALSE){
       $this->db->trans_rollback();
       $carid=0;
    }else{
       $this->db->trans_commit();
    } 
    
  }
  
  if ($carid>0){
  //  $this->genfactura2();
    $respuesta = array('error' => FALSE, 'carid' => $carid, 'xsql'=>$xsql);
  } 
//   $respuesta = array('error' => FALSE, 'carid' => $xsql); //, 'xsql'=>$xsql);
  $this->response( $respuesta );


}  

/************************************************************************/
 public function cargardet_post(){
  $data = $this->post();
  $carid=$data['carid'];

  $xsql="select a.cardetid, left(b.nombre,15) as nombre, a.descrip1, a.descrip2, a.descrip3, a.descrip4, a.peso, a.cantidad from cartaportedet a, proveedores b where a.proid=b.proid and a.carid=".$carid;
  $query = $this->db->query($xsql);
    
  if ($query) {
    $xsql="select a.descrip1, a.descrip2, a.descrip3, a.descrip4, b.cliid from clientesmat a, cartaportedet b where a.matcliid=b.matcliid and b.carid=".$carid." limit 1";
     $query2 = $this->db->query($xsql);              
      $respuesta = array(
        'error' => FALSE,
        'descrips' => $query2->result_array(),
        'items' => $query->result_array() 
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }
    $this->response( $respuesta );
  }
/************************************************************************/
 public function cargacli_post(){
  $this->datap = $this->post();
  $cliid=$this->datap['cliid'];

  $xsql="select a.crosdetid, a.descrip1, a.descrip2, a.descrip3, a.descrip4, a.proid, a.peso, a.cantidad, a.capturadas, (a.cantidad-a.capturadas) as pendientes, (a.cantidad-a.capturadas) as cant, left(b.nombre,15) as nombre, a.cliid from crossdockdet a, proveedores b where a.cantidad>a.capturadas and a.proid=b.proid and a.estatus='Pend' and a.carid=0 and a.cliid=".$cliid;
  $query = $this->db->query($xsql);
    if ($query) {
      $respuesta = array(
        'error' => FALSE,            
        'items' => $query->result_array()
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }
    $this->response( $respuesta );
  }

  /************************************************************************/
  public function agregardet_post(){
    $data = $this->post();
    $carid=$data['carid'];
    $crosdetid=$data['crosdetid'];
    $fecha=date("Y-m-d H:i:s");  
    $cantidad=$data['cant'];    
    $pendientes=$data['pendientes'];        
    $userid = $data['empid']; 
    $xsql = "Select * from crossdockdet Where crosdetid=".$crosdetid;
    $query = $this->db->query($xsql);
    $row = $query->row();
    $cliid = $row->CliId;
    $crosid = $row->CrosId;
    $proid = $row->ProId;
    $descr1 = $row->Descrip1;
    $descr2 = $row->Descrip2;
    $descr3 = $row->Descrip3;
    $descr4 = $row->Descrip4;
    $empreid = $row->EmpreId;
    $matcliid = $row->MatCliId;
    $piezas=intval($row->Cantidad);
    $peso=intval($row->Peso);
    $pesoreal = 0;
    if ($piezas>0){
      $pesoreal = ($cantidad*$peso)/$piezas;
    }
    $xsql = "SELECT carnum from crossdock where crosid=".$crosid;
    $query = $this->db->query($xsql);
    $row = $query->row();
    $carnum=$row->carnum;

    $cardetid=0;
    $xsql="SELECT count(*) AS numrows FROM cartaportedet WHERE crosdetid=".$crosdetid." and carid=".$carid;
    $query = $this->db->query($xsql);
    $row = $query->row();
    $numrows = $row->numrows;    
    if ($numrows>0){
      $xsql = "SELECT cardetid from cartaportedet where crosdetid=".$crosdetid." and carid=".$carid;
      // $r=$xsql;
      $query = $this->db->query($xsql);    
      $row = $query->row();
      $cardetid=$row->cardetid;
    }
    if ($cardetid>0){
      $xsql =  "UPDATE cartaportedet SET cantidad=cantidad+".$cantidad.", peso=peso+".$pesoreal." WHERE cardetid=".$cardetid;
      $query = $this->db->query($xsql);
    }else{
      $xsql = "INSERT INTO cartaportedet (carid, fecha, crosdetid, cantidad, peso, descrip1, descrip2, descrip3, descrip4, cliid, proid, crosid, matcliid, empreid, userid, estatus2, carnum, nvo) VALUES ('$carid', '$fecha', '$crosdetid', '$cantidad', '$pesoreal', '$descr1', '$descr2', '$descr3', '$descr4', '$cliid', '$proid', '$crosid', '$matcliid', '$empreid', '$userid', 'Pend', '$carnum', 'S')";
      $query = $this->db->query($xsql);
    }
    $entro="No";
    if ($query){
      $entro="Si";
      if ($cantidad==$pendientes){
        $xsql = "UPDATE crossdockdet SET capturadas=cantidad, estatus='EnCar' WHERE crosdetid=".$crosdetid;
         $query = $this->db->query($xsql);
      }else{
        $xsql = "UPDATE crossdockdet SET capturadas=capturadas+".$cantidad." WHERE crosdetid=".$crosdetid;
         $query = $this->db->query($xsql);
      }
    }
        
   if ($query){
      $respuesta = array(
            'error' => FALSE,
            'mensage' => 'Correcto');
    }else{
      $respuesta = array(
            'error' => TRUE,
            'mensage' => 'Error');
    }
/*      $respuesta = array(
            'error' => TRUE,
            'R' => $r,
            'mensage' => 'Error');*/
   $this->response( $respuesta );
}
// ************************************************************ 
  public function quitardet_post(){
    $data = $this->post();
    $cardetid=$data['cardetid'];

    $xsql="SELECT cantidad, crosdetid from cartaportedet where cardetid=".$cardetid;
    $query = $this->db->query($xsql);
    $row = $query->row();
    $cantidad=intval($row->cantidad);
    $crosdetid=intval($row->crosdetid);

    $xsql = "DELETE from cartaportedet where cardetid=".$cardetid;
    $query = $this->db->query($xsql);
    if ($query>0){
      $xsql="UPDATE crossdockdet SET estatus='Pend', capturadas=capturadas-".$cantidad." WHERE crosdetid=".$crosdetid;
      $query = $this->db->query($xsql);
      $xsql = "ALTER TABLE cartaportedet AUTO_INCREMENT=1";
      $query = $this->db->query($xsql);
    }
    if ($query){
      $respuesta = array(
            'error' => FALSE,
            'mensage' => 'Correcto');
    }else{
      $respuesta = array(
            'error' => TRUE,
            'mensage' => 'Error');
    }
    $this->response( $respuesta );
}
// ************************************************************     
  private function pruebas(){

    //***********************************************************
    if ($axion=="elimina"){
      if (!empty($cardetid)){
        
        //echo "CarDetId = ".$cardetid;

        $xsql="SELECT * from cartaportedet where cardetid=".$cardetid;
        //echo $xsql;
        $query=mysqli_query($con, $xsql);
        $row=mysqli_fetch_array($query);
        $cantidad = $row["Cantidad"];
        $doctraid = $row["DocTraId"];
        //echo "cantidad: ".$cantidad;
        //echo "doctraid: ".$doctraid;
        
        $estatus = "Pend";
        $query=mysqli_query($con, "DELETE from cartaportedet where cardetid=".$cardetid);
        if ($query>0){
          $xsql="UPDATE doctrailers SET estatus='".$estatus."', descargadas=descargadas-".$cantidad." WHERE doctraid=".$doctraid;
          //echo $xsql;
          $query=mysqli_query($con, $xsql);
          echo mysqli_error($con);

          $query=mysqli_query($con, "ALTER TABLE cartaportedet AUTO_INCREMENT=1");
        }
      }
    } 
  //*************************************************


  }




}
