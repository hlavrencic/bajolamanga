<?php
class partidas_eventos
{
	private $query_success = TRUE;	
	private $stmt;    
	
	// Parametros
	public $mysqli ;
	public $partida_id;
	public $uid;
	public $contenedor_id;
		
	public function prepare($mysqli_in){  	
		$sql="
			INSERT INTO partidas_eventos
			SET 
				partida_id = ?,
				uid = ?,
				contenedor_id = ?
		";
		$this->mysqli = $mysqli_in ;
		
		/* Switch off auto commit to allow transactions*/
		$this->mysqli->autocommit(FALSE);
		
		$this->stmt = $this->mysqli->prepare($sql);
		
		$this->stmt->bind_param('sss', 
			$this->partida_id,
			$this->uid,
			$this->contenedor_id
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
	
	function __construct($mysqli) {
		$this->prepare($mysqli);
	}	

	function __destruct() {
		$this->stmt->close();
	}	
}
?>