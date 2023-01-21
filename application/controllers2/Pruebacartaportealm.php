<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Pruebacartaportealm extends REST_Controller {


  var $datap,$data,$id;

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

/*
  if ($pcliid>0){
    $query .=" and cartaporte.cliid=".$pcliid;  
  }
  if ($ptraid>0){
    $query .=" and cartaporte.traid=".$ptraid;  
  }
  
  if ($pfolid>0){
    $query .=" and cartaporte.carid=".$pfolid;
  }
  if ($pdesid=="Almacen"){
    $query .=" and cartaporte.destino='Almacen'"; 
  }
  if ($pdesid=="Embarque"){
    $query .=" and cartaporte.destino='Embarque'";  
  }
  if ($ptipo<>"0"){
    $query .=" and cartaporte.tipo='".$ptipo."'"; 
  }
*/


//$cond="cartaporte.carid>0 ";
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
        $conduser.=" and cartaporte.cliid=$ctes[0] ";
      }else{
        foreach ($ctes as $cte) {
          $n=$n+1;
          if ($n==1){
            $conduser.=" and ( cartaporte.cliid=$cte ";
          }else{
            $conduser.=" or cartaporte.cliid=$cte ";
          }
        }
        $conduser.=" ) ";
      }
    }
    if ($tipouser=="Tra"){
      if (count($trans)==1){
        $conduser.=" and cartaporte.traid=$trans[0] ";
      }else{
        foreach ($trans as $tra) {
          $n=$n+1;
          if ($n==1){
            $conduser.=" and ( cartaporte.traid=$tra ";
          }else{
            $conduser.=" or cartaporte.traid=$tra ";
          }
        }
        $conduser.=" ) ";
      }
    }
    
     
    if ($estatus=="Pendientes"){
      $cond="and cartaporte.tipo='PendA' ";
    }else{
      $cond="and (cartaporte.tipo<>'PendC' and cartaporte.tipo<>'PendA') ";
      $cond.="and cartaporte.fecha>=$newf1 ";
      $cond.="and cartaporte.fecha<$newf2 ";
    }  

    if ($bmodo=='S'){
       if ($bcliid>0){
        $cond.="and cartaporte.cliid=$bcliid ";
       }
    }else{
      if (!empty($buscar)){
        $cond .= " and (clientes.nombre LIKE '%".$buscar."%' or transportistas.nombre LIKE '%".$buscar."%' or cartaporte.destino LIKE '%".$buscar."%')"; 
      }
    }

//$tem=$cond;
//$cond='';
//$conduser='';

  $tables="cartaporte, clientes, transportistas";
  $campos="cartaporte.carid, cartaporte.tipoalm, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha , cartaporte.destino, cartaporte.impresa, cartaporte.tipo, left(clientes.nombre,15) as nombre, left(transportistas.nombre,15) as nombretrans ";
  $sWhere="cartaporte.cliid=clientes.cliid and cartaporte.traid=transportistas.traid $cond $conduser";
  $sWhere.=" order by cartaporte.carid desc ";
    

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
        'fecha' => $newf2,
        'xsql' => $xsql,
        'items' => $query->result_array()
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query, 'nrr' => $sWhere, 'y1'=>$y1, 'y2'=>$y2);
    }



/*
      $respuesta = array(
        'error' => FALSE,            
        'page' => $query,
        'query' => $tem,
        'where' => $sWhere,
        'xsql' => $xsql
      );
*/

    $this->response( $respuesta );
  }

/************************************************************************/
 public function seleid_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
  $xsql ="Select carid, cliid, tipoalm, carnum, destino, fecha, dircliid, traid, equipidt, equipidr, choid, plaid, equipidm, manid, asisid, empreid, userid as empid from cartaporte where carid=$id";  
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
  $dat=json_decode($this->datap['datosm']);
  $empid=$dat->empid;
  $this->losdatos();
  $fecha=date("Y-m-d H:i:s");
  $this->data += [ "fecha" => $fecha ];
  $this->data += [ "tipo" => "PendA" ]; // Pendiente Cross
  $this->data += [ "impresa" => "No" ];
  $this->data += [ "afactura" => "N" ];
  $this->data += [ "nvo" => "S" ];
  $this->data += [ "destino" => "Embarque" ];
  $this->data += [ "userid" => $empid ];  

  $respuesta = array(
        'error' => FALSE,
        'dat' => $this->data,
        'datap' => $this->datap);

