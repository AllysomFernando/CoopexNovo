function toggleCampus() {
  const campus = document.querySelector("#campus_id_campus");
  const instituicao = document.querySelector("#proponente_instituicao");
  const coordenacao = document.querySelector("#proponente_coordenacao");
  const local = document.querySelector("#realizacao_local"); 

  if (campus.value == 1) {
    instituicao.value = "Centro Universitário Fundação Assis Gurgacz - FAG";
    coordenacao.value = "Coordenação de Pós-Graduação da FAG - CPG";
    local.value = "Centro Universitário Fundação Assis Gurgacz - FAG";
  } else {
    instituicao.value = "Faculdade Assis Gurgacz Toledo - FAG Toledo";
    coordenacao.value = "Coordenação de Pós-Graduação da FAG - CPG";
    local.value = "Faculdade Assis Gurgacz Toledo - FAG Toledo";
  }
}

document
  .querySelector("#campus_id_campus")
  .addEventListener("change", (event) => {
    toggleCampus();
  });
