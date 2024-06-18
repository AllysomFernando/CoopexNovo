//FORMATA A EXIBIÇÃO DO TEXTO DA DISCIPLINA
function formatoDisciplina(repo) {
  if (repo.loading) {
    return repo.text;
  }

  var markup =
    "<div class='select2-result-repository clearfix d-flex'>" +
    "<div class='select2-result-repository__avatar mr-2'><img src='https://coopex.fag.edu.br/img/departamentos/" +
    repo.crs_id_curso +
    ".png' class='width-2 height-2 mt-1 rounded-circle' /></div>" +
    "<div class='select2-result-repository__meta'>" +
    "<div><span class='select2-result-repository__title fs-lg fw-500'>" +
    repo.text +
    "</span>" +
    " (" +
    repo.atc_qt_horas +
    " - " +
    " horas)</div>";
  markup +=
    "<div class='select2-result-repository__description fs-xs opacity-80 mb-1'>" +
    repo.curso +
    "</div>";
  markup += "</div></div>";
  return markup;
}
function formatoTextoDisciplina(repo) {
  //console.log(repo);
  return repo.text || "";
}

function solicitarPermissao() {
  alert("Função indisponível para esta tela");
}

function formatoUsuario(repo) {
  if (repo.loading) {
    return repo.text;
  }

  var markup =
    "<div class='select2-result-repository clearfix d-flex'>" +
    "<div class='select2-result-repository__avatar mr-2'><img src='https://coopex.fag.edu.br/img/avatars/avatar-" +
    repo.sexo +
    ".png' class='width-2 height-2 mt-1 rounded-circle' /></div>" +
    "<div class='select2-result-repository__meta'>" +
    "<div><span class='select2-result-repository__title fs-lg fw-500'>" +
    repo.text +
    "</span>" +
    " (" +
    repo.usuario +
    ")</div>";

  markup +=
    "<div class='select2-result-repository__description fs-xs opacity-80 mb-1'>" +
    repo.tipo_descricao +
    "</div>";

  markup += "</div></div>";

  return markup;
}

function formatoTextoUsuario(repo) {
  return repo.text || "";
}

function subtraiHora(startTime, endTime) {
  var start = moment(startTime, "HH:mm");
  var end = moment(endTime, "HH:mm");
  var minutes = end.diff(start, "minutes");
  var divisao = minutes / 50;
  minutes = 59 * divisao;

  var interval = moment().hour(0).minute(minutes);

  interval.subtract(0.15, "minutes");
  return interval.format("HH:mm");
}

/**
 * Verifica o tamanho do arquivo sendo enviado através do input
 * 
 * @param {String} Id - Id do elemento input
 * @param {Integer} maxFileSize - Tamanho máximo do arquivo em MB
 * @returns {void}
 */
function validateFileSize(id, maxFileSize) {
  const input = document.querySelector(id);
  const fileSize = input.files[0].size / 1024 / 1024;
  if (fileSize > maxFileSize) {
    alert(`O arquivo excede o tamanho máximo de ${maxFileSize} MB`);
    return;
  }
}
