class DisciplinasLog {
  htmlId = "disciplinas_log";

  getLogAsJson() {
    const disciplinasLog = JSON.parse(
      document.querySelector(`#${this.htmlId}`).value
    );
    let i = 1;

    for (const disciplina of disciplinasLog) {
      disciplina.listIndex = i;
      i++;
    }

    return disciplinasLog;
  }

  getDisciplinaById(id) {
    const disciplinasLog = this.getLogAsJson();

    for (const disciplinaLog of disciplinasLog) {
      if (disciplinaLog.id == id) {
        return disciplinaLog;
      }
    }

    return null;
  }

  removeDisciplinaById(id, disciplinasLog) {
    disciplinasLog.filter((element) => element.id != id);
  }

  addToLog(disciplina) {
    const disciplinasLog = this.getLogAsJson();
    const newDisciplinas = [];
    const exists = this.existsInLog(disciplina)

    if (disciplinasLog.length > 0) {
      for (const disciplinaLog of disciplinasLog) {
        if (disciplinaLog.id == disciplina.id) {
          disciplinaLog.acao = disciplina.acao;
        }
      }

      if (!exists) {
        disciplinasLog.push(disciplina);
      }
    } else {
      disciplinasLog.push(disciplina);
    }

    console.log("NEW LOG")
    console.log(disciplinasLog)

    this.updateLogData(disciplinasLog);
  }

  updateLogData(disciplinas) {
    document.querySelector(`#${this.htmlId}`).value =
      JSON.stringify(disciplinas);
  }

  existsInLog(disciplina) {
    const disciplinasLog = this.getLogAsJson();

    for (const disciplinaLog of disciplinasLog) {
      if (disciplinaLog.id == disciplina.id) {
        return true;
      }
    }

    return false;
  }
}

function listCreateBadge(value) {
  const badgeItem = document.createElement("span");
  badgeItem.classList.add("badge", "badge-primary", "badge-pill");
  badgeItem.innerHTML = horas;

  return badgeItem;
}

function listCreateDeleteButton() {
  const deleteItem = document.createElement("button");
  deleteItem.type = "button";
  deleteItem.classList.add("btn", "btn-danger", "removerDoCurso");
  deleteItem.innerHTML = "X";

  return deleteItem;
}

function listCreateInput(id) {
  const input = document.createElement("input");
  input.setAttribute("type", "text");
  input.setAttribute("value", id);
  input.setAttribute("hidden", "hidden");

  return input;
}

function listCreateItem(disciplina) {
  const disciplinasLog = new DisciplinasLog();
  const json = {};
  const exists = disciplinasLog.getDisciplinaById(disciplina.id)

  if (exists && exists.acao == "INSERT") {
    alert("Esta disciplina já está anexada no projeto")
    return false
  }

  const nextListIndex = disciplinasLog.getLogAsJson().length + 1;

  const itemList = document.createElement("li");
    const badge = listCreateBadge(disciplina.carga_horaria);
    const deleteBtn = listCreateDeleteButton();
    const input = listCreateInput(disciplina.id, disciplina.nome);

    itemList.classList.add(
      "list-group-item",
      "d-flex",
      "justify-content-between",
      "align-items-center"
    );
    itemList.innerHTML = disciplina.nome;
    itemList.appendChild(input);
    itemList.appendChild(badge);
    itemList.appendChild(deleteBtn);
    itemList.setAttribute("data-docente", disciplina.id_titulacao);
    itemList.setAttribute("data-disciplina", disciplina.id);
    itemList.setAttribute("data-list", nextListIndex);

    json.id = disciplina.id;
    json.nome = disciplina.nome;
    json.id_titulacao = disciplina.id_titulacao;
    json.carga_horaria = disciplina.carga_horaria;
    json.acao = "INSERT";
    json.listIndex = nextListIndex;

    disciplinasLog.addToLog(json);

    return itemList;
}

async function getDisciplinas() {
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
  const localData = JSON.parse(localStorage.getItem("disciplinas")) ?? [];
  let parsedDisciplinas = [];

  for (const temp of localData) {
    parsedDisciplinas.push(JSON.parse(temp));
  }

  select.innerHTML = "";

  const disciplinas = [...parsedDisciplinas, ...jsonDisciplinas];

  for (const disciplina of disciplinas) {
    const parsed = disciplina;
    const option = document.createElement("option");
    option.setAttribute("value", parsed.id);
    option.innerHTML = parsed.nome;

    select.appendChild(option);
  }
}

async function getDocentes() {
  const select = document.querySelector("#docentes_select");
  const request = await fetch(
    "modulos/pos/projeto/api/routes/get-docentes.php",
    {
      method: "GET",
    }
  );

  select.innerHTML = "";
  const docentes = await request.json();
  const optionsList = [];

  for (docente of docentes.data) {
    const option = document.createElement("option");
    option.setAttribute("value", docente.id_docente);
    option.innerHTML = docente.nome;

    select.appendChild(option);
  }
}

function removeListItem() {
  const disciplinasLog = JSON.parse(
    document.querySelector("#disciplinas_log").value
  );
  const elements = document.querySelectorAll(".removerDoCurso");
  for (const [index, element] of elements.entries()) {
    element.addEventListener("click", (e) => {
      const json = {};
      const item = e.target.parentNode;
      const itemIndex = item.getAttribute("data-list");

      json.id = item.getAttribute("data-disciplina");
      json.acao = "DELETE";

      // console.log(index, element, disciplinasLog)

      // disciplinasLog[itemIndex].acao = json.acao
      // disciplinasLog[itemIndex].id = ""

      for (const disciplinaLog of disciplinasLog) {
        if (disciplinaLog.listIndex == itemIndex || disciplinaLog.id == json.id) {
          disciplinaLog.id = json.id;
          disciplinaLog.acao = json.acao;
        }
      }

      console.log(disciplinasLog, json, itemIndex)

      document.querySelector("#disciplinas_log").value =
        JSON.stringify(disciplinasLog);

      e.target.parentElement.remove();
    });
  }
}

function calculatePorcentagemDocente() {
  const listDisciplinas = document.querySelector("#lista_disciplinas");
  const qntdDisciplinas =
    listDisciplinas.children.length > 0 ? listDisciplinas.children.length : 0;
  let counter = 0;

  for (disciplina of listDisciplinas.children) {
    if (
      disciplina.getAttribute("data-docente") == 2 ||
      disciplina.getAttribute("data-docente") == 3
    ) {
      counter++;
    }
  }

  const res = (counter / qntdDisciplinas) * 100 || 0;

  const porcentagemDiv = document.querySelector("#porcentagem-docente");
  porcentagemDiv.innerHTML = `${Number(res).toFixed(1)}%`;

  return res;
}
