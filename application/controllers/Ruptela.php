<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
class Ruptela extends REST_Controller {


  var $datap,$data,$id, $empid, $rupdata, $lat1, $lon1, $lat2, $lon2, $difmin, $xp;

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
 

    $cond="vehid>0 ";
   if (!empty($buscar)){
      $cond .= " and (descrip LIKE '%".$buscar."%' or placas LIKE '%".$buscar."%' or serie LIKE '%".$buscar."%' )"; 
    }


  $tables="vehiculos ";
  $campos="vehid, descrip, marca, modelo, placas, serie ";
  $sWhere=" $cond ";
  $sWhere.=" order by vehid desc ";
    

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

/*    $respuesta = array('error' => TRUE, "message" => $xsql);
    $this->response( $respuesta );
*/
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
  // $this->datap = $this->post();
  // $id=$this->datap['id'];
$id=1;
  $xsql ="Select vehid, empid, empreid, descrip, placas, marca, modelo, anio, simcard, serie from vehiculos where vehid=$id";  
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
private function NumEstado($edo){
  $numedo = 8;
  switch ($edo) {
    case "EnFCC";
      $numedo = 1;
      break;
    case "EnBase1";
      $numedo = 2;
      break;
    case "EnBase2";
      $numedo = 3;
      break;
    case "EnRuta";
      $numedo = 4;
      break;
    case "EnCte";
      $numedo = 5;
      break;
    case "Regresando";
      $numedo = 6;
      break;
    case "Fuera";
      $numedo = 7;
      break;
  }
  return $numedo;
}


private function addEstado($vehid, $edo, $estid, $ordid) {
  date_default_timezone_set('America/Ciudad_Juarez'); 
  // $vehid, 'EnRuta', $estid, $ordid
  $numedo = $this->NumEstado($edo);
  $fecha=date("Y-m-d H:i:s");
  if ($estid>0){
    $xsql = "Update vehstados set estatus='Term' where estid=$estid";
    $query = $this->db->query($xsql);
  }
 /* if ($ordid>0){
    $xsql = "Update gpsorden set estatus='Term' where ordid=$ordid";
    $query = $this->db->query($xsql);
  } */

  $xsql = "Insert Into vehstados set vehid=$vehid, fechahora ='$fecha', ordid='$ordid', estado='$edo', numedo=$numedo, estatus='Actual' ";
  $query = $this->db->query($xsql);

  $xsql = "Update vehiculos set estatus='$edo', estado='$edo' Where vehid=$vehid";
  $query = $this->db->query($xsql);
  $respuesta = array('error' => TRUE, "message" => "Listo");

//  $this->response( $respuesta );
}
  
private function diffecha($f1){
  date_default_timezone_set('America/Ciudad_Juarez'); 
 $date1 = new DateTime($f1);
 $date2 = new DateTime("now");
$df = $date1->diff($date2);
$this->difmin = 0;
// echo get_format($diff);
//   function get_format($df) {

    $str = '';
    $str .= ($df->invert == 1) ? ' - ' : '';
    if ($df->y > 0) {
      $this->difmin = 9999;
        // years
        $str .= ($df->y > 1) ? $df->y . ' Año ' : $df->y . ' Año ';
    } if ($df->m > 0) {
      $this->difmin = 9999;
        // month
        $str .= ($df->m > 1) ? $df->m . ' Mes ' : $df->m . ' Mes ';
    } if ($df->d > 0) {
      $this->difmin = 9999;
        // days
        $str .= ($df->d > 1) ? $df->d . 'D.' : $df->d . 'D.';
    } if ($df->h > 0) {
      $this->difmin = 9999;
        // hours
        $str .= ($df->h > 1) ? $df->h . 'H.' : $df->h . 'H.';
    } if ($df->i > 0) {
        if ($this->difmin == 0){
          $this->difmin =$df->i;
        }
        // minutes
        $str .= ($df->i > 1) ? $df->i . 'M.' : $df->i . 'M.';
    } if ($df->s > 0) {
        // seconds
        $str .= ($df->s > 1) ? $df->s . 'S.' : $df->s . 'S.';
    }

    return $str;
}


function pointInPolygon($point, $polygon){
  $return = false; 
  foreach($polygon as $k=>$p){ 
    if(!$k) 
      $k_prev = count($polygon)-1; 
    else 
      $k_prev = $k-1; 
    if(($p[1]<$point[1] && $polygon[$k_prev][1]>=$point[1] || $polygon[$k_prev][1]<$point[1] && $p[1]>=$point[1]) && ($p[0]<=$point[0] || $polygon[$k_prev][0]<=$point[0])){ 
      if($p[0]+($point[1]-$p[1])/($polygon[$k_prev][1]-$p[1])*($polygon[$k_prev][0]-$p[0])<$point[0]){ 
        $return = !$return; 
      } 
    } 
  } 
  return $return; 
}

public function alta_post(){
  date_default_timezone_set('America/Ciudad_Juarez'); 
  $this->getdatos();
  $fecha=date("Y-m-d H:i:s"); 
  $entro1="Simondon";
  $imei="12345678";
  
  $fcc=array(31.722602, -106.472918); 
  $bodega1=array(31.72251780, -106.474329); 
  $bodega2=array(31.711737, -106.425786); 
 
  $data = array('datos'=>$entro1,'fecha'=>$fecha,'imei'=>$imei);
  $pts = "";
  $accion=$this->rupdata["accion"]; // Temporalmente esta accion
  $imei=$this->rupdata["imei"];
  $encend=$this->rupdata["encend"];
  $imeic=$this->rupdata["imeic"];
  $dat=json_decode($this->rupdata["datosm"]);
  $latact = $dat->latitude/10000000;
  $lonact = $dat->longitude/10000000;
  $this->lat1 = $latact; 
  $this->lon1 = $lonact; 
  $dist=7647;
  $entro1="Nada ";
  $xsql ="Select vehid, empid, empreid, descrip, placas, marca, modelo, anio, simcard, serie, carid, estatus, latitude, longitude from vehiculos where imei=$imei";  
  $query = $this->db->query($xsql);
  $numedo = 0;
  // $this->id = $query;
  if ($vehid=="3"){
    $xsqldat = "Insert into datos set dato='2 nvo imei = $imei' ";
    $querydat = $this->db->query($xsqldat);
  }  
  $this->xp='Entro ';
  $estatus = "";
  $carid = 0;
  $estid = 0;
  $ordid = 0;
  $latdest = "";
  $londest = "";
  $descr2 = "";
  if ($query) {
      $row = $query->row();
      $vehid = $row->vehid;
   
      $xsql="SELECT count(*) AS numrows FROM vehstados where vehid=$vehid and estatus='Actual' limit 1"; 
      $queryedo = $this->db->query($xsql);
      $row = $queryedo->row();
      $numrows = $row->numrows;
      if ($numrows>0){
        $xsql ="Select estid,estado,numedo,fechahora,fechafin,ordid from vehstados where vehid=$vehid and estatus='Actual' limit 1";  
        $queryedo = $this->db->query($xsql);
        if ($queryedo) {
          $rowedo = $queryedo->row();
          $estid = $rowedo->estid;
          $estado = $rowedo->estado;
          $numedo = $rowedo->numedo;
          $fechahora = $rowedo->fechahora;
          $fechafin = $rowedo->fechafin;
        }
      }  
      if ($vehid=="3"){
        $xsqldat = "Insert into datos set dato='3 nvo imei = $imei numedo=$numedo' ";
        $querydat = $this->db->query($xsqldat);
      }
      $xsql="SELECT count(*) AS numrows FROM gpsorden where vehid=$vehid and estatus='Pend' ";
      $queryord = $this->db->query($xsql);
      $row = $queryord->row();
      $numrows = $row->numrows;
      if ($numrows>0){
        $xsql ="Select ordid,sitid1,sitid2,carid,cliid from gpsorden where vehid=$vehid and estatus='Pend' ";  
        $queryord = $this->db->query($xsql);
        if ($queryord) {  
          $rord = $queryord->row();
          $ordid = $rord->ordid;
          $sitid1 = $rord->sitid1;
          $sitid2 = $rord->sitid2;
          $carid = $rord->carid;
          $cliid = $rord->cliid;      
          if ($vehid=="3"){
            $xsqldat = "Insert into datos set dato='4 nvo imei = $imei numedo=$numedo' ";
            $querydat = $this->db->query($xsqldat);
          }      
          if ($sitid1>0 && $sitid2>0){
            $xsql ="Select sitid,latitude,longitude,llegada,salida,descrip from gpssitios where sitid=$sitid1";  
            $querys1 = $this->db->query($xsql);
            if ($querys1){
              $rows1 = $querys1->row();
              $lats1 = $rows1->latitude;
              $lons1 = $rows1->longitude;
              $lleg1 = $rows1->llegada;
              $sal1 = $rows1->salida; 
              $descr1 = $rows1->descrip;           
              if ($vehid=="3"){
                $xsqldat = "Insert into datos set dato='5 nvo imei = $imei numedo=$numedo' ";
                $querydat = $this->db->query($xsqldat);
              }               
            }    
            $xsql ="Select sitid,latitude,longitude,llegada,salida,descrip from gpssitios where sitid=$sitid2";  
            $querys2 = $this->db->query($xsql);
            if ($querys2){
              $rows2 = $querys2->row();
              $lats2 = $rows2->latitude;
              $lons2 = $rows2->longitude;
              $lleg2 = $rows2->llegada;
              $sal2 = $rows2->salida;     
              $descr2 = $rows2->descrip;
              if ($vehid=="3"){
                $xsqldat = "Insert into datos set dato='6 nvo imei = $imei numedo=$numedo' ";
                $querydat = $this->db->query($xsqldat);
              }      
            }
          }          
        }    
      }
  }

//  if ($ordid==0 && ($numedo==0 || $numedo>2)) {
    $dist1 = $this->getKilometros($latact, $lonact, $fcc[0], $fcc[1]);
    $dist2 = $this->getKilometros($latact, $lonact, $bodega1[0], $bodega1[1]);
    $dist3 = $this->getKilometros($latact, $lonact, $bodega2[0], $bodega2[1]);
    $dist4 = $this->getKilometros($latact, $lonact, $lats2, $lons2);

    $this->xp=$this->xp.' D1:'.$dist1.' D2:'.$dist2;
if ($vehid=="3"){
   $xsqldat = "Insert into datos set dato='7 nvo vehiculo = $vehid d1=$dist1 d2=$dist2 d3=$dist3 d4=$dist4 numedo=$numedo ' ";
   $querydat = $this->db->query($xsqldat);
}


//    $xsql = "Select ordid,ruta,vehid,choid,sitid1,sitid2,carid,cliid,placas from gpsorden where vehid=$vehid and estatus='Pend' ";
//    $queryord = $this->db->query($xsql);
$listo = 0;
    if ($dist1<.04 && $numedo<>1){ // Primer Estatus en FCC
      $this->id = "Agregar Estado en FCC " ;
      if ($vehid=="3"){
        $xsqldat = "Insert into datos set dato='fcc nvo imei = $imei ' ";
        $querydat = $this->db->query($xsqldat);  
      }
      $this->addEstado($vehid, "EnFCC", $estid, 0);
      $listo = 1;
    }
    if ($dist2<.5 && $numedo<>2 && $listo==0){ // Primer Estatus cuando esta en Bodega
      $this->id = "Agregar Estado en Base 1 " ;
      if ($vehid=="3"){
        $xsqldat = "Insert into datos set dato='base1 nvo imei = $imei ' ";
        $querydat = $this->db->query($xsqldat);
      }
      $this->addEstado($vehid, "EnBase1", $estid, 0);
      $listo = 1;
    }
    if ($dist3<.5 && $numedo<>3 && $listo==0){ // Agregar Viaje Cuando va llegando con el Cliente
      $this->id = "Agregar Estado en Base 2" ;
      if ($vehid=="3"){
        $xsqldat = "Insert into datos set dato='base2 nvo imei = $imei ' ";
        $querydat = $this->db->query($xsqldat);
      }
      $this->addEstado($vehid, "EnBase2", $estid, 0);
      $listo = 1;
    }
    if ($vehid=="3"){
      $xsqldat = "Insert into datos set dato='Antes nvo orden = $ordid, estid = $estid $estado' ";
      $querydat = $this->db->query($xsqldat);
    }
    if ($ordid > 0 && $estid > 0 && $listo==0){
      if ($vehid=="3"){
        $xsqldat = "Insert into datos set dato='Ruta1 nvo numedo = $numedo $dist1 $dist2 $dist3 $dist4' ";
        $querydat = $this->db->query($xsqldat);
      }
      if ($dist1 > 1 && $numedo == 1){
        // Si esta en Base1;
        // Cambia a Estado En Ruta;
        if ($vehid=="3"){
          $xsqldat = "Insert into datos set dato='Ruta1 nvo imei = $imei ' ";
          $querydat = $this->db->query($xsqldat);  
        }
        $this->addEstado($vehid, 'EnRuta', $estid, $ordid);
        $listo = 1;
      }
      if ($dist2 > 1 && $numedo == 2 && $listo==0){
        // Si esta en Base2
        // Cambia a Estado En Ruta
        if ($vehid=="3"){
          $xsqldat = "Insert into datos set dato='Ruta2 nvo imei = $imei  ' ";
          $querydat = $this->db->query($xsqldat);  
        }
        $this->addEstado($vehid, 'EnRuta', $estid, $ordid);  
        $listo = 1;
      }
      if ($dist3 > 1 && $numedo == 3 && $listo==0){
        // Si esta en Base2
        // Cambia a Estado En Ruta
        if ($vehid=="3"){
          $xsqldat = "Insert into datos set dato='Ruta3 nvo imei = $imei  ' ";
          $querydat = $this->db->query($xsqldat);  
        }
        $this->addEstado($vehid, 'EnRuta', $estid, $ordid);  
        $listo = 1;
      }
    }    

    if ($estado=="EnRuta"){
      if ($dist4<.5){ // Ya llego con Cliente
        if ($descr2 == "FCC" || $descr2 == "Bodega 1" || $descr2 == "Bodega 2"){
          if ($dist1<.04 && $descr2 == "FFCC" && $listo==0){ // Primer Estatus en FCC
            $this->id = "Agregar Estado en FCC " ;
            if ($vehid=="3"){
              $xsqldat = "Insert into datos set dato='fcc 2 nvo imei = $imei ' ";
              $querydat = $this->db->query($xsqldat);     
            }
            $this->addEstado($vehid, "EnFCC", $estid, $ordid);
            $listo = 1;
          }
          if ($dist2<.5 && $numedo<>2 && $listo==0){ // Primer Estatus cuando esta en Bodega
            $this->id = "Agregar Estado en Base 1 " ;
            if ($vehid=="3"){
              $xsqldat = "Insert into datos set dato='base1 2 nvo imei = $imei  ' ";
              $querydat = $this->db->query($xsqldat);
            }
            $this->addEstado($vehid, "EnBase1", $estid, $ordid);
            $listo = 1;
          }
          if ($dist3<.5 && $numedo<>3 && $listo==0){ // Agregar Viaje Cuando va llegando con el Cliente
            $this->id = "Agregar Estado en Base 2" ;
            if ($vehid=="3"){
              $xsqldat = "Insert into datos set dato='base2 2 nvo imei = $imei  ' ";
              $querydat = $this->db->query($xsqldat);
            }
            $this->addEstado($vehid, "EnBase2", $estid, $ordid);
            $listo = 1;
          }      
        }else{
          if ($listo==0){
            if ($vehid=="3"){
              $xsqldat = "Insert into datos set dato='cte nvo imei = $imei  ' ";
              $querydat = $this->db->query($xsqldat);
            }
            $this->addEstado($vehid, "EnCte", $estid, $ordid);
            $listo = 1;
          }
        }
      }
    }
    if ($vehid=="3"){
      $xsqldat = "Insert into datos set dato='edo $estado nvo imei = $imei ' ";
      $querydat = $this->db->query($xsqldat);
    }
    if ($estado=="EnCte" && $listo==0){
      if ($dist4>1){ // Saliendo de el Cliente
        if ($vehid=="3"){
          $xsqldat = "Insert into datos set dato='regre nvo imei = $imei  ' ";
          $querydat = $this->db->query($xsqldat);
        }
        $this->addEstado($vehid, "Regresando", $estid, $ordid);
        $listo = 1;
      }
    }








// }
  $fecha=date("Y-m-d H:i:s");
  $data = array(
        'datos'=>$entro1,
        'fecha'=>$fecha,
        'imei'=>$imei,
        'imeical'=>$imeic,
        'timestam'=>$dat->timestamp,
        'latitude'=>$latact,
        'longitude'=>$lonact,
        'altitude'=>$dat->altitude,
        'angle'=>$dat->angle,
        'nvo'=>'E',
        'satelites'=>$dat->satellites,
        'hdop'=>$dat->hdop,
        'speed'=>$dat->speed,
        'vehid'=>$vehid,
        'encend'=>$encend,
        'eventid'=>$dat->event_id
    );
  $this->db->insert('ruptela',$data);
  $id=$this->db->insert_id();
  if ($id>0){
      $respuesta = array('error' => FALSE,'id' => $data);
    }else{
      $respuesta = array('error' => TRUE);
   }
  //$myString = print_r($this->xp, TRUE);
  $respuesta = array('error' => FALSE, 'vehid' => $vehid);
  $this->response( $respuesta ); 
}

