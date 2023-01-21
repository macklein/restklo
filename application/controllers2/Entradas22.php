<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Entradas extends REST_Controller {


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
        $conduser.=" and entradas.cliid=$ctes[0] ";
      }else{
        foreach ($ctes as $cte) {
          $n=$n+1;
          if ($n==1){
            $conduser.=" and ( entradas.cliid=$cte ";
          }else{
            $conduser.=" or entradas.cliid=$cte ";
          }
        }
        $conduser.=" ) ";
      }
    }
     
    if ($estatus=="Pendientes"){
      $cond="and entradas.estatus='Pend' ";
    }else{
      $cond="and (entradas.estatus<>'Pend') ";
      $cond.="and entradas.fecha>=$newf1 ";
      $cond.="and entradas.fecha<$newf2 ";
    }  

    if ($bmodo=='S'){
       if ($bcliid>0){
        $cond.="and entradas.cliid=$bcliid ";
       }
    }else{
      if (!empty($buscar)){
        $cond .= " and (clientes.nombre LIKE '%".$buscar."%' or entradas.carnum LIKE '%".$buscar."%')"; 
      }
    }

//$tem=$cond;
//$cond='';
//$conduser='';


  $tables="entradas, clientes, clientesmat ";
  $campos="entradas.entid, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha , entradas.carnum, entradas.capturados, entradas.umedida, entradas.upeso, entradas.conflete, entradas.cantidad, entradas.cliid, clientesmat.descripcion, clientes.nombre, cantidad-capturados as faltan";    
  $sWhere="entradas.cliid=clientes.cliid and entradas.matcliid=clientesmat.matcliid $cond $conduser";
  $sWhere.=" order by entradas.entid desc ";
    

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
  $xsql ="Select entid, cliid, matcliid, fecha, estatus, userid as empid, equipidm, manid, asisid, empreid, conflete, umedida, traid, choid, dircliid, equipidt, plaid, equipidr, capturados, cantidad-capturados as faltan, upeso, cantidad from entradas where entid=$id";  
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
  $fecha=date("Y-m-d H:i:s");
  $this->data += [ "fecha" => $fecha ];
  $this->data += [ "estatus" => "Pend" ];
  $this->data += [ "nvo" => "S" ];
  $this->data += [ "userid" => $this->empid ];  
  $this->db->insert('entradas',$this->data);
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
  $this->db->where( 'entid', $id );
  $hecho = $this->db->update( 'entradas', $this->data);
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
    $this->empid=$dat->empid;
    $this->data = array(
    'empreid'    => $dat->empreid,
    'conflete'   => $dat->conflete,
    'traid'      => $dat->traid,
    'choid'      => $dat->choid,
    'cliid'      => $dat->cliid,
    'dircliid'   => $dat->dircliid,
    'matcliid'   => $dat->matcliid,
    'umedida'    => strtoupper($dat->umedida),
    'upeso'      => strtoupper($dat->upeso),
    'cantidad'   => $dat->cantidad,
    'equipidt'   => $dat->equipidt,
    'plaid'      => $dat->plaid, 
    'equipidr'   => $dat->equipidr,
    'equipidm'   => $dat->equipidm,
    'manid'      => $dat->manid,
    'asisid'     => $dat->asisid
    );
  }
