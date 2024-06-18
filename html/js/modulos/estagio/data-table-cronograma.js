let indiceRegistro = 0;
let atividades = [];

$("#cronograma_tabela").append(`<tfoot>
  <tr role="row" class="odd">
    <td class="sorting_1"></td>
    <td class="sorting_1"></td>
    <td class="sorting_1"></td>
    <td><strong></strong></td>
    <td id="tempoTotal"><strong>00:00</strong></td>
  </tr>
</tfoot>`);

function intervalosDeTempoDeTrintaMinutos() {
  let currentTime = 0;
  const timeArray = [];

  while (currentTime <= 2400) {
    const hours = Math.floor(currentTime / 60);
    const minutes = currentTime % 60;
    const timeStr = `${hours.toString().padStart(2, "0")}:${minutes
      .toString()
      .padStart(2, "0")}`;
    timeArray.push(timeStr);
    currentTime += 30;
  }

  timeArray.shift();

  return timeArray;
}

const opcoesTempo = intervalosDeTempoDeTrintaMinutos();

const columnSet = [
  {
    data: "indice",
    title: "indice",
    name: "Row Index",
    value: indiceRegistro,
    visible: false,
    searchable: false,
    type: "hidden",
    placeholderMsg: "Gerado automaticamente",
    render: function (data, type, row, meta) {
      indiceRegistro++;
      return indiceRegistro;
    },
  },
  {
    title: "ID",
    id: "id_cronograma",
    data: "id_cronograma",
    placeholderMsg: "Gerado automaticamente",
    visible: false,
    searchable: false,
    type: "readonly",
    name: "id_cronograma",
  },
  {
    title: "Data",
    id: "data",
    data: "data",
    type: "date",
    pattern: "((?:19|20)dd)-(0?[1-9]|1[012])-([12][0-9]|3[01]|0?[1-9])",
    placeholderMsg: "dd-mm-yyyy",
    errorMsg: "*Invalid date format. Format must be yyyy-mm-dd",
    name: "data",
    render: function (data, type, row, meta) {
      const date = data != "" ? data : new Date();
      return moment(date).format("DD/MM/YYYY");
    },
  },
  {
    title: "Carga Horária",
    id: "carga_horaria",
    data: "carga_horaria",
    name: "carga_horaria",
    type: "select",
    pattern: "((?:19|20)dd)-(0?[1-9]|1[012])-([12][0-9]|3[01]|0?[1-9])",
    placeholderMsg: "HH:mm",
    errorMsg: "*Invalid date format. Format must be yyyy-mm-dd",
    options: opcoesTempo,
  },
  {
    title: "Descrição",
    id: "descricao",
    data: "descricao",
    name: "descricao",
    type: "textarea",
    placeholderMsg: "Descreva a atividade realizada",
    value: "Hello",
  },
];

const tableButtons = [
  {
    extend: "selected",
    text: '<i class="fal fa-times mr-1"></i> Excluir',
    name: "delete",
    className: "btn-primary btn-sm mr-1",
  },
  {
    extend: "selected",
    text: '<i class="fal fa-edit mr-1"></i> Alterar',
    name: "edit",
    className: "btn-primary btn-sm mr-1",
  },
  {
    text: '<i class="fal fa-plus mr-1"></i> Inserir',
    name: "add",
    className: "btn-success btn-sm mr-1",
  },
];

const columnDefinitions = [
  {
    targets: 0,
    visible: false,
  },
  {
    targets: 2,
    render: function (data, type, full, meta) {
      return moment(data).format("DD/MM/YYYY");
    },
  },
];

const estagioId = document.querySelector("#id_estagio").value;

const handleAddRow = (dt, rowdata, success, error) => {
  const cronogramaLog = JSON.parse(document.querySelector("#cronograma").value);

  rowdata.indice = indiceRegistro;
  rowdata.acao = "i";

  cronogramaLog.push(rowdata);

  document.querySelector("#cronograma").value = JSON.stringify(cronogramaLog);
  success(rowdata);
};

const handleEditRow = (dt, rowdata, success, error) => {
  const cronogramaLog = JSON.parse(document.querySelector("#cronograma").value);

  if (rowdata.id_cronograma == "") {
    for (e of cronogramaLog) {
      if (e.indice == rowdata.indice) {
        e.id_cronograma = rowdata.id_cronograma;
        e.data = rowdata.data;
        e.carga_horaria = rowdata.carga_horaria;
        e.descricao = rowdata.descricao;
        e.acao = "i";
      }
    }

  } else {
    rowdata.acao = "u";
    
    cronogramaLog.push(rowdata);
  }
  
  document.querySelector("#cronograma").value = JSON.stringify(cronogramaLog);
  success(rowdata);
};

const handleDeleteRow = (dt, rowdata, success, error) => {
  const cronogramaLog = JSON.parse(document.querySelector("#cronograma").value);

  if (rowdata.id_cronograma == "") {
    for (e of cronogramaLog) {
      if (e.indice == rowdata.indice) {
        e.id_cronograma = "";
        e.data = "";
        e.carga_horaria = "";
        e.descricao = "";
        e.acao = "";
      }
    }
  } else {
    rowdata.acao = "d";
    
    cronogramaLog.push(rowdata);
  }
  
  document.querySelector("#cronograma").value = JSON.stringify(cronogramaLog);
  success(rowdata);
};

$("#cronograma_tabela").dataTable({
  dom:
    "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
  ajax: `modulos/estagio/cadastro/ajax/cronograma.php?id_estagio=${estagioId}`,
  columns: columnSet,
  paging: false,
  select: "single",
  altEditor: true,
  responsive: true,
  buttons: tableButtons,
  columnDefs: columnDefinitions,
  footerCallback: function (row, data, start, end, display) {
    var api = this.api();
    var tempo_total = 0;
    const columns = api.columns().count();

    for (let i = 0; i < data.length; i++) {
      temp = formataHorarioParaHorasEMinutos(data[i].carga_horaria);
      tempo_total += moment.duration(data[i].carga_horaria).asMinutes();
    }

    const dur = moment.duration(tempo_total, "minutes");
    const hours = Math.floor(dur.asHours());
    const mins = Math.floor(dur.asMinutes()) - hours * 60;
    console.log(mins);
    const result = hours + ":" + (mins > 9 ? mins : "0" + mins);
    var aprovadoCSS = "";

    if (hours >= 40) {
      aprovadoCSS = "style='color: #18a899'";
    }

    $(api.column(columns - 2).footer()).html("<strong>TOTAL</strong>");
    $(api.column(columns - 1).footer()).html(
      "<strong " + aprovadoCSS + ">" + result + "</strong>"
    );
  },
  onAddRow: handleAddRow,
  onEditRow: handleEditRow,
  onDeleteRow: handleDeleteRow,
});
