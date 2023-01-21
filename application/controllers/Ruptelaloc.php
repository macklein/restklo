<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;

 
class Ruptela extends REST_Controller {


  var $datap,$data,$id, $empid, $rupdata, $lat1, $lon1, $lat2, $lon2, $difmin;

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
    case "EnBase";
      $numedo = 1;
      break;
    case "Cargando";
      $numedo = 2;
      break;
    case "EnRuta";
      $numedo = 3;
      break;
    case "Diesel";
      $numedo = 4;
      break;
    case "EnCte";
      $numedo = 5;
      break;
    case "Descarga";
      $numedo = 6;
      break;
    case "Regresa";
      $numedo = 7;
      break;
    case "Fuera";
      $numedo = 8;
      break;
  }
  return $numedo;
}

 /************************************************************************/
private function SigEstado($edo){
  $numedo = 8;
  switch ($edo) {
    case "EnBase";
      $numedo = 1;
      break;
    case "Cargando";
      $numedo = 2;
      break;
    case "EnRuta";
      $numedo = 3;
      break;
    case "Diesel";
      $numedo = 4;
      break;
    case "EnCte";
      $numedo = 5;
      break;
    case "Descarga";
      $numedo = 6;
      break;
    case "Regresa";
      $numedo = 7;
      break;
    case "Fuera";
      $numedo = 8;
      break;
  }
  return $numedo;
}


private function addviajeAuto($vehid, $edo) {
//  $this->id = "No Entro Bien"; 
date_default_timezone_set('America/Chihuahua'); 
  $numedo = $this->NumEstado($edo);
  $xsql ="Select viajeid, vehid, tipo, estatus, fecha1, numedo, rupid1, rupid2 from gpsviajes where vehid = $vehid and estatus='Actual' and tipo='Auto' ";  
//  $xsql ="Select * from gpsviajes where vehid = $vehid ";  

//  $xsql ="Select * from emisor ";  
  
 // $this->id = $xsql;
  $query = $this->db->query($xsql);
//  $this->id = $this->db->error();
  //$this->id = $query->row();
  if ($query) {
    //$this->id = "Si Entro Bien"; 
    //$this->id = $query->row();
    $row = $query->row();
    $this->data = $row;    
    $viajeid = $row->viajeid;
    $fecha1 = $row->fecha1;
    $rup1 = $row->rupid1;
    $rup2 = $row->rupid2;
    if ($rup1>0){
      $xsql = "Select MIN(fecha) as fecha1, MAX(fecha) as fecha2, MAX(speed) as speed2 from ruptela where rupid>=$rup1 and rupid<=$rup2";
      $this->id = $xsql;
      $query = $this->db->query($xsql);
      if ($query) {
        $row = $query->row();
        $vehid = $vehid; 
        $fecha=date("Y-m-d H:i:s");
        $dif = $this->diffecha($fecha1);
        $xsql = "Update gpsviajes set estatus='Term', fecha2='$fecha', tiempo='$dif' Where viajeid=$viajeid";
        $query = $this->db->query($xsql);
        if ($query) {
          $xsql = "Select rupid from ruptela order by rupid desc limit 1";
          $query = $this->db->query($xsql);
          $rupini = 0;
          if ($query) {
            $row = $query->row();
            $rupini = $row->rupid;        
          }
          $xsql = "Insert Into gpsviajes set vehid=$vehid, fecha='$fecha', lat1=$this->lat1, lon1=$this->lon1, lat2=$this->lat2, lon2=$this->lon2, tipo='Auto', Estatus='Actual', fecha1='$fecha', numedo=$numedo, rupid1=$rupini, nvo='S' ";
          $query = $this->db->query($xsql);
          if ($query) {
            $xsql = "Update vehiculos set estatus='$edo' Where vehid=$vehid";
            $query = $this->db->query($xsql);
            $respuesta = array('error' => TRUE, "message" => "Listo");
            $this->response( $respuesta );
          }
        }
      }
    } 
  } 
}


private function addviajeAutoSub($vehid, $lat, $lon, $cliid, $sitid, $descrip) {
  date_default_timezone_set('America/Chihuahua'); 
  $xsql = "Select rupid from ruptela order by rupid desc limit 1";
  $query = $this->db->query($xsql);
  $rupini = 0;
  if ($query) {
    $row = $query->row();
    $rupini = $row->rupid;        
  }
  
  $fecha=date("Y-m-d H:i:s");
  $xsql ="Select viajeid, vehid, tipo, estatus, fecha2, rupid1, rupid2 from gpsviajes where vehid = $vehid and estatus='Term' and tipo='AutoSub' order by viajeid desc limit 1 ";  
    $this->id = $xsql ;
   
  $query = $this->db->query($xsql);
  if ($query) {
    $row = $query->row();
    $viajeid = $row->viajeid;
    $this->id = "Aqui Uno";
    $fecha2 = $row->fecha2; // Cuando inicio el viaje
    $dif = $this->diffecha($fecha2);
    $xsql = "Update gpsviajes set fecha3='$fecha', tiempositio='$dif', rupid3='$rupini' Where viajeid='$viajeid' ";
    
    $query = $this->db->query($xsql);
  }

  // Insertar Viaje Abierto por que no se conoce el destino agregar estado
  $xsql = "Insert Into gpsviajes set vehid=$vehid, fecha='$fecha', lat1=$lat, lon1=$lon, tipo='AutoSub', Estatus='Actual', fecha1='$fecha', sitid1=$sitid, descrip1='$descrip', estado='EnViaje', rupid1=$rupini, nvo='S' ";
  $this->id = $xsql;
  $query = $this->db->query($xsql);
  if ($query) {
    $xsql = "Update vehiculos set estatustemp='EnViaje' Where vehid=$vehid";
    $query = $this->db->query($xsql);
  } 
}

