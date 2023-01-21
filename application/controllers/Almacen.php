<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Almacen extends REST_Controller {


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
        $conduser.=" and almacen.cliid=$ctes[0] ";
      }else{
        foreach ($ctes as $cte) {
          $n=$n+1;
          if ($n==1){
            $conduser.=" and ( almacen.cliid=$cte ";
          }else{
            $conduser.=" or almacen.cliid=$cte ";
          }
        }
        $conduser.=" ) ";
      }
    }
     
    if ($estatus=="Pendientes"){
      $cond="and almacen.estatus='Pend' ";
    }else{
      $cond="and (almacen.estatus='Pend') ";
      $cond.="and almacen.fecha>=$newf1 ";
      $cond.="and almacen.fecha<$newf2 ";
    }  

    if ($bmodo=='S'){
       if ($bcliid>0){        
          if (!empty($buscar)){
            $cond .= " and almacen.cliid=$bcliid and (almacen.carnum LIKE '%".$buscar."%' or almacen.descrip1 LIKE '%".$buscar."%' or almacen.descrip2 LIKE '%".$buscar."%' or almacen.descrip3 LIKE '%".$buscar."%' or almacen.descrip4 LIKE '%".$buscar."%' or clientesmat.descripcion LIKE '%".$buscar."%' )"; 
          }else{
            $cond.="and almacen.cliid=$bcliid ";
          }
       }
    }
    if (!empty($buscar)){
      $cond .= " and (clientes.nombre LIKE '%".$buscar."%' or almacen.carnum LIKE '%".$buscar."%' or almacen.descrip1 LIKE '%".$buscar."%' or almacen.descrip2 LIKE '%".$buscar."%' or almacen.descrip3 LIKE '%".$buscar."%' or almacen.descrip4 LIKE '%".$buscar."%' or clientesmat.descripcion LIKE '%".$buscar."%' )"; 
    }



//$tem=$cond;
//$cond='';
//$conduser='';


  $tables="almacen, clientes, clientesmat";
  $campos="almacen.almid, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha , almacen.carnum, almacen.cantidad, (almacen.cantidad-almacen.capturadas) as quedan, almacen.peso, left(clientes.nombre,15) as nombre, almacen.cliid, almacen.descrip1, almacen.descrip2, almacen.descrip3, almacen.descrip4, clientesmat.descripcion as material ";
  $sWhere="almacen.cliid=clientes.cliid and almacen.matcliid=clientesmat.matcliid $cond $conduser";
  $sWhere.=" order by almacen.almid desc ";
    

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
/*
//***********************************************************
    $xsql1="SELECT sum(cantidad-ca[turadas) AS numcant FROM $tables WHERE $sWhere";
    $query = $this->db->query($xsql1);
    $row = $query->row();
    $totcants = $row->numcant;
//***********************************************************
    $xsql1="SELECT sum(cantidad-ca[turadas) AS numcant FROM $tables WHERE $sWhere";
    $query = $this->db->query($xsql1);
    $row = $query->row();
    $totcants = $row->numcant;
//***********************************************************
    $xsql1="SELECT sum(cantidad-ca[turadas) AS numcant FROM $tables WHERE $sWhere";
    $query = $this->db->query($xsql1);
    $row = $query->row();
    $totcants = $row->numcant;        
//***********************************************************
*/

    $totcant=0;
    $totpeso=0;

    $xsql1 = "Select sum(cantidad-capturadas) AS totcant, ";
    $xsql1 = $xsql1."sum(((cantidad-capturadas)*peso)/cantidad) as totpeso FROM $tables WHERE $sWhere";

    $query0 = $this->db->query($xsql1);

    if ($query0){
      $row = $query0->row();
      $totcant = $row->totcant;  
      $totpeso = $row->totpeso;  
    }

/*
    $totcant=0;
    $totpeso=0;
    if ($pcliid>0){
      if ($pmatid>0){
        $xsql = "Select sum(cantidad-capturadas) AS totcant, ";
        $xsql = $xsql."sum(((cantidad-capturadas)*peso)/cantidad) as totpeso FROM almacen where estatus='Pend' and cliid=".$pcliid." and matcliid=".$pmatid;

      }else{
        $xsql = "Select sum(cantidad-capturadas) AS totcant, ";
        $xsql = $xsql."sum(((cantidad-capturadas)*peso)/cantidad) as totpeso FROM almacen where estatus='Pend' and cliid=".$pcliid;
      }
      $query0 = mysqli_query($con,$xsql);
     // echo $xsql;
     // echo mysqli_error($con);
      if ($query0){
        $row = mysqli_fetch_array($query0);
        $totcant = $row['totcant'];
        $totpeso = $row['totpeso'];
      }
    }
*/



    $xsql = "SELECT $campos FROM  $tables where $sWhere LIMIT $offset,$perPage";

    $query = $this->db->query($xsql);
    if ($query) {
      $respuesta = array(
        'error' => FALSE,            
        'paginas' => $paginas, 
        'numrows' => $numrows, 
        'totcant' => $totcant, 
        'totpeso' => $totpeso, 
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
  $xsql ="Select almid, cliid, carnum, tipo, fecha, comentario, empreid, userid as empid, equipidm, manid, asisid from almacen where almid=$id";  
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
  $this->db->insert('almacen',$this->data);
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
  $hecho = $this->db->update( 'almacen', $this->data);
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
    $xsql = "UPDATE almacen SET estatus='Term', fechaenv='$fecha' WHERE almid=".$almid;
    $query = $this->db->query($xsql);
    $xrec=0;
    if ($query){
      $xrec=$almid;
    }
  
    $respuesta = array('error' => FALSE, 'almid' => $xrec);
    $this->response( $respuesta );
  
  }  

/************************************************************************/
public function prueba(){
    $data = $this->post();
  $xrec=18;
    $respuesta = array('error' => FALSE, 'almid' => $xrec);
    $this->response( $respuesta );
  
  }  

}
