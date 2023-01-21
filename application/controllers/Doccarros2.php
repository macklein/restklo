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
  $campos="doccarros.doccarid, doccarros.sello, doccarros.fecha, clientes.nombre, clientesdir.descripcion, doccarros.carnum ";
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
  private function cargar_post(){
    $respuesta = array(
          'error' => FALSE,
          'piezas' => 'pruebas');
    $this->response( $respuesta );
  }
  
  private function carlgar_post(){
    $data = $this->post();
    $doccarid=$data['doccarid'];
    $doctraid=$data['doctraid'];
/*
    $xsql ="Select empreid, peso, piezas from doctrailers where doctraid=".$doctraid; 
    $query = $this->db->query($xsql);
    $row = $query->row();
    $piezas = $row->piezas;
*/
    $respuesta = array(
          'error' => FALSE,
          'piezas' => $doccarid);
    $this->response( $respuesta );
   


/*
        $pesoreal = 0;
        if ($piezas>0){
          $pesoreal = ($cantidad*$peso)/$piezas;
        }


    $traid=$this->datap['traid'];
    $cantidad=$this->datap['cantidad'];  
    $traid=$this->datap['empreid'];
    $traid=$this->datap['userid'];


    $traid=$this->datap['traid'];
  */  
    


  }


  private function pruebas(){

    // ************************************************* 
    if ($axion=="agrega"){

        $xsql="select * from doctrailers where doctraid=".$doctraid;
        $query=mysqli_query($con, $xsql);
        $row=mysqli_fetch_array($query);
        $traid = $row["TraId"];
        $empreid=$row["EmpreId"];
        $peso=intval($row["Peso"]);
        $piezas=intval($row["Piezas"]);
        $pesoreal = 0;
        if ($piezas>0){
          $pesoreal = ($cantidad*$peso)/$piezas;
        }

        $userid = $_SESSION['user_id'];
        $query=mysqli_query($con, "SELECT cardetid from doccarrosdet where doctraid=".$doctraid." and doccarid=".$doccarid);
        $row=mysqli_fetch_array($query);
        $cardetid=$row["cardetid"];
        if ($cardetid>0){
          $query=mysqli_query($con, "UPDATE doccarrosdet SET cantidad=cantidad+".$cantidad.", pesoreal=pesoreal+".$pesoreal." WHERE cardetid=".$cardetid);
        }else{

          $query=mysqli_query($con, "INSERT INTO doccarrosdet (doccarid, doctraid, traid, cantidad, empreid, userid, pesoreal, nvo) VALUES ('$doccarid', '$doctraid', '$traid', '$cantidad', '$empreid', '$userid', '$pesoreal', 'S')");

        }
        if ($query){
            if ($cantidad==$pendient){
              $sql = "UPDATE doctrailers SET descargadas=piezas, estatus='EnCar' WHERE doctraid=".$doctraid;
                    $query = mysqli_query($con,$sql);
          }else{
            $sql = "UPDATE doctrailers SET descargadas=descargadas+".$cantidad." WHERE doctraid=".$doctraid;
                    $query = mysqli_query($con,$sql);
          }

        }  

      }

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