/************************************************************************/
  public function cartaporte_post(){
    $data = $this->post();
    $dat=json_decode($data['datosm']);
    $tipouser   = $data['tipouser'];
    $tipodocs   = $data['estatus']; // Pendientes o Liberados
    $entid      = $dat->entid;
    $empid      = $dat->empid;
    $empreid    = $dat->empreid;
    $conflete   = $dat->conflete;
    $traid      = $dat->traid;
    $choid      = $dat->choid;
    $cliid      = $dat->cliid;
    $dircliid   = $dat->dircliid;
    $matcliid   = $dat->matcliid;
    $umedida    = $dat->umedida;
    $upeso      = $dat->upeso;
    $cantidad   = $dat->cantidad;
    $capturados = $dat->capturados;
    $faltan     = $dat->faltan;
    $equipidt   = $dat->equipidt;
    $plaid      = $dat->plaid;    
    $equipidr   = $dat->equipidr;
    $equipidm   = $dat->equipidm;
    $manid      = $dat->manid;
    $asisid     = $dat->asisid;
    $carid=0;
    $ecantidad=0;
    $respuesta = array('error' => TRUE, 'carid' => $carid);
    $xsql = "select estatus from entradas WHERE entid=".$entid;
    $fecha=date("Y-m-d H:i:s");
    $query = $this->db->query($xsql);
    $row = $query->row();
    $estatus = $row->estatus;
    
    if (($tipodocs=="Pendientes") and ($estatus<>"Term")){ 
   //  $this->db->trans_begin();
     $xsql = "UPDATE entradas SET dircliid='$dircliid', choid='$choid', conflete='$conflete', plaid='$plaid', fechaenv='$fecha', equipidm='$equipidm', equipidt='$equipidt', equipidr='$equipidr', manid='$manid', asisid='$asisid', traid='$traid', cliid='$cliid', matcliid='$matcliid', umedida='$umedida', upeso='$upeso', cantidad='$cantidad' WHERE entid='".$entid."'"; 
      $query = $this->db->query($xsql);
      if ($query){
        $xsql = "INSERT INTO cartaporte(fecha, fechaenv, cliid, matcliid, traid, destino, dircliid, choid, plaid, tipo, entid, empreid, userid, impresa, afactura, equipidm, equipidt, equipidr, manid, asisid, nvo) VALUES('$fecha', '$fecha', '$cliid', '$matcliid', '$traid', 'Almacen', '$dircliid', '$choid', '$plaid', 'Ent', $entid, $empreid, $empid, 'No', 'N', $equipidm, $equipidt, $equipidr, $manid, $asisid, 'S');";
        $query = $this->db->query($xsql);
        if ($query) {
          $carid=$this->db->insert_id();        
          $xsql = "UPDATE entradas SET carid=".$carid.", estatus='Term' WHERE entid=".$entid;
          $query = $this->db->query($xsql);
          $xsql = "UPDATE entradasdet SET carid=".$carid." WHERE entid=".$entid;
          $query = $this->db->query($xsql);
          $xsql = "select * from entradasdet where entid=".$entid;
          $query = $this->db->query($xsql);
          if ($query){
            foreach ( $query->result() as $row ) {              
              $entdetid = $rw1['EntDetId'];
              $proid = $rw1['ProId'];
              $cliid = $rw1['CliId'];
              $cantidad = $rw1['Cantidad'];
              $descrip1 = $rw1['Descrip1'];
              $descrip2 = $rw1['Descrip2'];
              $descrip3 = $rw1['Descrip3'];
              $descrip4 = $rw1['Descrip4'];
              $peso   = $rw1['Peso'];
              $matid  = $rw1['MatCliId'];
                //*************  entdetid = Detalle de Carro, entid = numero de carro, carid = carta
              $xsql0 = "INSERT INTO cartaportedet(fecha, entdetid, entid, carid, cliid, proid, cantidad, descrip1, descrip2, descrip3, descrip4, destino, peso, matcliid, estatus, empreid, userid, estatus2, nvo) VALUES('$fecha', '$entdetid', '$entid', '$carid', '$cliid', '$proid', '$cantidad', '$descrip1', '$descrip2', '$descrip3', '$descrip4', 'Almacen', '$peso', '$matid', 'Ent', $empreid, $empid, 'Pend', 'S');";
               $query = $this->db->query($xsql);
            //   if ($query){
            //     $cardetid=$this->db->insert_id();  
            //     $xsql0 = "INSERT INTO almacen (cardetid, entdetid, entid, carid, fecha, cantidad, peso, descrip1, descrip2, descrip3, descrip4, cliid, proid, matcliid, empreid, userid, estatus, tipo) VALUES ('$cardetid', '$entdetid', '$entid', '$carid', '$fecha', '$cantidad', '$peso', '$descrip1', '$descrip2', '$descrip3', '$descrip4', '$cliid', '$proid', '$matid', '$empreid', '$empid', 'Pend', 'Ent')";
                //  $query0  = mysqli_query($con,$xsql);
            //   }
            } 
            $xsql = "Update entradas set enviados='$cantidad', estatus='Term' where entid='".$entid."'";
            $query = $this->db->query($xsql);
          } 
        } 
      } 
  /*    if ($this->db->trans_status() === FALSE){
        $this->db->trans_rollback();
        $carid=0;
      }else{
        $this->db->trans_commit();
      }  */
   }else { 
      $xsql="select carid,empreid from entradas where entid=".$entid;
      $query = $this->db->query($xsql);
      $row = $query->row();
      $carid   = $row->carid;
      $empreid = $row->empreid;
    }

    if ($carid>0){
      $respuesta = array('error' => FALSE, 'carid' => $carid);
    }

    $respuesta = array('error' => FALSE, 'carid' => $xsql0);
    $this->response( $respuesta );
  }  
