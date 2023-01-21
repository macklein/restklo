<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Indicasem extends REST_Controller {


  var $datap,$data,$id,$empid;

  public function __construct(){
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    header("Access-Control-Allow-Origin: *");
    parent::__construct();
    $this->load->database();

  }

/************************************************************************/
 public function viajcros1_post(){
  $this->datap = $this->post();
  $semana=$this->datap['semana'];
  $sem = explode(' ', $semana);
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


  $count13 = $cpalm13+$cpemb13+$ent13;
  $countx = $cpalmx+$cpembx+$entx;

  $p13a = 0;
  $p13e = 0;
  $p13t = 0;
  $pxa = 0;
  $pxe = 0;
  $pxt = 0;
  if (($count13+$countx)>0){
    $p13a = round($cpalm13/($count13+$countx)*100);
    $p13e = round($cpemb13/($count13+$countx)*100);
    $p13t = round($ent13/($count13+$countx)*100);
    $pxa = round($cpalmx/($count13+$countx)*100);
    $pxe = round($cpembx/($count13+$countx)*100);
    $pxt = round($entx/($count13+$countx)*100);
  }


  $records[] = array('nombre'=>'Armando CpEmbarque', 'cant'=>$cpemb13, 'porcen'=>$p13e, 'tipo'=>'cpemb');
  $records[] = array('nombre'=>'Armando CpAlmacen', 'cant'=>$cpalm13, 'porcen'=>$p13a, 'tipo'=>'cpalm');
  $records[] = array('nombre'=>'Armando Entradas', 'cant'=>$ent13, 'porcen'=>$p13t, 'tipo'=>'ent');
  $records[] = array('nombre'=>'', 'cant'=>$cpemb13+$cpalm13+$ent13, 'porcen'=>$p13e+$p13a+$p13t, 'tipo'=>'tot');
  $records[] = array('nombre'=>'Otros CpEmbarque', 'cant'=>$cpembx, 'porcen'=>$pxe, 'tipo'=>'cpemb');
  $records[] = array('nombre'=>'Otros CpAlmacen', 'cant'=>$cpalmx, 'porcen'=>$pxa, 'tipo'=>'cpalm');
  $records[] = array('nombre'=>'Otros Entradas', 'cant'=>$entx, 'porcen'=>$pxt, 'tipo'=>'ent');
  $records[] = array('nombre'=>'', 'cant'=>$cpembx+$cpalmx+$entx, 'porcen'=>$pxe+$pxa+$pxt, 'tipo'=>'tot');


  $respuesta = array('error' => TRUE,
    'items' => $records
  );
  $this->response( $respuesta );
}
/************************************************************************/
 public function viajcros2_post(){
  $this->datap = $this->post();
  $semana=$this->datap['semana'];
  $sem = explode(' ', $semana);
  $f1 = explode('/', $sem[0]);
  $f2 = explode('/', $sem[1]);
  $fecha1 = $f1[2].substr("00{$f1[1]}", -2).substr("00{$f1[0]}", -2);
  $fecha2 = $f2[2].substr("00{$f2[1]}", -2).substr("00{$f2[0]}", -2);
  $nombre=$this->datap['nombre'];
  $tipo=$this->datap['tipo'];

  $condtra = "and a.TraId<>13 ";
  if (substr($nombre,0,3)=="Arm"){  //Si es Armando
    $condtra = "and a.TraId=13";
  }

  switch ($tipo) {
    case 'cpemb':
        $xsql="select COUNT(a.carid) as count, a.choid, b.nombrechofer, day(a.fecha) as dia from cartaporte a, transchofer b where a.choid=b.choid and (a.tipo='Cros' or a.tipo='Alma') and a.destino='Embarque' and CAST(fecha AS DATE)>='".$fecha1."' and CAST(fecha AS DATE)<='".$fecha2."' $condtra group by a.choid";
        break;
    case 'cpalm':
        $xsql="select COUNT(a.carid) as count, a.choid, b.nombrechofer, day(a.fecha) as dia from cartaporte a, transchofer b where a.choid=b.choid and (a.tipo='Cros' or a.tipo='Alma') and a.destino='Almacen' and CAST(fecha AS DATE)>='".$fecha1."' and CAST(fecha AS DATE)<='".$fecha2."' $condtra group by a.choid ";
        break;
    case 'ent':
        $xsql = "select COUNT(a.entid) as count, a.choid, b.nombrechofer, day(a.fecha) as dia  from entradas a, transchofer b where a.choid=b.choid and a.estatus<>'Pend' and CAST(fecha AS DATE)>='".$fecha1."' and CAST(fecha AS DATE)<='".$fecha2."' $condtra group by a.choid";
        break;
  }

  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  
  $this->response( $respuesta );
}
/************************************************************************/
 public function viajcros3_post(){
  $this->datap = $this->post();
  $semana=$this->datap['semana'];
  $sem = explode(' ', $semana);
  $f1 = explode('/', $sem[0]);
  $f2 = explode('/', $sem[1]);
  $fecha1 = $f1[2].substr("00{$f1[1]}", -2).substr("00{$f1[0]}", -2);
  $fecha2 = $f2[2].substr("00{$f2[1]}", -2).substr("00{$f2[0]}", -2);
  $choid=$this->datap['choid'];
  $tipo=$this->datap['tipo'];
  switch ($tipo) {
    case 'cpemb':
        $xsql="select a.carid as docid, DATE_FORMAT(a.fecha,'%d/%m/%Y') AS fecha, a.cliid, left(b.nombre,15) as nombre from cartaporte a, clientes b where (a.tipo='Cros' or a.tipo='Alma') and a.cliid=b.cliid and a.destino='Embarque' and CAST(fecha AS DATE)>='".$fecha1."' and CAST(fecha AS DATE)<='".$fecha2."' and a.choid=".$choid;
        break;
    case 'cpalm':
        $xsql="select a.carid as docid, DATE_FORMAT(a.fecha,'%d/%m/%Y') AS fecha, a.cliid, left(b.nombre,15) as nombre from cartaporte a, clientes b where (a.tipo='Cros' or a.tipo='Alma') and a.cliid=b.cliid and a.destino='Almacen' and CAST(fecha AS DATE)>='".$fecha1."' and CAST(fecha AS DATE)<='".$fecha2."' and a.choid=".$choid;
        break;
    case 'ent':
        $xsql="select a.entid as docid, DATE_FORMAT(a.fecha,'%d/%m/%Y') AS fecha, a.cliid, left(b.nombre,15) as nombre from entradas a, clientes b where a.estatus<>'Pend' and a.cliid=b.cliid and CAST(fecha AS DATE)>='".$fecha1."' and CAST(fecha AS DATE)<='".$fecha2."' and a.choid=".$choid;
        break;
  }
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  
  $this->response( $respuesta );
}


/************************************************************************/
 public function viajdocum1_post(){
  $this->datap = $this->post();
  $semana=$this->datap['semana'];
  $sem = explode(' ', $semana);
  $f1 = explode('/', $sem[0]);
  $f2 = explode('/', $sem[1]);
  $fecha1 = $f1[2].substr("00{$f1[1]}", -2).substr("00{$f1[0]}", -2);
  $fecha2 = $f2[2].substr("00{$f2[1]}", -2).substr("00{$f2[0]}", -2);

  //Trailers 13
  $xsql="select COUNT(*) as count from doctrailers where estatus='EnCar' and CAST(fecha AS DATE)>='".$fecha1."' and CAST(fecha AS DATE)<='".$fecha2."' and TraId=13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $trail13 = $row->count;

  //Cartas Porte Almacen <> 13
  $xsql="select COUNT(*) as count from doctrailers where estatus='EnCar' and CAST(fecha AS DATE)>='".$fecha1."' and CAST(fecha AS DATE)<='".$fecha2."' and TraId<>13 ";

  $query = $this->db->query($xsql);
  $row = $query->row();
  $trailx = $row->count;

  $count13 = $trail13;
  $countx = $trailx;
  $p13 = 0;
  $px = 0;
  if (($count13+$countx)>0){
    $p13 = round($count13/($count13+$countx)*100);
    $px = round($countx/($count13+$countx)*100);
  }
  $records[] = array('nombre'=>'Armando Terrazas', 'cant'=>$trail13, 'porcen'=>$p13);
  $records[] = array('nombre'=>'Todos los Demas', 'cant'=>$trailx, 'porcen'=>$px);
  $respuesta = array('error' => TRUE,
    'items' => $records
  );
  $this->response( $respuesta );
}
/************************************************************************/
 public function viajdocum2_post(){
  $this->datap = $this->post();
  $semana=$this->datap['semana'];
  $sem = explode(' ', $semana);
  $f1 = explode('/', $sem[0]);
  $f2 = explode('/', $sem[1]);
  $fecha1 = $f1[2].substr("00{$f1[1]}", -2).substr("00{$f1[0]}", -2);
  $fecha2 = $f2[2].substr("00{$f2[1]}", -2).substr("00{$f2[0]}", -2);
  $nombre=$this->datap['nombre'];
  $tipo=$this->datap['tipo'];

  $condtra = "and a.TraId<>13 ";
  if (substr($nombre,0,3)=="Arm"){  //Si es Armando
    $condtra = "and a.TraId=13";
  }
  if (substr($nombre,0,3)=="Arm"){  //Si es Armando
    $xsql ="select COUNT(a.doctraid) as count, a.choid, b.nombrechofer from doctrailers a, transchofer b where a.choid=b.choid and a.estatus='EnCar' and CAST(a.fecha AS DATE)>='".$fecha1."' and CAST(a.fecha AS DATE)<='".$fecha2."' $condtra group by a.choid";
  }else{
    $xsql ="select COUNT(a.doctraid) as count, a.choid, b.nombrechofer from doctrailers a, transchofer b where a.choid=b.choid and a.estatus='EnCar' and CAST(a.fecha AS DATE)>='".$fecha1."' and CAST(a.fecha AS DATE)<='".$fecha2."' $condtra group by a.choid";
  }

  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  
  $this->response( $respuesta );
}
/************************************************************************/
 public function viajdocum3_post(){
  $this->datap = $this->post();
  $semana=$this->datap['semana'];
  $sem = explode(' ', $semana);
  $f1 = explode('/', $sem[0]);
  $f2 = explode('/', $sem[1]);
  $fecha1 = $f1[2].substr("00{$f1[1]}", -2).substr("00{$f1[0]}", -2);
  $fecha2 = $f2[2].substr("00{$f2[1]}", -2).substr("00{$f2[0]}", -2);
  $choid=$this->datap['choid'];
  $tipo=$this->datap['tipo'];
  $xsql ="select a.doctraid as docid, a.fecha, a.cliid, left(b.nombre,15) as nombre from doctrailers a, clientes b where a.estatus='EnCar' and a.cliid=b.cliid and CAST(a.fecha AS DATE)>='".$fecha1."' and CAST(a.fecha AS DATE)<='".$fecha2."' and a.choid=".$choid;

  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  $this->response( $respuesta );
}


}
