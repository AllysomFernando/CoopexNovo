const ListHandlerActions = {
  INSERT: "INSERT",
  DELETE: "DELETE",
};

class ListHandler {
  htmlListElementId;
  htmlLogElementId;
  deleteIdentifierClassName;

  constructor(listHandlerConfig) {
    this.htmlListElementId = listHandlerConfig.htmlListElementId;
    this.htmlLogElementId = listHandlerConfig.htmlLogElementId;
    this.deleteIdentifierClassName =
      listHandlerConfig.deleteIdentifierClassName;
  }

  createBadge(value) {
    const badgeItem = document.createElement("span");
    badgeItem.classList.add("badge", "badge-primary", "badge-pill");
    badgeItem.innerHTML = value;

    return badgeItem;
  }

  createDeleteButton(identifierClassName) {
    const deleteItem = document.createElement("button");
    deleteItem.type = "button";
    deleteItem.classList.add("btn", "btn-danger", "." + identifierClassName);
    deleteItem.innerHTML = `<i class="fal fa-trash-alt"></i>`;

    return deleteItem;
  }

  createHiddenInput(id) {
    const input = document.createElement("input");
    input.setAttribute("type", "text");
    input.setAttribute("value", id);
    input.setAttribute("hidden", "hidden");

    return input;
  }

  createDisplayDiv(displayString) {
    const div = document.createElement("div");
    div.classList.add("w-25")
    div.innerHTML = displayString;

    return div;
  }

  createItem(data) {
    const logElement = document.querySelector(`#${this.htmlLogElementId}`);
    const logAsJson = JSON.parse(logElement?.value ? logElement?.value : []);
    const nextListIndex = logAsJson.length + 1;

    const isAlreadyOnList = this.itemIsAlreadyOnList(data.inputValue);

    if (isAlreadyOnList) return null;

    const itemList = document.createElement("li");
    const badge = this.createBadge(data.badge);
    const deleteBtn = this.createDeleteButton(this.deleteIdentifierClassName);
    const input = this.createHiddenInput(data.inputValue);
    const displayDiv = this.createDisplayDiv(data.displayName)

    this.deleteListener(deleteBtn);

    itemList.classList.add(
      "list-group-item",
      "d-flex",
      "justify-content-between",
      "align-items-center"
    );
    itemList.appendChild(displayDiv);
    itemList.appendChild(input);
    itemList.appendChild(badge);
    itemList.appendChild(deleteBtn);

    for (const attr of data.attributes) {
      itemList.setAttribute(`data-${attr[0]}`, attr[1]);
    }

    itemList.setAttribute("data-list", nextListIndex);

    return itemList;
  }

  appendItem(item) {
    const list = document.querySelector(`#${this.htmlListElementId}`);
    list?.appendChild(item);
  }

  deleteListener(btn) {
    btn.addEventListener("click", (e) => {
      let item = e.target;

      if (item.tagName != "LI") {
        while (item.tagName != "LI") {
          const temp = item;
          item = temp.parentElement;
        }
      }

      const itemId = item.getAttribute("data-id");
      console.log(itemId)
      this.deleteFromLog({ id: itemId });

      item.remove();
    });
  }

  hasItems() {
    const list = document.querySelector(`#${this.htmlListElementId}`);
    return list?.children.length > 0;
  }

  itemIsAlreadyOnList(id) {
    const list = document.querySelector(`#${this.htmlListElementId}`);
    for (const listItem of list.children) {
      if (listItem.getAttribute("data-id") == id) return true;
    }
    return false;
  }

  appendToLog(item) {
    const jsonLog = this.getLogAsJson();
    const exists = this.existsInLogById(item.id);

    if (!exists || jsonLog.length == 0) {
      item.acao = ListHandlerActions.INSERT;
      jsonLog.push(item);
    } else {
      for (const jsonItemLog of jsonLog) {
        if (jsonItemLog.id == item.id) {
          jsonItemLog.acao = ListHandlerActions.INSERT;
        }
      }
    }

    console.log("NEW APPEND LOG", jsonLog);

    this.setNewLogData(jsonLog);
  }

