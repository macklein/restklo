<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
 
     
class Transportistas extends REST_Controller {

  var $datap,$data,$id;
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
    $criterio = $data['criterio'];  //No Usado
    $tables = "transportistas";
    $campos = "traid,nombre,email,rfc,telefono1,direccion,telefono2,contacto1 ";
    $sWhere = "nombre LIKE '%".$buscar."%'"; 
    $order = " Order by traid ";

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
    $xsql = "SELECT $campos FROM  $tables where $sWhere $order LIMIT $offset,$perPage";
    $query = $this->db->query($xsql);
    if ($query) {
      $respuesta = array(
        'error' => FALSE,            
        'paginas' => $paginas, 
        'numrows' => $numrows,
        'items' => $query->result_array()
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }
    $this->response( $respuesta );
  }
/************************************************************************/

 public function carga_post(){
    $conduser='Where traid>0 ';
    /*
    $tipouser = $data['tipouser'];  
    $transids = $data['transids'];  
    
    $trans = explode(",", $transids);
    $n=0;
    if ($tipouser=="Tra"){
      if (count($trans)==1){
        $conduser.=" and traid=$trans[0] ";
      }else{
        foreach ($trans as $tra) {
          $n=$n+1;
          if ($n==1){
            $conduser.=" and ( traid=$tra ";
          }else{
            $conduser.=" or traid=$tra ";
          }
        }
        $conduser.=" ) ";
      }
    }
*/

    $xsql = "Select traid,nombre from transportistas $conduser order by nombre";
    $query = $this->db->query($xsql);
    if ($query) {
        foreach ( $query->result() as $row ) {
            $records[] = array( 'label' => $row->nombre, 'value' => $row->traid );
        } 
      $respuesta = array(
        'error' => FALSE,            
        'records' => $records
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }
    $this->response( $respuesta );
  }
/************************************************************************/

public function carganew_post(){
  $data = $this->post();
  $tiposal = $data['tiposal']; 
  $conduser='Where traid>0 ';
  if ($tiposal==1 or $tiposal==2){
    $conduser='Where traid=13 ';
  }
  $xsql = "Select traid,nombre from transportistas $conduser order by nombre";
  $query = $this->db->query($xsql);
  if ($query) {
      foreach ( $query->result() as $row ) {
          $records[] = array( 'label' => $row->nombre, 'value' => $row->traid );
      } 
    $respuesta = array(
      'error' => FALSE,            
      'records' => $records
    );
  } else {
    $respuesta = array('error' => TRUE, "message" => $query);
  }
  $this->response( $respuesta );
}
/************************************************************************/

public function cargafact_post(){
  $data = $this->post();
  $traid = $data['transids']; 
  $confact = $data['confact']; 
  $tipouser = $data['tipouser']; 

  $conduser="Where confact='S' ";
  if ($tipouser=="Tra"){
    $conduser="Where confact='X' ";
    if ($confact='S' && $traid>0){
      $conduser="Where confact='S' and traid=$traid";
    }
  }


  $xsql = "Select traid,nombre from transportistas $conduser order by nombre";
  $query = $this->db->query($xsql);
  if ($query) {
      foreach ( $query->result() as $row ) {
          $records[] = array( 'label' => $row->nombre, 'value' => $row->traid );
      } 
    $respuesta = array(
      'error' => FALSE,            
      'records' => $records
    );
  } else {
    $respuesta = array('error' => TRUE, "message" => $query);
  }
  $this->response( $respuesta );
}
/************************************************************************/

public function cargafactn_post(){
  $data = $this->post();  //Solo Armando
  $traid = $data['transids']; 
  $traid = 13;
  $confact = $data['confact']; 
  $tipouser = $data['tipouser']; 
  $conduser = "Where traid=$traid";
  /*
  $conduser="Where confact='S' ";
  if ($tipouser=="Tra"){
    $conduser="Where confact='X' ";
    if ($confact='S' && $traid>0){
      $conduser="Where confact='S' and traid=$traid";
    }
  }*/


  $xsql = "Select traid,nombre from transportistas $conduser order by nombre";
  $query = $this->db->query($xsql);
  if ($query) {
      foreach ( $query->result() as $row ) {
          $records[] = array( 'label' => $row->nombre, 'value' => $row->traid );
      } 
    $respuesta = array(
      'error' => FALSE,            
      'records' => $records
    );
  } else {
    $respuesta = array('error' => TRUE, "message" => $query);
  }
  $this->response( $respuesta );
}
/************************************************************************/




 public function carga0_post(){
    $data = $this->post();
    $conduser='Where traid>0 ';
    $xsql = "Select traid,nombre from transportistas $conduser order by nombre";
    $query = $this->db->query($xsql);
    if ($query) {
        foreach ( $query->result() as $row ) {
            $records[] = array( 'label' => $row->nombre, 'value' => $row->traid );
        } 
      $respuesta = array(
        'error' => FALSE,            
        'records' => $records
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }
    $this->response( $respuesta );
  }

/************************************************************************/

 public function alta_post(){
  $this->datap = $this->post();
  $this->losdatos();
  $this->data += [ "nvo" => "S" ];
  $this->db->insert('transportistas',$this->data);
  $id=$this->db->insert_id();
  if ($id>0){
      $respuesta = array(
        'error' => FALSE,
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
  $this->db->where( 'traId', $id );
  $hecho = $this->db->update( 'transportistas', $this->data);
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
    $this->data = array(
        'nombre'=>strtoupper($dat->nombre),
        'direccion'=>strtoupper($dat->direccion),
        'rfc'=>strtoupper($dat->tfc),
        'telefono1'=>$dat->telefono1,
        'telefono2'=>$dat->telefono2,
        'contacto1'=>strtoupper($dat->contacto1),
        'email'=>$dat->email,
    );
  }
/************************************************************************/
 public function servspend_post(){
  $this->datap = $this->post();
  $traid=$this->datap['btraid'];
  $empreid=$this->datap['empreid'];
  $cliid=$this->datap['bcliid'];
  $moneda=$this->datap['moneda'];
  
  $respuesta = array('error' => FALSE, 'empreid'=>$empreid, 'traid'=>$traid, 'cliid'=>$cliid);

//  $this->response( $respuesta );


// Trailes y Cartas ya cargadas a la Factura
//*****************************************************

  $xtotservs=0;
  $records=[];
//  $xsql1="select a.*,DATE_FORMAT(a.fecha,'%d/%m/%Y') AS fecha1, b.nombre,c.nombre as nombretrans,a.moneda from doctrailers a,clientes b, transportistas c where a.cliid=b.cliid and a.traid=c.traid and a.afactura='S' and a.traid=".$traid." and a.empreid=".$empreid;
  $xsql1="select a.*,DATE_FORMAT(a.fecha,'%d/%m/%Y') AS fecha1, b.nombre,c.nombre as nombretrans,a.moneda from doctrailers a,clientes b, transportistas c where a.cliid=b.cliid and a.traid=c.traid and a.afactura='S' and a.traid=".$traid;
  //echo $xsql;
  $query = $this->db->query($xsql1);
  if ($query) {
    foreach ( $query->result() as $row ) {
        $records[] = array( 'tipo'=>'Trail', 'fecha'=>$row->fecha1, 'id'=>$row->DocTraId, 'cliente'=>substr($row->nombre,0,14), 'transportista'=>substr($row->nombretrans,0, 14), 'pedimento'=>$row->Pedimento, 'caja'=>$row->Caja, 'precio'=>$row->Precio, 'moneda'=>$row->moneda);
    } 
  }
  $cond="(a.tipo='Cros' or a.tipo='Alma' or a.tipo='Ent') ";
//  $xsql2="select a.*,DATE_FORMAT(a.fecha,'%d/%m/%Y') AS fecha1,b.nombre,c.nombre as nombretrans,a.moneda from cartaporte a,clientes b, transportistas c where a.cliid=b.cliid and a.traid=c.traid and a.afactura='S' and ".$cond." and a.traid=".$traid." and a.empreid=".$empreid;
  $xsql2="select a.*,DATE_FORMAT(a.fecha,'%d/%m/%Y') AS fecha1,b.nombre,c.nombre as nombretrans,a.moneda from cartaporte a,clientes b, transportistas c where a.cliid=b.cliid and a.traid=c.traid and a.afactura='S' and ".$cond." and a.traid=".$traid;
  //echo $xsql;
  $query2 = $this->db->query($xsql2);
  if ($query2) {
    foreach ( $query2->result() as $row ) {
        $tip=$row->Tipo;
        $records[] = array( 'tipo'=>'Cp'.$tip, 'fecha'=>$row->fecha1, 'id'=>$row->CarId, 'cliente'=>substr($row->nombre,0,14), 'transportista'=>substr($row->nombretrans,0, 14), 'pedim'=>'', 'caja'=>'', 'precio'=>$row->Precio, 'moneda'=>$row->moneda);
    } 
  }


    

//*************************************************************

    $query3="doctrailers.estatus<>'Pend' ";
    if ($empreid>0){
     // $query3 .=" and doctrailers.empreid=".$empreid; 
    }
    if ($cliid>0){
      $query3 .=" and doctrailers.cliid=".$cliid; 
    }
    if ($traid>0){
      $query3 .=" and doctrailers.traid=".$traid; 
    }


  $tables="doctrailers, transportistas, clientes";
  $campos="doctrailers.doctraid, DATE_FORMAT(doctrailers.fecha,'%d/%m/%Y') AS fecha, doctrailers.enviado, left(transportistas.nombre,14) as nombretrans, left(clientes.nombre,14) as nombre, doctrailers.pedimento, doctrailers.caja, doctrailers.piezas, doctrailers.descargadas, doctrailers.moneda ";


  $sWhere=" doctrailers.traid=transportistas.traid and doctrailers.cliid=clientes.cliid ";
  $sWhere .= " and doctrailers.TipoFactCp<>'F' and doctrailers.afactura='N' and ordid=0 and ".$query3;
  $sWhere.=" order by doctrailers.doctraid desc ";

  $xsql3="SELECT $campos FROM  $tables where $sWhere LIMIT 20";
  $query3 = $this->db->query($xsql3);






  $query4="(cartaporte.tipo='Cros' or cartaporte.tipo='Alma' or cartaporte.tipo='Ent') ";
  if ($empreid>0){
   // $query4 .=" and cartaporte.empreid=".$empreid;  
  }
  if ($cliid>0){
    $query4 .=" and cartaporte.cliid=".$cliid;  
  }
  if ($traid>0){
    $query4 .=" and cartaporte.traid=".$traid;  
  }

  $tables="cartaporte, clientes, transportistas";
  $campos="cartaporte.carid, DATE_FORMAT(cartaporte.fecha,'%d/%m/%Y') AS fecha, cartaporte.destino, cartaporte.impresa, cartaporte.tipo, left(clientes.nombre,14) as nombre, left(transportistas.nombre,14) as nombretrans, cartaporte.moneda";

  $sWhere="cartaporte.cliid=clientes.cliid and cartaporte.traid=transportistas.traid";
  $sWhere.= " and cartaporte.afactura='N' and ordid=0 and ".$query4;  //Pendiente Cross
  $sWhere.=" order by cartaporte.carid desc ";
  

  $xsql4="SELECT $campos FROM  $tables where $sWhere LIMIT 20";
  $query4 = $this->db->query($xsql4);


  if ($query) {
      $respuesta = array('error' => FALSE, "trails" => $query->result_array(), "cartas" => $query2->result_array(), "trailerspend" => $query3->result_array(), 'sql1'=>$xsql1, 'sql2'=>$xsql2, 'sql3'=>$xsql3, "cartaspend" => $query4->result_array(), 'sql4'=>$xsql4, 'records'=>$records);
  }
  $this->response( $respuesta );


  }


public function servsdet_post(){
  
  $this->datap = $this->post();
  $ordid=$this->datap['ordid'];

  $respuesta = array('error' => FALSE);

// Trailes y Cartas ya cargadas a la Factura
//*****************************************************
  $xtotservs=0;
  $records=[];
  $xsql1="select a.*,DATE_FORMAT(a.fecha,'%d/%m/%Y') AS fecha1, b.nombre,c.nombre as nombretrans,a.moneda from doctrailers a,clientes b, transportistas c where a.cliid=b.cliid and a.traid=c.traid and a.ordid=".$ordid;
  //echo $xsql;
  $query = $this->db->query($xsql1);
  if ($query) {
    foreach ( $query->result() as $row ) {
        $records[] = array( 'tipo'=>'Trail', 'fecha'=>$row->fecha1, 'id'=>$row->DocTraId, 'cliente'=>substr($row->nombre,0,14), 'transportista'=>substr($row->nombretrans,0, 14), 'pedimento'=>$row->Pedimento, 'caja'=>$row->Caja, 'precio'=>$row->Precio);
    } 
  }
  $cond="(a.tipo='Cros' or a.tipo='Alma' or a.tipo='Ent') ";
  $xsql2="select a.*,DATE_FORMAT(a.fecha,'%d/%m/%Y') AS fecha1,b.nombre,c.nombre as nombretrans,a.moneda from cartaporte a,clientes b, transportistas c where a.cliid=b.cliid and a.traid=c.traid and ".$cond." and a.ordid=".$ordid;
  //echo $xsql;
  $query2 = $this->db->query($xsql2);
  if ($query2) {
    foreach ( $query2->result() as $row ) {
        $tip=$row->Tipo;
        $records[] = array( 'tipo'=>'Cp'.$tip, 'fecha'=>$row->fecha1, 'id'=>$row->CarId, 'cliente'=>substr($row->nombre,0,14), 'transportista'=>substr($row->nombretrans,0, 14), 'pedim'=>'', 'caja'=>'', 'precio'=>$row->Precio);
    } 
  }

  if ($query) {
      $respuesta = array('error' => FALSE, 'records'=>$records);
  }
  $this->response( $respuesta );


  }
  public function xrgenorden_post(){
    $respuesta = array('error' => TRUE, 'totf'=>'456');
    $this->response( $respuesta );

  }

 public function xgenorden_post(){
    $this->datap = $this->post();
    $empreid=$this->datap['empreid'];
    $traid=$this->datap['traid'];    
    $piva=$this->datap['piva'];
    $moneda=$this->datap['moneda'];
    $xsql="select * from doctrailers where afactura='S' and traid=".$traid." and empreid=".$empreid;
    $respuesta = array('error' => TRUE, 'totf'=>$xsql, 'datax'=>$moneda);
    $this->response( $respuesta );

  }

 public function genorden_post(){
    $this->datap = $this->post();
    date_default_timezone_set('America/Chihuahua'); 
    $empreid=$this->datap['empreid'];
    $traid=$this->datap['traid'];    
    $piva=$this->datap['piva'];
    $moneda=$this->datap['moneda'];
    $xsql="select * from doctrailers where afactura='S' and traid=".$traid; //." and empreid=".$empreid;
    $respuesta = array('error' => TRUE);
    $query = $this->db->query($xsql);
    $precio=0;
    $totf = 0;
    $totr = 0;
    $tott = 0; //Total Trailers
    foreach ( $query->result() as $row ) {
      $precio=$row->Precio;
      $totf=$totf+$precio;
      $totr=$totr+1;
      $tott=$tott+1;
    }
    //CartasPorte
    $xsql="select * from cartaporte where afactura='S' and traid=".$traid; //." and empreid=".$empreid;
    $query = $this->db->query($xsql);

    $precio=0;
    $totc = 0; //Total CartaPorte
    foreach ( $query->result() as $row ) {
      $precio=$row->Precio;
      $totf=$totf+$precio;
      $totr=$totr+1;
      $totc=$totc+1;
    }
    $iva = $totf*.16;
    if ($piva=='8'){
        $iva = $totf*.08;
    }
    $iva4 = $totf*.04;
    $total = $totf+$iva-$iva4;
    $fecha=date("Y-m-d H:i:s");
    $data = array(
        'fecha'=>$fecha,
        'traid'=>$traid,
        'pagada'=>'N',
        'conpdf'=>'N',
        'conxml'=>'N',
        'piva'=>$piva,
        'empreid'=>$empreid,
        'subtotal'=>$totf,
        'montoiva'=>$iva,
        'montoret'=>$iva4,
        'total'=>$total,
        'moneda'=>$moneda,
        'trailers'=>$tott,
        'cartas'=>$totc,
        'servs'=>$totr,
    );
    $this->db->insert('transordens',$data);
    $ordid=$this->db->insert_id();
    
    if ($ordid>0){
      // Trailers
      $xsql="update doctrailers set afactura='T', ordid=".$ordid." where afactura='S' and traid=".$traid;
      $query = $this->db->query($xsql);
    
    //Cartas
      $xsql="update cartaporte set afactura='T', ordid=".$ordid." where afactura='S' and traid=".$traid;
      $query = $this->db->query($xsql);

      $respuesta = array('error' => FALSE, 'sql'=>$xsql);

    }

    $respuesta = array('error' => TRUE, 'totf'=>$totf, 'datax'=>$data, 'ordid'=>$ordid);
    
  $this->response( $respuesta );


}

//*****************************************************************

  public function quitamov_post(){
    $this->datap = $this->post();
    $id=$this->datap['id'];
    $tipo=$this->datap['tipo'];
    if ($tipo=="Trail"){
      $xsql="update doctrailers set afactura='N', moneda='' where doctraid=".$id;
      $query = $this->db->query($xsql);
    }else{
      $xsql="update cartaporte set afactura='N', moneda='' where carid=".$id;
      $query = $this->db->query($xsql);
    }
    $respuesta = array('error' => FALSE, 'sql'=>$xsql);
    $this->response( $respuesta );

  }
  
  public function cargatrail_post(){
    $this->datap = $this->post();
    $doctraid=$this->datap['doctraid'];
    $traid=$this->datap['traid'];  //Transportista
    $moneda=$this->datap['moneda'];
    $mon='P';
    if ($moneda=='Dls'){
       $mon='D';
    }    
    $xsql = "select * from transpre where traid=".$traid." and TipoMov='Trailer'";
    $query = $this->db->query($xsql);
    $row = $query->row();
    $precio = $row->Precio;
    $monpre = $row->Moneda;
    $ent = 'Uno';
    if ($moneda=='' or ($mon==$monpre)){
       $moneda=$monpre;
       $ent = 'Dos';
       $xsql="update doctrailers set afactura='S', precio=".$precio.", moneda='".$moneda."' where doctraid=".$doctraid;
       $query = $this->db->query($xsql);
    }
    $respuesta = array('error' => FALSE, 'precio'=>$precio, 'doctra'=>$doctraid, 'moneda'=>$moneda, 'monpre'=>$monpre);
//    $respuesta = array('error' => FALSE, 'precio'=>$precio, 'doctra'=>$doctraid, 'moneda'=>$moneda, 'Otrox'=>$xsql, 'monpre'=>$monpre);
    $this->response( $respuesta );

  }

  public function cargacarta_post(){
    $this->datap = $this->post();
    $carid=$this->datap['carid'];
    $traid=$this->datap['traid'];  //Transportista
    $moneda=$this->datap['moneda'];
    $mon='P';
    if ($moneda=='Dls'){
       $mon='D';
    } 

    $xsql="select * from cartaporte where carid=".$carid;
    $query = $this->db->query($xsql);
    $row = $query->row();
    $tipo = $row->Tipo;
    $cliid = $row->CliId;
    $destino = $row->Destino;
    $tipor="";
    if ($tipo=="Alma"){
      $tipor="CpAlmEmb";
    }
    if ($tipo=="Cros"){
      if ($destino="Embarque"){
        $tipor="CpCrEmb";
      } else {
        $tipor="CpCrAlm";
      }   
    }
    if ($tipo=="Ent"){
      $tipor="CpEntAlm";
    }

    $xsql = "select * from transpre where traid=".$traid." and cliid=".$cliid." and TipoMov='".$tipor."'";
    $query = $this->db->query($xsql);
    $row = $query->row();
    $precio = $row->Precio;
    $moneda = $row->Moneda;

    if ($precio==0){
      $xsql = "select * from transpre where traid=".$traid." and TipoMov='".$tipor."'";
      $query = $this->db->query($xsql);
      $row = $query->row();
      $precio = $row->Precio;
      $moneda = $row->Moneda;
    }
    if ($moneda=='' or ($mon==$monpre)){
      $moneda=$monpre;
      $xsql = "update cartaporte set afactura='S', precio=".$precio.", moneda=".$moneda." where carid=".$carid;
      $query = $this->db->query($xsql);
    }
    $respuesta = array('error' => FALSE, 'precio'=>$precio, 'moneda'=>$moneda);
    $this->response( $respuesta );


  }
  
/************************************************************************/
 public function servsfacts_post(){

  $respuesta = array('error' => FALSE);
  
  $this->datap = $this->post();
  $traid  = $this->datap['traid'];
  $empreid  = $this->datap['empreid'];
  $tipouser  = $this->datap['tipouser'];
  $estatus  = $this->datap['estatus'];
  $buscar  = $this->datap['buscar'];
  $bmodo  = $this->datap['bmodo'];
  

//**********************************************
  $bfecha1 = $this->datap['bfecha1']; 
  $bfecha2 = $this->datap['bfecha2']; 
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
  $cond=' and a.ordid>0 ';
  $cond2=' and numfac>0 ';
  $n=0;
  if ($tipouser=="Tra"){
    $cond.=" and a.traid=$traid ";
    $cond2 =" and Empreid=$traid ";
  }
  if ($empreid>0){
    $cond .=" and a.empreid=$empreid ";
    $cond2 .=" and cliid=$empreid ";
  }
  if ($estatus=="Pendientes"){
    $cond =" and a.pagada='N' ";    
    $cond2 =" and pagada='N' ";    
  }else{
    $cond .=" and a.pagada='S' "; 
    $cond .=" and a.fecha>=$newf1 ";
    $cond .=" and a.fecha<$newf2 ";
    $cond2 .=" and pagada='S' "; 
    $cond2 .=" and fecha>=$newf1 ";
    $cond2 .=" and fecha<$newf2 ";
  }  
  if ($tipouser=="Tra"){
    $cond=" and a.traid=$traid and a.pagada='N' ";
    $cond2=" and Empreid=$traid and pagada='N' ";
  }
  if ($bmodo=='S'){
     if ($traid>0){
      $cond.=" and a.traid=$traid ";
      $cond2.=" and Empreid=$traid ";
     }
     if ($tipouser=="Tra"){
      $cond =" and a.traid=$traid ";
      $cond .=" and a.fecha>=$newf1 ";
      $cond .=" and a.fecha<$newf2 ";
      $cond2 =" and Empreid=$traid ";
      $cond2 .=" and fecha>=$newf1 ";
      $cond2 .=" and fecha<$newf2 ";
    }
    if ($empreid>0){
      $cond .=" and a.empreid=$empreid ";
      $cond2 .=" and cliid=$empreid ";
    }
  }
  if (!empty($buscar)){
    $cond .= " and (b.nombre LIKE '%".$buscar."%' or a.referencia LIKE '%".$buscar."%')"; 
    $cond2 .= " and (nombre LIKE '%".$buscar."%' or referencia LIKE '%".$buscar."%')"; 
  }

  $xsql1="select a.ordid, a.referencia, a.empreid, DATE_FORMAT(a.fecha,'%d/%m/%Y') AS fecha, a.conpdf, a.conxml, a.subtotal, left(b.nombre,15) as nombretrans, a.servs, a.montoiva, a.montoret, a.total, a.pagada, DATE_FORMAT(a.fechasubida,'%d/%m/%Y') AS fechasubida from transordens a, transportistas b Where a.traid=b.traid ".$cond." order by a.ordid desc ";
  $respuesta = array('error' => FALSE, 'tra'=>$traid, 'sql'=>$xsql1);

  $query = $this->db->query($xsql1);
  if ($query) {
    foreach ( $query->result() as $row ) {
      //TipoCfdi="N" para las facturas de los Transportistas
        $records[] = array( 'ordid'=>$row->ordid, 'referencia'=>$row->referencia, 'fecha'=>$row->fecha, 'conpdf'=>$row->conpdf, 'conxml'=>$row->conxml, 'subtotal'=>$row->subtotal, 'tipocfdi'=>'N', 'nombretrans'=>$row->nombretrans, 'servs'=>$row->servs, 'montoiva'=>$row->montoiva, 'montoret'=>$row->montoret, 'total'=>$row->total, 'pagada'=>$row->pagada, 'fechasubida'=>$row->fechasubida, 'empreid'=>$row->empreid, 'numfac'=>0, 'cliid'=>0 );
    } 
//    $cond2 =" Empreid=$traid ";
$cond2 .= " and tipofac='I' ";
    $xsql2="select CONCAT(serie,facidN) as ordid, carid as referencia, empreid, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha, 'S' as conpdf, 'S' as conxml, subtotal, left(nombre,15) as nombretrans, 1 as servs, iva as montoiva, retiva as montoret, tipocfdi, total, pedid, pagada, DATE_FORMAT(fecha,'%d/%m/%Y') AS fechasubida, numfac, cliid from trafacturas Where cancel<>'S' and factura=0 and numfac>0 ".$cond2." order by facidn desc ";
    $query2 = $this->db->query($xsql2);
    foreach ( $query2->result() as $row ) {
      $ref=$row->referencia; 
      $servs = $row->servs;
      if ($row->tipocfdi=="P"){
        $ref = "CompPag".$row->ordid;
        $servs = $row->pedid;
      }
        $records[] = array( 'ordid'=>$row->ordid, 'referencia'=>$ref, 'fecha'=>$row->fecha, 'conpdf'=>$row->conpdf, 'conxml'=>$row->conxml, 'subtotal'=>$row->subtotal, 'tipocfdi'=>$row->tipocfdi, 'nombretrans'=>$row->nombretrans, 'servs'=>$servs, 'montoiva'=>$row->montoiva, 'montoret'=>$row->montoret, 'total'=>$row->total, 'pagada'=>$row->pagada, 'fechasubida'=>$row->fechasubida, 'empreid'=>$row->empreid, 'numfac'=>$row->numfac, 'cliid'=>$row->cliid );
    }
    $respuesta = array('error' => FALSE, "items" => $records, 'sql'=>$xsql2);
  //  $respuesta = array('error' => FALSE, 'sql'=>$xsql2);
    
  }

  
  //$query = $this->db->query($xsql1);
  //if ($query) {
  //    $respuesta = array('error' => FALSE, "items" => $query->result_array(), 'sql'=>$xsql1);
 // }

  $this->response( $respuesta );

  }

 public function cargaformasp_post(){
  $respuesta = array('error' => FALSE);
  $this->datap = $this->post();
  $empreid  = $this->datap['empreid'];
  $xsql1="select formid,descripform from gasformasp where empreid=".$empreid." order by formid";
  $query = $this->db->query($xsql1);
  $records=[];
  if ($query) {
      foreach ( $query->result() as $row ) {
            $records[] = array( 'label' => $row->descripform, 'value' => $row->formid );
        } 
      $respuesta = array('error' => FALSE, "formasp" => $records);
  }
  $this->response( $respuesta );
  }
 
  public function guardapago_post(){
    $respuesta = array('error' => TRUE);
    date_default_timezone_set('America/Chihuahua'); 
    $this->datap = $this->post();
    $ordid  = $this->datap['ordid'];
    $empreid  = $this->datap['empreid'];
    $numfac  = $this->datap['numfac'];    
    $cliid  = $this->datap['cliid'];    
    
    $fechapago  = $this->datap['fechapago'];
    $formapago  = $this->datap['formapago'];
    $tipopago  = $this->datap['tipopago'];
    
    if ($numfac>0){

      $xsql = "select empreid,subtotal,iva as montoiva,retiva as montoret,total from trafacturas where numfac=".$numfac;
    }else{
        $xsql = "select empreid,subtotal,montoiva,montoret,total from transordens where ordid=".$ordid;
    }

    $query = $this->db->query($xsql);
    $rw = $query->row();
    
    $empreid0 = $rw->empreid;  
    $subtotal = $rw->subtotal;  
    $montoiva = $rw->montoiva;  
    $montoret = $rw->montoret;  
    $total    = $rw->total;  
    
 //   $xsql="update transordens set fechapago='".$fechapago."', pagada='S', formid='".$formapago."', empreidp='".$empreid."', tipo='".$tipopago."' where ordid=".$ordid;
    if ($numfac>0){
      $empreid0=$cliid;
      $xsql="update trafacturas set fechapago='".$fechapago."', pagada='S', formid='".$formapago."', empreidp='".$empreid."', tipo='".$tipopago."' where numfac=".$numfac;
    }else{
      $xsql="update transordens set fechapago='".$fechapago."', pagada='S', formid='".$formapago."', empreidp='".$empreid."', tipo='".$tipopago."' where ordid=".$ordid;
    }
 /*   $respuesta = array('error' => TRUE, 'sql' => $xsql);
    $this->response( $respuesta );
 */

    $equipid=0;
    $ctaid = 0;
    $desc = "ERROR EN CTA";
    $cta = "ERROR EN CTA";
   
    $query = $this->db->query($xsql);
    if ($query) {
      if ($empreid0==1 and $tipopago==1){
        $equipid=40;
        $ctaid = 5;
        $desc = "(GASTOS DE TRANSPORTE) FLETES LOCALES";
        $cta = "5103-301-001";
      }
      if ($empreid0==1 and $tipopago==2){
        $equipid=40;
        $ctaid = 6;
        $desc = "(GASTOS DE TRANSPORTE) FLETES IMPORTACION";
        $cta = "5103-301-002";
      }

      if ($empreid0==2 and $tipopago==1){
        $equipid=41;
        $ctaid = 130;
        $desc = "(GASTOS DE TRANSPORTE) FLETES LOCALES";
        $cta = "602-01-001-001";
      }
      if ($empreid0==2 and $tipopago==2){
        $equipid=41;
        $ctaid = 131;
        $desc = "(GASTOS DE TRANSPORTE) FLETES IMPORTACION";
        $cta = "602-01-001-002";
      }

      if ($total>0){
          $cantidad=1;
          $unidid=8;
          $empid = 2;
          $clasid = 1;
          $factura = $cta;
          $pagada='S';
          $formidt=$formapago;
          $fechapago = $fechapago;
          $fecha=date("Y-m-d H:i:s");
         $xsql = "INSERT INTO gastos(fecha, equipid, clasid, ctaid, cantidad, unidid, subtotal, montoiva, montoret, total, factura, descripgas, empreid, empid, formid, empreidp, fechapago, estatus, pagada, tipo) VALUES('$fecha', '$equipid', '$clasid', '$ctaid', '$cantidad', '$unidid', '$subtotal', '$montoiva', '$montoret', '$total', '$factura', '$desc', '$empreid0', '$empid', '$formidt', '$empreid', '$fechapago', 'Act', '$pagada', '$tipopago');";
          $query = $this->db->query($xsql);
          if ($query) {
            $respuesta = array('error' => FALSE, 'sql' => $xsql);
          }

     //     $respuesta = array('error' => FALSE, 'sql' => $xsql);
        
      }

    }
    $this->response( $respuesta );

  }

}
