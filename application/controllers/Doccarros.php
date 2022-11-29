<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Doccarros extends REST_Controller {


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
 //   $newf2->add(new DateInterval('P1D'));

/*
    $nfecha = strtotime ( $bfecha2)  ;
    $nuevafecha = $nfecha +  3600*24;
//    $nuevafecha->modify('+1 day');
//$nuevafecha = date ( 'Y-m-j' , $nuevafecha );

    $newf2 = date('Ymd', $nuevafecha);
//    date_add($newf2, date_interval_create_from_date_string('1 days'));
*/
 
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
        $conduser.=" and doccarros.cliid=$ctes[0] ";
      }else{
        foreach ($ctes as $cte) {
          $n=$n+1;
          if ($n==1){
            $conduser.=" and ( doccarros.cliid=$cte ";
          }else{
            $conduser.=" or doccarros.cliid=$cte ";
          }
        }
        $conduser.=" ) ";
      }
    }
    if ($estatus=="Pendientes"){
      $cond="and doccarros.estatus='Pend' ";
    }else{
      $cond="and doccarros.estatus<>'Pend' ";
      $cond.="and doccarros.fecha>=$newf1 ";
      $cond.="and doccarros.fecha<$newf2 ";
    }  

    if ($bmodo=='S'){
       if ($bcliid>0){
        $cond.="and doccarros.cliid=$bcliid ";
       }
    }else{
      if (!empty($buscar)){
        $cond .= "and (clientes.nombre LIKE '%".$buscar."%' or clientesdir.descripcion LIKE '%".$buscar."%' or doccarros.carnum LIKE '%".$buscar."%')"; 
      }
    }

  $tables="doccarros, clientes, clientesdir";
  $campos="doccarros.doccarid, doccarros.sello, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha, clientes.nombre, left(clientesdir.descripcion,20) as descripcion, doccarros.carnum ";
  $sWhere="doccarros.cliid=clientes.cliid and doccarros.dircliid=clientesdir.dircliid $cond $conduser";
  $sWhere.=" order by doccarros.doccarid desc ";
    
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
    $this->response( $respuesta );

  }

/************************************************************************/
 public function seleid_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
  $xsql ="Select doccarid, cliid, dircliid, fecha, carnum, sello, estatus, carid, userid as empid, equipidm, manid, asisid, empreid from doccarros where doccarid=$id";  
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
  $this->data += [ "userid" => $this->empid ];  
  $this->db->insert('doccarros',$this->data);
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
  $this->db->where( 'doccarid', $id );
  $hecho = $this->db->update( 'doccarros', $this->data);
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
        'cliid'=>$dat->cliid,
        'empreid'=>$dat->empreid,
        'dircliid'=>$dat->dircliid,
        'equipidm'=>$dat->equipidm,
        'manid'=>$dat->manid,
        'asisid'=>$dat->asisid,
        'carnum'=>strtoupper($dat->carnum),
        'sello'=>strtoupper($dat->sello)
    );
  }
