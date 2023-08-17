
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">SmartAdmin</a></li>
		<li class="breadcrumb-item">UI Components</li>
		<li class="breadcrumb-item active">List filter</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-window'></i> List filter
			<small>
				Allow any lists to be filtered via the included filter plugin. Very easy to setup, no programming needed!
			</small>
		</h1>
	</div>
	<div class="row">
		<div class="col-xl-6">
			<!--Default list filter-->
			
			<!--Filter accordions-->
			<div id="panel-2" class="panel">
				<div class="panel-hdr">
					<h2>
						Filter <span class="fw-300"><i>Accordions</i></span>
					</h2>
					<div class="panel-toolbar">
						<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
						<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						<button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<div class="panel-tag">
							You can arrange your filters like so to filter accordion headings
						</div>
						<div class="border bg-light rounded-top">
							<div class="form-group p-2 m-0 rounded-top">
								<input type="text" class="form-control form-control-lg shadow-inset-2 m-0" id="js_list_accordion_filter" placeholder="Filter accordion">
							</div>
							<ul id="js_nested_list" class="nav-menu nav-menu-reset nav-menu-compact bg-success-900 bg-info-gradient mb-sm-4 mb-md-0 rounded" data-nav-accordion="true">
								<li class="open active">
									<a href="#" data-filter-tags="user interface buttons compass action dropdown navigation sidebars">
										<span class="nav-link-text">User Interface </span>
										<strong class="dl-ref bg-primary-500">&nbsp;1.0&nbsp;</strong>
									</a>
									<ul style="display:block;">
										<li class="active">
											<a href="#" data-filter-tags="user interface buttons">
												<span class="nav-link-text">
													Buttons
												</span>
											</a>
										</li>
										<li>
											<a href="#" data-filter-tags="user interface compass">
												<span class="nav-link-text">
													Compass
												</span>
											</a>
										</li>
										<li>
											<a href="#" data-filter-tags="user interface action dropdown navigation sidebars">
												<span class="nav-link-text">
													Action
												</span>
												<strong class="dl-ref bg-primary-500">&nbsp;1.3&nbsp;</strong>
											</a>
											<ul>
												<li>
													<a href="#" data-filter-tags="user interface action dropdown">
														<span class="nav-link-text">
															Dropdown
														</span>
													</a>
												</li>
												<li>
													<a href="#" data-filter-tags="user interface action navigation">
														<span class="nav-link-text">
															Navigation
														</span>
													</a>
												</li>
												<li>
													<a href="#" data-filter-tags="user interface action sidebars">
														<span class="nav-link-text">
															Sidebars
														</span>
													</a>
												</li>
											</ul>
										</li>
									</ul>
								</li>
								<li>
									<a href="#" data-filter-tags="graphs flot chart pie sythentic graphs polygraphs">
										<span class="nav-link-text">
											Graphs
										</span>
										<strong class="dl-ref bg-primary-500">&nbsp;2.0&nbsp;</strong>
									</a>
									<ul>
										<li>
											<a href="#" data-filter-tags="graphs flot chart">
												<span class="nav-link-text">
													Flot chart
												</span>
											</a>
										</li>
										<li>
											<a href="#" data-filter-tags="graphs pie chart">
												<span class="nav-link-text">
													Pie charts
												</span>
											</a>
										</li>
										<li>
											<a href="#" data-filter-tags="graphs sythentic">
												<span class="nav-link-text">
													Sythentic graphs
												</span>
											</a>
										</li>
										<li>
											<a href="#" data-filter-tags="graphs flot polygraphs">
												<span class="nav-link-text">
													Polygraphs
												</span>
											</a>
										</li>
									</ul>
								</li>
								<li>
									<a href="#" data-filter-tags="forms controls loaders other elements buttons input checkbox">
										<span class="nav-link-text">Forms </span>
										<strong class="dl-ref bg-primary-500">&nbsp;3.0&nbsp;</strong>
									</a>
									<ul>
										<li>
											<a href="#" data-filter-tags="forms controls">
												<span class="nav-link-text"> Controls</span>
											</a>
										</li>
										<li>
											<a href="#" data-filter-tags="forms loaders">
												<span class="nav-link-text"> Loaders</span>
											</a>
										</li>
										<li>
											<a href="#" data-filter-tags="forms other elements buttons input checkbox">
												<span class="nav-link-text">
													Other elements
												</span>
												<strong class="dl-ref bg-primary-500">&nbsp;3.3&nbsp;</strong>
											</a>
											<ul>
												<li>
													<a href="#" data-filter-tags="forms other elements buttons">
														<span class="nav-link-text">
															Buttons
														</span>
													</a>
												</li>
												<li>
													<a href="#" data-filter-tags="forms other elements input">
														<span class="nav-link-text">
															Input
														</span>
													</a>
												</li>
												<li data-filter-tags="forms other elements checkbox">
													<a href="#">
														<span class="nav-link-text">
															Checkbox
														</span>
													</a>
												</li>
											</ul>
										</li>
									</ul>
								</li>
							</ul>
							<span class="filter-message js-filter-message"></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-6">
			<!--Custom response message-->
			
			<!--Nested filter search-->
			<div id="panel-4" class="panel">
				<div class="panel-hdr">
					<h2>
						Nested <span class="fw-300"><i>filter</i></span>
					</h2>
					<div class="panel-toolbar">
						<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
						<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						<button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<div class="bg-success-900 rounded bg-info-gradient">
							<div class="d-flex position-relative py-3 px-4">
								<i class="fal fa-search color-success-700 position-absolute pos-left fs-lg px-3 py-2 mt-1 ml-4"></i>
								<input type="text" id="js_nested_list_filter" class="form-control shadow-inset-1 pl-6 border-success" placeholder="Filter nested items (e.g buttons, chart)">
							</div>
							<!-- nav-menu-reset will reset the font colors -->
							<ul id="js_nested_list" class="nav-menu nav-menu-reset nav-menu-compact bg-success-900 bg-info-gradient mb-sm-4 mb-md-0 rounded" data-nav-accordion="true">
								<li class="open active">
									<a href="#" data-filter-tags="user interface buttons compass action dropdown navigation sidebars">
										<span class="nav-link-text">User Interface </span>
										<strong class="dl-ref bg-primary-500">&nbsp;1.0&nbsp;</strong>
									</a>
									<ul style="display:block;">
										<li class="active">
											<a href="#" data-filter-tags="user interface buttons">
												<span class="nav-link-text">
													Buttons
												</span>
											</a>
										</li>
										<li>
											<a href="#" data-filter-tags="user interface compass">
												<span class="nav-link-text">
													Compass
												</span>
											</a>
										</li>
										<li>
											<a href="#" data-filter-tags="user interface action dropdown navigation sidebars">
												<span class="nav-link-text">
													Action
												</span>
												<strong class="dl-ref bg-primary-500">&nbsp;1.3&nbsp;</strong>
											</a>
											<ul>
												<li>
													<a href="#" data-filter-tags="user interface action dropdown">
														<span class="nav-link-text">
															Dropdown
														</span>
													</a>
												</li>
												<li>
													<a href="#" data-filter-tags="user interface action navigation">
														<span class="nav-link-text">
															Navigation
														</span>
													</a>
												</li>
												<li>
													<a href="#" data-filter-tags="user interface action sidebars">
														<span class="nav-link-text">
															Sidebars
														</span>
													</a>
												</li>
											</ul>
										</li>
									</ul>
								</li>
								<li>
									<a href="#" data-filter-tags="graphs flot chart pie sythentic graphs polygraphs">
										<span class="nav-link-text">
											Graphs
										</span>
										<strong class="dl-ref bg-primary-500">&nbsp;2.0&nbsp;</strong>
									</a>
									<ul>
										<li>
											<a href="#" data-filter-tags="graphs flot chart">
												<span class="nav-link-text">
													Flot chart
												</span>
											</a>
										</li>
										<li>
											<a href="#" data-filter-tags="graphs pie chart">
												<span class="nav-link-text">
													Pie charts
												</span>
											</a>
										</li>
										<li>
											<a href="#" data-filter-tags="graphs sythentic">
												<span class="nav-link-text">
													Sythentic graphs
												</span>
											</a>
										</li>
										<li>
											<a href="#" data-filter-tags="graphs flot polygraphs">
												<span class="nav-link-text">
													Polygraphs
												</span>
											</a>
										</li>
									</ul>
								</li>
								<li>
									<a href="#" data-filter-tags="forms controls loaders other elements buttons input checkbox">
										<span class="nav-link-text">Forms </span>
										<strong class="dl-ref bg-primary-500">&nbsp;3.0&nbsp;</strong>
									</a>
									<ul>
										<li>
											<a href="#" data-filter-tags="forms controls">
												<span class="nav-link-text"> Controls</span>
											</a>
										</li>
										<li>
											<a href="#" data-filter-tags="forms loaders">
												<span class="nav-link-text"> Loaders</span>
											</a>
										</li>
										<li>
											<a href="#" data-filter-tags="forms other elements buttons input checkbox">
												<span class="nav-link-text">
													Other elements
												</span>
												<strong class="dl-ref bg-primary-500">&nbsp;3.3&nbsp;</strong>
											</a>
											<ul>
												<li>
													<a href="#" data-filter-tags="forms other elements buttons">
														<span class="nav-link-text">
															Buttons
														</span>
													</a>
												</li>
												<li>
													<a href="#" data-filter-tags="forms other elements input">
														<span class="nav-link-text">
															Input
														</span>
													</a>
												</li>
												<li data-filter-tags="forms other elements checkbox">
													<a href="#">
														<span class="nav-link-text">
															Checkbox
														</span>
													</a>
												</li>
											</ul>
										</li>
									</ul>
								</li>
							</ul>
							<div class="filter-message js-filter-message m-0 text-left pl-4 py-3 fw-500"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
<script>
	// default list filter
	initApp.listFilter($('#js_default_list'), $('#js_default_list_filter'));
	// custom response message
	initApp.listFilter($('#js-list-msg'), $('#js-list-msg-filter'));
	//accordion filter
	initApp.listFilter($('#js_list_accordion'), $('#js_list_accordion_filter'));
	// nested list filter
	initApp.listFilter($('#js_nested_list'), $('#js_nested_list_filter'));
	//init navigation 
	initApp.buildNavigation($('#js_nested_list'));

</script>

