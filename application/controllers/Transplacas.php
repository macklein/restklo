<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Transplacas extends REST_Controller {


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

    $tables = "transplacas a left join transportistas b on a.traid=b.traid ";
    $sWhere = "(a.placas LIKE '%".$buscar."%' or b.nombre LIKE '%".$buscar."%')"; 
    $order = " Order by traid ";

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

    $xsql = "Select a.plaid, a.traid, a.placas, b.nombre as nombretra from transplacas a left join transportistas b on a.traid=b.traid  where $sWhere $order LIMIT $offset,$perPage";

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
 public function cargaNO_post(){  
  $data = $this->post();
  $traid = $data['traid'];
  $equipid = $data['equipid'];
  $xfound="*";

   if ($traid==13){
      $xsql = "Select vehiculos.vehid, vehiculos.plaid, vehiculos.choid, vehiculos.equipidt, vehiculos.equipidr, transplacas.placas from vehiculos LEFT JOIN transplacas ON vehiculos.plaid=transplacas.plaid  where equipidt=$equipid";
  }else{
    $xsql = "Select vehiculos.vehid, vehiculos.plaid, vehiculos.placas, vehiculos.choid, vehiculos.equipidt, vehiculos.remid as equipidr from vehiculos where vehid=$equipid";
  }
  /*
  $query = $this->db->query($xsql);
  $row = $query->row();
  $plaid = $row->plaid; */
  $respuesta = array('error' => TRUE, "message" => $xsql);
  $this->response( $respuesta );
}
  /************************************************************************/
  public function carga_post(){  
    $data = $this->post();
    $traid = $data['traid'];
    $equipid = $data['equipid'];
    $xfound="*";

 //   if ($traid==13){
  if ($traid==13){
      $xsql = "Select vehiculos.vehid, vehiculos.plaid, vehiculos.choid, vehiculos.equipidt, vehiculos.equipidr, transplacas.placas from vehiculos LEFT JOIN transplacas ON vehiculos.plaid=transplacas.plaid  where equipidt=$equipid";
  }else{
    $xsql = "Select vehiculos.vehid, vehiculos.plaid, vehiculos.placas, vehiculos.choid, vehiculos.equipidt, vehiculos.remid as equipidr from vehiculos where vehid=$equipid";
  }
      $query = $this->db->query($xsql);
      $row = $query->row();
  //    $xsql = "Select plaid,placas from transplacas where plaid=$plaid";
  //    $query0 = $this->db->query($xsql);
  //    $row0 = $query0->row();
  $plaid = $row->plaid;
  if ($traid!=13){
 //   $plaid = 1;
  }
      $records[] = array( 'label' => $row->placas, 'value' => $row->plaid );
      $respuesta = array(
          'error' => FALSE,        
          'records' => $records,    
          'traid' => $traid,
          'equipid' => $equipid,
          'plaid' => $plaid,
          'choid' => $row->choid,
          'equipidr' => $row->equipidr,
        ); 
  /*  }else{       
      if ($equipid>0){
        $xsql = "Select plaid from gasequipos where equipid=$equipid";
        $query = $this->db->query($xsql);
        //$query->result() as $row;
        $row = $query->row();
        $plaid=$row->plaid;
        if ($plaid>0){
          $xsql = "Select plaid,placas from transplacas where plaid=$plaid";
          $xfound="1";
        }
      } 

      if ($xfound=="*"){
        $xsql = "Select plaid,placas from transplacas where traid=$traid";
      }
      
      $query = $this->db->query($xsql);
      if ($query) {
          $numr=$query->num_rows();
          if ($numr>0){
            foreach ( $query->result() as $row ) {
                $records[] = array( 'label' => $row->placas, 'value' => $row->plaid );
            } 
          }else{
              $records[] = array( 'label' => "", 'value' => 0 );
           }
        $respuesta = array(
          'error' => FALSE,            
          'records' => $records,
          'traid' => $traid,
          'equipid' => $equipid
        );
      } else {
        $respuesta = array('error' => TRUE, "message" => $query);
      }
    }  
    */
    
    $this->response( $respuesta );
 
  }
/************************************************************************/






/************************************************************************/
public function cargaot_post(){  
  $data = $this->post();
  $traid = $data['traid'];
  $equipid = $data['equipid'];
  $xfound="*";

   if ($traid==13){
      $xsql = "Select vehiculos.vehid, vehiculos.plaid, vehiculos.choid, vehiculos.equipidt, vehiculos.equipidr, transplacas.placas from vehiculos LEFT JOIN transplacas ON vehiculos.plaid=transplacas.plaid  where equipidt=$equipid";
  }else{
      $xsql = "Select vehiculos.vehid, vehiculos.plaid, vehiculos.placas, vehiculos.choid, vehiculos.equipidt, vehiculos.remid as equipidr from vehiculos where traid=$traid";
  }
  $query = $this->db->query($xsql);
  $row = $query->row();
//  $plaid = $row->plaid; 
  $respuesta = array('error' => TRUE, "message" => $xsql);
  $this->response( $respuesta );
}

  /************************************************************************/
 public function cargaant_post(){  
    $data = $this->post();
    $traid = $data['traid'];
    $equipid = $data['equipid'];
    $xfound="*";

    /*
    $respuesta = array('error' => TRUE, "message" => "hola");
    $this->response( $respuesta );
*/

 //   if ($traid==13){
  if ($traid==13){
      $xsql = "Select vehiculos.vehid, vehiculos.plaid, vehiculos.choid, vehiculos.equipidt, vehiculos.equipidr, transplacas.placas from vehiculos LEFT JOIN transplacas ON vehiculos.plaid=transplacas.plaid  where equipidt=$equipid";
  }else{
    $xsql = "Select vehiculos.vehid, vehiculos.plaid, vehiculos.placas, vehiculos.choid, vehiculos.equipidt, vehiculos.remid as equipidr from vehiculos where vehid=$equipid";
  }
      $query = $this->db->query($xsql);
      $row = $query->row();
  //    $xsql = "Select plaid,placas from transplacas where plaid=$plaid";
  //    $query0 = $this->db->query($xsql);
  //    $row0 = $query0->row();
  $plaid = $row->plaid;
  if ($traid!=13){
 //   $plaid = 1;
  }
      $records[] = array( 'label' => $row->placas, 'value' => $row->plaid );
      $respuesta = array(
          'error' => FALSE,        
          'records' => $records,    
          'traid' => $traid,
          'equipid' => $equipid,
          'plaid' => $plaid,
          'choid' => $row->choid,
          'equipidr' => $row->equipidr,
        ); 
  /*  }else{       
      if ($equipid>0){
        $xsql = "Select plaid from gasequipos where equipid=$equipid";
        $query = $this->db->query($xsql);
        //$query->result() as $row;
        $row = $query->row();
        $plaid=$row->plaid;
        if ($plaid>0){
          $xsql = "Select plaid,placas from transplacas where plaid=$plaid";
          $xfound="1";
        }
      } 

      if ($xfound=="*"){
        $xsql = "Select plaid,placas from transplacas where traid=$traid";
      }
      
      $query = $this->db->query($xsql);
      if ($query) {
          $numr=$query->num_rows();
          if ($numr>0){
            foreach ( $query->result() as $row ) {
                $records[] = array( 'label' => $row->placas, 'value' => $row->plaid );
            } 
          }else{
              $records[] = array( 'label' => "", 'value' => 0 );
           }
        $respuesta = array(
          'error' => FALSE,            
          'records' => $records,
          'traid' => $traid,
          'equipid' => $equipid
        );
      } else {
        $respuesta = array('error' => TRUE, "message" => $query);
      }
    }  
    */
    
    $this->response( $respuesta );
 
  }
/************************************************************************/
/************************************************************************/
  public function carga2_post(){  
    $data = $this->post();
    $traid = $data['traid'];
    $equipid = $data['equipid'];
    $xfound="*";
    $xsql = "Select vehiculos.vehid, vehiculos.placas, vehiculos.choid, vehiculos.remid from vehiculos where vehid=$equipid";
    $query = $this->db->query($xsql);
    $row = $query->row();
    $records[] = array( 'label' => $row->placas, 'value' => 1 );
    $respuesta = array(
          'error' => FALSE,        
          'records' => $records,    
          'traid' => $traid,
          'equipid' => $equipid,
          'plaid' => 1,
          'choid' => $row->choid,
          'equipidr' => $row->remid,
        ); 
    $this->response( $respuesta );
  }

  public function cargaent_post(){  
    $data = $this->post();
    $traid = $data['traid'];
    $remid = 0;
    $plaid = 0;
    $choid = 0;
    $equipid = $data['equipid'];
    $xfound="*";
    if ($traid==13){
        $xsql = "Select vehiculos.vehid, vehiculos.placas, vehiculos.plaid, vehiculos.choid, vehiculos.equipidr from vehiculos where vehid=$equipid";
        $query = $this->db->query($xsql);
        $row = $query->row();
        $plaid = $row->plaid;
        $choid = $row->choid;
        $remid = $row->equipidr;
      //  $records[] = array( 'label' => $row->placas, 'value' => 1 );
    }

    $xsql = "Select plaid,placas from transplacas  where traid=$traid order by plaid";
    $query = $this->db->query($xsql);
    if ($query) {
        foreach ( $query->result() as $row ) {
            $records[] = array( 'label' => $row->placas, 'value' => $row->plaid );
        } 
    }



    $respuesta = array(
          'error' => FALSE,        
          'records' => $records,    
          'traid' => $traid,
          'equipid' => $equipid,
          'plaid' => $plaid,
          'choid' => $choid,
          'equipidr' => $remid,
        ); 
    $this->response( $respuesta );
  }

  public function cargafact_post(){  
    $data = $this->post();
    $traid = $data['traid'];
    $equipid = $data['equipid'];
    $xfound="*";
  //  $xsql = "Select vehiculos.vehid, vehiculos.placas, vehiculos.choid, vehiculos.remid from vehiculos where vehid=$equipid";
    if ($traid==13){
    //  $xsql = "Select vehiculos.vehid, vehiculos.plaid, vehiculos.choid, vehiculos.equipidt, vehiculos.equipidr, transplacas.placas from vehiculos LEFT JOIN transplacas ON vehiculos.plaid=transplacas.plaid  where equipidt=$equipid";
      $xsql = "Select vehiculos.vehid, vehiculos.placas, vehiculos.choid, vehiculos.remid, vehiculos.equipidr from vehiculos where equipidt=$equipid";

    }else{
  //    $xsql = "Select vehiculos.vehid, vehiculos.plaid, vehiculos.placas, vehiculos.choid, vehiculos.equipidt, vehiculos.remid as equipidr from vehiculos where traid=$traid";
      $xsql = "Select vehiculos.vehid, vehiculos.placas, vehiculos.choid, vehiculos.remid from vehiculos where vehid=$equipid";

    }
    $query = $this->db->query($xsql);
    $row = $query->row();
    $records[] = array( 'label' => $row->placas, 'value' => 1 );
    $respuesta = array(
          'error' => FALSE,        
          'records' => $records,    
          'traid' => $traid,
          'equipid' => $equipid,
          'plaid' => 1,
          'choid' => $row->choid,
          'equipidr' => $row->remid,
        ); 
    $this->response( $respuesta );
  }
/************************************************************************/
public function cargafactn_post(){  
  $data = $this->post();
  $traid = 13;
  $equipid = $data['equipid'];
  $xfound="*";
  $xsql = "Select vehiculos.vehid, vehiculos.placas, vehiculos.choid, vehiculos.remid from vehiculos where vehid=$equipid";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $records[] = array( 'label' => $row->placas, 'value' => 1 );
  $respuesta = array(
        'error' => FALSE,        
        'records' => $records,    
        'traid' => $traid,
        'equipid' => $equipid,
        'plaid' => 1,
        'choid' => $row->choid,
        'equipidr' => $row->remid,
      ); 
  $this->response( $respuesta );
}
/************************************************************************/

 public function carga13_post(){
    $xsql = "Select plaid,placas from transplacas  where traid=13 order by plaid";
    $query = $this->db->query($xsql);
    if ($query) {
        foreach ( $query->result() as $row ) {
            $records[] = array( 'label' => $row->placas, 'value' => $row->plaid );
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
 public function alta_post(){
  $this->datap = $this->post();
  $this->losdatos();
  $this->data += [ "nvo" => "S" ];
  $this->db->insert('transplacas',$this->data);
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
  $this->db->where( 'plaid', $id );
  $hecho = $this->db->update( 'transplacas', $this->data);
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
        'traid'=>strtoupper($dat->traid),
        'placas'=>strtoupper($dat->placas)
    );
  }

}
