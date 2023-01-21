<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Clientes extends REST_Controller {


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

    $tables = "clientes";
    $campos = "cliid, nombre, email, telefono1, direccion, estatus, telefono2, contacto1, empreid ";
    $sWhere = "nombre LIKE '%".$buscar."%'"; 
    $order = " Order by cliid ";

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
    $xsql = "SELECT $campos FROM  $tables where $sWhere $order LIMIT $offset,$perPage";
    $query = $this->db->query($xsql);
//mysqli_error($con);
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
/***********************************************************************/
 public function cargax_post(){
    $data = $this->post();
    $tipouser = $data['tipouser'];  
    $ctesids = $data['ctesids'];  
    $ctes = $data['ctes'];  
    $conduser='Where cliid>0 ';
    $ctes = explode(",", $ctesids);
    $n=0;
    if ($tipouser=="Tra"){
    //  $conduser.=" and cliid=0 ";

    }
    if ($tipouser=="Cli"){
      if (count($ctes)==1){
        $conduser.=" and cliid=$ctes[0] ";
      }else{
        foreach ($ctes as $cte) {
          $n=$n+1;
          if ($n==1){
            $conduser.=" and ( cliid=$cte ";
          }else{
            $conduser.=" or cliid=$cte ";
          }
        }
        $conduser.=" ) ";
      }
    }
   // $records='';
    $xsql = "Select cliid,nombre from clientes $conduser order by nombre ";
    $query = $this->db->query($xsql);
    if ($query) {
        foreach ( $query->result() as $row ) {
            $records[] = array( 'label' => $row->nombre, 'value' => $row->cliid );
        } 
      $respuesta = array(
        'error' => FALSE,            
        'records' => $records,
        'ctes' => $ctes        
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }  
  //    $respuesta = array('error' => TRUE, "message" => $xsql);
    
    $this->response( $respuesta );
 }
/***********************************************************************/
 /***********************************************************************/
 public function cargaparatrail_post(){
  $data = $this->post();
  $tipouser = $data['tipouser'];  
  $ctesids = $data['ctesids'];  
  $traid = $data['traid'];  
  $conduser='Where cliid>0 ';
  $ctes = explode(",", $ctesids);
  $n=0;
  if ($tipouser=="Tra"){
    //  $conduser.=" and cliid=0 ";
    $xsql1 = "Select ctes from transportistas where traid=$traid";
    $query = $this->db->query($xsql1);
    $row = $query->row();
    $ctesids=$row->ctes;
    $ctes = explode(",", $ctesids);
    foreach ($ctes as $cte) {
      $n=$n+1;
      if ($n==1){
        $conduser.=" and ( cliid=$cte ";
      }else{
        $conduser.=" or cliid=$cte ";
      }
    }
    $conduser.=" ) ";
  }
  if ($tipouser=="Cli"){
    if (count($ctes)==1){
      $conduser.=" and cliid=$ctes[0] ";
    }else{
      foreach ($ctes as $cte) {
        $n=$n+1;
        if ($n==1){
          $conduser.=" and ( cliid=$cte ";
        }else{
          $conduser.=" or cliid=$cte ";
        }
      }
      $conduser.=" ) ";
    }
  }
  $xsql = "Select cliid,nombre from clientes $conduser order by nombre ";
  $query = $this->db->query($xsql);
  if ($query) {
      foreach ( $query->result() as $row ) {
          $records[] = array( 'label' => $row->nombre, 'value' => $row->cliid );
      } 
    $respuesta = array(
      'error' => FALSE,            
      'records' => $records,
      'ctes' => $xsql
    );
  } else {
    $respuesta = array('error' => TRUE, "message" => $query);
  }  
//    $respuesta = array('error' => TRUE, "message" => $xsql);
  
  $this->response( $respuesta );
}
/***********************************************************************/
public function carga11_post(){
  $data = $this->post();
  $xsql = "Select cliid,nombre from clientes where cliid=11 order by nombre ";
  $query1 = $this->db->query($xsql);
  $xsql = "Select traid,nombre from transportistas where traid=13 order by nombre ";
  $query2 = $this->db->query($xsql);

  if (($query1) && ($query2)) {
      foreach ( $query1->result() as $row ) {
          $records[] = array( 'label' => $row->nombre, 'value' => $row->cliid );
      } 
      foreach ( $query2->result() as $row ) {
        $trans[] = array( 'label' => $row->nombre, 'value' => $row->traid );
      }
    $respuesta = array(
      'error' => FALSE,            
      'records' => $records,
      'trans' => $trans,      
      'ctes' => $xsql
    );
  } else {
    $respuesta = array('error' => TRUE, "message" => $query);
  }  
  $this->response( $respuesta );
}
/***********************************************************************/
public function carga_post(){
  $data = $this->post();
  $tipouser = $data['tipouser'];  
  $ctesids = $data['ctesids'];  
  $conduser='Where cliid>0 ';
  $ctes = explode(",", $ctesids);
  $n=0;
  if ($tipouser=="Cli"){
    if (count($ctes)==1){
      $conduser.=" and cliid=$ctes[0] ";
    }else{
      foreach ($ctes as $cte) {
        $n=$n+1;
        if ($n==1){
          $conduser.=" and ( cliid=$cte ";
        }else{
          $conduser.=" or cliid=$cte ";
        }
      }
      $conduser.=" ) ";
    }
  }
  $xsql = "Select cliid,nombre from clientes $conduser order by nombre ";
  $query = $this->db->query($xsql);
  if ($query) {
      foreach ( $query->result() as $row ) {
          $records[] = array( 'label' => $row->nombre, 'value' => $row->cliid );
      } 
    $respuesta = array(
      'error' => FALSE,            
      'records' => $records,
      'tipou' => $tipouser,
      'ctesid' => $ctesids,
      'cltes' => $xsql
    );
  } else {
    $respuesta = array('error' => TRUE, "message" => $query);
  }  
//    $respuesta = array('error' => TRUE, "message" => $xsql);
  
  $this->response( $respuesta );
}
/***********************************************************************/
public function cargacar_post(){
  $data = $this->post();
  $tipouser = $data['tipouser'];  
  $ctesids = $data['ctesids'];
  $tipocar = $data['tipocar'];
    
  $conduser='Where cliid>0 ';
  $ctes = explode(",", $ctesids);
  $n=0;
  if ($tipouser=="Cli"){
    if (count($ctes)==1){
      $conduser.=" and cliid=$ctes[0] ";
    }else{
      foreach ($ctes as $cte) {
        $n=$n+1;
        if ($n==1){
          $conduser.=" and ( cliid=$cte ";
        }else{
          $conduser.=" or cliid=$cte ";
        }
      }
      $conduser.=" ) ";
    }
  }
  if ($tipocar==1){
    $conduser .=" and clase<>'Alm' ";
  }
  if ($tipocar==2){
    $conduser .=" and clase='Alm' ";
  }
  if ($tipocar==3){
    $conduser .=" and cliid=1 OR cliid=2 OR cliid=11 OR cliid=27 ";
  }
  $xsql = "Select cliid,nombre from clientes $conduser order by nombre ";
  
  $query = $this->db->query($xsql);
  if ($query) {
      foreach ( $query->result() as $row ) {
          $records[] = array( 'label' => $row->nombre, 'value' => $row->cliid );
      } 
    $respuesta = array(
      'error' => FALSE,            
      'records' => $records,
      'tipou' => $tipouser,
      'ctesid' => $ctesids,
      'cltes' => $xsql
    );
  } else {
    $respuesta = array('error' => TRUE, "message" => $query);
  } 

//    $respuesta = array('error' => TRUE, "message" => $xsql);
  
  $this->response( $respuesta );
}

/***********************************************************************/
public function carganew_post(){
  $data = $this->post();
  $tipouser = $data['tipouser'];  
  $ctesids = $data['ctesids'];  
  
  $tiposal = $data['tiposal']; 
  $conduser='Where cliid>0 ';


  $ctes = explode(",", $ctesids);
  $n=0;
  if ($tipouser=="Cli"){
    if (count($ctes)==1){
      $conduser.=" and cliid=$ctes[0] ";
    }else{
      foreach ($ctes as $cte) {
        $n=$n+1;
        if ($n==1){
          $conduser.=" and ( cliid=$cte ";
        }else{
          $conduser.=" or cliid=$cte ";
        }
      }
      $conduser.=" ) ";
    }
  }


  if ($tiposal==2 or $tiposal==3 or $tiposal==4){
    $conduser="Where clase='Alm' ";
    if ($tiposal==4){
      $conduser="Where cliid=10 ";
    }
  }


  $xsql = "Select cliid,nombre from clientes $conduser order by nombre ";
  $query = $this->db->query($xsql);
  if ($query) {
      foreach ( $query->result() as $row ) {
          $records[] = array( 'label' => $row->nombre, 'value' => $row->cliid );
      } 
    $respuesta = array(
      'error' => FALSE,            
      'records' => $records,
      'tipou' => $tipouser,
      'ctesid' => $ctesids,
      'cltes' => $xsql
    );
  } else {
    $respuesta = array('error' => TRUE, "message" => $query);
  } 
  
  //  $respuesta = array('error' => TRUE, "message" => $xsql);
  
  $this->response( $respuesta );
}

/***********************************************************************/

public function cargaok_post(){
  $data = $this->post();
  $tipouser = $data['tipouser'];  
  $ctesids = $data['ctesids'];  
  $traid = $data['traid'];  
  $conduser='Where cliid>0 ';
  $ctes = explode(",", $ctesids);
  $n=0;
  if ($tipouser=="Tra"){
    //  $conduser.=" and cliid=0 ";
    $xsql1 = "Select ctes from transportistas where traid=$traid";
    $query = $this->db->query($xsql1);
    $row = $query->row();
    $ctesids=$row->ctes;
    $ctes = explode(",", $ctesids);
    foreach ($ctes as $cte) {
      $n=$n+1;
      if ($n==1){
        $conduser.=" and ( cliid=$cte ";
      }else{
        $conduser.=" or cliid=$cte ";
      }
    }
    $conduser.=" ) ";
  }
  if ($tipouser=="Cli"){
    if (count($ctes)==1){
      $conduser.=" and cliid=$ctes[0] ";
    }else{
      foreach ($ctes as $cte) {
        $n=$n+1;
        if ($n==1){
          $conduser.=" and ( cliid=$cte ";
        }else{
          $conduser.=" or cliid=$cte ";
        }
      }
      $conduser.=" ) ";
    }
  }
  $xsql = "Select cliid,nombre from clientes $conduser order by nombre ";
  $query = $this->db->query($xsql);
// $query=1;
  if ($query) {
      foreach ( $query->result() as $row ) {
          $records[] = array( 'label' => $row->nombre, 'value' => $row->cliid );
      } 
    $respuesta = array(
      'error' => FALSE,            
      'records' => $records,
      'ctes' => $xsql
    );
  } else {
    $respuesta = array('error' => TRUE, "message" => $query);
  }  
//    $respuesta = array('error' => TRUE, "message" => $xsql);
  
  $this->response( $respuesta );
}
/***********************************************************************/
public function cargafact_post(){
  $data = $this->post();
  $tipouser = $data['tipouser'];  
  $ctesids = $data['ctesids'];  
  $conduser='Where cliid>0 ';
  $ctes = explode(",", $ctesids);
  $n=0;
  $conduser=" Where fac='S' ";
  $xsql = "Select cliid,nombre from clientes $conduser order by nombre ";
  $query = $this->db->query($xsql);
  if ($query) {
      foreach ( $query->result() as $row ) {
          $records[] = array( 'label' => $row->nombre, 'value' => $row->cliid );
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
 
 public function cargaall_post(){
  $xsql = "Select cliid,nombre from clientes order by nombre ";
  $query = $this->db->query($xsql);
  if ($query) {
      foreach ( $query->result() as $row ) {
          $records[] = array( 'label' => $row->nombre, 'value' => $row->cliid );
      } 
    $respuesta = array(
      'error' => FALSE,            
      'records' => $records
    );
  } else {
    $respuesta = array('error' => TRUE, "message" => $query);
  }  
//    $respuesta = array('error' => TRUE, "message" => $xsql);
  
  $this->response( $respuesta );
}
/************************************************************************/
 public function alta_post(){
  $this->datap = $this->post();
  $this->losdatos();
  $this->data += [ "nvo" => "S" ];
  $this->db->insert('clientes',$this->data);
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
  $this->db->where( 'cliid', $id );
  $hecho = $this->db->update( 'clientes', $this->data);
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
  public function cargaempre_post(){
    $data = $this->post();
    $cliid = $data['cliid'];
    $xsql = "Select empreid from clientes where cliid=$cliid";
    $query1 = $this->db->query($xsql);
    $empreid=0;
/*   if ($cliid==15){
        $xsql = "Select sitid, cliid, descrip, calle from gpssitios where cliid=$cliid order by TipoOrd";
    }else{
        $xsql = "Select sitid, cliid, descrip, calle from gpssitios where cliid=15 or cliid=$cliid order by TipoOrd";
    }
    $xsql = "Select a.sitid, a.cliid, a.descrip, a.calle, b.rfc from gpssitios a, clientes b where (a.empreid=$empreid or a.cliid=$cliid) and (a.cliid=b.cliid) order by TipoOrd";
 */

//    $query2 = $this->db->query($xsql);
    if ($query1) {
      $row = $query1->row();
      $empreid = $row->empreid;

      $xsql = "Select a.sitid, a.cliid, a.descrip, a.calle, b.rfc from gpssitios a, clientes b where (a.empreid=$empreid or a.cliid=$cliid) and (a.cliid=b.cliid) order by TipoOrd";
      $query2 = $this->db->query($xsql);
      

      $respuesta = array(
        'error' => FALSE,            
        'empreid' => $empreid,    
        'sitios' => $query2->result_array(),      
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }
    $this->response( $respuesta );
  }

 /************************************************************************/
  private function losdatos(){
    $dat=json_decode($this->datap['datosm']);
    $this->data = array(
        'nombre'=>strtoupper($dat->nombre),
        'nombrefac'=>strtoupper($dat->nombre),
        'direccion'=>strtoupper($dat->direccion),
        'rfc'=>strtoupper($dat->rfc),
        'telefono1'=>$dat->telefono1,
        'telefono2'=>$dat->telefono2,
        'contacto1'=>strtoupper($dat->contacto1),
        'email'=>$dat->email,
        'empreid'=>$dat->empreid
    );
  }

}