private function cierraviajeAutoSub($vehid, $lat, $lon, $cliid, $sitid, $descrip) {
  date_default_timezone_set('America/Chihuahua'); 
  $xsql ="Select viajeid, vehid, tipo, estatus, fecha1, rupid1, rupid2 from gpsviajes where vehid = $vehid and estatus='Actual' and tipo='AutoSub' ";  
  $this->id = "Uno " ;
  
  $query = $this->db->query($xsql);
  if ($query) {
    $this->id = $this->id. " Dos " ;
    $row = $query->row();
    $viajeid = $row->viajeid;
    $fecha1 = $row->fecha1; // Cuando inicio el viaje
    $rup1 = $row->rupid1;
    $xsql = "Select rupid from ruptela order by rupid desc limit 1";
    $query = $this->db->query($xsql);
    $rup2 = 0;
    $this->id = $this->id." Tres " ;

    if ($query) {
      $row = $query->row();
      $rup2 = $row->rupid;        
      $this->id = $this->id. " Cuatro " ;

    }
 
    if ($rup1>0 and $rup2>$rup1){
      $xsql = "Select MIN(fecha) as fecha1, MAX(fecha) as fecha2, MAX(speed) as speed2 from ruptela where rupid>=$rup1 and rupid<=$rup2";
      $this->id = $this->id. " Cinco " ;

      $query = $this->db->query($xsql);
      if ($query) {
        $this->id = $this->id. " Seis " ;

        $row = $query->row();
        $fecha=date("Y-m-d H:i:s");
        $dif = $this->diffecha($fecha1);
        // *R Capturar tambien el tiempo que se queda en el sitio con tiempoensitio y fecha3 
        $xsql = "Update gpsviajes set estatus='Term', fecha2='$fecha', tiempo='$dif' Where viajeid=$viajeid";
        $query = $this->db->query($xsql);
        if ($query) {
              $this->id = $this->id. " Siete " ;

        }
      }
    } 
  } 
}



