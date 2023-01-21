<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Transpagos extends REST_Controller {


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
    $traid = $data['traid'];  //No Usado
    $tipouser = $data['tipouser'];  //No Usado
    
//    $traid = 96;
    $paginas = 1;
    $numrows = 100;
    $xsql = "Select a.facidn, a.fecha, a.pedid as facturs, a.serie, b.nombre, a.total, a.tipofac, a.uuid, a.timbrada, a.formapago, a.fechapago, a.empreid as traid, ";
    $xsql = $xsql." a.numfac, CONCAT(a.serie,trim(a.facidn)) as folio, a.cliid from trafacturas a, emisor b where a.tipofac='P' and a.cancel<>'S' and a.cliid=b.emiid and a.empreid=$traid order by facidn";
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
 public function carga_post(){
    $data = $this->post();
    $traid = $data['traid'];

    $xsql = "Select choid,nombrechofer from transchofer where traid=$traid";
    $query = $this->db->query($xsql);
    if ($query) {
        $numr=$query->num_rows();
        if ($numr>0){
          foreach ( $query->result() as $row ) {
            $records[] = array( 'label' => $row->nombrechofer, 'value' => $row->choid );
            }
          }else{
            $records[] = array( 'label' => "", 'value' => 0 );
         }
      $respuesta = array(
        'error' => FALSE,            
        'records' => $records,
        'numr' => $numr
      );
    } else {
      $respuesta = array('error' => TRUE, "message" => $query);
    }
    $this->response( $respuesta );
  }
/************************************************************************/
  public function creapago2_post(){
    $data = $this->post();
    $dat=json_decode($data['datosm']);

    $cliid = $dat->cliid;
    $formapago = $dat->formapago;
    $fechapago = $dat->fechapago;
    $tcfdi = "P";
    $traid = $dat->traid; //$dat->traid

    $respuesta = array('error' => TRUE, "maxid"=>$traid);
    $this->response( $respuesta );

/*
    $sql="Select Max(NumFac) as maxid from trafacturas";
    $querym = $this->db->query($sql);
    $rowm = $querym->row();
    $maxid=$rowm->maxid;

    $xsql = "Select serie,ivaporcen,nombre from transportistas where traid=$traid";
    $query = $this->db->query($xsql);
    $rw = $query->row();
    $serie = $rw->serie;  
    $respuesta = array('error' => TRUE, "message" => $xsql, "maxid"=>$maxid);
    $this->response( $respuesta );
    */
  }
  public function creapago_post(){
    $data = $this->post();
    $dat=json_decode($data['datosm']);
    date_default_timezone_set('America/Chihuahua'); 
    $cliid = $dat->cliid;
    $formapago = $dat->formapago;
    $fechapago = $dat->fechapago;
    $tcfdi = "P";
    $traid = $dat->traid; //$dat->traid

    $sql="Select Max(NumFac) as maxid from trafacturas";
    $querym = $this->db->query($sql);
    $rowm = $querym->row();
    $maxid=$rowm->maxid;

    $xsql = "Select serie,ivaporcen,nombre from transportistas where traid=$traid";
    $query = $this->db->query($xsql);
    $rw = $query->row();
    $serie = $rw->serie;  
    $porceniva = $rw->ivaporcen;  
    $nombretrans = $rw->nombre;  

    $xsql = "Select nombre,rfc from emisor where emiid=$cliid";
    $query = $this->db->query($xsql);
    $rw = $query->row();
    $clirfc = $rw->rfc;  
    $facid=0;
    $fecha=date("Y-m-d");   
    $hora = date ('H:i:s');         
    $data = array(
      'fecha'=>$fecha,
      'serie'=>$serie,
      'facid'=>'SinNum',    
      'ivaporcen'=>$porceniva,            
      'cliid'=>$cliid,                    
      'empreid'=>$traid,  
  //    'carid'=>$carid,
      'Rfc'=>$clirfc,
      'Nombre'=>$nombretrans,              
  //    'MatCliId'=>$matid,
      'FormaPago'=>$formapago, 
      'FechaPago'=>$fechapago,
      'HoraPago'=>$hora,
      
      'Version'=>'3.3',
  //    'MetodoPago'=>$metpago,
      'TipoCfdi'=>$tcfdi,
  //    'UsoCfdi'=>'G03',
      'Pagada'=>'N',
      'Timbrada'=>'N',
      'PdfOnLine'=>'N',
      'XmlOnLine'=>'N',
      'TipoFac'=>$tcfdi,
  //    'TipoCp'=>$t,
  //    'Cargo'=>$precio,
  //    'Tickets'=>$descrip,
  //    'RetIvaPor'=>$porret,
  //    'SubTotal'=>$subtot, 
  //    'Iva'=>$iva,
  //    'RetIva'=>$retiva,
  //    'Total'=>$total,  
  //    'Saldo'=>$total,              
  //    'Moneda'=>$moneda,  
  //    'Atencion'=>$codigo,  
  //    'Horario'=>$unidad,  
      'Nvo'=>'W',
  //    'Ruta'      => $ruta,  
  //    'DescripMat' => $descmat,
  //    'NumCp'      => $n,  
  //    'TipoCambio'=>$tipcam,                
    );         
    $file="trafacturas";   
    if ($this->db->insert($file,$data)){
      $facid=$this->db->insert_id();
    }

    $error=true;
   // if ($facid>$maxid){
     // $xsql = "Update  set Timbrada='X', NumFac=$facid, Serie='$serie' Where fletid=$folid";
     // $query = $this->db->query($xsql);
   // }
   $respuesta = array('error' => TRUE, "message" => $query);

    if ($facid>0) {
      $xsql = "Select FacIdN from $file Where EmpreId=$traid Order by FacIdN DESC Limit 1";
      $querysig = $this->db->query($xsql);
      $rw = $querysig->row();
      $sigfol = $rw->FacIdN+1;  
      $fact_folio=$sigfol;
      $folfac=strval($sigfol);
      $space = str_repeat(' ',7-strlen($folfac));
      $fol = $space.$sigfol;
      $xsql = "update $file set FacIdN=$sigfol, facid='$fol' Where numfac=".$facid;
      $queryup = $this->db->query($xsql);
      if ($queryup){
        $respuesta = array(
          'error' => FALSE,            
          'numfac' => $facid,
          'facidn' => $sigfol,
          'folio' => $serie.$sigfol          
        );
      }  
//      $respuesta = array('error' => TRUE, "message" => $xsql);
    }
    $this->response( $respuesta );
  }

/************************************************************************/
public function detalle_post(){
  $data = $this->post();
  $dat=json_decode($data['datosm']);
  $traid = $dat->traid; 
  $cliid = $dat->cliid; 
  $numfac = $dat->numfac;
 
//  $xsql1 = "Select CONCAT(serie,trim(facid)) as folio, articid as numfac, fabid as serie, almid as facidn, fechavta as fecha, precio as total, docid as trailid from trafacturasdet where numfac=$numfac order by facidn";
  $xsql1 = "Select CONCAT(fabid,trim(almid)) as folio, articid as numfac, fabid as serie, almid as facidn, fechavta as fecha, round(precio,0) as total, docid as doctraid, pedm as pedimento, subfacid from trafacturasdet where numfac=$numfac order by facidn";
  $querydet = $this->db->query($xsql1);
  $tot = 0;
  $n = 0;
  foreach ( $querydet->result() as $row ) {
    $n=$n+1;
    $tot=$tot+$row->total;
  }

//  $xsql2 = "Select CONCAT(serie,trim(facid)) as folio, numfac, serie, facidn, fecha, total, carid as trailid from trafacturas where tipofac='I' and Timbrada='S' and Pagada='N' and empreid=$traid order by facidn";
//  $xsql2 = "Select CONCAT(a.serie,trim(a.facid)) as folio, a.numfac, a.serie, a.facidn, a.fecha, a.total, a.carid as doctraid, b.pedimento from trafacturas a, doctrailers b where a.carid=b.doctraid and tipofac='I' and Timbrada='S' and Pagada='N' and empreid=$traid order by facidn";
//  $xsql2 = "Select CONCAT(a.serie,trim(a.facid)) as folio, a.numfac, a.serie, a.facidn, a.fecha, a.total, a.carid as doctraid, b.pedimento from trafacturas a, doctrailers b where a.carid=b.doctraid and a.tipofac='I' and a.Cancel<>'S' and a.Timbrada='S' and a.Factura=0 and a.empreid=$traid and empreidtrail=$cliid order by a.facidn";
  $xsql2 = "Select CONCAT(a.serie,trim(a.facid)) as folio, a.numfac, a.serie, a.facidn, a.fecha, a.saldo, round(a.saldo,0) as cantidad, a.carid as doctraid, b.pedimento from trafacturas a, doctrailers b where a.carid=b.doctraid and a.tipofac='I' and a.Cancel<>'S' and a.Timbrada='S' and a.Factura=0 and a.empreid=$traid and a.emprefactid=$cliid order by a.facidn";
  $querypend = $this->db->query($xsql2);

  if ($querydet) {
    $respuesta = array(
      'error' => FALSE,
      'dato' => $dat,       
      'total' => $tot,
      'sql' => $xsql2,      
      'numdocs' => $n,    
      'detalle' => $querydet->result_array(),
      'pendientes' => $querypend->result_array()
    );
  } else {
    $respuesta = array('error' => TRUE, "message" => $querydet);
  }
  $this->response( $respuesta );
}  
/************************************************************************/
public function quitafact_post(){
  $data = $this->post();
  $dat=json_decode($data['datosm']);
  $numfac0 = $dat->numfac; 
  $subfacid = $data['subfacid'];
  $numfac1=$data['numfac'];
 // $cantidad=$data['cantidad'];
  
  $total=$data['total'];
  
  $respuesta = array('error' => TRUE, 'mensage' => 'Error de Actualizacion');
  $xsql = "Update trafacturas Set Factura=0 where numfac=$numfac1";
 $query = $this->db->query($xsql);
  if ($query){
    $xsql = "Update trafacturas Set pedid=pedid-1, total=total-$total where numfac=$numfac0";
    $query = $this->db->query($xsql);
    if ($query){
      $xsql = "Delete from trafacturasdet Where subfacid=$subfacid";
      $query = $this->db->query($xsql);
      if ($query){
        $xsql = "ALTER TABLE trafacturasdet AUTO_INCREMENT=1";
        $query = $this->db->query($xsql);
        $respuesta = array(
          'error' => FALSE,
          'mensage' => $xsql);
      }
    }
  } 
  $respuesta = array(
    'error' => FALSE,
    'numfac1' => $numfac1,
    'subfacid' => $subfacid,
    'numfac0' => $numfac0,    
    'mensage' => $xsql);
  $this->response( $respuesta );
}
/************************************************************************/
public function ponefact_post(){
  $data = $this->post();
  $dat=json_decode($data['datosm']);
  date_default_timezone_set('America/Chihuahua'); 
  $traid = $dat->traid; 
  $cliid = $dat->cliid; 
  $numfac0 = $dat->numfac;
  $cantidad = $data['cantidad'];
  $numfac1=$data['numfac'];
  $xsql1 = "Select CONCAT(a.serie,trim(a.facid)) as folio, a.numfac, a.serie, a.facidn, a.fecha, a.total, a.saldo, a.carid as doctraid, b.pedimento, a.uuid from trafacturas a, doctrailers b where a.carid=b.doctraid and a.numfac=$numfac1";
  $queryfact = $this->db->query($xsql1);
  $rw1 = $queryfact->row();
  $serie = $rw1->serie;
  $total = $rw1->total;
  $saldo = $rw1->saldo;
  $f1 = $rw1->fecha;
//  $d1 = strtotime($rw1->fecha);
//  $f1 = date('Y-m-d',$d1);
//  $f1 = date('d-m-Y',$fechatrail);

//  $xsql1 = "Select articid as numfac, fabid as serie, almid as facidn, fechavta as fecha, precio as total, docid as doctraid, pedm as pedimento from trafacturasdet where numfac=$numfac order by facidn";

  $xsql0 = "INSERT INTO trafacturasdet set fabid='$rw1->serie', articid=$rw1->numfac, almid=$rw1->facidn, fechavta='$f1', ";
  $xsql0 .= "numfac=$numfac0, artid='$rw1->folio', precio=$cantidad, preorig=$total, costo=$saldo, docid=$rw1->doctraid, pedm=$rw1->pedimento, ";
  $xsql0 .= "observdet='$rw1->uuid'";
  $querydet = $this->db->query($xsql0);
  if ($querydet){
    $xsql = "Update trafacturas set factura=$numfac0 Where numfac=$numfac1";
    $queryup = $this->db->query($xsql);
    if ($queryup){
      $xsql = "Update trafacturas set pedid=pedid+1, total=total+$cantidad Where numfac=$numfac0";
      $queryadd = $this->db->query($xsql);   
    }
  }

  $respuesta = array(
    'error' => FALSE,
    'cardetid' => $f1,
    'dara'  =>$data['cantidad'], 
    'mensage' => $xsql1);
  $this->response( $respuesta );

}
/************************************************************************/
public function aplicapagos_post(){
  $data = $this->post();
  $traid = $data['traid'];
  $respuesta = array('error' => TRUE, "message" => '');
  $this->response( $respuesta );
}

public function cargafact_post(){
  $data = $this->post();
  $traid = $data['traid'];
  $xsql = "Select choid,nombrechofer from transchofer where traid=$traid";
  $query = $this->db->query($xsql);
  if ($query) {
      $numr=$query->num_rows();
      if ($numr>0){
        foreach ( $query->result() as $row ) {
          $records[] = array( 'label' => $row->nombrechofer, 'value' => $row->choid );
          }
        }else{
          $records[] = array( 'label' => "", 'value' => 0 );
       }
    $respuesta = array(
      'error' => FALSE,            
      'records' => $records,
      'numr' => $numr
    );
  } else {
    $respuesta = array('error' => TRUE, "message" => $query);
  }
  $this->response( $respuesta );
}

/************************************************************************/
 public function alta_post(){
  $this->datap = $this->post();
  date_default_timezone_set('America/Chihuahua'); 
  $this->losdatos();
  $this->data += [ "nvo" => "S" ];
  $this->db->insert('transchofer',$this->data);
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
  $this->db->where( 'choid', $id );
  $hecho = $this->db->update( 'transchofer', $this->data);
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
        'empid'=>strtoupper($dat->empid),
        'nombrechofer'=>strtoupper($dat->nombrechofer)
    );
  }

}
