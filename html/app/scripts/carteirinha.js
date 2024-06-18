document.addEventListener("DOMContentLoaded", () => {
	var get_qr_url = document.getElementById("ra").value;
	var qr_api_address =
		"https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=";
	var qr_img =
		'<img class="mx-auto polaroid-effect shadow-l mt-4 delete-qr" width="120" src="' +
		qr_api_address +
		get_qr_url +
		'" alt="img"><p class="font-12 text-center mb-0">RA: ' +
		get_qr_url +
		"</p>";
	document.getElementsByClassName("generate-qr-result")[0].innerHTML = qr_img;
});