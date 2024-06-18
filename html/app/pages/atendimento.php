<?
require_once("class/materiais.php");

$material = listar_material();
$mochila = listar_mochila();

?>

<div class="page-content header-clear-large">

    <div class="card card-style">
        <div class="content">
            <h2 class="mb-0">
                Protocolo de Atendimento
            </h2>
            <div class="d-flex mt-4">
                <div><img src="images/coordenacao/kelly.jpg" width="40" class="rounded-sm"></div>
                <div>
                    <h5 class="mx-2">Kelly</h5>
                    <p class="mb-0 mt-n2 font-12 mx-2">15/04/2024 - 16:45</p>
                </div>
            </div>
            <p class="mb-2">
            <p>A Sra. Ana Silva, mãe do aluno Lucas Silva, entrou em contato com a coordenação da escola para expressar suas preocupações sobre o comportamento e desempenho acadêmico de seu filho. Ela relata uma queda significativa no desempenho de Lucas nas últimas semanas, além de relatos de professores sobre seu comportamento distraído em sala de aula.</p>
            <p>
                A Sra. Silva expressou sua preocupação com a falta de interesse de Lucas nas atividades escolares e solicitou uma reunião para discutir mais detalhadamente as preocupações mencionadas. Ela demonstrou interesse em colaborar com a escola para implementar estratégias que possam apoiar o progresso acadêmico e comportamental de Lucas.</p>
            <p>
                A coordenação registrou as informações fornecidas pela Sra. Silva e se comprometeu a agendar uma reunião com ela o mais breve possível para discutir o assunto em detalhes e desenvolver um plano de ação para apoiar o aluno.</p>
        </div>
        <div class="divider divider-margins mb-0"></div>
        <div class="content">
            <div class="d-flex">
                <div><img src="images/coordenacao/kelly.jpg" width="40" class="rounded-sm"></div>
                <div>
                    <h5 class="mx-2">Kelly</h5>
                    <p class="mb-0 mt-n2 font-12 mx-2">16/04/2024 - 09:32</p>
                </div>
            </div>
            <p class="mb-2">Durante a reunião, teremos a oportunidade de ouvir suas preocupações em detalhes, compartilhar informações sobre o desempenho de Lucas na escola e colaborar na identificação de estratégias que possam apoiar seu progresso acadêmico e comportamental.
            </p>
            <p>
                Por favor, confirme sua disponibilidade para a reunião e, se houver algum tópico específico que você gostaria de discutir, sinta-se à vontade para compartilhar conosco antecipadamente.</p>


        </div>

        <div class="content">
            <div class="divider divider-margins mb-0"></div>
            <h2 class="mt-4">Feedback do atendimento</h2>

            <div class="d-flex mt-5">
                <div class="m-auto text-center"><a href="#" class="color-theme opacity-70 font-10"><span class="fa-5x">😀</span>
                        <p class="font-12">Satisfeita</p>
                    </a></div>
                <div class="m-auto text-center"><a href="#" class="color-theme opacity-70 font-10"><span class="fa-5x">😐</span>
                        <p class="font-12">Parcialmente Satisfeita</p>
                    </a></div>
                <div class="m-auto text-center"><a href="#" class="color-theme opacity-70 font-10"><span class="fa-5x">🙁</span>
                        <p class="font-12">Não Satisfeita</p>
                    </a></div>
            </div>
        </div>

        <!-- <div class="divider divider-margins mb-0"></div>
        <div class="content">
            <h4>Leave a reply.</h4>
            <p class="font-12">
                Please keep in mind of our <a href="page-terms.html">Terms and Conditions</a>
            </p>
            <div class="input-style no-borders no-icon validate-field mb-4">
                <input type="name" class="form-control validate-name" id="form1a" placeholder="Name">
                <label for="form1a" class="color-highlight">Name</label>
                <i class="fa fa-times disabled invalid color-red-dark"></i>
                <i class="fa fa-check disabled valid color-green-dark"></i>
                <em>(required)</em>
            </div>
            <div class="input-style no-borders no-icon validate-field mb-4">
                <input type="email" class="form-control validate-text" id="form2a" placeholder="Email">
                <label for="form2a" class="color-highlight">Email</label>
                <i class="fa fa-times disabled invalid color-red-dark"></i>
                <i class="fa fa-check disabled valid color-green-dark"></i>
                <em>(required)</em>
            </div>
            <div class="input-style no-borders no-icon mb-4">
                <textarea id="form7a" placeholder="Enter your message"></textarea>
                <label for="form7a" class="color-highlight">Enter your Message</label>
                <em class="mt-n3">(required)</em>
            </div>
        </div> -->
    </div>


</div>