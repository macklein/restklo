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
     // if (count($trans)==1){
        $conduser.=" and doctrailers.traid=$trans[0] ";
    /*  }else{
        foreach ($trans as $tra) {
          $n=$n+1;
          if ($n==1){
            $conduser.=" and ( doctrailers.traid=$tra ";
          }else{
            $conduser.=" or doctrailers.traid=$tra ";
          }
        }
        $conduser.=" ) ";
      } */
      $cond="and (doctrailers.estatus='Pend' || doctrailers.estatus='Lista') ";
    }else{
      if ($estatus=="Pendientes"){
        $cond="and (doctrailers.estatus='Pend' || doctrailers.estatus='Lista') ";
      }else{
        $cond="and doctrailers.estatus<>'Pend' ";
        $cond.="and date(doctrailers.fecha)>=$newf1 ";
        $cond.="and date(doctrailers.fecha)<$newf2 ";
      }  

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
  $campos="doctrailers.doctraid+0 as doctraid, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha, doctrailers.enviado, left(transportistas.nombre,20) as nombretrans, clientes.nombre, doctrailers.pedimento, doctrailers.caja, doctrailers.matid, doctrailers.piezas as cantidad, doctrailers.descargadas, doctrailers.tipofactcp, doctrailers.tipocp, doctrailers.serie, doctrailers.facidn, doctrailers.numfac, doctrailers.timbrada, doctrailers.impresa, doctrailers.traid, doctrailers.carid ";
  $sWhere=" doctrailers.traid=transportistas.traid and doctrailers.cliid=clientes.cliid $cond $conduser";
  $sWhere.=" order by doctrailers.doctraid desc ";
    
  $conFact = "N";
  $xsql1 = "";
  $traid = 0;
 // $tipouser=="Tra";
  if ($tipouser=="Tra"){
    $traid=$trans[0];
    $xsql1 = "Select confact from transportistas where traid=$traid";
    $query = $this->db->query($xsql1);
    $row = $query->row();
    $conFact = $row->confact;
  }



/*      $respuesta = array(
        'error' => FALSE,            
        'nrr' => $sWhere,
        'cond' => $cond,   
        'confact' => $conFact,   
        'sql' => $trans[0],          
        'cond2' => $conduser  
      ); */
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
    'confact' => $conFact,       
    'xsql' => $xsql,
    'items' => $query->result_array()
  );
} else {
  $respuesta = array('error' => TRUE, "message" => $query, 'nrr' => $sWhere, 'y1'=>$y1, 'y2'=>$y2);
} 
$this->response( $respuesta );

}

