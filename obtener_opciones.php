<?php

$resultado_json = array(); 
$excepcion = null ;

require_once('bdd_coneccion.inc.php'); 

function validar_existencia_tabla(
	$tabla,
	$mysqli
){
	$resultado = false;
	global $excepcion; 
	
	try{
		$qry = "SHOW TABLES" ;
		
		if($stmt = $mysqli->prepare($qry)){			
			
			$stmt->execute();
			
			/* bind variables to prepared statement */
			$stmt->bind_result(
				$qry_tabla
			);

			/* fetch values */
			while ($stmt->fetch()) {
				if($qry_tabla == $tabla) $resultado = true ;
			}

			/* close statement */
			$stmt->close();			
			
		}  else {
			throw new Exception("Fallo al preparar la query: $qry");
		}	
	} catch (Exception $e) {
		$excepcion = $e;
	}	
	return $resultado;
}

function obtener_opciones(
		$tabla,
		$mysqli
	){
	global $excepcion;
	$resultado = null ;
	try{		
		$qry = "SELECT * FROM $tabla" ;
		
		if($rs = $mysqli->query($qry)){
			$resultado = array();
			while($rsArr = $rs->fetch_array(MYSQLI_ASSOC)){
				$resultado[] = $rsArr;
			}
			$rs->free();
		}
	} catch (Exception $e) {
		$excepcion = $e;
	}	
	return $resultado ;	
}
 
if(isset($_GET['tabla'])){
	$tabla = $_GET['tabla']; 
	
	if($tabla <> ''){
		if(validar_existencia_tabla(
			$tabla,
			$mysqli		
		)){
			$obtener_opciones_resultado = null ;
			if($obtener_opciones_resultado = obtener_opciones(
				$tabla,
				$mysqli
			)){
				$resultado_json['obtener_opciones'] = $obtener_opciones_resultado ;
			}
		}else{
			$resultado_json['motivo'] = "La tabla $tabla no existe en la base de datos" ;
		}
	}
} 

/* cerrar la conexión */
$mysqli->close();
if($excepcion){
	$resultado_json['ex'] = array(
		'getMessage' => $excepcion->getMessage(),
		'getTrace' => $excepcion->getTrace()
		) ;
}
echo json_encode($resultado_json); 

?>