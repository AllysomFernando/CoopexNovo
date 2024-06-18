<?php

	$id_menu = 6;
	$chave	 = "id_noticia";


	@ini_set( 'upload_max_size' , '512M' );
	@ini_set( 'post_max_size', '512M');
	@ini_set( 'max_execution_time', '300' );

	if(isset($_GET['id'])){
		$$chave = $_GET['id'];

		//CARREGA DADOS DA REOFERTA
		$sql = "SELECT
					*,
					date( data_cadastro ) AS data_publicacao,
					time( data_cadastro ) AS hora_publicacao,
					date( data_cadastro ) AS data_atualizacao,
					time( data_cadastro ) AS hora_atualizacao 
				FROM
					noticia_2022 
				WHERE
					id_noticia = ".$_GET['id'];
		$res = $coopex->query($sql);
		$dados = $res->fetch(PDO::FETCH_OBJ);

		$dados->titulo = str_replace('"', "&quot;", $dados->titulo);
		$dados->resumo = str_replace('"', "&quot;", $dados->resumo);
	} else {
		$$chave = 0;
	}

	//print_r($dados);

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/summernote/summernote.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/cropperjs/cropper.css">
<link rel="stylesheet" media="screen, print" href="css/fa-solid.css">
<script src="js/core.js"></script>

<style type="text/css">
	.img-galeria{
		border: solid #eee 1px;
		padding: 5px;
		margin: 0 5px;
	}
</style>

<script type="text/javascript">
	 var imagem_cortada;
</script>

