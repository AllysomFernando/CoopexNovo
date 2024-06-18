<pre>
<?php

	
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	$id_menu = 6; #ID DO MENU
	$tabela  = "noticia_2022"; #TABELA PRINCIPAL
	$chave	 = "id_noticia"; #CAMPO CHAVE DA TABELA

	$id_registro = $_POST[$chave] ? $_POST[$chave] : "0";

	$_POST['publicado'] = isset($_POST['publicado']) ? 1 : 2;
	$_POST['uniao'] = isset($_POST['uniao']) ? 1 : 0;
	$_POST['dailymotion'] = isset($_POST['dailymotion']) ? $_POST['dailymotion'] : '';

	
	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");
	require_once("../../../php/utils.php");
	
	if(isset($_POST['excluido'])){
		if($_POST['excluido'] == 0){
			$sql = "UPDATE $tabela SET excluido = 0 WHERE id_noticia = $id_registro";
			$stm = $coopex->prepare($sql);
			$stm->execute();
		}
	}


	function tratar_texto($texto){
        $texto = str_replace('“', '"', $texto);
        $texto = str_replace('”', '"', $texto);
        $texto = str_replace('–', '-', $texto);
		$texto = str_replace('＄', '$', $texto);
		echo $texto;
        return $texto;
    }
	
	verificarPermissao($id_menu, $tabela, $chave, $id_registro);

	#VERFIFICA SE O FORMULÁRIO FOI ENVIADO
	if($_POST){
		extract($_POST);
		if(isset($excluir_registro)){
			excluirRegistro($tabela, $chave, $excluir_registro);
		} else {

			$enviado_para_aprovacao_reducao = false;

			#TRATAMENTOS DOS DADOS VINDOS DO FORMULÁRIOS
			#CAMPOS QUE DEVEM SER DESCARTADOS DO BIND
			$_POST['data_cadastro'] = converterData($_POST['data_publicacao'])." ".$_POST['hora_publicacao'];

			if(isset($_POST['data_alteracao'])){
				$_POST['data_alteracao'] = converterData($_POST['data_atualizacao'])." ".$_POST['hora_atualizacao'];
			} else {
				$_POST['data_alteracao'] = converterData($_POST['data_publicacao'])." ".$_POST['hora_publicacao'];
			}

			if(isset($imagem_galeria)){
				$imagem_galeria  	= $_POST['imagem_galeria'];
				$legenda_galeria 	= $_POST['legenda_galeria'];
				$credito_galeria 	= $_POST['credito_galeria'];
				$id_noticia_galeria = $_POST['id_noticia_galeria'];
			}
			

			unset($_POST['files']);
			unset($_POST['file']);
			unset($_POST['legenda_galeria']);
			unset($_POST['credito_galeria']);
			unset($_POST['data_publicacao']);
			unset($_POST['hora_publicacao']);
			unset($_POST['imagem_galeria']);
			unset($_POST['data_atualizacao']);
			unset($_POST['hora_atualizacao']);
			unset($_POST['id_noticia_galeria']);

			$base = "../../../../../images/";
			$diretorio = $_POST['diretorio'] ? $_POST['diretorio'] : date("Y/m/d");

			if(!$id_registro){
				$_POST['diretorio'] = date("Y/m/d");
								
				if(!is_dir("$base$diretorio")){
					if(!is_dir("$base".date("Y"))){
						mkdir("$base".date("Y"), 0777);
					}
					if(!is_dir("$base".date("Y")."/".date("m"))){
						mkdir("$base".date("Y")."/".date("m"), 0777);
					}
					if(!is_dir("$base".date("Y")."/".date("m")."/".date("d"))){
						mkdir("$base".date("Y")."/".date("m")."/".date("d"), 0777);
					}
				}

				/*$base_json = "../../../../../json/";
				if(!is_dir("$base_json$diretorio")){
					if(!is_dir("$base".date("Y"))){
						mkdir("$base".date("Y"), 0777);
					}
					if(!is_dir("$base".date("Y")."/".date("m"))){
						mkdir("$base".date("Y")."/".date("m"), 0777);
					}
					if(!is_dir("$base".date("Y")."/".date("m")."/".date("d"))){
						mkdir("$base".date("Y")."/".date("m")."/".date("d"), 0777);
					}
				}*/
			} else {
				//$sql = "SELECT * FROM noticia WHERE id_noticia = $id_registro";
				//$img = $coopex->query($sql);
			}

			//print_r($_POST);

			//echo $NUM = time();
			
			if(strlen($_POST['imagem_capa']) > 23){
				$base64_string = $_POST['imagem_capa'];
				$nome_arquivo = uniqid(time());
				$extensao = "jpg";
				base64ToImage($base64_string, "$base/$diretorio/$nome_arquivo.$extensao");

				$file = "$base/$diretorio/$nome_arquivo.$extensao";
				$image =  imagecreatefromjpeg($file);
				ob_start();
				imagejpeg($image,NULL,100);
				$cont =  ob_get_contents();
				ob_end_clean();
				imagedestroy($image);
				$content =  imagecreatefromstring($cont);
				$extensao = "webp";
				imagewebp($content, "$base/$diretorio/$nome_arquivo.$extensao");
				imagedestroy($content);

				$_POST['imagem_capa'] = $nome_arquivo;
			} else {

			}

			//echo "$base/$diretorio/$nome_arquivo.$extensao";

			$_POST['titulo'] = tratar_texto($_POST['titulo']);
			$_POST['resumo'] = tratar_texto($_POST['resumo']);
			$_POST['texto'] = tratar_texto($_POST['texto']);

			if(!$id_registro){
				$_POST['id_pessoa'] = $_SESSION['coopex']['usuario']['id_pessoa'];
			}
			

			//print_r($_POST);

			#VERFICA SE ESTÁ CADASTRANDO OU ALTERANDO
			//if(!$id_registro){$_POST['data_cadastro'] = date("Y-m-d H:i:s"); $_POST['id_pessoa'] = $_SESSION['coopex']['usuario']['id_pessoa'];}
			$sql = !$id_registro ? preparaSQL($_POST, $tabela) : preparaSQL($_POST, $tabela, $chave, $$chave); //MONTA A SQL PARA INSERT OU UPDATE
			$stm = $coopex->prepare($sql); //PREPARA A EXECUÇÃO DA QUERY
			//echo $sql;
			extract($_POST); //CONVERTE O POST EM VARIÁVEIS
			$dados = ''; foreach ($_POST as $key => $value) { if($value){ $stm->bindValue(":$key", ($$key)); $dados .= "$key => $value\n"; }} //PREPARA OS BINDS VINDOS POR POST 
			$registro = 0;
			$cadastro_sucesso = false;
			$operacao = !$id_registro ? 1 : 2;
			try { 
				$coopex->beginTransaction();
				$stm->execute();
				$last_id = $coopex->lastInsertId();
				$coopex->commit();
				$id_registro = !$id_registro ? $last_id : $id_registro;
				gravarLog($tabela, $id_registro, $operacao, $sql, $dados);
				$cadastro_sucesso = true;
			} catch(PDOException $e) {
				gravarLog($tabela, !$id_registro ? 0 : $id_registro, !$id_registro ? 1 : 2, $sql, $dados, $e->getMessage());
				//coopex->rollback();
				$cadastro_sucesso = false;
				print "Error!: " . $e->getMessage() . "</br>" . print_r($sql) . "</br>". $dados;
			} #--VERFICA SE O USUÁRIO ESTÁ CADASTRANDO OU ALTERANDO MONTA A SQL PARA INSERT OU UPDATE

			$_POST['id_noticia'] = $id_registro;

			print_r($imagem_galeria);

			if(isset($imagem_galeria)){
				for($i = 0; $i<count($imagem_galeria); $i++){

					if($id_noticia_galeria[$i]){
						$sql = "UPDATE noticia_galeria SET legenda = '".$legenda_galeria[$i]."', credito = '".$credito_galeria[$i]."' WHERE id_noticia_galeria = ".$id_noticia_galeria[$i];
						$coopex->query($sql);

						$excluir_galeria = implode(",", $id_noticia_galeria);

						$sql = "DELETE FROM noticia_galeria  WHERE id_noticia_galeria NOT IN ($excluir_galeria) AND id_noticia = $id_registro";
						$coopex->query($sql);

						$sql = "UPDATE noticia_galeria SET legenda = '".$legenda_galeria[$i]."', credito = '".$credito_galeria[$i]."' WHERE id_noticia_galeria = ".$id_noticia_galeria[$i];
						$coopex->query($sql);
					} else {

						$base64_string = $imagem_galeria[$i];
						$nome_arquivo = uniqid(time());
						$extensao = "jpg";
						base64ToImage($base64_string, "$base/$diretorio/$nome_arquivo.$extensao");
						$legenda = $legenda_galeria[$i];
						$credito = $credito_galeria[$i];

						$file = "$base/$diretorio/$nome_arquivo.$extensao";
						$image =  imagecreatefromjpeg($file);
						ob_start();
						imagejpeg($image,NULL,100);
						$cont =  ob_get_contents();
						ob_end_clean();
						imagedestroy($image);
						$content =  imagecreatefromstring($cont);
						$extensao = "webp";
						imagewebp($content, "$base/$diretorio/$nome_arquivo.$extensao");
						imagedestroy($content);

						$sql = "INSERT INTO `noticia_galeria`(`id_noticia`, `imagem`, `legenda`, `credito`) VALUES ($id_registro, '$nome_arquivo', '$legenda', '$credito')";
						$coopex->query($sql);
					}
				}
				//$_POST['galeria'][] =  array("arquivo" => "$nome_arquivo", "legenda" => "$legenda", "credito" => "$credito");
			}

			$sql = "SELECT imagem, legenda, credito FROM noticia_galeria WHERE id_noticia = ".$id_registro;
			$galeria = $coopex->query($sql);
			while($row = $galeria->fetch(PDO::FETCH_OBJ)){
				$_POST['galeria'][] = $row;
			}


			if(isset($_POST['capa'])){
				/*DEFINIR COMO CAPA*/
				$sql = "SELECT * FROM relevancia where id_noticia <> $id_registro";
				$res = $coopex->query($sql);

				$coopex->query("delete from relevancia");
				$coopex->query("insert into relevancia (relevancia, id_noticia) values (1, $id_registro)");

				$i=2;
				while($row = $res->fetch(PDO::FETCH_OBJ)){
					$coopex->query("insert into relevancia (relevancia, id_noticia) values ($i, $row->id_noticia)");
					$i++;
				}
			}

			/* NOTICIAS RELACIONADAS*/
			if($_POST['palavra_chave']){
				$p_chave = $_POST['palavra_chave'];
				$sql = "SELECT
							id_noticia,
							titulo,
							resumo,
							editoria,
							id_editoria,
							cidade,
							data_cadastro,
							imagem_capa,
							diretorio,
							if(video_terra or youtube or facebook, 1, 0) as video  
						FROM
							noticia_2022
							INNER JOIN editoria USING ( id_editoria )
							INNER JOIN cidade USING ( id_cidade ) 
						WHERE
							palavra_chave = '$p_chave' 
						AND
							id_noticia <> $id_registro
						ORDER BY
							id_noticia DESC";

				$capa = $coopex->query($sql);
				while($row = $capa->fetch(PDO::FETCH_OBJ)){
					$_POST['relacionada'][] = $row;
				}
			}
			/* NOTICIAS RELACIONADAS*/

			if($_POST['id_cidade']){
				$sql = "SELECT cidade FROM cidade WHERE id_cidade = ".$_POST['id_cidade'];
				$cidade = $coopex->query($sql);
				$row = $cidade->fetch(PDO::FETCH_OBJ);
				$_POST['cidade'] = $row->cidade;
			}

			if($_POST['id_editoria']){
				$sql = "SELECT editoria FROM editoria WHERE id_editoria = ".$_POST['id_editoria'];
				$editoria = $coopex->query($sql);
				$row = $editoria->fetch(PDO::FETCH_OBJ);
				$_POST['editoria'] = utf8_encode($row->editoria);
			}
			
			/* ÚLTIMAS NOTÍCIAS HOME*/
			unset($json);
			$sql = "SELECT
						id_noticia,
						n.titulo,
						b.titulo AS blog,
						uri,
						n.data_cadastro,
						cidade,
						id_editoria,
						imagem_capa,
						diretorio
					FROM
						noticia_2022 n
						LEFT JOIN cidade USING ( id_cidade )
						LEFT JOIN blog b USING ( id_blog ) 
					WHERE
						publicado = 1 
						AND n.excluido = 0 AND galeria = 0 
						and capa = 0
					ORDER BY
						data_cadastro DESC 
						LIMIT 10";
			$ultimas = $coopex->query($sql);
			while($row = $ultimas->fetch(PDO::FETCH_OBJ)){
				$json[] = $row;
			}
			$json_data = json_encode($json);
			file_put_contents("../../../../../json/home/ultimas.json", $json_data);
			/* ÚLTIMAS NOTÍCIAS HOME*/



			


			/* SINAL*/
			/*unset($json);
			$sql = "SELECT
						* 
					FROM
						sinal";
			$ultimas = $coopex->query($sql);
			while($row = $ultimas->fetch(PDO::FETCH_OBJ)){
				$json[] = $row;
			}
			$json_data = json_encode($json);
			file_put_contents("../../../../../json/sinal.json", $json_data);*/
			/* SINAL*/

			/* ÚLTIMAS NOTÍCIAS*/
			unset($json);
			$sql = "SELECT
						id_noticia,
						n.titulo,
						b.titulo AS blog,
						uri,
						resumo,
						editoria,
						id_editoria,
						cidade,
						n.data_cadastro,
						imagem_capa,
						diretorio,
						if(video_terra or youtube or facebook, 1, 0) as video  
					FROM
						noticia_2022 n
						LEFT JOIN cidade USING ( id_cidade )
						LEFT JOIN editoria USING ( id_editoria )
						LEFT JOIN blog b USING ( id_blog ) 
					WHERE
						publicado = 1 
					AND
						n.excluido = 0 AND galeria = 0 AND galeria = 0	
					ORDER BY
						data_cadastro DESC 
						LIMIT 100";
			$ultimas = $coopex->query($sql);
			while($row = $ultimas->fetch(PDO::FETCH_OBJ)){
				$json[] = $row;
			}
			$json_data = json_encode($json);
			file_put_contents("../../../../../json/ultimas.json", $json_data);
			/* ÚLTIMAS NOTÍCIAS*/

			/* CAPA */
			unset($json);
			$sql = "SELECT
						id_noticia,
						titulo,
						resumo,
						editoria,
						id_editoria,
						cidade,
						data_cadastro,
						imagem_capa,
						diretorio,
						if(video_terra or youtube or facebook, 1, 0) as video 
					FROM
						noticia_2022 n
						INNER JOIN editoria USING ( id_editoria )
						INNER JOIN cidade USING ( id_cidade )
						INNER JOIN relevancia USING ( id_noticia ) 
					WHERE
						publicado = 1 
					AND
						n.excluido = 0 AND galeria = 0	
					ORDER BY
						relevancia 
						LIMIT 30";
			$capa = $coopex->query($sql);
			while($row = $capa->fetch(PDO::FETCH_OBJ)){
				$json[] = $row;
				$id_noticia_not_in[] = $row->id_noticia;
			}
			$json_data = json_encode($json);
			file_put_contents("../../../../../json/home/capa.json", $json_data);
			/* CAPA */

			/* EDITORIA CAPA*/
			if($id_editoria){
				unset($json);
				$id_noticia_capa = implode(",", $id_noticia_not_in);
				$sql = "SELECT
							id_noticia,
							titulo,
							resumo,
							editoria,
							id_editoria,
							cidade,
							data_cadastro,
							imagem_capa,
							diretorio,
							if(video_terra or youtube or facebook, 1, 0) as video  
						FROM
							noticia_2022 n
							INNER JOIN editoria USING ( id_editoria )
							INNER JOIN cidade USING ( id_cidade ) 
						WHERE
							id_editoria = $id_editoria 
						AND
							id_noticia NOT IN ($id_noticia_capa)	
						AND
							publicado = 1 
						AND
							n.excluido = 0 AND galeria = 0
						ORDER BY
							id_noticia DESC 
							LIMIT 6";
				$capa = $coopex->query($sql);
				while($row = $capa->fetch(PDO::FETCH_OBJ)){
					$json[] = $row;
				}
				$json_data = json_encode($json);
				file_put_contents("../../../../../json/home/editoria_$id_editoria.json", $json_data);
			} /* EDITORIA CAPA*/


			


			/* EDITORIA*/
			if($id_editoria){
				unset($json);
				$sql = "SELECT
							id_noticia,
							titulo,
							resumo,
							editoria,
							id_editoria,
							cidade,
							data_cadastro,
							imagem_capa,
							diretorio,
							if(video_terra or youtube or facebook, 1, 0) as video  
						FROM
							noticia_2022 n
							INNER JOIN editoria USING ( id_editoria )
							INNER JOIN cidade USING ( id_cidade ) 
						WHERE
							id_editoria = $id_editoria 
						AND
							publicado = 1 
						AND
							n.excluido = 0 AND galeria = 0	
						ORDER BY
							id_noticia DESC 
							LIMIT 50";
				$capa = $coopex->query($sql);
				while($row = $capa->fetch(PDO::FETCH_OBJ)){
					$json[] = $row;
				}
				$json_data = json_encode($json);
				file_put_contents("../../../../../json/editoria/$id_editoria.json", $json_data);
			}/* EDITORIA*/


			


			/* EDITORIA MAIS LIDAS*/
			if($id_editoria){
				unset($json);
				$sql = "SELECT
							id_noticia,
							titulo,
							resumo,
							editoria,
							id_editoria,
							cidade,
							data_cadastro,
							imagem_capa,
							diretorio,
							if(video_terra or youtube or facebook, 1, 0) as video  
						FROM
							noticia_2022 n
							INNER JOIN editoria USING ( id_editoria )
							INNER JOIN cidade USING ( id_cidade ) 
						WHERE
							data_cadastro >= ( cast( now( ) AS date ) - INTERVAL 4 DAY ) 
						AND
							id_editoria = $id_editoria
						AND
							publicado = 1 
						AND
							n.excluido = 0 AND galeria = 0	
						ORDER BY
							acesso DESC 
							LIMIT 3";
				$capa = $coopex->query($sql);
				while($row = $capa->fetch(PDO::FETCH_OBJ)){
					$json[] = $row;
				}
				$json_data = json_encode($json);
				file_put_contents("../../../../../json/editoria/mais_lidas_$id_editoria.json", $json_data);
			}/* EDITORIA MAIS LIDAS*/


			

			/* BLOGS*/
			/*unset($json);
			$sql = "SELECT
						id_noticia,
						titulo,
						resumo,
						nome,
						data_cadastro,
						imagem_capa,
						diretorio,
						id_blog,
						uri 
					FROM
						noticia
						INNER JOIN blog USING ( id_blog ) 
					ORDER BY
						id_noticia DESC 
						LIMIT 2";
			$capa = $coopex->query($sql);
			while($row = $capa->fetch(PDO::FETCH_OBJ)){
				$json[] = $row;
			}
			$json_data = json_encode($json);
			file_put_contents("../../../../../json/home/blog.json", $json_data);*/
			/* BLOGS*/



			/* ESPECIAL*/
			unset($json);
			$sql = "SELECT
						id_noticia,
						titulo,
						resumo,
						editoria,
						id_editoria,
						cidade,
						data_cadastro,
						imagem_capa,
						diretorio,
						if(video_terra or youtube or facebook, 1, 0) as video  
					FROM
						noticia_2022 n
						INNER JOIN editoria USING ( id_editoria )
						INNER JOIN cidade USING ( id_cidade ) 
					WHERE
	
						publicado = 1 
					AND
						n.excluido = 0 AND galeria = 0
					ORDER BY
						id_noticia DESC 
						LIMIT 10";
			$capa = $coopex->query($sql);
			while($row = $capa->fetch(PDO::FETCH_OBJ)){
				$json[] = $row;
			}
			$json_data = json_encode($json);
			file_put_contents("../../../../../json/home/especial.json", $json_data);
			/* EDITORIA*/


			/* ESPECIAL*/
			if($id_especial){
				unset($json);
				$sql = "SELECT
							id_noticia,
							titulo,
							resumo,
							editoria,
							id_editoria,
							cidade,
							data_cadastro,
							imagem_capa,
							diretorio,
							if(video_terra or youtube or facebook, 1, 0) as video  
						FROM
							noticia_2022 n
							INNER JOIN editoria USING ( id_editoria )
							INNER JOIN cidade USING ( id_cidade ) 
						WHERE
							id_especial = $id_especial
						AND	
							publicado = 1 
						AND
							n.excluido = 0 AND galeria = 0
						ORDER BY
							id_noticia DESC";
				$capa = $coopex->query($sql);
				while($row = $capa->fetch(PDO::FETCH_OBJ)){
					$json[] = $row;
				}
				$json_data = json_encode($json);
				file_put_contents("../../../../../json/especial/$id_especial.json", $json_data);
			}
			/* EDITORIA*/


			




			/* MAIS LIDAS HOME*/
			unset($json);
			$sql = "SELECT
						id_noticia,
						titulo,
						resumo,
						editoria,
						id_editoria,
						cidade,
						data_cadastro,
						imagem_capa,
						diretorio,
						if(video_terra or youtube or facebook, 1, 0) as video  
					FROM
						noticia_2022 n
						INNER JOIN editoria USING ( id_editoria )
						INNER JOIN cidade USING ( id_cidade ) 
					WHERE
						data_cadastro >= ( cast( now( ) AS date ) - INTERVAL 4 DAY )
					AND
						publicado = 1 
					AND
						n.excluido = 0 AND galeria = 0	
						AND data_cadastro < now()  
					ORDER BY
						acesso DESC 
						LIMIT 3";
			$capa = $coopex->query($sql);
			while($row = $capa->fetch(PDO::FETCH_OBJ)){
				$json[] = $row;
			}
			$json_data = json_encode($json);
			file_put_contents("../../../../../json/home/mais_lidas.json", $json_data);
			/* MAIS LIDAS HOME*/


			





			/* MAIS LIDAS*/
			unset($json);
			$sql = "SELECT
						id_noticia,
						titulo,
						resumo,
						editoria,
						id_editoria,
						cidade,
						data_cadastro,
						imagem_capa,
						diretorio,
						if(video_terra or youtube or facebook, 1, 0) as video  
					FROM
						noticia_2022 n
						INNER JOIN editoria USING ( id_editoria )
						INNER JOIN cidade USING ( id_cidade ) 
					WHERE
						data_cadastro >= ( cast( now( ) AS date ) - INTERVAL 4 DAY ) 
					AND
						publicado = 1 
					AND
						n.excluido = 0 AND galeria = 0
						AND data_cadastro < now() 	
					ORDER BY
						acesso DESC 
						LIMIT 30";
			$capa = $coopex->query($sql);
			while($row = $capa->fetch(PDO::FETCH_OBJ)){
				$json[] = $row;
			}
			$json_data = json_encode($json);
			file_put_contents("../../../../../json/mais_lidas.json", $json_data);
			/* MAIS LIDAS*/



			/* MAIS LIDAS toledo*/
			unset($json);
			$sql = "SELECT
						id_noticia,
						titulo,
						resumo,
						editoria,
						id_editoria,
						cidade,
						data_cadastro,
						imagem_capa,
						diretorio,
						if(video_terra or youtube or facebook, 1, 0) as video  
					FROM
						noticia_2022 n
						INNER JOIN editoria USING ( id_editoria )
						INNER JOIN cidade USING ( id_cidade ) 
					WHERE
						data_cadastro >= ( cast( now( ) AS date ) - INTERVAL 4 DAY ) 
					AND
						publicado = 1 
					AND
						n.excluido = 0 AND galeria = 0
						AND data_cadastro < now() 
					AND (
						id_cidade IN (2, 21, 30, 12, 76, 449, 42)
						OR (uniao = 1)
					)
					ORDER BY
						acesso DESC 
						LIMIT 30";
			$capa = $coopex->query($sql);
			while($row = $capa->fetch(PDO::FETCH_OBJ)){
				$json[] = $row;
			}
			$json_data = json_encode($json);
			file_put_contents("../../../../../json/uniao/mais_lidas.json", $json_data);
			/* MAIS LIDAS toledo*/


			if($id_blog){
				/* MAIS LIDAS HOME*/
				unset($json);
				$sql = "SELECT
							id_noticia,
							n.titulo,
							resumo,
							n.data_cadastro,
							imagem_capa,
							diretorio,
							b.titulo as nome,
							uri,
							id_blog 
						FROM
							noticia_2022 n
							INNER JOIN blog b USING ( id_blog ) 
						WHERE
							n.data_cadastro >= ( cast( now( ) AS date ) - INTERVAL 130 DAY ) 
							AND id_blog = $id_blog 
						AND
							publicado = 1 
						AND
							n.excluido = 0 AND galeria = 0	
						AND ativo = 1	
						ORDER BY
							acesso DESC 
							LIMIT 3";
				$capa = $coopex->query($sql);
				while($row = $capa->fetch(PDO::FETCH_OBJ)){
					$json[] = $row;
					$id_noticia_not_in[] = $row->id_noticia;
				}
				$json_data = json_encode($json);
				file_put_contents("../../../../../json/blog/mais_lidas_$id_blog.json", $json_data);
				/* MAIS LIDAS HOME*/

				/* BLOGS */
				unset($json);
				$id_noticia_capa = implode(",", $id_noticia_not_in);
				$sql = "SELECT
							id_noticia,
							n.titulo,
							resumo,
							n.data_cadastro,
							imagem_capa,
							diretorio,
							b.titulo as nome,
							uri,
							id_blog 
						FROM
							noticia_2022 n
							INNER JOIN blog b USING ( id_blog )
						WHERE
							id_blog = $id_blog 
						
						AND
							publicado = 1 
						AND
							n.excluido = 0 AND galeria = 0	
						AND ativo = 1	
						ORDER BY
							n.data_cadastro DESC 
							LIMIT 30";
				$capa = $coopex->query($sql);
				while($row = $capa->fetch(PDO::FETCH_OBJ)){
					$json[] = $row;
				}
				$json_data = json_encode($json);
				file_put_contents("../../../../../json/blog/ultimas_$id_blog.json", $json_data);
				/* BLOGS*/
					
				/* BLOGS CAPA*/
				unset($json);
				
				$sql = "SELECT
							id_noticia,
							n.titulo,
							resumo,
							n.data_cadastro,
							imagem_capa,
							diretorio,
							b.titulo as nome,
							uri,
							id_blog 
						FROM
							noticia_2022 n
							INNER JOIN blog b USING ( id_blog )
						WHERE
						
							publicado = 1 
						AND
							n.excluido = 0 AND galeria = 0	
						AND ativo = 1	
						ORDER BY
							n.data_cadastro DESC 
							LIMIT 30";
				$capa = $coopex->query($sql);
				while($row = $capa->fetch(PDO::FETCH_OBJ)){
					$json[] = $row;
				}
				
				$json_data = json_encode($json);
				file_put_contents("../../../../../json/home/blog.json", $json_data);
				/* BLOGS CAPA */

				/* ÚLTIMAS NOTÍCIAS BLOGS*/
				unset($json);
				$sql = "SELECT
						id_noticia,
						n.titulo,
						resumo,
						n.data_cadastro,
						imagem_capa,
						diretorio,
						b.titulo as nome,
						uri,
						id_blog 
					FROM
						noticia_2022 n
						INNER JOIN blog b USING ( id_blog )
					WHERE
						publicado = 1 
					AND
						n.excluido = 0 AND galeria = 0	
					AND ativo = 1	
					ORDER BY
						n.data_cadastro DESC 
						LIMIT 30";
				$ultimas = $coopex->query($sql);
				while($row = $ultimas->fetch(PDO::FETCH_OBJ)){
					$json[] = $row;
				}
				$json_data = json_encode($json);
				file_put_contents("../../../../../json/ultimas_blogs.json", $json_data);
				/* ÚLTIMAS NOTÍCIAS BLOGS*/


			}


			$json_data = json_encode($_POST);
			file_put_contents("../../../../../json/noticias/$id_registro.json", $json_data);

			//require_once("../../../php/sitemap_news.php");

			/*$fp = fopen('$base_json$diretorio.json', 'w');
			fwrite($fp, json_encode($_POST));
			fclose($fp);*/

			/*$json_data = json_encode($posts);
			file_put_contents('myfile.json', $json_data);*/
			

			if($cadastro_sucesso){
				//if($_SESSION['coopex']['usuario']['id_usuario'] > 1){
					echo "<script>parent.cadastroOK($operacao)</script>";
				//}
			} else {
				echo "<script>parent.cadastroFalha($operacao)</script>";
			}
		}
	}

	
?>