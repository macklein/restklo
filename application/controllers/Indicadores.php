<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Indicadores extends REST_Controller {


  var $datap,$data,$id,$empid;

  public function __construct(){
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    header("Access-Control-Allow-Origin: *");
    parent::__construct();
    $this->load->database();

  }

/************************************************************************/
 public function cargadia_post(){
  $this->datap = $this->post();
  $f=$this->datap['fecha'];
  //$d1 = strtotime($f);
  //$fecha = date('d-m-Y',$d1);
  $fecha = date('Ymd', strtotime($f));
  /* CROSS DOCK */
  $xsql="select COUNT(*) as count from crossdock where estatus='Term' and CAST(fecha AS DATE)='".$fecha."'";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cross = $row->count;

  /* CARTA PORTE EMBARQUE */
  $xsql="select COUNT(*) as count from cartaporte where tipo<>'PendC' and tipo<>'PendA' and destino='Embarque' and CAST(fecha AS DATE)='".$fecha."'";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cpemb = $row->count;

  /* CARTA PORTE ALMACEN */
  $xsql="select COUNT(*) as count from cartaporte where tipo<>'PendC' and tipo<>'PendA' and destino='Almacen' and CAST(fecha AS DATE)='".$fecha."'";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cpalm = $row->count;

  /* TRAILERS */
  $xsql="select COUNT(*) as count from doctrailers where estatus='EnCar' and CAST(fecha AS DATE)='".$fecha."'";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $trailers = $row->count;

  /* CARRO LIBERADOS */
  $xsql="select COUNT(*) as count from doccarros where estatus='Term' and CAST(fecha AS DATE)='".$fecha."'";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $carrolib = $row->count;

  /* ALMACEN */  
  $xsql="select sum(cantidad) as count from almacen where estatus='Pend'";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $inventa = $row->count;

  /* FACTURAS GENERADAS */  
  $xsql="select COUNT(*) as count from facturas where timbrada='S' and tipocfdi='I' and cancel<>'S' and CAST(fecha AS DATE)='".$fecha."'";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $facgen = $row->count;

  /* VIAJES CROSS ****************************************/  

  $xsql="select COUNT(*) as count from cartaporte where (tipo='Cros' or tipo='Alma') and destino='Almacen' and CAST(fecha AS DATE)='".$fecha."' and TraId=13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cpalm13 = $row->count;

  $xsql="select COUNT(*) as count from cartaporte where (tipo='Cros' or tipo='Alma') and destino='Almacen' and CAST(fecha AS DATE)='".$fecha."' and TraId<>13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cpalmx = $row->count;

  $xsql="select COUNT(*) as count from cartaporte where (tipo='Cros' or tipo='Alma') and destino='Embarque' and CAST(fecha AS DATE)='".$fecha."' and TraId=13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cpemb13 = $row->count;

  $xsql="select COUNT(*) as count from cartaporte where (tipo='Cros' or tipo='Alma') and destino='Embarque' and CAST(fecha AS DATE)='".$fecha."' and TraId<>13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cpembx = $row->count;

  //Entradas 13

  $xsql="select COUNT(*) as count from entradas where estatus<>'Pend' and CAST(fecha AS DATE)='".$fecha."' and TraId=13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $ent13 = $row->count;

  //Entradas <> 13
  $xsql="select COUNT(*) as count from entradas where estatus<>'Pend' and CAST(fecha AS DATE)='".$fecha."' and TraId<>13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $entx = $row->count;

  $viajecross = $cpalm13+$cpalmx+$cpemb13+$cpembx+$ent13+$entx;


  /* VIAJES DOCUM *********************************************/  
  $xsql="select COUNT(*) as count from doctrailers where estatus='EnCar' and CAST(fecha AS DATE)='".$fecha."' and TraId=13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $trail13 = $row->count;

  //Trailers <> 13
  $xsql="select COUNT(*) as count from doctrailers where estatus='EnCar' and CAST(fecha AS DATE)='".$fecha."' and TraId<>13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $trailx = $row->count;


  $viajedocum = $trail13+$trailx;

  $respuesta = array('error' => TRUE,
    "cross" => $cross,
    "cpemb" => $cpemb,
    "cpalm" => $cpalm,
    "trail" => $trailers,
    "carro" => $carrolib,
    "inve" => $inventa,
    "fact" => $facgen,
    "viajcros" => $viajecross,
    "viajdocum" => $viajedocum,


  );

  $this->response( $respuesta );
 }

/************************************************************************/
 public function cargames_post(){
  $this->datap = $this->post();
  $mes=$this->datap['mes'];
  $anio=$this->datap['anio'];  
  
  $xsql = "select COUNT(*) as count from crossdock where estatus='Term' and year(fecha) = '$anio' and month(fecha)= '$mes' ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cross = $row->count;

  $xsql="select COUNT(*) as count from cartaporte where tipo<>'PendC' and tipo<>'PendA' and destino='Embarque' and year(fecha) = '$anio' and month(fecha)= '$mes' ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cpemb = $row->count;

  $xsql="select COUNT(*) as count from cartaporte where tipo<>'PendC' and tipo<>'PendA' and destino='Almacen' and year(fecha) = '$anio' and month(fecha)= '$mes' ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cpalm = $row->count;

  $xsql="select COUNT(*) as count from doctrailers where estatus='EnCar' and year(fecha) = '$anio' and month(fecha)= '$mes' ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $trailers = $row->count;

  $xsql="select COUNT(*) as count from doccarros where estatus='Term' and year(fecha) = '$anio' and month(fecha)= '$mes' ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $carrolib = $row->count;

  $xsql="select COUNT(*) as count from facturas where timbrada='S' and tipocfdi='I' and cancel<>'S' and year(fecha) = '$anio' and month(fecha)= '$mes' ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $facgen = $row->count;

  $xsql="select COUNT(*) as count from facturas where timbrada='S' and tipocfdi='I' and cancel<>'S' and pagada='N'";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $facporcob = $row->count;

/******************************************************************************/

  $xsql="select COUNT(*) as count from cartaporte where (tipo='Cros' or tipo='Alma') and destino='Almacen'  and year(fecha) = '$anio' and month(fecha)= '$mes' and TraId=13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cpalm13 = $row->count;

  //Cartas Porte Almacen <> 13
  $xsql="select COUNT(*) as count from cartaporte where (tipo='Cros' or tipo='Alma') and destino='Almacen' and year(fecha) = '$anio' and month(fecha)= '$mes' and TraId<>13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cpalmx = $row->count;

  //Cartas Porte Embarque 13
  $xsql="select COUNT(*) as count from cartaporte where (tipo='Cros' or tipo='Alma') and destino='Embarque' and year(fecha) = '$anio' and month(fecha)= '$mes' and TraId=13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cpemb13 = $row->count;

  //Cartas Porte Embarque <> 13
  $xsql="select COUNT(*) as count from cartaporte where (tipo='Cros' or tipo='Alma') and destino='Embarque' and year(fecha) = '$anio' and month(fecha)= '$mes' and TraId<>13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cpembx = $row->count;

  //Entradas 13
  $xsql="select COUNT(*) as count from entradas where estatus<>'Pend' and year(fecha) = '$anio' and month(fecha)= '$mes' and TraId=13 ";  
  $query = $this->db->query($xsql);
  $row = $query->row();
  $ent13 = $row->count;


  //Entradas <> 13
  $xsql="select COUNT(*) as count from entradas where estatus<>'Pend' and year(fecha) = '$anio' and month(fecha)= '$mes' and TraId<>13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $entx = $row->count;

  $viajecross = $cpalm13+$cpalmx+$cpemb13+$cpembx+$ent13+$entx;

/**************************************/

  $xsql="select COUNT(*) as count from doctrailers where estatus='EnCar' and year(fecha) = '$anio' and month(fecha)= '$mes' and TraId=13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $trail13 = $row->count;

  //Trailers <> 13
  $xsql="select COUNT(*) as count from doctrailers where estatus='EnCar' and year(fecha) = '$anio' and month(fecha)= '$mes' and TraId<>13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $trailx = $row->count;

  $viajedocum = $trail13+$trailx;

  $respuesta = array('error' => TRUE,
    "cross" => $cross,
    "cpemb" => $cpemb,
    "cpalm" => $cpalm,
    "trail" => $trailers,
    "carro" => $carrolib,
    "fact" => $facgen,
    "factxc" => $facporcob,    
    "viajcros" => $viajecross,
    "viajdocum" => $viajedocum
  );

  $this->response( $respuesta );

}

/************************************************************************/
 public function cargasem_post(){
  $this->datap = $this->post();
  $semana=$this->datap['semana'];
  $sem = explode(' ', $semana);

//  $f1 =strtotime ($sem[0]);
//  $f2 =strtotime ($sem[1]);
 // $f1 = date('Ymd', strtotime($sem[0]));
 // $f2 = date('Ymd', strtotime($sem[1]));
  $f1 = explode('/', $sem[0]);
  $f2 = explode('/', $sem[1]);
  $fecha1 = $f1[2].substr("00{$f1[1]}", -2).substr("00{$f1[0]}", -2);
  $fecha2 = $f2[2].substr("00{$f2[1]}", -2).substr("00{$f2[0]}", -2);

  $xsql="select COUNT(*) as count from cartaporte where (tipo='Cros' or tipo='Alma') and destino='Almacen' and CAST(fecha AS DATE)>='".$fecha1."' and CAST(fecha AS DATE)<='".$fecha2."' and TraId=13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cpalm13 = $row->count;

  //Cartas Porte Almacen <> 13
  $xsql="select COUNT(*) as count from cartaporte where (tipo='Cros' or tipo='Alma') and destino='Almacen' and CAST(fecha AS DATE)>='".$fecha1."' and CAST(fecha AS DATE)<='".$fecha2."' and TraId<>13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cpalmx = $row->count;

  //Cartas Porte Embarque 13
  $xsql="select COUNT(*) as count from cartaporte where (tipo='Cros' or tipo='Alma') and destino='Embarque' and CAST(fecha AS DATE)>='".$fecha1."' and CAST(fecha AS DATE)<='".$fecha2."' and TraId=13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cpemb13 = $row->count;

  //Cartas Porte Embarque <> 13
  $xsql="select COUNT(*) as count from cartaporte where (tipo='Cros' or tipo='Alma') and destino='Embarque' and CAST(fecha AS DATE)>='".$fecha1."' and CAST(fecha AS DATE)<='".$fecha2."' and TraId<>13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $cpembx = $row->count;

  //Entradas 13
  $xsql="select COUNT(*) as count from entradas where estatus<>'Pend' and CAST(fecha AS DATE)>='".$fecha1."' and CAST(fecha AS DATE)<='".$fecha2."' and TraId=13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $ent13 = $row->count;

  //Entradas <> 13
  $xsql="select COUNT(*) as count from entradas where estatus<>'Pend' and CAST(fecha AS DATE)>='".$fecha1."' and CAST(fecha AS DATE)<='".$fecha2."' and TraId<>13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $entx = $row->count;

  $viajecross = $cpalm13+$cpalmx+$cpemb13+$cpembx+$ent13+$entx;


/**************************************/

  $xsql="select COUNT(*) as count from doctrailers where estatus='EnCar' and CAST(fecha AS DATE)>='".$fecha1."' and CAST(fecha AS DATE)<='".$fecha2."' and TraId=13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $trail13 = $row->count;

  //Trailers <> 13
  $xsql="select COUNT(*) as count from doctrailers where estatus='EnCar' and CAST(fecha AS DATE)>='".$fecha1."' and CAST(fecha AS DATE)<='".$fecha2."' and TraId<>13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $trailx = $row->count;

  $viajedocum = $trail13+$trailx;

  $respuesta = array('error' => TRUE,
    "viajcros" => $viajecross,
    "viajdocum" => $viajedocum
  );

  $this->response( $respuesta );

}
/************************************************************************/
 public function cargaano_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];

  $xsql="select COUNT(*) as count from facturas where year(fecha)='".$anio."' and timbrada='S' and tipocfdi='I' and cancel<>'S'";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $factug = $row->count;

  $xsql="select COUNT(*) as count from facturas where year(fechapago)='".$anio."' and timbrada='S' and tipocfdi='I' and cancel<>'S' and pagada='S'";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $factuc = $row->count;


  $respuesta = array('error' => TRUE,
    "factug" => $factug,
    "factuc" => $factuc
  );

  $this->response( $respuesta );

}

}
