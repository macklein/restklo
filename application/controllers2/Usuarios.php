<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Usuarios extends REST_Controller {


  var $datap,$data,$id,$empid,$myip;

  public function __construct(){
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    header("Access-Control-Allow-Origin: *");
    parent::__construct();
    $this->load->database();

  }


 public function get_real_ip(){
   if (isset($_SERVER["HTTP_CLIENT_IP"]))
    {
        $this->myip = $_SERVER["HTTP_CLIENT_IP"];
    }
    elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
    {
        $this->myip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    elseif (isset($_SERVER["HTTP_X_FORWARDED"]))
    {
        $this->myip = $_SERVER["HTTP_X_FORWARDED"];
    }
    elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]))
    {
        $this->myip = $_SERVER["HTTP_FORWARDED_FOR"];
    }
    elseif (isset($_SERVER["HTTP_FORWARDED"]))
    {
        $this->myip = $_SERVER["HTTP_FORWARDED"];
    }
    else
    {
        $this->myip = $_SERVER["REMOTE_ADDR"];
    }
 }


private function getRealIP()
{
 
   if( $_SERVER['HTTP_X_FORWARDED_FOR'] != '' )
   {
      $client_ip = 
         ( !empty($_SERVER['REMOTE_ADDR']) ) ? 
            $_SERVER['REMOTE_ADDR'] 
            : 
            ( ( !empty($_ENV['REMOTE_ADDR']) ) ? 
               $_ENV['REMOTE_ADDR'] 
               : 
               "unknown" );
 
      // los proxys van añadiendo al final de esta cabecera
      // las direcciones ip que van "ocultando". Para localizar la ip real
      // del usuario se comienza a mirar por el principio hasta encontrar 
      // una dirección ip que no sea del rango privado. En caso de no 
      // encontrarse ninguna se toma como valor el REMOTE_ADDR
 
      $entries = preg_split('/[, ]/', $_SERVER['HTTP_X_FORWARDED_FOR']);
 
      reset($entries);
      while (list(, $entry) = each($entries)) 
      {
         $entry = trim($entry);
         if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list) )
         {
            // http://www.faqs.org/rfcs/rfc1918.html
            $private_ip = array(
                  '/^0\./', 
                  '/^127\.0\.0\.1/', 
                  '/^192\.168\..*/', 
                  '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/', 
                  '/^10\..*/');
 
            $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
 
            if ($client_ip != $found_ip)
            {
               $client_ip = $found_ip;
               break;
            }
         }
      }
   }
   else
   {
      $client_ip = 
         ( !empty($_SERVER['REMOTE_ADDR']) ) ? 
            $_SERVER['REMOTE_ADDR'] 
            : 
            ( ( !empty($_ENV['REMOTE_ADDR']) ) ? 
               $_ENV['REMOTE_ADDR'] 
               : 
               "unknown" );
   }
 
   return $client_ip;
 
}

public function getClientIP(){       
     if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)){
            return  $_SERVER["HTTP_X_FORWARDED_FOR"];  
     }else if (array_key_exists('REMOTE_ADDR', $_SERVER)) { 
            return $_SERVER["REMOTE_ADDR"]; 
     }else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            return $_SERVER["HTTP_CLIENT_IP"]; 
     } 

     return '';
}

/************************************************************************/
 public function login_post(){
    $data = $this->post();
    $email = $data['email'];
    $pass = $data['pass'];
    
    date_default_timezone_set("America/Chihuahua"); 
    $xsql = "SELECT * FROM usuarios WHERE email='".$email."' and password='".$pass."'";
    $respuesta = array("status" => "noFound");
    $query1 = $this->db->query($xsql);
    $rows = $query1->num_rows();
  
    if ($rows==1){
      $row = $query1->row();


      $this->myip="simon";
      $this->get_real_ip();
      $this->myip=$this->getClientIP();

      $localIP = getHostByName(getHostName());  //Si Funciona
      $nompc = gethostbyaddr($localIP); 
      $mytok = $nompc.$localIP;
      $mytok =  utf8_encode(crypt($mytok, "siempre el mismo password"));
    //  $token = utf8_encode(password_hash($mytok,  PASSWORD_DEFAULT));
      $fecha=date("Y-m-d H:i:s");
      

      $xsql = "INSERT INTO usersesion SET UserId=$row->user_id, UserName='$row->user_name', FechaHora='$fecha', MyToken='$mytok', Estatus='Actual', nompc='$nompc', tcpip='$localIP', nvo='S' "; //, Token='".$token."'";
      $query = $this->db->query($xsql);

// $respuesta = array('status' => $xsql, 'user' => $row->user_id, 'ip'=>$localIP);
 
      $query = $this->db->query($xsql);
      $sesid=$this->db->insert_id();
      if ($sesid>0){
        $respuesta = array(
          'error' => FALSE,           
          'status' => "Found", 
          'miip' => $this->myip,
          'serverppp' => $_SERVER,
          'env' => $_ENV,
          'items' => $query1->result_array(),
          'sesid'=>$sesid);
      } 
  } 
  $this->response( $respuesta );
}

