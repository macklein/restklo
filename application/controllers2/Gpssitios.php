<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Gpssitios extends REST_Controller {


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
 

    $cond="gpssitios.sitid>0 ";
   if (!empty($buscar)){
      $cond .= " and (gpssitios.descrip LIKE '%".$buscar."%' ) "; 
    }


//        LEFT JOIN
//    t2 ON t1.c1 = t2.c1;

  $tables="gpssitios ";
  $campos="gpssitios.sitid, gpssitios.descrip, gpssitios.latitude, gpssitios.longitude ";
  $sWhere="WHERE $cond ";
//  $sWhere=" gpssitios.plaid=transplacas.plaid and $cond ";
  $sWhere.=" order by gpssitios.sitid ";
    

    $offset = ($page - 1) * $perPage;
    $xsql="SELECT count(*) AS numrows FROM $tables $sWhere";
   

 //    $respuesta = array('error' => TRUE, "message" => $xsql);

    $query = $this->db->query($xsql);
    $row = $query->row();
    $numrows = $row->numrows;
    if ($numrows>0){
       $paginas = ceil($numrows/$perPage);
    }else{
      $paginas = 1;
    }
    $xsql = "SELECT $campos FROM  $tables $sWhere LIMIT $offset,$perPage"; 


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
//     $respuesta = array('error' => TRUE, "message" => $xsql);
 
    $this->response( $respuesta );
  }
/************************************************************************/
 public function carga_post(){
  $this->datap = $this->post();
  $xsql ="Select sitid, descrip from gpssitios order by descrip";  
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
 public function seleid_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
//  $id=1;
  $xsql ="Select sitid, descrip, latitude, longitude from gpssitios where sitid=$id";  
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
  $this->data += [ "nvo" => "S" ];
  $this->db->insert('gpssitios',$this->data);
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
  $this->db->where( 'sitid', $id );
  $hecho = $this->db->update( 'gpssitios', $this->data);
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
    $this->data = array(
        'descrip'=>$dat->descrip,
        'latitude'=>$dat->latitude,
        'longitude'=>$dat->longitude
    );
  }

/************************************************************************/

}
