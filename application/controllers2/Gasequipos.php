<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Gasequipos extends REST_Controller {


  var $datap,$data,$id;

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
    $criterio = $data['criterio'];  //No Usado
    $tables = "gasequipos ";
    $campos = "equipid, tipoid, descripequip, refer, fechacompra, fechaplacas, fechaseguro, numserie, numint, plaid, placas, bodega, tipo, empreid  ";
    $sWhere = "descripequip LIKE '%".$buscar."%'"; 
    $order = " Order by equipid ";


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

//    $xsql = "Select a.*,b.DescripEquip from gasequipos a left join gasclasif b on a.ClasId=b.ClasId where $sWhere $order LIMIT $offset,$perPage";

$xsql = "Select $campos from gasequipos where $sWhere $order LIMIT $offset,$perPage";


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
   
    $data = $this->post();
//    $xsql = $data;

    $tipo = $data['tipo'];
    $modogps = $data['modogps'];
    $cond="equipid>0 ";
    if ($tipo=="r"){
      $cond.=" and tipo='Remolque' ";
    }
    if ($tipo=="m"){
      $cond.=" and tipo='Maniobra' ";
    }    





    $xsql = "Select equipid,descripequip from gasequipos Where $cond order by descripequip";
    if ($tipo=="t"){
      // Rul 
      if ($modogps=="Si"){
        $xsql = "Select a.equipid,a.descripequip,b.vehid,b.carid from gasequipos a, vehiculos b Where a.tipo='Transporte' and a.vehid=b.vehid and b.carid=0 and b.estatus='EnBase' order by a.descripequip";
      }else{
//        $xsql = "Select a.equipid,a.descripequip,b.vehid,b.carid from gasequipos a, vehiculos b Where a.tipo='Transporte' and a.vehid=b.vehid order by a.descripequip";
        $xsql = "Select a.equipid,a.descripequip,b.vehid,b.carid from gasequipos a LEFT JOIN vehiculos b ON a.vehid=b.vehid where a.tipo='Transporte' order by a.descripequip";
      }
    }
    if ($tipo=="m"){
      // Rul 
      if ($modogps=="Si"){
        $xsql = "Select a.equipid,a.descripequip,b.vehid,b.carid from gasequipos a, vehiculos b Where a.tipo='Maniobra' and a.vehid=b.vehid and b.carid=0 and b.estatus='EnBase' order by a.descripequip";
      }else{
//        $xsql = "Select a.equipid,a.descripequip,b.vehid,b.carid from gasequipos a, vehiculos b Where a.tipo='Maniobra' and a.vehid=b.vehid order by a.descripequip";
        $xsql = "Select a.equipid,a.descripequip,b.vehid,b.carid from gasequipos a LEFT JOIN vehiculos b ON a.vehid=b.vehid where a.tipo='Maniobra' order by a.descripequip";
      }
    }

/*
    $respuesta = array('error' => TRUE, "message" => $xsql);
    $this->response( $respuesta );
*/

    $query = $this->db->query($xsql);
    if ($query) {
        $numr=$query->num_rows();
        if ($numr>0){
          foreach ( $query->result() as $row ) {
              $records[] = array( 'label' => $row->descripequip, 'value' => $row->equipid );
          } 
        }else{
            $records[] = array( 'label' => "", 'value' => 0 );
         }
      $respuesta = array(
        'error' => FALSE,            
        'records' => $records
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query,  "xsql" => $xsql);
    }
    
    // $respuesta = array('error' => TRUE, "message" => $xsql);
    $this->response( $respuesta );
  
  }
