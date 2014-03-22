<?php
class clsEtapas
{
	private $mysqli;
	private $partida_id;
	private $usuario_id;
	
	private $interrupcion = false;
	private $interrupcion_titulo = 'titulo';
	private $interrupcion_contenido = 'contenido';
	private $interrupcion_boton_cancelar = false ;
	
	function __construct(
		$partida_id,
		$usuario_id) 
	{
		global $mysqli ;
		
		$this->mysqli = $mysqli;
		$this->partida_id = $partida_id ;
		$this->usuario_id = $usuario_id ;
	}	
	
	function Interrupcion(){
		return $this->interrupcion;
	}
	
	function Interrupcion_titulo(){
		return $this->interrupcion_titulo;
	}
	
	function Interrupcion_contenido(){
		return $this->interrupcion_contenido;
	}
	
	function Interrupcion_boton_cancelar(){
		return $this->interrupcion_boton_cancelar;
	}
	
	function EtapaDescripcion(){
		$resultado = '' ;
		try{		
			$qry = "
				SELECT nombre AS resultado
				FROM `etapas_partidas` AS ep
				WHERE 1
					AND ep.`etapa_id` = ?				
			" ;
			
			if($stmt = $this->mysqli->prepare($qry)){
			
				$stmt->bind_param('i', 
					$this->Etapa()
				);			
				
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
				echo 'mysql_error: ' . $this->mysqli->error ; 
			}
		} catch (Exception $e) {
			echo 'Excepción capturada: ',  $e->getMessage(), "\n";
		}
		return $resultado ;	
	}
	
	function ExisteCartaEn($tipo_contenedor,$tipo_carta){
		$resultado = 0 ;
		try{	
			$qry = "
				SELECT (COUNT(*) > 0) AS resultado
				FROM `partidas_cartas` AS pc
				JOIN `usuarios_cartas` AS uc 
					ON uc.`carta_id` = pc.`carta_id`
				JOIN `tipos_cartas` AS tr 
					ON tr.`id` = uc.`tipo`
				JOIN `contenedores_partidas` AS cp 
					ON cp.`contenedor_id` = pc.`contenedor_id`
				JOIN `tipos_contenedores` AS tc
					ON tc.`id` = cp.`tipo_contenedor_id`
				WHERE 1
					AND pc.`partida_id` = ?
					AND pc.`usuario_id` = ?
					AND tc.`tipo` = ?
					AND tr.`nombre` = ?					
			" ;
			
			if($stmt = $this->mysqli->prepare($qry)){
			
				$stmt->bind_param('iiss', 
					$this->partida_id,
					$this->usuario_id,
					$tipo_contenedor,
					$tipo_carta
				);			
				
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
				echo 'mysql_error: ' . $this->mysqli->error ; 
			}
		} catch (Exception $e) {
			echo 'Excepción capturada: ',  $e->getMessage(), "\n";
		}
		return $resultado ;
	}
	
	function Etapa($etapa_id = NULL){
		$resultado = 0 ;
		try{
			if(is_null($etapa_id )){
				$qry = "
					SELECT etapa_id AS resultado
					FROM partidas AS p
					WHERE 1
						AND p.id = ?
				" ;
				
				if($stmt = $this->mysqli->prepare($qry)) {
					
					$stmt->bind_param('i', 
						$this->partida_id
					);			
					
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
				} else {
					echo 'mysql_error: ' . $this->mysqli->error ; 
				}
			} else {
				$qry = "
					UPDATE `partidas` AS p 
					SET etapa_id = ?
					WHERE 1
						AND p.`id` = ?
				" ;
				
				if($stmt = $this->mysqli->prepare($qry)) {
					$stmt->bind_param('ii', 
						$etapa_id,
						$this->partida_id
					);			
					
					$resultado = $stmt->execute();

					$stmt->close();					
				} else {
					echo 'mysql_error: ' . $this->mysqli->error ; 
				}		
			}
		} catch (Exception $e) {
			echo 'Excepción capturada: ',  $e->getMessage(), "\n";
		}
		return $resultado ;
	}
	
