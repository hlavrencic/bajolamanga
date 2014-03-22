<?php
class partidas_cartas
{
	private $tabla;
	private $mysqli ;
	
	// Parametros
	public $partida_id;
	public $vida;
	public $carta_id 	;
	public $especialidad 	;
	public $nivel 	;
 
	public function Leer(){
		/* prepare statement */
		$resultado = false ;
		
		$sql = "
			SELECT 
				carta_id  ,
				partida_id ,
				vida ,
				especialidad ,
				nivel 
			FROM $this->tabla
			WHERE carta_id = ?
				AND partida_id = ?
				OR TRUE 
			LIMIT 1
		";
		
		if ($stmt = $this->mysqli->prepare($sql)) {
						
			$stmt->bind_param('ss', 
				$this->carta_id, 
				$this->partida_id		
			);
			
			$stmt->execute();

			/* bind variables to prepared statement */
			$stmt->bind_result(
				$this->carta_id, 
				$this->partida_id,
				$this->vida,
				$this->especialidad,
				$this->nivel
			);

			/* fetch values */
			while ($stmt->fetch()) {
				$resultado = true ;
			}

			/* close statement */
			$stmt->close();
		}
		return $resultado ;
	}
	
	public function Guardar(){  	
		$sql="
			INSERT INTO $this->tabla
			SET 
				carta_id = ? ,
				partida_id = ?,
				vida = ?,
				especialidad = ?,
				nivel = ?
				
			ON DUPLICATE KEY UPDATE
				vida = ?,
				especialidad = ?,
				nivel = ?
		";
		
		if($stmt = $this->mysqli->prepare($sql)){ 
			$stmt->bind_param('ssssssss',  
				$this->carta_id, 
				$this->partida_id,
				$this->vida,
				$this->especialidad,
				$this->nivel,
				$this->vida,
				$this->especialidad,
				$this->nivel			
			);

			$resultado = $stmt->execute();
			$stmt->close();
			return $resultado ;		
		}
	}
	
	function __construct() {
		global $mysqli ;
		$this->mysqli = $mysqli;
		$this->tabla = "partidas_cartas" ;
	}	

}

?>