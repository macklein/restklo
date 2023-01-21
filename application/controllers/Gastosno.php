<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Gastos extends REST_Controller {


  var $datap,$data,$id;  //Datos que se agregan solp cuando se dan de alta 

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
    $bmodo = $data['bmodo'];  //No Usado
    $estatus = $data['estatus'];  
    $bempreid = $data['bempreid'];  
    $bproid = $data['bproid'];      
    $bclasid = $data['bclasid'];      

    $bfecha1 = $data['bfecha1'];  
    $d1 = strtotime($bfecha1);
    $f1 = date('d-m-Y',$d1);
    $date1 = explode('-', $f1);
    $y1 = intval($date1[2]);

    $bfecha2 = $data['bfecha2'];  
    $d2 = strtotime($bfecha2);
    $f2 = date('d-m-Y',$d2);
    $date2 = explode('-', $f2);        
    $y2 = intval($date2[2]);
    
    if ($estatus=="Pendientes"){
      $cond="and gastos.estatus='Pend' ";
    }else{
      $cond="and gastos.estatus<>'Pend' ";
    } 
    if ($bmodo=='S'){
       if ($y1>2000){
        $cond.="and gastos.fecha>='$bfecha1' ";
       } 
       if ($y2>2000){
        $cond.="and gastos.fecha<='$bfecha2' ";
       } 
       if ($bcliid>0){
        $cond.="and gastos.empreid='$bempreid' ";
       }
       if ($bcliid>0){
        $cond.="and gastos.proid='$bproid' ";
       }
       if ($btraid>0){
        $cond.="and doctrailers.clasid='$bclasid' ";
       }
    }else{
      if (!empty($buscar)){
        $cond = $cond . "and (gastos.descripgas LIKE '%$buscar%' or gascuentas.descripcta LIKE '%$buscar%' or gascuentas.descripsub LIKE '%$buscar%' or gasunidmed.descripunid LIKE '%$buscar%') "; 
      }
   }

  $tables="gasclasif, gascuentas, gasequipos, gasunidmed, gastos left join proveedores on gastos.proid=proveedores.proid left join gasformasp on gastos.formid=gasformasp.formid ";
  $campos="gastos.gastid, gastos.empreid, gastos.proid, DATE_FORMAT(gastos.fecha,'%d/%m/%Y') AS fecha, gasclasif.descripclas, gascuentas.descripcta, gascuentas.numcta, gascuentas.subcta, gascuentas.descripsub, gasequipos.descripequip, gastos.cantidad, gasunidmed.descripunid, gastos.subtotal, gastos.montoiva, gastos.montoret, gastos.total, gastos.descripgas, gastos.factura, gastos.estatus, gastos.pagada, gasformasp.descripform, gastos.fechapago, gastos.formid, gastos.empreidp, proveedores.nombre  ";
  $sWhere="gastos.clasid=gasclasif.clasid and gastos.ctaid=gascuentas.ctaid and gastos.equipid=gasequipos.equipid and gastos.unidid=gasunidmed.unidid $cond ";

$sWhere.=" order by gastos.gastid desc ";

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
 public function seleid_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
  $xsql = "Select gastid, fecha, equipid, empreid, clasid, ctaid, cantidad, unidid, proid, subtotal, montoiva, montoret, total, factura, descripgas from gastos where gastid=$id";  
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

 public function altar_post(){
  $this->datap = $this->post();
 // $empid=$this->datap['empid'];
 // $this->losdatos();  


  $fecha=date("Y-m-d H:i:s");
  // Datos solo en Alta
//  $this->data += [ "fecha" => $fecha ];
//  $this->data += [ "estatus" => "Pend" ];
//  $this->data += [ "pagada" => "N" ];
 // $this->data += [ "empid" => $empid ];  
//  $this->data += [ "nvo" => "S" ];
    $respuesta = array(
  'error' => FALSE,
  'idata' => 'No se Genero el Gasto');

/*  $this->db->insert('gastos',$this->data);
  $id=$this->db->insert_id();
  if ($id>0){
      $respuesta = array(
        'error' => FALSE,
        'id' => $id);
    }else{
        $respuesta = array(
          'error' => TRUE,
        'message' => $this->db->error() );
   } 
  */
   
  $this->response( $respuesta ); 
  
  }

/************************************************************************/
 public function modif_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
  $this->losdatos();
  $this->db->where( 'gastid', $id );
  $hecho = $this->db->update( 'gastos', $this->data);
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
  public function actualiza_post(){
    $data = $this->post();
    $gastid = $data['gastid'];
    $respuesta = array('error' => TRUE, 'gastid' => $gastid);
    $xsql="update gastos set estatus='Act' where gastid='".$gastid."'";
    $query = $this->db->query($xsql);
    if ($query){
      $respuesta = array('error' => FALSE, 'gastid' => $gastid);
    }
    $this->response( $respuesta );
  }  

//  
/************************************************************************/
/*
  private function losdatos(){
    $dat=json_decode($this->datap['datosm']);
  //  $this->empid=$dat->empid;
    $this->data = array(
        'equipid'=>$dat->equipid,
        'empreid'=>$dat->empreid,
        'clasid'=>$dat->clasid,
        'ctaid'=>$dat->ctaid,
        'cantidad'=>$dat->cantidad,
        'unidid'=>$dat->unidid,
        'proid'=>$dat->proid,
        'subtotal'=>$dat->subtotal,
        'montoiva'=>$dat->montoiva,
        'montoret'=>$dat->montoret,
        'total'=>$dat->total,
        'factura'=>strtoupper($dat->factura),
        'descripgas'=>strtoupper($dat->descripgas)
    );
    
  }
*/
}