	private function MoverCartasContId(
		$contenedor_id,
		$tipo_contenedor_destino,
		$orden_destino
	){
		try{
			list($orden_origen,$tipo_contenedor_origen) = $this->ObtenerOrden_Tipo($contenedor_id);
			echo "$orden_origen:" . $orden_origen . "." ;
			return $this->MoverCartas(
				$tipo_contenedor_origen,
				$tipo_contenedor_destino,
				$orden_origen,
				$orden_destino,
				1
			) ;
		} catch (Exception $e) {
			echo 'Excepción capturada: ',  $e->getMessage(), "\n"; 
		}
		return false ;
	}
	
	private function MoverCartas(
		$tipo_contenedor_origen,
		$tipo_contenedor_destino,
		$orden_origen,
		$orden_destino,
		$cantidad_cartas
	){
		/*
		Orden:
		>=0: Se utiliza el orden especificado
		-1: Se utiliza el ultimo orden existente o disponible
		-2: Se utiliza el primer orden existente o disponible
		-3: Se obtiene un orden aleatorio existente
		*/
		$resultado = false ;
		try{
			switch($orden_origen){
				case -1:
					$qry = "
						SELECT 
						  IFNULL(MAX(pc.`contenedor_orden`) ,0)
						FROM
						  partidas_cartas AS pc 
						  JOIN tipos_contenedores AS tc 
							ON tc.`id` = pc.`tipo_contenedor_id` 
						WHERE 1 
						  AND pc.`partida_id` = ? 
						  AND pc.`usuario_id` = ? 
						  AND tc.`tipo` = ? 
					" ;
					if($stmt = $this->mysqli->prepare($qry)) {
						
						$stmt->bind_param('isi', 
							$this->partida_id,
							$this->usuario_id,
							$tipo_contenedor_destino
						);			
						
						$stmt->execute();
						
						$stmt->bind_result(
							$orden_origen
						);

						/* fetch values */
						while ($stmt->fetch()) {
							
						}
						$stmt->close();	
					}				
				break;
				case -2:
					$qry = "
						SELECT 
						  IFNULL(MIN(pc.`contenedor_orden`),0) 
						FROM
						  partidas_cartas AS pc 
						  JOIN tipos_contenedores AS tc 
							ON tc.`id` = pc.`tipo_contenedor_id` 
						WHERE 1 
						  AND pc.`partida_id` = ? 
						  AND pc.`usuario_id` = ? 
						  AND tc.`tipo` = ?
					" ;
					if($stmt = $this->mysqli->prepare($qry)) {
						
						$stmt->bind_param('isi', 
							$this->partida_id,
							$this->usuario_id,
							$tipo_contenedor_destino
						);			
						
						$stmt->execute();
						
						$stmt->bind_result(
							$orden_origen
						);

						/* fetch values */
						while ($stmt->fetch()) {
							
						}
						$stmt->close();	
					}				
				break;
				case -3:
					$qry = "
						SELECT 
						  IFNULL(pc.`contenedor_orden`,0) 
						FROM
						  partidas_cartas AS pc 
						  JOIN tipos_contenedores AS tc 
							ON tc.`id` = pc.`tipo_contenedor_id` 
						WHERE 1 
						  AND pc.`partida_id` = ? 
						  AND pc.`usuario_id` = ? 
						  AND tc.`tipo` = ?
						ORDER BY RAND()
						LIMIT 1
					" ;
					if($stmt = $this->mysqli->prepare($qry)) {
						
						$stmt->bind_param('isi', 
							$this->partida_id,
							$this->usuario_id,
							$tipo_contenedor_destino
						);			
						
						$stmt->execute();
						
						$stmt->bind_result(
							$orden_origen
						);

						/* fetch values */
						while ($stmt->fetch()) {
							
						}
						$stmt->close();	
					}				
				break;
				default:
				
			}
		
			switch($orden_destino){
				case -1:
					$qry = "SELECT ObtenerOrdenLibreEn(?,?,?) " ;
					if($stmt = $this->mysqli->prepare($qry)) {
						
						$stmt->bind_param('isi', 
							$this->partida_id,
							$tipo_contenedor_destino,
							$this->usuario_id
						);			
						
						$stmt->execute();
						
						$stmt->bind_result(
							$orden_destino
						);

						/* fetch values */
						while ($stmt->fetch()) {
							
						}
						$stmt->close();	
					}				
				break;
				case -2:
				
				break;
				case -3:
				
				break;
				default:
				
			}

					
			$arrQry = Array();
			
			$arrQry[] = "SET @partida_id = $this->partida_id ;" ;
			$arrQry[] = "SET @usuario_id = $this->usuario_id ;" ;
			$arrQry[] = "SET @tipo_contenedor_origen = '$tipo_contenedor_origen' ;" ;
			$arrQry[] = "SET @tipo_contenedor_destino = '$tipo_contenedor_destino' ;" ;
			$arrQry[] = "SET @orden_origen = $orden_origen ;" ;
			$arrQry[] = "SET @orden_destino = $orden_destino ;" ;
			
			/*
			$arrQry[] = "
				SET @carta_id = IFNULL((
				SELECT 
				  carta_id 
				FROM
				  `partidas_cartas` AS pc 
				  JOIN `contenedores_partidas` AS cp 
					ON cp.`contenedor_id` = pc.`contenedor_id` 
				  JOIN `tipos_contenedores` AS tc 
					ON tc.`id` = cp.`tipo_contenedor_id` 
				WHERE 1 
				  AND pc.`partida_id` = @partida_id 
				  AND pc.`usuario_id` = @usuario_id 
				  AND tc.`tipo` = @tipo_contenedor_origen
				ORDER BY RAND()
				LIMIT 1
				),0);			
			" ;
			
			$arrQry[] = "
				SET @contenedor_id_nuevo = IFNULL(
				(SELECT cp.`contenedor_id`
				FROM `contenedores_partidas` AS cp
				JOIN `tipos_contenedores` AS tc 
					ON tc.id = cp.tipo_contenedor_id
				LEFT JOIN `partidas_cartas` AS pc
					ON pc.`contenedor_id` = cp.`contenedor_id`
				WHERE 1
					AND tc.`tipo` = @tipo_contenedor_destino
					AND ISNULL(pc.`contenedor_id`)
				ORDER BY pc.`contenedor_id`
				LIMIT 1
				  ),0) ;			
			" ;
			*/
			
			/*
			$arrQry[] = "
				UPDATE partidas_cartas AS pc
				JOIN tipos_contenedores AS tc
					ON tc.id = pc.tipo_contenedor_id
				SET pc.contenedor_id = ObtenerIdcontenedor(@tipo_contenedor_destino,@orden_destino) ,
					pc.contenedor_orden = @orden_destino,
					pc.tipo_contenedor_id = (SELECT id FROM tipos_contenedores WHERE tipo = @tipo_contenedor_destino)
				WHERE 1
				  AND pc.`partida_id` = @partida_id 
				  AND pc.`usuario_id` = @usuario_id 
				  AND ( 0 
					#OR pc.`carta_id` = @carta_id  
					OR (tc.tipo = @tipo_contenedor_origen 
						AND pc.contenedor_orden = @orden_origen
					) 
					OR (pc.contenedor_id = ObtenerIdcontenedor(@tipo_contenedor_origen,@orden_origen)
						AND pc.contenedor_id > 0
					)
				);			
			" ;
			*/
			 
			$resultado = true ;
			for($i=1;$i<=$cantidad_cartas;$i++){
				foreach ($arrQry as $value) {
					echo $value ;
					if($result = $this->mysqli->query($value)) {
						//mysqli_free_result($result);
					}else {
						$resultado = false  ;
						echo 'mysql_error: ' . $this->mysqli->error ; 
					}
				}									
			} 
			return $resultado ;
		} catch (Exception $e) {
			echo 'Excepción capturada: ',  $e->getMessage(), "\n";
		}
		return false;
		
	}
	
