<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
*/

class Prueba extends CI_Controller {
//class Prueba extends REST_Controller {

/*
 public function __construct(){
    header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    header("Access-Control-Allow-Origin: *");
    parent::__construct();
    $this->load->database();
  }
*/
  public function index(){
    $this->load->database();
    $query = $this->db->query("SELECT * FROM `productos` limit 3");
    echo json_encode( $query->result() );
  }

   public function obtener_arreglo_get(){
      $arreglo = array( "Manzana", "Pera", "Piña" );
      echo json_encode( $arreglo );
  }

    public function obtener_productos_get(){

    $this->load->database();

    $query = $this->db->query("SELECT * FROM `productos` limit 3");

    // $query->result()
 //   $this->response( $query->result() );

     echo json_encode( $query->result() );

  }

}
