<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		$this->load->view('welcome_message');
	}


  public function reporte_post(){
    $this->datap = $this->post();
    $rep1=$this->datap['rep1'];

    $respuesta = array(
    'error' => FALSE,            
    'item' => $rep1,
    'result' => "Nueva Respuesta"
    );

   $this->response( $respuesta );


  }




}
