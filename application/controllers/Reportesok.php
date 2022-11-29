<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//require_once( APPPATH.'/libraries/REST_Controller.php' );
//use Restserver\libraries\REST_Controller;


//class Reportes extends REST_Controller 
class Reportes extends CI_Controller {


  var $datap,$data,$id,$empid;

/*
  public function __construct(){
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    header("Access-Control-Allow-Origin: *");
    parent::__construct();
    $this->load->database();

  }
*/


  

  public function index(){
    $this->load->view('sendmail');
}

  public function index_post(){
	

    $this->datap = $this->post();
    $rep1=$this->datap['rep1'];
    $respuesta = array(
      'error' => FALSE,            
      'item' => $rep1
    );

    $this->load->view('sendmail');
    $this->response( $respuesta );

   
	}

}
