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
    $tipoin = $data['tipoin']; 
    


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
  $campos="doccarros.doccarid+0 as doccarid, doccarros.sello, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha, clientes.nombre, left(clientesdir.descripcion,20) as descripcion, doccarros.carnum, doccarros.carid,";
  //, left(transportistas.nombre,20) as nombret,";
  $campos.="doccarros.piezas, doccarros.pesoreal, doccarros.tipocarro, doccarros.tipoin ";
//  $campos.="doccarros.traid, doccarros.piezas, doccarros.peso, doccarros.tipocarro ";

  $sWhere=" doccarros.cliid=clientes.cliid and doccarros.dircliid=clientesdir.dircliid $cond $conduser";
  $sWhere.=" order by doccarros.doccarid desc ";

/*
  $tables="doccarrosn, clientes, clientesdir, transportistas";
  $campos="doccarrosn.doccarid, doccarrosn.sello, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha, left(clientes.nombre,25) as nombre, left(clientesdir.descripcion,20) as descripcion, doccarrosn.carnum, doccarrosn.carid, left(transportistas.nombre,20) as nombret, ";
  $campos.="doccarrosn.traid, doccarrosn.piezas, doccarrosn.peso, doccarrosn.tipocarro ";
  $sWhere="doccarrosn.cliid=clientes.cliid and doccarrosn.traid=transportistas.traid and doccarrosn.dircliid=clientesdir.dircliid $cond $conduser";
  $sWhere.=" order by doccarrosn.doccarid desc ";
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
  $tipocarro = $this->datap['tipo'];  //N-Nacional, I-Internacional, C-aja
  $this->losdatos();
  date_default_timezone_set('America/Chihuahua'); 
  $fecha=date("Y-m-d H:i:s");
  $this->data += [ "fecha" => $fecha ];
  $this->data += [ "estatus" => "Pend" ];
  $this->data += [ "tipocarro" => $tipocarro ];
  $this->data += [ "tipoin" => $tipocarro ];  
  $this->data += [ "nvo" => "S" ];
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
    date_default_timezone_set('America/Chihuahua'); 
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
    $xsql = "select estatus,tipoin from doccarros WHERE doccarid=".$doccarid;

    $query = $this->db->query($xsql);
    $row = $query->row();
    $estatus = $row->estatus;
    $tipoin = $row->tipoin;
    
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
        $xsql = "INSERT INTO cartaporte(fecha, cliid, traid, destino, dircliid, choid, plaid, tipo, doccarid, empreid, userid, equipidm, manid, asisid, nvo, terminada) VALUES('$fecha', '$cliid', '$traid', '$desid', '$dircliid', '$choid', '$plaid', 'Carr', $doccarid, $empreid, $empid, $equipidm, $manid, $asisid, 'S', 'S');";
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
               $xsql = "INSERT INTO cartaportedet(fecha, cardocid, cardocdetid, carid, cliid, cantidad, estatus, empreid, userid, peso, doctraid, nvo) VALUES('$fecha', '$doccarid', '$cardocdetid', '$carid', '$cliid', '$cantidad', 'Car', $empreid, $empid, $peso, $doctraid, 'S')";
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
    $doccarid=$data['doccarid'];
    $doctradetid=$data['doctradetid'];    
    $cantidad=$data['cantidad'];
    $pesoreal=$data['pesoreal'];
    
    $respuesta = array('error' => TRUE, 'mensage' => 'Error de Actualizacion');
    $xsql = "DELETE from doccarrosdet where cardetid=".$cardetid;
    $query = $this->db->query($xsql);
    if ($query){
        $xsql="UPDATE doctrailers SET estatus='Pend', descargadas=descargadas-".$cantidad." WHERE doctraid=".$doctraid;
        $query = $this->db->query($xsql);
        $xsql1="UPDATE doctrailersdet SET estatus='Pend', descargadas=descargadas-".$cantidad." WHERE doctradetid=".$doctradetid;
        $query = $this->db->query($xsql1);
        if ($query){
          
          $xsql="UPDATE doccarros SET piezas=piezas-".$cantidad.", pesoreal = pesoreal - ".$pesoreal." WHERE doccarid=".$doccarid;
          $query = $this->db->query($xsql);
          
          $xsql = "ALTER TABLE doccarrosdet AUTO_INCREMENT=1";
          $query = $this->db->query($xsql);
          $respuesta = array(
            'error' => FALSE,
            'mensage' => $xsql1);
        }
    }
    $this->response( $respuesta );
  }
/************************************************************************/
  public function cargartrail_post(){
    $data = $this->post();
    date_default_timezone_set('America/Chihuahua'); 

    $doccarid=$data['doccarid'];
    $doctraid=$data['doctraid'];
    $doctradetid=$data['doctradetid'];
    $cant=$data['cant'];
    $pendient=$data['pendient'];
    $empid=$data['empid'];
//    $xsql ="Select empreid, peso, piezas, traid from doctrailers where doctraid=".$doctraid; 
    $xsql ="Select empreid, peso, cantidad, traid, descrip, pedimento, almid from doctrailersdet where doctradetid=".$doctradetid; 
    $query = $this->db->query($xsql);
    $row = $query->row();
//    $piezas = $row->piezas;
    $piezas = $row->cantidad;
    $peso = $row->peso;
    $traid = $row->traid;
    $descrip = $row->descrip;
    $pedimento = $row->pedimento;
    $almid = $row->almid;
    
    $empreid=$row->empreid;
    $pesoreal = 0;
    if ($piezas>0){
      $pesoreal = intval(($cant*$peso)/$piezas);
    }
 
    $xsql="SELECT cardetid from doccarrosdet where doctradetid=".$doctradetid." and doccarid=".$doccarid;
    $x=1;
    $query = $this->db->query($xsql);
    if ($query) {
      $row = $query->row();
      if ($row){
        $cardetid=$row->cardetid;
      }else{
        $cardetid=0;
      }
    }else{
        $cardetid=0;
    }
  
    if ($cardetid>0){
      $xsql="UPDATE doccarrosdet SET cantidad=cantidad+".$cant.", pesoreal = pesoreal + ".$pesoreal." WHERE cardetid=".$cardetid;
    }else{
      $xsql="INSERT INTO doccarrosdet (doccarid, doctraid, doctradetid, traid, cantidad, empreid, userid, pesoreal, nvo, descrip1, almid) VALUES ('$doccarid', '$doctraid', '$doctradetid', '$traid', '$cant', '$empreid', '$empid', '$pesoreal', 'S', '$descrip', '$almid')";
    }
    $query = $this->db->query($xsql);
    if ($query){
      $xsql = "UPDATE doctrailers SET descargadas=descargadas+".$cant." WHERE doctraid=".$doctraid;
      $query = $this->db->query($xsql);
    //  $xsql = "UPDATE doctrailersdet SET descargadas=descargadas+".$cant." WHERE doctradetid=".$doctradetid;
    //  $query = $this->db->query($xsql);
      
      if ($query){
        $xsql = "Select descargadas,piezas from doctrailers WHERE doctraid=".$doctraid;
        $query = $this->db->query($xsql);
        $row = $query->row();
        if ($row->descargadas==$row->piezas){
          $xsql = "UPDATE doctrailers SET estatus='EnCar' WHERE doctraid=".$doctraid;
          $query = $this->db->query($xsql);
        }
      }
    }

    $xsql = "UPDATE doctrailersdet SET descargadas=descargadas+".$cant." WHERE doctradetid=".$doctradetid;
    $query = $this->db->query($xsql);
    if ($query){
      $xsql = "Select descargadas,cantidad from doctrailersdet WHERE doctradetid=".$doctradetid;
      $query = $this->db->query($xsql);
      $row = $query->row();
      if ($row->descargadas==$row->cantidad){
        $xsql = "UPDATE doctrailersdet SET estatus='EnCar' WHERE doctradetid=".$doctradetid;
        $query = $this->db->query($xsql);
      }
    }
    $xsql="UPDATE doccarros SET piezas=piezas+".$cant.", pesoreal = pesoreal + ".$pesoreal." WHERE doccarid=".$doccarid;
    $query = $this->db->query($xsql);
    if ($query){
      $respuesta = array(
            'error' => FALSE,
            'cardetid' => $cardetid,
            'mensage' => $pesoreal);
    }else{
      $respuesta = array(
            'error' => TRUE,
            'cardetid' => $cardetid,
            'mensage' => $pesoreal);
    } 
    $this->response( $respuesta );

  }