/*  $this->db->insert('cartaporte',$this->data);
  $id=$this->db->insert_id();
  if ($id>0){
      $respuesta = array(
        'error' => FALSE,
        'id' => $id,
        'dat' => $this->data);
    }else{
        $respuesta = array(
          'error' => TRUE);
   } */
  $this->response( $respuesta ); 
  }

/************************************************************************/
 public function modif_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
  $this->losdatos();
  $this->db->where( 'carid', $id );
  $hecho = $this->db->update( 'cartaporte', $this->data);
  if ($hecho){
      $respuesta = array(
        'error' => FALSE,
        'id' => $this->id,
        'dat'=> $this->data);
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
        'tipoalm'=>$dat->tipoalm,
        'cliid'=>$dat->cliid,
        'empreid'=>$dat->empreid,
        'traid'=>$dat->traid,
        'dircliid'=>$dat->dircliid,
        'equipidt'=>$dat->equipidt,
        'equipidr'=>$dat->equipidr,
        'equipidm'=>$dat->equipidm,
        'choid'=>$dat->choid,
        'manid'=>$dat->manid,
        'asisid'=>$dat->asisid,
        'plaid'=>$dat->plaid
    );
  }
/************************************************************************/
  public function enviarcarta_post(){
    $data = $this->post();
    $dat=json_decode($data['datosm']);
    $tipouser = $data['tipouser'];
    $tipodocs = $data['estatus']; // Pendientes o Liberados
    $tipoalm  = $dat->tipoalm; // 'almacen';  // almacen, almacen2, almacentrans
    $carid    = $dat->carid;
    $dircliid = $dat->dircliid;
    $cliid    = $dat->cliid;
    $empreid  = $dat->empreid;
    $empid    = $dat->empid;
    $traid    = $dat->traid;
    $destino  = "Embarque";
    $equipidm = $dat->equipidm;
    $equipidt = $dat->equipidt;
    $equipidr = $dat->equipidr;
    $choid    = $dat->choid;
    $plaid    = $dat->plaid;
    $manid    = $dat->manid;
    $asisid   = $dat->asisid;

    $tipoc="Alma";
    $tipocar="Almacen";
    $respuesta = array('error' => TRUE, 'carid' => 0);
    $pasa = 1;
    $xsql = "select year(fechaenv) as anio from cartaporte WHERE carid=$carid";
    $query = $this->db->query($xsql);
    $row = $query->row();
    $anio = intval($row->anio);
    $error=0;
    $fecha=date("Y-m-d H:i:s");
    if ($anio==0){  
      $this->db->trans_begin();
      $xsql = "UPDATE cartaporte SET dircliid='$dircliid', destino='$destino', traid='$traid', choid='$choid', plaid='$plaid', fechaenv='$fecha', tipo='$tipoc', equipidt='$equipidt', equipidr='$equipidr', equipidm='$equipidm', manid='$manid',  empreid='$empreid', asisid='$asisid' WHERE carid='".$carid."'";
      $query = $this->db->query($xsql);
      if ($query){
        $xsql = "select * from cartaportedet where carid='".$carid."'";
        $query = $this->db->query($xsql);
        foreach ( $query->result() as $row ) {
          $almid = $row->AlmId;
          if ($almid>0){
            $xsql = "SELECT Capturadas,Cantidad FROM $tipoalm WHERE almid='".$almid."'";
            $query1 = $this->db->query($xsql);
            $row1 = $query1->row();
            $capt=$row1->Capturadas;
            $cant=$row1->Cantidad;
            if ($capt>=$cant){
              $xsql = "UPDATE $tipoalm SET estatus='Term', fechaenv='$fecha', destino='$destino' WHERE almid='".$almid."'";
              $query2 = $this->db->query($xsql);
            }  
          }
        }
      }
    if ($this->db->trans_status() === FALSE){
       $this->db->trans_rollback();
       $carid=0;
    }else{
       $this->db->trans_commit();
    }
  }
  if ($carid>0){
    $respuesta = array('error' => FALSE, 'carid' => $carid);
  }
  

  //$respuesta = array('error' => FALSE, 'dat' => $dat, 'sql'=>$xsql);
  $this->response( $respuesta );

}  

