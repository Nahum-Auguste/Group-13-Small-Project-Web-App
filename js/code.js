const urlBase = 'https://lampmekha.xyz/LAMPAPI';
const extension = 'php';

let userId = 0;
let firstName = "";
let lastName = "";

function doLogin() {
	// reset global vars
	userId = 0;
	firstName = "";
	lastName = "";

	// get inputs
	let login = document.getElementById("loginName").value.trim();
	let password = document.getElementById("loginPassword").value.trim();
	//	var hash = md5( password );

	document.getElementById("loginResult").innerHTML = "";

	// ensure all info was inputted
	if (!login || !password) {
		document.getElementById("signupResult").innerHTML = "All fields are required!";
		return;
	}

	// create payload
	let tmp = { login: login, password: password };
	//	var tmp = {login:login,password:hash};
	let jsonPayload = JSON.stringify(tmp);

	let url = urlBase + '/Login.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try {
		xhr.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {
				let jsonObject = JSON.parse(xhr.responseText);
				userId = jsonObject.id;

				// error check
				if (userId < 1) {
					document.getElementById("loginResult").innerHTML = "User/Password combination incorrect";
					return;
				}

				// success!
				firstName = jsonObject.firstName;
				lastName = jsonObject.lastName;

				saveCookie(userId, firstName, lastName, login);

				window.location.href = "dashboard.html";
			}
		};
		xhr.send(jsonPayload);
	}
	catch (err) {
		document.getElementById("loginResult").innerHTML = err.message;
	}

}

function doRegister() {
	// reset global vars
	userId = 0;
	firstName = "";
	lastName = "";

	// get inputs
	let fName = document.getElementById("firstName").value.trim();
	let lName = document.getElementById("lastName").value.trim();
	let login = document.getElementById("signupName").value.trim();
	let password = document.getElementById("signupPassword").value.trim();

	document.getElementById("signupResult").innerHTML = "";

	// ensure all info was inputted
	if (!fName || !lName || !login || !password) {
		document.getElementById("signupResult").innerHTML = "All fields are required!";
		return;
	}

	// create payload
	let tmp = { firstName: fName, lastName: lName, login: login, password: password };
	let jsonPayload = JSON.stringify(tmp);

	let url = urlBase + '/Register.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

	try {
		xhr.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {
				let jsonObject = JSON.parse(xhr.responseText);

				// error check
				if (jsonObject.error && jsonObject.error !== "") {
					document.getElementById("signupResult").innerHTML = jsonObject.error;
					return;
				}

				// success!
				firstName = jsonObject.firstName;
				lastName = jsonObject.lastName;

				saveCookie(0, fName, lName, login);

				window.location.href = "dashboard.html";
			}
		};
		xhr.send(jsonPayload);
	}
	catch (err) {
		document.getElementById("loginResult").innerHTML = err.message;
	}
}

function saveCookie(userId = 0, fName = firstName, lName = lastName, loginName = "") {
	// set cookie to expire in 20 minutes
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime() + (minutes * 60 * 1000));

	// construct cookie string
	let cookieValue = `firstName=${fName},lastName=${lName},userId=${userId},login=${loginName}`;
	document.cookie = cookieValue + ";expires=" + date.toGMTString() + ";path=/";
}

function readCookie() {
	userId = -1;
	firstName = "";
	lastName = "";
	let login = "";

	let data = document.cookie;
	let splits = data.split(",");
	for (let i = 0; i < splits.length; i++) {
		let thisOne = splits[i].trim();
		let tokens = thisOne.split("=");
		if (tokens[0] === "firstName") {
			firstName = tokens[1];
		} else if (tokens[0] === "lastName") {
			lastName = tokens[1];
		} else if (tokens[0] === "userId") {
			userId = parseInt(tokens[1].trim());
		} else if (tokens[0] === "login") {
			login = tokens[1];
		}
	}

	// redirect if not logged in
	if (userId < 0) {
		window.location.href = "index.html";
	} else {
		// document.getElementById("userName").innerHTML = "Logged in as " + firstName + " (" + login + ")";
	}
}


function doLogout() {
	// reset globals
	userId = 0;
	firstName = "";
	lastName = "";
	let login = "";

	// expire the cookie
	document.cookie = "firstName=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
	document.cookie = "lastName=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
	document.cookie = "userId=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
	document.cookie = "login=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";

	window.location.href = "index.html";
}


function addColor() {
	let newColor = document.getElementById("colorText").value;
	document.getElementById("colorAddResult").innerHTML = "";

	let tmp = { color: newColor, userId, userId };
	let jsonPayload = JSON.stringify(tmp);

	let url = urlBase + '/AddColor.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try {
		xhr.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {
				document.getElementById("colorAddResult").innerHTML = "Color has been added";
			}
		};
		xhr.send(jsonPayload);
	}
	catch (err) {
		document.getElementById("colorAddResult").innerHTML = err.message;
	}

}

function searchColor() {
	let srch = document.getElementById("searchText").value;
	document.getElementById("colorSearchResult").innerHTML = "";

	let colorList = "";

	let tmp = { search: srch, userId: userId };
	let jsonPayload = JSON.stringify(tmp);

	let url = urlBase + '/SearchColors.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try {
		xhr.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {
				document.getElementById("colorSearchResult").innerHTML = "Color(s) has been retrieved";
				let jsonObject = JSON.parse(xhr.responseText);

				for (let i = 0; i < jsonObject.results.length; i++) {
					colorList += jsonObject.results[i];
					if (i < jsonObject.results.length - 1) {
						colorList += "<br />\r\n";
					}
				}

				document.getElementsByTagName("p")[0].innerHTML = colorList;
			}
		};
		xhr.send(jsonPayload);
	}
	catch (err) {
		document.getElementById("colorSearchResult").innerHTML = err.message;
	}

}
