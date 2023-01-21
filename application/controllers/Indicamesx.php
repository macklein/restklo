<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Indicames extends REST_Controller {


  var $datap,$data,$id,$empid;

  public function __construct(){
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    header("Access-Control-Allow-Origin: *");
    parent::__construct();
    $this->load->database();

  }

/************************************************************************/
 public function cross1_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  /* CROSS DOCK */

  $xsql = "select a.cliid, count(a.crosid) as totcant, b.nombre from crossdock a, clientes b where a.cliid=b.cliid and a.estatus='Term' and year(fecha)='$anio' and month(fecha)='$mes' group by a.cliid ";
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );

  $this->response( $respuesta );

}
/************************************************************************/
 public function cross2_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $cliid=$this->datap['cliid'];

  $xsql="select a.crosid, a.fecha, a.carnum, a.cantidad, a.capturados, a.enviados, a.enviado, b.nombre, c.descripcion, (a.cantidad-a.capturados) as faltan from crossdock a, clientes b, clientesmat c where a.cliid=b.cliid and a.matcliid=c.matcliid and a.estatus='Term' and year(fecha) ='$anio' and month(fecha)='$mes' and a.cliid=".$cliid; 
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  $this->response( $respuesta );
}
/************************************************************************/
 public function cpemb1_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  /* CROSS DOCK */

  $xsql = "select a.cliid, count(a.carid) as totcant, b.nombre from cartaporte a, clientes b where a.tipo<>'PendC' and a.tipo<>'PendA' and a.destino='Embarque' and a.cliid=b.cliid  and year(fecha) = '$anio' and month(fecha)= '$mes' group by a.cliid ";
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );

  $this->response( $respuesta );

}
/************************************************************************/
 public function cpemb2_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $cliid=$this->datap['cliid'];

  $xsql = "select a.carid, a.fecha, a.destino, left(b.nombre,15) as nombre, c.nombre as nombretrans from cartaporte a, clientes b, transportistas c where a.cliid=b.cliid and a.traid=c.traid and a.tipo<>'PendC' and a.tipo<>'PendA' and a.destino='Embarque' and year(fecha) = '$anio' and month(fecha)= '$mes' and a.cliid=".$cliid;
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  $this->response( $respuesta );
}
/************************************************************************/
 public function cpalm1_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $xsql = "select a.cliid, count(a.carid) as totcant, b.nombre from cartaporte a, clientes b where a.tipo<>'PendC' and a.tipo<>'PendA' and a.destino='Almacen' and a.cliid=b.cliid  and year(fecha) = '$anio' and month(fecha)= '$mes' group by a.cliid ";
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  $this->response( $respuesta );
}
/************************************************************************/
 public function cpalm2_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $cliid=$this->datap['cliid'];
  $xsql = "select a.carid, a.fecha, a.destino, left(b.nombre,15) as nombre, c.nombre as nombretrans from cartaporte a, clientes b, transportistas c where a.cliid=b.cliid and a.traid=c.traid and a.tipo<>'PendC' and a.tipo<>'PendA' and a.destino='Almacen' and year(fecha) = '$anio' and month(fecha)= '$mes' and a.cliid=".$cliid;
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  $this->response( $respuesta );
}
/************************************************************************/
 public function trail1_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  /* CROSS DOCK */
  $xsql = "select a.cliid, count(a.traid) as totcant, b.nombre from doctrailers a, clientes b where a.cliid=b.cliid and a.estatus='EnCar' and year(fecha) = '$anio' and month(fecha)= '$mes' group by a.cliid ";
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  $this->response( $respuesta );
}
/************************************************************************/
 public function trail2_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $cliid=$this->datap['cliid'];
  $xsql = "select a.doctraid, a.fecha, a.pedimento, a.caja, a.piezas, a.descargadas, left(b.nombre,12) as nombretrans, left(c.nombre,15) as nombre from doctrailers a, transportistas b, clientes c where a.traid=b.traid and a.cliid=c.cliid and a.estatus='EnCar' and year(fecha) = '$anio' and month(fecha)= '$mes' and a.cliid=".$cliid; 
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  $this->response( $respuesta );
}
/************************************************************************/
 public function carro1_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $xsql = "select a.cliid, count(a.doccarid) as totcant, b.nombre from doccarros a, clientes b where a.cliid=b.cliid and a.estatus='Term' and year(fecha) = '$anio' and month(fecha)= '$mes' group by a.cliid ";
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  $this->response( $respuesta );
}
/************************************************************************/
 public function carro2_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $cliid=$this->datap['cliid'];
  $xsql="select a.doccarid, a.sello, a.fecha, b.nombre, c.descripcion, a.carnum from doccarros a, clientes b, clientesdir c where a.cliid=b.cliid and a.dircliid=c.dircliid and a.estatus='Term' and year(fecha) = '$anio' and month(fecha)= '$mes' and a.cliid=".$cliid; 
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  $this->response( $respuesta );
}
/************************************************************************/
 public function fact1_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $xsql = "select a.cliid, a.empreid, a.serie, a.moneda, count(a.numfac) as totcant, sum(a.total) as total, a.nombre from facturas a where a.timbrada='S' and a.tipocfdi='I' and cancel<>'S' and year(a.fecha)='$anio' and month(a.fecha)='$mes' group by a.empreid,a.moneda ";
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  $this->response( $respuesta );
}
/************************************************************************/
 public function fact2_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $empreid=$this->datap['empreid'];
  $moneda=$this->datap['moneda'];  
  $xsql = "select a.cliid, count(a.numfac) as totcant, sum(a.total) as total, a.moneda, a.nombre from facturas a where a.timbrada='S' and a.tipocfdi='I' and cancel<>'S' and year(a.fecha)='$anio' and month(a.fecha)='$mes' and a.empreid = '".$empreid."' and a.moneda = '".$moneda."' group by a.cliid ";
  $query = $this->db->query($xsql);

  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  $this->response( $respuesta );
}
/************************************************************************/
 public function fact3_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $cliid=$this->datap['cliid'];
  $moneda=$this->datap['moneda'];  
  //$xsql = "select a.serie, a.facid, CONCAT(a.serie,'-',ltrim(a.facid)) as numfac, a.cliid, a.fecha, day(a.fecha) as dia, moneda, a.pagada, count(a.numfac) as totcant, sum(a.total) as total, a.nombre from facturas a where a.timbrada='S' and a.tipocfdi='I' and cancel<>'S' and year(fecha)='$anio' and month(fecha)='$mes' and a.cliid=".$cliid." group by DAY(fecha),moneda ";

  $xsql="select CONCAT(serie,'-',ltrim(facid)) as numf, empreid, numfac, facid, fecha, serie, cliid, nombre, subtotal, iva, retiva, total, timbrada, pagada, moneda, cancel from facturas where timbrada='S' and tipocfdi='I' and cancel<>'S' and year(fecha) ='$anio' and month(fecha)='$mes' and cliid=".$cliid." and moneda='".$moneda."'"; 

  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  $this->response( $respuesta );
}
/************************************************************************/
 public function factxc1_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $xsql = "select a.empreid, a.serie, a.moneda, count(a.numfac) as totcant, sum(a.total) as total, a.nombre from facturas a where a.timbrada='S' and a.tipocfdi='I' and cancel<>'S' and pagada='N' group by a.empreid,a.moneda ";
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  $this->response( $respuesta );
}
/************************************************************************/
 public function factxc2_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $empreid=$this->datap['empreid'];
  $moneda=$this->datap['moneda'];  