private function diffecha($f1){
  date_default_timezone_set('America/Chihuahua'); 
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


public function alta_post(){
 // $this->getdatos();
  $this->rupdata = $this->post();
  date_default_timezone_set('America/Chihuahua'); 
  $accion=$this->rupdata["accion"]; // Temporalmente esta accion
  $imei=$this->rupdata["imei"];
  $imeic=$this->rupdata["imeic"];
  $dat=json_decode($this->rupdata["datosm"]);
  $latact = $dat->latitude/10000000;
  $lonact = $dat->longitude/10000000;
  $this->lat1 = $latact;
  $this->lon1 = $lonact; 
 // $ios=$dat->io;
  $dist=7647;
  $entro1="Nada ";
  $xsql ="Select vehid, empid, empreid, descrip, placas, marca, modelo, anio, simcard, serie, carid, estatus, latitude, longitude from vehiculos where imei=$imei";  
  $query = $this->db->query($xsql);

 // $this->id = $query;

  $estatus = "";
  $carid = 0;
  $latdest = "";
  $londest = "";
  if ($query) {
      $row = $query->row();
      $vehid = $row->vehid;
      $estatus = $row->estatus;
      $carid = $row->carid;
      $latdest = $row->latitude;
      $londest = $row->longitude;   
      $this->lat2 = $latdest;  
      $this->lon2 = $londest;     
  }



  if ($accion==1 or (($estatus=="EnBase") and intval($carid)>0 and intval($latact)>0 and intval($lonact)<0)){
    // Esta es la Base de Ferrocarril
    // Agregar Viaje Automatico en la Salida de la Base
    $this->lat2 = 31.727693; // Esta es la Base   
    $this->lon2 = -106.476500; 
    $dist = $this->getKilometros($latact, $lonact, $this->lat2, $this->lon2);
    if ($accion==1 or $dist>1.2){ // Agregar Viaje Cuando Sale
      if ($this->addviajeAuto($vehid, "EnRuta")){
        $entro1 = " 3 ";
      } 
    }
  }

  if ($accion==2 or (($estatus=="EnRuta") and intval($carid)>0 and intval($latact)>0 and intval($lonact)<0)){
    $dist = $this->getKilometros($latact, $lonact, $latdest, $londest);
    if ($accion==2 or $dist<.5){ // Agregar Viaje Cuando va llegando con el Cliente
      $this->id = "Agregar Viaje" ;
      if ($this->addviajeAuto($vehid, "EnCte")){
        $entro1 = " 4 ";
      }
    }
  }

  // Me Falta Analizar este Estatus
  if ($accion==3 or (($estatus=="EnCte") and intval($latdest)>0 and intval($londest)<0)){
    $dist = $this->getKilometros($latact, $lonact, $latdest, $londest);
    if ($accion==3 or $dist<.5){ // Agregar Viaje Cuando va llegando con el Cliente
      if ($this->addviajeAuto($vehid, "Regresa")){
        $entro1 = " 5 ";
      }
    }
  }

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
        'satelites'=>$dat->satellites,
        'hdop'=>$dat->hdop,
        'speed'=>$dat->speed,
        'vehid'=>$vehid,
        'nvo'=>'S',
        'eventid'=>$dat->event_id
    );
  
    $this->db->insert('ruptela',$data);
    $id=$this->db->insert_id();
    if ($id>0){
        $respuesta = array(
          'error' => FALSE,
          'id' => $data);
      }else{
          $respuesta = array(
            'error' => TRUE);
     }

/*
  $xsql ="Select * from ruptela ";  
  $query = $this->db->query($xsql);
  $this->id = $query;
*/
  /*
  $respuesta = array('error' => FALSE, 'vehid' => $data, 'accion' => $accion, 'datos' => $this->data, 'id' => $this->id);
  $this->response( $respuesta ); 
*/



  // * Validar si esta cerca de un sitio *************************************************

    $xsql = "Select destidtemp, fechatemp, estatustemp, viajeidtemp, lattemp, lontemp, espera from vehiculos where vehid=$vehid";
    $query = $this->db->query($xsql);
    if ($query){

      $row0 = $query->row();
      $destid = $row0->destidtemp;
      $fecha1 = $row0->fechatemp;
      $viajeid = $row0->viajeidtemp;
      $estatus = $row0->estatustemp;
      $descrip = "Temporal Descrip";
      $cliid = 7;
      $espera = $row0->espera;  // Si esta EnViaje y EnEspera verificar si ya pasaron los 5 min
      $lattemp = $row0->lattemp;
      $lontemp = $row0->lontemp;
      $this->id = "Espera: ".$espera." ".$xsql;
      if ($estatus=="EnViaje" or $accion==4 or $accion==5 or $accion==6 or $accion==7){
        $this->id = "Cero";
        if ($espera='Espera' or $accion==4 or $accion==5){
          // Si ya se alejo
          $dist = $this->getKilometros($this->lat1, $this->lon1, $lattemp, $lontemp);
          $this->id = "Dist ".$dist." Accion ".$accion;
          // $dist>1 asi debe ser
          if ($dist>100 or $accion==4){ // Agregar Viaje Cuando va llegando con el 
            $xsql = "Update vehiculos set espera='No' Where vehid='$vehid' ";
            $query = $this->db->query($xsql);
            if ($query){
             // break;
              $nada=0;
            }
          }else{ // or $accion==5
            // Si ya paso mas de 8 min en el mismo lugar
            $this->difmin = 0;
            $this->id = "Cero xx rull";
            $dif = $this->diffecha($fecha1);
            if ($this->difmin>8 or $accion==5){
              // Cerrar Viaje de Entrada
              if ($this->cierraviajeAutoSub($vehid, $lattemp, $lontemp, $cliid, $destid, $descrip)){
                $entro1 = " Simon ";
              } 
            }
          }
        }
       
        if ($espera='No' or $accion==6 or $accion==7){
          $xsql = "Select sitid, cliid, descrip, latitude, longitude from gpssitios where activo='S' ";
          $this->id = "Simon";
          $query = $this->db->query($xsql);
          foreach ( $query->result() as $row ){
            // $this->id = $this->id . $row;
            $sitid = $row->sitid;
            $cliid = $row->cliid;
            $descrip = $row->descrip;
            $latsit = $row->latitude;
            $lonsit = $row->longitude;
            $this->id = $this->id . " - " . $row->latitude;
            $dist = $this->getKilometros($this->lat1, $this->lon1, $latsit, $lonsit);
            if ($dist<.2 or $accion==7){ // Agregar Viaje Cuando va llegando con el 

              $xsql = "Update vehiculos set espera='Espera', fechatemp='$fecha', lattemp='$latsit', lontemp='$lonsit' Where vehid=$vehid ";
              $query = $this->db->query($xsql);
              if ($query){
                break;
              }
            }
          }
        } // el if
      }

      if ($estatus=="EnSitio" or $accion==8){
        $dist = $this->getKilometros($lattemp, $lontemp, $latact, $lonact);
        if ($dist>1.2 or $accion==8){
          // Crear Viaje de Salida
           $this->id = "Por Aqui llego";
          if ($this->addviajeAutoSub($vehid, $lattemp, $lontemp, $cliid, $destid, $descrip)){
            $this->id = "Por Aqui llego";
            $entro1 = " Simon ";
          } 
        }
      } 
    }


  //  $this->response( $respuesta ); 
    $respuesta = array('error' => FALSE, 'vehid' => $data, 'accion' => $accion, 'datos' => $this->data, 'id' => $this->id);
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