/************************************************************************/
 public function cargardetx_post(){
  $data = $this->post();

  $cliid=$data['cliid'];
  $buscar=$data['buscar'];

  $cond = " and almacen.cliid=$cliid and almacen.estatus='Pend'";
  if (!empty($buscar)){
    $cond .= " and (almacen.carnum LIKE '%".$buscar."%' or almacen.descrip1 LIKE '%".$buscar."%' or almacen.descrip2 LIKE '%".$buscar."%' or almacen.descrip3 LIKE '%".$buscar."%' or almacen.descrip4 LIKE '%".$buscar."%' or clientesmat.descripcion LIKE '%".$buscar."%' )"; 
  }
  $tables="almacen, clientes, clientesmat";
  $campos="almacen.almid, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha , almacen.carnum, almacen.cantidad, (almacen.cantidad-almacen.capturadas) as quedan,  (almacen.cantidad-almacen.capturadas) as cant, almacen.peso, left(clientes.nombre,15) as nombre, almacen.matcliid, almacen.cliid, almacen.descrip1, almacen.descrip2, almacen.descrip3, almacen.descrip4, left(clientesmat.descripcion,15) as material ";
  $sWhere="almacen.cliid=clientes.cliid and almacen.matcliid=clientesmat.matcliid $cond ";
  $sWhere.=" order by almacen.almid desc ";
  $xsql = "SELECT $campos FROM  $tables where $sWhere LIMIT 30";

  $query = $this->db->query($xsql);

  if ($query) {
    $row = $query->row();
    $matid=$row->matcliid;

    $xsql="select descrip1, descrip2, descrip3, descrip4 from clientesmat where matcliid=$matid";
     $query2 = $this->db->query($xsql); 
             
      $respuesta = array(
        'error' => FALSE,
        'descrips' => $query2->result_array(),
        'items' => $query->result_array() 
      );
  }

    $this->response( $respuesta );
  }
  /************************************************************************/
 public function cargardet_post(){
  $data = $this->post();
  $carid=$data['carid'];
  $tipoalm=$data['tipoalm'];

  $xsql="select a.cardetid, left(b.nombre,15) as nombre, a.descrip1, a.descrip2, a.descrip3, a.descrip4, a.peso, a.cantidad from cartaportedet a, proveedores b where a.proid=b.proid and a.carid=".$carid;
  $query = $this->db->query($xsql);
    
  if ($query) {
    $xsql="select a.descrip1, a.descrip2, a.descrip3, a.descrip4, b.cliid from clientesmat a, cartaportedet b where a.matcliid=b.matcliid and b.carid=".$carid." limit 1";
     $query2 = $this->db->query($xsql);              
      $respuesta = array(
        'error' => FALSE,
        'descrips' => $query2->result_array(),
        'items' => $query->result_array() 
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }
    
 //   $respuesta = array('error' => TRUE, "message" => $carid);
    $this->response( $respuesta );
  }
/************************************************************************/
 public function cargaralm_post(){
  $data = $this->post();

  $cliid=$data['cliid'];
  $buscar=$data['buscar'];
  $tipoalm=$data['tipoalm'];

/*
    $respuesta = array(
      'error' => TRUE,
      'descrips' => "No Encontrado",
      'nums' => $query->num_rows()
    ); */

  $cond = " and $tipoalm.cliid=$cliid and $tipoalm.estatus='Pend'";
  if (!empty($buscar)){
    $cond .= " and ($tipoalm.carnum LIKE '%".$buscar."%' or $tipoalm.descrip1 LIKE '%".$buscar."%' or $tipoalm.descrip2 LIKE '%".$buscar."%' or $tipoalm.descrip3 LIKE '%".$buscar."%' or $tipoalm.descrip4 LIKE '%".$buscar."%' or clientesmat.descripcion LIKE '%".$buscar."%' )"; 
  }
  $tables="$tipoalm, clientes, clientesmat";
  $campos="$tipoalm.almid, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha , $tipoalm.carnum, $tipoalm.cantidad, ($tipoalm.cantidad-$tipoalm.capturadas) as quedan,  ($tipoalm.cantidad-$tipoalm.capturadas) as cant, $tipoalm.peso, left(clientes.nombre,15) as nombre, $tipoalm.matcliid, $tipoalm.cliid, $tipoalm.descrip1, $tipoalm.descrip2, $tipoalm.descrip3, $tipoalm.descrip4, left(clientesmat.descripcion,15) as material ";
  $sWhere="$tipoalm.cliid=clientes.cliid and $tipoalm.matcliid=clientesmat.matcliid $cond ";
  $sWhere.=" order by $tipoalm.almid desc ";
  $xsql = "SELECT $campos FROM  $tables where $sWhere LIMIT 30";
  $query = $this->db->query($xsql);
  $respuesta = array(
      'error' => TRUE,
      'descrips' => "No Encontrado"); 
  if ($query->num_rows()>0) {
    $matid=0;
    if ($query) {
      $row = $query->row();
      if ($row){
        $matid=intval($row->matcliid);
      }
    }
    if ($matid>0){
      $xsql="select descrip1, descrip2, descrip3, descrip4 from clientesmat where matcliid=$matid";
      $query2 = $this->db->query($xsql); 
    }else{
      $query2 = '';
    }
      $respuesta = array(
        'error' => FALSE,
        'descrips' => $query2->result_array(),
        'items' => $query->result_array() 
      ); 
  }


/*
    $respuesta = array(
        'error' => FALSE,
        'xsql' => $xsql);
  */
    $this->response( $respuesta );
  }
/************************************************************************/
 public function cargacli_post(){

  $this->datap = $this->post();
  $cliid=$this->datap['cliid'];

  $xsql="select a.crosdetid, a.descrip1, a.descrip2, a.descrip3, a.descrip4, a.proid, a.peso, a.cantidad, a.capturadas, (a.cantidad-a.capturadas) as pendientes, (a.cantidad-a.capturadas) as cant, left(b.nombre,15) as nombre, a.cliid from crossdockdet a, proveedores b where a.cantidad>a.capturadas and a.proid=b.proid and a.estatus='Pend' and a.carid=0 and a.cliid=".$cliid;
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
  public function agregardet_post(){
    $data = $this->post();
    $respuesta = array(
      'error' => TRUE,
      'mensage' => 'Error');
    $almid=$data['almid'];
    $carid=$data['carid'];
    $fecha=date("Y-m-d H:i:s");  
    $cantidad=$data['cant'];    
    $pendientes=$data['quedan'];        
    $userid = $data['empid']; 
    $tipoalm=$data['tipoalm']; // almacen, almacen2, almacentrans
    $xsql = "Select * from $tipoalm Where almid=".$almid;
    $query = $this->db->query($xsql);
 
    if ($query->num_rows()>0) {
      $row = $query->row();
      $cliid=$row->CliId;
      $proid=$row->ProId;
      $carnum=$row->CarNum;
      $descr1=$row->Descrip1;
      $descr2=$row->Descrip2;
      $descr3=$row->Descrip3;
      $descr4=$row->Descrip4;
      $empreid=$row->EmpreId;
      $matcliid=$row->MatCliId;    
      $peso=intval($row->Peso);
      $piezas=intval($row->Cantidad);
      $pesoreal = 0;
      if ($piezas>0){
        $pesoreal = ($cantidad*$peso)/$piezas;
      }
      $xsql = "SELECT cardetid from cartaportedet where almid=".$almid." and carid=".$carid;  
      $query = $this->db->query($xsql);
      $cardetid=0;
      if ($query->num_rows()>0) {
        $row = $query->row();
        if ($row){
          $cardetid=intval($row->cardetid);
        }
      }
      $desid='Embarque';

      $respuesta = array(
      'error' => TRUE,
      'mensage' => 'Error',
      'pesoreal'=> $xsql);  

   
      if ($cardetid>0){
        $xsql = "UPDATE cartaportedet SET cantidad=cantidad+".$cantidad.", peso=peso+".$pesoreal." WHERE cardetid=".$cardetid;
        $query = $this->db->query($xsql); 
      }else{
         $xsql = "INSERT INTO cartaportedet (carid, almid, fecha, cantidad, peso, carnum, descrip1, descrip2, descrip3, descrip4, cliid, proid, matcliid, empreid, userid, destino, estatus, nvo) VALUES ('$carid', '$almid', '$fecha', '$cantidad', '$pesoreal', '$carnum', '$descr1', '$descr2', '$descr3', '$descr4', '$cliid', '$proid', '$matcliid', '$empreid', '$userid', '$desid', 'Alma', 'S')"; 
        $query = $this->db->query($xsql);
      }
    
      if ($query){
        if ($cantidad==$pendientes){
           $xsql = "UPDATE $tipoalm SET capturadas=cantidad, estatus='Captur' WHERE almid=".$almid;
           $query = $this->db->query($xsql);
        }else{
           $xsql = "UPDATE $tipoalm SET capturadas=capturadas+".$cantidad." WHERE almid=".$almid;
           $query = $this->db->query($xsql);
        }
      }  
     if ($query){
        $respuesta = array(
              'error' => FALSE,
              'mensage' => 'Correcto');
      }else{
        $respuesta = array(
              'error' => TRUE,
              'mensage' => 'Error');
      }

  }

  /*  
    $respuesta = array(
            'error' => TRUE,
            'cardet' => $cardetid,
            'mensage' => $xsql); 
            */
   $this->response( $respuesta );
}
// ************************************************************ 
  public function quitardet_post(){
    $data = $this->post();
    $cardetid=$data['cardetid'];
    $tipoalm=$data['tipoalm']; // almacen, almacen2, almacentrans

    $xsql="SELECT * from cartaportedet where cardetid=".$cardetid;
    $query = $this->db->query($xsql);
    $row = $query->row();
    $cantidad = $row->Cantidad;
    $almid = $row->AlmId;
    
    $estatus = "Pend";
    $xsql = "DELETE from cartaportedet where cardetid=".$cardetid;
    $query = $this->db->query($xsql);
    if ($query){
      $xsql="UPDATE $tipoalm SET estatus='".$estatus."', capturadas=capturadas-".$cantidad." WHERE almid=".$almid;
      $query = $this->db->query($xsql);
      $xsql = "ALTER TABLE cartaportedet AUTO_INCREMENT=1";
      $query = $this->db->query($xsql);
    }

    if ($query){
      $respuesta = array(
            'error' => FALSE,
            'mensage' => 'Correcto');
    }else{
      $respuesta = array(
            'error' => TRUE,
            'mensage' => 'Error');
    }
    $this->response( $respuesta );
}
// ************************************************************     
  private function pruebas(){

    //***********************************************************
    if ($axion=="elimina"){
      if (!empty($cardetid)){
        
        //echo "CarDetId = ".$cardetid;

        $xsql="SELECT * from cartaportedet where cardetid=".$cardetid;
        //echo $xsql;
        $query=mysqli_query($con, $xsql);
        $row=mysqli_fetch_array($query);
        $cantidad = $row["Cantidad"];
        $doctraid = $row["DocTraId"];
        //echo "cantidad: ".$cantidad;
        //echo "doctraid: ".$doctraid;
        
        $estatus = "Pend";
        $query=mysqli_query($con, "DELETE from cartaportedet where cardetid=".$cardetid);
        if ($query>0){
          $xsql="UPDATE doctrailers SET estatus='".$estatus."', descargadas=descargadas-".$cantidad." WHERE doctraid=".$doctraid;
          //echo $xsql;
          $query=mysqli_query($con, $xsql);
          echo mysqli_error($con);

          $query=mysqli_query($con, "ALTER TABLE cartaportedet AUTO_INCREMENT=1");
        }
      }
    } 
  //*************************************************


  }




}
