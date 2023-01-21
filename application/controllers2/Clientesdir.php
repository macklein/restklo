<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Clientesdir extends REST_Controller {


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
    $tables = "clientesdir a, clientes b";
    $campos = "a.dircliid, a.cliid, a.descripcion, a.latitude, a.longitude, b.nombre ";
    $sWhere = "a.cliid=b.cliid and a.descripcion LIKE '%".$buscar."%'"; 
    $order = " Order by a.cliid ";

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
 /************************************************************************/
  public function carga_post(){
    $data = $this->post();
    $cliid = $data['cliid'];
    $xsql = "Select a.dircliid,a.descripcion,b.empreid,b.nombre from clientesdir a, clientes b where a.cliid=b.cliid and a.cliid=$cliid";
    $query = $this->db->query($xsql);
    $empreid=0;
    $nombre='';
    if ($query) {
        $numr=$query->num_rows();
        if ($numr>0){
          foreach ( $query->result() as $row ) {
            $records[] = array( 'label' => $row->descripcion, 'value' => $row->dircliid );
            $empreid=$row->empreid;
            $nombre=$row->nombre;
            }
          }else{
            $records[] = array( 'label' => "", 'value' => 0 );
         }
      //   if ($cliid==15){
      //      $xsql = "Select sitid, cliid, descrip, calle from gpssitios where cliid=$cliid order by TipoOrd";
      //   }else{
          $xsql = "Select a.sitid, a.cliid, a.descrip, a.calle, b.rfc from gpssitios a, clientes b where (a.empreid=$empreid or a.cliid=$cliid) and (a.cliid=b.cliid) order by TipoOrd";
      //   }
         $query = $this->db->query($xsql); 
          $respuesta = array(
            'error' => FALSE,            
            'records' => $records,
            'empreid' => $empreid,      
            'nombre' => $nombre,    
            'sitios' => $query->result_array(),  
            'numr' => $numr
          );
        } else {
          $respuesta = array('error' => TRUE, "message" => $xsql);
    } 
    $this->response( $respuesta );
  }
 /************************************************************************/
  public function cargasitios_post(){
    $data = $this->post();
    $xsql = "Select a.dircliid,a.descripcion,a.latitude,a.longitude,b.nombre from clientesdir a, clientes b where a.cliid=b.cliid and a.latitude<>'' and a.longitude<>'' " ;
    $xsql = "Select sitid, cliid, descrip, descripcion, latitude, longitude from gpssitios where activo='S'" ;

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
 public function alta_post(){
  $this->datap = $this->post();
  $this->losdatos();
  $this->data += [ "nvo" => "S" ];
  $this->db->insert('clientesdir',$this->data);
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
  $this->db->where( 'dircliid', $id );
  $hecho = $this->db->update( 'clientesdir', $this->data);
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
        'cliid'=>strtoupper($dat->cliid),
        'descripcion'=>strtoupper($dat->descripcion),
        'latitude'=>strtoupper($dat->latitude),
        'longitude'=>strtoupper($dat->longitude)
    );
  }

}