public function index22_post(){
    $conFact = "N";
    if ($tipouser=="Tra"){
    //  $traid=$trans[0];
    //  $xsql = "Select confact from transportistas where traid=$traid";
    //  $query = $this->db->query($xsql);
    //  $row = $query->row();
    //  $conFact = $row->confact;
    }
    
    $offset = ($page - 1) * $perPage;
    $xsql="SELECT count(*) AS numrows FROM $tables WHERE $sWhere";
  /*  $query = $this->db->query($xsql);
    $row = $query->row();
    $numrows = $row->numrows;
    if ($numrows>0){
       $paginas = ceil($numrows/$perPage);
    }else{
      $paginas = 1;
    }
    $xsql = "SELECT $campos FROM  $tables where $sWhere LIMIT $offset,$perPage";

    */
 //     $respuesta = array('error' => TRUE, "message" => $xsql);

 $query=true;
  //  $query = $this->db->query($xsql);
    if ($query) {
      $respuesta = array(
        'error' => FALSE,            
        'paginas' => $paginas, 
        'numrows' => $numrows, 
        'confact' => $conFact,       
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
  $this->data="";
  $this->datap = $this->post();
  $id=$this->datap['id'];
//  $xsql ="Select doctraid, traid, userid as empid, equipidt, EquipIdr, PlaId, placa, cliid, empreid, matid, caja, umedida, upeso, pedimento, peso, piezas, ruta from doctrailers where doctraid=$id";  
//  $xsql ="Select doctraid,choid,plaid,equipidr,traid,userid as empid,equipidt,placa,cliid,empreid,matid,caja,umedida,upeso,pedimento,peso,piezas,ruta from doctrailers where doctraid=$id";  
  $xsql ="Select doctraid,choid,plaid,equipidr,traid,userid as empid,equipidt,placa,cliid,empreid,matid,caja,umedida,upeso,pedimento,peso,piezas,ruta from doctrailers where doctraid=$id";  
//  $xsql ="Select * from doctrailers where doctraid=$id";  
//  $xsql ="Select doctraid from doctrailers where doctraid=$id";  

$query = $this->db->query($xsql);
$row = $query->row();

//  $xsql ="Select ruta from doctrailers where doctraid=$id";  
//  $query2 = $this->db->query($xsql);
//  $row = $query2->row();
//  $ruta = $row->ruta;
$ruta = "pruebamm,fhjhdg";
    if ($query) {
      $respuesta = array(
        'error' => FALSE,            
        'ruta' => $ruta, 
        'item' => $row
     //   'item'=> $query->result_array()
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
  $tipo=$this->datap['tipo'];  

/*  $respuesta = array(
    'error' => FALSE,    
    'prueba' => 123,        
    'tipo' => $tipo
  );
*/
  
//  $xsql = "select doctraid, date(fecha) as fecha, traid, pedimento, CAST(peso as UNSIGNED) as peso, piezas, piezas-descargadas as pendientes, piezas-descargadas as cantidad from doctrailers where piezas>descargadas and estatus='Pend' and cliid=".$cliid;
  if ($tipo=='I' || $tipo=='C'){
    $xsql = "select doctradetid, doctraid, date(fecha) as fecha, traid, pedimento, CAST(peso as UNSIGNED) as peso, cantidad as piezas, cantidad-descargadas as pendientes, cantidad-descargadas as cantidad from doctrailersdet where tipoIN='I' and cantidad>descargadas and estatus='Pend' and cliid=".$cliid;
  }
  if ($tipo=='N'){
//    $xsql = "select doctradetid, doctraid, date(fecha) as fecha, traid, pedimento as referencia, CAST(peso as UNSIGNED) as peso, cantidad as piezas, cantidad-descargadas as pendientes, cantidad-descargadas as cantidad from doctrailersdet where tipoIN='N' and cantidad>descargadas and estatus='Pend' and cliid=".$cliid;
    $xsql = "select doctradetid, doctraid, date(fecha) as fecha, traid, descrip as pedimento, CAST(peso as UNSIGNED) as peso, cantidad as piezas, cantidad-descargadas as pendientes, cantidad-descargadas as cantidad from doctrailersdet where tipoIN='N' and cantidad>descargadas and estatus='Pend' and cliid=".$cliid;
  }
  if ($tipo=='C'){
    //    $xsql = "select doctradetid, doctraid, date(fecha) as fecha, traid, pedimento as referencia, CAST(peso as UNSIGNED) as peso, cantidad as piezas, cantidad-descargadas as pendientes, cantidad-descargadas as cantidad from doctrailersdet where tipoIN='N' and cantidad>descargadas and estatus='Pend' and cliid=".$cliid;
 //   $xsql = "select doctradetid, doctraid, date(fecha) as fecha, traid, descrip as pedimento, CAST(peso as UNSIGNED) as peso, cantidad as piezas, cantidad-descargadas as pendientes, cantidad-descargadas as cantidad from doctrailersdet where tipoIN='C' and cantidad>descargadas and estatus='Pend' and cliid=".$cliid;
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
 /************************************************************************/
 /************************************************************************/
 public function cargaralm_post(){
  $data = $this->post();
  $cliid=$data['cliid'];

//  $buscar=$data['buscar'];
//  $tipoalm=$data['tipoalm'];
  $buscar='';
  $tipoalm='almacen';
  $cond = " and almacen.cliid=$cliid and almacen.estatus='Pend'";
  if (!empty($buscar)){
    $cond .= " and ($tipoalm.carnum LIKE '%".$buscar."%' or $tipoalm.descrip1 LIKE '%".$buscar."%' or $tipoalm.descrip2 LIKE '%".$buscar."%' or $tipoalm.descrip3 LIKE '%".$buscar."%' or $tipoalm.descrip4 LIKE '%".$buscar."%' or clientesmat.descripcion LIKE '%".$buscar."%' )"; 
  }
  $tables="$tipoalm, clientes, clientesmat";
  $campos="$tipoalm.almid, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha , $tipoalm.carnum, $tipoalm.cantidad, ($tipoalm.cantidad-$tipoalm.capturadas) as quedan,  ($tipoalm.cantidad-$tipoalm.capturadas) as cant, $tipoalm.peso, left(clientes.nombre,15) as nombre, $tipoalm.matcliid, $tipoalm.cliid, $tipoalm.descrip1, $tipoalm.descrip2, $tipoalm.descrip3, $tipoalm.descrip4, left(clientesmat.descripcion,15) as material ";
  $sWhere="$tipoalm.cliid=clientes.cliid and $tipoalm.matcliid=clientesmat.matcliid $cond ";
  $sWhere.=" order by $tipoalm.almid desc ";
  $xsql = "SELECT $campos FROM  $tables where $sWhere LIMIT 500";
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
    $this->response( $respuesta );
  }
/************************************************************************/
public function cargaclidealm_post(){
  $this->datap = $this->post();
  $cliid=$this->datap['cliid'];
  $tipo=$this->datap['tipo'];  
  $xsql = "select almid, doctraid, date(fecha) as fecha, traid, pedimento as referencia, CAST(peso as UNSIGNED) as peso, cantidad as piezas, cantidad-descargadas as pendientes, cantidad-descargadas as cantidad from doctrailersdet where tipoIN='N' and cantidad>descargadas and estatus='Pend' and cliid=".$cliid;

//  $xsql = "select doctradetid, doctraid, date(fecha) as fecha, traid, pedimento as referencia, CAST(peso as UNSIGNED) as peso, cantidad as piezas, cantidad-descargadas as pendientes, cantidad-descargadas as cantidad from doctrailersdet where tipoIN='N' and cantidad>descargadas and estatus='Pend' and cliid=".$cliid;
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

 public function alta_post(){
  $this->datap = $this->post();
  $dat=json_decode($this->datap['datosm']);
  date_default_timezone_set('America/Chihuahua'); 
  $ruta=$this->datap['chips'];
  $empid=$dat->empid;
  $traid=$dat->traid;
  $cliid=$dat->cliid;
  $xsql = "Select EmpreIdTrail from clientes Where CliId=$cliid";
  $querycli = $this->db->query($xsql);
  $rowcli = $querycli->row();
  $empreid = $rowcli->EmpreIdTrail;
  
  $xsql = "select nombre,rfc,precio,serie,factdescrip,tipocp,metpago,codigosat,unidadsat from transportistas where traid=".$traid;  
  $querytrans = $this->db->query($xsql);
  $rowtrans = $querytrans->row();
  $tipocp = $rowtrans->tipocp;
  $serie = $rowtrans->serie;
  $codigosat = $rowtrans->codigosat;
  $unidadsat = $rowtrans->unidadsat;
  $descrip = $rowtrans->factdescrip;

//$tipocp = "T";
//$serie = "RH";
//$codigosat = "71892345";
//$unidadsat = "h38";
//$descrip = "FLETE INTERNACIONAL";
  $this->losdatos();
  $fecha=date("Y-m-d H:i:s");
  $this->data += [ "fecha" => $fecha ];
  $this->data += [ "estatus" => "Pend" ];
  $this->data += [ "userid" => $empid ];  
  $this->data += [ "ruta" => $ruta ];  
  $this->data += [ "tipofactcp" => $tipocp ]; 
  $this->data += [ "serie" => $serie ]; 
  $this->data += [ "codigosatf" => $codigosat ]; 
  $this->data += [ "unidadsatf" => $unidadsat ]; 
  $this->data += [ "descrip" => $descrip ]; 
  $this->data += [ "empreid" => $empreid ]; 
  $this->data += [ "moneda" => "P" ]; 
  $this->data += [ "enviado" => "No" ];  
  $this->data += [ "impresa" => "N" ];  
  $this->data += [ "tipoin" => "I" ];  
  $this->data += [ "terminada" => "N" ];  
  $this->data += [ "timbrada" => "N" ];    
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
        'cliid'=>$dat->cliid);
    
    
    //    'matid'=>$dat->matid,
    //    'umedida'=>strtoupper($dat->umedida),
    //    'upeso'=>strtoupper($dat->upeso),
    //    'pedimento'=>strtoupper($dat->pedimento),
    //    'peso'=>$dat->peso,
    //    'piezas'=>$dat->piezas,
    //    'caja'=>strtoupper($dat->caja)
  }

/************************************************************************/
/************************************************************************/
public function cargardet_post(){
  $data = $this->post();
  $doctraid=$data['doctraid'];

  $xsql="select doctradetid, pedimento, arancel, codigosat, unidadsat, descrip, peso, cantidad, caja, matid from doctrailersdet where doctraid=".$doctraid;
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
    $dat=json_decode($data['datosdet']);
    $doctraid=$data['doctraid'];
    $matid0=$dat->matid;
    
    $cliid=$data['cliid'];
    $traid=$data['traid'];
    
    $empid=$data['empid'];
    $empreid=$data['empreid'];
    $xsql="Select matid,piezas from doctrailers where doctraid=".$doctraid;
    $query = $this->db->query($xsql);
    $row = $query->row();
    $matid = $row->matid;   
    $cant = $dat->cantidad; //+$row->piezas;  
    $caja=strtoupper($dat->caja);  
    $pedm=strtoupper($dat->pedimento); 
    
    if ($matid==0){     
  //    $xsql="Update doctrailers set matid=$matid0, umedida='$dat->umedida', codigosat='$dat->codigosat', unidadsat='$dat->unidadsat', descrip='$dat->descrip', upeso='$dat->upeso', pedimento='$pedm', peso=$dat->peso, piezas=$cant, caja='$caja' where doctraid=".$doctraid;
      $xsql="Update doctrailers set matid=$matid0, umedida='$dat->umedida', codigosat='$dat->codigosat', unidadsat='$dat->unidadsat', descrip='$dat->descrip', upeso='$dat->upeso', peso=$dat->peso, piezas=$cant, pedimento='$pedm', caja='$caja' where doctraid=".$doctraid;
      $query = $this->db->query($xsql);
    }else{
      $xsql="Update doctrailers set peso=peso+$dat->peso, piezas=piezas+$cant where doctraid=".$doctraid;
      $query = $this->db->query($xsql);
    }  

    $fecha=date("Y-m-d H:i:s");  
    $xsql = "Insert into doctrailersdet (doctraid, fecha, cliid, matid, traid, codigosat, unidadsat, descrip, peso, estatus, cantidad, empreid, userid, nvo, pedimento, caja, upeso, umedida, arancel, tipoin) Values('$doctraid', '$fecha', '$cliid', '$matid0', '$traid', '$dat->codigosat', '$dat->unidadsat', '$dat->descrip', '$dat->peso', 'Pend', '$dat->cantidad', '$empreid', '$empid', 'S', '$pedm', '$caja', '$dat->upeso', '$dat->umedida', '$dat->arancel', 'I')";
    $query = $this->db->query($xsql);
    if ($query){
      $respuesta = array(
            'error' => FALSE,
            'capturados' => $xsql,
            'mensage' => 'Correcto');
    }else{
      $respuesta = array(
           'error' => TRUE,
            'mensage' => 'Error');
    }
    $this->response( $respuesta );
  }

// ************************************************************ 
  public function quitardet_post(){
    $data = $this->post();
    $doctraid=$data['doctraid'];
    $doctradetid=$data['doctradetid'];
    $cantidad=$data['cantidad'];
    $peso=$data['peso'];
    
    $captur=0;
    $faltan=0;
    $xsql="DELETE from doctrailersdet where doctradetid=".$doctradetid;
    $query = $this->db->query($xsql);
    if ($query>0){
      $xsql="UPDATE doctrailers SET peso=peso-".$peso.", piezas=piezas-".$cantidad." WHERE doctraid=".$doctraid;
      $query = $this->db->query($xsql);
      $xsql="ALTER TABLE doctrailersdet AUTO_INCREMENT=1";
      $query = $this->db->query($xsql);

      $xsql="Select COUNT(doctradetid) as tot from doctrailersdet WHERE doctraid=".$doctraid;
      $query = $this->db->query($xsql);
      $row = $query->row();
      if ($row->tot==0){
         $xsql="UPDATE doctrailers SET matid=0 WHERE doctraid=".$doctraid;
         $query = $this->db->query($xsql);
      }       
    }
    if ($query){
      $respuesta = array(
            'error' => FALSE,
         //   'capturados' => $captur,
         //   'faltan' => $faltan,
            'mensage' => 'Correcto');
    }else{
      $respuesta = array(
            'error' => TRUE,
          //  'capturados' => $captur,
         //   'faltan' => $faltan,
            'mensage' => 'Error');
    }
    $this->response( $respuesta );
}
// ************************************************************     

/************************************************************************/

public function enviarcarta_post(){
  $data = $this->post(); 
  date_default_timezone_set('America/Chihuahua'); 
  $doctraid = $data['doctraid']; //
  $tipofactcp = $data['tipofactcp']; //
  $numfac = $data['numfac']; //

  $fecha=date("Y-m-d H:i:s");  
  $xsql = "UPDATE doctrailers SET arancel='R' WHERE doctraid=".$doctraid;
  $query = $this->db->query($xsql);
  $xrec=0;
  if ($query){
    $xrec=$doctraid;
  }
//   $respuesta = array('error' => FALSE, 'fletid' => $xrec);


  $this->facidcp=0;
  if ($tipofactcp=="F" || $tipofactcp=="T"){
      $this->facidcp=$numfac;
      if ($numfac==0){
     
        $x = "insert into datos set dato='Simon Paso por Aqui'";
        $querydat = $this->db->query($x);
        $this->load->model('GeneraFactDocTrail');
        $this->facidcp=$this->GeneraFactDocTrail->genfactura($doctraid,"doctrail");
      }
  } 
  $respuesta = array('error' => FALSE, 'facidcp'=>$this->facidcp, 'tipofactcp'=>$tipofactcp,'xsql'=>$xsql);

  $this->response( $respuesta );


}  
/************************************************************************/
/************************************************************************/







}
