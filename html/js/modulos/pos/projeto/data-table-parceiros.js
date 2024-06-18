let indiceRegistro = 0;
const id_projeto = document.querySelector("#id_projeto").value;

const DataTableAction = {
  INSERT: "INSERT",
  DELETE: "DELETE",
  UPDATE: "UPDATE",
};

const columnSet = [
  {
    data: "indice",
    id: "indice",
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
    id: "id",
    data: "id",
    name: "id",
    placeholderMsg: "Gerado automaticamente",
    visible: false,
    searchable: false,
    type: "readonly",
  },
  {
    title: "Nome",
    id: "nome",
    data: "nome",
    type: "text",
    placeholderMsg: "Nome do parceiro",
    errorMsg: "Nome inválido",
    name: "nome",
    render: function (data, type, full, meta) {
      return data.toUpperCase();
    },
  },
  {
    title: "CPF",
    id: "cpf",
    data: "cpf",
    type: "text",
    placeholderMsg: "CPF do parceiro (sem pontuação)",
    errorMsg: "CPF inválido",
    name: "cpf",
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
    targets: 3,
  },
];

const handleAddRow = (dt, rowdata, success, error) => {
  const parceirosLog = JSON.parse(document.querySelector("#parceiros").value);

  rowdata.indice = indiceRegistro;
  rowdata.acao = DataTableAction.INSERT;

  parceirosLog.push(rowdata);

  document.querySelector("#parceiros").value = JSON.stringify(parceirosLog);

  success(rowdata);
};

const handleEditRow = (dt, rowdata, success, error) => {
  const cronogramaLog = JSON.parse(document.querySelector("#parceiros").value);

  if (rowdata.id_cronograma == "") {
    for (e of cronogramaLog) {
      if (e.indice == rowdata.indice) {
        e.id = "";
        e.nome = "";
        e.cpf = "";
        e.acao = DataTableAction.INSERT;
      }
    }
  } else {
    rowdata.acao = DataTableAction.UPDATE;

    cronogramaLog.push(rowdata);
  }

  document.querySelector("#parceiros").value = JSON.stringify(cronogramaLog);
  success(rowdata);
};

const handleDeleteRow = (dt, rowdata, success, error) => {
  const cronogramaLog = JSON.parse(document.querySelector("#parceiros").value);

  if (rowdata.id_cronograma == "") {
    for (e of cronogramaLog) {
      if (e.indice == rowdata.indice) {
        e.id = "";
        e.nome = "";
        e.cpf = "";
      }
    }
  } else {
    rowdata.acao = DataTableAction.DELETE;

    cronogramaLog.push(rowdata);
  }

  document.querySelector("#parceiros").value = JSON.stringify(cronogramaLog);
  success(rowdata);
};

const loadFromLocalStorage = () => {
  const formContent = JSON.parse(localStorage.getItem("cache-form"));

  if (formContent != null) {
    for (const entry of formContent) {
      if (entry[0] == "parceiros") {
        const parceiros = JSON.parse(entry[1]);
        const arr = [];

        for (const parceiro of parceiros) {
          const obj = {
            indice: parceiro.indice,
            id: parceiro.id,
            nome: parceiro.nome,
            cpf: parceiro.cpf,
          };
          arr.push(obj);
        }

        return arr;
      }
    }
  }
};

const dataSet = loadFromLocalStorage();

function createDataTableParceiros(editable = true) {
  $("#parceiros_table").dataTable({
    dom:
      "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    ajax: `https://coopex.fag.edu.br/modulos/pos/projeto/api/routes/get-parceiros.php?id=${id_projeto}`,
    columns: columnSet,
    data: dataSet,
    paging: false,
    select: "single",
    altEditor: editable,
    responsive: true,
    buttons: editable ? tableButtons : [],
    columnDefs: columnDefinitions,
    order: [[2, "asc"]],
    onAddRow: handleAddRow,
    onEditRow: handleEditRow,
    onDeleteRow: handleDeleteRow,
  });
}
