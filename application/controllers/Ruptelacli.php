<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Ruptelacli extends REST_Controller {


  var $datap,$data,$id,$empid,$rupdata;

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
 public function cargaruta_post(){
  $data = $this->post();
//  $vehid = $data['vehid'];
//  $imei = $data['imei'];
  $vehid = 2;
//  $xsql ="Select longitude, latitude from ruptela where imei=$imei order by rupid desc limit 2000";  
  $xsql ="Select longitude, latitude from ruptela where vehid=$vehid order by rupid desc limit 2000";  
  $query = $this->db->query($xsql);
    if ($query) {
      $respuesta = array(
        'error' => FALSE,            
        'items' => $query->result_array()
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }
//    $respuesta = array('error' => TRUE, "message" => $xsql);
    $this->response( $respuesta );
  }  

/************************************************************************/  
  public function cargasitios_post(){
  // $data = $this->post();
  $xsql ="Select sitid, cliid, descrip, descrip as nombre, latitude, longitude from gpssitios where latitude<>'' and longitude<>'' ";  
  $query = $this->db->query($xsql);
    if ($query) {
      $xsql ="Select a.dircliid, a.cliid, b.nombre, a.descripcion, a.latitude, a.longitude from clientesdir a, clientes b where a.cliid=b.cliid and a.latitude<>'' and a.longitude<>'' ";  
      $query2 = $this->db->query($xsql);
      if ($query2) {
        foreach ( $query->result() as $row ) {
          $records[] = array( 'tipo' => 'sit', 'sitid' => $row->sitid, 'nombre' => $row->nombre, 'cliid' => $row->cliid, 'descrip' => $row->descrip, 'latitude' => $row->latitude, 'longitude' => $row->longitude);
        }
        foreach ( $query2->result() as $row ) {
          $records[] = array( 'tipo' => 'dir', 'sitid' => $row->dircliid+100, 'nombre' => $row->nombre, 'cliid' => $row->cliid, 'descrip' => $row->descripcion, 'latitude' => $row->latitude, 'longitude' => $row->longitude);
        }
        $respuesta = array(
          'error' => FALSE,            
          'items' => $records
         );
      }
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }

/*  if ($query) {
    $numr=$query->num_rows();
    if ($numr>0){
      foreach ( $query->result() as $row ) {
        $records[] = array( 'label' => $row->descrip, 'value' => $row->sitid);
        }
      }else{
        $records[] = array( 'label' => "", 'value' => 0 );
     }
     $respuesta = array(
      'error' => FALSE,            
      'records' => $records,
      'numr' => $numr
    );
  } else {
    $respuesta = array('error' => TRUE, "message" => $query);
  } 
*/



//    $respuesta = array('error' => TRUE, "message" => $xsql);
    $this->response( $respuesta );
  }  
 public function cargaviaje_post(){
  $data = $this->post();
  $vehid = $data['vehid'];
  $imei = $data['imei'];
  $xsql ="Select viajeid, vehid, lat1, lon1, lat2, lon2, fecha, estatus, numedo from gpsviajes where vehid=$vehid and estatus='Actual' limit 1";  
  $query = $this->db->query($xsql);
    if ($query) {
      $respuesta = array(
        'error' => FALSE,            
        'items' => $query->result_array()
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }
//    $respuesta = array('error' => TRUE, "message" => $xsql);
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
public function alta_post(){
  $this->getdatos();
  date_default_timezone_set('America/Chihuahua'); 
  $imei=$this->rupdata["imei"];
  $dat=json_decode($this->rupdata["datosm"]);
  $ios=$dat->io;
  /*
  $data = array(
      'imei'=>$imei,
      'timestamp'=>$dat->timestamp,
      'longitude'=>$dat->longitude,
      'latitude'=>$dat->latitude,
      'altitude'=>$dat->altitude,
      'angle'=>$dat->angle,
      'satelites'=>$dat->satellites,
      'hdop'=>$dat->hdop,
      'speed'=>$dat->speed,
      'eventid'=>$dat->event_id
  );
*/

$data = array(
      'timestam'=>$dat->timestamp,
      'longitude'=>$dat->longitude,
      'latitude'=>$dat->latitude,
      'altitude'=>$dat->altitude,
      'angle'=>$dat->angle,
      'satelites'=>$dat->satellites,
      'hdop'=>$dat->hdop,
      'speed'=>$dat->speed,
      'nvo'=>'S',
      'eventid'=>$dat->event_id
  );


//  $this->losdatos();
  $fecha=date("Y-m-d H:i:s");
//var_dump($this->datap)
/*  
    $respuesta = array(
        'error' => FALSE,
        'id' => $imei);
  $this->response( $respuesta ); 
*/
  


//  $data += [ "fecha" => $fecha ];
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
  $this->response( $respuesta ); 


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
