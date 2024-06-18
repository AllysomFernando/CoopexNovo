<?php
	require_once("php/config.php");
	if(!isset($_SESSION['coopex'])){
		header("Location: $_base");
	}
?>


<aside class="page-sidebar">
	<div class="page-logo">
		<a href="#" class="page-logo-link press-scale-down d-flex align-items-center" data-toggle="modal" data-target="#modal-shortcut">
			<img src="img/coopex_logo.png" alt="SmartAdmin WebApp" aria-roledescription="logo">
			<span class="page-logo-text mr-1">Sistema Coopex</span>
			<i class="fal fa-angle-down d-inline-block ml-1 fs-lg color-primary-300"></i>
		</a>
	</div>
	<!-- BEGIN PRIMARY NAVIGATION -->
	<nav id="js-primary-nav" class="primary-nav" role="navigation">
		<div class="nav-filter">
			<div class="position-relative">
				<input type="text" id="nav_filter_input" placeholder="Buscar módulo" class="form-control" tabindex="0">
				<a href="#" onclick="return false;" class="btn-primary btn-search-close js-waves-off" data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar">
					<i class="fal fa-chevron-up"></i>
				</a>
			</div>
		</div>
		<?php
			//echo utf8_encode($_SESSION['coopex']['usuario']['pessoa']['tipo_usuario']);
			if($_SESSION['coopex']['usuario']['sistema']['avatar']){
				$imagem = "img/avatars/".$_SESSION['coopex']['usuario']['sistema']['avatar'];
			} else {
				$imagem = "img/avatars/avatar-".strtolower($_SESSION['coopex']['usuario']['sexo']).".png";
			}
			$pronome = $_SESSION['coopex']['usuario']['sexo'] == "M" ? "" : "a";
			$tratamento =  $_SESSION['coopex']['usuario']['tipo_usuario'];
		?>
		<div class="info-card">
			<img src="<?php echo $imagem?>" class="profile-image rounded-circle" alt="<?php echo $_SESSION['coopex']['usuario']['nome']?>">
			<div class="info-card-text">
				<a href="#" class="d-flex align-items-center text-white">
					<span class="text-truncate text-truncate-sm d-inline-block">
						<?php echo $_SESSION['coopex']['usuario']['primeiro_nome']?>
					</span>
				</a>
				<span class="d-inline-block text-truncate text-truncate-sm"><?php echo $tratamento?></span>
			</div>
			<img src="img/card-backgrounds/cover-2-lg.png" class="cover" alt="cover">
			<a href="#" onclick="return false;" class="pull-trigger-btn" data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar" data-focus="nav_filter_input">
				<i class="fal fa-angle-down"></i>
			</a>
		</div>
		<ul id="js-nav-menu" class="nav-menu">
			<?
				if($_SESSION['coopex']['usuario']['id_curso'] == 1000000391){

			?>
				
			<?
				}
			?>	
			<?php
				//GRUPO
				foreach($_SESSION['coopex']['menu']['grupo'] as $grupo){
			?>
			<li class="nav-title"><?php echo utf8_encode($grupo['nome'])?></li>
				<?php
					//MENU NIVEL 1
					foreach($grupo['nivel1'] as $nivel1){
				?>
			<li>
				<a href="<?php echo $nivel1['url'] ? $nivel1['url'] : "#"?>" title="<?php echo utf8_encode($nivel1['nome'])?>" data-filter-tags="<?php echo strtolower(utf8_encode($nivel1['nome']))?>">
					<i class="<?php echo $nivel1['icone']?>"></i>
					<span class="nav-link-text" data-i18n="nav.pages"><?php echo utf8_encode($nivel1['nome'])?></span>
				</a>
					<?php
						if(isset($nivel1['nivel2'])){
					?>
				<ul>
					<?php
							//MENU NIVEL 2
							foreach($nivel1['nivel2'] as $nivel2){
								if(isset($nivel2['nivel3'])){
					?>
					<li>
						<a href="javascript:void(0);" title="<?php echo utf8_encode($nivel2['nome'])?>" data-filter-tags="<?php echo strtolower(utf8_encode($nivel1['nome']))?>">
							<span class="nav-link-text" data-i18n="nav.pages_error_pages"><?php echo utf8_encode($nivel2['nome'])?></span>
						</a>
						<ul>
								<?php
									//MENU NIVEL 3
									foreach($nivel2['nivel3'] as $nivel3){
								?>
							<li>
								<a href="<?php echo $nivel3['url']?>" title="<?php echo utf8_encode($nivel3['nome'])?>" data-filter-tags="<?php echo strtolower(utf8_encode($nivel3['nome']))?>">
									<span class="nav-link-text" data-i18n="nav.pages_error_pages_general_error"><?php echo utf8_encode($nivel3['nome'])?></span>
								</a>
							</li>
								<?php
									}
								?>
						</ul>
					</li>
					<?php
								} else {
					?>
						<li>
							<a href="<?php echo $nivel2['url']?>" title="Profile" data-filter-tags="<?php echo strtolower(utf8_encode($nivel2['nome']))?>">
								<span class="nav-link-text" data-i18n="nav.pages_profile"><?php echo utf8_encode($nivel2['nome'])?></span>
							</a>
						</li>
					<?php
								}
							}
					?>
				</ul>
				<?php
						}
				?>
			</li>
			<?php
					}
				}
			?>
		</ul>
		<div class="filter-message js-filter-message bg-success-600"></div>
	</nav>
	<!-- END PRIMARY NAVIGATION -->
	<!-- NAV FOOTER -->
	<div class="nav-footer shadow-top">
		<a href="#" onclick="return false;" data-action="toggle" data-class="nav-function-minify" class="hidden-md-down">
			<i class="ni ni-chevron-right"></i>
			<i class="ni ni-chevron-right"></i>
		</a>
		<ul class="list-table m-auto nav-footer-buttons d-none">
			<li>
				<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Chat logs">
					<i class="fal fa-comments"></i>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Support Chat">
					<i class="fal fa-life-ring"></i>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Make a call">
					<i class="fal fa-phone"></i>
				</a>
			</li>
		</ul>
	</div> <!-- END NAV FOOTER -->
</aside>