/************************************************************************/
 public function cargardet_post(){
  $data = $this->post();
  $entid=$data['entid'];

  $xsql="select a.entdetid, left(b.nombre,15) as nombre, a.descrip1, a.descrip2, a.descrip3, a.descrip4, a.peso, a.cantidad, a.capturadas from entradasdet a, proveedores b where a.proid=b.proid and a.entid=".$entid;
  $query = $this->db->query($xsql);
    
  if ($query) {
    $xsql="select a.descrip1, a.descrip2, a.descrip3, a.descrip4, b.cliid from clientesmat a, entradas b where a.matcliid=b.matcliid and b.entid=".$entid;
     $query2 = $this->db->query($xsql); 
             
      $respuesta = array(
        'error' => FALSE,
        'descrips' => $query2->result_array(),
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
    $dat=json_decode($data['datosdet']);;
    $entid=$data['entid'];
    $empid=$data['empid'];
    $empreid=$data['empreid'];
    $matid=$data['matcliid'];
    $cliid=$data['cliid'];
    $proid = $dat->proid;
    $descr1 = $dat->descrip1;
    $descr2 = $dat->descrip2;
    $descr3 = $dat->descrip3;
    $descr4 = $dat->descrip4;    
    $peso     = $dat->peso;    
    $cantidad = $dat->cantidad;    
    $fecha=date("Y-m-d H:i:s");  
    $captur=0;
    $faltan=0;

    $xsql = "Insert into entradasdet (entid, fecha, cliid, proid, descrip1, descrip2, descrip3, descrip4, peso, matcliid, estatus, cantidad, empreid, userid, nvo) Values('$entid', '$fecha', '$cliid', '$proid', '$descr1', '$descr2', '$descr3', '$descr4', '$peso', '$matid', 'Pend', '$cantidad', '$empreid', '$empid, 'S')";
    $query = $this->db->query($xsql);
    if ($query){
      $xsql="Update entradas set capturados=capturados+".$cantidad." where entid=".$entid;
      $query = $this->db->query($xsql);
      $xsql="Select cantidad,capturados from entradas where entid=".$entid;
      $query = $this->db->query($xsql);
          $row = $query->row();
          $captur = $row->capturados;       
          $faltan = $row->cantidad-$row->capturados;       
    }
    if ($query){
      $respuesta = array(
            'error' => FALSE,
            'capturados' => $captur,
            'faltan' => $faltan,
            'mensage' => 'Correcto');
    }else{
      $respuesta = array(
            'error' => TRUE,
            'capturados' => $captur,
            'faltan' => $faltan,
            'mensage' => 'Error');
    }
    $this->response( $respuesta );
  }

// ************************************************************ 
  public function quitardet_post(){
    $data = $this->post();
    $entid=$data['entid'];
    $entdetid=$data['entdetid'];
    $cantidad=$data['cantidad'];
    $captur=0;
    $faltan=0;
    

    $xsql="DELETE from entradasdet where entdetid=".$entdetid;
    $query = $this->db->query($xsql);
    if ($query>0){
      $xsql="UPDATE entradas SET capturados=capturados-".$cantidad." WHERE entid=".$entid;
      $query = $this->db->query($xsql);
      $xsql="ALTER TABLE entradasdet AUTO_INCREMENT=1";
      $query = $this->db->query($xsql);
 
      $xsql="Select cantidad,capturados from entradas where entid=".$entid;
      $query = $this->db->query($xsql);
      $row = $query->row();
      $captur = $row->capturados;       
      $faltan = $row->cantidad-$row->capturados;       
    }
    if ($query){
      $respuesta = array(
            'error' => FALSE,
            'capturados' => $captur,
            'faltan' => $faltan,
            'mensage' => 'Correcto');
    }else{
      $respuesta = array(
            'error' => TRUE,
            'capturados' => $captur,
            'faltan' => $faltan,
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

        $xsql="SELECT * from entradasdet where cardetid=".$cardetid;
        //echo $xsql;
        $query=mysqli_query($con, $xsql);
        $row=mysqli_fetch_array($query);
        $cantidad = $row["Cantidad"];
        $doctraid = $row["DocTraId"];
        //echo "cantidad: ".$cantidad;
        //echo "doctraid: ".$doctraid;
        
        $estatus = "Pend";
        $query=mysqli_query($con, "DELETE from entradasdet where cardetid=".$cardetid);
        if ($query>0){
          $xsql="UPDATE doctrailers SET estatus='".$estatus."', descargadas=descargadas-".$cantidad." WHERE doctraid=".$doctraid;
          //echo $xsql;
          $query=mysqli_query($con, $xsql);
          echo mysqli_error($con);

          $query=mysqli_query($con, "ALTER TABLE entradasdet AUTO_INCREMENT=1");
        }
      }
    } 
  //*************************************************


  }




}
