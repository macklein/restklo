<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Indicano extends REST_Controller {


  var $datap,$data,$id,$empid;

  public function __construct(){
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    header("Access-Control-Allow-Origin: *");
    parent::__construct();
    $this->load->database();

  }

/************************************************************************/
 public function factu0g1_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $xsql = "SELECT a.serie,a.nombre,a.moneda, ";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 1, 1,0)) as mes1c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 1, ABS(a.total),0)) as mes1t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 2, 1,0)) as mes2c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 2, ABS(a.total),0)) as mes2t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 3, 1,0)) as mes3c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 3, ABS(a.total),0)) as mes3t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 4, 1,0)) as mes4c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 4, ABS(a.total),0)) as mes4t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 5, 1,0)) as mes5c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 5, ABS(a.total),0)) as mes5t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 6, 1,0)) as mes6c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 6, ABS(a.total),0)) as mes6t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 7, 1,0)) as mes7c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 7, ABS(a.total),0)) as mes7t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 8, 1,0)) as mes8c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 8, ABS(a.total),0)) as mes8t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 9, 1,0)) as mes9c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 9, ABS(a.total),0)) as mes9t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 10, 1,0)) as mes10c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 10, ABS(a.total),0)) as mes10t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 11, 1,0)) as mes11c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 11, ABS(a.total),0)) as mes11t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 12, 1,0)) as mes12c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 12, ABS(a.total),0)) as mes12t, ";
  $xsql = $xsql . "Count(a.facid) as totalc,";
  $xsql = $xsql . "Sum(a.total) as total ";
  $xsql = $xsql . "from facturas a where a.timbrada='S' and a.tipocfdi='I' and cancel<>'S' and year(a.fecha)='$anio' group by a.serie,a.moneda ";
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  $this->response( $respuesta );
}
/************************************************************************/
 public function factu0g2_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $serie=$this->datap['serie'];
  $moneda=$this->datap['moneda'];  
  $xsql = "SELECT a.serie,a.cliid,a.nombre,a.moneda, ";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 1, 1,0)) as mes1c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 1, ABS(a.total),0)) as mes1t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 2, 1,0)) as mes2c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 2, ABS(a.total),0)) as mes2t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 3, 1,0)) as mes3c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 3, ABS(a.total),0)) as mes3t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 4, 1,0)) as mes4c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 4, ABS(a.total),0)) as mes4t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 5, 1,0)) as mes5c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 5, ABS(a.total),0)) as mes5t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 6, 1,0)) as mes6c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 6, ABS(a.total),0)) as mes6t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 7, 1,0)) as mes7c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 7, ABS(a.total),0)) as mes7t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 8, 1,0)) as mes8c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 8, ABS(a.total),0)) as mes8t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 9, 1,0)) as mes9c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 9, ABS(a.total),0)) as mes9t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 10, 1,0)) as mes10c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 10, ABS(a.total),0)) as mes10t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 11, 1,0)) as mes11c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 11, ABS(a.total),0)) as mes11t,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 12, 1,0)) as mes12c,";
  $xsql = $xsql . "Sum(IF(month(a.fecha) = 12, ABS(a.total),0)) as mes12t, ";
  $xsql = $xsql . "Count(a.facid) as totalc,";
  $xsql = $xsql . "Sum(a.total) as total ";
  $xsql = $xsql . "from facturas a where a.timbrada='S' and a.tipocfdi='I' and a.serie='".$serie."' and cancel<>'S' and moneda='$moneda' and year(a.fecha)='$anio' group by a.cliid ";
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array());
  $this->response( $respuesta );
}
/************************************************************************/
 public function factu0g3_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $cliid=$this->datap['cliid'];
  $serie=$this->datap['serie'];
  $moneda=$this->datap['moneda'];  

  $xsql="select facid, cliid, cancel, pagada, serie, empreid, fecha, nombre, total, moneda, uuid from facturas where timbrada='S' and tipocfdi='I' and tipocfdi='I' and cancel<>'S' and cliid=$cliid and serie='$serie' and moneda='$moneda' and year(fecha)=$anio order by facid desc ";
//  $query = mysqli_query($con,$xsql);

  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
   'items' => $query->result_array());
 // 'sql'=>$xsql);
  $this->response( $respuesta );
}