/************************************************************************/
  public function cartaporte_post(){
    $data = $this->post();
    $dat=json_decode($data['datosm']);;
    $tipouser = $data['tipouser'];
    $tipodocs = $data['estatus']; // Pendientes o Liberados
    $doccarid = $dat->doccarid;
    $dircliid = $dat->dircliid;
    $cliid    = $dat->cliid;
    $sello    = strtoupper($dat->sello);
    $carnum   = $dat->carnum;
    $equipidm = $dat->equipidm;
    $manid    = $dat->manid;
    $asisid   = $dat->asisid;
    $carnum   = strtoupper($dat->carnum);
    $empreid  = $dat->empreid;
    $empid    = $dat->empid;

    $carid=0;
    $respuesta = array('error' => TRUE, 'carid' => $carid);
    $xsql = "select estatus from doccarros WHERE doccarid=".$doccarid;

    $query = $this->db->query($xsql);
    $row = $query->row();
    $estatus = $row->estatus;
    
    if (($tipodocs=="Pendientes") and ($estatus<>"Term")){ 
      $this->db->trans_begin();
      $xsql = "UPDATE doccarros SET dircliid='$dircliid', sello='$sello', carnum='$carnum', equipidm='$equipidm', manid='$manid', asisid='$asisid' WHERE doccarid=".$doccarid;
      $query = $this->db->query($xsql);
      if ($query){
        $desid="";
        $traid=0;        
        $choid=0;
        $plaid=0; 
        $fecha=date("Y-m-d H:i:s");            
        $xsql = "INSERT INTO cartaporte(fecha, cliid, traid, destino, dircliid, choid, plaid, tipo, doccarid, empreid, userid, equipidm, manid, asisid) VALUES('$fecha', '$cliid', '$traid', '$desid', '$dircliid', '$choid', '$plaid', 'Carr', $doccarid, $empreid, $empid, $equipidm, $manid, $asisid);";
        $query = $this->db->query($xsql);
        if ($query) {
          $carid=$this->db->insert_id();        
          $xsql = "UPDATE doccarros SET carid=".$carid.", estatus='Term' WHERE doccarid=".$doccarid;
          $query = $this->db->query($xsql);
          $xsql = "UPDATE doccarrosdet SET carid=".$carid." WHERE doccarid=".$doccarid;
          $query = $this->db->query($xsql);
          $xsql = "select * from doccarrosdet where doccarid=".$doccarid;
          $query = $this->db->query($xsql);
         if ($query){
            foreach ( $query->result() as $row ) {              
                $cardocdetid = $row->CarDetId;
                $traid = $row->TraId;
                $cantidad = $row->Cantidad;
                $doctraid = $row->DocTraId;
                $peso = $row->PesoReal;
                $empreid = $row->EmpreId;
                //*************  entdetid = Detalle de Carro, doccarid = numero de carro, carid = carta
               $xsql = "INSERT INTO cartaportedet(fecha, cardocid, cardocdetid, carid, cliid, cantidad, estatus, empreid, userid, peso, doctraid) VALUES('$fecha', '$doccarid', '$cardocdetid', '$carid', '$cliid', '$cantidad', 'Car', $empreid, $empid, $peso, $doctraid)";
               $query = $this->db->query($xsql);
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
   }else { 
      $xsql="select carid,empreid from doccarros where doccarid=".$doccarid;
      $query = $this->db->query($xsql);
      $row = $query->row();
      $carid   = $row->carid;
      $empreid = $row->empreid;
    }

    if ($carid>0){
      $respuesta = array('error' => FALSE, 'carid' => $carid);
    }

    $this->response( $respuesta );
  }  
/************************************************************************/
  public function quitartrail_post(){
    $data = $this->post();
    $cardetid=$data['cardetid'];
    $doctraid=$data['doctraid'];
    $cantidad=$data['cantidad'];
    $respuesta = array('error' => TRUE, 'mensage' => 'Error de Actualizacion');
    $xsql = "DELETE from doccarrosdet where cardetid=".$cardetid;
    $query = $this->db->query($xsql);
    if ($query){
      $xsql="UPDATE doctrailers SET estatus='Pend', descargadas=descargadas-".$cantidad." WHERE doctraid=".$doctraid;
        $query = $this->db->query($xsql);
        if ($query){
          $xsql = "ALTER TABLE doccarrosdet AUTO_INCREMENT=1";
          $query = $this->db->query($xsql);
          $respuesta = array(
            'error' => FALSE,
            'mensage' => $xsql);
        }
    }
    $this->response( $respuesta );
  }
/************************************************************************/
  public function cargartrail_post(){
    $data = $this->post();
    $doccarid=$data['doccarid'];
    $doctraid=$data['doctraid'];
    $cantidad=$data['cantidad'];
    $pendient=$data['pendient'];
    $empid=$data['empid'];

    $xsql ="Select empreid, peso, piezas, traid from doctrailers where doctraid=".$doctraid; 
    $query = $this->db->query($xsql);
    $row = $query->row();
    $piezas = $row->piezas;
    $peso = $row->peso;
    $traid = $row->traid;
    $empreid=$row->empreid;

    $pesoreal = 0;
    if ($piezas>0){
      $pesoreal = ($cantidad*$peso)/$piezas;
    }
    $xsql="SELECT cardetid from doccarrosdet where doctraid=".$doctraid." and doccarid=".$doccarid;
    $query = $this->db->query($xsql);
    $row = $query->row();
    $cardetid=$row->cardetid;
    if ($cardetid>0){
      $xsql="UPDATE doccarrosdet SET cantidad=cantidad+".$cantidad.", pesoreal = pesoreal + ".$pesoreal." WHERE cardetid=".$cardetid;
    }else{
      $xsql="INSERT INTO doccarrosdet (doccarid, doctraid, traid, cantidad, empreid, userid, pesoreal) VALUES ('$doccarid', '$doctraid', '$traid', '$cantidad', '$empreid', '$empid', '$pesoreal')";
    }
    $query = $this->db->query($xsql);
    if ($query){
      if ($cantidad==$pendient){
        $xsql = "UPDATE doctrailers SET descargadas=piezas, estatus='EnCar' WHERE doctraid=".$doctraid;
      }else{
        $xsql = "UPDATE doctrailers SET descargadas=descargadas+".$cantidad." WHERE doctraid=".$doctraid;
      }
    }
    $query = $this->db->query($xsql);

    if ($query){
      $respuesta = array(
            'error' => FALSE,
            'mensage' => $pesoreal);
    }else{
      $respuesta = array(
            'error' => TRUE,
            'mensage' => $pesoreal);
    }
    $this->response( $respuesta );

  }
/************************************************************************/
 public function cargardet_post(){
  $data = $this->post();
  $doccarid=$data['doccarid'];
  $xsql="select a.cardetid, a.doctraid, a.cantidad, a.pesoreal, left(b.nombre,15) as nombre, date(c.fecha) as fecha, c.pedimento from doccarrosdet a, transportistas b, doctrailers c where a.traid=b.traid and a.doctraid=c.doctraid and a.doccarid=".$doccarid;
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

// ************************************************* 


  private function pruebas(){

    //***********************************************************
    if ($axion=="elimina"){
      if (!empty($cardetid)){
        
        //echo "CarDetId = ".$cardetid;

        $xsql="SELECT * from doccarrosdet where cardetid=".$cardetid;
        //echo $xsql;
        $query=mysqli_query($con, $xsql);
        $row=mysqli_fetch_array($query);
        $cantidad = $row["Cantidad"];
        $doctraid = $row["DocTraId"];
        //echo "cantidad: ".$cantidad;
        //echo "doctraid: ".$doctraid;
        
        $estatus = "Pend";
        $query=mysqli_query($con, "DELETE from doccarrosdet where cardetid=".$cardetid);
        if ($query>0){
          $xsql="UPDATE doctrailers SET estatus='".$estatus."', descargadas=descargadas-".$cantidad." WHERE doctraid=".$doctraid;
          //echo $xsql;
          $query=mysqli_query($con, $xsql);
          echo mysqli_error($con);

          $query=mysqli_query($con, "ALTER TABLE doccarrosdet AUTO_INCREMENT=1");
        }
      }
    } 
  //*************************************************


  }




}
