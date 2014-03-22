<?php


require_once('bdd_coneccion.inc.php');

$usuario_id = $_GET['usuario_id'] ;
$partida_id = $_GET['partida_id'] ;
$contenedor_id = $_GET['contenedor_id'] ;

$sql = " 
	SELECT 
		NOT ISNULL(pc.carta_id) AS contenedor_lleno,
		IFNULL(pc.carta_id,0) AS carta_id,
		IFNULL(pc.vida,0) AS vida,
		IFNULL(pc.especialidad,0) AS especialidad,
		IFNULL(uc.ataque1,c.ataque1) AS ataque1,
		IFNULL(uc.ataque2,c.ataque2) AS ataque2,
		IFNULL(uc.ataque3,c.ataque3) AS ataque3,
		IFNULL(uc.efecto_id,c.efecto_id) AS efecto_id,
		IFNULL(uc.soporte_id,c.soporte_id) AS soporte_id,
		IFNULL(ic.imagen_url,'') AS imagen,
		IFNULL(uc.genes_necesarios,0) AS genes_necesarios,
		IFNULL(uc.genes_propios,0) AS genes_propios,
		IFNULL(uc.tipo,0) AS tipo,
		(1
			AND ed.drop_contenedor_tipo_id = cp.tipo_contenedor_id
			AND ed.drop_carta_tipo_id = IFNULL(uc.tipo,0)
			AND p.turno_usuario_id = pj.usuario_id
			AND p.turno_usuario_id = ?
		) AS dropeable,
		(1
			AND ed.drag_contenedor_tipo_id = cp.tipo_contenedor_id
			AND ed.drag_carta_tipo_id = IFNULL(uc.tipo,0)
			AND p.turno_usuario_id = pj.usuario_id
			AND p.turno_usuario_id = ?
		) AS draggable
	FROM partidas p	
	#JOIN etapas_partidas ep ON ep.etapa_id = p.etapa_id
	LEFT JOIN etapas_dragdrop ed ON ed.etapa_id = p.etapa_id
	JOIN partidas_jugadores pj ON pj.partida_id = p.id
		AND pj.orden = ((?-1) DIV 14) +1  
	JOIN contenedores_partidas cp ON cp.contenedor_id + (pj.orden-1)*14 = ?
	LEFT JOIN partidas_cartas pc ON pc.partida_id = p.id
		AND pj.usuario_id = pc.usuario_id
		AND cp.contenedor_id = pc.contenedor_id
	LEFT JOIN usuarios_cartas uc ON uc.carta_id = pc.carta_id
	LEFT JOIN cartas c ON c.carta_original_id = uc.carta_original_id
	LEFT JOIN imagenes_cartas ic ON ic.imagen_id = uc.imagen_id 
	WHERE 1
		AND p.id = ? 
";

if ($stmt = $mysqli->prepare($sql)) {
					
	$stmt->bind_param('iiiii',
		$usuario_id,
		$usuario_id,
		$contenedor_id,
		$contenedor_id, 
		$partida_id
	);
	
	if($stmt->execute()) { 
		/* bind variables to prepared statement */
		$stmt->bind_result(
			$contenedor_lleno,
			$carta_id,
			$vida, 
			$especialidad,
			$ataque1,
			$ataque2,
			$ataque3,
			$efecto_id,
			$soporte_id,
			$imagen,
			$genes_necesarios,
			$genes_propios,
			$tipo,
			$dropeable,
			$draggable
		);

		/* fetch values */
		while ($stmt->fetch()) {
			$arr = array(
				'contenedor_lleno' => $contenedor_lleno
				,'carta_id' => $carta_id
				,'imagen' => $imagen
				, 'vida' => $vida
				, 'ataque1' => $ataque1
				, 'ataque2' => $ataque2
				, 'ataque3' => $ataque3
				, 'efecto_id' => $efecto_id
				, 'efecto_texto' => 'efecto'
				, 'soporte_id' => $soporte_id
				, 'soporte_texto' => 'soporte' 
				, 'tipo' => $tipo 
				, 'especialidad' => $especialidad 
				, 'genes_necesarios' => $genes_necesarios
				, 'genes_propios' => $genes_propios
				, 'draggable' => $draggable
				, 'dropeable' => $dropeable
			); 
			header('Content-Type: application/json');
			echo json_encode($arr);		
		} 	
	}else{
		header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
	}

	/* close statement */
	$stmt->close();
}else{
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
}


?>
