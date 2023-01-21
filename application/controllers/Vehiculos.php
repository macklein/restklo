<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Vehiculos extends REST_Controller {


  var $datap,$data,$id,$empid;

  public function __construct(){
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    header("Access-Control-Allow-Origin: *");
    parent::__construct();
    $this->load->database();

  }

/************************************************************************/
public function index_get(){
  }
 public function index_post(){
    $data = $this->post();
    $page = $data['page'];
    $buscar = $data['buscar'];
    $perPage = $data['perPage'];
 

    $cond="vehiculos.vehid>0 ";
   if (!empty($buscar)){
      $cond .= " and (vehiculos.descrip LIKE '%".$buscar."%' or vehiculos.placas LIKE '%".$buscar."%' or vehiculos.serie LIKE '%".$buscar."%' )"; 
    }


//        LEFT JOIN
//    t2 ON t1.c1 = t2.c1;

  $tables="vehiculos ";
  $campos="vehiculos.vehid, vehiculos.choid, vehiculos.plaid, vehiculos.descrip, vehiculos.marca, transchofer.nombrechofer, transplacas.placas, gasequipos.descripequip as descripr ";
  $sWhere="LEFT JOIN transchofer ON vehiculos.choid=transchofer.choid LEFT JOIN transplacas ON vehiculos.plaid=transplacas.plaid LEFT JOIN gasequipos ON vehiculos.equipidr=gasequipos.equipid WHERE $cond ";
//  $sWhere=" vehiculos.plaid=transplacas.plaid and $cond ";
  $sWhere.=" order by vehiculos.vehid ";
    

    $offset = ($page - 1) * $perPage;
    $xsql="SELECT count(*) AS numrows FROM $tables $sWhere";
   
 //    $respuesta = array('error' => TRUE, "message" => $xsql);

    $query = $this->db->query($xsql);
    $row = $query->row();
    $numrows = $row->numrows;
    if ($numrows>0){
       $paginas = ceil($numrows/$perPage);
    }else{
      $paginas = 1;
    }
    $xsql = "SELECT $campos FROM  $tables $sWhere LIMIT $offset,$perPage";

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
  public function chgestado_post(){
  $datap = $this->post();
  $fecha=date("Y-m-d H:i:s");
  $data = [ "fechahora" => $fecha ];
  $data += [ "vehid" => $datap['vehid']];  
  $data += [ "estado" => $datap['estado']];  
  $data += [ "nvo" => "S" ];
  $data += [ "numedo" => $datap['numedo']];  
  $this->db->insert('vehstados',$data);
  $id=$this->db->insert_id();
  if ($id>0){
      $respuesta = array(
        'error' => FALSE,
        'id' => $data);
    }else{
        $respuesta = array(
          'error' => TRUE);
   } 
  $this->response( $respuesta ); 
  }  
/************************************************************************/
 public function carga_post(){
  $this->datap = $this->post();
  $xsql ="Select vehid, empid, empreid, descrip, placas, marca, modelo, anio, simcard, serie, selectx, iconosel from vehiculos order by descrip";  
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
 public function seleid_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
//  $id=1;
  $xsql ="Select vehid, empid, empreid, descrip, placas, marca, modelo, anio, simcard, serie, equipidt, equipidr, choid, plaid from vehiculos where vehid=$id";  
  $query = $this->db->query($xsql);
    if ($query) {
      $respuesta = array(
        'error' => FALSE,            
        'item' => $query->result_array()
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }
    $this->response( $respuesta );
  }
/************************************************************************/
 public function buscaimei_post(){
  $datap = $this->post();
  $imei=$datap['imei'];
 // $respuesta = array('error' => TRUE, "item" => $imei);
 // $this->response( $respuesta );
  
   $xsql = "SELECT latitude,longitude,speed,fecha FROM ruptela Where imei=$imei order by rupid desc limit 1";
   $query1 = $this->db->query($xsql);


//  $xsql = "SELECT a.vehid,a.descrip,a.imei,latitude,longitude,speed,fecha FROM vehiculos a JOIN (SELECT MAX(rupid) rid, imei FROM ruptela GROUP BY imei) c_max ON (c_max.imei = a.imei) JOIN ruptela cd ON (cd.rupid = c_max.rid)";

/*
  $xsql ="SELECT vehid, descrip, imei, imeical, telefono, placas, simcard FROM vehiculos a JOIN (SELECT MAX(estid) eid, numedo, vehid FROM vehstados GROUP BY vehid) c_max ON (c_max.vehid = a.vehid)";
*/

$xsql ="SELECT vehid, descrip, imei, imeical, telefono, placas, simcard FROM vehiculos Where imei=$imei";
$query = $this->db->query($xsql);
$row = $query->row();
$vehid = $row->vehid;

$xsql ="SELECT estid, vehid, fechahora, estado, numedo FROM vehstados Where vehid=$vehid order by estid desc limit 1";
$query2 = $this->db->query($xsql);

if ($query) {

      $respuesta = array(
        'error' => FALSE,           
        'xsql' => $xsql,
        'vehid' => $vehid,
        'item' => $query->result_array(),
        'estad' => $query2->result_array(),
        'rupt' => $query1->result_array()
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
  $fecha=date("Y-m-d H:i:s");
  $this->data += [ "fecha" => $fecha ];
  $this->data += [ "empid" => $this->empid ];  
  $this->data += [ "nvo" => "S" ];
  $this->db->insert('vehiculos',$this->data);
  $id=$this->db->insert_id();
  if ($id>0){
      $respuesta = array(
        'error' => FALSE,
        'id' => $this->data);
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
  $this->db->where( 'vehid', $id );
  $hecho = $this->db->update( 'vehiculos', $this->data);
  if ($hecho){
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
  private function losdatos(){
    $dat=json_decode($this->datap['datosm']);
    $this->empid=$dat->empid;
    $this->data = array(
        'empreid'=>$dat->empreid,
        'descrip'=>$dat->descrip,
        'placas'=>$dat->placas,
        'marca'=>$dat->marca,
        'modelo'=>$dat->modelo,
        'anio'=>$dat->anio,
        'plaid'=>$dat->plaid,
        'choid'=>$dat->choid,
        'equipidr'=>$dat->equipidr,
        'simcard'=>$dat->simcard,
        'serie'=>$dat->serie
    );
  }
/************************************************************************/

  public function enviarcarta_post(){
    $data = $this->post();
    $vehid = $data['vehid']; //
    $fecha=date("Y-m-d H:i:s");  
    $xsql = "UPDATE vehiculos SET estatus='Term', fechaenv='$fecha' WHERE vehid=".$vehid;
    $query = $this->db->query($xsql);
    $xrec=0;
    if ($query){
      $xrec=$vehid;
    }
  
    $respuesta = array('error' => FALSE, 'vehid' => $xrec);
    $this->response( $respuesta );
  
  }  

/************************************************************************/

}