	private function ObtenerOrden_Tipo($contenedor_id){
		$orden = 0 ;
		$tipo = '' ;
		
		
		try{
			$qry = "
				SELECT 
					cp.contenedor_id - MIN(cp1.`contenedor_id`) + 1 AS orden ,
					tc.tipo
				FROM contenedores_partidas AS cp
				JOIN contenedores_partidas AS cp1
					ON cp1.`tipo_contenedor_id` = cp.`tipo_contenedor_id`
				JOIN tipos_contenedores AS tc
					ON tc.id = cp.tipo_contenedor_id
					AND cp.contenedor_id = ? ; 				
			" ;
			
			if($stmt = $this->mysqli->prepare($qry)){
			
				$stmt->bind_param('i', 
					$contenedor_id
				);			
				
				echo "contenedor_id: $contenedor_id";
				
				$stmt->execute();

				/* bind variables to prepared statement */
				$stmt->bind_result(
					$orden,
					$tipo
				);

				/* fetch values */
				while ($stmt->fetch()) {
					echo "orden:" . $orden . "." ;
				}

				/* close statement */
				$stmt->close();			
				
			}  else {
				echo 'mysql_error: ' . $this->mysqli->error ; 
			}		
		} catch (Exception $e) {
			echo 'Excepción capturada: ',  $e->getMessage(), "\n";
		}
		return array($orden,$tipo) ;
	}
	