<main id="js-page-content" role="main" class="page-content">

	<?php
		if(!isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][1])){
	?>
	<div class="alert border-danger bg-transparent text-secondary fade show" role="alert">
		<div class="d-flex align-items-center">
			<div class="alert-icon">
				<span class="icon-stack icon-stack-md">
					<i class="base-7 icon-stack-3x color-danger-900"></i>
					<i class="fal fa-times icon-stack-1x text-white"></i>
				</span>
			</div>
			<div class="flex-1">
				<span class="h5 color-danger-900">Seu usuário não possui permissão para acessar esta tela</span>
			</div>
			<a href="javascript:solicitarPermissao()" class="btn btn-outline-danger btn-sm btn-w-m">Solicitar acesso</a>
		</div>
	</div>
	<?php		
			exit;
		}
	?>

	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Notícias</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-file-alt'></i> Cadastro de Notícias
		</h1>
	</div>

	<iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 100%; background-color: #fff; top: 0; left: 0; height: 250px"></iframe>

	<form class="needs-validation" id="form_noticia" novalidate="" method="post" target="dados" action="modulos/noticia/cadastro/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave?>" value="<?php echo $$chave ? $$chave : 0?>">
		<input type="hidden" name="diretorio" value="<?php echo isset($dados->diretorio) ? ($dados->diretorio) : ""?>">
		<div class="row">
			<div class="col-xl-12">
			  	<div id="panel-2" class="panel">
				  	<div class="panel-hdr">
						<h2>
							1. Detalhes
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">

						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="form-row">

									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Cidade</label>
										<?php
											$sql = "SELECT
														* 
													FROM
														cidade";

											$cidade = $coopex->query($sql);
										?>
										<select id="id_cidade" name="id_cidade" class="select2 form-control">
											<option value="">Selecione a Cidade</option>
										<?php
											while($row = $cidade->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if($dados->id_cidade == $row->id_cidade){
													$selecionado = 'selected=""';
												}
										?>	
											<option <?php echo isset($dados->id_cidade) ? $selecionado : ""?> value="<?php echo $row->id_cidade?>"><?php echo ($row->cidade)?></option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											Selecione a Cidade
										</div>
									</div>

									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Editoria</label>
										<?php
											$sql = "SELECT
														* 
													FROM
														editoria
													WHERE
														ativa = 1";

											$editoria = $coopex->query($sql);
										?>
										<select id="id_editoria" name="id_editoria" class="select2 form-control">
											<option value="">Selecione a Editoria</option>
										<?php
											while($row = $editoria->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if($dados->id_editoria == $row->id_editoria){
													$selecionado = 'selected=""';
												}
										?>	
											<option <?php echo isset($dados->id_editoria) ? $selecionado : ""?> value="<?php echo $row->id_editoria?>"><?php echo ($row->editoria)?></option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											Selecione a Editoria
										</div>
									</div>

									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Especial</label>
										<?php
											$sql = "SELECT
														* 
													FROM
														especial";

											$especial = $coopex->query($sql);
										?>
										<select id="id_especial" name="id_especial" class="select2 form-control">
											<option value="">Selecione o Especial</option>
										<?php
											while($row = $especial->fetch(PDO::FETCH_OBJ)){

												//print_r($row);

												$selecionado = '';
												if($dados->id_especial == $row->id_especial){
													$selecionado = 'selected=""';
												}
										?>	
											<option <?php echo isset($dados->id_especial) ? $selecionado : ""?> value="<?php echo $row->id_especial?>"><?php echo ($row->especial)?></option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											Selecione o Especial
										</div>
									</div>


									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Blog</label>
										<?php
											$sql = "SELECT
														* 
													FROM
														blog
													WHERE
														ativo = 1";

											$blog = $coopex->query($sql);
										?>
										<select id="id_blog" name="id_blog" class="select2 form-control" onchange="obrigatoriedade()">
											<option value="">Selecione o Blog</option>
										<?php
											while($row = $blog->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if($dados->id_blog == $row->id_blog){
													$selecionado = 'selected=""';
												}
										?>	
											<option <?php echo isset($dados->id_blog) ? $selecionado : ""?> value="<?php echo $row->id_blog?>"><?php echo ($row->titulo)?></option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											Selecione o Blog
										</div>
									</div>

									

								</div>	

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<div class="row">
			<div class="col-xl-12">
			  <div id="panel-2" class="panel">
				 	<div class="panel-hdr">
						<h2>
							2. Matéria
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="form-row">
									<div class="col-md-12 mb-12">
										<label class="form-label" for="validationCustom02">Título <span class="text-danger">*</span></label>
										<input maxlength="110" type="text" class="form-control" id="titulo" name="titulo" placeholder="" value="<?php echo isset($dados->titulo) ? ($dados->titulo) : ""?>" required>
									</div>
								</div>
								<br>
								<div class="form-row">
									<div class="col-md-12 mb-12">
										<label class="form-label" for="validationCustom02">Linha Fina <span class="text-danger mr-2">*</span></label> (Meta Descrição)
										<input maxlength="300" type="text" class="form-control" id="resumo" name="resumo" placeholder="" value="<?php echo isset($dados->resumo) ? ($dados->resumo) : ""?>" required>
									</div>
								</div>
								<br>
								<div class="form-row">
									<div class="col-md-12 mb-12">
										<label class="form-label" for="validationCustom02">Frase-chave <span class="text-danger">*</span></label>
										<input type="text" class="form-control" id="palavra_chave" name="palavra_chave" placeholder="" value="<?php echo isset($dados->palavra_chave) ? ($dados->palavra_chave) : ""?>" required>
									</div>
								</div>
								<br>
								<div class="form-row">
									<div class="col-md-3 mb-12">
										<label class="form-label" for="validationCustom02">Dailymotion ID - (Vídeo)</label>
										<input type="text" class="form-control" name="dailymotion" placeholder="" value="<?php echo isset($dados->dailymotion) ? ($dados->dailymotion) : ""?>">
									</div>

									<div class="col-md-3 mb-12">
										<label class="form-label" for="validationCustom02">Facebook ID</label>
										<input type="text" class="form-control" name="facebook" placeholder="" value="<?php echo isset($dados->facebook) ? ($dados->facebook) : ""?>">
									</div>
									<div class="col-md-3 mb-12">
										<label class="form-label" for="validationCustom02">YouTube ID</label>
										<input type="text" class="form-control" name="youtube" placeholder="" value="<?php echo isset($dados->youtube) ? ($dados->youtube) : ""?>">
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Ao Vivo</label>
										<?php
											$sql = "SELECT
														* 
													FROM
														sinal";

											$especial = $coopex->query($sql);
										?>
										<select id="id_sinal" name="id_sinal" class="select2 form-control">
											<option value="">Selecione o Sinal</option>
										<?php
											while($row = $especial->fetch(PDO::FETCH_OBJ)){

												//print_r($row);

												$selecionado = '';
												if($dados->id_sinal == $row->id_sinal){
													$selecionado = 'selected=""';
												}
										?>	
											<option <?php echo isset($dados->id_sinal) ? $selecionado : ""?> value="<?php echo $row->id_sinal?>"><?php echo ($row->sinal)?></option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											Selecione o Especial
										</div>
									</div>
								</div>
								<br>
								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Reporter </label>
										<input type="text" class="form-control" name="reporter" placeholder="" value="<?php echo isset($dados->reporter) ? ($dados->reporter) : ""?>">
									</div>

								</div>
								<div class="form-row" style="display: none;">
									<div class="col-md-3 mb-3">
										<div class="custom-control custom-checkbox" id="uniao_check">
											<input <?php echo isset($dados->uniao) && $dados->uniao == 1 ? 'checked=""' : ""?>  type="checkbox" class="custom-control-input" id="invalidCheck3" value="1" name="uniao">
											<label class="custom-control-label" for="invalidCheck3">União</label>
										</div>
									</div>
								</div>			
								<br>
								<div class="form-row">
									<div class="col-md-12 mb-12">
										<label class="form-label" for="validationCustom02">Texto<span class="text-danger">*</span></label>
										<textarea class="js-summernote" id="saveToLocal" name="texto"><?php echo isset($dados->texto) ? ($dados->texto) : ""?></textarea>
										<!-- <textarea id="summernote" name="editordata"></textarea> -->
                                        <div class="mt-3">
                                            <div class="custom-control custom-checkbox d-none">
                                                <input type="checkbox" class="custom-control-input" id="autoSave" checked="checked">
                                                <label class="custom-control-label" for="autoSave">Salvar texto automaticamente <span class="fw-300">(a cada 3 segundos)</span></label>
                                            </div>
                                        </div>
									</div>
								</div>
								
						
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xl-12">
			  <div id="panel-2" class="panel">
				 	<div class="panel-hdr">
						<h2>
							3. Imagens
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
                        <div class="panel-content">
                            <!-- Content -->
                            <div class="">
                                <div class="row">
                                    <div class="col-xl-9">
                                        <!-- <h3>Demo:</h3> -->
                                        <div class="img-container">
                                            <img id="image" src="img/catve_padrao.png?asdf" alt="Picture">
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <!-- <h3>Preview:</h3> -->
                                        <div class="docs-preview clearfix" >
                                            <div class="img-preview preview-lg"></div>
                                        </div>

                                        <div id="botoes" style="display: none;">
                                            <a class="btn btn-primary col-10 mb-3" href="javascript:imagem_capa()">Definir como capa</a>
                                            <a class="btn btn-success col-10 mb-5" href="javascript:imagem_galeria();">Incluir na Galeria</a>
											<a class="btn btn-default col-10" id="download" href="javascript:void(0);" download="cropped.jpg">Download</a>
										</div>

                                        <script type="text/javascript">
                                        	
                                        	function imagem_capa(){
                                        		$("#imagem_de_capa").attr("src", imagem_cortada);
                                        		$("#imagem_de_capa_input").val(imagem_cortada);
                                        		$("#galeria_imagens").show();
                                        		$("#botoes").hide();
                                        	}
                                        	function imagem_galeria(){
                                        		$('#galeria').append('<div class="col-3 mb-3 img-galeria"><img width=100% src="'+imagem_cortada+'" /><input name="legenda_galeria[]" class="form-control" placeholder="Legenda" type="text" /><input name="credito_galeria[]" class="form-control" placeholder="Crédito" type="text" /><input type="hidden" value="'+imagem_cortada+'" name="imagem_galeria[]"><button onclick="excluir_galeria(this)" type="button" class="form-control mt-1">Excluir</button></div>');


                                        		$("#galeria_imagens").show();
                                        		$("#botoes").hide();
                                        	}

                                        	function excluir_galeria(btn){
                                        		$(btn).parent().remove();
                                        	}

                                        </script>	

                                      
                                        <!-- <h3>Data:</h3> -->
                              
                                    </div>
                                </div>

                                
                                <div class="row">
                                    <div class="col-lg-9 docs-buttons">
                                        <!-- <h3>Toolbar:</h3> -->
										<label class="btn btn-success btn-upload" for="inputImage" title="Upload image file">
                                                <input type="file" class="sr-only" id="inputImage" name="file" accept=".jpg,.jpeg,.png,.gif,.bmp,.tiff">
                                                <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="Clique para selecionar a imagem">
                                                    <span class="fas fa-image mr-1"></span> Upload
                                                </span>
                                            </label>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary" data-method="zoom" data-option="0.1" title="Zoom In">
                                                <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="Aumentar Zoom">
                                                    <span class="fas fa-search-plus"></span>
                                                </span>
                                            </button>
                                            <button type="button" class="btn btn-primary" data-method="zoom" data-option="-0.1" title="Zoom Out">
                                                <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="Diminuir Zoom">
                                                    <span class="fas fa-search-minus"></span>
                                                </span>
                                            </button>
                                        </div>

                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary" data-method="move" data-option="-10" data-second-option="0" title="Move Left">
                                                <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="Mover para esquerda">
                                                    <span class="fas fa-arrow-left"></span>
                                                </span>
                                            </button>
                                            <button type="button" class="btn btn-primary" data-method="move" data-option="10" data-second-option="0" title="Move Right">
                                                <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="Mover para direita">
                                                    <span class="fas fa-arrow-right"></span>
                                                </span>
                                            </button>
                                            <button type="button" class="btn btn-primary" data-method="move" data-option="0" data-second-option="-10" title="Move Up">
                                                <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="Mover para cima">
                                                    <span class="fas fa-arrow-up"></span>
                                                </span>
                                            </button>
                                            <button type="button" class="btn btn-primary" data-method="move" data-option="0" data-second-option="10" title="Move Down">
                                                <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="Mover para baixo">
                                                    <span class="fas fa-arrow-down"></span>
                                                </span>
                                            </button>
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary" data-method="rotate" data-option="-45" title="Rotate Left">
                                                <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="Rotacionar -45º">
                                                    <span class="fas fa-undo"></span>
                                                </span>
                                            </button>
                                            <button type="button" class="btn btn-primary" data-method="rotate" data-option="45" title="Rotate Right">
                                                <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="Rotacionar 45º">
                                                    <span class="fas fa-redo"></span>
                                                </span>
                                            </button>
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary" data-method="scaleX" data-option="-1" title="Flip Horizontal">
                                                <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="Espelhar horizonalmente">
                                                    <span class="fas fa-arrows-h"></span>
                                                </span>
                                            </button>
                                            <button type="button" class="btn btn-primary" data-method="scaleY" data-option="-1" title="Flip Vertical">
                                                <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="Espelhar Verticalmente">
                                                    <span class="fal fa-arrows-v"></span>
                                                </span>
                                            </button>
                                        </div>
                                        
                                        
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary" data-method="reset" title="Reset">
                                                <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="Posição original">
                                                    <span class="fas fa-sync"></span>
                                                </span>
                                            </button>
                                            
                                        </div>

                                        <div class="btn-group btn-group-crop">
                                            <button type="button" class="btn btn-success" data-method="getCroppedCanvas" data-option="{ &quot;width&quot;: 960, &quot;height&quot;: 640 }">
                                                <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="Clique para cortar a imagem">
                                                    Cortar Imagem
                                                </span>
                                            </button>
                                            
                                        </div>

                                    </div>
                                    <!-- /.docs-buttons -->
                                    
                                    <!-- /.docs-toggles -->
                                </div>
                                <hr>
                                <div class="form-row" id="galeria_imagens" style="display: <?php echo isset($dados->imagem_capa) ? "block" : "none"?>;">
									<div class="col-4 mb-3">
										<label class="form-label" for="validationCustom03">Imagem de Capa<span class="text-danger">*</span></label>
										<img width="100%" id="imagem_de_capa" src="<?php echo isset($dados->imagem_capa) ? "/images/".$dados->diretorio."/".$dados->imagem_capa.".jpg" : ""?>">
										<input type="hidden" id="imagem_de_capa_input" name="imagem_capa" value="<?php echo isset($dados->imagem_capa) ? ($dados->imagem_capa) : ""?>">
										<input id="legenda_capa" placeholder="Legenda" type="text" class="form-control" name="legenda_capa" placeholder="" value="<?php echo isset($dados->legenda_capa) ? ($dados->legenda_capa) : ""?>">
										<input id="credito_capa" placeholder="Crédito" type="text" class="form-control" name="credito_capa" placeholder="" value="<?php echo isset($dados->credito_capa) ? ($dados->credito_capa) : ""?>">
									</div>

									<div class="col-8 mb-3 pl-2">
										<label class="form-label" for="validationCustom03">Galeria de Imagens</label>
										<div id="galeria" class="row">
											<?
												if(isset($_GET['id'])){
													$sql = "SELECT
																* 
															FROM
																noticia_galeria
															WHERE
																id_noticia = ".$_GET['id'];

													$galeria = $coopex->query($sql);
													while($row = $galeria->fetch(PDO::FETCH_OBJ)){
											?>
												<div class="col-3 mb-3 img-galeria">
													<img width=100% src="<?="/images/".$dados->diretorio."/".$row->imagem.".jpg"?>" />
													<input name="id_noticia_galeria[]" class="form-control" placeholder="Legenda" type="hidden" value="<?=$row->id_noticia_galeria?>" />
													<input name="legenda_galeria[]" class="form-control" placeholder="Legenda" type="text" value="<?=$row->legenda?>" />
													<input name="credito_galeria[]" class="form-control" placeholder="Crédito" type="text" value="<?=$row->credito?>"/>
													<input type="hidden" value="'+imagem_cortada+'" name="imagem_galeria[]">
													<button onclick="excluir_galeria(this)" type="button" class="form-control mt-1">Excluir</button>
												</div>
											<?			
													}
												}
											?>
										</div>
									</div>
								</div>
          
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xl-12">
			  <div id="panel-2" class="panel">
				<div class="panel-hdr">
						<h2>
							4. SEO
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
							
					<div class="panel-container show">


						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="form-row form-group">
									<div class="col-12 mr-lg-auto">
										<h1>Título</h1>
										<div id="frase_chave_titulo" class="has-popover d-flex align-items-center">
	                                    	<div class="d-inline-block rounded-circle mr-2 bg-danger-500 bg-danger-gradient" style="width:15px; height:15px;"></div>
	                                    	<strong>Frase-chave no Título</strong>
	                                    	<span class="ml-3"></span>
	                                    </div>

	                                    <hr>


	                                    <h1>Linha fina</h1>
	                                    <div id="frase_chave_resumo" class="has-popover d-flex align-items-center">
	                                    	<div class="d-inline-block rounded-circle mr-2 bg-danger-500 bg-danger-gradient" style="width:15px; height:15px;"></div>
	                                    	<strong>Frase-chave na Linha fina</strong>
	                                    	<span class="ml-3"></span>
	                                    </div>

										<div id="linha_fina_tamanho" class="has-popover d-flex align-items-center">
	                                    	<div class="d-inline-block rounded-circle mr-2 bg-danger-500 bg-danger-gradient" style="width:15px; height:15px;"></div>
	                                    	<strong>Tamanho</strong>
	                                    	<span class="ml-3"></span>
	                                    </div>
	                                    <hr>


	                                    <h1>Frase-chave</h1>
	                                    <div id="frase_chave_utilizada" class="has-popover d-flex align-items-center">
	                                    	<div class="d-inline-block rounded-circle mr-2 bg-danger-500 bg-danger-gradient" style="width:15px; height:15px;"></div>
	                                    	<strong>Frase-chave</strong>
	                                    	<span class="ml-3"></span>
	                                    </div>
	                                    <hr>


	                                    <h1>Texto</h1>
	                                    <div id="comprimento_texto" class="has-popover d-flex align-items-center">
	                                    	<div class="d-inline-block rounded-circle mr-2 bg-danger-500 bg-danger-gradient" style="width:15px; height:15px;"></div>
	                                    	<strong>Comprimento de Texto</strong>
	                                    	<span class="ml-3"></span>
	                                    </div>

	                                    <div id="densidade_frase_chave" class="has-popover d-flex align-items-center">
	                                    	<div class="d-inline-block rounded-circle mr-2 bg-danger-500 bg-danger-gradient" style="width:15px; height:15px;"></div>
	                                    	<strong>Densidade da Frase-chave</strong>
	                                    	<span class="ml-3"></span>
	                                    </div>

	                                    <div id="frase_chave_primeiro_paragrafo" class="has-popover d-flex align-items-center">
	                                    	<div class="d-inline-block rounded-circle mr-2 bg-danger-500 bg-danger-gradient" style="width:15px; height:15px;"></div>
	                                    	<strong>Frase-chave no primeiro parágrafo</strong>
	                                    	<span class="ml-3"></span>
	                                    </div>

	                                    <div id="link_externo" class="has-popover d-flex align-items-center">
	                                    	<div class="d-inline-block rounded-circle mr-2 bg-danger-500 bg-danger-gradient" style="width:15px; height:15px;"></div>
	                                    	<strong>Link Externo</strong>
	                                    	<span class="ml-3"></span>
	                                    </div>

	                                    <div id="link_interno" class="has-popover d-flex align-items-center">
	                                    	<div class="d-inline-block rounded-circle mr-2 bg-danger-500 bg-danger-gradient" style="width:15px; height:15px;"></div>
	                                    	<strong>Link Interno</strong>
	                                    	<span class="ml-3"></span>
	                                    </div>

										<div id="palavras_por_paragrafo" class="has-popover d-flex align-items-center">
	                                    	<div class="d-inline-block rounded-circle mr-2 bg-danger-500 bg-danger-gradient" style="width:15px; height:15px;"></div>
	                                    	<strong>Tamanho dos parágrafos</strong>
	                                    	<span class="ml-3"></span>
	                                    </div>

	                                    <div id="palavras_por_sentenca" class="has-popover d-flex align-items-center">
	                                    	<div class="d-inline-block rounded-circle mr-2 bg-danger-500 bg-danger-gradient" style="width:15px; height:15px;"></div>
	                                    	<strong>Tamanho dos sentença</strong>
	                                    	<span class="ml-3"></span>
	                                    </div>


	                                    <hr>
										
	                                    <h1>Imagens</h1>
										<div id="legenda_capa_seo" class="has-popover d-flex align-items-center">
	                                    	<div class="d-inline-block rounded-circle mr-2 bg-danger-500 bg-danger-gradient" style="width:15px; height:15px;"></div>
	                                    	<strong>Legenda da imagem de capa</strong>
	                                    	<span class="ml-3"></span>
	                                    </div>

	                                    <div id="legenda_galeria_seo" class="has-popover d-flex align-items-center">
	                                    	<div class="d-inline-block rounded-circle mr-2 bg-danger-500 bg-danger-gradient" style="width:15px; height:15px;"></div>
	                                    	<strong>Legenda da galeria</strong>
	                                    	<span class="ml-3"></span>
	                                    </div>

	                                    <hr>

	                                    <div class="d-flex mt-2 mb-1 fs-lg">
	                                        Desempenho Geral de SEO: <strong class="ml-3" id="pontuacao_seo_valor"></strong>%
	                                    </div>
	                                    <div class="progress progress-lg mb-3">
	                                        <div id="desempenho_seo" class="progress-bar bg-danger" role="progressbar" style="width: 0%;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
	                                    </div>
	                                    
	                                </div>

	                                <div class="row ml-2">
	                                	
	                                	<div>
	                                		<div class="p-2">Resultado para dispositivos móveis</div>
		                                	<div class="card " style="max-width: 400px;">
	                                            <div class="card-body">
	                                                <h5 class="card-title titulo_google_mobile">Card title</h5>
	                                                <div class="row mt-2">
		                                                <p class="card-text descricao_google_mobile col-8 ">This is another card with title and supporting text below. This card has some additional content to make it slightly taller overall.</p>
		                                                <div class="col-4">
		                                                	<img style="background: url(/images/2022/09/13/166307015263206fc8af33d.jpg); background-size: cover; background-position: center; width: 104px; height:104px;" class="rounded border border-white">
		                                                </div>
		                                            </div>
	                                            </div>
	                                        </div>
                                        </div>

                                        <div>
                                        	<div class="p-2">Resultado para computadores</div>
                                        <div class="mt-5 " style="width: 640px;">
                                        	<div class="card-body">
                                                <h5 class="card-title titulo_google_desktop">Card title</h5>
                                                <div class="row mt-2">
	                                                <p class="card-text descricao_google_desktop col-12">This is another card with title and supporting text below. This card has some additional content to make it slightly taller overall.</p>
	                                            </div>
                                            </div>
                                        </div>
                                        </div>
	                                </div>

	                                <style type="text/css">
	                                	.titulo_google_mobile, .titulo_google_desktop{
	                                	    color: rgb(21, 88, 214);
										    text-decoration: none;
										    font-size: 20px;
										    line-height: 26px;
										    font-weight: normal;
										    margin: 0px;
										    display: inline-block;
										    overflow: hidden;
										    vertical-align: top;
										    text-overflow: ellipsis;
										}
										.descricao_google_mobile, .descricao_google_desktop {
										    color: rgb(60, 64, 67);
										    font-size: 14px;
										    line-height: 20px;
										    cursor: pointer;
										    position: relative;
										}
	                                </style>
								</div>
							</div>
						</div>

						
					</div>
				</div>
			</div>
		</div>


		<div class="row">
			<div class="col-xl-12">
			  <div id="panel-2" class="panel">
				<div class="panel-hdr">
						<h2>
							4. Publicação
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
							
					<div class="panel-container show">

						<?

						?>

						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="form-row form-group">	
									<div class="col-md-2 mb-3">
										<label class="form-label" for="validationCustom02">Data de Publicação <span class="text-danger">*</span></label>
										<input data-inputmask="'mask': '99/99/9999'" type="text" class="form-control periodo_diferente" name="data_publicacao" placeholder="" value="<?php echo isset($dados->data_publicacao) ? converterData($dados->data_publicacao) : date("d/m/Y")?>">
									</div>
									<div class="col-md-2 mb-3">
										<label class="form-label" for="validationCustom02">Horário de Publicação <span class="text-danger">*</span></label>
										<input data-inputmask="'mask': '99:99:99'" type="text" class="form-control periodo_diferente" name="hora_publicacao" placeholder="" value="<?php echo isset($dados->hora_publicacao) ? ($dados->hora_publicacao) : date("H:i:s")?>">
									</div>
									<?
										if(isset($_GET['id'])){
									?>
									<div class="col-md-1 mb-3">
									</div>
									<div class="col-md-2 mb-3">
										<label class="form-label" for="validationCustom02">Data de Atualização <span class="text-danger">*</span></label>
										<input data-inputmask="'mask': '99/99/9999'" type="text" class="form-control periodo_diferente" name="data_atualizacao" placeholder="" value="<?php echo date("d/m/Y")?>">
									</div>
									<div class="col-md-2 mb-3">
										<label class="form-label" for="validationCustom02">Horário de Atualização <span class="text-danger">*</span></label>
										<input data-inputmask="'mask': '99:99:99'" type="text" class="form-control periodo_diferente" name="hora_atualizacao" placeholder="" value="<?php echo date("H:i:s")?>" required>
									</div>
									<?
										}
									?>
								</div>
							</div>
						</div>

						<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
							<div class="custom-control custom-checkbox" id="aprovacao_check">
								<input <?php echo isset($dados->publicado) && $dados->publicado == 1 ? 'checked=""' : ""?> <?php echo !isset($_GET['id']) ? 'checked=""' : ""?> type="checkbox" class="custom-control-input" id="invalidCheck2" value="1" name="publicado">
								<label class="custom-control-label" for="invalidCheck2">Publicar</label>
							</div>

						<?
							if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][4])){
								if(isset($dados->excluido)){
						?>
								<button onclick="$('#excluido').val(0); $('#form_noticia').submit()" class="btn btn-success ml-auto">Recuperar</button>
								<input type="hidden" id="excluido" name="excluido" value="<?=$dados->excluido?>">
						<?
								} else {
						?>
								<button onclick="excluir_registro('noticia_2022', 'id_noticia', <?php echo $_GET['id']?>)" class="btn btn-danger ml-auto" type="submit">Excluir</button>
						<?
								}
						?>	
							
						<?
							}
						?>

							<div class="custom-control custom-checkbox ml-auto" id="aprovacao_check">
								<input <?php echo isset($dados->capa) && $dados->capa == 1 ? 'checked=""' : ""?> <?php echo !isset($_GET['id']) ? 'checked=""' : ""?> type="checkbox" class="custom-control-input" id="invalidCheck22" value="1" name="capa">
								<label class="custom-control-label" for="invalidCheck22">Capa</label>
							</div>
				
						<button class="btn btn-primary ml-auto" type="submit"><?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar"?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	</form>

<form id="form_excluir_registro" method="post" target="dados" action="modulos/noticia/cadastro/cadastro_dados.php">
	<input type="hidden" id="excluir_registro" name="excluir_registro">
</form>		

</main>

<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/datagrid/datatables/dataTables.editor.min.js"></script>
<script src="js/moment-with-locales.js"></script>
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<script src="js/formplugins/summernote/summernote.js"></script>
<script src="js/formplugins/summernote/summernote-cleaner.js"></script>

<script src="js/formplugins/cropperjs/cropper.js"></script>


<script>

	function excluir_noticia(){
		var swalWithBootstrapButtons = Swal.mixin({
	        customClass:
	        {
	            confirmButton: "btn btn-primary",
	            cancelButton: "btn btn-danger mr-2"
	        },
	        buttonsStyling: false
	    });

    	swalWithBootstrapButtons
        .fire({
            title: "Deseja realmente excluir este registro?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Sim, excluir o registro!",
            cancelButtonText: "Não, cancelar!",
            reverseButtons: true
        })
        .then(function(result){
            if (result.value){
            	$('#form_excluir_registro').submit();
            } else if (
                // Read more about handling dismissals
                result.dismiss === Swal.DismissReason.cancel
            ){
                swalWithBootstrapButtons.fire(
                    "Cancelado!",
                    "Seu registro não foi excluído!",
                    "error"
                );
            }
        });

		
	}

	function recuperar_noticia(){
		var swalWithBootstrapButtons = Swal.mixin({
	        customClass:
	        {
	            confirmButton: "btn btn-primary",
	            cancelButton: "btn btn-danger mr-2"
	        },
	        buttonsStyling: false
	    });

    	swalWithBootstrapButtons
        .fire({
            title: "Deseja realmente recuperar este registro?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Sim, recuperar o registro!",
            cancelButtonText: "Não, cancelar!",
            reverseButtons: true
        })
        .then(function(result){
            if (result.value){
            	$('#form_recuperar_registro').submit();
            } else if (
                // Read more about handling dismissals
                result.dismiss === Swal.DismissReason.cancel
            ){
                swalWithBootstrapButtons.fire(
                    "Cancelado!",
                    "Seu registro não foi recuperado!",
                    "error"
                );
            }
        });

		
	}

	function obrigatoriedade(){
		/*console.log($("#id_blog").val());

		if($("#id_blog").val()){
			$("#id_cidade").removeAttr("required");
			$("#id_editoria").removeAttr("required");
			
		} else {
			$("#id_cidade").attr("required", "required");
			$("#id_editoria").attr("required", "required");
		}*/
	}

	function recuperacaoOK(){
		Swal.fire({
			type: "success",
			title: "Registro recuperado com sucesso!",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				window.history.back();
			}
		});
	}

	function exclusaoOK(){
		Swal.fire({
			type: "success",
			title: "Registro excluido com sucesso!",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				window.history.back();
			}
		});
	}
	function exclusaoFalha(){
		Swal.fire({
			type: "error",
			title: "Falha ao excluir registro",
			showConfirmButton: true
		});
	}

    var autoSave = $('#autoSave');
    var interval;
    var timer = function()
    {
        interval = setInterval(function()
        {
            //start slide...
            if (autoSave.prop('checked'))
                saveToLocal();

            clearInterval(interval);
        }, 3000);
    };

    //save
    var saveToLocal = function()
    {
        localStorage.setItem('summernoteData', $('#saveToLocal').summernote("code"));
        console.log("saved");
    }

    //delete 
    var removeFromLocal = function()
    {
        localStorage.removeItem("summernoteData");
        $('#saveToLocal').summernote('reset');
    }

    function analise_seo(){

		var titulo = $("#titulo").val();
		var resumo = $("#resumo").val();
		var texto = $($(".js-summernote").summernote("code")).text();
		var texto_codigo = $($(".js-summernote").summernote("code"));
		var palavra_chave = $("#palavra_chave").val().trim();
		var data = "<?= date('d \d\e M \d\e Y')?>";

		console.log("texto", texto);


		//verifica quantas vezes a frase-chave aparece no texto
		var count = 0;
		pos = texto.indexOf(palavra_chave); // retorna 3
		console.log("pos", pos);
		while ( pos > 0 ) {
		   count++;
		   pos = texto.indexOf( palavra_chave, pos + 1  );
		}
		if(count == 0){
			fseo_falha("#densidade_frase_chave", `A sua frase-chave não foi encontrada nenhuma vez. Isso é menos que o mínimo recomendado de 2 vezes.`);
		} else if(count == 1){
			fseo_falha("#densidade_frase_chave", `A frase-chave foi encontrada 1 vez. O mínimo recomendado é de 2 vezes.`);
		} else if(count > 6){
			fseo_falha("#densidade_frase_chave", `a frase-chave em foco foi encontrada ${count} vezes. Isso é muito mais do que o máximo recomendado de 6 vezes.`);
		} else {
			fseo_ok("#densidade_frase_chave", `A frase-chave em foco foi encontrada ${count} vezes. Isso é ótimo!`);
		}


	
		//verifica o tamanho dos parágrafos
		var count = 0;
		for(i=0; i<texto_codigo.length; i++){
			/*if(texto_codigo[i].innerHTML.split(" ").length > 150){
				count++;
			}*/
		}
		if(count > 0){
			fseo_falha("#palavras_por_paragrafo", `${count} parágrafo${count == 1 ? "" : "s"} contém mais do que o máximo recomendado de 150 palavras`);
		} else {
			fseo_ok("#palavras_por_paragrafo", `Nenhum dos parágrafos é longo demais. Bom trabalho!`);			
		}


		//verifica o tamanho das sentenças
		var count = 0;
		var sentencas = texto.split(".");
		var total_sentencas = sentencas.length;
		for(i=0; i<sentencas.length; i++){
			console.log("count",sentencas[i].split(" ").length);
			if(sentencas[i].split(" ").length > 20){
				count++;
			}
		}
		var total = count * 100 / total_sentencas;
		console.log("count",count);
		console.log("total",total);
		if(total > 25){
			fseo_falha("#palavras_por_sentenca", `${Math.floor(total)}% das frases contém mais do que 25 palavras, e o máximo recomendado é de 25%.`);
		} else {
			fseo_ok("#palavras_por_sentenca", `Tamanho das frases adequados. Bom trabalho!`);			
		}


		//verifica se a frase-chave consta no primeiro parágrafo
		if(texto_codigo[0].innerHTML.indexOf(palavra_chave) > -1){
			fseo_ok("#frase_chave_primeiro_paragrafo", `Sua frase-chave consta no primeiro parágrafo do texto`);
		} else {
			fseo_falha("#frase_chave_primeiro_paragrafo", `Sua frase-chave, não está no primeiro parágrafo do texto`);
		}
		


		$(".titulo_google_mobile").html(titulo.substring(0,64)+"...");
		$(".descricao_google_mobile").html(data + " - " + resumo.substring(0,128));

		$(".titulo_google_desktop").html(titulo.substring(0,60)+"...");
		$(".descricao_google_desktop").html(data + " - " + resumo.substring(0,128));

	
		//verifica o tamanho do texto
		comprimento_texto = texto.split(" ");
		if(comprimento_texto.length >= 300){
			fseo_ok("#comprimento_texto", `Texto com bom comprimento: ${comprimento_texto.length} palavras`);
		} else {
			fseo_falha("#comprimento_texto", `O texto contém ${comprimento_texto.length} palavras. Está abaixo do mínimo recomendado de 300 palavras`);
		}


		//verifica se a existe link externo no texto
		var link_externo = texto.indexOf("http://");
		var link_externo2 = texto.indexOf("https://");

		if(link_externo > -1 || link_externo2 > -1){
			fseo_ok("#link_externo", `O texto possui link externo`);
		} else {
			fseo_falha("#link_externo", `O texto não possui nenhum link externo`);
		}


		//verifica se a existe link interno no texto
		var link_interno = texto.indexOf("https://sgc.com.br");
		var link_interno2 = texto.indexOf("https://www.sgc.com.br");
		if(link_interno > -1 || link_interno2 > -1){
			fseo_ok("#link_interno", `O texto possui link interno`);
		} else {
			fseo_falha("#link_interno", `O texto não possui nenhum link interno`);
		}


		//verifica se a frase-chave está no título
		var frase_chave_titulo = titulo.indexOf(palavra_chave);
		
		if(frase_chave_titulo > -1 && palavra_chave != ""){
			fseo_ok("#frase_chave_titulo", `A frase-chave presente está no título`);
		} else {
			fseo_falha("#frase_chave_titulo", `A frase-chave não está presente no título`);
		}


		//verifica se a frase-chave está na linha fina
		var frase_chave_resumo = resumo.indexOf(palavra_chave);
		if(frase_chave_resumo > -1 && palavra_chave != ""){
			fseo_ok("#frase_chave_resumo", `A frase-chave está na linha fina`);
		} else {
			fseo_falha("#frase_chave_resumo", `A frase-chave não está presente na linha fina`);
		}


		//verifica o tamanho da linha-fina
		if(resumo.length >= 120 && resumo.length < 156){
			fseo_ok("#linha_fina_tamanho", `A linha fina está com tamanho adequado`);
		} else if(resumo.length >= 156){
			fseo_falha("#linha_fina_tamanho", `A linha fina está com mais de 156 carateres`);
		} else {
			fseo_falha("#linha_fina_tamanho", `A linha fina está com menos de 120 carateres`);
		}


		//verifica se a imagem de capa possui legenda
		if($("#legenda_capa").val() == ""){
			fseo_falha("#legenda_capa_seo", `Imagem de capa sem legenda`);
		} else {
			fseo_ok("#legenda_capa_seo", `Imagem de capa com legenda`);
		}

		if($(".legenda_galeria_seo").length){
			var legenda_galeria = 0;
			$(".legenda_galeria_seo").each(function( index ) {
				if($(this).val() == ""){
					legenda_galeria++;
				}
			});

			console.log("legenda_galeria",legenda_galeria);

			//verifica se a imagem de capa possui legenda
			if(legenda_galeria > 0){
				fseo_falha("#legenda_galeria_seo", `Imagens da galeria sem legenda`);
			} else {
				fseo_ok("#legenda_galeria_seo", `Imagens da galeria com legenda`);
			}
		} else {
			fseo_nao_aplica("#legenda_galeria_seo", `Não se aplica, matéria sem galeria`);
		}


		//verifica se a palavra chave já é utilizada em outra postagem
		if(palavra_chave){
			$.ajax({
		      type: "POST",
		      url: `modulos/noticia/cadastro/ajax/validar_frase_chave.php?frase=${palavra_chave}&id_noticia=<?=$_GET['id'] ? $_GET['id'] : 0 ?>`,
		      cache: false,
		      contentType: false,
		      processData: false,
		      success: function(data) {
		      	if(data > 0){
		      		fseo_falha("#frase_chave_utilizada", `Frase-chave já utilizada em outra postagem`);
				} else {
					fseo_ok("#frase_chave_utilizada", `Frase-chave original`);
				}
				calcular_pontuacao();
		      }
		    });
		}

		calcular_pontuacao();

		

    }

    function calcular_pontuacao(){
    	//define a pontuação geral do SEO
		var seo_ok = $(".bg-success-500").length;
		var seo_nao_ok = $(".bg-danger-500").length;
		var pontuacao_seo = seo_ok * 100 / (seo_ok + seo_nao_ok);

		$("#pontuacao_seo_valor").html(" " + Math.floor(pontuacao_seo));
		$("#desempenho_seo").css("width", pontuacao_seo+"%");

		console.log("pontuacao_seo",pontuacao_seo);

		if(pontuacao_seo >= 50 && pontuacao_seo < 70){
			$("#desempenho_seo").removeClass("bg-danger bg-success bg-warning");
			$("#desempenho_seo").addClass("bg-warning");
		} else if(pontuacao_seo >= 70){
			$("#desempenho_seo").removeClass("bg-danger bg-success bg-warning");
			$("#desempenho_seo").addClass("bg-success");
		} else {
			$("#desempenho_seo").removeClass("bg-danger bg-success bg-warning");
			$("#desempenho_seo").addClass("bg-danger bg-fusion-gradient");
		}
    }

    function fseo_ok(div, frase){
    	$(`${div} div`).removeClass("bg-danger-500 bg-danger-gradient bg-fusion-50");
		$(`${div} div`).addClass("bg-success-500 bg-success-gradient ");
		$(`${div} span`).html(frase);
    }

    function fseo_falha(div, frase){
		$(`${div} div`).addClass("bg-danger-500 bg-danger-gradient");
		$(`${div} div`).removeClass("bg-success-500 bg-success-gradient bg-fusion-50");
		$(`${div} span`).html(frase);
    }

    function fseo_nao_aplica(div, frase){
		$(`${div} div`).addClass("bg-fusion-50");
		$(`${div} div`).removeClass("bg-success-500 bg-danger-500 bg-danger-gradient bg-success-gradient");
		$(`${div} span`).html(frase);
    }

    

    $(document).ready(function(){
    	$("input").change(function() {
            analise_seo()
        });
    });

    $(document).ready(function()
    {
        //init default
        $('.js-summernote').summernote(
        {
            height: 500,
            tabsize: 2,
            placeholder: "Texto...",
            dialogsFade: true,
            spellCheck: true,
            toolbar: [
                //['style', ['style']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontsize', ['fontsize']],
                //['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video', 'hr']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks:
            {
                //restore from localStorage
                onInit: function(e)
                {
                    analise_seo();
                    //$('.js-summernote').summernote("code", localStorage.getItem("summernoteData"));
                },
                onChange: function(contents, $editable)
                {
                    clearInterval(interval);
                    timer();
                    analise_seo()
                    
                },
                onImageUpload: function(files, editor, welEditable) {
			      sendFile(files[0], editor, welEditable);
			    }
            },
		    cleaner:{
		          action: 'paste', 
		          keepHtml: false,
		          keepClasses: false,
		          limitChars: false, 
		          limitDisplay: 'both',
		          limitStop: false
		    }
		    
        });


        function sendFile(file, editor, welEditable) {
		    data = new FormData();
		    data.append("file", file);
		    $.ajax({
		      data: data,
		      type: "POST",
		      url: "modulos/noticia/cadastro/ajax/upload.php?Asdf",
		      cache: false,
		      contentType: false,
		      processData: false,
		      success: function(url) {
		        //editor.insertImage(welEditable, url);
		        $('.js-summernote').summernote('insertImage', url, welEditable);
		      }
		    });
		}

        //load emojis
        $.ajax(
        {
            url: 'https://api.github.com/emojis',
            async: false
        }).then(function(data)
        {
            window.emojis = Object.keys(data);
            window.emojiUrls = data;
        });

        //init emoji example
        $(".js-hint2emoji").summernote(
        {
            height: 100,
            toolbar: false,
            placeholder: 'type starting with : and any alphabet',
            hint:
            {
                match: /:([\-+\w]+)$/,
                search: function(keyword, callback)
                {
                    callback($.grep(emojis, function(item)
                    {
                        return item.indexOf(keyword) === 0;
                    }));
                },
                template: function(item)
                {
                    var content = emojiUrls[item];
                    return '<img src="' + content + '" width="20" /> :' + item + ':';
                },
                content: function(item)
                {
                    var url = emojiUrls[item];
                    if (url)
                    {
                        return $('<img />').attr('src', url).css('width', 20)[0];
                    }
                    return '';
                }
            }
        });

        //init mentions example
        $(".js-hint2mention").summernote(
        {
            height: 100,
            toolbar: false,
            placeholder: "type starting with @",
            hint:
            {
                mentions: ['jayden', 'sam', 'alvin', 'david'],
                match: /\B@(\w*)$/,
                search: function(keyword, callback)
                {
                    callback($.grep(this.mentions, function(item)
                    {
                        return item.indexOf(keyword) == 0;
                    }));
                },
                content: function(item)
                {
                    return '@' + item;
                }
            }
        });

    });


	//MENSAGEM DE CADASTRO OK
	function cadastroOK(operacao){ 
		var msg = operacao == 1 ? "Registro cadastrado com sucesso" : "Registro alterado com sucesso";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 500,
			onClose: () => {
				<?php
					if(!isset($_GET['id'])){
						echo "window.history.back();";
					} else {
						//echo "document.location.reload(true);";
						echo "window.history.back();";
					}
				?>
				
				//document.location.reload(true)
			}
		});
	}

	//MENSAGEM DE FALHA NO CADASTRO
	function cadastroFalha(operacao){ 
		var msg = operacao == 1 ? "Falha ao cadastrar dados" : "Falha ao alterar dados";
		Swal.fire({
			type: "error",
			title: msg,
			showConfirmButton: false,
			timer: 1500
		});
	}

	


	$(document).ready(function(){
		$(":input").inputmask();
		$('.select2').select2();
	});

	// Example starter JavaScript for disabling form submissions if there are invalid fields
	(function() {
		'use strict';
		window.addEventListener('load', function(){
			// Fetch all the forms we want to apply custom Bootstrap validation styles to
			var forms = document.getElementsByClassName('needs-validation');
			// Loop over them and prevent submission
			var validation = Array.prototype.filter.call(forms, function(form){
				form.addEventListener('submit', function(event){
					if (form.checkValidity() === false){
						event.preventDefault();
						event.stopPropagation();
					}
					form.classList.add('was-validated');
				}, false);
			});
		}, false);
	})();


    $(function()
    {
        'use strict';

        /*var console = window.console || {
        	log: function () {}
        };*/

        var URL = window.URL || window.webkitURL;
        var $image = $('#image');
        var $download = $('#download');

       
        
        var options = {
            aspectRatio: 16 / 9,
            zoomOnWheel: false,
            preview: '.img-preview',
            crop: function(e)
            {

            }
        };
        var originalImageURL = $image.attr('src');
        var uploadedImageName = 'cropped.jpg';
        var uploadedImageType = 'image/jpeg';
        var uploadedImageURL;

        // Tooltip
        $('[data-toggle="tooltip"]').tooltip();

        // Cropper
        $image.on(
        {
            ready: function(e)
            {
                console.log(e.type);
            },
            cropstart: function(e)
            {
                console.log(e.type, e.detail.action);
            },
            cropmove: function(e)
            {
                console.log(e.type, e.detail.action);
            },
            cropend: function(e)
            {
                console.log(e.type, e.detail.action);
            },
            crop: function(e)
            {
                console.log(e.type);
            },
            zoom: function(e)
            {
                console.log(e.type, e.detail.ratio);
            }
        }).cropper(options);

        // Buttons
        if (!$.isFunction(document.createElement('canvas').getContext))
        {
            $('button[data-method="getCroppedCanvas"]').prop('disabled', true);
        }

        if (typeof document.createElement('cropper').style.transition === 'undefined')
        {
            $('button[data-method="rotate"]').prop('disabled', true);
            $('button[data-method="scale"]').prop('disabled', true);
        }

        // Download
        if (typeof $download[0].download === 'undefined')
        {
            $download.addClass('disabled');
        }

        // Options
        $('.docs-toggles').on('change', 'input', function()
        {
            var $this = $(this);
            var name = $this.attr('name');
            var type = $this.prop('type');
            var cropBoxData;
            var canvasData;

            if (!$image.data('cropper'))
            {
                return;
            }

            if (type === 'checkbox')
            {
                options[name] = $this.prop('checked');
                cropBoxData = $image.cropper('getCropBoxData');
                canvasData = $image.cropper('getCanvasData');

                options.ready = function()
                {
                    $image.cropper('setCropBoxData', cropBoxData);
                    $image.cropper('setCanvasData', canvasData);
                };
            }
            else if (type === 'radio')
            {
                options[name] = $this.val();
            }

            $image.cropper('destroy').cropper(options);
        });

        // Methods
        $('.docs-buttons').on('click', '[data-method]', function()
        {
            var $this = $(this);
            var data = $this.data();
            var cropper = $image.data('cropper');
            var cropped;
            var $target;
            var result;

            if ($this.prop('disabled') || $this.hasClass('disabled'))
            {
                return;
            }

            if (cropper && data.method)
            {
                data = $.extend(
                {}, data); // Clone a new one

                if (typeof data.target !== 'undefined')
                {
                    $target = $(data.target);

                    if (typeof data.option === 'undefined')
                    {
                        try
                        {
                            data.option = JSON.parse($target.val());
                        }
                        catch (e)
                        {
                            console.log(e.message);
                        }
                    }
                }

                cropped = cropper.cropped;

                switch (data.method)
                {
                    case 'rotate':
                        if (cropped && options.viewMode > 0)
                        {
                            $image.cropper('clear');
                        }

                        break;

                    case 'getCroppedCanvas':
                        if (uploadedImageType === 'image/jpeg')
                        {
                            if (!data.option)
                            {
                                data.option = {};
                            }

                            data.option.fillColor = '#fff';
                        }

                        break;
                }

                result = $image.cropper(data.method, data.option, data.secondOption);

                switch (data.method)
                {
                    case 'rotate':
                        if (cropped && options.viewMode > 0)
                        {
                            $image.cropper('crop');
                        }

                        break;

                    case 'scaleX':
                    case 'scaleY':
                        $(this).data('option', -data.option);
                        break;

                    case 'getCroppedCanvas':
                        if (result)
                        {
                            // Bootstrap's Modal
                            //$('#getCroppedCanvasModal').find('.modal-body').html(result);
                            $("#download").show();

                            if (!$download.hasClass('disabled'))
                            {
                                
                                download.download = uploadedImageName;
                                $download.attr('href', result.toDataURL(uploadedImageType));

                                imagem_cortada = result.toDataURL(uploadedImageType);

                                $("#botoes").show();
                            }
                        }

                        break;

                    case 'destroy':
                        if (uploadedImageURL)
                        {
                            URL.revokeObjectURL(uploadedImageURL);
                            uploadedImageURL = '';
                            $image.attr('src', originalImageURL);
                        }

                        break;
                }

                if ($.isPlainObject(result) && $target)
                {
                    try
                    {
                        $target.val(JSON.stringify(result));
                    }
                    catch (e)
                    {
                        console.log(e.message);
                    }
                }
            }
        });

        // Keyboard
        $(document.body).on('keydown', function(e)
        {
            if (e.target !== this || !$image.data('cropper') || this.scrollTop > 300)
            {
                return;
            }

            switch (e.which)
            {
                case 37:
                    e.preventDefault();
                    $image.cropper('move', -1, 0);
                    break;

                case 38:
                    e.preventDefault();
                    $image.cropper('move', 0, -1);
                    break;

                case 39:
                    e.preventDefault();
                    $image.cropper('move', 1, 0);
                    break;

                case 40:
                    e.preventDefault();
                    $image.cropper('move', 0, 1);
                    break;
            }
        });

        // Import image
        var $inputImage = $('#inputImage');

        if (URL)
        {
            $inputImage.change(function()
            {
                var files = this.files;
                var file;

                if (!$image.data('cropper'))
                {
                    return;
                }

                if (files && files.length)
                {
                    file = files[0];

                    if (/^image\/\w+$/.test(file.type))
                    {
                        uploadedImageName = file.name;
                        uploadedImageType = file.type;

                        if (uploadedImageURL)
                        {
                            URL.revokeObjectURL(uploadedImageURL);
                        }

                        uploadedImageURL = URL.createObjectURL(file);
                        $image.cropper('destroy').attr('src', uploadedImageURL).cropper(options);
                        $inputImage.val('');
                    }
                    else
                    {
                        window.alert('Please choose an image file.');
                    }
                }
            });
        }
        else
        {
            $inputImage.prop('disabled', true).parent().addClass('disabled');
        }
    });

</script>