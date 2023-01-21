<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class fletelocal extends REST_Controller {


  var $datap,$data,$id,$empid;

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
        $conduser.=" and fletelocal.cliid=$ctes[0] ";
      }else{
        foreach ($ctes as $cte) {
          $n=$n+1;
          if ($n==1){
            $conduser.=" and ( fletelocal.cliid=$cte ";
          }else{
            $conduser.=" or fletelocal.cliid=$cte ";
          }
        }
        $conduser.=" ) ";
      }
    }
     
    if ($estatus=="Pendientes"){
      $cond="and fletelocal.estatus='Pend' ";
    }else{
      $cond="and (fletelocal.estatus<>'Pend') ";
      $cond.="and fletelocal.fecha>=$newf1 ";
      $cond.="and fletelocal.fecha<$newf2 ";
    }  

    if ($bmodo=='S'){
       if ($bcliid>0){
        $cond.="and fletelocal.cliid=$bcliid ";
       }
    }else{
      if (!empty($buscar)){
        $cond .= " and (clientes.nombre LIKE '%".$buscar."%' )"; 
      }
    }
 
//$tem=$cond;
//$cond='';
//$conduser='';


  $tables="fletelocal, clientes, transportistas";
  $campos="fletelocal.fletid, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha , left(fletelocal.comentario,30) as comentario, fletelocal.tipo, left(clientes.nombre,15) as nombre, left(transportistas.nombre,15) as nombretrans, fletelocal.cliid ";
  $sWhere="fletelocal.cliid=clientes.cliid and fletelocal.traid=transportistas.traid $cond $conduser";
  $sWhere.=" order by fletelocal.fletid desc ";
    

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

    $query = $this->db->query($xsql);
    if ($query) {
      $respuesta = array(
        'error' => FALSE,            
        'paginas' => $paginas, 
        'numrows' => $numrows, 
        'xsql' => $xsql,
        'items' => $query->result_array()
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query, 'nrr' => $sWhere, 'y1'=>$y1, 'y2'=>$y2);
    }

/*
      $respuesta = array(
        'error' => FALSE,            
        'page' => $estatus,
      );
*/


    $this->response( $respuesta );
  }
 
/************************************************************************/
 public function seleid_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
  $xsql ="Select fletid, cliid, fecha, traid, comentario, empreid, userid as empid, equipidt, equipidr, choid, plaid from fletelocal where fletid=$id";  
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
  public function alta_post(){
  $this->datap = $this->post();
  $this->losdatos();
  $fecha=date("Y-m-d H:i:s");
  $this->data += [ "fecha" => $fecha ];
  $this->data += [ "estatus" => "Pend" ];
  $this->data += [ "nvo" => "S" ];
  $this->data += [ "userid" => $this->empid ];  
  $this->db->insert('fletelocal',$this->data);
  $id=$this->db->insert_id();
  if ($id>0){
      $respuesta = array(
        'error' => FALSE,
        'id' => $this->data);
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
  $this->db->where( 'fletid', $id );
  $hecho = $this->db->update( 'fletelocal', $this->data);
  if ($hecho){
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
  private function losdatos(){
    $dat=json_decode($this->datap['datosm']);
    $this->empid=$dat->empid;
    $this->data = array(
        'cliid'=>$dat->cliid,
        'empreid'=>$dat->empreid,
        'traid'=>$dat->traid,
        'choid'=>$dat->choid,
        'comentario'=>$dat->comentario,
        'equipidt'=>$dat->equipidt,
        'equipidr'=>$dat->equipidr
    );
  }
/************************************************************************/

  public function enviarcarta_post(){
    $data = $this->post();
    $fletid = $data['fletid']; //
    $fecha=date("Y-m-d H:i:s");  
    $xsql = "UPDATE fletelocal SET estatus='Term', fechaenv='$fecha' WHERE fletid=".$fletid;
    $query = $this->db->query($xsql);
    $xrec=0;
    if ($query){
      $xrec=$fletid;
    }
    $respuesta = array('error' => FALSE, 'fletid' => $xrec);
    $this->response( $respuesta );
  
  }  

/************************************************************************/

}
