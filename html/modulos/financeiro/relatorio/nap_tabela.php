					<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
                    <main id="js-page-content" role="main" class="page-content">
                        
                        <div class="row">
                            <div class="col-xl-12">
                                <div id="panel-1" class="panel">
                                    <div class="panel-hdr">
                                        <h2>
                                            Example <span class="fw-300"><i>Table</i></span>
                                        </h2>
                                        <div class="panel-toolbar">
                                            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                                            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                                            <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                                        </div>
                                    </div>
                                    <div class="panel-container show">
                                        <div class="panel-content">
  											
                                            <table id="dt-basic-example" class="table table-sm table-bordered table-hover table-striped w-100">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Position</th>
                                                        <th>Office</th>
                                                        <th>Age</th>
                                                        <th>Start date</th>
                                                        <th>Salary</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
												<?php
													$sql = "SELECT
																* 
															FROM
																coopex_usuario.evento_projeto
																INNER JOIN coopex_usuario.evento_inscricao USING ( id_evento )
																INNER JOIN coopex_usuario.evento_pessoa USING ( id_pessoa )
															WHERE
																id_projeto = 1
																AND pago = 1";
													$nap = $coopex->prepare($sql);
													$nap->execute();
													
													while($row = $nap->fetch(PDO::FETCH_OBJ)){
												?>
                                                    <tr>
														<td><?php echo utf8_encode($row->titulo)?></td>
                                                        <td><?php echo utf8_encode($row->nome)?></td>
                                                        <td>Edinburgh</td>
                                                        <td>61</td>
                                                        <td>2011/04/25</td>
                                                        <td align="right">R$ <?php echo number_format($row->valor, 2, ',', '.');?></td>
                                                    </tr>
												<?
													}
												?>	
                                                    
                                                </tbody>
                                                <tfoot class="thead-themed">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Position</th>
                                                        <th>Office</th>
                                                        <th>Age</th>
                                                        <th>Start date</th>
                                                        <th>Salary</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                            <!-- datatable end -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                    

        <script>
            $(document).ready(function()
            {
                $('#dt-basic-example').dataTable({
                    responsive: true,
                    pageLength: 15,
                    order: [
                        [2, 'desc']
                    ],
                    rowGroup:
                    {
                        dataSrc: 0
                    },"columnDefs": [{ "visible": false, "targets": 0 }]
                });
            });

        </script>
    </body>
</html>