	function ProcesarEtapa($contenedor_id){
		try{
			switch($this->Etapa()){
				case 1:
					// Decidir quien empieza la partida
					$turno_jugador_id = $this->TurnoJugador();
					switch($contenedor_id){
						case 0:
							$this->interrupcion = true;
							$this->interrupcion_titulo = 'TURNO JUGADOR';
							
							if($this->usuario_id == $turno_jugador_id) {
								$this->interrupcion_contenido = 'Vos empezás';
								
							} else {
								$this->interrupcion_contenido = 'Empieza el otro jugador';
							}
							return true ;
						break;
						case -1:
							$tipo_contenedor_origen = 'mazo' ;
							$tipo_contenedor_destino = 'mano' ;
							$orden_origen = -3 ; // Random
							$orden_destino = -1 ; // 1er lugar libre
							$cantidad_cartas = '4' ;
							if($this->MoverCartas(
								$tipo_contenedor_origen,
								$tipo_contenedor_destino,
								$orden_origen,
								$orden_destino,
								$cantidad_cartas)
							){
								if($this->Etapa(2)){
									return $this->ProcesarEtapa(0);
								}
							}							
						break;
					}
				case 2:
					
					if($this->ExisteCartaEn('mano','criatura')){
						switch($contenedor_id){
							case 0:
								$this->interrupcion = true;
								$this->interrupcion_titulo = '';
								$this->interrupcion_contenido = 'Acepta cartas ?';
								$this->interrupcion_boton_cancelar = true;	
								
								return true;
							break;
							case -1:
								if($this->Etapa(3)){
									return $this->ProcesarEtapa(0);
								}
							break;
							case -2:
								// Descarto
								$tipo_contenedor_origen = 'mano' ;
								$tipo_contenedor_destino = 'pozo' ;
								$orden_origen = -3 ; // Random
								$orden_destino = -1 ; // 1er lugar libre
								$cantidad_cartas = '4'  ;
								if($this->MoverCartas(
									$tipo_contenedor_origen,
									$tipo_contenedor_destino,
									$orden_origen,
									$orden_destino,
									$cantidad_cartas)
								){
									// Vuelvo a repartir
									$tipo_contenedor_origen = 'mazo' ;
									$tipo_contenedor_destino = 'mano' ;
									$orden_origen = -3 ; // Random
									$orden_destino = -1 ; // 1er lugar libre
									$cantidad_cartas = '4' ;
									if($this->MoverCartas(
										$tipo_contenedor_origen,
										$tipo_contenedor_destino,
										$orden_origen,
										$orden_destino,
										$cantidad_cartas)
									){
										return $this->ProcesarEtapa(0);
									}								
								}
							break;
						}		
					}else{
						switch($contenedor_id){
							case 0:
								// Falta verificar si se quedo sin cartas
								$this->interrupcion = true;
								$this->interrupcion_titulo = '';
								$this->interrupcion_contenido = 'No hay Criaturas para seleccionar';
								$this->interrupcion_boton_cancelar = false;

								return true;								
							break;
							case -1:
								// Descarto
								$tipo_contenedor_origen = 'mano' ;
								$tipo_contenedor_destino = 'pozo' ;
								$orden_origen = -3 ; // Random
								$orden_destino = -1 ; // 1er lugar libre
								$cantidad_cartas = '4'  ;
								if($this->MoverCartas(
									$tipo_contenedor_origen,
									$tipo_contenedor_destino,
									$orden_origen,
									$orden_destino,
									$cantidad_cartas)
								){
									// Vuelvo a repartir
									$tipo_contenedor_origen = 'mazo' ;
									$tipo_contenedor_destino = 'mano' ;
									$orden_origen = -3 ; // Random
									$orden_destino = -1 ; // 1er lugar libre
									$cantidad_cartas = '4' ;
									if($this->MoverCartas(
										$tipo_contenedor_origen,
										$tipo_contenedor_destino,
										$orden_origen,
										$orden_destino,
										$cantidad_cartas)
									){
										return $this->ProcesarEtapa(0);
									}								
								}				
							break;				
						}
					}
				break;
				case 3:
					if($this->ExisteCartaEn('activa','criatura')){
						if($this->Etapa(4)){
							return $this->ProcesarEtapa(0);
						}						
					} else {
						switch($contenedor_id){	
							case 0: 
								// Espero al Jugador
								return true;
							break;
							default:
								$tipo_contenedor_destino = 'activa' ;
								$orden_destino = -1 ;
								if($this->MoverCartasContId(
									$contenedor_id,
									$tipo_contenedor_destino,
									$orden_destino
								)) {
									if($this->Etapa(4)){
										return $this->ProcesarEtapa(0);
									}
								}
						}					
					}
					/*
					switch($contenedor_id){
						case 0 :
							// Nada
						case ($contenedor_id > 0):
							// Ajustar valores de carta según nivel de la criatura.

							if($this->ExisteCartaEnMano()){
								if($this->Etapa(6)){
									return $this->ProcesarEtapa(0);
								}							
							}
						break;
					}
					*/					
				break;
				case 4:
					if($this->ExisteCartaEn('mano','criatura')){
						switch($contenedor_id){
							case -1: //Ignorar
								if($this->Etapa(5)){
									return $this->ProcesarEtapa(0);
								}							
							break;								
							case 0: 
								// Espero al Jugador
								return true;
							break;	
							default: 
								$tipo_contenedor_destino = 'deposito' ;
								$orden_destino = -1 ;
								if($this->MoverCartasContId(
									$contenedor_id,
									$tipo_contenedor_destino,
									$orden_destino
								)) {
									if($this->Etapa(5)){
										return $this->ProcesarEtapa(0);
									}
								}						
						}
					} else {
						if($this->Etapa(5)){
							return $this->ProcesarEtapa(0);
						}					
					}				
				break;
				case 5:
					if($this->ExisteCartaEn('mano','genetica')){
						switch($contenedor_id){
							case -1: //Ignorar
								if($this->Etapa(6)){
									return $this->ProcesarEtapa(0);
								}							
							break;	
							case 0: 
								// Espero al Jugador
								return true;
							break;
							default:
								$tipo_contenedor_destino = 'soporte' ;
								$orden_destino = -1 ;
								if($this->MoverCartasContId(
									$contenedor_id,
									$tipo_contenedor_destino,
									$orden_destino
								)) {
									if($this->Etapa(6)){
										return $this->ProcesarEtapa(0);
									}
								}							
						}					
					} else {
						if($this->Etapa(6)){
							return $this->ProcesarEtapa(0);
						}						
					}
				break;
				case 6:
					if(false){
					
					
					}else{
						switch($contenedor_id){
							case -1: //Ignorar
								if($this->Etapa(6)){
									return $this->ProcesarEtapa(0);
								}							
							break;	
							case 0: 
								$this->interrupcion = true;
								$this->interrupcion_titulo = 'CONTINUAR ?';
								$this->interrupcion_contenido = 'Acepta cartas ?'; 
								$this->interrupcion_boton_cancelar = true;	
								return true;
							break;
							default:
								$tipo_contenedor_destino = 'soporte' ;
								$orden_destino = -1 ;
								if($this->MoverCartasContId(
									$contenedor_id,
									$tipo_contenedor_destino,
									$orden_destino
								)) {
									if($this->Etapa(6)){
										return $this->ProcesarEtapa(0);
									}
								}							
						}						
					}
				break;
				case 7:
				
				break;
				case 8:
				
				break;
				case 9:
				
				break;
				default:				
			}
		} catch (Exception $e) {
			echo 'Excepción capturada: ',  $e->getMessage(), "\n";
		}
		return false;
	}
	
