<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Doctrailersn extends REST_Controller {


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
  $campos="doctrailers.doctraid, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha, doctrailers.enviado, left(transportistas.nombre,20) as nombretrans, clientes.nombre, doctrailers.pedimento, doctrailers.caja, doctrailers.matid, doctrailers.piezas as cantidad, doctrailers.descargadas, doctrailers.tipofactcp, doctrailers.serie, doctrailers.conxml, doctrailers.facidn, doctrailers.numfac, doctrailers.timbrada, doctrailers.impresa, doctrailers.traid ";
  $sWhere=" doctrailers.traid=transportistas.traid and doctrailers.tipoin='N' and doctrailers.cliid=clientes.cliid $cond $conduser";
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
//  $xsql = "select doctraid, date(fecha) as fecha, traid, pedimento, CAST(peso as UNSIGNED) as peso, piezas, piezas-descargadas as pendientes, piezas-descargadas as cantidad from doctrailers where piezas>descargadas and estatus='Pend' and cliid=".$cliid;
  $xsql = "select doctradetid, doctraid, date(fecha) as fecha, traid, pedimento, CAST(peso as UNSIGNED) as peso, cantidad as piezas, cantidad-descargadas as pendientes, cantidad-descargadas as cantidad from doctrailersdet where cantidad>descargadas and estatus='Pend' and cliid=".$cliid;
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

 public function alta_post(){
  $this->datap = $this->post();
  $dat=json_decode($this->datap['datosm']);
  date_default_timezone_set('America/Chihuahua'); 
  $ruta=$this->datap['chips'];
  $tipotrail=$this->datap['tipotrail'];
  $empid=$dat->empid;
  $traid=$dat->traid;
  $traid=13;
  $cliid=$dat->cliid;
  $xsql = "Select EmpreIdTrail from clientes Where CliId=$cliid";
  $querycli = $this->db->query($xsql);
  $rowcli = $querycli->row();
  $empreid = $rowcli->EmpreIdTrail;
  
  $xsql = "select nombre,rfc,precio,serie,factdescrip,tipocp,metpago,codigosat,unidadsat from transportistas where traid=".$traid;  
  $querytrans = $this->db->query($xsql);
  $rowtrans = $querytrans->row();
  $tipocp = $rowtrans->tipocp; // Este Sirve para el Internacional Solamente
  $serie = $rowtrans->serie;
  $codigosat = $rowtrans->codigosat;
  $unidadsat = $rowtrans->unidadsat;
  $descrip = $rowtrans->factdescrip;

  $tipocp='N'; 
  $conxml='N';
  $serie = '';
  if ($tipotrail=='C'){ //Si es Con, hay que Facturar
    $tipocp='F';
    $conxml='S';
    $serie = 'AT';  //Armando Terrasas
  }
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
  $this->data += [ "conxml" => $conxml ]; 
  $this->data += [ "moneda" => "P" ]; 
  $this->data += [ "enviado" => "No" ];  
  $this->data += [ "impresa" => "N" ];  
  $this->data += [ "tipoin" => "N" ];  
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
    $arancel='';
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
    $xsql = "Insert into doctrailersdet (doctraid, fecha, cliid, matid, traid, codigosat, unidadsat, descrip, peso, estatus, cantidad, empreid, userid, nvo, pedimento, caja, upeso, umedida, arancel, tipoin) Values('$doctraid', '$fecha', '$cliid', '$matid0', '$traid', '$dat->codigosat', '$dat->unidadsat', '$dat->descrip', '$dat->peso', 'Pend', '$dat->cantidad', '$empreid', '$empid', 'S', '$pedm', '$caja', '$dat->upeso', '$dat->umedida', '$arancel', 'N')";
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
  $xsql = "UPDATE doctrailers SET arancel='U' WHERE doctraid=".$doctraid;
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
        $x = "insert into datos set dato='Simon Paso por Aqui DOCTRANAC'";
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


/*
Agregar esto en el transportista
  $codigosat = $rowtrans->codigosat;
  $unidadsat = $rowtrans->unidadsat;
  $descrip = $rowtrans->factdescrip;
*/