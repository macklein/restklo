<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Almacentrans extends REST_Controller {


  var $datap,$data,$id,$empid;

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
    $bmodo = $data['bmodo'];  //No Usado
    $estatus = $data['estatus'];  
    $tipouser = $data['tipouser'];  
    $ctesids = $data['ctesids'];  
    $bcliid = $data['bcliid'];  
    $bfecha1 = $data['bfecha1']; 
    $bfecha2 = $data['bfecha2']; 




    $newf1 = date('Ymd', strtotime($bfecha1));
    $newf2 = date('Ymd', strtotime($bfecha2.'+1 day'));
 

    $d1 = strtotime($bfecha1);
    $f1 = date('d-m-Y',$d1);
    $date1 = explode('-', $f1);
    $y1 = intval($date1[2]);

  
    $d2 = strtotime($bfecha2);
    $f2 = date('d-m-Y',$d2);
    $date2 = explode('-', $f2);        
    $y2 = intval($date2[2]);

    $conduser='';
    $ctes = explode(",", $ctesids);
    $n=0;

    
    if ($tipouser=="Cli"){
      if (count($ctes)==1){
        $conduser.=" and almacentrans.cliid=$ctes[0] ";
      }else{
        foreach ($ctes as $cte) {
          $n=$n+1;
          if ($n==1){
            $conduser.=" and ( almacentrans.cliid=$cte ";
          }else{
            $conduser.=" or almacentrans.cliid=$cte ";
          }
        }
        $conduser.=" ) ";
      }
    }
     
    if ($estatus=="Pendientes"){
      $cond="and almacentrans.estatus='Pend' ";
    }else{
      $cond="and (almacentrans.estatus='Pend') ";
      $cond.="and almacentrans.fecha>=$newf1 ";
      $cond.="and almacentrans.fecha<$newf2 ";
    }  

    if ($bmodo=='S'){
       if ($bcliid>0){        
          if (!empty($buscar)){
            $cond .= " and almacentrans.cliid=$bcliid and (almacentrans.carnum LIKE '%".$buscar."%' or almacentrans.descrip1 LIKE '%".$buscar."%' or almacentrans.descrip2 LIKE '%".$buscar."%' or almacentrans.descrip3 LIKE '%".$buscar."%' or almacentrans.descrip4 LIKE '%".$buscar."%' or clientesmat.descripcion LIKE '%".$buscar."%' )"; 
          }else{
            $cond.="and almacentrans.cliid=$bcliid ";
          }
       }
    }
    if (!empty($buscar)){
      $cond .= " and (clientes.nombre LIKE '%".$buscar."%' or almacentrans.carnum LIKE '%".$buscar."%' or almacentrans.descrip1 LIKE '%".$buscar."%' or almacentrans.descrip2 LIKE '%".$buscar."%' or almacentrans.descrip3 LIKE '%".$buscar."%' or almacentrans.descrip4 LIKE '%".$buscar."%' or clientesmat.descripcion LIKE '%".$buscar."%' )"; 
    }



//$tem=$cond;
//$cond='';
//$conduser='';


  $tables="almacentrans, clientes, clientesmat";
  $campos="almacentrans.almid, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha , almacentrans.carnum, almacentrans.cantidad, (almacentrans.cantidad-almacentrans.capturadas) as quedan, almacentrans.peso, left(clientes.nombre,15) as nombre, almacentrans.cliid, almacentrans.descrip1, almacentrans.descrip2, almacentrans.descrip3, almacentrans.descrip4, clientesmat.descripcion as material ";
  $sWhere="almacentrans.cliid=clientes.cliid and almacentrans.matcliid=clientesmat.matcliid $cond $conduser";
  $sWhere.=" order by almacentrans.almid desc ";
    

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

    $query = $this->db->query($xsql);
    if ($query) {
      $respuesta = array(
        'error' => FALSE,            
        'paginas' => $paginas, 
        'numrows' => $numrows, 
        'xsql' => $xsql,
        'items' => $query->result_array()
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query, 'nrr' => $sWhere, 'y1'=>$y1, 'y2'=>$y2);
    }

/*
      $respuesta = array(
        'error' => FALSE,            
        'page' => $estatus,
      );
*/


    $this->response( $respuesta );
  }

/************************************************************************/
 public function seleid_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
  $xsql ="Select almid, cliid, carnum, tipo, fecha, comentario, empreid, userid as empid, equipidm, manid, asisid from almacentrans where almid=$id";  
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
  $this->datap = $this->post();
  $this->losdatos();
  date_default_timezone_set('America/Chihuahua'); 
  $fecha=date("Y-m-d H:i:s");
  $this->data += [ "fecha" => $fecha ];
  $this->data += [ "estatus" => "Pend" ];
  $this->data += [ "userid" => $this->empid ];  
  $this->db->insert('almacentrans',$this->data);
  $id=$this->db->insert_id();
  if ($id>0){
      $respuesta = array(
        'error' => FALSE,
        'id' => $this->data);
    }else{
        $respuesta = array(
          'error' => TRUE);
   }
  $this->response( $respuesta ); 
  }

/************************************************************************/
 public function modif_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
  $this->losdatos();
  $this->db->where( 'almid', $id );
  $hecho = $this->db->update( 'almacentrans', $this->data);
  if ($hecho){
      $respuesta = array(
        'error' => FALSE,
        'id' => $id);
    }else{
        $respuesta = array(
          'error' => TRUE);
   }
  $this->response( $respuesta );
  }
/************************************************************************/
  private function losdatos(){
    $dat=json_decode($this->datap['datosm']);
    $this->empid=$dat->empid;
    $this->data = array(
        'cliid'=>$dat->cliid,
        'empreid'=>$dat->empreid,
        'tipo'=>$dat->tipo,
        'carnum'=>$dat->carnum,
        'comentario'=>$dat->comentario,
        'equipidm'=>$dat->equipidm,
        'manid'=>$dat->manid,
        'asisid'=>$dat->asisid
    );
  }
/************************************************************************/

  public function enviarcarta_post(){
    $data = $this->post();
    $almid = $data['almid']; //
    $fecha=date("Y-m-d H:i:s");  
    $xsql = "UPDATE almacentrans SET estatus='Term', fechaenv='$fecha' WHERE almid=".$almid;
    $query = $this->db->query($xsql);
    $xrec=0;
    if ($query){
      $xrec=$almid;
    }
  
    $respuesta = array('error' => FALSE, 'almid' => $xrec);
    $this->response( $respuesta );
  
  }  

/************************************************************************/

}
