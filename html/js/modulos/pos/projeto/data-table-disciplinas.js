function createDataTableDisciplina() {
  let indiceRegistroDisciplina = 0;

  const columnSetDisciplina = [
    {
      data: "indice",
      title: "indice",
      name: "Row Index",
      value: indiceRegistroDisciplina,
      visible: false,
      searchable: false,
      type: "hidden",
      placeholderMsg: "Gerado automaticamente",
      render: function (data, type, row, meta) {
        indiceRegistroDisciplina++;
        return indiceRegistroDisciplina;
      },
    },
    {
      title: "ID",
      id: "id",
      data: "id",
      placeholderMsg: "Gerado automaticamente",
      visible: false,
      searchable: false,
      type: "readonly",
      name: "id",
    },
    {
      title: "Nome",
      id: "nome",
      data: "nome",
      type: "text",
      placeholderMsg: "Nome do parceiro",
      errorMsg: "Nome inválido",
      name: "nome",
    },
  ];

  const columnDefinitionsDisciplina = [
    {
      targets: 0,
      visible: false,
    },
  ];

  const handleAddRowDisciplina = (dt, rowdata, success, error) => {
    const cronogramaLog = JSON.parse(
      document.querySelector("#disciplinas").value
    );

    rowdata.indice = indiceRegistroDisciplina;
    rowdata.acao = DataTableAction.INSERT;

    cronogramaLog.push(rowdata);

    document.querySelector("#disciplinas").value =
      JSON.stringify(cronogramaLog);
    success(rowdata);
  };

  const handleEditRowDisciplina = (dt, rowdata, success, error) => {
    const cronogramaLog = JSON.parse(
      document.querySelector("#disciplinas").value
    );

    if (rowdata.id_cronograma == "") {
      for (e of cronogramaLog) {
        if (e.indice == rowdata.indice) {
          e.id_cronograma = rowdata.id_cronograma;
          e.data = rowdata.data;
          e.carga_horaria = rowdata.carga_horaria;
          e.descricao = rowdata.descricao;
          e.acao = DataTableAction.INSERT;
        }
      }
    } else {
      rowdata.acao = DataTableAction.UPDATE;

      cronogramaLog.push(rowdata);
    }

    document.querySelector("#disciplinas").value =
      JSON.stringify(cronogramaLog);
    success(rowdata);
  };

  const handleDeleteRowDisciplina = (dt, rowdata, success, error) => {
    const cronogramaLog = JSON.parse(
      document.querySelector("#disciplinas").value
    );

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
      rowdata.acao = DataTableAction.DELETE;

      cronogramaLog.push(rowdata);
    }

    document.querySelector("#disciplinas").value =
      JSON.stringify(cronogramaLog);
    success(rowdata);
  };

  $("#disciplinas_table").dataTable({
    dom:
      "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    ajax: ``,
    columns: columnSetDisciplina,
    paging: false,
    select: "single",
    altEditor: true,
    responsive: true,
    buttons: tableButtons,
    columnDefs: columnDefinitionsDisciplina,
    footerCallback: function (row, data, start, end, display) {
      var api = this.api();
    },
    onAddRow: handleAddRowDisciplina,
    onEditRow: handleEditRowDisciplina,
    onDeleteRow: handleDeleteRowDisciplina,
  });
}
