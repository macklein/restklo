<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Clientesmat extends REST_Controller {


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
    $tables = "clientesmat a, clientes b";
    $campos = "a.matcliid, a.cliid, a.descripcion, a.unidadmed, a.unidadpes, a.descrip1, a.descrip2, a.descrip3, a.descrip4, a.capcantidad, b.nombre ";
    $sWhere = "a.cliid=b.cliid and (a.descripcion LIKE '%".$buscar."%' or b.nombre LIKE '%".$buscar."%')"; 
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
   /************************************************************************/
 public function cargadoctrail_post(){  
    $data = $this->post();
    $cliid = $data['cliid'];
    $empreid = 0;
    $tipocp6 = "N";
    $tipocp7 = "N";
    
  //  a.tipocp6=Entrada normal ,a.tipocp7=Entrada con Flete
    $xsql = "Select a.empreidtrail as empreid,b.matcliid,b.descripcion,a.tipocp6,a.tipocp7 from clientes a, clientesmat b where a.cliid=b.cliid and a.cliid=$cliid";
    $query = $this->db->query($xsql);
    if ($query) {
        $numr=$query->num_rows();
        if ($numr>0){
          foreach ( $query->result() as $row ) {
              $empreid = $row->empreid;
              $tipocp6 = $row->tipocp6;
              $tipocp7 = $row->tipocp7;              
              $records[] = array( 'label' => $row->descripcion, 'value' => $row->matcliid );
          } 
        }else{
            $records[] = array( 'label' => "", 'value' => 0 );
         }
   //      if ($cliid==15){
   //           $xsql = "Select sitid, cliid, descrip, calle from gpssitios where cliid=$cliid order by TipoOrd";
   //      }else{
   //           $xsql = "Select sitid, cliid, descrip, calle from gpssitios where cliid=15 or cliid=$cliid order by TipoOrd";
   //      }
         $xsql = "Select a.sitid, a.cliid, a.descrip, a.calle, b.rfc from gpssitios a, clientes b where (a.empreid=$empreid or a.cliid=$cliid) and (a.cliid=b.cliid) order by TipoOrd";


         $query = $this->db->query($xsql);
      $respuesta = array(
        'error' => FALSE,      
        'empreid' => $empreid,
        'tipocp6' => $tipocp6,
        'tipocp7' => $tipocp7,
        'sitios' => $query->result_array(),  
        'records' => $records
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
  $empreid = 0;
  $tipocp6 = "N";
  $tipocp7 = "N";
  
//  a.tipocp6=Entrada normal ,a.tipocp7=Entrada con Flete
  $xsql = "Select a.empreid,b.matcliid,b.descripcion,a.tipocp6,a.tipocp7 from clientes a, clientesmat b where a.cliid=b.cliid and a.cliid=$cliid";
  $query = $this->db->query($xsql);
  if ($query) {
      $numr=$query->num_rows();
      if ($numr>0){
        foreach ( $query->result() as $row ) {
            $empreid = $row->empreid;
            $tipocp6 = $row->tipocp6;
            $tipocp7 = $row->tipocp7;              
            $records[] = array( 'label' => $row->descripcion, 'value' => $row->matcliid );
        } 
      }else{
          $records[] = array( 'label' => "", 'value' => 0 );
       }
 //      if ($cliid==15){
 //           $xsql = "Select sitid, cliid, descrip, calle from gpssitios where cliid=$cliid order by TipoOrd";
 //      }else{
 //           $xsql = "Select sitid, cliid, descrip, calle from gpssitios where cliid=15 or cliid=$cliid order by TipoOrd";
 //      }
       $xsql = "Select a.sitid, a.cliid, a.descrip, a.calle, b.rfc from gpssitios a, clientes b where (a.empreid=$empreid or a.cliid=$cliid) and (a.cliid=b.cliid) order by TipoOrd";


       $query = $this->db->query($xsql);
    $respuesta = array(
      'error' => FALSE,      
      'empreid' => $empreid,
      'tipocp6' => $tipocp6,
      'tipocp7' => $tipocp7,
      'sitios' => $query->result_array(),  
      'records' => $records
    );
  } else {
    $respuesta = array('error' => TRUE, "message" => $query);
  }
  $this->response( $respuesta );
}
/************************************************************************/
public function cargaid_post(){  
    $data = $this->post();
    $matid = $data['matid'];
    $xsql = "Select a.arancel, a.unidadmed, a.unidadpes, a.codigosat, a.unidadsat, a.descrip1, a.descrip2, a.descrip3, a.descrip4, a.capcantidad, b.empreid from clientesmat a, clientes b where a.cliid=b.cliid and matcliid=$matid";
    $query = $this->db->query($xsql);
    if ($query) {
      $numr=$query->num_rows();
      if ($numr>0){
        $row = $query->row();
        $respuesta = array(
          'error' => FALSE,            
          'umedida' => $row->unidadmed,
          'arancel' => $row->arancel,
          'upeso' => $row->unidadpes,
          'codigosat' => $row->codigosat,
          'unidadsat' => $row->unidadsat,
          
          'descrip1' => $row->descrip1,
          'descrip2' => $row->descrip2,
          'descrip3' => $row->descrip3,
          'descrip4' => $row->descrip4,
          'empreid' => $row->empreid,          
          'capcantidad' => $row->capcantidad,
        );
      }else{
         $respuesta = array(
          'error' => FALSE,            
          'umedida' => "",
          'upeso' => "",
          'descrip1' => "",
          'descrip2' => "",
          'descrip3' => "",
          'descrip4' => "",
          'empreid' => 0,          
          'capcantidad' => ""
        );
      }  
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }
    $this->response( $respuesta );
  }
/**************************************************************************************/
 public function alta_post(){
  $this->datap = $this->post();
  $this->losdatos();
  $this->data += [ "nvo" => "S" ];
  $this->db->insert('clientesmat',$this->data);
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
  $this->db->where( 'matcliid', $id );
  $hecho = $this->db->update( 'clientesmat', $this->data);
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
        'unidadmed'=>strtoupper($dat->unidadmed),
        'unidadpes'=>strtoupper($dat->unidadpes),
        'descrip1'=>strtoupper($dat->descrip1),
        'descrip2'=>strtoupper($dat->descrip2),
        'descrip3'=>strtoupper($dat->descrip3),
        'descrip4'=>strtoupper($dat->descrip4),
        'capcantidad'=>$dat->capcantidad
    );
  }

}