/********************************************************************* */
 public function logout_post(){
 $data = $this->post();
 $sesid = intval($data['sesid']);
 if ($sesid<=0){
    date_default_timezone_set("America/Chihuahua"); 
    $localIP = getHostByName(getHostName());  //Si Funciona
    $nompc = gethostbyaddr($localIP); 
    $mytok = $nompc.$localIP;
    $mytok =  utf8_encode(crypt($mytok, "siempre el mismo password"));
    $date2=date("Y-m-d H:i:s");
    $xsql = "SELECT * FROM usersesion WHERE MyToken='".$mytok."' and Estatus='Actual' ";
    
    $respuesta = array("status" => "noFound");
    $query1 = $this->db->query($xsql);
    $rows = $query1->num_rows();
    if ($rows==1){
      $row1 = $query1->row();
      $date1 = $row1->FechaHora;
      $sesid = $row1->SesId;
    }
 }   
 $respuesta = array("status" => "fallo", "sesid" => $sesid);
 if ($sesid>0){
   $xsql = "UPDATE usersesion SET Estatus='Term' WHERE SesId=".$sesid;
   $query = $this->db->query($xsql);
   if ($query){
      $respuesta = array("status" => "success");
    }
  }  
  $this->response( $respuesta );
}
/********************************************************************* */
 public function sesionseek_post(){
 // $data = $this->post();
  
  date_default_timezone_set("America/Chihuahua"); 
  $localIP = getHostByName(getHostName());  //Si Funciona
  $nompc = gethostbyaddr($localIP); 
  $mytok = $nompc.$localIP;
  $mytok =  utf8_encode(crypt($mytok, "siempre el mismo password"));
  $date2=date("Y-m-d H:i:s");
  $xsql = "SELECT * FROM usersesion WHERE MyToken='".$mytok."' and Estatus='Actual' ";
  
  $respuesta = array("status" => "noFound");
  $query1 = $this->db->query($xsql);
  $rows = $query1->num_rows();
  if ($rows==1){
    $row1 = $query1->row();
    $date1 = $row1->FechaHora;
    $sesid = $row1->SesId;
    $diff="";
    $result1 = (explode(' ', $date1)) ;
    $result2 = (explode(' ', $date2)) ;
    $f1=$result1[0]." ".$result1[1];
    $f2=$result2[0]." ".$result2[1];
    $fecha1= new DateTime($f1);
    $fecha2= new DateTime($f2);
    $dif = $fecha1->diff($fecha2);
    $totalh = ($dif->y * 365.25 + $dif->m * 30 + $dif->d) * 24 + $dif->h + $dif->i/60;
    if ($totalh>10){
      $xId=$row1->SesId;
      $xsql = "UPDATE usersesion SET Estatus='Term' WHERE SesId=".$xId;
      $query1 = $this->db->query($xsql);
      $respuesta = array("status" => "Term");
    }else{
      $sesid=$row1->SesId;
      $userid=$row1->UserId;
      $xsql = "SELECT * FROM usuarios WHERE user_id=".$userid;
      $query1 = $this->db->query($xsql);
      $rows = $query1->num_rows();
      if ($rows==1){
        $row = $query1->row();
        if ($sesid>0){
           $respuesta = array(
            'error' => FALSE,           
            'status' => "Found", 
            'items' => $query1->result_array(),
            'sesid'=>$sesid);
         }   
       }
    }
  }else{
    $respuesta = array("status" => "Term");
  }
  $this->response( $respuesta );
}
/********************************************************************* */
 public function index_post(){

    $data = $this->post();
    
    $page = $data['page'];
    $buscar = $data['buscar'];
    $perPage = $data['perPage'];
    $criterio = $data['criterio'];  //No Usado

    $tables = "usuarios";
    $campos = "usuid, nombre, email, tipouser, emailreal, enviar, ctesids, transids ";
    $sWhere = "nombre LIKE '%".$buscar."%'"; 
    $order = " Order by usuid ";

    $offset = ($page - 1) * $perPage;
    $xsql="SELECT count(*) AS numrows FROM $tables WHERE $sWhere";
    
    $query = $this->db->query($xsql);
    $row = $query->row();
    $numrows = $row->numrows;
    /*
    if ($numrows>0){
       $paginas = ceil($numrows/$perPage);
    }else{
      $paginas = 1;
    }
    $xsql = "SELECT $campos FROM  $tables where $sWhere $order LIMIT $offset,$perPage";
    $query = $this->db->query($xsql);
//mysqli_error($con);
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
*/
     $respuesta = array('error' => TRUE, "message" => $xsql);
 


    $this->response( $respuesta );
  }