	private function UltimoOrdenEn($tipo_contenedor){
		$resultado = 0 ;
		try{		
			$qry = "
				SELECT IFNULL(MAX(pc.`contenedor_orden`),0)
				FROM partidas_cartas AS pc
				JOIN `tipos_contenedores` AS tc
					ON tc.`id` = pc.`tipo_contenedor_id`
				WHERE 1
					AND pc.partida_id = ?
					AND tc.`tipo` = ? 				
			" ;
			
			if($stmt = $this->mysqli->prepare($qry)){
			
				$stmt->bind_param('is', 
					$this->partida_id,
					$tipo_contenedor
				);			
				
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
				echo 'mysql_error: ' . $this->mysqli->error ; 
			}
		} catch (Exception $e) {
			echo 'Excepción capturada: ',  $e->getMessage(), "\n";
		}
		return $resultado ;
	}	
	
	function TurnoJugador($cambiar = false){
		$resultado = 0 ;
		try{
			$turno_usuario_id = 0 ;
			$cambiar_qry = "" ;
			if($cambiar){
				$cambiar_qry = " AND p.turno_usuario_id <> pc.usuario_id " ;
			}
			$qry = "
				SELECT 
					IF(p.turno_usuario_id > 0,
						p.turno_usuario_id
						,pc.usuario_id
					) AS turno
				FROM partidas AS p
				JOIN partidas_cartas AS pc
					ON pc.partida_id = p.id
				WHERE 1
					AND p.id = ?
					$cambiar_qry
				GROUP BY pc.usuario_id
				ORDER BY RAND()
				LIMIT 1
			" ;				
			
			if($stmt = $this->mysqli->prepare($qry)){
				
				$stmt->bind_param('i', 
					$this->partida_id
				);			
				
				$stmt->execute();
	
				/* bind variables to prepared statement */
				$stmt->bind_result(
					$turno_usuario_id
				);

				/* fetch values */
				while ($stmt->fetch()) {
					
				}
							
				/* close statement */
				$stmt->close();			
				
			}  else {
				echo 'mysql_error: ' . $this->mysqli->error ; 
			}			
		
			if($cambiar){
				$qry = "
					UPDATE partidas AS p
					SET p.turno_usuario_id = ?
					WHERE 1
						AND p.id = ?
				" ;		
				
				if($stmt = $this->mysqli->prepare($qry)){
					
					$stmt->bind_param('ii', 
						$turno_usuario_id,
						$this->partida_id
					);			
					
					$stmt->execute() ;
					
					if($this->mysqli->affected_rows == 1) {
						// Devuelvo el resultado solo si se que se actualizo correctamente
						$resultado = $turno_usuario_id ;		
					}
					
					$stmt->close();							
				}  else {
					echo 'mysql_error: ' . $this->mysqli->error ; 
				}
			} else {
				$resultado = $turno_usuario_id ;
			}		
		} catch (Exception $e) {
			echo 'Excepción capturada: ',  $e->getMessage(), "\n";
		}
		return $resultado ;
	}	
}
?>