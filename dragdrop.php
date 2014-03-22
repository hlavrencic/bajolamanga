<?php
	function TipoContenedor($contenedor_id){
		try {
			$sql= "
				SELECT tipo_contenedor_id
				FROM contenedores_partidas
				WHERE contenedor_id = ?			
			";
		
			$stmt = $mysqli->prepare($sql);

			$stmt->bind_param('i', 
				$contenedor_id	
			);
			
			$stmt->execute();

			/* bind variables to prepared statement */
			$stmt->bind_result(
				$tipo_contenedor_id
			);

			/* fetch values */
			while ($stmt->fetch()) {
				
			}

			/* close statement */
			$stmt->close();
			
			return $tipo_contenedor_id;
		} catch(Exception $e){
			// no funka
		}
	}
	function EvalDraggable($contenedor_id,$etapa_id) {
		try {
			switch($etapa_id){
			    case 1:
					echo "i es igual a 0";
					break;
			}
		
		} catch(Exception $e){
			// no funka 
		}
	}
	function EvalDropable($contenedor_id,$etapa_id) {
		try {
			switch($etapa_id){
			    case 1:
					echo "i es igual a 0";
					break;
			}
		
		} catch(Exception $e){
			// no funka
		}
	}
?>