/************************************************************************/
 public function seleid_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
  $xsql ="Select user_id, user_name, enviar, tipouser, ctesids, email, transids, fullname, emailreal from usuarios where user_id=$id";  
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
 public function email_post(){
  $this->datap = $this->post();
  $email=$this->datap['email'];
  $xsql ="Select usuid, email from usuarios where email='$email'";  
 
  $query1 = $this->db->query($xsql);
  $rows = $query1->num_rows();
  if ($rows==0){
    $respuesta = array(
        'error' => FALSE,            
        'num' => $rows
      );
    } else {
      $respuesta = array('error' => TRUE, "num" => $rows);
    }
    
  //    $respuesta = array('error' => TRUE, "num" => $xsql);
  
    $this->response( $respuesta );
  }  
 /************************************************************************/
  public function alta_post(){
  $this->datap = $this->post();
  
  $this->losdatos();
  $fecha=date("Y-m-d H:i:s");
  $this->data += [ "fecha" => $fecha ];
  $this->data += [ "empid" => $this->empid ];  
  
  $this->data += [ "nvo" => "S" ];
  $this->db->insert('usuarios',$this->data);

  $id=$this->db->insert_id();

  if ($id>0){
      $respuesta = array(
        'error' => FALSE,
        'id' => $this->data);
    }else{
        $respuesta = array(
          'error' => TRUE);
   }
/*
      $respuesta = array(
          'error' => TRUE,
          'datap' => $this->data);
  */
  $this->response( $respuesta ); 
  }

/************************************************************************/
 public function modif_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
  $this->losdatos();
  $this->db->where( 'usuid', $id );
  $hecho = $this->db->update( 'usuarios', $this->data);
  if ($hecho){
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
  private function losdatos(){
    $dat=json_decode($this->datap['datosm']);
    $this->empid=$dat->empid;
    $this->data = array(        
        'nombre'=>strtoupper($dat->nombre),
        'ctesids'=>$dat->ctesids,
        'transids'=>$dat->transids,
        'tipouser'=>$dat->tipouser,
        'email'=>$dat->email,
        'enviar'=>$dat->enviar);
  }
 /************************************************************************/
 public function modifpas_post(){
  $this->datap = $this->post();
  $id=$this->datap['id'];
  $this->data = array('password'=>$this->datap['pass']);
  $this->db->where('usuid', $id);
  $hecho = $this->db->update( 'usuarios', $this->data);
  if ($hecho){
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

  public function enviarcarta_post(){
    $data = $this->post();
    $almid = $data['almid']; //
    $fecha=date("Y-m-d H:i:s");  
    $xsql = "UPDATE almacen SET estatus='Term', fechaenv='$fecha' WHERE almid=".$almid;
    $query = $this->db->query($xsql);
    $xrec=0;
    if ($query){
      $xrec=$almid;
    }
  
    $respuesta = array('error' => FALSE, 'almid' => $xrec);
    $this->response( $respuesta );
  
  }  

/************************************************************************/

}
