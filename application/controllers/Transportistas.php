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
    $tipouser = $data['tipouser'];  
    $transids = $data['transids'];  
    $conduser='Where traid>0 ';
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

}