 private function rad($x) { 
  return $x * pi() / 180; 
 }

private function getKilometros($lat1, $lon1, $lat2, $lon2) {
    $r = 6378.137; // Radio de la tierra en km
    $dLat = $this->rad($lat2 - $lat1);
    $dLong = $this->rad($lon2 - $lon1);
    $a = sin($dLat / 2) * sin($dLat / 2) + cos($this->rad($lat1)) * cos($this->rad($lat2)) * sin($dLong / 2) * sin($dLong / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $d = $r * $c;
    return(number_format($d, 3)); 
}  

private function getdatos(){
  // Fetch content and determine boundary
  $raw_data = file_get_contents('php://input');
  $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));
  // Fetch each part
  $parts = array_slice(explode($boundary, $raw_data), 1);
  $data = array();
  foreach ($parts as $part) {
      // If this is the last part, break
      if ($part == "--\r\n") break; 
      // Separate content from headers
      $part = ltrim($part, "\r\n");
      list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);
      // Parse the headers list
      $raw_headers = explode("\r\n", $raw_headers);
      $headers = array();
      foreach ($raw_headers as $header) {
          list($name, $value) = explode(':', $header);
          $headers[strtolower($name)] = ltrim($value, ' '); 
      } 
      // Parse the Content-Disposition to get the field name, etc.
      if (isset($headers['content-disposition'])) {
          $filename = null;
          preg_match(
              '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/', 
              $headers['content-disposition'], 
              $matches
          );
          list(, $type, $name) = $matches;
          isset($matches[4]) and $filename = $matches[4]; 
          // handle your fields here
          switch ($name) {
              // this is a file upload
              case 'userfile':
                  file_put_contents($filename, $body);
                  break;
              // default for all other files is to populate $data
              default: 
                  $data[$name] = substr($body, 0, strlen($body) - 2);
                  break;
          } 
      }
  }
  $this->rupdata=$data;
}


