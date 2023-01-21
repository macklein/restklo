<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Crossdock extends REST_Controller {


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
        $conduser.=" and crossdock.cliid=$ctes[0] ";
      }else{
        foreach ($ctes as $cte) {
          $n=$n+1;
          if ($n==1){
            $conduser.=" and ( crossdock.cliid=$cte ";
          }else{
            $conduser.=" or crossdock.cliid=$cte ";
          }
        }
        $conduser.=" ) ";
      }
    }
    if ($estatus=="Pendientes"){
      $cond="and (crossdock.estatus='Pend' or crossdock.Enviado='No') ";
    }else{
      $cond="and (crossdock.estatus<>'Pend' and crossdock.Enviado='Si')";
      $cond.="and crossdock.fecha>=$newf1 ";
      $cond.="and crossdock.fecha<$newf2 ";
    }  

    if ($bmodo=='S'){
       if ($bcliid>0){
        $cond.="and crossdock.cliid=$bcliid ";
       }
    }else{
      if (!empty($buscar)){
        $cond .= "and (clientes.nombre LIKE '%".$buscar."%' or clientesmat.descripcion LIKE '%".$buscar."%' or crossdock.carnum LIKE '%".$buscar."%')"; 
      }
    }

  $tables="crossdock, clientes, clientesmat";
  $campos="crossdock.crosid, crossdock.cliid, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha, clientes.nombre, left(clientesmat.descripcion,20) as descripcion, crossdock.carnum, crossdock.cantidad, crossdock.cantidad-crossdock.capturados as faltanc, crossdock.cantidad-crossdock.enviados as faltane, crossdock.capturados, crossdock.enviados ";
  $sWhere="crossdock.cliid=clientes.cliid and crossdock.matcliid=clientesmat.matcliid $cond $conduser";
  $sWhere.=" order by crossdock.crosid desc ";
    
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
  $xsql ="Select crosid, cliid, matcliid, fecha, carnum, estatus, userid as empid, equipidm, manid, asisid, empreid, umedida, capturados, cantidad-capturados as faltan, upeso, cantidad from crossdock where crosid=$id";  
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
  $this->data += [ "enviado" => "No" ];
  $this->data += [ "nvo" => "S" ];
  $this->data += [ "userid" => $this->empid ];
 /* $respuesta = array(
        'error' => FALSE,
        'id' => $this->data); */
  $this->db->insert('crossdock',$this->data);
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
  $this->db->where( 'crosid', $id );
  $hecho = $this->db->update( 'crossdock', $this->data);
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
        'matcliid'=>$dat->matcliid,
        'equipidm'=>$dat->equipidm,
        'umedida'=>strtoupper($dat->umedida),
        'upeso'=>strtoupper($dat->upeso),
        'cantidad'=>$dat->cantidad,
        'manid'=>$dat->manid,
        'asisid'=>$dat->asisid,
        'carnum'=>strtoupper($dat->carnum)
    );
  }
