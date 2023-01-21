<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Facturas extends REST_Controller {

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
    
    $perPage  = $data['perPage'];
    $bcliid   = $data['bcliid'];   
    $tipof    = $data['tipof']; 
    $tipocfdi = $data['tipocfdi']; 
    $bfecha1   = $data['bfecha1']; 
    $bfecha2   = $data['bfecha2']; 
    $fecha1 = date('Ymd', strtotime($bfecha1));
    $fecha2 = date('Ymd', strtotime($bfecha2.'+1 day'));
    $moneda   = $data['moneda']; 
    $serie    = $data['serie']; 

    $query = " facturas.fecha>='".$fecha1."' and facturas.fecha<='".$fecha2."' ";
    switch($tipof) {
      case 'NoP':
        $query = " facturas.pagada='N' "; 
        break;
      case 'Pag':
        $query = "facturas.fechapago>='".$fecha1."' and facturas.fechapago<='".$fecha2."'";
        $query .=" and facturas.pagada='S' "; 
        break;
      default:    
        $nada=0; 
    }
    switch($tipocfdi) {
      case 'Fac':
        $query .= " and facturas.tipocfdi='I' "; 
        break;
      case 'Pag':
        $query .= " and facturas.tipocfdi='P' "; 
        break;
      default:    
        $nada=0; 
    }
    if ($serie<>'To'){
      $query .= " and facturas.serie='$serie' "; 
    }
    $query .= " and facturas.moneda='$moneda' "; 
    if ($bcliid>0){
      $query .=" and facturas.cliid=".$bcliid;  
    }
   

// $query = "facturas.pagada='N'"; 

  $tables="facturas";
  $campos="facturas.serie, facturas.facid, facturas.cliid, facturas.cancel, facturas.pagada, facturas.serie, facturas.empreid, facturas.fecha, facturas.nombre, facturas.moneda, facturas.tipocfdi, facturas.total, facturas.uuid, facturas.fechapago,facturas.empreid,IF(facturas.cancel='S','Can',IF(facturas.pagada='S','Pag','NoP')) as estat ";
  $sWhere="facturas.timbrada='S' and facturas.cancel<>'S' and ".$query;
  $sWhere.=" order by facturas.facid desc ";

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


  /*  $respuesta = array(
        'error' => FALSE,            
        'items' => $query
      );
    $this->response( $respuesta );*/
    

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
