<?php if ($isAdmin) { ?>

  <div class="row">
    <div class="col-xl-12">
      <div id="panel-1" class="panel">
        <div class="panel-hdr">
          <?php $badge = getTicketPanelBadge(4); ?>
          <h2>
            <span class="badge <?php echo $badge->class_name ?> fw-400 l-h-n">
              <?php echo $badge->message ?>
            </span>
          </h2>
          <div class="panel-toolbar">
            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
          </div>
        </div>
        <div class="panel-container show">
          <div class="panel-content">
            <?php if (count($tickets) > 0 && $id_pessoa != $tickets[0]->id_usuario) { ?>
              <!-- datatable start -->
              <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100 dt-basic-example">
                <thead>
                  <tr>
                    <th>Id Usuario</th>
                    <th>Data de envio</th>
                    <th>Titulo</th>
                    <th>URL</th>
                    <th>Status</th>
                    <th>Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($tickets as $ticket) {
                    if ($ticket->status == 4) { ?>

                      <tr>
                        <td>
                          <?= texto($ticket->id_usuario) ?>
                        </td>
                        <td>
                          <?php echo date_format(new DateTime($ticket->data_envio), "d/m/Y") ?>
                        </td>
                        <td>
                          <?= ($ticket->titulo) ?>
                        </td>
                        <td>
                          <?= ($ticket->url) ?>
                        </td>
                        <td>
                          AGUARDANDO
                        </td>

                        <td>
                          <a href="coopex/ticket/atendimento/<?php echo $ticket->id ?>" class="btn btn-icon btn-success w-100" title="Iniciar atendimento">
                            <i class="fal fa-comment-alt"></i>
                          </a>
                        </td>
                      </tr>
                  <?php
                    }
                  }
                  ?>
                </tbody>
              </table>
              <!-- datatable end -->
            <?php } ?>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="row">
    <div class="col-xl-12">
      <div id="panel-1" class="panel">
        <div class="panel-hdr">
          <?php $badge = getTicketPanelBadge(3); ?>
          <h2>
            <span class="badge <?php echo $badge->class_name ?> fw-400 l-h-n">
              <?php echo $badge->message ?>
            </span>
          </h2>
          <div class="panel-toolbar">
            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
          </div>
        </div>
        <div class="panel-container show">
          <div class="panel-content">
            <?php if (count($tickets) > 0 && $id_pessoa != $tickets[0]->id_usuario) { ?>
              <!-- datatable start -->
              <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100 dt-basic-example">
                <thead>
                  <tr>
                    <th>Id Usuario</th>
                    <th>Data de envio</th>
                    <th>Titulo</th>
                    <th>URL</th>
                    <th>Status</th>
                    <th>Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($tickets as $ticket) {
                    if ($ticket->status == 3) { ?>

                      <tr>
                        <td>
                          <?= texto($ticket->id_usuario) ?>
                        </td>
                        <td>
                          <?php echo date_format(new DateTime($ticket->data_envio), "d/m/Y") ?>
                        </td>
                        <td>
                          <?= ($ticket->titulo) ?>
                        </td>
                        <td>
                          <?= ($ticket->url) ?>
                        </td>
                        <td>
                          EM ANDAMENTO
                        </td>

                        <td>
                          <a href="coopex/ticket/atendimento/<?php echo $ticket->id ?>" class="btn btn-icon btn-success w-100" title="Iniciar atendimento">
                            <i class="fal fa-comment-alt"></i>
                          </a>
                        </td>
                      </tr>
                  <?php
                    }
                  }
                  ?>
                </tbody>
              </table>
              <!-- datatable end -->
            <?php } else { ?>

              <div class="col-xl-12">
                <div class="border-faded bg-faded p-3 mb-g d-flex">
                  <input type="text" id="js-filter-tickets" name="filter-contacts" class="form-control shadow-inset-2 form-control-lg" placeholder="Filtrar tickets">
                </div>
              </div>

              <div id="ticket-contacts" class="d-flex align-items-center justify-content-flex-start flex-wrap">
                <?php
                foreach ($tickets as $ticket) {
                  $badge = getTicketPanelBadge($ticket->status)
                ?>

                  <div class="col-xl-4 w-100 h-300">
                    <div id="c_1" class="card border shadow-0 mb-g shadow-sm-hover" data-filter-tags="<?php echo strtolower($ticket->titulo) ?>">
                      <div class="card-body border-faded border-top-0 border-left-0 border-right-0 rounded-top">
                        <div class="d-flex flex-row align-items-center">
                          <div class="info-card-text flex-1">
                            <h3 class="color-primary-400">
                              <strong><?php echo $ticket->titulo ?></strong>
                              <span class="badge <?php echo $badge->class_name ?> fw-400 l-h-n ml-2">
                                <?php echo $badge->message ?>
                              </span>
                            </h3>
                            <span class="text-truncate text-truncate-xl"><?php echo $ticket->data_envio ?></span>
                          </div>

                        </div>
                      </div>
                      <div class="card-body p-0 collapse show">
                        <div class="p-3">
                          <p><?php echo $ticket->descricao ?></p>
                          <div class="d-flex flex-row">
                            <a href="coopex/ticket/atendimento/<?php echo $ticket->id ?>" class="btn btn-icon btn-primary w-100" title="Atendimento">
                              <i class="fal fa-comment-alt"></i> Atendimento
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                <?php } ?>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="row">
    <div class="col-xl-12">
      <div id="panel-1" class="panel">
        <div class="panel-hdr">
          <?php $badge = getTicketPanelBadge(1); ?>
          <h2>
            <span class="badge <?php echo $badge->class_name ?> fw-400 l-h-n">
              <?php echo $badge->message ?>
            </span>
          </h2>
          <div class="panel-toolbar">
            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
          </div>
        </div>
        <div class="panel-container show">
          <div class="panel-content">
            <?php if (count($tickets) > 0 && $id_pessoa != $tickets[0]->id_usuario) { ?>
              <!-- datatable start -->
              <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100 dt-basic-example">
                <thead>
                  <tr>
                    <th>Id Usuario</th>
                    <th>Data de envio</th>
                    <th>Titulo</th>
                    <th>URL</th>
                    <th>Status</th>
                    <th>Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($tickets as $ticket) {
                    if ($ticket->status == 1) { ?>
                      <tr>
                        <td>
                          <?= texto($ticket->id_usuario) ?>
                        </td>
                        <td>
                          <?php echo date_format(new DateTime($ticket->data_envio), "d/m/Y") ?>
                        </td>
                        <td>
                          <?= ($ticket->titulo) ?>
                        </td>
                        <td>
                          <?= ($ticket->url) ?>
                        </td>
                        <td>
                          SOLUCIONADO
                        </td>

                        <td>
                          <a href="coopex/ticket/atendimento/<?php echo $ticket->id ?>" class="btn btn-icon btn-success w-100" title="Iniciar atendimento">
                            <i class="fal fa-comment-alt"></i>
                          </a>
                        </td>
                      </tr>
                  <?php
                    }
                  }
                  ?>
                </tbody>
              </table>
              <!-- datatable end -->
            <?php } else { ?>

              <div class="col-xl-12">
                <div class="border-faded bg-faded p-3 mb-g d-flex">
                  <input type="text" id="js-filter-tickets" name="filter-contacts" class="form-control shadow-inset-2 form-control-lg" placeholder="Filtrar tickets">
                </div>
              </div>

              <div id="ticket-contacts" class="d-flex align-items-center justify-content-flex-start flex-wrap">
                <?php
                foreach ($tickets as $ticket) {
                  $badge = getTicketPanelBadge($ticket->status)
                ?>

                  <div class="col-xl-4 w-100 h-300">
                    <div id="c_1" class="card border shadow-0 mb-g shadow-sm-hover" data-filter-tags="<?php echo strtolower($ticket->titulo) ?>">
                      <div class="card-body border-faded border-top-0 border-left-0 border-right-0 rounded-top">
                        <div class="d-flex flex-row align-items-center">
                          <div class="info-card-text flex-1">
                            <h3 class="color-primary-400">
                              <strong><?php echo $ticket->titulo ?></strong>
                              <span class="badge <?php echo $badge->class_name ?> fw-400 l-h-n ml-2">
                                <?php echo $badge->message ?>
                              </span>
                            </h3>
                            <span class="text-truncate text-truncate-xl"><?php echo $ticket->data_envio ?></span>
                          </div>

                        </div>
                      </div>
                      <div class="card-body p-0 collapse show">
                        <div class="p-3">
                          <p><?php echo $ticket->descricao ?></p>
                          <div class="d-flex flex-row">
                            <a href="coopex/ticket/atendimento/<?php echo $ticket->id ?>" class="btn btn-icon btn-primary w-100" title="Atendimento">
                              <i class="fal fa-comment-alt"></i> Atendimento
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                <?php } ?>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="row">
    <div class="col-xl-12">
      <div id="panel-1" class="panel">
        <div class="panel-hdr">
          <?php $badge = getTicketPanelBadge(2); ?>
          <h2>
            <span class="badge <?php echo $badge->class_name ?> fw-400 l-h-n">
              <?php echo $badge->message ?>
            </span>
          </h2>
          <div class="panel-toolbar">
            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
          </div>
        </div>
        <div class="panel-container show">
          <div class="panel-content">
            <?php if (count($tickets) > 0 && $id_pessoa != $tickets[0]->id_usuario) { ?>
              <!-- datatable start -->
              <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100 dt-basic-example">
                <thead>
                  <tr>
                    <th>Id Usuario</th>
                    <th>Data de envio</th>
                    <th>Titulo</th>
                    <th>URL</th>
                    <th>Status</th>
                    <th>Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($tickets as $ticket) {
                    if ($ticket->status == 2) { ?>

                      <tr>
                        <td>
                          <?= texto($ticket->id_usuario) ?>
                        </td>
                        <td>
                          <?php echo date_format(new DateTime($ticket->data_envio), "d/m/Y") ?>
                        </td>
                        <td>
                          <?= ($ticket->titulo) ?>
                        </td>
                        <td>
                          <?= ($ticket->url) ?>
                        </td>
                        <td>
                          CANCELADO
                        </td>

                        <td>
                          <a href="coopex/ticket/atendimento/<?php echo $ticket->id ?>" class="btn btn-icon btn-success w-100" title="Iniciar atendimento">
                            <i class="fal fa-comment-alt"></i>
                          </a>
                        </td>
                      </tr>
                  <?php
                    }
                  }
                  ?>
                </tbody>
              </table>
              <!-- datatable end -->
            <?php } else { ?>

              <div class="col-xl-12">
                <div class="border-faded bg-faded p-3 mb-g d-flex">
                  <input type="text" id="js-filter-tickets" name="filter-contacts" class="form-control shadow-inset-2 form-control-lg" placeholder="Filtrar tickets">
                </div>
              </div>

              <div id="ticket-contacts" class="d-flex align-items-center justify-content-flex-start flex-wrap">
                <?php
                foreach ($tickets as $ticket) {
                  $badge = getTicketPanelBadge($ticket->status)
                ?>

                  <div class="col-xl-4 w-100 h-300">
                    <div id="c_1" class="card border shadow-0 mb-g shadow-sm-hover" data-filter-tags="<?php echo strtolower($ticket->titulo) ?>">
                      <div class="card-body border-faded border-top-0 border-left-0 border-right-0 rounded-top">
                        <div class="d-flex flex-row align-items-center">
                          <div class="info-card-text flex-1">
                            <h3 class="color-primary-400">
                              <strong><?php echo $ticket->titulo ?></strong>
                              <span class="badge <?php echo $badge->class_name ?> fw-400 l-h-n ml-2">
                                <?php echo $badge->message ?>
                              </span>
                            </h3>
                            <span class="text-truncate text-truncate-xl"><?php echo $ticket->data_envio ?></span>
                          </div>

                        </div>
                      </div>
                      <div class="card-body p-0 collapse show">
                        <div class="p-3">
                          <p><?php echo $ticket->descricao ?></p>
                          <div class="d-flex flex-row">
                            <a href="coopex/ticket/atendimento/<?php echo $ticket->id ?>" class="btn btn-icon btn-primary w-100" title="Atendimento">
                              <i class="fal fa-comment-alt"></i> Atendimento
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                <?php } ?>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>

  </div>


<?php } ?>