/************************************************************************/
  private function losdatos(){
    $dat=json_decode($this->datap['datosm']);
 //   $dat=($this->datap['datosm']);

    $this->data = array(
    //    'imei'=>$this->datap['imei'],
        'timestamp'=>$dat->timestamp,
        'longitude'=>$dat->longitude,
        'latitude'=>$dat->latitude,
        'altitude'=>$dat->altitude,
        'angle'=>$dat->angle,
        'satelites'=>$dat->satellites,
        'hdop'=>$dat->hdop,
        'speed'=>$dat->speed,
        'eventid'=>$dat->event_id,
        'io2'=>$dat->io[2],
        'io3'=>$dat->io[3]

    );

/*
            var t=res.data.payload.records[0].timestamp
            console.log("imei "+res.data.imei);
            console.log("latitude "+res.data.payload.records[0].timestamp);
            console.log("longitude "+res.data.payload.records[0].longitude);
            console.log("latitude "+res.data.payload.records[0].latitude);
            console.log("altitude "+res.data.payload.records[0].altitude);
            console.log("angle "+res.data.payload.records[0].angle);
            console.log("satelites "+res.data.payload.records[0].satellites);
            console.log("hdop "+res.data.payload.records[0].hdop);
            console.log("speed "+res.data.payload.records[0].speed);
            console.log("eventid "+res.data.payload.records[0].event_id);
            console.log("io "+res.data.payload.records[0].io);
            console.log("io2 "+res.data.payload.records[0].io[2]);
            console.log("io3 "+res.data.payload.records[0].io[3]);
            console.log("io4 "+res.data.payload.records[0].io[4]);
            console.log("io5 "+res.data.payload.records[0].io[5]);
            console.log("io22 "+res.data.payload.records[0].io[22]);
            console.log("io23 "+res.data.payload.records[0].io[23]);
            var dt = new Date(t*1000);
            console.log("datetime "+dt);
*/
  }
/************************************************************************/
  public function enviarcarta_post(){
    date_default_timezone_set('America/Ciudad_Juarez'); 
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