/************************************************************************/
 public function cargardet_post(){
  $data = $this->post();
  $doccarid=$data['doccarid'];
  $tipo=$data['tipo'];
  if ($tipo=='I'){
    $xsql="select a.cardetid, a.doctraid, a.doccarid, a.doctradetid, a.cantidad, a.pesoreal, left(b.nombre,15) as nombre, date(c.fecha) as fecha, c.pedimento from doccarrosdet a, transportistas b, doctrailers c where a.traid=b.traid and a.doctraid=c.doctraid and a.doccarid=".$doccarid;
  }
  if ($tipo=='N'){
//    $xsql="select a.cardetid, a.doctraid, a.doccarid, a.doctradetid, a.cantidad, a.pesoreal, left(b.nombre,15) as nombre, date(c.fecha) as fecha, c.pedimento as referencia from doccarrosdet a, transportistas b, doctrailers c where a.traid=b.traid and a.doctraid=c.doctraid and a.doccarid=".$doccarid;
    $xsql="select a.cardetid, a.doctraid, a.doccarid, a.doctradetid, a.cantidad, a.pesoreal, left(b.nombre,15) as nombre, date(c.fecha) as fecha, a.descrip1 as pedimento from doccarrosdet a, transportistas b, doctrailers c where a.traid=b.traid and a.doctraid=c.doctraid and a.doccarid=".$doccarid;
  }
  if ($tipo=='C'){
    //    $xsql="select a.cardetid, a.doctraid, a.doccarid, a.doctradetid, a.cantidad, a.pesoreal, left(b.nombre,15) as nombre, date(c.fecha) as fecha, c.pedimento as referencia from doccarrosdet a, transportistas b, doctrailers c where a.traid=b.traid and a.doctraid=c.doctraid and a.doccarid=".$doccarid;
        $xsql="select a.cardetid, a.doctraid, a.doccarid, a.doctradetid, a.cantidad, a.pesoreal, left(b.nombre,15) as nombre, date(c.fecha) as fecha, a.descrip1 as pedimento from doccarrosdet a, transportistas b, doctrailers c where a.traid=b.traid and a.doctraid=c.doctraid and a.doccarid=".$doccarid;
  }
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

public function gencarta_post(){
    $data = $this->post();
    $carid=$data['carid'];
    $xsql="select a.destino, a.cliid, a.tipo, a.fecha, a.empreid, a.carid, a.doccarid, b.nombre, b.rfc, b.email, c.carnum, c.sello, e.descripcion, c.tipoin from cartaporte a, clientes b, doccarros c, clientesdir e where a.cliid=b.cliid and a.doccarid=c.doccarid and a.dircliid=e.dircliid and a.carid=".$carid;
//$xsql="select a.destino, a.cliid, a.tipo, a.fecha, a.empreid, a.carid, b.nombre, b.rfc, b.email, e.descripcion from cartaporte a, clientes b, clientesdir e where a.cliid=b.cliid and a.dircliid=e.dircliid and a.carid=".$carid;
    $query1 = $this->db->query($xsql);
    $row1 = $query1->row();
    $empreid = $row1->empreid;
    $cliid = $row1->cliid;
    $tipoin = $row1->tipoin;
    
    
/*
    $nombre = $row->nombre;
    
    $emailcli = trim($row->emailcli);   
    $emailtra = trim($row->emailtra);   
    if ($emailcli=="aterrazasv@hotmail.com"){
        $emailcli="*";
    }
    if (($emailtra=="aterrazasv@hotmail.com") || empty($emailtra)){
        $emailtra="*";
    }
    $rfc = $row->rfc;
    $nombretrans = $row->nombretrans;
    $nombrechofer = $row->nombrechofer;
    $direccion = $row->descripcion;
    $placas = $row->placas;
    $cliid = $row->cliid;    
    $desid = $row->destino;  
*/


    $xsql="SELECT * FROM  business_profile where business_profile.id=".$empreid;
    $query2 = $this->db->query($xsql);

if ($tipoin=="N" || $tipoin=="C"){
  $xsql = "select a.*,b.doctraid,b.pesoreal,a.descrip1 as pedimento,c.peso as pesotot,c.piezas,c.caja from cartaportedet a, doccarrosdet b, doctrailers c where a.cardocdetid=b.cardetid and a.doctraid=c.doctraid and a.carid=".$carid;
}else{
  $xsql = "select a.*,b.doctraid,b.pesoreal,c.pedimento,c.peso as pesotot,c.piezas,c.caja from cartaportedet a, doccarrosdet b, doctrailers c where a.cardocdetid=b.cardetid and a.doctraid=c.doctraid and a.carid=".$carid;
}
    $query3 = $this->db->query($xsql);


    if ($query2) {
      $respuesta = array(
        'error' => FALSE,            
        'carta' => $query1->result_array(),
        'empre' => $query2->result_array(),    
        'detalle' => $query3->result_array()
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query1);
    }
    $this->response( $respuesta );

  }


  public function gencartaok_post(){
    $data = $this->post();
    $carid=$data['carid'];
    $xsql="select a.destino, a.cliid, a.tipo, a.fecha, a.empreid, a.carid, a.doccarid, b.nombre, b.rfc, b.email, c.carnum, c.sello, e.descripcion from cartaporte a, clientes b, doccarros c, clientesdir e where a.cliid=b.cliid and a.doccarid=c.doccarid and a.dircliid=e.dircliid and a.carid=".$carid;

    $query1 = $this->db->query($xsql);
    $row1 = $query1->row();
    $empreid = $row1->empreid;
    $cliid = $row1->cliid;
    $nombre = $row1->nombre;

    $rfc = $row1->rfc;
   
/*
    $respuesta = array(
        'error' => FALSE,            
        'carta' => $nombre
      );
    $this->response( $respuesta );
*/

    $xsql="SELECT * FROM  business_profile where business_profile.id=".$empreid;
    $query2 = $this->db->query($xsql);

    $xsql = "select a.*,b.doctraid,b.pesoreal,c.pedimento,c.peso as pesotot,c.piezas,c.caja from cartaportedet a, doccarrosdet b, doctrailers c where a.cardocdetid=b.cardetid and a.doctraid=c.doctraid and a.carid=".$carid;
    $query3 = $this->db->query($xsql);


    if ($query2) {
      $respuesta = array(
        'error' => FALSE,            
        'carta' => $query1->result_array(),
        'empre' => $query2->result_array(),    
        'detalle' => $query3->result_array()
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query1);
    }
    $this->response( $respuesta );



  }




}