/************************************************************************/
  public function cartaporte_post(){
    $data = $this->post();
    $dat=json_decode($data['datosm']);;
    $tipouser = $data['tipouser'];
    $tipodocs = $data['estatus']; // Pendientes o Liberados
    $crosid = $dat->crosid;
    $dircliid = $dat->dircliid;
    $cliid    = $dat->cliid;
    $sello    = $dat->sello;
    $carnum   = $dat->carnum;
    $equipidm = $dat->equipidm;
    $manid    = $dat->manid;
    $asisid   = $dat->asisid;
    $carnum   = $dat->carnum;
    $empreid  = $dat->empreid;
    $empid    = $dat->empid;

    $carid=0;
    $respuesta = array('error' => TRUE, 'carid' => $carid);
    $xsql = "select estatus from crossdock WHERE crosid=".$crosid;

    $query = $this->db->query($xsql);
    $row = $query->row();
    $estatus = $row->estatus;
    
    if (($tipodocs=="Pendientes") and ($estatus<>"Term")){ 
      $this->db->trans_begin();
      $xsql = "UPDATE crossdock SET dircliid='$dircliid', sello='$sello', carnum='$carnum', equipidm='$equipidm', manid='$manid', asisid='$asisid' WHERE crosid=".$crosid;
      $query = $this->db->query($xsql);
      if ($query){
        $desid="";
        $traid=0;        
        $choid=0;
        $plaid=0; 
        $fecha=date("Y-m-d H:i:s");            
        $xsql = "INSERT INTO cartaporte(fecha, cliid, traid, destino, dircliid, choid, plaid, tipo, crosid, empreid, userid, equipidm, manid, asisid, nvo) VALUES('$fecha', '$cliid', '$traid', '$desid', '$dircliid', '$choid', '$plaid', 'Carr', $crosid, $empreid, $empid, $equipidm, $manid, $asisid, 'S');";
        $query = $this->db->query($xsql);
        if ($query) {
          $carid=$this->db->insert_id();        
          $xsql = "UPDATE crossdock SET carid=".$carid.", estatus='Term' WHERE crosid=".$crosid;
          $query = $this->db->query($xsql);
          $xsql = "UPDATE crossdockdet SET carid=".$carid." WHERE crosid=".$crosid;
          $query = $this->db->query($xsql);
          $xsql = "select * from crossdockdet where crosid=".$crosid;
          $query = $this->db->query($xsql);
         if ($query){
            foreach ( $query->result() as $row ) {              
                $cardocdetid = $row->CarDetId;
                $traid = $row->TraId;
                $cantidad = $row->Cantidad;
                $doctraid = $row->DocTraId;
                $peso = $row->PesoReal;
                $empreid = $row->EmpreId;
                //*************  entdetid = Detalle de Carro, crosid = numero de carro, carid = carta
               $xsql = "INSERT INTO cartaportedet(fecha, cardocid, cardocdetid, carid, cliid, cantidad, estatus, empreid, userid, peso, doctraid, nvo) VALUES('$fecha', '$crosid', '$cardocdetid', '$carid', '$cliid', '$cantidad', 'Car', $empreid, $empid, $peso, $doctraid, 'S')";
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
      $xsql="select carid,empreid from crossdock where crosid=".$crosid;
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
  public function enviacros_post(){
    $data = $this->post();
    $crosid = $data['crosid'];
    $fecha=date("Y-m-d H:i:s");     
    $respuesta = array('error' => TRUE, 'crosid' => $crosid);
    $xsql = "UPDATE crossdock SET enviado='Si', peso='12345', estatus='Term', fecha='$fecha', fechaenv='$fecha' WHERE crosid='".$crosid."'";
    $query = $this->db->query($xsql);
    $estat = "No";
    if ($query){
      $respuesta = array('error' => FALSE, 'carid' => $crosid);
      $estat = "Si";
    }
    $xsql = "Insert into datos set dato='$estat', fecha='$fecha', nvo='S' ";
    $query = $this->db->query($xsql);
    $this->response( $respuesta );
  }  

/************************************************************************/
 public function cargardet_post(){
  $data = $this->post();
  $crosid=$data['crosid'];
  $estatus=$data['estatus'];

  if ($estatus=='Pendientes'){
    $cond=" a.estatus='Pend' ";
  } else{
    $cond=" a.estatus<>'Pend' ";
  }


  $xsql="select a.crosdetid, left(b.nombre,15) as nombre, a.descrip1, a.descrip2, a.descrip3, a.descrip4, a.peso, a.cantidad, a.capturadas from crossdockdet a, proveedores b where $cond and a.proid=b.proid and a.crosid=".$crosid;
  $query = $this->db->query($xsql);


/*
  $rows = $query1->num_rows();
  if ($rows>0){
    $items = $query->result_array();
  }else{
    $items = [];
  }
*/
  if ($query){
    $xsql="select a.descrip1, a.descrip2, a.capcantidad, a.descrip3, a.descrip4, b.cliid from clientesmat a, crossdock b where a.matcliid=b.matcliid and b.crosid=".$crosid;
       $query2 = $this->db->query($xsql); 
       $respuesta = array(
          'error' => FALSE,
          'descrips' => $query2->result_array(),
          'items' => $query->result_array()
        );
  } else {
    $respuesta = array('error' => TRUE, "message" => $query);
  }

//     $respuesta = array('error' => TRUE, "message" => $xsql);

    $this->response( $respuesta );
  }


  /************************************************************************/
  public function agregardet_post(){
    $data = $this->post();
    $dat=json_decode($data['datosdet']);
    $crosid=$data['crosid'];
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

    $xsql = "Insert into crossdockdet (crosid, fecha, cliid, proid, descrip1, descrip2, descrip3, descrip4, peso, matcliid, estatus, cantidad, empreid, userid, nvo) Values('$crosid', '$fecha', '$cliid', '$proid', '$descr1', '$descr2', '$descr3', '$descr4', '$peso', '$matid', 'Pend', '$cantidad', '$empreid', '$empid', 'S')";
    $query = $this->db->query($xsql);
    if ($query){
      $xsql="Update crossdock set capturados=capturados+".$cantidad." where crosid=".$crosid;
      $query = $this->db->query($xsql);
      $xsql="Select cantidad,capturados from crossdock where crosid=".$crosid;
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
    $crosid=$data['crosid'];
    $crosdetid=$data['crosdetid'];
    $cantidad=$data['cantidad'];
    $captur=0;
    $faltan=0;
    

    $xsql="DELETE from crossdockdet where crosdetid=".$crosdetid;
    $query = $this->db->query($xsql);
    if ($query>0){
      $xsql="UPDATE crossdock SET capturados=capturados-".$cantidad." WHERE crosid=".$crosid;
      $query = $this->db->query($xsql);
      $xsql="ALTER TABLE crossdockdet AUTO_INCREMENT=1";
      $query = $this->db->query($xsql);
 
      $xsql="Select cantidad,capturados from crossdock where crosid=".$crosid;
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

        $xsql="SELECT * from crossdockdet where cardetid=".$cardetid;
        //echo $xsql;
        $query=mysqli_query($con, $xsql);
        $row=mysqli_fetch_array($query);
        $cantidad = $row["Cantidad"];
        $doctraid = $row["DocTraId"];
        //echo "cantidad: ".$cantidad;
        //echo "doctraid: ".$doctraid;
        
        $estatus = "Pend";
        $query=mysqli_query($con, "DELETE from crossdockdet where cardetid=".$cardetid);
        if ($query>0){
          $xsql="UPDATE doctrailers SET estatus='".$estatus."', descargadas=descargadas-".$cantidad." WHERE doctraid=".$doctraid;
          //echo $xsql;
          $query=mysqli_query($con, $xsql);
          echo mysqli_error($con);

          $query=mysqli_query($con, "ALTER TABLE crossdockdet AUTO_INCREMENT=1");
        }
      }
    } 
  //*************************************************


  }




}
