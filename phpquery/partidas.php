<?php
class partidas
{
	private $query_success = TRUE;	
	private $stmt;    
	
	// Parametros
	public $mysqli ;
	public $id;
	public $etapa_id;
	public $turno_usuario_id 	;
		
	public function prepare(){  	
		$sql="
			INSERT INTO partidas
			SET 
				id = ?,
				etapa_id = ?,
				turno_usuario_id = ? 
			ON DUPLICATE KEY UPDATE
				etapa_id = ?,
				turno_usuario_id = ?			
		";
		
		/* Switch off auto commit to allow transactions*/
		$this->mysqli->autocommit(FALSE);
		
		$this->stmt = $this->mysqli->prepare($sql);
		
		$this->stmt->bind_param('sssss', 
			$this->id,
			$this->etapa_id,
			$this->turno_usuario_id 	,
			$this->etapa_id,
			$this->turno_usuario_id 	
		);
		
		return $stmt;
	}

	public function execute(){
		$resultado = $this->stmt->execute();
		if (!$resultado) {
			$query_success = FALSE;
		}
		return $resultado;
	}

	public function commit(){
		/* Commit or rollback transaction */
		$resultado = false ;
		if ($query_success) {
			$resultado = $this->mysqli->commit(); 
		} else {
			$this->mysqli->rollback();
		}
		
		return $resultado ;
	}	
	
	function __construct($mysqli_in) {
		$this->mysqli = $mysqli_in ;
		$this->prepare();
	}	

	function __destruct() {
		$this->stmt->close();
	}	
}
?>