function trocar_usuario(id_pessoa, pessoa_ativa) {
	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function () {
		if (xhr.readyState == 4 && xhr.status == 200) {
			location.reload();
		}
	};
	xhr.open(
		"GET",
		"ajax/index/trocar_usuario.php?id_pessoa=" +
			id_pessoa +
			"&pessoa_ativa=" +
			pessoa_ativa,
		true
	);
	xhr.send();
}

function logout() {
	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function () {
		if (xhr.readyState == 4 && xhr.status == 200) {
			location.reload();
		}
	};
	xhr.open("GET", "ajax/index/logout.php", true);
	xhr.send();
}
