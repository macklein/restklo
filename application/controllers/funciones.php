<?php
/*INICIA FUNCIONES PARA CREAR EDITAR Y ELIMINAR ORDENES*/
	
	

	function agregar_orden(){
		global $con;
		if (empty($_POST['customer_id'])){
			$errors[] = "Debes seleccionar el cliente. ";
		} else if (empty($_POST['product_description'])){
			$errors[] = "Ingresa la descripción o el nombre del equipo. ";
		} else if (empty($_POST['issue'])) {
            $errors[] = "Ingresa el problema del equipo. ";
        } else if (
			!empty($_POST['customer_id'])
			&& !empty($_POST['product_description'])
			&& !empty($_POST['issue'])
		) {
			
			// escaping, additionally removing everything that could be (html/javascript-) code
               
				$customer_id=intval($_POST['customer_id']);
				$status=intval($_POST['status']);
				$employee_id=intval($_POST['employee_id']);
				$model = mysqli_real_escape_string($con,(strip_tags($_POST["model"],ENT_QUOTES)));
				$brand = mysqli_real_escape_string($con,(strip_tags($_POST["brand"],ENT_QUOTES)));
				$serial_number = mysqli_real_escape_string($con,(strip_tags($_POST["serial_number"],ENT_QUOTES)));
				$product_description = mysqli_real_escape_string($con,(strip_tags($_POST["product_description"],ENT_QUOTES)));
				$accessories = mysqli_real_escape_string($con,(strip_tags($_POST["accessories"],ENT_QUOTES))); 
				$issue = mysqli_real_escape_string($con,(strip_tags($_POST["issue"],ENT_QUOTES))); 
				$note = mysqli_real_escape_string($con,(strip_tags($_POST["note"],ENT_QUOTES)));
				$delivery_date = mysqli_real_escape_string($con,(strip_tags($_POST["delivery_date"],ENT_QUOTES)));
				$order_date=date("Y-m-d H:i:s");
               // write new  data into database
                    $sql = "INSERT INTO orders (order_date,delivery_date,customer_id,status,employee_id,model, brand,serial_number,product_description, accessories, issue, note)
					values ('$order_date','$delivery_date','$customer_id','$status', '$employee_id', '$model', '$brand', '$serial_number', '$product_description', '$accessories', '$issue', '$note')";
                    $query = mysqli_query($con,$sql);

                    // if  has been update successfully
                    if ($query) {
                        $messages[] = "La orden ha sido registrada con éxito.";
						
						
                    } else {
                        $errors[] = "Lo sentimos , registro falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
                
			
		}else {
			$errors[] = "Error desconocido";	
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
	}
	
	function modificar_orden(){
		global $con;
		if (empty($_POST['order_id'])){
			$errors[] = "ID de la orden vacía. ";
		} else if (empty($_POST['customer_id'])){
			$errors[] = "Debes seleccionar el cliente. ";
		} else if (empty($_POST['product_description'])){
			$errors[] = "Ingresa la descripción o el nombre del equipo. ";
		} else if (empty($_POST['issue'])) {
            $errors[] = "Ingresa el problema del equipo. ";
        } else if (
			!empty($_POST['order_id'])
			&& !empty($_POST['customer_id'])
			&& !empty($_POST['product_description'])
			&& !empty($_POST['issue'])
		) {
			
			// escaping, additionally removing everything that could be (html/javascript-) code
                $order_id=intval($_POST['order_id']);
				$customer_id=intval($_POST['customer_id']);
				$status=intval($_POST['status']);
				$employee_id=intval($_POST['employee_id']);
				$model = mysqli_real_escape_string($con,(strip_tags($_POST["model"],ENT_QUOTES)));
				$brand = mysqli_real_escape_string($con,(strip_tags($_POST["brand"],ENT_QUOTES)));
				$serial_number = mysqli_real_escape_string($con,(strip_tags($_POST["serial_number"],ENT_QUOTES)));
				$product_description = mysqli_real_escape_string($con,(strip_tags($_POST["product_description"],ENT_QUOTES)));
				$accessories = mysqli_real_escape_string($con,(strip_tags($_POST["accessories"],ENT_QUOTES))); 
				$issue = mysqli_real_escape_string($con,(strip_tags($_POST["issue"],ENT_QUOTES))); 
				$note = mysqli_real_escape_string($con,(strip_tags($_POST["note"],ENT_QUOTES)));
				$delivery_date = mysqli_real_escape_string($con,(strip_tags($_POST["delivery_date"],ENT_QUOTES)));
               // write new  data into database
                    $sql = "UPDATE orders SET delivery_date='$delivery_date', customer_id='$customer_id', status='$status', employee_id='$employee_id', model='$model', brand='$brand', serial_number='$serial_number', product_description='$product_description', accessories='$accessories', issue='$issue', note='$note'  WHERE order_id='".$order_id."'";
                    $query = mysqli_query($con,$sql);

                    // if  has been update successfully
                    if ($query) {
                        $messages[] = "La orden ha sido actualizada con éxito.";
						
						
                    } else {
                        $errors[] = "Lo sentimos , el actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
                
			
		}else {
			$errors[] = "Error desconocido";	
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
	}
	
	function eliminar_orden($order_id){
		global $con, $aviso, $classM, $times, $msj;
	if($delete=mysqli_query($con, "DELETE FROM orders WHERE order_id='".$order_id."'") and $delete2=mysqli_query($con, "DELETE FROM order_product WHERE order_id='".$order_id."'") ){
				$aviso="Bien hecho!";
				$msj="Datos eliminados satisfactoriamente.";
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
	}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR ORDENES*/



/*INICIA FUNCIONES PARA CREAR EDITAR Y ELIMINAR PRODUCTOS*/
	function modificar_producto(){
		global $con;
		$pattern = '/^\d+(?:\.\d{2})?$/';
		
		if (empty($_POST['product_id'])){
			$errors[] = "ID del producto está vacío";
		} else if (empty($_POST['product_code'])){
			$errors[] = "Código del producto está vacío";
		}else if (empty($_POST['product_name'])){
			$errors[] = "Nombre del producto está vacío";
		} else if (empty($_POST['manufacturer_id'])){
			$errors[] = "Fabricante está vacío";
		}  else if (empty($_POST['selling_price'])){
			$errors[] = "Precio de venta está vacío";
		} else if(preg_match($pattern, $_POST['selling_price']) == '0'){
			$errors[] = "Precio de venta tiene un formato inválido. Asegurate que sea un número con 2 decimales";
		}  elseif (
			!empty($_POST['product_code'])
			&& !empty($_POST['product_name'])
			&& !empty($_POST['manufacturer_id'])
			&& !empty($_POST['selling_price'])
			) {
		
			
			// escaping, additionally removing everything that could be (html/javascript-) code
				$product_id=intval($_POST['product_id']);
                $product_code = mysqli_real_escape_string($con,(strip_tags($_POST["product_code"],ENT_QUOTES)));
				$product_name = mysqli_real_escape_string($con,(strip_tags($_POST["product_name"],ENT_QUOTES)));
				$model= mysqli_real_escape_string($con,(strip_tags($_POST["model"],ENT_QUOTES)));
				$note= mysqli_real_escape_string($con,$_POST["note"]);
				$status= mysqli_real_escape_string($con,(strip_tags($_POST["status"],ENT_QUOTES)));
				$manufacturer_id= mysqli_real_escape_string($con,(strip_tags($_POST["manufacturer_id"],ENT_QUOTES)));
				$selling_price= mysqli_real_escape_string($con,(strip_tags($_POST["selling_price"],ENT_QUOTES)));
				
            
				// update data
                    $sql = "UPDATE products SET product_code='".$product_code."',model='".$model."',product_name='".$product_name."',
					note='".$note."',status='".$status."', manufacturer_id='".$manufacturer_id."', selling_price='".$selling_price."' WHERE product_id='$product_id' ";
                    $query = mysqli_query($con,$sql);

                    // if user has been update successfully
                    if ($query) {
                        $messages[] = "Los datos han sido procesados exitosamente.";
                    } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo. ".mysqli_error($con);
                    }
                
			
		} else {
			$errors[] = " Desconocido";	
		}
		if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
	}
	function eliminar_producto($id){
		global $con, $aviso, $classM, $times, $msj;
		$query_validate=mysqli_query($con,"select product_id from order_product where product_id='".$id."'");
		$count=mysqli_num_rows($query_validate);
	
	if ($count==0){
		if($delete=mysqli_query($con, "DELETE FROM products WHERE product_id='$id'") ){
				$aviso="Bien hecho!";
				$msj="Datos eliminados satisfactoriamente.";
				$classM="alert alert-success";
				$times="&times;";	
			}
			else
			{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
	} 
	else 
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El producto se encuentra vinculado al inventario";
			$classM="alert alert-error";
			$times="&times;";
		}
		
	}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR PRODUCTOS*/

/*INICIO FUNCIONES PARA CREAR EDITAR Y ELIMINAR SERVICIOS*/
	function agregar_servicio(){
		global $con;
		if (empty($_POST['cod_service'])){
			$errors[] = "El código del servicio está vacío.";
		}
	else if (empty($_POST['name_service'])){
			$errors[] = "El nombre del servicio está vacío.";
		}
	else if (empty($_POST['selling_price'])){
			$errors[] = "El precio del servicio está vacío.";
		}
	else if (
			!empty($_POST['cod_service']) && 
			!empty($_POST['name_service']) && 
			!empty($_POST['selling_price']) 
			
			)
		{
			
			// escaping, additionally removing everything that could be (html/javascript-) code
            $cod_service= mysqli_real_escape_string($con,(strip_tags($_POST["cod_service"],ENT_QUOTES)));
			$name_service= mysqli_real_escape_string($con,(strip_tags($_POST["name_service"],ENT_QUOTES)));
			$selling_price= floatval($_POST["selling_price"]);
			$status=1;
			$manufacturer_id=0;
			$buying_price=0;
			$is_service=1;
			$date_added=date("Y-m-d H:i:s");
			//Write register in to database 
			$sql = "INSERT INTO products (product_code, product_name, status, manufacturer_id, 	buying_price, selling_price, created_at, is_service) 
			VALUES('".$cod_service."','".$name_service."','".$status."','".$manufacturer_id."', '".$buying_price."', '".$selling_price."', '".$date_added."','".$is_service."');";
			$query_new = mysqli_query($con,$sql);
            // if has been added successfully
            if ($query_new) {
                $messages[] = "Servicio ha sido creado con éxito.";
            } else {
                $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo. ".mysqli_error($con);
            }
		} 
		else 
		{
			$errors[] = "desconocido.";	
		}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
	}
	function modificar_servicio(){
		global $con;
		if (empty($_POST['cod_service'])){
			$errors[] = "El código del servicio está vacío.";
		}
	else if (empty($_POST['name_service'])){
			$errors[] = "El nombre del servicio está vacío.";
		}
	else if (empty($_POST['selling_price'])){
			$errors[] = "El precio del servicio está vacío.";
		}
	else if (
			!empty($_POST['cod_service']) && 
			!empty($_POST['name_service']) && 
			!empty($_POST['selling_price']) 
			
			)
	
	{
	
	// escaping, additionally removing everything that could be (html/javascript-) code
    $cod_service= mysqli_real_escape_string($con,(strip_tags($_POST["cod_service"],ENT_QUOTES)));
	$name_service= mysqli_real_escape_string($con,(strip_tags($_POST["name_service"],ENT_QUOTES)));
	$selling_price= floatval($_POST["selling_price"]);
	$status=intval($_POST['status']);
	$id=intval($_POST['id']);
	// UPDATE data into database
    $sql = "UPDATE products SET product_code='".$cod_service."', product_name='".$name_service."', 	selling_price='".$selling_price."',  status='".$status."' WHERE 	product_id='".$id."' ";
    $query = mysqli_query($con,$sql);
    // if user has been added successfully
    if ($query) {
        $messages[] = "El servicio ha sido actualizado con éxito.";
    } else {
        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
    }
		
	} else 
	{
		$errors[] = "desconocido.";
	}
if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
	}
	function eliminar_servicio($id){
		global $con, $aviso, $classM, $times, $msj;
		$query_validate=mysqli_query($con,"select product_id from order_product where product_id='".$id."'");
		$count=mysqli_num_rows($query_validate);
	
	if ($count==0){
		if($delete=mysqli_query($con, "DELETE FROM products WHERE product_id='$id'") ){
				$aviso="Bien hecho!";
				$msj="Datos eliminados satisfactoriamente.";
				$classM="alert alert-success";
				$times="&times;";	
			}
			else
			{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
	} 
	else 
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El producto se encuentra vinculado al inventario";
			$classM="alert alert-error";
			$times="&times;";
		}
	}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR SERVICIOS*/


/*INICIA FUNCIONES PARA CREAR EDITAR Y ELIMINAR FABRICANTES*/
function agregar_fabricante(){
	global $con;
	if (empty($_POST['name'])){
			$errors[] = "Nombre del fabricante está vacío.";
		} elseif (!empty($_POST['name'])){
			
			// escaping, additionally removing everything that could be (html/javascript-) code
            $name = mysqli_real_escape_string($con,(strip_tags($_POST["name"],ENT_QUOTES)));
			$date_added=date("Y-m-d H:i:s");
			//Write register in to database 
			$sql = "INSERT INTO manufacturers (name, date_added) VALUES('".$name."','".$date_added."');";
			$query_new = mysqli_query($con,$sql);
            // if has been added successfully
            if ($query_new) {
                $messages[] = "Fabricante ha sido creado con &eacute;xito.";
            } else {
                $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
            }
		} else 
		{
			$errors[] = "desconocido.";	
		}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}

function modificar_fabricante(){
	global $con;
	if (empty($_POST['name'])){
			$errors[] = "Nombre del fabricante está vacío.";
	} elseif (!empty($_POST['name'])){
	
	// escaping, additionally removing everything that could be (html/javascript-) code
    $name = mysqli_real_escape_string($con,(strip_tags($_POST["name"],ENT_QUOTES)));
	$status=intval($_POST['status']);
	$id=intval($_POST['id']);
	// UPDATE data into database
    $sql = "UPDATE manufacturers SET name='".$name."',  status='".$status."' WHERE id='".$id."' ";
    $query = mysqli_query($con,$sql);
    // if user has been added successfully
    if ($query) {
        $messages[] = "El fabricante ha sido actualizado con &eacute;xito.";
    } else {
        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
    }
		
	} else 
	{
		$errors[] = "desconocido.";
	}
	if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}

function eliminar_fabricante($id){
	global $con, $aviso, $classM, $times, $msj;
	$query_validate=mysqli_query($con,"select manufacturer_id from products where manufacturer_id='".$id."'");
	$count=mysqli_num_rows($query_validate);
	if ($count==0){
			if($delete=mysqli_query($con, "DELETE FROM manufacturers WHERE id='$id'")){
				$aviso="Bien hecho!";
				$msj="Datos eliminados satisfactoriamente.";
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
	}
	else 
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El fabricante se encuentra vinculado con un producto";
			$classM="alert alert-danger";
			$times="&times;";
		}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR FABRICANTES*/

/*INICIA FUNCIONES PARA CREAR EDITAR Y ELIMINAR CLIENTES*/

function agregar_cliente(){
	global $con; 
	if (empty($_POST['nombre'])){
			$errors[] = "Nombres vacíos";
		 } else{
						
			// escaping, additionally removing everything that could be (html/javascript-) code
                $nombre = mysqli_real_escape_string($con,(strip_tags($_POST["nombre"],ENT_QUOTES)));
				$direccion = mysqli_real_escape_string($con,(strip_tags($_POST["direccion"],ENT_QUOTES)));
                $rfc = mysqli_real_escape_string($con,(strip_tags($_POST["rfc"],ENT_QUOTES)));
				$telefono1 = mysqli_real_escape_string($con,(strip_tags($_POST["telefono1"],ENT_QUOTES)));
				$telefono2 = mysqli_real_escape_string($con,(strip_tags($_POST["telefono2"],ENT_QUOTES)));
				$contacto1 = mysqli_real_escape_string($con,(strip_tags($_POST["contacto1"],ENT_QUOTES)));
				$email	 = mysqli_real_escape_string($con,(strip_tags($_POST["email"],ENT_QUOTES)));
				$empreid	 = mysqli_real_escape_string($con,(strip_tags($_POST["empreid"],ENT_QUOTES)));
				$created_at=date("Y-m-d H:i:s");
               
				
					// write new  data into database
                    $sql = "INSERT INTO clientes(nombre, direccion, rfc, telefono1, telefono2, contacto1, email, empreid) VALUES('$nombre','$direccion','$rfc','$telefono1','$telefono2', '$contacto1','$email', '$empreid');";
                    $query = mysqli_query($con,$sql);

                    // if has been added successfully
                    if ($query) {
                        $messages[] = "El cliente ha sido creado con éxito.";
						$last=mysqli_query($con,"select LAST_INSERT_ID(cliid) as last from clientes order by cliid desc limit 0,1 ");
						$rw=mysqli_fetch_array($last);
						$cliid=$rw['last'];
			         } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_cliente(){
	global $con;
	if (empty($_POST['cliid'])){
			$errors[] = "ID del cliente vacío";
		} else {
			
			// escaping, additionally removing everything that could be (html/javascript-) code
                $nombre = mysqli_real_escape_string($con,(strip_tags($_POST["nombre"],ENT_QUOTES)));
				$direccion = mysqli_real_escape_string($con,(strip_tags($_POST["direccion"],ENT_QUOTES)));
                $rfc = mysqli_real_escape_string($con,(strip_tags($_POST["rfc"],ENT_QUOTES)));
				$telefono1 = mysqli_real_escape_string($con,(strip_tags($_POST["telefono1"],ENT_QUOTES)));
				$telefono2 = mysqli_real_escape_string($con,(strip_tags($_POST["telefono2"],ENT_QUOTES)));
				$contacto1 = mysqli_real_escape_string($con,(strip_tags($_POST["contacto1"],ENT_QUOTES)));
				$email	 = mysqli_real_escape_string($con,(strip_tags($_POST["email"],ENT_QUOTES)));
				$empreid	 = mysqli_real_escape_string($con,(strip_tags($_POST["empreid"],ENT_QUOTES)));
$cmes 	= mysqli_real_escape_string($con,(strip_tags($_POST["cmes"],ENT_QUOTES)));
$ctrail = mysqli_real_escape_string($con,(strip_tags($_POST["ctrail"],ENT_QUOTES)));
$ccarro = mysqli_real_escape_string($con,(strip_tags($_POST["ccarro"],ENT_QUOTES)));
$crosdo = mysqli_real_escape_string($con,(strip_tags($_POST["crosdo"],ENT_QUOTES)));
//$crosde = mysqli_real_escape_string($con,(strip_tags($_POST["crosde"],ENT_QUOTES)));
$ccarcre = mysqli_real_escape_string($con,(strip_tags($_POST["ccarcre"],ENT_QUOTES)));
$ccarcra = mysqli_real_escape_string($con,(strip_tags($_POST["ccarcra"],ENT_QUOTES)));

$crecon = mysqli_real_escape_string($con,(strip_tags($_POST["crecon"],ENT_QUOTES)));
$creaco = mysqli_real_escape_string($con,(strip_tags($_POST["creaco"],ENT_QUOTES)));
$ccaral = mysqli_real_escape_string($con,(strip_tags($_POST["ccaral"],ENT_QUOTES)));
$centra = mysqli_real_escape_string($con,(strip_tags($_POST["centra"],ENT_QUOTES)));
$calmac = mysqli_real_escape_string($con,(strip_tags($_POST["calmac"],ENT_QUOTES)));


				$created_at=date("Y-m-d H:i:s");
               
				$cliid=intval($_POST['cliid']);//ID del cliente
				
					// write new  data into database
                    $sql = "UPDATE clientes SET nombre='$nombre', rfc='$rfc', direccion='$direccion', telefono1='$telefono1', telefono2='$telefono2', empreid='$empreid', contacto1='$contacto1', email='$email', cargomes='$cmes', cargotrailer='$ctrail', cargocarro='$ccarro', cargocrossdock='$crosdo', cargocartacrosemb='$ccarcre', cargocartacrosalm='$ccarcra', cargoreconocim='$crecon', cargoreacomodo='$creaco', cargocartaalm='$ccaral', cargoentrada='$centra', cargoalmacen='$calmac' WHERE cliid='".$cliid."'";
                    $query = mysqli_query($con,$sql);

                    // if  has been update successfully
                    if ($query) {
                        $messages[] = "El cliente ha sido actualizado con éxito.";
						
						
                    } else {
                        $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
              		
		}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function eliminar_cliente($id){
	global $con, $aviso, $classM, $times, $msj;
//	$query_validate=mysqli_query($con,"select cliid from orders where customer_id='".$id."'");
//	$count=mysqli_num_rows($query_validate);
		$count=0;
		if ($count==0)
		{
			if($delete=mysqli_query($con, "DELETE FROM clientes WHERE cliid='$id'") ){
				//and $delete2=mysqli_query($con,"DELETE FROM contacts WHERE client_id='$id' ")){
				$aviso="Registro Eliminado!";
				$msj="Datos eliminados satisfactoriamente.";
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El cliente se encuentra vinculado al modulo de ventas";
			$classM="alert alert-danger";
			$times="&times;";
		}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR CLIENTES*/


/*****************************************************************************/
/*        CLIENTESMAT                       */
function agregar_clientemat(){
	global $con; 
	if (empty($_POST['descrip'])){
			$errors[] = "Descripcion vacia";
		 } else{
						
			// escaping, additionally removing everything that could be (html/javascript-) code
                $cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
				$descrip = mysqli_real_escape_string($con,(strip_tags($_POST["descrip"],ENT_QUOTES)));
                $umed = mysqli_real_escape_string($con,(strip_tags($_POST["umed"],ENT_QUOTES)));
				$upes = mysqli_real_escape_string($con,(strip_tags($_POST["upes"],ENT_QUOTES)));
		$descrip1 = mysqli_real_escape_string($con,(strip_tags($_POST["descrip1"],ENT_QUOTES)));
		$descrip2 = mysqli_real_escape_string($con,(strip_tags($_POST["descrip2"],ENT_QUOTES)));
		$descrip3 = mysqli_real_escape_string($con,(strip_tags($_POST["descrip3"],ENT_QUOTES)));
		$descrip4 = mysqli_real_escape_string($con,(strip_tags($_POST["descrip4"],ENT_QUOTES)));

				$created_at=date("Y-m-d H:i:s");
               
				
					// write new  data into database
                    $sql = "INSERT INTO clientesmat(cliid, descripcion, unidadmed, unidadpes, descrip1, descrip2, descrip3, descrip4) VALUES('$cliid','$descrip','$umed','$upes', '$descrip1', '$descrip2', '$descrip3', '$descrip4');";
                    $query = mysqli_query($con,$sql);

                    // if has been added successfully
                    if ($query) {
                        $messages[] = "El Material del Cliente ha sido creado con éxito.";
					//	$last=mysqli_query($con,"select LAST_INSERT_ID(cliid) as last from clientes order by cliid desc limit 0,1 ");
					//	$rw=mysqli_fetch_array($last);
					//	$cliid=$rw['last'];
			         } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_clientemat(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID del material de cliente vacío";
		} else {
			
			// escaping, additionally removing everything that could be (html/javascript-) code
                $cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
				$descrip = mysqli_real_escape_string($con,(strip_tags($_POST["descrip"],ENT_QUOTES)));
                $umed = mysqli_real_escape_string($con,(strip_tags($_POST["umed"],ENT_QUOTES)));
				$upes = mysqli_real_escape_string($con,(strip_tags($_POST["upes"],ENT_QUOTES)));
		$descrip1 = mysqli_real_escape_string($con,(strip_tags($_POST["descrip1"],ENT_QUOTES)));
		$descrip2 = mysqli_real_escape_string($con,(strip_tags($_POST["descrip2"],ENT_QUOTES)));
		$descrip3 = mysqli_real_escape_string($con,(strip_tags($_POST["descrip3"],ENT_QUOTES)));
		$descrip4 = mysqli_real_escape_string($con,(strip_tags($_POST["descrip4"],ENT_QUOTES)));
		$capcant = mysqli_real_escape_string($con,(strip_tags($_POST["capcant"],ENT_QUOTES)));

				$created_at=date("Y-m-d H:i:s");
               
				$matcliid=intval($_POST['id']);//ID del cliente
				
					// write new  data into database
                    $sql = "UPDATE clientesmat SET cliid='$cliid', descripcion='$descrip', unidadmed='$umed', unidadpes='$upes', descrip1='$descrip1', descrip2='$descrip2', descrip3='$descrip3', descrip4='$descrip4', capcantidad='$capcant' WHERE matcliid='".$matcliid."'";
                    $query = mysqli_query($con,$sql);

                    // if  has been update successfully
                    if ($query) {
                        $messages[] = "El Material del cliente ha sido actualizado con éxito.";
						
						
                    } else {
                        $errors[] = "Lo sentimos , el actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
              		
		}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Actualizado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function eliminar_clientemat($id){
	global $con, $aviso, $classM, $times, $msj;
//	$query_validate=mysqli_query($con,"select cliid from orders where customer_id='".$id."'");
//	$count=mysqli_num_rows($query_validate);
		$count=0;
		if ($count==0)
		{
			if($delete=mysqli_query($con, "DELETE FROM clientesmat WHERE matcliid='$id'") ){
				//and $delete2=mysqli_query($con,"DELETE FROM contacts WHERE client_id='$id' ")){
				$aviso="Registro Eliminado!";
				$msj="Datos eliminados satisfactoriamente.";
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El Registro se encuentra vinculado al modulo de ventas";
			$classM="alert alert-danger";
			$times="&times;";
		}
}






/*****************************************************************************/
/*        CLIENTESDIR                       */
function agregar_clientedir(){
	global $con; 
	if (empty($_POST['descrip'])){
			$errors[] = "Descripcion vacia";
		 } else{
						
			// escaping, additionally removing everything that could be (html/javascript-) code
                $cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
				$descrip = mysqli_real_escape_string($con,(strip_tags($_POST["descrip"],ENT_QUOTES)));
               	$created_at=date("Y-m-d H:i:s");
               
				
					// write new  data into database
                    $sql = "INSERT INTO clientesdir(cliid, descripcion) VALUES('$cliid','$descrip');";
                    $query = mysqli_query($con,$sql);

                    // if has been added successfully
                    if ($query) {
                        $messages[] = "La Direccion del Cliente ha sido creada con éxito.";
					//	$last=mysqli_query($con,"select LAST_INSERT_ID(cliid) as last from clientes order by cliid desc limit 0,1 ");
					//	$rw=mysqli_fetch_array($last);
					//	$cliid=$rw['last'];
			         } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_clientedir(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID de la Direccion de cliente vacío";
		} else {
			
			// escaping, additionally removing everything that could be (html/javascript-) code
                $cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
				$descrip = mysqli_real_escape_string($con,(strip_tags($_POST["descrip"],ENT_QUOTES)));
                $created_at=date("Y-m-d H:i:s");
               
				$dircliid=intval($_POST['id']);//ID del cliente
				
					// write new  data into database
                    $sql = "UPDATE clientesdir SET cliid='$cliid', descripcion='$descrip' WHERE dircliid='".$dircliid."'";
                    $query = mysqli_query($con,$sql);

                    // if  has been update successfully
                    if ($query) {
                        $messages[] = "La Direccion del cliente ha sido actualizada con éxito.";
						
						
                    } else {
                        $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
              		
		}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Actualizado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function eliminar_clientedir($id){
	global $con, $aviso, $classM, $times, $msj;
//	$query_validate=mysqli_query($con,"select cliid from orders where customer_id='".$id."'");
//	$count=mysqli_num_rows($query_validate);
		$count=0;
		if ($count==0)
		{
			if($delete=mysqli_query($con, "DELETE FROM clientesdir WHERE dircliid='$id'") ){
				//and $delete2=mysqli_query($con,"DELETE FROM contacts WHERE client_id='$id' ")){
				$aviso="Registro Eliminado!";
				$msj="Datos eliminados satisfactoriamente.";
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El Registro se encuentra vinculado al modulo de ventas";
			$classM="alert alert-danger";
			$times="&times;";
		}
}


/*****************************************************************************/

/*****************************************************************************/
/*        CLIENTESSER                       */
function agregar_clienteser(){
	global $con; 
	if (empty($_POST['descrip'])){
			$errors[] = "Descripcion vacia";
		 } else{
						
			// escaping, additionally removing everything that could be (html/javascript-) code
                $cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
				$descrip = mysqli_real_escape_string($con,(strip_tags($_POST["descrip"],ENT_QUOTES)));
				$precio = mysqli_real_escape_string($con,(strip_tags($_POST["precio"],ENT_QUOTES)));
               	$created_at=date("Y-m-d H:i:s");
               
				
					// write new  data into database
              //      $sql = "INSERT INTO clientesser(cliid, descripcion, precio) VALUES('$cliid','$descrip','precio');";


					$sql = "INSERT INTO clientesser(cliid, descripcion, precio) 
					VALUES('".$cliid."','".$descrip."','".$precio."');";


                    $query = mysqli_query($con,$sql);

                    // if has been added successfully
                    if ($query) {
                        $messages[] = "El Servicio del Cliente ha sido creada con éxito.";
					//	$last=mysqli_query($con,"select LAST_INSERT_ID(cliid) as last from clientes order by cliid desc limit 0,1 ");
					//	$rw=mysqli_fetch_array($last);
					//	$cliid=$rw['last'];
			         } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_clienteser(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID del Servicio de cliente vacío";
		} else {
			
			// escaping, additionally removing everything that could be (html/javascript-) code
                $cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
				$descrip = mysqli_real_escape_string($con,(strip_tags($_POST["descrip"],ENT_QUOTES)));
				$precio = mysqli_real_escape_string($con,(strip_tags($_POST["precio"],ENT_QUOTES)));
                $created_at=date("Y-m-d H:i:s");
               
				$id=intval($_POST['id']);//ID del cliente
				
					// write new  data into database
                //    $sql = "UPDATE clientesser SET cliid='$cliid', descripcion='$descrip', precio='".$precio."' WHERE sercliid='".$id."'";
                    


  					$sql = "UPDATE clientesser SET cliid='".$cliid."',descripcion='".$descrip."',precio='".$precio."' WHERE sercliid='$id' ";

					$query = mysqli_query($con,$sql);


                    // if  has been update successfully
                    if ($query) {
                        $messages[] = "El Servicio del cliente ha sido actualizada con éxito.";
						
						
                    } else {
                        $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
              		
		}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Actualizado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function eliminar_clienteser($id){
	global $con, $aviso, $classM, $times, $msj;
//	$query_validate=mysqli_query($con,"select cliid from orders where customer_id='".$id."'");
//	$count=mysqli_num_rows($query_validate);
		$count=0;
		if ($count==0)
		{
			if($delete=mysqli_query($con, "DELETE FROM clientesser WHERE sercliid='$id'") ){
				//and $delete2=mysqli_query($con,"DELETE FROM contacts WHERE client_id='$id' ")){
				$aviso="Registro Eliminado!";
				$msj="Datos eliminados satisfactoriamente.";
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El Registro se encuentra vinculado al modulo de ventas";
			$classM="alert alert-danger";
			$times="&times;";
		}
}


/*****************************************************************************/


/*****************************************************************************/
function agregar_transportista(){
	global $con; 
	if (empty($_POST['nombre'])){
			$errors[] = "Nombres vacíos";
		 } else{
						
			// escaping, additionally removing everything that could be (html/javascript-) code
                $nombre = mysqli_real_escape_string($con,(strip_tags($_POST["nombre"],ENT_QUOTES)));
				$direccion = mysqli_real_escape_string($con,(strip_tags($_POST["direccion"],ENT_QUOTES)));
                $rfc = mysqli_real_escape_string($con,(strip_tags($_POST["rfc"],ENT_QUOTES)));
				$telefono1 = mysqli_real_escape_string($con,(strip_tags($_POST["telefono1"],ENT_QUOTES)));
				$telefono2 = mysqli_real_escape_string($con,(strip_tags($_POST["telefono2"],ENT_QUOTES)));
				$contacto1 = mysqli_real_escape_string($con,(strip_tags($_POST["contacto1"],ENT_QUOTES)));
				$email	 = mysqli_real_escape_string($con,(strip_tags($_POST["email"],ENT_QUOTES)));
				$created_at=date("Y-m-d H:i:s");
               
				
					// write new  data into database
                    $sql = "INSERT INTO transportistas(nombre, direccion, rfc, telefono1, telefono2, contacto1, email) VALUES('$nombre','$direccion','$rfc','$telefono1','$telefono2', '$contacto1','$email');";
                    $query = mysqli_query($con,$sql);

                    // if has been added successfully
                    if ($query) {
                        $messages[] = "El registro ha sido creado con éxito.";
//						$last=mysqli_query($con,"select LAST_INSERT_ID(cliid) as last from clientes order by cliid desc limit 0,1 ");
//						$rw=mysqli_fetch_array($last);
//						$cliid=$rw['last'];
			         } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_transportista(){
	global $con;
	if (empty($_POST['traid'])){
			$errors[] = "ID del Registro vacío";
		} else {
			
			// escaping, additionally removing everything that could be (html/javascript-) code
                $nombre = mysqli_real_escape_string($con,(strip_tags($_POST["nombre"],ENT_QUOTES)));
				$direccion = mysqli_real_escape_string($con,(strip_tags($_POST["direccion"],ENT_QUOTES)));
                $rfc = mysqli_real_escape_string($con,(strip_tags($_POST["rfc"],ENT_QUOTES)));
				$telefono1 = mysqli_real_escape_string($con,(strip_tags($_POST["telefono1"],ENT_QUOTES)));
				$telefono2 = mysqli_real_escape_string($con,(strip_tags($_POST["telefono2"],ENT_QUOTES)));
				$contacto1 = mysqli_real_escape_string($con,(strip_tags($_POST["contacto1"],ENT_QUOTES)));
				$email	 = mysqli_real_escape_string($con,(strip_tags($_POST["email"],ENT_QUOTES)));
				$precio	 = mysqli_real_escape_string($con,(strip_tags($_POST["precio"],ENT_QUOTES)));
				
				$created_at=date("Y-m-d H:i:s");
               
				$traid=intval($_POST['traid']);//ID del cliente
				
					// write new  data into database
                    $sql = "UPDATE transportistas SET nombre='$nombre', rfc='$rfc', direccion='$direccion', telefono1='$telefono1', telefono2='$telefono2', contacto1='$contacto1', email='$email', precio='$precio' WHERE traid='".$traid."'";
                    $query = mysqli_query($con,$sql);

                    // if  has been update successfully
                    if ($query) {
                        $messages[] = "El Registro ha sido actualizado con éxito.";
						
						
                    } else {
                        $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
              		
		}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function eliminar_transportista($id){
	global $con, $aviso, $classM, $times, $msj;
//	$query_validate=mysqli_query($con,"select cliid from orders where customer_id='".$id."'");
//	$count=mysqli_num_rows($query_validate);
		$count=0;
		if ($count==0)
		{
			if($delete=mysqli_query($con, "DELETE FROM transportistas WHERE traid='$id'") ){
				//and $delete2=mysqli_query($con,"DELETE FROM contacts WHERE client_id='$id' ")){
				$aviso="Registro Eliminado!";
				$msj="Datos eliminados satisfactoriamente.";
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El cliente se encuentra vinculado al modulo de ventas";
			$classM="alert alert-danger";
			$times="&times;";
		}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR TRANSPORTISTA*/


/*****************************************************************************/


/*****************************************************************************/
function agregar_transchofer(){
	global $con; 
	if (empty($_POST['chofer'])){
			$errors[] = "Nombre vacíos";
		 } else{
						
			// escaping, additionally removing everything that could be (html/javascript-) code
                $traid = mysqli_real_escape_string($con,(strip_tags($_POST["traid"],ENT_QUOTES)));
				$nombrechofer = mysqli_real_escape_string($con,(strip_tags($_POST["chofer"],ENT_QUOTES)));
              	
					// write new  data into database
                    $sql = "INSERT INTO transchofer(traid, nombrechofer) VALUES('$traid','$nombrechofer');";
                    $query = mysqli_query($con,$sql);

                    // if has been added successfully
                    if ($query) {
                        $messages[] = "El registro ha sido creado con éxito.";
//						$last=mysqli_query($con,"select LAST_INSERT_ID(cliid) as last from clientes order by cliid desc limit 0,1 ");
//						$rw=mysqli_fetch_array($last);
//						$cliid=$rw['last'];
			         } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_transchofer(){
	global $con;
	if (empty($_POST['traid'])){
			$errors[] = "ID del Registro vacío";
		} else {
			
			// escaping, additionally removing everything that could be (html/javascript-) code
                $traid = mysqli_real_escape_string($con,(strip_tags($_POST["traid"],ENT_QUOTES)));
				$nombrechofer = mysqli_real_escape_string($con,(strip_tags($_POST["chofer"],ENT_QUOTES)));
            	$empid = mysqli_real_escape_string($con,(strip_tags($_POST["empid"],ENT_QUOTES)));
               
				$choid=intval($_POST['id']);//ID del cliente
				
					// write new  data into database
                    $sql = "UPDATE transchofer SET traid='$traid', empid='$empid', nombrechofer='$nombrechofer' WHERE choid='".$choid."'";
                    $query = mysqli_query($con,$sql);

                    // if  has been update successfully
                    if ($query) {
                        $messages[] = "El Registro ha sido actualizado con éxito.";
						
						
                    } else {
                        $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
              		
		}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function eliminar_transchofer($id){
	global $con, $aviso, $classM, $times, $msj;
//	$query_validate=mysqli_query($con,"select cliid from orders where customer_id='".$id."'");
//	$count=mysqli_num_rows($query_validate);
		$count=0;
		if ($count==0)
		{
			if($delete=mysqli_query($con, "DELETE FROM transchofer WHERE choid='$id'") ){
				//and $delete2=mysqli_query($con,"DELETE FROM contacts WHERE client_id='$id' ")){
				$aviso="Registro Eliminado!";
				$msj="Datos eliminados satisfactoriamente.";
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El Registro se encuentra vinculado al modulo de ventas";
			$classM="alert alert-danger";
			$times="&times;";
		}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR TRANSCHOFER*/


/*****************************************************************************/


/*****************************************************************************/
function agregar_transplacas(){
	global $con; 
	if (empty($_POST['placas'])){
			$errors[] = "Placas vacías";
		 } else{
						
			// escaping, additionally removing everything that could be (html/javascript-) code
                $traid = mysqli_real_escape_string($con,(strip_tags($_POST["traid"],ENT_QUOTES)));
				$placas = mysqli_real_escape_string($con,(strip_tags($_POST["placas"],ENT_QUOTES)));
              	
					// write new  data into database
                    $sql = "INSERT INTO transplacas(traid, placas) VALUES('$traid','$placas');";
                    $query = mysqli_query($con,$sql);

                    // if has been added successfully
                    if ($query) {
                        $messages[] = "El registro ha sido creado con éxito.";
//						$last=mysqli_query($con,"select LAST_INSERT_ID(cliid) as last from clientes order by cliid desc limit 0,1 ");
//						$rw=mysqli_fetch_array($last);
//						$cliid=$rw['last'];
			         } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_transplacas(){
	global $con;
	if (empty($_POST['placas'])){
			$errors[] = "datos del Registro vacío";
		} else {
			
			// escaping, additionally removing everything that could be (html/javascript-) code
                $traid = mysqli_real_escape_string($con,(strip_tags($_POST["traid"],ENT_QUOTES)));
				$placas = mysqli_real_escape_string($con,(strip_tags($_POST["placas"],ENT_QUOTES)));
               
				$plaid=intval($_POST['id']);//ID del cliente
				
					// write new  data into database
                    $sql = "UPDATE transplacas SET traid='$traid', placas='$placas' WHERE plaid='".$plaid."'";
                    $query = mysqli_query($con,$sql);

                    // if  has been update successfully
                    if ($query) {
                        $messages[] = "El Registro ha sido actualizado con éxito.";
						
						
                    } else {
                        $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
              		
		}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}


/*****************************************************************************/
function agregar_transpre(){
	global $con; 
	if (empty($_POST['tipomov'])){
			$errors[] = "Tipo Movimiento vacío";
		 } else{
						
			// escaping, additionally removing everything that could be (html/javascript-) code
            $traid = mysqli_real_escape_string($con,(strip_tags($_POST["traid"],ENT_QUOTES)));
			$tipomov = mysqli_real_escape_string($con,(strip_tags($_POST["tipomov"],ENT_QUOTES)));
			$cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
			$precio = mysqli_real_escape_string($con,(strip_tags($_POST["precio"],ENT_QUOTES)));
				
              	
					// write new  data into database
                    $sql = "INSERT INTO transpre(traid, tipomov, precio, cliid) VALUES('$traid','$tipomov', '$precio', '$cliid');";
                    $query = mysqli_query($con,$sql);

                    // if has been added successfully
                    if ($query) {
                        $messages[] = "El registro ha sido creado con éxito.";
//						$last=mysqli_query($con,"select LAST_INSERT_ID(cliid) as last from clientes order by cliid desc limit 0,1 ");
//						$rw=mysqli_fetch_array($last);
//						$cliid=$rw['last'];
			         } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_transpre(){
	global $con;
	if (empty($_POST['precio'])){
			$errors[] = "datos del Registro vacío";
		} else {
			
			// escaping, additionally removing everything that could be (html/javascript-) code
                $traid = mysqli_real_escape_string($con,(strip_tags($_POST["traid"],ENT_QUOTES)));
				$cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
				$tipomov = mysqli_real_escape_string($con,(strip_tags($_POST["tipomov"],ENT_QUOTES)));
				$precio = mysqli_real_escape_string($con,(strip_tags($_POST["precio"],ENT_QUOTES)));

				$id=intval($_POST['id']);//ID del cliente
				
					// write new  data into database
                    $sql = "UPDATE transpre SET traid='$traid', cliid='$cliid', tipomov='$tipomov', precio='$precio' WHERE trapreid='".$id."'";
                    $query = mysqli_query($con,$sql);

                    // if  has been update successfully
                    if ($query) {
                        $messages[] = "El Registro ha sido actualizado con éxito.";
						
						
                    } else {
                        $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
              		
		}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}



function eliminar_transplacas($id){
	global $con, $aviso, $classM, $times, $msj;
//	$query_validate=mysqli_query($con,"select cliid from orders where customer_id='".$id."'");
//	$count=mysqli_num_rows($query_validate);
		$count=0;
		if ($count==0)
		{
			if($delete=mysqli_query($con, "DELETE FROM transplacas WHERE plaid='$id'") ){
				//and $delete2=mysqli_query($con,"DELETE FROM contacts WHERE client_id='$id' ")){
				$aviso="Registro Eliminado!";
				$msj="Datos eliminados satisfactoriamente.";
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El Registro se encuentra vinculado al modulo de ventas";
			$classM="alert alert-danger";
			$times="&times;";
		}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR TRANSPLACAS*/

/*****************************************************************************/


/*****************************************************************************/
function agregar_proveedores(){
	global $con; 
	if (empty($_POST['nombre'])){
			$errors[] = "Nombres vacíos";
		 } else{
						
			// escaping, additionally removing everything that could be (html/javascript-) code
                $nombre = mysqli_real_escape_string($con,(strip_tags($_POST["nombre"],ENT_QUOTES)));
				$direccion = mysqli_real_escape_string($con,(strip_tags($_POST["direccion"],ENT_QUOTES)));
                $rfc = mysqli_real_escape_string($con,(strip_tags($_POST["rfc"],ENT_QUOTES)));
				$telefono1 = mysqli_real_escape_string($con,(strip_tags($_POST["telefono1"],ENT_QUOTES)));
				$telefono2 = mysqli_real_escape_string($con,(strip_tags($_POST["telefono2"],ENT_QUOTES)));
				$contacto1 = mysqli_real_escape_string($con,(strip_tags($_POST["contacto1"],ENT_QUOTES)));
				$email	 = mysqli_real_escape_string($con,(strip_tags($_POST["email"],ENT_QUOTES)));
				$created_at=date("Y-m-d H:i:s");
               
				
					// write new  data into database
                    $sql = "INSERT INTO proveedores(nombre, direccion, rfc, telefono1, telefono2, contacto1, email) VALUES('$nombre','$direccion','$rfc','$telefono1','$telefono2', '$contacto1','$email');";
                    $query = mysqli_query($con,$sql);

                    // if has been added successfully
                    if ($query) {
                        $messages[] = "El registro ha sido creado con éxito.";
//						$last=mysqli_query($con,"select LAST_INSERT_ID(cliid) as last from clientes order by cliid desc limit 0,1 ");
//						$rw=mysqli_fetch_array($last);
//						$cliid=$rw['last'];
			         } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_proveedores(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID del Registro vacío";
		} else {
			
			// escaping, additionally removing everything that could be (html/javascript-) code
                $nombre = mysqli_real_escape_string($con,(strip_tags($_POST["nombre"],ENT_QUOTES)));
				$direccion = mysqli_real_escape_string($con,(strip_tags($_POST["direccion"],ENT_QUOTES)));
                $rfc = mysqli_real_escape_string($con,(strip_tags($_POST["rfc"],ENT_QUOTES)));
				$telefono1 = mysqli_real_escape_string($con,(strip_tags($_POST["telefono1"],ENT_QUOTES)));
				$telefono2 = mysqli_real_escape_string($con,(strip_tags($_POST["telefono2"],ENT_QUOTES)));
				$contacto1 = mysqli_real_escape_string($con,(strip_tags($_POST["contacto1"],ENT_QUOTES)));
				$email	 = mysqli_real_escape_string($con,(strip_tags($_POST["email"],ENT_QUOTES)));
				
				
				$created_at=date("Y-m-d H:i:s");
               
				$id=intval($_POST['id']);//ID del cliente
				
					// write new  data into database
                    $sql = "UPDATE proveedores SET nombre='$nombre', rfc='$rfc', direccion='$direccion', telefono1='$telefono1', telefono2='$telefono2', contacto1='$contacto1', email='$email' WHERE proid='".$id."'";
                    $query = mysqli_query($con,$sql);

                    // if  has been update successfully
                    if ($query) {
                        $messages[] = "El Registro ha sido actualizado con éxito.";
						
						
                    } else {
                        $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
              		
		}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function eliminar_proveedores($id){
	global $con, $aviso, $classM, $times, $msj;
//	$query_validate=mysqli_query($con,"select cliid from orders where customer_id='".$id."'");
//	$count=mysqli_num_rows($query_validate);
		$count=0;
		if ($count==0)
		{
			if($delete=mysqli_query($con, "DELETE FROM proveedores WHERE proid='$id'") ){
				//and $delete2=mysqli_query($con,"DELETE FROM contacts WHERE client_id='$id' ")){
				$aviso="Registro Eliminado!";
				$msj="Datos eliminados satisfactoriamente.";
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El cliente se encuentra vinculado al modulo de ventas";
			$classM="alert alert-danger";
			$times="&times;";
		}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR PROVEEDORES*/


/*****************************************************************************/



/*INICIO FUNCION PARA CAMBIAR LOS DATOS DE LA EMPRESA*/
	function modificar_perfil(){
		global $con;
		if (empty($_POST['business_name'])){
			$errors[] = "Nombre del negocio está vacío";
		}else if (empty($_POST['number_id'])){
			$errors[] = "Número de registro está vacío";
		} else if (empty($_POST['email'])){
			$errors[] = "Email está vacío";
		} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Su dirección de correo electrónico no está en un formato de correo electrónico válida";
        } else if (empty($_POST['phone'])){
			$errors[] = "Teléfono está vacío";
		} else if (empty($_POST['address1'])){
			$errors[] = "La dirección está vacía";
		}  elseif (empty($_POST['city'])) {
            $errors[] = "La ciudad está vacía";
        } elseif (empty($_POST['state'])) {
            $errors[] = "Región/Provincia está vacío";
        } elseif (empty($_POST['postal_code'])) {
            $errors[] = "Código Postal está vacío";
        } elseif (empty($_POST['country_id'])) {
            $errors[] = "Selecciona el País";
        }    elseif (
			!empty($_POST['address1'])
			&& !empty($_POST['business_name'])
			&& !empty($_POST['number_id'])
			&& !empty($_POST['city'])
			&& !empty($_POST['state'])
			&& !empty($_POST['postal_code'])
			&& !empty($_POST['country_id'])
			&& filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)
			) {
		
			
			// escaping, additionally removing everything that could be (html/javascript-) code
                $business_name = mysqli_real_escape_string($con,(strip_tags($_POST["business_name"],ENT_QUOTES)));
				$number_id = mysqli_real_escape_string($con,(strip_tags($_POST["number_id"],ENT_QUOTES)));
				$email= mysqli_real_escape_string($con,(strip_tags($_POST["email"],ENT_QUOTES)));
				$phone= mysqli_real_escape_string($con,(strip_tags($_POST["phone"],ENT_QUOTES)));
				$tax= intval($_POST["tax"]);
				$currency= intval($_POST["currency"]);
				$timezone=intval($_POST["timezone"]);
				$address1 = mysqli_real_escape_string($con,(strip_tags($_POST["address1"],ENT_QUOTES)));
				$city= mysqli_real_escape_string($con,(strip_tags($_POST["city"],ENT_QUOTES)));
                $state = mysqli_real_escape_string($con,(strip_tags($_POST["state"],ENT_QUOTES)));
				$postal_code=intval($_POST['postal_code']);
				$country_id=intval($_POST['country_id']);
            
				// update data
                    $sql = "UPDATE business_profile SET name='".$business_name."',number_id='".$number_id."',email='".$email."',
					phone='".$phone."',tax='".$tax."', currency_id='".$currency."', timezone_id='".$timezone."', address='".$address1."', city='".$city."', state='".$state."',  postal_code='".$postal_code."', country_id='".$country_id."' WHERE id='1' ";
                    $query = mysqli_query($con,$sql);

                    // if user has been update successfully
                    if ($query) {
                        $messages[] = "Los datos han sido actualizados exitosamente.";
                    } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
			
		} else {
			$errors[] = " Desconocido";	
		}
		if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
	}
/*FIN FUNCION PARA CAMBIAR LOS DATOS DE LA EMPRESA*/

/*INICIA FUNCIONES PARA CREAR EDITAR Y ELIMINAR GRUPOS DE USUARIOS*/
function agregar_grupo(){
	global $con;
	/* Inicio Validacion*/
	if (empty($_POST["nombres"])){
		$errors[] = "Nombres vacío";
	}elseif (!empty($_POST['nombres'])){
		$num=1;
		$sql="select * from modulos";
		$q=mysqli_query($con,$sql);
		$num_md=mysqli_num_rows($q);
		$num=1;
		$permisos_url="";
		while ($num<=$num_md){
			$perm="permisos_".$num;
			$view="view_".$num;
			$edit="edit_".$num;
			$del="del_".$num;
			$permisosfiles=@$_POST[$perm];
			$permisosview=@$_POST[$view];
			$permisosedit=@$_POST[$edit];
			$permisosdel=@$_POST[$del];
			if (empty($permisosview)){$permisosview=0;}
			if (empty($permisosedit)){$permisosedit=0;}
			if (empty($permisosdel)){$permisosdel=0;}
			$permisos_url.=$permisosfiles.",".$permisosview.",".$permisosedit.",".$permisosdel.";";
			$num++;
		}
		// escaping, additionally removing everything that could be (html/javascript-) code
		$nombres = mysqli_real_escape_string($con,(strip_tags($_POST['nombres'], ENT_QUOTES)));
		$date_added=date("Y-m-d H:i:s");
		// Guardo los datos
         $sql = "INSERT INTO user_group (name, permission, date_added) VALUES 
			('".$nombres."', '".$permisos_url."','".$date_added."');";
        $query_new_user_insert = mysqli_query($con,$sql);
		// if is added successfully
         if ($query_new_user_insert) {
            $messages[] = "Datos han sido registrados satisfactoriamente.";
          } else {
             $errors[] = "Lo sentimos, registro falló. Intente nuevamente. ".mysqli_error($con);
          }
	}
	
	if (isset($errors)){
		?>
		<div class="alert alert-error">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Error! </strong>
		<?php
			foreach ($errors as $error){
				echo $error;
			}	
		?>
		</div>	
		<?php	
	} 
	if (isset($messages)){
	?>
		<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Aviso! </strong>
	<?php
		foreach ($messages as $message){
			echo $message;
		}
	?>
		</div>	
	<?php
	}
}
function modificar_grupo(){
	global $con;
	if (empty($_POST["nombres"])){
		$errors[] = "Nombres vacío";
	}elseif (!empty($_POST['nombres'])){
		$id=base64_decode($_POST["user_group_id"]);
		$user_group_id=intval($id);
		$num=1;
		$sql="select * from modulos";
		$q=mysqli_query($con,$sql);
		$num_md=mysqli_num_rows($q);
		$num=0;
		$permisos_url="";
		while ($num<$num_md){
			$perm="permisos_".$num;
			$view="view_".$num;
			$edit="edit_".$num;
			$del="del_".$num;
			$permisosfiles=@$_POST[$perm];
			$permisosview=@$_POST[$view];
			$permisosedit=@$_POST[$edit];
			$permisosdel=@$_POST[$del];
			if (empty($permisosview)){$permisosview=0;}
			if (empty($permisosedit)){$permisosedit=0;}
			if (empty($permisosdel)){$permisosdel=0;}
			$permisos_url.=$permisosfiles.",".$permisosview.",".$permisosedit.",".$permisosdel.";";
			$num++;
		}
		$permisos_url;
		// escaping, additionally removing everything that could be (html/javascript-) code
		$nombres = mysqli_real_escape_string($con,(strip_tags($_POST['nombres'], ENT_QUOTES)));
		$date_added=date("Y-m-d H:i:s");
		// update data into database
         $sql = "UPDATE user_group SET name='".$nombres."', permission='".$permisos_url."' 
		WHERE user_group_id='".$user_group_id."';";
        $query_new_user_insert = mysqli_query($con,$sql);
        // if user has been added successfully
         if ($query_new_user_insert) {
            $messages[] = "Grupo de usuario actualizado satisfactoriamente.";
          } else {
            $errors[] = "Lo sentimos, actualización falló. Intente nuevamente. ".mysqli_error($con);
          }
	}
		if (isset($errors)){
		?>
		<div class="alert alert-error">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Error! </strong>
		<?php
			foreach ($errors as $error){
				echo $error;
			}	
		?>
		</div>	
		<?php	
	} 
	if (isset($messages)){
	?>
		<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Aviso! </strong>
	<?php
		foreach ($messages as $message){
			echo $message;
		}
	?>
		</div>	
	<?php
	}
}
function eliminar_grupo($user_group_id){
	global $con, $aviso, $classM, $times, $msj;
	$sql_user=mysqli_query($con,"select * from users where user_group_id='$user_group_id'");
	$num_user=mysqli_num_rows($sql_user);
		if ($num_user>0){
			$aviso="Aviso!";
			$msj="No se puede borrar este grupo de usuarios. Existen usuarios vinculados a este grupo.";
			$classM="alert alert-error";
			$times="&times;";
		} else if ($num_user==0){
			if($delete=mysqli_query($con, "DELETE FROM user_group WHERE user_group_id='$user_group_id'")){
				$aviso="Bien hecho!";
				$msj="Datos eliminados satisfactoriamente.";
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-danger";
				$times="&times;";					
			}
		
		}
}
/*FIN  FUNCIONES PARA CREAR EDITAR Y ELIMINAR GRUPOS DE USUARIOS*/


/*****************************************************************************/
function agregar_doctrailer(){
	global $con; 
	if (empty($_POST['traid'])){
			$errors[] = "Seleccione Transportista";
		 } else{
						
// escaping, additionally removing everything that could be (html/javascript-) code
    $traid = mysqli_real_escape_string($con,(strip_tags($_POST["traid"],ENT_QUOTES)));
	$choid = mysqli_real_escape_string($con,(strip_tags($_POST["choid"],ENT_QUOTES)));
    $cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
    $plaid = mysqli_real_escape_string($con,(strip_tags($_POST["plaid"],ENT_QUOTES)));
    $matid = mysqli_real_escape_string($con,(strip_tags($_POST["matid"],ENT_QUOTES)));
	$umed  = mysqli_real_escape_string($con,(strip_tags($_POST["umed"],ENT_QUOTES)));
	$upes  = mysqli_real_escape_string($con,(strip_tags($_POST["upes"],ENT_QUOTES)));
	$pedim = mysqli_real_escape_string($con,(strip_tags($_POST["pedim"],ENT_QUOTES)));
	$peso = mysqli_real_escape_string($con,(strip_tags($_POST["peso"],ENT_QUOTES)));
	$piezas = mysqli_real_escape_string($con,(strip_tags($_POST["piezas"],ENT_QUOTES)));
	$caja = mysqli_real_escape_string($con,(strip_tags($_POST["caja"],ENT_QUOTES)));
	$equipidt = mysqli_real_escape_string($con,(strip_tags($_POST["equipidt"],ENT_QUOTES)));
	$equipidr = mysqli_real_escape_string($con,(strip_tags($_POST["equipidr"],ENT_QUOTES)));

	$fecha=date("Y-m-d H:i:s");
    $xsql = "Select empreid from clientes Where CliId=".$cliid;
    $query = mysqli_query($con,$xsql);

    $xerr = mysqli_error($con);

    $row   = mysqli_fetch_array($query);
	$empreid = intval($row["empreid"]);
	/*
echo '<script language="javascript">alert("';
echo "Error: ".$xerr;
echo '");</script>'; 

echo '<script language="javascript">alert("';
echo "Empresa: ".$empreid;
echo '");</script>';
*/
	
	$userid = $_SESSION['user_id'];
			
	// write new  data into database
	$estatus = "Pend";
    $sql = "INSERT INTO doctrailers(fecha, traid, choid, cliid, plaid, matid, umedida, upeso, pedimento, peso, piezas, caja, estatus, empreid, userid, enviado, afactura, equipidt, equipidr) VALUES('$fecha', '$traid', '$choid', '$cliid', '$plaid', '$matid', '$umed', '$upes', '$pedim', '$peso', '$piezas', '$caja', '$estatus', '$empreid', '$userid', 'No', 'N', '$equipidt', '$equipidr');";
    $query = mysqli_query($con,$sql);

    // if has been added successfully
    if ($query) {
        $messages[] = "El registro ha sido creado con éxito.";
     } else {
        $errors[] = mysqli_error($con)." Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_doctrailer(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID del Registro vacío";
		} else {
		
	$traid = mysqli_real_escape_string($con,(strip_tags($_POST["traid"],ENT_QUOTES)));
	$choid = mysqli_real_escape_string($con,(strip_tags($_POST["choid"],ENT_QUOTES)));
	$plaid = mysqli_real_escape_string($con,(strip_tags($_POST["plaid"],ENT_QUOTES)));
    $cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
	$matid = mysqli_real_escape_string($con,(strip_tags($_POST["matid"],ENT_QUOTES)));
	$umed  = mysqli_real_escape_string($con,(strip_tags($_POST["umed"],ENT_QUOTES)));
	$upes  = mysqli_real_escape_string($con,(strip_tags($_POST["upes"],ENT_QUOTES)));
	$pedim = mysqli_real_escape_string($con,(strip_tags($_POST["pedim"],ENT_QUOTES)));
	$peso = mysqli_real_escape_string($con,(strip_tags($_POST["peso"],ENT_QUOTES)));
	$piezas = mysqli_real_escape_string($con,(strip_tags($_POST["piezas"],ENT_QUOTES)));
	$caja = mysqli_real_escape_string($con,(strip_tags($_POST["caja"],ENT_QUOTES)));
	$equipidt = mysqli_real_escape_string($con,(strip_tags($_POST["equipidt"],ENT_QUOTES)));
	$equipidr = mysqli_real_escape_string($con,(strip_tags($_POST["equipidr"],ENT_QUOTES)));
		
				
	$created_at=date("Y-m-d H:i:s");
   
	$id=intval($_POST['id']);//ID del cliente
	
		// write new  data into database
        $sql = "UPDATE doctrailers SET traid='$traid', choid='$choid', cliid='$cliid', plaid='$plaid', matid='$matid', umedida='$umed', upeso='$upes', pedimento='$pedim', peso='$peso', piezas='$piezas', caja='$caja', equipidt='$equipidt', equipidr='$equipidr' WHERE doctraid='".$id."'";
        $query = mysqli_query($con,$sql);

        // if  has been update successfully
        if ($query) {
            $messages[] = "El Registro ha sido actualizado con éxito.";
			
			
        } else {
            $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
        }
              		
	}

if (isset($errors)){
			
	?>
	<div class="alert alert-danger" role="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Error!</strong> 
			<?php
				foreach ($errors as $error) {
						echo $error;
					}
				?>
	</div>
	<?php
	}
	if (isset($messages)){
		
		?>
		<div class="alert alert-success" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>¡Registro Agregado!</strong>
				<?php
					foreach ($messages as $message) {
							echo $message;
						}
					?>
		</div>
		<?php
	}
}
function eliminar_doctrailer($id){
	global $con, $aviso, $classM, $times, $msj;
//	$query_validate=mysqli_query($con,"select cliid from orders where customer_id='".$id."'");
//	$count=mysqli_num_rows($query_validate);

		$count=0;
		if ($count==0)
		{
			if($delete=mysqli_query($con, "DELETE FROM doctrailers WHERE doctraid='$id'") ){
				//and $delete2=mysqli_query($con,"DELETE FROM contacts WHERE client_id='$id' ")){
				$aviso="Registro Eliminado!";
				$msj="Datos eliminados satisfactoriamente.".$id;
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El cliente se encuentra vinculado al modulo de ventas";
			$classM="alert alert-danger";
			$times="&times;";
		}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR DOCTRAILERS*/

function modificar_almacen(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID del Registro vacío";
		} else {
		
	$id = mysqli_real_escape_string($con,(strip_tags($_POST["id"],ENT_QUOTES)));
	$proid = mysqli_real_escape_string($con,(strip_tags($_POST["proid"],ENT_QUOTES)));
    $descrip1 = mysqli_real_escape_string($con,(strip_tags($_POST["descrip1"],ENT_QUOTES)));
	$descrip2 = mysqli_real_escape_string($con,(strip_tags($_POST["descrip2"],ENT_QUOTES)));
	$descrip3 = mysqli_real_escape_string($con,(strip_tags($_POST["descrip3"],ENT_QUOTES)));
	$descrip4  = mysqli_real_escape_string($con,(strip_tags($_POST["descrip4"],ENT_QUOTES)));
	$peso  = mysqli_real_escape_string($con,(strip_tags($_POST["peso"],ENT_QUOTES)));
				
	$created_at=date("Y-m-d H:i:s");
   
	$id=intval($_POST['id']);//ID del cliente
	
		// write new  data into database
        $sql = "UPDATE almacen SET proid='$proid', descrip1='$descrip1', descrip2='$descrip2', descrip3='$descrip3', descrip4='$descrip4', peso='$peso' WHERE almid='".$id."'";
        $query = mysqli_query($con,$sql);

        // if  has been update successfully
        if ($query) {
            $messages[] = "El Registro ha sido actualizado con éxito.";
			
			
        } else {
            $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
        }
              		
	}

if (isset($errors)){
			
	?>
	<div class="alert alert-danger" role="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Error!</strong> 
			<?php
				foreach ($errors as $error) {
						echo $error;
					}
				?>
	</div>
	<?php
	}
	if (isset($messages)){
		
		?>
		<div class="alert alert-success" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>¡Registro Agregado!</strong>
				<?php
					foreach ($messages as $message) {
							echo $message;
						}
					?>
		</div>
		<?php
	}
}


/*****************************************************************************/
function agregar_crossdock(){
	global $con; 
	if (empty($_POST['cliid'])){
			$errors[] = "Seleccione Cliente";
		 } else{
						
// escaping, additionally removing everything that could be (html/javascript-) code
    $cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
    $matid = mysqli_real_escape_string($con,(strip_tags($_POST["matid"],ENT_QUOTES)));
    $carnum = mysqli_real_escape_string($con,(strip_tags($_POST["carnum"],ENT_QUOTES)));
	$cantidad = mysqli_real_escape_string($con,(strip_tags($_POST["cantidad"],ENT_QUOTES)));
	$umed  = mysqli_real_escape_string($con,(strip_tags($_POST["umed"],ENT_QUOTES)));
	$upes  = mysqli_real_escape_string($con,(strip_tags($_POST["upes"],ENT_QUOTES)));
	$equipidm  = mysqli_real_escape_string($con,(strip_tags($_POST["equipidm"],ENT_QUOTES)));
	$manid  = mysqli_real_escape_string($con,(strip_tags($_POST["manid"],ENT_QUOTES)));
	$asisid  = mysqli_real_escape_string($con,(strip_tags($_POST["asisid"],ENT_QUOTES)));

	$fecha=date("Y-m-d H:i:s");
   
	$xsql = "Select empreid from clientes Where CliId=".$cliid;
    $query = mysqli_query($con,$xsql);
    $row   = mysqli_fetch_array($query);
	$empreid = intval($row["empreid"]);
	$userid = $_SESSION['user_id'];


	// write new  data into database
    $sql = "INSERT INTO crossdock(fecha, cliid, matcliid, carnum, umedida, upeso, cantidad, estatus, empreid, userid, enviado, equipidm, manid, asisid) VALUES('$fecha', '$cliid', '$matid', '$carnum', '$umed', '$upes', '$cantidad', 'Pend', '$empreid', '$userid', 'No', $equipidm, $manid, $asisid);";
    $query = mysqli_query($con,$sql);

    // if has been added successfully
    if ($query) {
        $messages[] = "El registro ha sido creado con éxito.";
     } else {
        $errors[] = mysqli_error($con)." Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_crossdock(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID del Registro vacío";
		} else {
		

//$errors[] = "Este es un Error de Prueba";

	$cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
    $matid = mysqli_real_escape_string($con,(strip_tags($_POST["matid"],ENT_QUOTES)));
    $carnum = mysqli_real_escape_string($con,(strip_tags($_POST["carnum"],ENT_QUOTES)));
	$cantidad = mysqli_real_escape_string($con,(strip_tags($_POST["cantidad"],ENT_QUOTES)));
	$umed  = mysqli_real_escape_string($con,(strip_tags($_POST["umed"],ENT_QUOTES)));
	$upes  = mysqli_real_escape_string($con,(strip_tags($_POST["upes"],ENT_QUOTES)));
	$equipidm  = mysqli_real_escape_string($con,(strip_tags($_POST["equipidm"],ENT_QUOTES)));

	$manid  = mysqli_real_escape_string($con,(strip_tags($_POST["manid"],ENT_QUOTES)));
	$asisid  = mysqli_real_escape_string($con,(strip_tags($_POST["asisid"],ENT_QUOTES)));
   
	$id=intval($_POST['id']);//ID del cliente
	
		// write new  data into database
        $sql = "UPDATE crossdock SET cliid='$cliid', matcliid='$matid', carnum='$carnum', umedida='$umed', upeso='$upes', cantidad='$cantidad', equipidm='$equipidm', manid='$manid', asisid='$asisid'  WHERE crosid='".$id."'";
        $query = mysqli_query($con,$sql);

        // if  has been update successfully
        if ($query) {
            $messages[] = "El Registro ha sido actualizado con éxito.";
			
			
        } else {
            $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
        }
              		
	}

if (isset($errors)){
			
	?>
	<div class="alert alert-danger" role="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Error!</strong> 
			<?php
				foreach ($errors as $error) {
						echo $error;
					}
				?>
	</div>
	<?php
	}
	if (isset($messages)){
		
		?>
		<div class="alert alert-success" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>¡Registro Agregado!</strong>
				<?php
					foreach ($messages as $message) {
							echo $message;
						}
					?>
		</div>
		<?php
	}
}
function eliminar_crossdock($id){
	global $con, $aviso, $classM, $times, $msj;
//	$query_validate=mysqli_query($con,"select cliid from orders where customer_id='".$id."'");
//	$count=mysqli_num_rows($query_validate);

		$count=0;
		if ($count==0)
		{
			if($delete=mysqli_query($con, "DELETE FROM crossdock WHERE crosid='$id'") ){
				//and $delete2=mysqli_query($con,"DELETE FROM contacts WHERE client_id='$id' ")){
				$aviso="Registro Eliminado!";
				$msj="Datos eliminados satisfactoriamente.".$id;
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El cliente se encuentra vinculado al modulo de ventas";
			$classM="alert alert-danger";
			$times="&times;";
		}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR CROSSDOCK*/






/*****************************************************************************/
/*  rr */
function agregar_cartaporte(){
	global $con; 
	if (empty($_POST['cliid'])){
			$errors[] = "Seleccione Cliente";
		 } else{
						
// escaping, additionally removing everything that could be (html/javascript-) code
    $cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
    $traid = mysqli_real_escape_string($con,(strip_tags($_POST["traid"],ENT_QUOTES)));
    $desid = mysqli_real_escape_string($con,(strip_tags($_POST["desid"],ENT_QUOTES)));
	$dirid = mysqli_real_escape_string($con,(strip_tags($_POST["dirid"],ENT_QUOTES)));
	$choid = mysqli_real_escape_string($con,(strip_tags($_POST["choid"],ENT_QUOTES)));
	$plaid = mysqli_real_escape_string($con,(strip_tags($_POST["plaid"],ENT_QUOTES)));
	$equipidt = mysqli_real_escape_string($con,(strip_tags($_POST["equipidt"],ENT_QUOTES)));
	$equipidr = mysqli_real_escape_string($con,(strip_tags($_POST["equipidr"],ENT_QUOTES)));
	
$tipocarta = $_SESSION['tipocarta'];
$xtip="PendC";
if ($tipocarta==2){
  $xtip="PendA";
}

	$fecha=date("Y-m-d H:i:s");
   
   	$xsql = "Select empreid from clientes Where CliId=".$cliid;
    $query = mysqli_query($con,$xsql);
    $row   = mysqli_fetch_array($query);
	$empreid = intval($row["empreid"]);
	$userid = $_SESSION['user_id'];
			
	// write new  data into database
    $sql = "INSERT INTO cartaporte(fecha, cliid, traid, destino, dircliid, choid, plaid, tipo, empreid, userid, impresa, afactura, equipidt, equipidr) VALUES('$fecha', '$cliid', '$traid', '$desid', '$dirid', '$choid', '$plaid','$xtip', '$empreid', '$userid', 'No', 'N', '$equipidt', '$equipidr');";
    $query = mysqli_query($con,$sql);

    // if has been added successfully
    if ($query) {
        $messages[] = "El registro ha sido creado con éxito.";
     } else {
        $errors[] = mysqli_error($con)." Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_cartaporte(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID del Registro vacío";
		} else {
		
	$cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
    $traid = mysqli_real_escape_string($con,(strip_tags($_POST["traid"],ENT_QUOTES)));
    $desid = mysqli_real_escape_string($con,(strip_tags($_POST["desid"],ENT_QUOTES)));
	$dirid = mysqli_real_escape_string($con,(strip_tags($_POST["dirid"],ENT_QUOTES)));
	$choid = mysqli_real_escape_string($con,(strip_tags($_POST["choid"],ENT_QUOTES)));
	$plaid = mysqli_real_escape_string($con,(strip_tags($_POST["plaid"],ENT_QUOTES)));
	$equipidt = mysqli_real_escape_string($con,(strip_tags($_POST["equipidt"],ENT_QUOTES)));
	$equipidr = mysqli_real_escape_string($con,(strip_tags($_POST["equipidr"],ENT_QUOTES)));



   
	$id=intval($_POST['id']);//ID del cliente
	
		// write new  data into database
        $sql = "UPDATE cartaporte SET cliid='$cliid', traid='$traid', dircliid='$dirid', destino='$desid', choid='$choid', plaid='$plaid', equipidt='$equipidt', equipidr='$equipidr'  WHERE carid='".$id."'";
        $query = mysqli_query($con,$sql);

        // if  has been update successfully
        if ($query) {
            $messages[] = "El Registro ha sido actualizado con éxito.";
			
			
        } else {
            $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
        }
              		
	}

if (isset($errors)){
			
	?>
	<div class="alert alert-danger" role="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Error!</strong> 
			<?php
				foreach ($errors as $error) {
						echo $error;
					}
				?>
	</div>
	<?php
	}
	if (isset($messages)){
		
		?>
		<div class="alert alert-success" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>¡Registro Agregado!</strong>
				<?php
					foreach ($messages as $message) {
							echo $message;
						}
					?>
		</div>
		<?php
	}
}
function eliminar_cartaporte($id){
	global $con, $aviso, $classM, $times, $msj;
//	$query_validate=mysqli_query($con,"select cliid from orders where customer_id='".$id."'");
//	$count=mysqli_num_rows($query_validate);

		$count=0;
		if ($count==0)
		{
			if($delete=mysqli_query($con, "DELETE FROM cartaporte WHERE carid='$id'") ){
				//and $delete2=mysqli_query($con,"DELETE FROM contacts WHERE client_id='$id' ")){
				$aviso="Registro Eliminado!";
				$msj="Datos eliminados satisfactoriamente.".$id;
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El cliente se encuentra vinculado al modulo de ventas";
			$classM="alert alert-danger";
			$times="&times;";
		}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR DOCTRAILERS*/



function agregar_cartaportealm(){
	global $con; 
	if (empty($_POST['cliid']) or empty($_POST['traid'])){
			$errors[] = "Seleccione Cliente y Transportista";

		 } else{
						
// escaping, additionally removing everything that could be (html/javascript-) code
    $cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
    $traid = mysqli_real_escape_string($con,(strip_tags($_POST["traid"],ENT_QUOTES)));
    $desid = mysqli_real_escape_string($con,(strip_tags($_POST["desid"],ENT_QUOTES)));
	$dirid = mysqli_real_escape_string($con,(strip_tags($_POST["dirid"],ENT_QUOTES)));

	if (empty($_POST["choid"])){
	  $choid = 0;	
	}else{
	  $choid = mysqli_real_escape_string($con,(strip_tags($_POST["choid"],ENT_QUOTES)));
	}
	if (empty($_POST["choid"])){
	  $plaid = 0;	
	}else{
	  $plaid = mysqli_real_escape_string($con,(strip_tags($_POST["plaid"],ENT_QUOTES)));
    }

	$equipidm  = mysqli_real_escape_string($con,(strip_tags($_POST["equipidm"],ENT_QUOTES)));
	$equipidt  = mysqli_real_escape_string($con,(strip_tags($_POST["equipidt"],ENT_QUOTES)));
	$equipidr  = mysqli_real_escape_string($con,(strip_tags($_POST["equipidr"],ENT_QUOTES)));
	$manid  = mysqli_real_escape_string($con,(strip_tags($_POST["manid"],ENT_QUOTES)));
	$asisid  = mysqli_real_escape_string($con,(strip_tags($_POST["asisid"],ENT_QUOTES)));


	$tipocarta = $_SESSION['tipocarta'];
$xtip="PendA";


	$fecha=date("Y-m-d H:i:s");
   
   	$xsql = "Select empreid from clientes Where CliId=".$cliid;
    $query = mysqli_query($con,$xsql);
    $row   = mysqli_fetch_array($query);
	$empreid = intval($row["empreid"]);
	$userid = $_SESSION['user_id'];
			
	// write new  data into database
    $sql = "INSERT INTO cartaporte(fecha, cliid, traid, destino, dircliid, choid, plaid, tipo, empreid, userid, afactura, equipidm, equipidt, equipidr, manid, asisid) VALUES('$fecha', '$cliid', '$traid', '$desid', '$dirid', '$choid', '$plaid','$xtip', '$empreid', '$userid', 'N', '$equipidm', '$equipidt', '$equipidr', '$manid', '$asisid');";
    $query = mysqli_query($con,$sql);

    // if has been added successfully
    if ($query) {
        $messages[] = "El registro ha sido creado con éxito.";
     } else {
        $errors[] = mysqli_error($con)." Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_cartaportealm(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID del Registro vacío";
		} else {
		
	$cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
    $traid = mysqli_real_escape_string($con,(strip_tags($_POST["traid"],ENT_QUOTES)));
    $desid = mysqli_real_escape_string($con,(strip_tags($_POST["desid"],ENT_QUOTES)));
	$dirid = mysqli_real_escape_string($con,(strip_tags($_POST["dirid"],ENT_QUOTES)));
	$choid = mysqli_real_escape_string($con,(strip_tags($_POST["choid"],ENT_QUOTES)));
	$plaid = mysqli_real_escape_string($con,(strip_tags($_POST["plaid"],ENT_QUOTES)));

	$equipidm = mysqli_real_escape_string($con,(strip_tags($_POST["equipidm"],ENT_QUOTES)));
	$equipidt = mysqli_real_escape_string($con,(strip_tags($_POST["equipidt"],ENT_QUOTES)));
	$equipidr = mysqli_real_escape_string($con,(strip_tags($_POST["equipidr"],ENT_QUOTES)));
	$manid  = mysqli_real_escape_string($con,(strip_tags($_POST["manid"],ENT_QUOTES)));
	$asisid  = mysqli_real_escape_string($con,(strip_tags($_POST["asisid"],ENT_QUOTES)));

   
	$id=intval($_POST['id']);//ID del cliente
	
		// write new  data into database
        $sql = "UPDATE cartaporte SET cliid='$cliid', traid='$traid', dircliid='$dirid', destino='$desid', choid='$choid', plaid='$plaid', equipidm='$equipidm', equipidt='$equipidt', equipidr='$equipidr', manid='$manid', asisid='$asisid'  WHERE carid='".$id."'";
        $query = mysqli_query($con,$sql);

        // if  has been update successfully
        if ($query) {
            $messages[] = "El Registro ha sido actualizado con éxito.";
			
			
        } else {
            $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
        }
              		
	}

if (isset($errors)){
			
	?>
	<div class="alert alert-danger" role="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Error!</strong> 
			<?php
				foreach ($errors as $error) {
						echo $error;
					}
				?>
	</div>
	<?php
	}
	if (isset($messages)){
		
		?>
		<div class="alert alert-success" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>¡Registro Agregado!</strong>
				<?php
					foreach ($messages as $message) {
							echo $message;
						}
					?>
		</div>
		<?php
	}
}
function eliminar_cartaportealm($id){
	global $con, $aviso, $classM, $times, $msj;
//	$query_validate=mysqli_query($con,"select cliid from orders where customer_id='".$id."'");
//	$count=mysqli_num_rows($query_validate);

		$count=0;
		if ($count==0)
		{
			if($delete=mysqli_query($con, "DELETE FROM cartaporte WHERE carid='$id'") ){
				//and $delete2=mysqli_query($con,"DELETE FROM contacts WHERE client_id='$id' ")){
				$aviso="Registro Eliminado!";
				$msj="Datos eliminados satisfactoriamente.".$id;
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El cliente se encuentra vinculado al modulo de ventas";
			$classM="alert alert-danger";
			$times="&times;";
		}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR DOCTRAILERS*/








/*****************************************************************************/
function agregar_doccarro(){
	global $con; 
	if (empty($_POST['cliid'])){
			$errors[] = "Seleccione Cliente";
		 } else{
						
// escaping, additionally removing everything that could be (html/javascript-) code
    $cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
    $dirid = mysqli_real_escape_string($con,(strip_tags($_POST["dirid"],ENT_QUOTES)));
    $carnum = mysqli_real_escape_string($con,(strip_tags($_POST["carnum"],ENT_QUOTES)));
	$sello = mysqli_real_escape_string($con,(strip_tags($_POST["sello"],ENT_QUOTES)));
	$equipidm = mysqli_real_escape_string($con,(strip_tags($_POST["equipidm"],ENT_QUOTES)));
	$manid  = mysqli_real_escape_string($con,(strip_tags($_POST["manid"],ENT_QUOTES)));
	$asisid  = mysqli_real_escape_string($con,(strip_tags($_POST["asisid"],ENT_QUOTES)));

	$fecha=date("Y-m-d H:i:s");
   
    $xsql = "Select empreid from clientes Where CliId=".$cliid;
    $query = mysqli_query($con,$xsql);
    $row   = mysqli_fetch_array($query);
	$empreid = intval($row["empreid"]);
	$userid = $_SESSION['user_id'];
			
	// write new  data into database
	$estatus = "Pend";
    $sql = "INSERT INTO doccarros(fecha, cliid, dircliid, carnum, sello, estatus, empreid, userid, equipidm, manid, asisid) VALUES('$fecha', '$cliid', '$dirid', '$carnum', '$sello', '$estatus', '$empreid', '$userid', '$equipidm', '$manid', '$asisid');";
    $query = mysqli_query($con,$sql);

    // if has been added successfully
    if ($query) {
        $messages[] = "El registro ha sido creado con éxito.";
     } else {
        $errors[] = mysqli_error($con)." Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_doccarro(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID del Registro vacío";
		} else {
		
	$cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
    $dirid = mysqli_real_escape_string($con,(strip_tags($_POST["dirid"],ENT_QUOTES)));
    $carnum = mysqli_real_escape_string($con,(strip_tags($_POST["carnum"],ENT_QUOTES)));
	$sello = mysqli_real_escape_string($con,(strip_tags($_POST["sello"],ENT_QUOTES)));
	$equipidm = mysqli_real_escape_string($con,(strip_tags($_POST["equipidm"],ENT_QUOTES)));
	$manid  = mysqli_real_escape_string($con,(strip_tags($_POST["manid"],ENT_QUOTES)));
	$asisid  = mysqli_real_escape_string($con,(strip_tags($_POST["asisid"],ENT_QUOTES)));


   
	$id=intval($_POST['id']);//ID del cliente
	
		// write new  data into database
        $sql = "UPDATE doccarros SET cliid='$cliid', dircliid='$dirid', carnum='$carnum', sello='$sello', equipidm='$equipidm', manid='$manid', asisid='$asisid' WHERE doccarid='".$id."'";
        $query = mysqli_query($con,$sql);

        // if  has been update successfully
        if ($query) {
            $messages[] = "El Registro ha sido actualizado con éxito.";
			
			
        } else {
            $errors[] = mysqli_error($con)." Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.";
        }
              		
	}

if (isset($errors)){
			
	?>
	<div class="alert alert-danger" role="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Error!</strong> 
			<?php
				foreach ($errors as $error) {
						echo $error;
					}
				?>
	</div>
	<?php
	}
	if (isset($messages)){
		
		?>
		<div class="alert alert-success" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>¡Registro Agregado!</strong>
				<?php
					foreach ($messages as $message) {
							echo $message;
						}
					?>
		</div>
		<?php
	}
}
function eliminar_doccarro($id){
	global $con, $aviso, $classM, $times, $msj;
//	$query_validate=mysqli_query($con,"select cliid from orders where customer_id='".$id."'");
//	$count=mysqli_num_rows($query_validate);

		$count=0;
		if ($count==0)
		{
			if($delete=mysqli_query($con, "DELETE FROM doccarros WHERE doctraid='$id'") ){
				//and $delete2=mysqli_query($con,"DELETE FROM contacts WHERE client_id='$id' ")){
				$aviso="Registro Eliminado!";
				$msj="Datos eliminados satisfactoriamente.".$id;
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El cliente se encuentra vinculado al modulo de ventas";
			$classM="alert alert-danger";
			$times="&times;";
		}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR DOCTRAILERS*/


/*****************************************************************************/
function agregar_otrosmovs(){
	global $con; 
	if (empty($_POST['cliid'])){
			$errors[] = "Seleccione Cliente";
		 } else{
						
// escaping, additionally removing everything that could be (html/javascript-) code
    $cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
    $tipo = mysqli_real_escape_string($con,(strip_tags($_POST["tipo"],ENT_QUOTES)));
    $carnum = mysqli_real_escape_string($con,(strip_tags($_POST["carnum"],ENT_QUOTES)));
	$coment = mysqli_real_escape_string($con,(strip_tags($_POST["coment"],ENT_QUOTES)));
	$equipidm = mysqli_real_escape_string($con,(strip_tags($_POST["equipidm"],ENT_QUOTES)));
	$manid  = mysqli_real_escape_string($con,(strip_tags($_POST["manid"],ENT_QUOTES)));
	$asisid  = mysqli_real_escape_string($con,(strip_tags($_POST["asisid"],ENT_QUOTES)));


	$fecha=date("Y-m-d H:i:s");
    
    $xsql = "Select empreid from clientes Where CliId=".$cliid;
    $query = mysqli_query($con,$xsql);
    $row   = mysqli_fetch_array($query);
	$empreid = intval($row["empreid"]);
	$userid = $_SESSION['user_id'];
			
	// write new  data into database
	$estatus = "Pend";
    $sql = "INSERT INTO otrosmovs(fecha, cliid, tipo, carnum, comentario, estatus, empreid, userid, equipidm, manid, asisid) VALUES('$fecha', '$cliid', '$tipo', '$carnum', '$coment', '$estatus', '$empreid', '$userid', '$equipidm', '$manid', '$asisid');";
    $query = mysqli_query($con,$sql);

    // if has been added successfully
    if ($query) {
        $messages[] = "El registro ha sido creado con éxito.";
     } else {
        $errors[] = mysqli_error($con)." Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_otrosmovs(){
/*
	echo '<script language="javascript">alert("';
echo "Hola 2";
echo '");</script>'; 
*/
	global $con;
	if (empty($_POST['id'])){
/*
						echo '<script language="javascript">alert("';
echo "Hola 3";
echo '");</script>'; 
*/
			$errors[] = "ID del Registro vacío";
		} else {



		
	$cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
    $tipo = mysqli_real_escape_string($con,(strip_tags($_POST["tipo"],ENT_QUOTES)));
    $carnum = mysqli_real_escape_string($con,(strip_tags($_POST["carnum"],ENT_QUOTES)));
	$coment = mysqli_real_escape_string($con,(strip_tags($_POST["coment"],ENT_QUOTES)));
	$equipidm = mysqli_real_escape_string($con,(strip_tags($_POST["equipidm"],ENT_QUOTES)));
	$manid  = mysqli_real_escape_string($con,(strip_tags($_POST["manid"],ENT_QUOTES)));
	$asisid  = mysqli_real_escape_string($con,(strip_tags($_POST["asisid"],ENT_QUOTES)));


/*
echo '<script language="javascript">alert("';
echo "Id: ".$id;
echo "CliId: ".$cliid;

echo "Tipo: ".$tipo;
echo "CarNum: ".$carnum;
echo "Coment: ".$coment;
echo '");</script>'; 

*/
   
	$id=intval($_POST['id']);//ID del cliente
	
		// write new  data into database
        $sql = "UPDATE otrosmovs SET cliid='$cliid', tipo='$tipo', carnum='$carnum', comentario='$coment', equipidm='$equipidm', manid='$manid', asisid='$asisid' WHERE recid='".$id."'";
        $query = mysqli_query($con,$sql);

        // if  has been update successfully
        if ($query) {
            $messages[] = "El Registro ha sido actualizado con éxito.";
			
			
        } else {
            $errors[] = mysqli_error($con)." Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.";
        }
              		
	}

if (isset($errors)){
			
	?>
	<div class="alert alert-danger" role="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Error!</strong> 
			<?php
				foreach ($errors as $error) {
						echo $error;
					}
				?>
	</div>
	<?php
	}
	if (isset($messages)){
		
		?>
		<div class="alert alert-success" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>¡Registro Agregado!</strong>
				<?php
					foreach ($messages as $message) {
							echo $message;
						}
					?>
		</div>
		<?php
	}
}
function eliminar_otrosmovs($id){
	global $con, $aviso, $classM, $times, $msj;
//	$query_validate=mysqli_query($con,"select cliid from orders where customer_id='".$id."'");
//	$count=mysqli_num_rows($query_validate);

		$count=0;
		if ($count==0)
		{
			if($delete=mysqli_query($con, "DELETE FROM otrosmovs WHERE recid='$id'") ){
				//and $delete2=mysqli_query($con,"DELETE FROM contacts WHERE client_id='$id' ")){
				$aviso="Registro Eliminado!";
				$msj="Datos eliminados satisfactoriamente.".$id;
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El Registro se encuentra vinculado a otro modulo";
			$classM="alert alert-danger";
			$times="&times;";
		}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR DOCTRAILERS*/



/*****************************************************************************/
function agregar_entradas(){
	global $con; 
	if (empty($_POST['cliid'])){
			$errors[] = "Seleccione Cliente";
		 } else{
						
// escaping, additionally removing everything that could be (html/javascript-) code
$cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
$matid = mysqli_real_escape_string($con,(strip_tags($_POST["matid"],ENT_QUOTES)));
$traid = mysqli_real_escape_string($con,(strip_tags($_POST["traid"],ENT_QUOTES)));
$choid = mysqli_real_escape_string($con,(strip_tags($_POST["choid"],ENT_QUOTES)));
$plaid = mysqli_real_escape_string($con,(strip_tags($_POST["plaid"],ENT_QUOTES)));
$dirid = mysqli_real_escape_string($con,(strip_tags($_POST["dirid"],ENT_QUOTES)));
$flete = mysqli_real_escape_string($con,(strip_tags($_POST["flete"],ENT_QUOTES)));

$matid = mysqli_real_escape_string($con,(strip_tags($_POST["matid"],ENT_QUOTES)));
$cantidad = mysqli_real_escape_string($con,(strip_tags($_POST["cantidad"],ENT_QUOTES)));
$umed  = mysqli_real_escape_string($con,(strip_tags($_POST["umed"],ENT_QUOTES)));
$upes  = mysqli_real_escape_string($con,(strip_tags($_POST["upes"],ENT_QUOTES)));

$equipidm  = mysqli_real_escape_string($con,(strip_tags($_POST["equipidm"],ENT_QUOTES)));
$equipidt  = mysqli_real_escape_string($con,(strip_tags($_POST["equipidt"],ENT_QUOTES)));
$equipidr  = mysqli_real_escape_string($con,(strip_tags($_POST["equipidr"],ENT_QUOTES)));
$manid  = mysqli_real_escape_string($con,(strip_tags($_POST["manid"],ENT_QUOTES)));
$asisid  = mysqli_real_escape_string($con,(strip_tags($_POST["asisid"],ENT_QUOTES)));


if ($flete=="No"){
	$equipidt  = 0;	
}

$fecha=date("Y-m-d H:i:s");

 $xsql = "Select empreid from clientes Where CliId=".$cliid;
$query = mysqli_query($con,$xsql);
$row   = mysqli_fetch_array($query);
$empreid = intval($row["empreid"]);
$userid = $_SESSION['user_id'];


 /*		
echo '<script language="javascript">alert("';

echo "matid: ".$matid."<br>";
echo "traid: ".$traid."<br>";
echo "choid: ".$choid."<br>";
echo "plaid: ".$plaid."<br>";
echo "dirid: ".$dirid."<br>";
echo "cant: ".$cantidad."<br>";

echo "umed: ".$umed;
echo "upes: ".$upes;


echo '");</script>'; 
*/



	// write new  data into database
    $sql = "INSERT INTO entradas(fecha, cliid, matcliid, conflete, cantidad, umedida, upeso, estatus, traid, choid, plaid, dircliid, empreid, userid, equipidm, equipidt, equipidr, manid, asisid) VALUES('$fecha', '$cliid', '$matid', '$flete', '$cantidad', '$umed', '$upes', 'Pend', '$traid', '$choid', '$plaid', '$dirid', '$empreid', '$userid', '$equipidm', '$equipidt', '$equipidr', '$manid', '$asisid');";
  


    $query = mysqli_query($con,$sql);

    // if has been added successfully
    if ($query) {
        $messages[] = "El registro ha sido creado con éxito.";
     } else {
        $errors[] = mysqli_error($con)." Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_entradas(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID del Registro vacío";
		} else {
		
	$cliid = mysqli_real_escape_string($con,(strip_tags($_POST["cliid"],ENT_QUOTES)));
    $choid = mysqli_real_escape_string($con,(strip_tags($_POST["choid"],ENT_QUOTES)));
 	$cantidad = mysqli_real_escape_string($con,(strip_tags($_POST["cantidad"],ENT_QUOTES)));
	$plaid  = mysqli_real_escape_string($con,(strip_tags($_POST["plaid"],ENT_QUOTES)));
	$dirid  = mysqli_real_escape_string($con,(strip_tags($_POST["dirid"],ENT_QUOTES)));
	$flete  = mysqli_real_escape_string($con,(strip_tags($_POST["flete"],ENT_QUOTES)));
	$equipidm  = mysqli_real_escape_string($con,(strip_tags($_POST["equipidm"],ENT_QUOTES)));
	$equipidt  = mysqli_real_escape_string($con,(strip_tags($_POST["equipidt"],ENT_QUOTES)));
	$equipidr  = mysqli_real_escape_string($con,(strip_tags($_POST["equipidr"],ENT_QUOTES)));
	$manid  = mysqli_real_escape_string($con,(strip_tags($_POST["manid"],ENT_QUOTES)));
	$asisid  = mysqli_real_escape_string($con,(strip_tags($_POST["asisid"],ENT_QUOTES)));

	if ($flete=="No"){
		$equipidt  = 0;	
	}

   
	$id=intval($_POST['id']);//ID del cliente
	
		// write new  data into database
        $sql = "UPDATE entradas SET choid='$choid', dircliid='$dirid', conflete='$flete', plaid='$plaid', cantidad='$cantidad', equipidm='$equipidm', equipidt='$equipidt', equipidr='$equipidr', manid='$manid', asisid='$asisid'  WHERE entid='".$id."'";
        $query = mysqli_query($con,$sql);



        // if  has been update successfully
        if ($query) {
            $messages[] = "El Registro ha sido actualizado con éxito.";
			
			
        } else {
            $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
        }
              		
	}

if (isset($errors)){
			
	?>
	<div class="alert alert-danger" role="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Error!</strong> 
			<?php
				foreach ($errors as $error) {
						echo $error;
					}
				?>
	</div>
	<?php
	}
	if (isset($messages)){
		
		?>
		<div class="alert alert-success" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>¡Registro Agregado!</strong>
				<?php
					foreach ($messages as $message) {
							echo $message;
						}
					?>
		</div>
		<?php
	}
}
function eliminar_entradas($id){
	global $con, $aviso, $classM, $times, $msj;
//	$query_validate=mysqli_query($con,"select cliid from orders where customer_id='".$id."'");
//	$count=mysqli_num_rows($query_validate);

		$count=0;
		if ($count==0)
		{
			if($delete=mysqli_query($con, "DELETE FROM entradas WHERE entid='$id'") ){
				//and $delete2=mysqli_query($con,"DELETE FROM contacts WHERE client_id='$id' ")){
				$aviso="Registro Eliminado!";
				$msj="Datos eliminados satisfactoriamente.".$id;
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El cliente se encuentra vinculado al modulo de ventas";
			$classM="alert alert-danger";
			$times="&times;";
		}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR ENTRADAS*/








/*INICIA  FUNCIONES PARA CREAR EDITAR Y ELIMINAR  USUARIOS*/
	function agregar_usuario(){
		global $con;
		if (empty($_POST['fullname'])){
			$errors[] = "Nombres vacíos";
		}  elseif (empty($_POST['user_name'])) {
            $errors[] = "Nombre de usuario vacío";
        } elseif (empty($_POST['user_password_new']) || empty($_POST['user_password_repeat'])) {
            $errors[] = "Contraseña vacía";
        } elseif ($_POST['user_password_new'] !== $_POST['user_password_repeat']) {
            $errors[] = "la contraseña y la repetición de la contraseña no son lo mismo";
        } elseif (strlen($_POST['user_password_new']) < 6) {
            $errors[] = "La contraseña debe tener como mínimo 6 caracteres";
        } elseif (strlen($_POST['user_name']) > 64 || strlen($_POST['user_name']) < 2) {
            $errors[] = "Nombre de usuario no puede ser inferior a 2 o más de 64 caracteres";
        } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])) {
            $errors[] = "Nombre de usuario no encaja en el esquema de nombre: Sólo aZ y los números están permitidos , de 2 a 64 caracteres";
        } elseif (empty($_POST['user_email'])) {
            $errors[] = "El correo electrónico no puede estar vacío";
        } elseif (strlen($_POST['user_email']) > 64) {
            $errors[] = "El correo electrónico no puede ser superior a 64 caracteres";
        } elseif (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Su dirección de correo electrónico no está en un formato de correo electrónico válida";
        } elseif (
			!empty($_POST['user_name'])
			&& !empty($_POST['fullname'])
			&& strlen($_POST['user_name']) <= 64
            && strlen($_POST['user_name']) >= 2
            && preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])
            && !empty($_POST['user_email'])
            && strlen($_POST['user_email']) <= 64
            && filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)
            && !empty($_POST['user_password_new'])
            && !empty($_POST['user_password_repeat'])
            && ($_POST['user_password_new'] === $_POST['user_password_repeat'])
        ) {
		
			// escaping, additionally removing everything that could be (html/javascript-) code
                $fullname = mysqli_real_escape_string($con,(strip_tags($_POST["fullname"],ENT_QUOTES)));
				$user_name = mysqli_real_escape_string($con,(strip_tags($_POST["user_name"],ENT_QUOTES)));
                $user_email = mysqli_real_escape_string($con,(strip_tags($_POST["user_email"],ENT_QUOTES)));
				$user_password = $_POST['user_password_new'];
				$tipo=$_POST['tipo'];
				$cliid=intval($_POST['cliid']);
				$traid=intval($_POST['traid']);
				
				
				$date_added=date("Y-m-d H:i:s");
                // crypt the user's password with PHP 5.5's password_hash() function, results in a 60 character
                // hash string. the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using
                // PHP 5.3/5.4, by the password hashing compatibility library
				$user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);
				// check if user or email address already exists
                $sql = "SELECT * FROM users WHERE user_name = '" . $user_name . "' OR user_email = '" . $user_email . "';";
                $query_check_user_name = mysqli_query($con,$sql);
				$query_check_user=mysqli_num_rows($query_check_user_name);
                if ($query_check_user == 1) {
                    $errors[] = "Lo sentimos , el nombre de usuario ó la dirección de correo electrónico ya está en uso.";
                } else {
					// write new user's data into database
                    $sql = "INSERT INTO users (fullname, user_name, user_password_hash, user_email, date_added, tipouser, cliid, traid)
                            VALUES('".$fullname."','" . $user_name . "', '" . $user_password_hash . "', '" . $user_email . "','".$date_added."','".$tipo."','".$cliid."','".$traid."');";
                    $query_new_user_insert = mysqli_query($con,$sql);

                    // if user has been added successfully
                    if ($query_new_user_insert) {
                        $messages[] = "La cuenta ha sido creada con éxito.";
                    } else {
                        $errors[] = mysqli_error($con)."Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                }
			
		}else {
			$errors[] = "Error desconocido";	
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
	}
	function modificar_usuario(){
		global $con;
		if (empty($_POST['fullname'])){
			$errors[] = "Nombres vacíos";
		}  elseif (empty($_POST['user_name'])) {
            $errors[] = "Nombre de usuario vacío";
        }  elseif (strlen($_POST['user_name']) > 64 || strlen($_POST['user_name']) < 2) {
            $errors[] = "Nombre de usuario no puede ser inferior a 2 o más de 64 caracteres";
        } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])) {
            $errors[] = "Nombre de usuario no encaja en el esquema de nombre: Sólo aZ y los números están permitidos , de 2 a 64 caracteres";
        } elseif (empty($_POST['user_email'])) {
            $errors[] = "El correo electrónico no puede estar vacío";
        } elseif (strlen($_POST['user_email']) > 64) {
            $errors[] = "El correo electrónico no puede ser superior a 64 caracteres";
        } elseif (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Su dirección de correo electrónico no está en un formato de correo electrónico válida";
        } elseif (
			!empty($_POST['user_name'])
			&& !empty($_POST['fullname'])
			&& strlen($_POST['user_name']) <= 64
            && strlen($_POST['user_name']) >= 2
            && preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])
            && !empty($_POST['user_email'])
            && strlen($_POST['user_email']) <= 64
            && filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)
            ) {
		
			
			// escaping, additionally removing everything that could be (html/javascript-) code
                $fullname = mysqli_real_escape_string($con,(strip_tags($_POST["fullname"],ENT_QUOTES)));
				$user_name = mysqli_real_escape_string($con,(strip_tags($_POST["user_name"],ENT_QUOTES)));
                $user_email = mysqli_real_escape_string($con,(strip_tags($_POST["user_email"],ENT_QUOTES)));
                $emailreal = mysqli_real_escape_string($con,(strip_tags($_POST["emailreal"],ENT_QUOTES)));
                
			if (isset($_POST["enviar"])){
                $enviar = mysqli_real_escape_string($con,(strip_tags($_POST["enviar"],ENT_QUOTES)));
			}else{
				$enviar = 0;
			}			
				$user_id=intval($_POST['user_id']);
				$tipo=$_POST['tipo'];
				$cliid=intval($_POST['cliid']);
				$cliid2=intval($_POST['cliid2']);				
			//	$proid=intval($_POST['proid']);
				$traid=intval($_POST['traid']);

						
            
				// write new user's data into database
                    $sql = "UPDATE users SET fullname='".$fullname."', user_name='".$user_name."', user_email='".$user_email."', emailreal='".$emailreal."', tipouser='".$tipo."', enviar='".$enviar."', cliid='".$cliid."', cliid2='".$cliid2."', traid='".$traid."' WHERE user_id='".$user_id."' ";
                    $query = mysqli_query($con,$sql);

                    // if user has been added successfully
                    if ($query) {
                        $messages[] = "La cuenta ha sido actualizada con éxito.";
                    } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
			
		} else {
			$errors[] = " Desconocido";	
		}
		if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
	}
	function  eliminar_usuario($user_id){
	global $con, $aviso, $classM, $times, $msj;
	$query_validate1=mysqli_query($con,"select employee_id from orders where employee_id='".$user_id."'");
	$count1=mysqli_num_rows($query_validate1);
		if ($count1==0 and $user_id!=1)
		{	
			if($delete=mysqli_query($con, "DELETE FROM users WHERE user_id='$user_id'")){
				$aviso="Bien hecho!";
				$msj="Datos eliminados satisfactoriamente.";
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else 
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. Este usuario se encuentra vinculado al modulo de ordenes";
			$classM="alert alert-error";
			$times="&times;";
		}
	}
	
	function modificar_password(){
		global $con;
		
		if ($_POST){
	if (empty($_POST['user_password_new']) || empty($_POST['user_password_repeat'])) {
            $errors[] = "Contraseña vacía";
        } elseif ($_POST['user_password_new'] !== $_POST['user_password_repeat']) {
            $errors[] = "la contraseña y la repetición de la contraseña no son lo mismo";
        } elseif (strlen($_POST['user_password_new']) < 6) {
            $errors[] = "La contraseña debe tener como mínimo 6 caracteres";
        }elseif (
			!empty($_POST['user_password_new'])
            && !empty($_POST['user_password_repeat'])
            && ($_POST['user_password_new'] === $_POST['user_password_repeat'])
        ) 
		{
			
			$user_password = $_POST['user_password_new'];
			$user_id=intval($_POST['user_id']);
			$user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);//Encripta la contraseña
			$query=mysqli_query($con,"select * from users  where user_id='".$user_id."'");
			$count=mysqli_num_rows($query);
			if ($count==1)
			{
				$update=mysqli_query($con,"update users set user_password_hash='".$user_password_hash."' where user_id='".$user_id."' ");
				// if is successfully
                    if ($update) {
                        $messages[] = "La contraseña ha sido cambiada con éxito.";
                    } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
			} 
			else
			{
				$errors[] = "Usuario no encontrado.";
			}
		
			
		}
}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
	}
	
/*FIN  FUNCIONES PARA CREAR EDITAR Y ELIMINAR  USUARIOS*/

/*****************************************************************************/
function agregar_gasclasif(){
	global $con; 
	if (empty($_POST['descrip'])){
			$errors[] = "Nombres vacíos";
		 } else{
						
			// escaping, additionally removing everything that could be (html/javascript-) code
                $descrip = mysqli_real_escape_string($con,(strip_tags($_POST["descrip"],ENT_QUOTES)));
				$fecha=date("Y-m-d H:i:s");
               			
					// write new  data into database
                    $sql = "INSERT INTO gasclasif(descripclas, fecha) VALUES('$descrip','$fecha');";
                    $query = mysqli_query($con,$sql);

                    // if has been added successfully
                    if ($query) {
                        $messages[] = "El registro ha sido creado con éxito.";
//						$last=mysqli_query($con,"select LAST_INSERT_ID(cliid) as last from clientes order by cliid desc limit 0,1 ");
//						$rw=mysqli_fetch_array($last);
//						$cliid=$rw['last'];
			         } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_gasclasif(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID del Registro vacío";
		} else {
			
			// escaping, additionally removing everything that could be (html/javascript-) code
                $descrip = mysqli_real_escape_string($con,(strip_tags($_POST["descrip"],ENT_QUOTES)));
			
				$created_at=date("Y-m-d H:i:s");
        		$id=intval($_POST['id']);//ID del cliente
				
					// write new  data into database
                    $sql = "UPDATE gasclasif SET descripclas='$descrip' WHERE clasid='".$id."'";
                    $query = mysqli_query($con,$sql);

                    // if  has been update successfully
                    if ($query) {
                        $messages[] = "El Registro ha sido actualizado con éxito.";
						
						
                    } else {
                        $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
              		
		}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR GASCLASIF*/

/*****************************************************************************/
function agregar_gasunidmed(){
	global $con; 
	if (empty($_POST['descrip'])){
			$errors[] = "Nombres vacíos";
		 } else{
						
			// escaping, additionally removing everything that could be (html/javascript-) code
                $descrip = mysqli_real_escape_string($con,(strip_tags($_POST["descrip"],ENT_QUOTES)));
				$fecha=date("Y-m-d H:i:s");
               			
					// write new  data into database
                    $sql = "INSERT INTO gasunidmed(descripunid, fecha) VALUES('$descrip','$fecha');";
                    $query = mysqli_query($con,$sql);

                    // if has been added successfully
                    if ($query) {
                        $messages[] = "El registro ha sido creado con éxito.";
//						$last=mysqli_query($con,"select LAST_INSERT_ID(cliid) as last from clientes order by cliid desc limit 0,1 ");
//						$rw=mysqli_fetch_array($last);
//						$cliid=$rw['last'];
			         } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_gasunidmed(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID del Registro vacío";
		} else {
			
			// escaping, additionally removing everything that could be (html/javascript-) code
                $descrip = mysqli_real_escape_string($con,(strip_tags($_POST["descrip"],ENT_QUOTES)));
			
				$created_at=date("Y-m-d H:i:s");
        		$id=intval($_POST['id']);//ID del cliente
				
					// write new  data into database
                    $sql = "UPDATE gasunidmed SET descripunid='$descrip' WHERE unidid='".$id."'";
                    $query = mysqli_query($con,$sql);

                    // if  has been update successfully
                    if ($query) {
                        $messages[] = "El Registro ha sido actualizado con éxito.";
						
						
                    } else {
                        $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
              		
		}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR GASUNIDMED*/

/*****************************************************************************/
function agregar_gascuentas(){
	global $con; 
	if (empty($_POST['descrip'])){
			$errors[] = "Nombres vacíos";
		 } else{
						
			// escaping, additionally removing everything that could be (html/javascript-) code
	    $descrip = mysqli_real_escape_string($con,(strip_tags($_POST["descrip"],ENT_QUOTES)));
		$numcta = mysqli_real_escape_string($con,(strip_tags($_POST["numcta"],ENT_QUOTES)));
		$subcta = mysqli_real_escape_string($con,(strip_tags($_POST["subcta"],ENT_QUOTES)));
		$nombre = mysqli_real_escape_string($con,(strip_tags($_POST["nombre"],ENT_QUOTES)));
		$empreid = mysqli_real_escape_string($con,(strip_tags($_POST["empreid"],ENT_QUOTES)));
		$clasid = mysqli_real_escape_string($con,(strip_tags($_POST["clasid"],ENT_QUOTES)));
				$fecha=date("Y-m-d H:i:s");
               
				
					// write new  data into database
                    $sql = "INSERT INTO gascuentas(descripcta, numcta, subcta, descripsub, fecha, empreid, clasid) VALUES('$descrip', '$numcta', '$subcta','$nombre', '$fecha', '$empreid', '$clasid');";
                    $query = mysqli_query($con,$sql);

                    // if has been added successfully
                    if ($query) {
                        $messages[] = "El registro ha sido creado con éxito.";
//						$last=mysqli_query($con,"select LAST_INSERT_ID(cliid) as last from clientes order by cliid desc limit 0,1 ");
//						$rw=mysqli_fetch_array($last);
//						$cliid=$rw['last'];
			         } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_gascuentas(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID del Registro vacío";
		} else {
			
			// escaping, additionally removing everything that could be (html/javascript-) code
        $descrip = mysqli_real_escape_string($con,(strip_tags($_POST["descrip"],ENT_QUOTES)));
		$numcta = mysqli_real_escape_string($con,(strip_tags($_POST["numcta"],ENT_QUOTES)));
		$subcta = mysqli_real_escape_string($con,(strip_tags($_POST["subcta"],ENT_QUOTES)));
		$nombre = mysqli_real_escape_string($con,(strip_tags($_POST["nombre"],ENT_QUOTES)));
//		$empreid = mysqli_real_escape_string($con,(strip_tags($_POST["empreid"],ENT_QUOTES)));
		$clasid = mysqli_real_escape_string($con,(strip_tags($_POST["clasid"],ENT_QUOTES)));
			
				$created_at=date("Y-m-d H:i:s");
               
				$id=intval($_POST['id']);//ID del cliente
				
					// write new  data into database
                    $sql = "UPDATE gascuentas SET clasid='$clasid', descripcta='$descrip', numcta='$numcta', subcta='$subcta', descripsub='$nombre' WHERE ctaid='".$id."'";
                    $query = mysqli_query($con,$sql);

                    // if  has been update successfully
                    if ($query) {
                        $messages[] = "El Registro ha sido actualizado con éxito.";
						
						
                    } else {
                        $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
              		
		}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}

/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR GASCUENTAS*/


/*****************************************************************************/
function agregar_gasequipos(){
	global $con; 
	if (empty($_POST['descrip'])){
			$errors[] = "Nombres vacíos";
		 } else{
						
			// escaping, additionally removing everything that could be (html/javascript-) code
        $descrip = mysqli_real_escape_string($con,(strip_tags($_POST["descrip"],ENT_QUOTES)));
		$refer = mysqli_real_escape_string($con,(strip_tags($_POST["refer"],ENT_QUOTES)));
		$empreid = mysqli_real_escape_string($con,(strip_tags($_POST["empreid"],ENT_QUOTES)));
		$tipoid = mysqli_real_escape_string($con,(strip_tags($_POST["tipoid"],ENT_QUOTES)));
		$fechacom = mysqli_real_escape_string($con,(strip_tags($_POST["fechacompra"],ENT_QUOTES)));
		$fechapla = mysqli_real_escape_string($con,(strip_tags($_POST["fechaplacas"],ENT_QUOTES)));
		$fechaseg = mysqli_real_escape_string($con,(strip_tags($_POST["fechaseguro"],ENT_QUOTES)));
		$numserie = mysqli_real_escape_string($con,(strip_tags($_POST["numserie"],ENT_QUOTES)));
		$numint = mysqli_real_escape_string($con,(strip_tags($_POST["numint"],ENT_QUOTES)));
		$placas = mysqli_real_escape_string($con,(strip_tags($_POST["placas"],ENT_QUOTES)));
		$bodega = mysqli_real_escape_string($con,(strip_tags($_POST["bodega"],ENT_QUOTES)));
		
				$fecha=date("Y-m-d H:i:s");
               			
					// write new  data into database
                    $sql = "INSERT INTO gasequipos(descripequip, refer, fecha, empreid, fechacompra, fechaplacas, fechaseguro, numserie, numint, placas, bodega, tipoid) VALUES('$descrip', '$refer', '$fecha', '$empreid', '$fechacom', '$fechapla', '$fechaseg', '$numserie', '$numint', '$placas', '$bodega', '$tipoid');";
                    $query = mysqli_query($con,$sql);

                    // if has been added successfully
                    if ($query) {
                        $messages[] = "El registro ha sido creado con éxito.";
//						$last=mysqli_query($con,"select LAST_INSERT_ID(cliid) as last from clientes order by cliid desc limit 0,1 ");
//						$rw=mysqli_fetch_array($last);
//						$cliid=$rw['last'];
			         } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_gasequipos(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID del Registro vacío";
		} else {
			
			// escaping, additionally removing everything that could be (html/javascript-) code
            $descrip = mysqli_real_escape_string($con,(strip_tags($_POST["descrip"],ENT_QUOTES)));
			$refer = mysqli_real_escape_string($con,(strip_tags($_POST["refer"],ENT_QUOTES)));
//			$empreid = mysqli_real_escape_string($con,(strip_tags($_POST["empreid"],ENT_QUOTES)));
		$tipoid = mysqli_real_escape_string($con,(strip_tags($_POST["tipoid"],ENT_QUOTES)));
		$fechacom = mysqli_real_escape_string($con,(strip_tags($_POST["fechacompra"],ENT_QUOTES)));
		$fechapla = mysqli_real_escape_string($con,(strip_tags($_POST["fechaplacas"],ENT_QUOTES)));
		$fechaseg = mysqli_real_escape_string($con,(strip_tags($_POST["fechaseguro"],ENT_QUOTES)));
		$numserie = mysqli_real_escape_string($con,(strip_tags($_POST["numserie"],ENT_QUOTES)));
		$numint = mysqli_real_escape_string($con,(strip_tags($_POST["numint"],ENT_QUOTES)));
		$plaid = mysqli_real_escape_string($con,(strip_tags($_POST["plaid"],ENT_QUOTES)));
		$placas = mysqli_real_escape_string($con,(strip_tags($_POST["placas"],ENT_QUOTES)));
		$bodega = mysqli_real_escape_string($con,(strip_tags($_POST["bodega"],ENT_QUOTES)));
		$tipo = mysqli_real_escape_string($con,(strip_tags($_POST["tipo"],ENT_QUOTES)));

		if ($plaid>0){
			$sql = "select * from transplacas Where plaid='$plaid'";
		    $query = mysqli_query($con,$sql);
		    $row = mysqli_fetch_array($query);
		    $placas=$row['Placas'];
		}
			 
				$created_at=date("Y-m-d H:i:s");
        		$id=intval($_POST['id']);//ID del cliente

   				// write new  data into database
                    $sql = "UPDATE gasequipos SET descripequip='$descrip', refer='$refer', fechacompra='$fechacom', fechaplacas='$fechapla', fechaseguro='$fechaseg', numserie='$numserie', numint='$numint', placas='$placas', plaid='$plaid', bodega='$bodega', tipoid='$tipoid', tipo='$tipo' WHERE equipid='".$id."'";
                    $query = mysqli_query($con,$sql);

                    // if  has been update successfully
                    if ($query) {
                        $messages[] = "El Registro ha sido actualizado con éxito.";
						
						
                    } else {
                        $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
              		
		}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR GASEQUIPOS*/


/*****************************************************************************/
function agregar_empleados(){
	global $con; 
	if (empty($_POST['nombre'])){
			$errors[] = "Nombres vacíos";
		 } else{
						
			






			// escaping, additionally removing everything that could be (html/javascript-) code
    $nombre = mysqli_real_escape_string($con,(strip_tags($_POST["nombre"],ENT_QUOTES)));
	$direccion = mysqli_real_escape_string($con,(strip_tags($_POST["direccion"],ENT_QUOTES)));
	$telefono1 = mysqli_real_escape_string($con,(strip_tags($_POST["telefono1"],ENT_QUOTES)));
	$telefono2 = mysqli_real_escape_string($con,(strip_tags($_POST["telefono2"],ENT_QUOTES)));
	$puesto = mysqli_real_escape_string($con,(strip_tags($_POST["puesto"],ENT_QUOTES)));
	$empreid	 = mysqli_real_escape_string($con,(strip_tags($_POST["empreid"],ENT_QUOTES)));
    $perioid = mysqli_real_escape_string($con,(strip_tags($_POST["perioid"],ENT_QUOTES)));
    $seguro = mysqli_real_escape_string($con,(strip_tags($_POST["seguro"],ENT_QUOTES)));
    $salario = mysqli_real_escape_string($con,(strip_tags($_POST["salario"],ENT_QUOTES)));
    $factor = mysqli_real_escape_string($con,(strip_tags($_POST["factor"],ENT_QUOTES)));
    $puntualidad = mysqli_real_escape_string($con,(strip_tags($_POST["puntualidad"],ENT_QUOTES)));
    $despensa = mysqli_real_escape_string($con,(strip_tags($_POST["despensa"],ENT_QUOTES)));
    $asistencia = mysqli_real_escape_string($con,(strip_tags($_POST["asistencia"],ENT_QUOTES)));
    $premiotrab = mysqli_real_escape_string($con,(strip_tags($_POST["premiotrab"],ENT_QUOTES)));
    $premioprod = mysqli_real_escape_string($con,(strip_tags($_POST["premioprod"],ENT_QUOTES)));
    $diasvac = mysqli_real_escape_string($con,(strip_tags($_POST["diasvac"],ENT_QUOTES)));
    $primavac = mysqli_real_escape_string($con,(strip_tags($_POST["primavac"],ENT_QUOTES)));
    $smgdf = mysqli_real_escape_string($con,(strip_tags($_POST["smgdf"],ENT_QUOTES)));
    $fechaing = mysqli_real_escape_string($con,(strip_tags($_POST["fechaing"],ENT_QUOTES)));
    $imss = mysqli_real_escape_string($con,(strip_tags($_POST["imss"],ENT_QUOTES)));
    $imss2 = mysqli_real_escape_string($con,(strip_tags($_POST["imss2"],ENT_QUOTES)));
    $especie = mysqli_real_escape_string($con,(strip_tags($_POST["especie"],ENT_QUOTES)));
    $prestamo = mysqli_real_escape_string($con,(strip_tags($_POST["prestamo"],ENT_QUOTES)));
    $infonavit = mysqli_real_escape_string($con,(strip_tags($_POST["infonavit"],ENT_QUOTES)));
    $clasid = mysqli_real_escape_string($con,(strip_tags($_POST["clasid"],ENT_QUOTES)));
    $ennom = mysqli_real_escape_string($con,(strip_tags($_POST["ennom"],ENT_QUOTES)));


	$fecha=date("Y-m-d H:i:s");
               
				
					// write new  data into database
                  $sql = "INSERT INTO empleados(nombre, direccion, telefono1, telefono2, fecha, puesto, empreid, seguro, factor, puntualidad, despensa, asistencia, premiotrab, premioprod, diasvac, primavac, smgdf, fechaing, imss, imss2, especie, prestamo, infonavit, perioid, clasid, ennomina) VALUES('$nombre', '$direccion', '$telefono1','$telefono2', '$fecha', '$puesto', '$empreid', '$seguro', '$factor', '$puntualidad', '$despensa', '$asistencia', '$premiotrab', '$premioprod', '$diasvac', '$primavac', '$smgdf', '$fechaing', '$imss', '$imss2', '$especie', '$prestamo', '$infonavit', '$perioid', '$clasid', '$ennom');";
                    $query = mysqli_query($con,$sql);

                    // if has been added successfully
                    if ($query) {
                        $messages[] = "El registro ha sido creado con éxito.";
//						$last=mysqli_query($con,"select LAST_INSERT_ID(cliid) as last from clientes order by cliid desc limit 0,1 ");
//						$rw=mysqli_fetch_array($last);
//						$cliid=$rw['last'];
			         } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_empleados(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID del Registro vacío";
		} else {
			
    $nombre = mysqli_real_escape_string($con,(strip_tags($_POST["nombre"],ENT_QUOTES)));
	$direccion = mysqli_real_escape_string($con,(strip_tags($_POST["direccion"],ENT_QUOTES)));
	$telefono1 = mysqli_real_escape_string($con,(strip_tags($_POST["telefono1"],ENT_QUOTES)));
	$telefono2 = mysqli_real_escape_string($con,(strip_tags($_POST["telefono2"],ENT_QUOTES)));
	$puesto = mysqli_real_escape_string($con,(strip_tags($_POST["puesto"],ENT_QUOTES)));
    $perioid = mysqli_real_escape_string($con,(strip_tags($_POST["perioid"],ENT_QUOTES)));

    $seguro = mysqli_real_escape_string($con,(strip_tags($_POST["seguro"],ENT_QUOTES)));
    $salario = mysqli_real_escape_string($con,(strip_tags($_POST["salario"],ENT_QUOTES)));
    $factor = mysqli_real_escape_string($con,(strip_tags($_POST["factor"],ENT_QUOTES)));
    $puntualidad = mysqli_real_escape_string($con,(strip_tags($_POST["puntualidad"],ENT_QUOTES)));
    $despensa = mysqli_real_escape_string($con,(strip_tags($_POST["despensa"],ENT_QUOTES)));
    $asistencia = mysqli_real_escape_string($con,(strip_tags($_POST["asistencia"],ENT_QUOTES)));
    $premiotrab = mysqli_real_escape_string($con,(strip_tags($_POST["premiotrab"],ENT_QUOTES)));
    $premioprod = mysqli_real_escape_string($con,(strip_tags($_POST["premioprod"],ENT_QUOTES)));
    $diasvac = mysqli_real_escape_string($con,(strip_tags($_POST["diasvac"],ENT_QUOTES)));
    $primavac = mysqli_real_escape_string($con,(strip_tags($_POST["primavac"],ENT_QUOTES)));
    $smgdf = mysqli_real_escape_string($con,(strip_tags($_POST["smgdf"],ENT_QUOTES)));
    $fechaing = mysqli_real_escape_string($con,(strip_tags($_POST["fechaing"],ENT_QUOTES)));
    $imss = mysqli_real_escape_string($con,(strip_tags($_POST["imss"],ENT_QUOTES)));
    $imss2 = mysqli_real_escape_string($con,(strip_tags($_POST["imss2"],ENT_QUOTES)));
    $especie = mysqli_real_escape_string($con,(strip_tags($_POST["especie"],ENT_QUOTES)));
    $prestamo = mysqli_real_escape_string($con,(strip_tags($_POST["prestamo"],ENT_QUOTES)));
    $infonavit = mysqli_real_escape_string($con,(strip_tags($_POST["infonavit"],ENT_QUOTES)));
    $clasid = mysqli_real_escape_string($con,(strip_tags($_POST["clasid"],ENT_QUOTES)));
    $ennom = mysqli_real_escape_string($con,(strip_tags($_POST["ennom"],ENT_QUOTES)));
				
				$created_at=date("Y-m-d H:i:s");
               
				$id=intval($_POST['id']);//ID del cliente
				
					// write new  data into database
                    $sql = "UPDATE empleados SET nombre='$nombre', direccion='$direccion', telefono1='$telefono1', telefono2='$telefono2', puesto='$puesto', seguro='$seguro', salario='$salario', factor='$factor', puntualidad='$puntualidad', despensa='$despensa', asistencia='$asistencia', premiotrab='$premiotrab', premioprod='$premioprod', diasvac='$diasvac', primavac='$primavac', smgdf='$smgdf', fechaing='$fechaing', imss='$imss', imss2='$imss2', especie='$especie', prestamo='$prestamo', infonavit='$infonavit', perioid='$perioid', clasid='$clasid', ennomina='$ennom' WHERE empid='".$id."'";
                    $query = mysqli_query($con,$sql);

                    // if  has been update successfully
                    if ($query) {
                        $messages[] = "El Registro ha sido actualizado con éxito.";
						
						
                    } else {
                        $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
              		
		}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}



/*****************************************************************************/
function agregar_gastos(){
	global $con; 
	if (empty($_POST['equipid'])){
			$errors[] = "Seleccione un Activo";
		 } else{
						
  $equipid = mysqli_real_escape_string($con,(strip_tags($_POST["equipid"],ENT_QUOTES)));
  $empreid = mysqli_real_escape_string($con,(strip_tags($_POST["empreid"],ENT_QUOTES)));
  $clasid = mysqli_real_escape_string($con,(strip_tags($_POST["clasid"],ENT_QUOTES)));
  $ctaid = mysqli_real_escape_string($con,(strip_tags($_POST["ctaid"],ENT_QUOTES)));
  $proid = mysqli_real_escape_string($con,(strip_tags($_POST["proid"],ENT_QUOTES)));
  $cantidad = mysqli_real_escape_string($con,(strip_tags($_POST["cantidad"],ENT_QUOTES)));
  $unidid = mysqli_real_escape_string($con,(strip_tags($_POST["unidid"],ENT_QUOTES)));
  $subtotal  = mysqli_real_escape_string($con,(strip_tags($_POST["subtotal"],ENT_QUOTES)));
  $montoiva  = mysqli_real_escape_string($con,(strip_tags($_POST["montoiva"],ENT_QUOTES)));
  $montoret = mysqli_real_escape_string($con,(strip_tags($_POST["montoret"],ENT_QUOTES)));
  $total = mysqli_real_escape_string($con,(strip_tags($_POST["total"],ENT_QUOTES)));
  $factura = mysqli_real_escape_string($con,(strip_tags($_POST["factura"],ENT_QUOTES)));
  $descripgas = mysqli_real_escape_string($con,(strip_tags($_POST["descripgas"],ENT_QUOTES)));


	$fecha=date("Y-m-d H:i:s");
   
	$empid=$_SESSION['gempid'];

		
	// write new  data into database
    $sql = "INSERT INTO gastos(fecha, equipid, clasid, ctaid, cantidad, unidid, subtotal, montoiva, montoret, total, factura, descripgas, estatus, pagada, empreid, empid, proid) VALUES('$fecha', '$equipid', '$clasid', '$ctaid', '$cantidad', '$unidid', '$subtotal', '$montoiva', '$montoret', '$total', '$factura', '$descripgas', 'Pend', 'N', '$empreid', '$empid', '$proid');";
    $query = mysqli_query($con,$sql);

    // if has been added successfully
    if ($query) {
        $messages[] = "El registro ha sido creado con éxito.";
     } else {
        $errors[] = $sql."--".mysqli_error($con)." Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_gastos(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID del Registro vacío";
		} else {
		

  $equipid = mysqli_real_escape_string($con,(strip_tags($_POST["equipid"],ENT_QUOTES)));
  $clasid = mysqli_real_escape_string($con,(strip_tags($_POST["clasid"],ENT_QUOTES)));
  $ctaid = mysqli_real_escape_string($con,(strip_tags($_POST["ctaid"],ENT_QUOTES)));
  $proid = mysqli_real_escape_string($con,(strip_tags($_POST["proid"],ENT_QUOTES)));
  $cantidad = mysqli_real_escape_string($con,(strip_tags($_POST["cantidad"],ENT_QUOTES)));
  $unidid = mysqli_real_escape_string($con,(strip_tags($_POST["unidid"],ENT_QUOTES)));
  $subtotal  = mysqli_real_escape_string($con,(strip_tags($_POST["subtotal"],ENT_QUOTES)));
  $montoiva  = mysqli_real_escape_string($con,(strip_tags($_POST["montoiva"],ENT_QUOTES)));
  $montoret = mysqli_real_escape_string($con,(strip_tags($_POST["montoret"],ENT_QUOTES)));
  $total = mysqli_real_escape_string($con,(strip_tags($_POST["total"],ENT_QUOTES)));
  $factura = mysqli_real_escape_string($con,(strip_tags($_POST["factura"],ENT_QUOTES)));
  $descripgas = mysqli_real_escape_string($con,(strip_tags($_POST["descripgas"],ENT_QUOTES)));
  $empreid = mysqli_real_escape_string($con,(strip_tags($_POST["empreid"],ENT_QUOTES)));
   
	$id=intval($_POST['id']);//ID del cliente
	
		// write new  data into database
        $sql = "UPDATE gastos SET equipid='$equipid', clasid='$clasid', ctaid='$ctaid', cantidad='$cantidad', unidid='$unidid', subtotal='$subtotal', montoiva='$montoiva', montoret='$montoret', total='$total', factura='$factura', descripgas='$descripgas', empreid='$empreid', proid='$proid' WHERE gastid='".$id."'";
        $query = mysqli_query($con,$sql);

        // if  has been update successfully
        if ($query) {
            $messages[] = "El Registro ha sido actualizado con éxito.";
			
			
        } else {
            $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
        }
              		
	}

if (isset($errors)){
			
	?>
	<div class="alert alert-danger" role="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Error!</strong> 
			<?php
				foreach ($errors as $error) {
						echo $error;
					}
				?>
	</div>
	<?php
	}
	if (isset($messages)){
		
		?>
		<div class="alert alert-success" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>¡Registro Agregado!</strong>
				<?php
					foreach ($messages as $message) {
							echo $message;
						}
					?>
		</div>
		<?php
	}
}
function eliminar_gastos($id){
	global $con, $aviso, $classM, $times, $msj;
//	$query_validate=mysqli_query($con,"select cliid from orders where customer_id='".$id."'");
//	$count=mysqli_num_rows($query_validate);

		$count=0;
		if ($count==0)
		{
			if($delete=mysqli_query($con, "DELETE FROM gastos WHERE doctraid='$id'") ){
				//and $delete2=mysqli_query($con,"DELETE FROM contacts WHERE client_id='$id' ")){
				$aviso="Registro Eliminado!";
				$msj="Datos eliminados satisfactoriamente.".$id;
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. El cliente se encuentra vinculado al modulo de ventas";
			$classM="alert alert-danger";
			$times="&times;";
		}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR GASTOS*/


/*****************************************************************************/
function agregar_gasformasp(){
	global $con; 
	if (empty($_POST['descrip'])){
			$errors[] = "Nombres vacíos";
		 } else{
						
			// escaping, additionally removing everything that could be (html/javascript-) code
            $descrip = mysqli_real_escape_string($con,(strip_tags($_POST["descrip"],ENT_QUOTES)));
		    $empreid = mysqli_real_escape_string($con,(strip_tags($_POST["empreid"],ENT_QUOTES)));

				$created_at=date("Y-m-d H:i:s");
               			
					// write new  data into database
                    $sql = "INSERT INTO gasformasp(descripform, fecha, empreid) VALUES('$descrip','$fecha', '$empreid');";
                    $query = mysqli_query($con,$sql);

                    // if has been added successfully
                    if ($query) {
                        $messages[] = "El registro ha sido creado con éxito.";
//						$last=mysqli_query($con,"select LAST_INSERT_ID(cliid) as last from clientes order by cliid desc limit 0,1 ");
//						$rw=mysqli_fetch_array($last);
//						$cliid=$rw['last'];
			         } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
			
		}	 
	

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
function modificar_gasformasp(){
	global $con;
	if (empty($_POST['id'])){
			$errors[] = "ID del Registro vacío";
		} else {
			
			// escaping, additionally removing everything that could be (html/javascript-) code
            $descrip = mysqli_real_escape_string($con,(strip_tags($_POST["descrip"],ENT_QUOTES)));
//		    $empreid = mysqli_real_escape_string($con,(strip_tags($_POST["empreid"],ENT_QUOTES)));
			
				$created_at=date("Y-m-d H:i:s");
        		$id=intval($_POST['id']);//ID del cliente
				
					// write new  data into database
                    $sql = "UPDATE gasformasp SET descripform='$descrip' WHERE formid='".$id."'";
                    $query = mysqli_query($con,$sql);

                    // if  has been update successfully
                    if ($query) {
                        $messages[] = "El Registro ha sido actualizado con éxito.";
						
						
                    } else {
                        $errors[] = "Lo sentimos , la actualización falló. Por favor, regrese y vuelva a intentarlo.".mysqli_error($con);
                    }
              		
		}

if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Registro Agregado!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
}
/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR GASFORMASP*/







/*FIN FUNCIONES PARA CREAR EDITAR Y ELIMINAR EMPLEADOS*/





  /*          
            $domain=$_SERVER['SERVER_NAME'];
            $product="3";
            $licenseServer = ""; //http://alvarado.pw/code/api/";

            $postvalue="domain=$domain&product=".urlencode($product);
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $licenseServer);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postvalue);
            $result= json_decode(curl_exec($ch), true);
            curl_close($ch);
    */
            $result = 1;
			function core_app(){
				return true;
			}
			if($result['status'] != 200) {
            return true;

            }
            ?>