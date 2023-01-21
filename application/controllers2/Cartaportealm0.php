<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
//require_once( APPPATH.'/controllers/Cartaporte.php' );
//require_once( APPPATH.'controllers/Cartaporte.php'); //include controller
use Restserver\libraries\REST_Controller;


class Cartaportealm extends REST_Controller {


  var $datap,$data,$id,$facidcp, $gcarid, $gtipocp;

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
      $cond="and (cartaporte.tipo='PendA' or cartaporte.terminada='A') ";
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

  $tables="cartaporte, clientes, transportistas, transchofer";
  $campos="cartaporte.carid, cartaporte.tipoalm, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha , cartaporte.destino, cartaporte.impresa, cartaporte.tipo, left(clientes.nombre,15) as nombre, left(transportistas.nombre,15) as nombretrans, transchofer.nombrechofer as chofer, cartaporte.tipofactcp, cartaporte.serie, cartaporte.facidn, cartaporte.numfac, cartaporte.timbrada, cartaporte.cliid, cartaporte.tipocp  ";
  $sWhere="cartaporte.cliid=clientes.cliid and cartaporte.traid=transportistas.traid and cartaporte.choid=transchofer.choid $cond $conduser";
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



//$respuesta = array('error' => TRUE, "message" => $sWhere);



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
  $xsql ="Select carid, cliid, tipoalm, carnum, destino, fecha, dircliid, traid, equipidt, equipidr, choid, plaid, equipidm, manid, asisid, empreid, userid as empid, ruta, tipofactcp, facidn, numfac from cartaporte where carid=$id";  
  $query = $this->db->query($xsql);
//  $records[] = array( 'label' => 'PLAC123', 'value' => 1 );
    if ($query) {
      $row = $query->row();
      $equipid = $row->equipidt;  
      $traid = $row->traid;  
      if ($traid==13){
         $xsql2 = "Select vehiculos.vehid, vehiculos.placas, vehiculos.choid, vehiculos.remid from vehiculos where equipidt=$equipid";
      }else{
         $xsql2 = "Select vehiculos.vehid, vehiculos.placas, vehiculos.choid, vehiculos.remid from vehiculos where vehid=$equipid";
      }
      $query2 = $this->db->query($xsql2);
      $row = $query2->row();
      $records[] = array( 'label' => $row->placas, 'value' => 1 );
 
      $respuesta = array(
        'error' => FALSE,      
        'placas' => $records,     
        'item' => $query->result_array()
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }
//$respuesta = array('error' => TRUE, "message" => $xsql2);
    $this->response( $respuesta );
  }
 /************************************************************************/
  public function alta_post(){
  $this->datap = $this->post();
  $ruta=$this->datap['chips'];
  $tipocp=$this->datap['tiposal'];
  $placa=$this->datap['placa'];
  
  
  date_default_timezone_set('America/Chihuahua'); 
  $dat=json_decode($this->datap['datosm']);
  $empid=$dat->empid;
  $this->losdatos('A');
  $fecha=date("Y-m-d H:i:s");
  $this->data += [ "fecha" => $fecha ];
  $this->data += [ "ruta" => $ruta ];
  $this->data += [ "tipo" => "PendA" ]; // Pendiente Cross
  $this->data += [ "impresa" => "X" ];
  $this->data += [ "terminada" => "A" ]; //Le pongo A cuando es de Almacen y N=CrossDock  
  $this->data += [ "afactura" => "N" ];
  $this->data += [ "nvo" => "S" ];
  $this->data += [ "destino" => "Embarque" ];
  $this->data += [ "tipocp" => $tipocp ];  
  $this->data += [ "placa" => $placa ];  
  
  $this->data += [ "userid" => $empid ];  
  $this->db->insert('cartaporte',$this->data);
  $id=$this->db->insert_id();
  if ($id>0){
      $respuesta = array(
        'error' => FALSE,
        'ruta' => $ruta,
        'tipofactcp' => $this->data["tipofactcp"],
        'id' => $id,
        'dat' => $this->data);
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
  $this->losdatos('M');
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
    $dirid = $dat->dircliid;
    $id = $dat->cliid;
    $tcp = "N";
    $xsql = "select tipocp5 from clientes where cliid=".$id;  
    $query = $this->db->query($xsql);
    if ($query) {
      $row = $query->row();
      $tcp = $row->tipocp5;  
    }
    $this->empid=$dat->empid;
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
        'plaid'=>$dat->plaid,
        'tipofactcp'=>$tcp, 
    );
  }
/************************************************************************/
  public function enviarcarta_post(){
    $data = $this->post();
    date_default_timezone_set('America/Chihuahua'); 
    $this->facidcp=0;
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
    $xsql = "select year(fechaenv) as anio,tipofactcp,tipocp from cartaporte WHERE carid=$carid";
    $query = $this->db->query($xsql);
    $row = $query->row();
    $anio = intval($row->anio);
    $tipofactcp = $row->tipofactcp;
    $tipocp = $row->tipocp;
    
    if ($tipocp==1 || $tipocp==2){
      $xsql = "select vehid from vehiculos WHERE equipidt=$equipidt";
      $query = $this->db->query($xsql);
      $row = $query->row();
      $vehid = $row->vehid+0;
    }else{
      $vehid = $equipidt+0;
    }

// $this->response( $respuesta );
    $xsql = "select latitude, longitude from clientesdir WHERE dircliid=$dircliid";
    $lat=0;
    $long=0;
    if ($dircliid>0){
      $query = $this->db->query($xsql);
      $row = $query->row();
      $lat = $row->latitude;
      $long = $row->longitude;
    }

    
    $error=0;
    $fecha=date("Y-m-d H:i:s");
    if ($anio==0 && $numfac==0){  
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

      $xsql = "Update vehiculos set CarId=$carid, Estatus='EnRuta', DirCliId=$dircliid, latitude='$lat', longitude='$long' Where VehId=$vehid ";
      $query = $this->db->query($xsql); 

      if ($this->db->trans_status() === FALSE){
        $this->db->trans_rollback();
        $carid=0;
      }else{
        $this->db->trans_commit();
      }
  }

//  $carid=50212;
  if ($carid>0){
    $x = "insert into datos set dato='1CartaPorte:  $carid'";
    $querydat = $this->db->query($x);
    if ($tipocp=="2" || $tipocp=="3"){
      //Generar los Trailers
      $x = "insert into datos set dato='2CartaPorte:  $carid'";
      $querydat = $this->db->query($x);
      $this->GenTrailers($carid,$tipocp);
    }
  
    $this->facidcp=0;
    if ($tipofactcp=="F" || $tipofactcp=="T"){
       $this->facidcp=$numfac;
       if ($numfac==0){
         $this->load->model('GeneraFact');
         $this->facidcp=$this->GeneraFact->genfactura($carid,"cartaalm");
       }
    } 
    $respuesta = array('error' => FALSE, 'carid' => $carid, 'facidcp'=>$this->facidcp);
  }
//  $respuesta = array('error' => FALSE, 'carid' => $carid, 'tipocp'=>$tipocp);
  $this->response( $respuesta );
}  
/************************************************************************/
public function GenTrailers($carid,$tipocp){
  date_default_timezone_set('America/Chihuahua'); 
  $fecha=date("Y-m-d H:i:s");
  $data = array('fecha'=>$fecha);
  $this->db->insert('doctrailers',$data);


  $xsql = "select traid,choid,equipidt,equipidr,equipidm,plaid,placa,cliid,userid,empreid,ruta,serie,tipofactcp from cartaporte WHERE carid=$carid";
  $query = $this->db->query($xsql);
  $row = $query->row();
  $traid = $row->traid;
  $cliid = $row->cliid;
  $xsql = "Select EmpreIdTrail from clientes Where CliId=$cliid";
  $querycli = $this->db->query($xsql);
  $rowcli = $querycli->row();
  $empreid = $rowcli->EmpreIdTrail;
  
  $xsql = "select nombre,rfc,precio,serie,factdescrip,tipocp,metpago,codigosat,unidadsat from transportistas where traid=".$traid;  
  $querytrans = $this->db->query($xsql);
  $rowtrans = $querytrans->row();
  $tipocpf = $rowtrans->tipocp;
  $serie = $rowtrans->serie;
  $codigosat = $rowtrans->codigosat;
  $unidadsat = $rowtrans->unidadsat;
  $descrip = $rowtrans->factdescrip;
  $userid = $row->userid;

  $data += [ "traid" => $traid ];
  $data += [ "choid" => $row->choid ];
  $data += [ "carid" => $carid ];
  
  $data += [ "equipidt" => $row->equipidt ];
  $data += [ "equipidr" => $row->equipidr ];
  $data += [ "plaid" => $row->plaid ];
  $data += [ "placa" => $row->placa ];
  $data += [ "cliid" => $row->cliid ];
  $data += [ "estatus" => "Pend" ];
  $data += [ "userid" => $userid ];  
  $data += [ "ruta" => $row->ruta ];  
  $data += [ "tipofactcp" => $tipocpf ]; 
  $data += [ "tipocp" => $tipocp ]; 
  $data += [ "serie" => $serie ]; 
  $data += [ "codigosatf" => $codigosat ]; 
  $data += [ "unidadsatf" => $unidadsat ]; 
  $data += [ "descrip" => $descrip ]; 
  $data += [ "empreid" => $empreid ]; 
  $data += [ "moneda" => "P" ]; 
  $data += [ "enviado" => "No" ];  
  $data += [ "impresa" => "N" ];  
  $data += [ "tipoin" => "N" ];  
  $data += [ "terminada" => "N" ];  
  $data += [ "timbrada" => "N" ];    
  $data += [ "nvo" => "S" ];
  $data += [ "afactura" => "N" ];    
  $this->db->insert('doctrailers',$data);
  $id=$this->db->insert_id();
  if ($id>0){
      //matid,descrip,pedimento,peso,cantidad,caja
      $xsql = "select cardetid,almid,proid,cantidad,descrip1,peso,matcliid from cartaportedet WHERE carid=$carid";
      $querydet = $this->db->query($xsql);
      foreach ( $querydet->result() as $row ) {
        $almid = $row->almid;
        $cardetid = $row->cardetid;

        $matid = $row->matcliid;
        $descrip = $row->descrip1;
        $peso = $row->peso;
        $cantidad = $row->cantidad;
        $pedm = ""; 
        $caja = "";
        
        $xsql = "select codigosat,unidadsat,matcliid,unidadmed,unidadpes,arancel from clientesmat WHERE matcliid=$matid";
        $querymat = $this->db->query($xsql);
        $rowmat = $querymat->row();
        $upeso = $rowmat->unidadmed;
        $umedida = $rowmat->unidadpes;
        $arancel = $rowmat->arancel;
        $codigosat = $rowmat->codigosat;
        $unidadsat = $rowmat->unidadsat;

        $fecha=date("Y-m-d H:i:s");  
        $xsql = "Insert into doctrailersdet (doctraid, fecha, cliid, matid, traid, codigosat, unidadsat, descrip, peso, estatus, cantidad, empreid, userid, nvo, pedimento, caja, upeso, umedida, arancel, tipoin, carid, cardetid, almid) Values('$id', '$fecha', '$cliid', '$matid', '$traid', '$codigosat', '$unidadsat', '$descrip', '$peso', 'Pend', '$cantidad', '$empreid', '$userid', 'S', '$pedm', '$caja', '$upeso', '$umedida', '$arancel', 'N', '$carid', '$cardetid', '$almid')";
        $query = $this->db->query($xsql);
      }    
      $respuesta = array(
        'error' => FALSE,
        'id' => $id);
    }else{
        $respuesta = array(
          'error' => TRUE);
   }








  $da="CartaPorte: ".$carid."  Tipo: ".$tipocp;
  $x = "insert into datos set dato='$da'";
  $querydat = $this->db->query($x);
}
/************************************************************************/
public function regenerafact_post(){
  $data = $this->post();
  date_default_timezone_set('America/Chihuahua'); 
  $this->facidcp=0;
  $carid = $data['carid'];
  $tipofactcp="F";
  $numfac = 0;
  if ($carid>0){
    if ($tipofactcp=="F" || $tipofactcp=="T"){
       $this->facidcp=$numfac;
       if ($numfac==0){
         $this->load->model('GeneraFact');
         $this->facidcp=$this->GeneraFact->genfactura($carid,"cartaalm");
       }
    } 
    $respuesta = array('error' => FALSE, 'carid' => $carid, 'facidcp'=>$this->facidcp);
  }
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
    date_default_timezone_set('America/Chihuahua'); 
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
  public function gencarta55_post(){
    $data = $this->post();
    $carid=$data['carid'];
  //  $xsql="select a.destino, a.cliid, a.tipo, a.fecha, a.empreid, a.carid, b.nombre, b.rfc, b.email,c.nombre as nombretrans, c.email as emailtra, d.nombrechofer, e.descripcion, f.placas from cartaporte a, clientes b, transportistas c,transchofer d, clientesdir e, transplacas f where a.cliid=b.cliid and a.traid=c.traid and a.choid=d.choid and a.dircliid=e.dircliid and a.plaid=f.plaid and a.carid=".$carid;
  //  $query1 = $this->db->query($xsql);
  //  $row1 = $query1->row();
  //  $empreid = $row1->empreid;
  //  $cliid = $row1->cliid;
    $respuesta = array('error' => TRUE, "message" => $carid);
    $this->response( $respuesta );
  }


  public function gencartaNO_post(){
    $data = $this->post();
    $carid=$data['carid'];
    $xsql="select a.destino, a.cliid, a.tipo, a.fecha, a.empreid, a.carid, b.nombre, b.rfc, b.email,c.nombre as nombretrans, c.email as emailtra, d.nombrechofer, e.descripcion, f.placas from cartaporte a, clientes b, transportistas c,transchofer d, clientesdir e, transplacas f where a.cliid=b.cliid and a.traid=c.traid and a.choid=d.choid and a.dircliid=e.dircliid and a.plaid=f.plaid and a.carid=".$carid;
 /*   $query1 = $this->db->query($xsql);
    $row1 = $query1->row();
    $empreid = $row1->empreid;
    $cliid = $row1->cliid;
    $respuesta = array('error' => TRUE, "message" => $query1);
    */
    $respuesta = array('error' => TRUE, "message" => $xsql);
  
     $this->response( $respuesta );
  }


  public function gencarta_post(){
    $data = $this->post();
    $carid=$data['carid'];
//    $xsql="select a.destino, a.cliid, a.tipo, a.fecha, a.empreid, a.carid, b.nombre, b.rfc, b.email,c.nombre as nombretrans, c.email as emailtra, d.nombrechofer, e.descripcion, f.placas from cartaporte a, clientes b, transportistas c,transchofer d, clientesdir e, transplacas f where a.cliid=b.cliid and a.traid=c.traid and a.choid=d.choid and a.dircliid=e.dircliid and a.plaid=f.plaid and a.carid=".$carid;
    $xsql="select a.destino, a.cliid, a.tipo, a.fecha, a.empreid, a.carid, b.nombre, b.rfc, b.email,c.nombre as nombretrans, c.email as emailtra, d.nombrechofer, e.descripcion, a.placa as placas from cartaporte a, clientes b, transportistas c,transchofer d, clientesdir e where a.cliid=b.cliid and a.traid=c.traid and a.choid=d.choid and a.dircliid=e.dircliid and a.carid=".$carid;
    $query1 = $this->db->query($xsql);
    $row1 = $query1->row();
    $empreid = $row1->empreid;
    $cliid = $row1->cliid;


    
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

    //$xsql = "select a.*,b.nombre from cartaportedet a, proveedores b where a.proid=b.proid and a.carid=".$carid;
    //$query3 = $this->db->query($xsql);


    $xsql = "select a.*,b.nombre,c.descripcion,c.descrip1 as desc1,c.descrip2 as desc2,c.descrip3 as desc3,c.descrip4 as desc4,c.unidadpes,c.unidadmed from cartaportedet a, proveedores b, clientesmat c where a.proid=b.proid and a.matcliid=c.matcliid and a.carid=".$carid;
    $query3 = $this->db->query($xsql);

    $xsql="select * from clientesmat where cliid=".$cliid;
    $query4 = $this->db->query($xsql);     
    if ($query2) {
      $respuesta = array(
        'error' => FALSE,            
        'carta' => $query1->result_array(),
        'empre' => $query2->result_array(),    
        'detalle' => $query3->result_array(),
        'material' => $query4->result_array()        
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query1);
    }
    $this->response( $respuesta );

  }


}
