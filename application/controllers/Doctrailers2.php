<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Doctrailers extends REST_Controller {


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
     $this->response('');
  }
  public function index_post(){
    $data = $this->post();
//    $respuesta = array('error' => TRUE, "message" => "22");
//    $this->response( $respuesta );

  
    $page = $data['page'];
    $buscar = $data['buscar'];
    $perPage = $data['perPage'];
    $bmodo = $data['bmodo'];  //No Usado
    $estatus = $data['estatus'];  
    $tipouser = $data['tipouser'];  
    $ctesids = $data['ctesids'];  
    $transids = $data['transids'];  

    $bcliid = $data['bcliid'];  
    $btraid = $data['btraid'];      
    $bfecha1 = $data['bfecha1']; 
    $bfecha2 = $data['bfecha2']; 


    $newf1 = date('Ymd', strtotime($bfecha1));
    $newf2 = date('Ymd', strtotime($bfecha2.'+ 1 day'));

 
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
    $trans = explode(",", $transids);
    $n=0;
    if ($tipouser=="Cli"){
      if (count($ctes)==1){
        $conduser.=" and doctrailers.cliid=$ctes[0] ";
      }else{
        foreach ($ctes as $cte) {
          $n=$n+1;
          if ($n==1){
            $conduser.=" and ( doctrailers.cliid=$cte ";
          }else{
            $conduser.=" or doctrailers.cliid=$cte ";
          }
        }
        $conduser.=" ) ";
      }
    }
    if ($tipouser=="Tra"){
      if (count($trans)==1){
        $conduser.=" and doctrailers.traid=$trans[0] ";
      }else{
        foreach ($trans as $tra) {
          $n=$n+1;
          if ($n==1){
            $conduser.=" and ( doctrailers.traid=$tra ";
          }else{
            $conduser.=" or doctrailers.traid=$tra ";
          }
        }
        $conduser.=" ) ";
      }
    }
    if ($estatus=="Pendientes"){
      $cond="and doctrailers.estatus='Pend' ";
    }else{
      $cond="and doctrailers.estatus<>'Pend' ";
      $cond.="and date(doctrailers.fecha)>=$newf1 ";
      $cond.="and date(doctrailers.fecha)<$newf2 ";
    }  

    if ($bmodo=='S'){
       if ($bcliid>0){
        $cond.="and doctrailers.cliid=$bcliid ";
       }
       if ($btraid>0){
        $cond.="and doctrailers.traid=$btraid ";
       }
    }else{
      if (!empty($buscar)){
        $cond .= "and (clientes.nombre LIKE '%".$buscar."%' or transportistas.nombre LIKE '%".$buscar."%' or doctrailers.pedimento LIKE '%".$buscar."%')"; 
      }
    }


  $tables="doctrailers, transportistas, clientes";
  $campos="doctrailers.doctraid, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha, doctrailers.enviado, transportistas.nombre as nombretrans, clientes.nombre, doctrailers.pedimento, doctrailers.caja, doctrailers.piezas, doctrailers.descargadas ";
  $sWhere=" doctrailers.traid=transportistas.traid and doctrailers.cliid=clientes.cliid $cond $conduser";
  $sWhere.=" order by doctrailers.doctraid desc ";
    
/*
      $respuesta = array(
        'error' => FALSE,            
        'nrr' => $sWhere,
        'cond' => $cond,      
        'cond2' => $conduser  
      );
  $this->response( $respuesta );
*/


    
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

 //     $respuesta = array('error' => TRUE, "message" => $xsql);


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
    $this->response( $respuesta );

  }

/************************************************************************/
 public function seleid_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
  $xsql ="Select doctraid, traid, userid as empid, choid, equipidt, equipidr, plaid, placa, cliid, empreid, matid, umedida, upeso, pedimento, peso, piezas, caja from doctrailers where doctraid=$id";  
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
 public function cargacli_post(){
  $this->datap = $this->post();
  $cliid=$this->datap['cliid'];
  $xsql = "select doctraid, date(fecha) as fecha, traid, pedimento, CAST(peso as UNSIGNED) as peso, piezas, piezas-descargadas as pendientes, piezas-descargadas as cantidad from doctrailers where piezas>descargadas and estatus='Pend' and cliid=".$cliid;
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
 public function alta_post(){
  $this->datap = $this->post();
  $dat=json_decode($this->datap['datosm']);
  $empid=$dat->empid;
  $this->losdatos();
  $fecha=date("Y-m-d H:i:s");
  $this->data += [ "fecha" => $fecha ];
  $this->data += [ "estatus" => "Pend" ];
  $this->data += [ "userid" => $empid ];  
  $this->data += [ "enviado" => "No" ];  
  $this->data += [ "nvo" => "S" ];
  $this->data += [ "afactura" => "N" ];    
  $this->db->insert('doctrailers',$this->data);
  $id=$this->db->insert_id();
  if ($id>0){
      $respuesta = array(
        'error' => FALSE,
        'id' => $id);
    }else{
        $respuesta = array(
          'error' => TRUE);
   }
   /*
   $respuesta = array(
          'error' => TRUE, 'datos' => $this->data);
  $respuesta = array('error' => TRUE, 'datos' => $this->data);
*/
  $this->response( $respuesta ); 
  }
/************************************************************************/
 public function modif_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
  $this->losdatos();
  $this->db->where( 'doctraid', $id );
  $hecho = $this->db->update( 'doctrailers', $this->data);
  if ($hecho){
      $respuesta = array(
        'error' => FALSE,
        'id' => $this->id);
    }else{
        $respuesta = array(
          'error' => TRUE);
   }
  $this->response( $respuesta );

  }
/************************************************************************/
  private function losdatos(){
    $dat=json_decode($this->datap['datosm']);
    $this->data = array(
        'traid'=>$dat->traid,
        'choid'=>$dat->choid,
        'equipidt'=>$dat->equipidt,
        'equipidr'=>$dat->equipidr,
        'plaid'=>$dat->plaid,
        'placa'=>$dat->placa,
        'cliid'=>$dat->cliid,
        'empreid'=>$dat->empreid,
        'matid'=>$dat->matid,
        'umedida'=>strtoupper($dat->umedida),
        'upeso'=>strtoupper($dat->upeso),
        'pedimento'=>strtoupper($dat->pedimento),
        'peso'=>$dat->peso,
        'piezas'=>$dat->piezas,
        'caja'=>strtoupper($dat->caja)
    );
  }

}
