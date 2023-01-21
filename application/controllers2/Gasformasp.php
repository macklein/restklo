<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Gasformasp extends REST_Controller {


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

    $tables = "gasformasp ";
    $campos = "formid, descripform ";
    $sWhere = "descripform LIKE '%".$buscar."%'"; 
    $order = " Order by formid ";

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


$xsql = "Select formid, descripform, empreid from gasformasp where $sWhere $order LIMIT $offset,$perPage";

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
 public function alta_post(){
  $this->datap = $this->post();
  $this->losdatos();
  $this->data += [ "nvo" => "S" ];
  $this->db->insert('gasformasp',$this->data);
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
  $this->db->where( 'formid', $id );
  $hecho = $this->db->update( 'gasformasp', $this->data);
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
        'empreid'=>strtoupper($dat->empreid),
        'descripform'=>strtoupper($dat->descripform)
    );
  }
  public function cargaformas_post(){
    $data = $this->post();
    $empreid = $data['empreid'];
    $xsql="select formid,descripform from gasformasp where empreid='".$empreid."' order by formid";
  //   $records[] = array( 'label' => 'uno', 'value' => 'dos' );
  //   $respuesta = array('error' => TRUE, "records" => $records, 'sql'=>$xsql);

    $query = $this->db->query($xsql);
    if ($query) {
        foreach ( $query->result() as $row ) {
            $records[] = array( 'label' => $row->descripform, 'value' => $row->formid );
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

}