/*****************************************************************************************/
/*****************************************************************************************/
/*****************************************************************************************/
/*****************************************************************************************/
/*****************************************************************************************/
/*****************************************************************************************/

 public function factu0c1_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $xsql = "SELECT a.serie,a.nombre,a.moneda, ";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 1, 1,0)) as mes1c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 1, ABS(a.total),0)) as mes1t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 2, 1,0)) as mes2c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 2, ABS(a.total),0)) as mes2t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 3, 1,0)) as mes3c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 3, ABS(a.total),0)) as mes3t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 4, 1,0)) as mes4c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 4, ABS(a.total),0)) as mes4t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 5, 1,0)) as mes5c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 5, ABS(a.total),0)) as mes5t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 6, 1,0)) as mes6c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 6, ABS(a.total),0)) as mes6t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 7, 1,0)) as mes7c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 7, ABS(a.total),0)) as mes7t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 8, 1,0)) as mes8c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 8, ABS(a.total),0)) as mes8t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 9, 1,0)) as mes9c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 9, ABS(a.total),0)) as mes9t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 10, 1,0)) as mes10c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 10, ABS(a.total),0)) as mes10t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 11, 1,0)) as mes11c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 11, ABS(a.total),0)) as mes11t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 12, 1,0)) as mes12c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 12, ABS(a.total),0)) as mes12t, ";
  $xsql = $xsql . "Count(a.facid) as totalc,";
  $xsql = $xsql . "Sum(a.total) as total ";
  $xsql = $xsql . "from facturas a where a.timbrada='S' and a.tipocfdi='I' and a.pagada='S' and cancel<>'S' and year(a.fechapago)='$anio' group by a.serie,a.moneda ";
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  $this->response( $respuesta );
}
/************************************************************************/
 public function factu0c2_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $serie=$this->datap['serie'];
  $moneda=$this->datap['moneda'];  
  $xsql = "SELECT a.serie,a.cliid,a.nombre,a.moneda, ";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 1, 1,0)) as mes1c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 1, ABS(a.total),0)) as mes1t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 2, 1,0)) as mes2c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 2, ABS(a.total),0)) as mes2t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 3, 1,0)) as mes3c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 3, ABS(a.total),0)) as mes3t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 4, 1,0)) as mes4c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 4, ABS(a.total),0)) as mes4t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 5, 1,0)) as mes5c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 5, ABS(a.total),0)) as mes5t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 6, 1,0)) as mes6c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 6, ABS(a.total),0)) as mes6t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 7, 1,0)) as mes7c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 7, ABS(a.total),0)) as mes7t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 8, 1,0)) as mes8c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 8, ABS(a.total),0)) as mes8t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 9, 1,0)) as mes9c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 9, ABS(a.total),0)) as mes9t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 10, 1,0)) as mes10c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 10, ABS(a.total),0)) as mes10t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 11, 1,0)) as mes11c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 11, ABS(a.total),0)) as mes11t,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 12, 1,0)) as mes12c,";
  $xsql = $xsql . "Sum(IF(month(a.fechapago) = 12, ABS(a.total),0)) as mes12t, ";
  $xsql = $xsql . "Count(a.facid) as totalc,";
  $xsql = $xsql . "Sum(a.total) as total ";
  $xsql = $xsql . "from facturas a where a.timbrada='S' and a.tipocfdi='I' and a.pagada='S' and a.serie='".$serie."' and cancel<>'S' and year(a.fechapago)='$anio' group by a.cliid,a.moneda ";
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array());
  $this->response( $respuesta );
}
/************************************************************************/
 public function factu0c3_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $cliid=$this->datap['cliid'];
  $serie=$this->datap['serie'];
  $moneda=$this->datap['moneda'];  

  $xsql="select facid, cliid, cancel, pagada, serie, empreid, fecha, fechapago, nombre, total, moneda, uuid from facturas where timbrada='S' and tipocfdi='I' and pagada='S' and cancel<>'S' and cliid=$cliid and serie='$serie' and moneda='$moneda' and year(fechapago)=$anio order by facid desc ";
//  $query = mysqli_query($con,$xsql);

  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
   'items' => $query->result_array());
 // 'sql'=>$xsql);
  $this->response( $respuesta );
}

}
