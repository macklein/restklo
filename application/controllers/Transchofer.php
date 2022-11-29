<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Transchofer extends REST_Controller {


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

    $tables = "transchofer a left join transportistas b on a.traid=b.traid left join empleados c on a.empid=c.empid ";
    $sWhere = "(a.nombrechofer LIKE '%".$buscar."%' or b.nombre LIKE '%".$buscar."%' or c.nombre LIKE '%".$buscar."%')"; 
    $order = " Order by choid ";

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
//    $xsql = "SELECT $campos FROM  $tables where $sWhere $order LIMIT $offset,$perPage";
//    $query = $this->db->query($xsql);
//    $paginas=1;
//    $numrows=1;


$xsql = "Select a.choid,a.traid,a.nombrechofer,a.empid,b.nombre as nombretra, c.nombre as nombreemp from $tables where $sWhere $order LIMIT $offset,$perPage";

/*
$xsql = "Select a.*,b.Nombre as NombreTra from transchofer a left join transportistas b on a.TraId=b.TraId ";
$xsql = "Select a.* from transchofer a ";
*/

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
    $traid = $data['traid'];
    $xsql = "Select choid,nombrechofer from transchofer where traid=$traid";
    $query = $this->db->query($xsql);
    if ($query) {
        $numr=$query->num_rows();
        if ($numr>0){
          foreach ( $query->result() as $row ) {
            $records[] = array( 'label' => $row->nombrechofer, 'value' => $row->choid );
            }
          }else{
            $records[] = array( 'label' => "", 'value' => 0 );
         }
      $respuesta = array(
        'error' => FALSE,            
        'records' => $records,
        'numr' => $numr
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
  $this->db->insert('transchofer',$this->data);
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
  $this->db->where( 'choid', $id );
  $hecho = $this->db->update( 'transchofer', $this->data);
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
        'traid'=>strtoupper($dat->traid),
        'empid'=>strtoupper($dat->empid),
        'nombrechofer'=>strtoupper($dat->nombrechofer)
    );
  }

}
