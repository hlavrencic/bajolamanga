<?php
$resultado_json = array();
$resultado_json['ex'] = false ;
$resultado_json['agregarEfectoCondicion_id'] = 0 ;

header('Content-Type: application/json');

require_once('bdd_coneccion.inc.php'); 

class ClsSoporte
{
	private $soporte_id;
	private $mysqli ; 	
	
	private function efectos_como_insert(
			$que_tipo,
			$que1_jugador,
			$que1_valor,        
			$que2_jugador,
			$que2_valor,  
			$logica_id,   
			$orden_pos 	
	){  	
		$efectos_como_id = 0 ;
		try{
			$sql="
				INSERT INTO efectos_como
				SET 
					que_tipo =?,       
					que1_jugador =?,
					que1_valor  =?,
					que2_jugador =?,
					que2_valor  =?,
					logica_id   =?,
					orden_pos  =?
			";
			
			$stmt = $this->mysqli->prepare($sql);
			
			$stmt->bind_param('iiiiiii', 
				$que_tipo,
				$que1_jugador,
				$que1_valor,    
				$que2_jugador,
				$que2_valor,  
				$logica_id,   
				$orden_pos  			
			);
			
			if($stmt->execute()){
				$efectos_como_id = $stmt->insert_id;
			}
		} catch (Exception $e) {
			$resultado_json['ex_msg'] = $e->getMessage() ;
			$resultado_json['ex'] = true ;
		}	
		return $efectos_como_id;
	}
	
	private function soporte_efectos_insert(
			$id,
			$efectos_como_id,
			$nro_veces_tipo ,
			$nro_veces_valor
	){  	
		$soporte_efectos_id = false ;
		try{
			$sql="
				INSERT INTO soporte_efectos
				SET 
					id=?,
					efectos_como_id =?,
					nro_veces_tipo  =?,
					nro_veces_valor =?
			";
			
			
			/* Switch off auto commit to allow transactions*/
			//$this->mysqli->autocommit(FALSE);
			
			$stmt = $this->mysqli->prepare($sql);
			
			$stmt->bind_param('iisi', 
				$id,
				$efectos_como_id,
				$nro_veces_tipo ,
				$nro_veces_valor			
			);
			
			if($stmt->execute()){
				$soporte_efectos_id = true;
			}
		} catch (Exception $e) {
			$resultado_json['ex_msg'] = $e->getMessage() ;
			$resultado_json['ex'] = true ;
		}		
		return $soporte_efectos_id;
	}

	private function NuevoSoporteID(){
		$resultado = 0 ;
		try{		
			$qry = "
				IFNULL(
				SELECT MAX(id)
				FROM soporte_efectos
				,0) + 1
			" ;
			
			if($stmt = $this->mysqli->prepare($qry)){			
				
				$stmt->execute();

				/* bind variables to prepared statement */
				$stmt->bind_result(
					$resultado
				);

				/* fetch values */
				while ($stmt->fetch()) {
					
				}

				/* close statement */
				$stmt->close();			
				
			}  else {
				$resultado_json['ex_msg'] = $this->mysqli->error ;
				$resultado_json['ex'] = true ;
			}
		} catch (Exception $e) {
			$resultado_json['ex_msg'] = $e->getMessage() ;
			$resultado_json['ex'] = true ;
		}
		return $resultado ;	
	}
	
	function __construct($soporte_id) {
		global $mysqli;
		
		$this->mysqli = $mysqli;
		$this->soporte_id = $soporte_id;
		
		// Si NO recibo un ID de soporte, doy por hecho que estoy dando un alta.
		if($soporte_id == 0){
			$this->soporte_id = $this->NuevoSoporteID();
		}
	}	

	function __destruct() {
		$this->stmt->close();
	}	
	
	function AgregarEfectoCondicion(
		$que_tipo,
		$que1_jugador,
		$que1_valor,      
		$que2_jugador,
		$que2_valor,  
		$logica_id,   
		$orden_pos,
		$nro_veces_tipo ,
		$nro_veces_valor		
	){
		$resultado = 0 ;
		try{
			$efectos_como_id = $this->efectos_como_insert(
						$que_tipo,
						$que1_jugador,
						$que1_valor,       
						$que2_jugador,
						$que2_valor,  
						$logica_id,   
						$orden_pos 	
				); 
			if($efectos_como_id>0){
				if($this->soporte_efectos_insert(
							$this->soporte_id,
							$efectos_como_id,
							$nro_veces_tipo ,
							$nro_veces_valor
					)
				){
					$resultado = $efectos_como_id; 
				}
			}
		}catch(Exception $e) {
			$resultado_json['ex_msg'] = $e->getMessage() ;
			$resultado_json['ex'] = true ;
		}
		
		return $resultado ;
	}
}

 

// Utilizo la clase

$soporte_efecto_id = 0 ;
if(isset($_REQUEST['soporte_efecto_id'])){
	$soporte_efecto_id = $_REQUEST['soporte_efecto_id'] ;
}

$soporte = new ClsSoporte($soporte_efecto_id);

if(isset($_REQUEST['crear_nuevo'])){
	try{	
		
		$que_tipo = 0 ; 			if(isset($_REQUEST['que_tipo']))  $que_tipo = $_REQUEST['que_tipo'] ;
		$que1_jugador = 0 ; 		if(isset($_REQUEST['que1_jugador']))  $que1_jugador = $_REQUEST['que1_jugador'] ;
		$que1_valor = 0 ; 			if(isset($_REQUEST['que1_valor']))  $que1_valor = $_REQUEST['que1_valor'] ;
		$que2 = 0 ;        			if(isset($_REQUEST['que2']))  $que2 = $_REQUEST['que2'] ;
		$que2_jugador = 0 ;			if(isset($_REQUEST['que2_jugador']))  $que2_jugador = $_REQUEST['que2_jugador'] ;
		$que2_valor = 0 ;  			if(isset($_REQUEST['que2_valor']))  $que2_valor = $_REQUEST['que2_valor'] ;
		$logica_id = 0 ;   			if(isset($_REQUEST['logica_id']))  $logica_id = $_REQUEST['logica_id'] ;
		$orden_pos = 0 ;			if(isset($_REQUEST['orden_pos']))  $orden_pos = $_REQUEST['orden_pos'] ;
		$nro_veces_tipo = 'valor_fijo' ;	if(isset($_REQUEST['nro_veces_tipo']))  $nro_veces_tipo = $_REQUEST['nro_veces_tipo'] ;
		$nro_veces_valor = 1 ;		if(isset($_REQUEST['nro_veces_valor']))  $nro_veces_valor = $_REQUEST['nro_veces_valor'] ;

		$agregarEfectoCondicion_id = $soporte->AgregarEfectoCondicion(
			$que_tipo,
			$que1_jugador,
			$que1_valor,     
			$que2_jugador,
			$que2_valor,  
			$logica_id,   
			$orden_pos,
			$nro_veces_tipo ,
			$nro_veces_valor		
		);
		
		
		
		$resultado_json['agregarEfectoCondicion_id'] = $agregarEfectoCondicion_id ;
		
	}catch(Exception $e) {
		$resultado_json['ex_msg'] = $e->getMessage() ;
		$resultado_json['ex'] = true ;
	}
}
//////////////////////////////////////
// Resultado JSON
//////////////////////////////////////
echo json_encode($resultado_json);
?>