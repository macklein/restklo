<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Empleados extends REST_Controller {

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
    
    $tables = "empleados";
    $campos = "empid, nombre, direccion, telefono1, puesto, empreid ";
    $sWhere = "nombre LIKE '%".$buscar."%'"; 
    $order = " Order by empid ";

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
    
/*
$xsql = "Select EmpId,Nombre,Puesto,Telefono1,Direccion,Telefono2,EmpreId from empleados";
$paginas=1;
$numrows=1;
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
    $xsql = "Select empid,nombre from empleados order by nombre";
    $query = $this->db->query($xsql);
    if ($query) {
        foreach ( $query->result() as $row ) {
            $records[] = array( 'label' => $row->nombre, 'value' => $row->empid );
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
 public function cargapuesto_post(){
    $data = $this->post();
    $puesto = $data['puesto']; // m=Maniobrista, a=Ayudante
    if ($puesto=="m"){
      $xsql = "select empid, nombre from empleados where (puesto='MANIOBRISTA' or puesto='SUPERVICION ALMACEN') order by nombre";
    }
    if ($puesto=="a"){
      $xsql = "select empid, nombre from empleados where (puesto='AYUDANTE MANIOBRISTA' or puesto='SUPERVICION ALMACEN') order by nombre";
    }
    $query = $this->db->query($xsql);
    if ($query) {
        foreach ( $query->result() as $row ) {
            $records[] = array( 'label' => $row->nombre, 'value' => $row->empid );
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
  $this->db->insert('empleados',$this->data);
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
  $this->db->where( 'empid', $id );
  $hecho = $this->db->update( 'empleados', $this->data);
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
        'puesto'=>strtoupper($dat->puesto),
        'telefono1'=>$dat->telefono1,
        'telefono2'=>$dat->telefono2,
        'empreid'=>strtoupper($dat->empreid),
    );
  }

}
