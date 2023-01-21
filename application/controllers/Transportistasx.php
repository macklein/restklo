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
    $data = $this->post();
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
  
  $respuesta = array('error' => FALSE, 'empreid'=>$empreid, 'traid'=>$traid, 'cliid'=>$cliid);

//  $this->response( $respuesta );


// Trailes y Cartas ya cargadas a la Factura
//*****************************************************
  $xtotservs=0;
  $records=[];
  $xsql1="select a.*,DATE_FORMAT(a.fecha,'%d/%m/%Y') AS fecha1, b.nombre,c.nombre as nombretrans from doctrailers a,clientes b, transportistas c where a.cliid=b.cliid and a.traid=c.traid and a.afactura='S' and a.traid=".$traid." and a.empreid=".$empreid;
  //echo $xsql;
  $query = $this->db->query($xsql1);
  if ($query) {
    foreach ( $query->result() as $row ) {
        $records[] = array( 'tipo'=>'Trail', 'fecha'=>$row->fecha1, 'id'=>$row->DocTraId, 'cliente'=>substr($row->nombre,0,14), 'transportista'=>substr($row->nombretrans,0, 14), 'pedimento'=>$row->Pedimento, 'caja'=>$row->Caja, 'precio'=>$row->Precio);
    } 
  }
  $cond="(a.tipo='Cros' or a.tipo='Alma' or a.tipo='Ent') ";
  $xsql2="select a.*,DATE_FORMAT(a.fecha,'%d/%m/%Y') AS fecha1,b.nombre,c.nombre as nombretrans from cartaporte a,clientes b, transportistas c where a.cliid=b.cliid and a.traid=c.traid and a.afactura='S' and ".$cond." and a.traid=".$traid." and a.empreid=".$empreid;
  //echo $xsql;
  $query2 = $this->db->query($xsql2);
  if ($query2) {
    foreach ( $query2->result() as $row ) {
        $tip=$row->Tipo;
        $records[] = array( 'tipo'=>'Cp'.$tip, 'fecha'=>$row->fecha1, 'id'=>$row->CarId, 'cliente'=>substr($row->nombre,0,14), 'transportista'=>substr($row->nombretrans,0, 14), 'pedim'=>'', 'caja'=>'', 'precio'=>$row->Precio);
    } 
  }


    

//*************************************************************

    $query3="doctrailers.estatus<>'Pend' ";
    if ($empreid>0){
      $query3 .=" and doctrailers.empreid=".$empreid; 
    }
    if ($cliid>0){
      $query3 .=" and doctrailers.cliid=".$cliid; 
    }
    if ($traid>0){
      $query3 .=" and doctrailers.traid=".$traid; 
    }

  $tables="doctrailers, transportistas, clientes";
  $campos="doctrailers.doctraid, DATE_FORMAT(doctrailers.fecha,'%d/%m/%Y') AS fecha, doctrailers.enviado, left(transportistas.nombre,14) as nombretrans, left(clientes.nombre,14) as nombre, doctrailers.pedimento, doctrailers.caja, doctrailers.piezas, doctrailers.descargadas ";


  $sWhere=" doctrailers.traid=transportistas.traid and doctrailers.cliid=clientes.cliid ";
  $sWhere .= " and doctrailers.afactura='N' and ordid=0 and ".$query3;
  $sWhere.=" order by doctrailers.doctraid desc ";

  $xsql3="SELECT $campos FROM  $tables where $sWhere LIMIT 20";
  $query3 = $this->db->query($xsql3);






  $query4="(cartaporte.tipo='Cros' or cartaporte.tipo='Alma' or cartaporte.tipo='Ent') ";
  if ($empreid>0){
    $query4 .=" and cartaporte.empreid=".$empreid;  
  }
  if ($cliid>0){
    $query4 .=" and cartaporte.cliid=".$cliid;  
  }
  if ($traid>0){
    $query4 .=" and cartaporte.traid=".$traid;  
  }

  $tables="cartaporte, clientes, transportistas";
  $campos="cartaporte.carid, DATE_FORMAT(cartaporte.fecha,'%d/%m/%Y') AS fecha, cartaporte.destino, cartaporte.impresa, cartaporte.tipo, left(clientes.nombre,14) as nombre, left(transportistas.nombre,14) as nombretrans";

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



  public function genorden_post(){

    $this->datap = $this->post();
    $empreid=$this->datap['empreid'];
    $traid=$this->datap['traid'];
    $xsql="select * from doctrailers where afactura='S' and traid=".$traid." and empreid=".$empreid;
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
    $xsql="select * from cartaporte where afactura='S' and traid=".$traid." and empreid=".$empreid;
    $query = $this->db->query($xsql);

    $precio=0;
    $totc = 0; //Total CartaPorte
    foreach ( $query->result() as $row ) {
      $precio=$row->Precio;
      $totf=$totf+$precio;
      $totr=$totr+1;
      $totc=$totc+1;
    }

    $fecha=date("Y-m-d H:i:s");
    $data = array(
        'fecha'=>$fecha,
        'traid'=>$traid,
        'pagada'=>'N',
        'empreid'=>$empreid,
        'subtotal'=>$totf,
        'trailers'=>$tott,
        'cartas'=>$totc,
        'servs'=>$totr,
    );
    $this->db->insert('transordens',$data);
    $ordid=$this->db->insert_id();
    
    if ($ordid>0){
      // Trailers
      $xsql="update doctrailers set afactura='T', ordid=".$ordid." where afactura='S' and traid=".$traid." and empreid=".$empreid;
      $query = $this->db->query($xsql);
    
    //Cartas
      $xsql="update cartaporte set afactura='T', ordid=".$ordid." where afactura='S' and traid=".$traid." and empreid=".$empreid;
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
      $xsql="update doctrailers set afactura='N' where doctraid=".$id;
      $query = $this->db->query($xsql);
    }else{
      $xsql="update cartaporte set afactura='N' where carid=".$id;
      $query = $this->db->query($xsql);
    }
    $respuesta = array('error' => FALSE, 'sql'=>$xsql);
    $this->response( $respuesta );

  }
  
  public function cargatrail_post(){
    $this->datap = $this->post();
    $doctraid=$this->datap['doctraid'];
    $traid=$this->datap['traid'];  //Transportista

    
    $xsql = "select * from transpre where traid=".$traid." and TipoMov='Trailer'";
    $query = $this->db->query($xsql);
    $row = $query->row();
    $precio = $row->Precio;


    $xsql="update doctrailers set afactura='S', precio=".$precio." where doctraid=".$doctraid;
    $query = $this->db->query($xsql);
    
    $respuesta = array('error' => FALSE, 'precio'=>$precio);
    $this->response( $respuesta );

  }

  public function cargacarta_post(){
    $this->datap = $this->post();
    $carid=$this->datap['carid'];
    $traid=$this->datap['traid'];  //Transportista

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
    if ($precio==0){
      $xsql = "select * from transpre where traid=".$traid." and TipoMov='".$tipor."'";
      $query = $this->db->query($xsql);
      $row = $query->row();
      $precio = $row->Precio;
    }

    $xsql = "update cartaporte set afactura='S', precio=".$precio." where carid=".$carid;
    $query = $this->db->query($xsql);

    $respuesta = array('error' => FALSE, 'precio'=>$precio);
    $this->response( $respuesta );


  }
  




}
