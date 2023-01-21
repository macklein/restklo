<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Clientes extends REST_Controller {

  public function __construct(){
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    header("Access-Control-Allow-Origin: *");
    parent::__construct();
    $this->load->database();

  }

  public function index0_get(){

      $query = $this->db->query("SELECT * FROM clientes");

      $respuesta = array(
              'error' => FALSE,
              'clientes' => $query->result_array()
            );

      $this->response( $respuesta );

  }

 public function index_post(){


    $data = $this->post();
    $page = $data['page'];
    $buscar = $data['buscar'];
    $criterio = $data['criterio'];  //No Usado

    $tables = "clientes";
    $campos = "cliId,nombre,email,telefono1,direccion,estatus ";
    $sWhere = "nombre LIKE '%".$buscar."%'"; 

/*    $xcond=" estado='".$estado."' and ((Nombre LIKE '%".$busca."%') or (Numero LIKE '%".$busca."%') or (NumOdc LIKE '%".$busca."%')) ";
    if ($busca==""){
      $xcond=" estado='".$estado."' ";
    }
*/

    $order = " Order by CliId ";
    $per_page=20;
    $offset = ($page - 1) * $per_page;
    $xsql="SELECT count(*) AS numrows FROM $tables WHERE $sWhere";
    $query = $this->db->query($xsql);
    $row = $query->row();
    $numrows = $row->numrows;
    if ($numrows>0){
       $paginas = ceil($numrows/$per_page);
    }else{
      $paginas = 1;
    }
    $xsql = "SELECT $campos FROM  $tables where $sWhere LIMIT $offset,$per_page";
    $query = $this->db->query($xsql);
    $respuesta = array(
            'error' => FALSE,            
            'paginas' => $paginas, 
            'numrows' => $numrows,                       
            'items' => $query->result_array()
          );
    $this->response( $respuesta );

/**********************************************************************************/



/*
  $data = $app->request->post();
  $estado = $data['estado'];
  $busca = $data['busca'];
  $pagina = $data['pagina'];
  $limit = $data['limit'];

  $xcond=" estado='".$estado."' and ((Nombre LIKE '%".$busca."%') or (Numero LIKE '%".$busca."%') or (NumOdc LIKE '%".$busca."%')) ";
  if ($busca==""){
    $xcond=" estado='".$estado."' ";
  }

  $xsql = "SELECT COUNT(CotId) as TotRec From cotizacion WHERE ".$xcond;
  $query = $db->query($xsql);
  $row = $query->fetch_assoc();
  $paginas = $row["TotRec"]/$limit;  
  if ($paginas>intval($paginas)){
    $paginas=intval($paginas)+1;  
  }
  $inicio = ($pagina-1)*$limit;
  $xsql = "SELECT * FROM cotizacion WHERE ".$xcond." limit ".$inicio.",".$limit;
  $query = $db->query($xsql);
  $error = mysqli_error($db);
  $datos = array();
  while ($row = $query->fetch_assoc()) {
    $datos[] = $row;
  }
  $respuesta = array("status" => "success","paginas" =>$paginas,"data" => $datos, "sql"=>$xsql);
  echo json_encode($respuesta);
*/



/*    $respuesta = array(
                  'error' => FALSE,
                  'token' => $data,
                  'id_usuario' => $data['buscar']
                );
    $this->response( $respuesta );
*/

/*
    $respuesta = array(
                  'total' => $numrows,
                  'current_page' => $page,
                  'per_page' => $per_page,
                  'per_page' => $per_page,
                  'per_page' => $per_page,
                  
                );
    $this->response( $respuesta );

       return [
            'pagination' => [
                'total'        => $clientes->total(),
                'current_page' => $clientes->currentPage(),
                'per_page'     => $clientes->perPage(),
                'last_page'    => $clientes->lastPage(),
                'from'         => $clientes->firstItem(),
                'to'           => $clientes->lastItem(),
            ],
            'clientes' => $clientes
        ];
        */

  }



}
