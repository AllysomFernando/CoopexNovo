    <div class="page-content header-clear-large">


        <!-- Estrutura HTML do calendário -->
        <div class="calendar bg-theme shadow-xl rounded-m">
            <div class="cal-header">
                <h4 class="cal-title text-center text-uppercase font-800 bg-dark-dark color-white">Junho 2024</h4>
                <h6 class="cal-title-left color-white"><i class="fa fa-chevron-left"></i></h6>
                <h6 class="cal-title-right color-white"><i class="fa fa-chevron-right"></i></h6>
            </div>
            <div class="clearfix"></div>
            <div class="cal-days bg-dark-dark opacity-80 bottom-0">
                <a href="#">SEG</a>
                <a href="#">TER</a>
                <a href="#">QUA</a>
                <a href="#">QUI</a>
                <a href="#">SEX</a>
                <a href="#">SAB</a>
                <a href="#">DOM</a>
                <div class="clearfix"></div>
            </div>
            <div class="cal-dates cal-dates-border">
                <!-- Datas do calendário aqui -->
            </div>
        </div>

        <div id="agenda_do_dia">
            <!-- Eventos da agenda aqui -->
        </div>

    </div>



    <script type="text/javascript" src="scripts/agenda.js?<?= rand() ?>"></script>