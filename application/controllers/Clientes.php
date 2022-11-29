<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Clientes extends REST_Controller {


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

    $tables = "clientes";
    $campos = "cliid, nombre, email, telefono1, direccion, estatus, telefono2, contacto1, empreid ";
    $sWhere = "nombre LIKE '%".$buscar."%'"; 
    $order = " Order by cliid ";

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
//mysqli_error($con);
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
/***********************************************************************/
 public function carga_post(){
    $data = $this->post();
    $tipouser = $data['tipouser'];  
    $ctesids = $data['ctesids'];  
    $conduser='Where cliid>0 ';
    $ctes = explode(",", $ctesids);
    $n=0;
    if ($tipouser=="Cli"){
      if (count($ctes)==1){
        $conduser.=" and cliid=$ctes[0] ";
      }else{
        foreach ($ctes as $cte) {
          $n=$n+1;
          if ($n==1){
            $conduser.=" and ( cliid=$cte ";
          }else{
            $conduser.=" or cliid=$cte ";
          }
        }
        $conduser.=" ) ";
      }
    }
    $xsql = "Select cliid,nombre from clientes $conduser order by nombre ";
    $query = $this->db->query($xsql);
    if ($query) {
        foreach ( $query->result() as $row ) {
            $records[] = array( 'label' => $row->nombre, 'value' => $row->cliid );
        } 
      $respuesta = array(
        'error' => FALSE,            
        'records' => $records
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }  
  //    $respuesta = array('error' => TRUE, "message" => $xsql);
    
    $this->response( $respuesta );
 }
/************************************************************************/
 public function alta_post(){
  $this->datap = $this->post();
  $this->losdatos();
  $this->db->insert('clientes',$this->data);
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
  $this->db->where( 'cliid', $id );
  $hecho = $this->db->update( 'clientes', $this->data);
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
  public function cargaempre_post(){
    $data = $this->post();
    $cliid = $data['cliid'];
    $xsql = "Select empreid from clientes where cliid=$cliid";
    $query = $this->db->query($xsql);
    $empreid=0;
    if ($query) {
      $row = $query->row();
      $empreid = $row->empreid;
      $respuesta = array(
        'error' => FALSE,            
        'empreid' => $empreid,      
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }
    $this->response( $respuesta );
  }

 /************************************************************************/
  private function losdatos(){
    $dat=json_decode($this->datap['datosm']);
    $this->data = array(
        'nombre'=>strtoupper($dat->nombre),
        'nombrefac'=>strtoupper($dat->nombre),
        'direccion'=>strtoupper($dat->direccion),
        'rfc'=>strtoupper($dat->rfc),
        'telefono1'=>$dat->telefono1,
        'telefono2'=>$dat->telefono2,
        'contacto1'=>strtoupper($dat->contacto1),
        'email'=>$dat->email,
        'empreid'=>$dat->empreid
    );
  }

}
