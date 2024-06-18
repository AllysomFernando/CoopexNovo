(function () {
	$(document).ready(function () {
		carregarAgendaDoDia(obterDataAtual());
	});
})();



function carregarAgendaDoDia(data) {
	$.ajax({
		url: "ajax/agenda/agenda_infantil.php?data=" + data,
		method: "GET",
		success: function (data) {
			$("#agenda_do_dia").html(data);
		},
		error: function (xhr, status, error) {
			console.error("Erro ao carregar a agenda do dia:", status, error);
		},
	});
}



// Obter elemento do calendário
var calDates = document.querySelector(".cal-dates");

// Variáveis para rastrear o mês e o ano atual
var currentMonth = new Date().getMonth();
var currentYear = new Date().getFullYear();

// Função para criar as datas do calendário
function createCalendar(year, month) {
	// Limpa as datas existentes
	calDates.innerHTML = "";

	// Cria um objeto Date para o primeiro dia do mês e do ano fornecidos
	var firstDayOfMonth = new Date(year, month, 1);

	// Obtém o dia da semana do primeiro dia do mês (0 a 6, onde 0 é domingo)
	var startingDay = firstDayOfMonth.getDay();

	// Obtém o número de dias no mês
	var daysInMonth = new Date(year, month + 1, 0).getDate();

	// Atualiza o título do calendário com o nome do mês e o ano
	var monthName = getMonthName(month);
	document.querySelector(".cal-title").textContent = monthName + " " + year;

	// Cria elementos de âncora para os dias do mês anterior
	var lastMonthDays = new Date(year, month, 0).getDate();
	for (var i = startingDay - 1; i >= 0; i--) {
		var date = document.createElement("a");
		date.href = "#";
		date.textContent = lastMonthDays - i;
		date.classList.add("cal-disabled", "opacity-50");
		date.addEventListener("click", function (e) {
			e.preventDefault(); // Previne o comportamento padrão do link
			var clickedDate =
				year + "-" + pad(month, 2) + "-" + pad(this.textContent, 2);
			var formattedDate =
				getMonthName(month - 1) + " " + this.textContent + ", " + year;
			carregarAgendaDoDia(clickedDate);
		});
		calDates.appendChild(date);
	}

	// Cria elementos de âncora para cada dia do mês atual
	for (var i = 1; i <= daysInMonth; i++) {
		var date = document.createElement("a");
		date.href = "#";
		date.textContent = i;
		// Verifica se é o dia atual
		if (
			i === new Date().getDate() &&
			month === new Date().getMonth() &&
			year === new Date().getFullYear()
		) {
			date.classList.add("cal-selected", "bg-blue-dark");
			var span = document.createElement("span");
			date.appendChild(span);
		} else {
			date.addEventListener("click", function (e) {
				e.preventDefault(); // Previne o comportamento padrão do link
				var clickedDate =
					year + "-" + pad(month + 1, 2) + "-" + pad(this.textContent, 2);
				var formattedDate =
					getMonthName(month) + " " + this.textContent + ", " + year;
				carregarAgendaDoDia(clickedDate);
			});
		}
		calDates.appendChild(date);
	}

	// Cria elementos de âncora para os dias do próximo mês
	var nextMonthDays = 42 - (startingDay + daysInMonth); // 42 é o número total de células em um calendário de 7 dias e 6 semanas
	for (var i = 1; i <= nextMonthDays; i++) {
		var date = document.createElement("a");
		date.href = "#";
		date.textContent = i;
		date.classList.add("cal-disabled", "opacity-50");
		date.addEventListener("click", function (e) {
			e.preventDefault(); // Previne o comportamento padrão do link
			var clickedDate =
				year + "-" + pad(month + 2, 2) + "-" + pad(this.textContent, 2);
			var formattedDate =
				getMonthName(month + 1) + " " + this.textContent + ", " + year;
			carregarAgendaDoDia(clickedDate);
		});
		calDates.appendChild(date);
	}
}

// Função para adicionar zeros à esquerda para preencher um número
function pad(num, size) {
	var s = num + "";
	while (s.length < size) s = "0" + s;
	return s;
}

// Função para obter o nome do mês
function getMonthName(month) {
	var months = [
		"Janeiro",
		"Fevereiro",
		"Março",
		"Abril",
		"Maio",
		"Junho",
		"Julho",
		"Agosto",
		"Setembro",
		"Outubro",
		"Novembro",
		"Dezembro",
	];
	return months[month];
}

// Chama a função para criar o calendário inicialmente
createCalendar(currentYear, currentMonth);

// Evento para navegar para o mês anterior
document
	.querySelector(".cal-title-left")
	.addEventListener("click", function () {
		currentMonth--; // Reduz o mês atual
		if (currentMonth < 0) {
			currentMonth = 11; // Volta para dezembro
			currentYear--; // Reduz o ano
		}
		createCalendar(currentYear, currentMonth);
	});

// Evento para navegar para o próximo mês
document
	.querySelector(".cal-title-right")
	.addEventListener("click", function () {
		currentMonth++; // Aumenta o mês atual
		if (currentMonth > 11) {
			currentMonth = 0; // Volta para janeiro
			currentYear++; // Aumenta o ano
		}
		createCalendar(currentYear, currentMonth);
	});

// Evento para clicar na data atual
document.querySelector(".cal-selected").addEventListener("click", function (e) {
	e.preventDefault(); // Previne o comportamento padrão do link
	var clickedDate =
		currentYear +
		"-" +
		pad(currentMonth + 1, 2) +
		"-" +
		pad(new Date().getDate(), 2);
	var formattedDate =
		getMonthName(currentMonth) +
		" " +
		new Date().getDate() +
		", " +
		currentYear;
	carregarAgendaDoDia(clickedDate);
});
