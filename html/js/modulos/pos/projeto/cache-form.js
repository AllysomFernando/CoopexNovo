function getFormEntries(formId) {
  const formHtmlElement = document.querySelector(formId);
  const formData = new FormData(formHtmlElement);
  const arr = [];

  for (const entry of formData.entries()) {
    arr.push(entry);
  }

  return arr;
}

async function getCacheFromDatabase() {
  const apiURL = "modulos/pos/projeto/api/routes/form/cache-form.php";
  const pessoaId = document.querySelector("#id_pessoa");

  const urlParams = new URLSearchParams({
    id_pessoa: pessoaId.value,
  });

  const apiData = await fetch(apiURL + "?" + urlParams, {
    method: "GET",
  });

  const response = await apiData.json();

  if (response.entries) {
    return JSON.parse(response.entries);
  }

  return [];
}

async function loadFormFromCache() {
  const projetoId = document.querySelector("#id_projeto");

  const formFromDatabase = await getCacheFromDatabase();
  const formFromCache = JSON.parse(localStorage.getItem("cache-form"));

  if (
    projetoId.value == 0 ||
    projetoId.value == null ||
    projetoId.value == ""
  ) {
    let formContent = formFromCache;
    const listHandlerDisciplinaConfig = {
      htmlListElementId: "lista_disciplinas",
      htmlLogElementId: "disciplinas_log",
      deleteIdentifierClassName: "removerDoCurso",
    };

    const listHandlerDisciplina = new ListHandler(listHandlerDisciplinaConfig);

    if (!formFromCache || formFromCache.length == 0) {
      formContent = formFromDatabase;
    }

    if (formContent != null) {
      for (const entry of formContent) {
        if (entry[0] == "disciplinas") {
          const disciplinas = JSON.parse(entry[1]);

          for (const disciplina of disciplinas) {
            const logObject = {
              id: disciplina.id,
              carga_horaria: disciplina.carga_horaria,
              nome: disciplina.nome,
              id_titulacao: disciplina.id_titulacao,
              acao: disciplina.acao,
            };

            if (disciplina.acao != "DELETE") {
              const listItemData = {
                badge: logObject.carga_horaria + "h",
                inputValue: logObject.id,
                displayName: logObject.nome,
                attributes: [
                  ["id", logObject.id],
                  ["id_titulacao", logObject.id_titulacao],
                  ["disciplina", logObject.id],
                ],
              };

              let itemList = listHandlerDisciplina.createItem(listItemData);
              if (itemList) {
                listHandlerDisciplina.appendItem(itemList);
              }
            }

            listHandlerDisciplina.appendToLog(logObject);
          }
        }

        if (entry[0] == "curso_valor_customizado" && entry[1] == "true") {
          const input = document.querySelector(`[name="${entry[0]}"]`);
          input.value = true;

          const checkbox = document.querySelector("#select_valor_diferente");
          checkbox.setAttribute("checked", "checked");
        }

        const element = document.querySelector(`[name="${entry[0]}"]`);
        element.value = entry[1];
      }
      Swal.fire({
        type: "success",
        title: "Formulário restaurado com sucesso",
        showConfirmButton: false,
        timer: 1500,
      });
    }
  }
}

async function saveFormToCache() {
  const projetoId = document.querySelector("#id_projeto");

  const arr = getFormEntries("#main-form");

  if (
    projetoId.value == 0 ||
    projetoId.value == null ||
    projetoId.value == ""
  ) {
    localStorage.setItem("cache-form", JSON.stringify(arr));
    await Swal.fire({
      title: "Atenção",
      text: "Não é possível salvar o cadastro de docente para depois",
      type: "warning",
      confirmButtonText: "Entendi",
    });

    await Swal.fire({
      type: "success",
      title: "Formulário salvado no navegador com sucesso",
      showConfirmButton: false,
      timer: 1500,
    });
  }
}

async function saveFormToDatabase() {
  const arr = getFormEntries("#main-form");
  const requestBody = {};

  requestBody.id_pessoa = document.querySelector("#id_pessoa").value;
  requestBody.entries = arr;

  $.ajax({
    type: "POST",
    url: "modulos/pos/projeto/api/routes/form/cache-form.php",
    data: JSON.stringify(requestBody),
    dataType: "json",
    success: function (data) {
      Swal.fire({
        type: "success",
        title: "Formulário salvo no banco de dados",
        showConfirmButton: false,
        timer: 1500,
      });
    },
    error: function (xhr) {
      console.log(xhr.responseJSON);
    },
  });
}

function deleteFormFromCache() {
  localStorage.removeItem("cache-form");
}

async function deleteFormFromDatabase() {
  const arr = getFormEntries("#main-form");
  const requestBody = {};

  requestBody.id_pessoa = document.querySelector("#id_pessoa").value;
  requestBody.entries = arr;

  $.ajax({
    type: "DELETE",
    url: "modulos/pos/projeto/api/routes/form/cache-form.php?action=delete",
    data: JSON.stringify(requestBody),
    dataType: "json",
    success: function (data) {
      Swal.fire({
        type: "success",
        title: "Formulário salvo com sucesso",
        showConfirmButton: false,
        timer: 1500,
      });
    },
    error: function (xhr) {
      console.log(xhr.responseJSON);
    },
  });
}

class CacheForm {
 htmlFormId
 htmlRegisterId

 constructor(cacheFormConfig) {
  this.htmlFormId = cacheFormConfig.htmlFormId
  this.htmlRegisterId = cacheFormConfig.htmlRegisterId
 }

}

document
  .querySelector("#save-local-storage")
  ?.addEventListener("click", async () => {
    await saveFormToCache();
    await saveFormToDatabase();
  });
