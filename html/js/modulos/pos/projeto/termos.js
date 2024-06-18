function toggleTermos() {
  const termos = document.querySelector("#termos_condicoes");
  const mainForm = document.querySelector("#main-form");

  if (termos.checked) {
    mainForm.style.display = "block";
    localStorage.setItem("termos", JSON.stringify(true))
  } else {
    mainForm.style.display = "none";
  }
}

function checkTermos() {
  const termos = document.querySelector("#termos_condicoes");
  const projetoId = document.querySelector("#main-form #id_projeto");
  const isChecked = JSON.parse(localStorage.getItem("termos"))

  if (projetoId.value != 0) {
    if (termos) termos.checked = true;
    document.querySelector("#main-form").style.display = "block";
    return
  }

  if (isChecked) {
    console.log("aceitou")
    if (termos) termos.checked = true;
    document.querySelector("#main-form").style.display = "block";
    return
  }
}

document
  .querySelector("#termos_condicoes")
  ?.addEventListener("change", (event) => {
    toggleTermos();
    checkTermos();
  });