  deleteFromLog(item) {
    const jsonLog = this.getLogAsJson();
    const exists = this.existsInLogById(item.id);

    if (!exists || jsonLog.length == 0) {
      return;
    } else {
      for (const jsonItemLog of jsonLog) {
        if (jsonItemLog.id == item.id) {
          jsonItemLog.acao = ListHandlerActions.DELETE;
        }
      }
    }

    console.log("NEW LOG DELETED", jsonLog);

    this.setNewLogData(jsonLog);
  }

  existsInLogById(id) {
    const jsonLog = this.getLogAsJson();

    for (const item of jsonLog) {
      if (item.id == id) {
        return true;
      }
    }

    return false;
  }

  getLogAsJson() {
    const jsonLog = JSON.parse(
      document.querySelector(`#${this.htmlLogElementId}`).value
    );
    let i = 1;

    for (const item of jsonLog) {
      item.listIndex = i;
      i++;
    }

    return jsonLog;
  }

  setNewLogData(log) {
    document.querySelector(`#${this.htmlLogElementId}`).value =
      JSON.stringify(log);
  }
}

// const listHandlerDocente2 = new ListHandler({
//   htmlListElementId: "docentes_list",
//   htmlLogElementId: "docentes_log",
//   deleteIdentifierClassName: "removerDaDisciplina",
// });

// for (let i = 0; i < 20; i++) {
//   const logObject = {
//     id: i,
//     nome: "HELLO",
//   };

//   const listItemData = {
//     badge: "WORLD",
//     inputValue: logObject.id,
//     displayName: logObject.nome,
//     attributes: [
//       ["id", logObject.id],
//     ],
//   };

//   let itemList = listHandlerDocente2.createItem(listItemData);
//   if (itemList) {
//     listHandlerDocente2.appendItem(itemList);
//   }
// }

async function getDisciplinasOptions() {
  const select = document.querySelector("#disciplinas_select");
  const apiURL = "modulos/pos/projeto/api/routes/get-disciplinas-by-user.php";
  const pessoaId = document.querySelector("#id_pessoa");

  const urlParams = new URLSearchParams({
    id_pessoa: pessoaId.value,
  });

  const apiData = await fetch(apiURL + "?" + urlParams, {
    method: "GET",
  });

  const jsonDisciplinas = await apiData.json();
  // const localData = JSON.parse(localStorage.getItem("disciplinas")) ?? [];
  // let parsedDisciplinas = [];

  // for (const temp of localData) {
  //   parsedDisciplinas.push(JSON.parse(temp));
  // }

  select.innerHTML = "";

  const disciplinas = [...jsonDisciplinas];

  for (const disciplina of disciplinas) {
    const option = document.createElement("option");
    option.setAttribute("value", disciplina.id);
    option.setAttribute("data-carga_horaria", disciplina.carga_horaria);
    option.setAttribute("data-nome", disciplina.nome);
    option.setAttribute("data-id_titulacao", disciplina.id_titulacao);
    option.innerHTML = disciplina.nome;

    select.appendChild(option);
  }
}

async function getDocentesOptions() {
  const select = document.querySelector("#docentes_select");
  const request = await fetch(
    "modulos/pos/projeto/api/routes/get-docentes.php",
    {
      method: "GET",
    }
  );

  select.innerHTML = "";
  const docentes = await request.json();

  for (const docente of docentes.data) {
    const option = document.createElement("option");
    option.setAttribute("value", docente.id_docente);
    option.setAttribute("data-id_titulacao", docente.id_titulacao);
    option.setAttribute("data-titulacao", docente.titulacao);
    option.setAttribute("data-docente", docente.id_docente);
    option.setAttribute("data-nome", docente.nome);
    option.innerHTML = docente.nome;

    select.appendChild(option);
  }
}

function calculatePorcentagemDocente() {
  const listDisciplinas = document.querySelector("#lista_disciplinas");
  const qntdDisciplinas =
    listDisciplinas.children.length > 0 ? listDisciplinas.children.length : 0;
  let counter = 0;

  for (disciplina of listDisciplinas.children) {
    if (
      disciplina.getAttribute("data-id_titulacao") == 2 ||
      disciplina.getAttribute("data-id_titulacao") == 3
    ) {
      counter++;
    }
  }

  const res = (counter / qntdDisciplinas) * 100 || 0;

  const porcentagemDiv = document.querySelector("#porcentagem-docente");
  porcentagemDiv.innerHTML = `${Number(res).toFixed(1)}%`;

  return Number(res).toFixed(1);
}
