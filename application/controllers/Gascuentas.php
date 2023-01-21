<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Gascuentas extends REST_Controller {


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
    $tables = "gascuentas ";
    $campos = "ctaid, descripcta ";
    $sWhere = "descripcta LIKE '%".$buscar."%'"; 
    $order = " Order by ctaid ";


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

  $xsql = "Select * from gascuentas where $sWhere $order LIMIT $offset,$perPage";

    $xsql = "Select a.ctaid, a.descripcta, a.numcta, a.subcta, a.descripsub, a.empreid, a.clasid, a.tipo ,b.descripclas from gascuentas a left join gasclasif b on a.clasid=b.clasid where $sWhere $order LIMIT $offset,$perPage";

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
    $empreid = $data['empreid'];
    $clasid = $data['clasid'];
    $cond = "ctaid>0 ";
    if ($empreid>0){
      $cond = $cond . "and empreid=$empreid ";
    }
    if ($clasid>0){
      $cond = $cond . "and clasid=$clasid";
    }

    $xsql = "Select ctaid,descripcta,descripsub from gascuentas where $cond order by clasid";
    $query = $this->db->query($xsql);
    if ($query) {
        foreach ( $query->result() as $row ) {
            $records[] = array( 'label' => $row->descripcta."-".$row->descripsub, 'value' => $row->ctaid );
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
  $this->db->insert('gascuentas',$this->data);
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
  $this->db->where( 'ctaid', $id );
  $hecho = $this->db->update( 'gascuentas', $this->data);
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
        'descripcta'=>strtoupper($dat->descripcta),
        'numcta'=>strtoupper($dat->numcta),
        'subcta'=>strtoupper($dat->subcta),
        'descripsub'=>strtoupper($dat->descripsub),
        'empreid'=>strtoupper($dat->empreid),
        'clasid'=>strtoupper($dat->clasid),
        'tipo'=>strtoupper($dat->tipo)
    );
  }

}