//  $xsql="select empreid, numfac, facid, fecha, serie, cliid, nombre, moneda, subtotal, iva, retiva, total, timbrada, pagada, cancel from facturas where timbrada='S' and tipocfdi='I' and cancel<>'S' and pagada='N' and cliid=".$empreid." and moneda='".$moneda."'"; 

$xsql = "select a.cliid, count(a.numfac) as totcant, sum(a.total) as total, a.moneda, left(a.nombre,15) as nombre from facturas a where a.timbrada='S' and a.tipocfdi='I' and cancel<>'S' and pagada='N' and a.empreid='".$empreid."' and moneda='".$moneda."' group by a.cliid ";

  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );

/*
  $respuesta = array('error' => TRUE,
    'items' => $xsql
  );
  */
  $this->response( $respuesta );
}
/************************************************************************/
 public function factxc3_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $cliid=$this->datap['cliid'];
  $moneda=$this->datap['moneda'];  
  $xsql="select CONCAT(serie,'-',ltrim(facid)) as numf, empreid, numfac, facid, fecha, serie, cliid, nombre, moneda, subtotal, iva, retiva, total, timbrada, pagada, cancel from facturas where timbrada='S' and tipocfdi='I' and cancel<>'S' and pagada='N' and cliid=".$cliid." and moneda='".$moneda."'"; 

  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array(),
    'xsql' => $xsql
  );
  $this->response( $respuesta );
}
/************************************************************************/
 public function viajcros1_post(){
  $this->datap = $this->post();
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $xsql="select COUNT(*) as count from cartaporte where (tipo='Cros' or tipo='Alma') and destino='Almacen' and year(fecha) = '$anio' and month(fecha)= '$mes' and TraId=13 ";
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
  $xsql="select COUNT(*) as count from entradas where estatus<>'Pend' and year(fecha) = '$anio' and month(fecha)= '$mes' and TraId=13 and TraId=1 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $ent13 = $row->count;


  //Entradas <> 13
  $xsql="select COUNT(*) as count from entradas where estatus<>'Pend' and year(fecha) = '$anio' and month(fecha)= '$mes' and TraId<>13 ";
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
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $nombre=$this->datap['nombre'];
  $tipo=$this->datap['tipo'];

  $condtra = "and a.TraId<>13 ";
  if (substr($nombre,0,3)=="Arm"){  //Si es Armando
    $condtra = "and a.TraId=13";
  }

  switch ($tipo) {
    case 'cpemb':
        $xsql="select COUNT(a.carid) as count, a.choid, b.nombrechofer, day(a.fecha) as dia from cartaporte a, transchofer b where a.choid=b.choid and (a.tipo='Cros' or a.tipo='Alma') and a.destino='Embarque' and year(a.fecha) = '$anio' and month(a.fecha)= '$mes' $condtra group by a.choid";
        break;
    case 'cpalm':
        $xsql="select COUNT(a.carid) as count, a.choid, b.nombrechofer, day(a.fecha) as dia from cartaporte a, transchofer b where a.choid=b.choid and (a.tipo='Cros' or a.tipo='Alma') and a.destino='Almacen' and year(a.fecha) = '$anio' and month(a.fecha)= '$mes' $condtra group by a.choid ";
        break;
    case 'ent':
        $xsql = "select COUNT(a.entid) as count, a.choid, b.nombrechofer, day(a.fecha) as dia  from entradas a, transchofer b where a.choid=b.choid and a.estatus<>'Pend' and year(a.fecha) = '$anio' and month(a.fecha)= '$mes' $condtra group by a.choid";
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
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $choid=$this->datap['choid'];
  $tipo=$this->datap['tipo'];
  switch ($tipo) {
    case 'cpemb':
        $xsql="select a.carid as docid, DATE_FORMAT(a.fecha,'%d/%m/%Y') AS fecha, a.cliid, left(b.nombre,15) as nombre from cartaporte a, clientes b where (a.tipo='Cros' or a.tipo='Alma') and a.cliid=b.cliid and a.destino='Embarque' and year(a.fecha) = '$anio' and month(a.fecha)= '$mes' and a.choid=".$choid;
        break;
    case 'cpalm':
        $xsql="select a.carid as docid, DATE_FORMAT(a.fecha,'%d/%m/%Y') AS fecha, a.cliid, left(b.nombre,15) as nombre from cartaporte a, clientes b where (a.tipo='Cros' or a.tipo='Alma') and a.cliid=b.cliid and a.destino='Almacen' and year(a.fecha) = '$anio' and month(a.fecha)= '$mes' and a.choid=".$choid;
        break;
    case 'ent':
        $xsql="select a.entid as docid, DATE_FORMAT(a.fecha,'%d/%m/%Y') AS fecha, a.cliid, left(b.nombre,15) as nombre from entradas a, clientes b where a.estatus<>'Pend' and a.cliid=b.cliid and  year(a.fecha) = '$anio' and month(a.fecha)= '$mes' and a.choid=".$choid;
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
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];

  //Trailers 13
  $xsql="select COUNT(*) as count from doctrailers where estatus='EnCar' and year(fecha) = '$anio' and month(fecha)= '$mes' and TraId=13 ";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $trail13 = $row->count;

  //Cartas Porte Almacen <> 13
  $xsql="select COUNT(*) as count from doctrailers where estatus='EnCar' and year(fecha) = '$anio' and month(fecha)= '$mes' and TraId<>13 ";

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
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $nombre=$this->datap['nombre'];
  $tipo=$this->datap['tipo'];

  if (substr($nombre,0,3)=="Arm"){  //Si es Armando
    $xsql ="select COUNT(a.doctraid) as count, a.choid, b.nombrechofer from doctrailers a, transchofer b where a.choid=b.choid and a.estatus='EnCar' and year(a.fecha) = '$anio' and month(a.fecha)= '$mes' and a.TraId=13 group by a.choid";
  }else{
    $xsql ="select COUNT(a.doctraid) as count, a.choid, b.nombrechofer from doctrailers a, transchofer b where a.choid=b.choid and a.estatus='EnCar' and year(a.fecha) = '$anio' and month(a.fecha)= '$mes' and a.TraId<>13 group by a.choid";
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
  $anio=$this->datap['anio'];
  $mes=$this->datap['mes'];
  $choid=$this->datap['choid'];
  $xsql ="select a.doctraid as docid, a.fecha, a.cliid, left(b.nombre,15) as nombre from doctrailers a, clientes b where a.estatus='EnCar' and a.cliid=b.cliid and year(a.fecha) = '$anio' and month(a.fecha)= '$mes' and a.choid=".$choid;

  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE,
    'items' => $query->result_array()
  );
  
  $this->response( $respuesta );
}


}