/************************************************************************/
public function cargafact_post(){
  $data = $this->post();
  $tipo = $data['tipo'];
  $traid = $data['traid'];
  $modogps = $data['modogps'];
  if ($tipo=="t"){
    $xsql = "Select vehid as id,descrip from vehiculos where traid=$traid order by descrip";
  }
  if ($tipo=="m"){
    $xsql = "Select vehid as id,descrip from vehiculos where traid=13 order by descrip";
  }
  if ($tipo=="r"){
    $xsql = "Select remid as id,descrip from transremol where traid=$traid order by descrip";
  }    
  $query = $this->db->query($xsql);
  if ($query) {
      $numr=$query->num_rows();
      if ($numr>0){
        foreach ( $query->result() as $row ) {
            $records[] = array( 'label' => $row->descrip, 'value' => $row->id );
        } 
      }else{
          $records[] = array( 'label' => "", 'value' => 0 );
       }
    $respuesta = array(
      'error' => FALSE,            
      'records' => $records
    );
  } else {
    $respuesta = array('error' => TRUE, "message" => $query);
  }
  $this->response( $respuesta );
}  


/************************************************************************/
public function carganew_post(){
   
  $data = $this->post();
  $tipo = $data['tipo'];
  $tiposal = $data['tiposal'];

  $modogps = $data['modogps'];
  $cond="equipid>0 ";
  if ($tipo=="r"){
    $cond.=" and tipo='Remolque' ";
  }
  if ($tipo=="m"){
    $cond.=" and tipo='Maniobra' ";
  }    
  $xsql = "Select equipid,descripequip from gasequipos Where $cond order by descripequip";
  if ($tipo=="t"){
    // Rul 
    if ($modogps=="Si"){
      $xsql = "Select a.equipid,a.descripequip,b.vehid,b.carid from gasequipos a, vehiculos b Where a.tipo='Transporte' and a.vehid=b.vehid and b.carid=0 and b.estatus='EnBase' order by a.descripequip";
    }else{
      $xsql = "Select a.equipid,a.descripequip,b.vehid,b.carid from gasequipos a LEFT JOIN vehiculos b ON a.vehid=b.vehid where a.tipo='Transporte' order by a.descripequip";
    }
  }
  $query = $this->db->query($xsql);
  if ($query) {
      $numr=$query->num_rows();
      if ($numr>0){
        foreach ( $query->result() as $row ) {
            $records[] = array( 'label' => $row->descripequip, 'value' => $row->equipid );
        } 
      }else{
          $records[] = array( 'label' => "", 'value' => 0 );
       }
    $respuesta = array(
      'error' => FALSE,            
      'records' => $records
    );
  } else {
    $respuesta = array('error' => TRUE, "message" => $query);
  }
  $this->response( $respuesta );
}
/************************************************************************/




/***********************************************************************/
 public function alta_post(){
  $this->datap = $this->post();
  date_default_timezone_set('America/Chihuahua'); 
  $this->losdatos();
  $this->data += [ "nvo" => "S" ];
  $this->db->insert('gasequipos',$this->data);
 // $error = $this->db->error();
/*
       $respuesta = array(
        'error' => FALSE,
        'id' => $this->data);
  $this->response( $respuesta );
*/

  $id=$this->db->insert_id();
  if ($id>0){
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
 
 public function modif_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
  $this->losdatos();
  $this->db->where( 'equipid', $id );
  $hecho = $this->db->update( 'gasequipos', $this->data);
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
    $fcom = (is_null($dat->fechacompra) ? "":$dat->fechacompra);
    $fpla = (is_null($dat->fechaplacas) ? "":$dat->fechaplacas);
    $fseg = (is_null($dat->fechaseguro) ? "":$dat->fechaseguro);
    $this->data = array(
        'descripequip'=>strtoupper($dat->descripequip),
        'refer'=>strtoupper($dat->refer),
        'empreid'=>$dat->empreid,
        'tipoid'=>$dat->tipoid,
        'fechacompra'=>$fcom,
        'fechaplacas'=>$fpla,
        'fechaseguro'=>$fseg,
        'numserie'=>strtoupper($dat->numserie),
        'numint'=>strtoupper($dat->numint),
        'placas'=>strtoupper($dat->placas),
        'bodega'=>strtoupper($dat->bodega),
        'tipo'=>$dat->tipo,
        'plaid'=>$dat->plaid
    );
  }

}
