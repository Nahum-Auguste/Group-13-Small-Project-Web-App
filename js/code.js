const urlBase = 'http://localhost/Group-13-Small-Project-Web-App/LAMPAPI';
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
		document.getElementById("loginResult").innerHTML = "All fields are required!";
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
		document.getElementById("signupResult").innerHTML = err.message;
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
		document.getElementById("first-name").textContent = firstName;
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

function addContact() {
	// get inputs
	let contactFirstName = document.getElementById("contactFirstName").value.trim();
	let contactLastName = document.getElementById("contactLastName").value.trim();
	let contactPhone = document.getElementById("contactPhone").value.trim();
	let contactEmail = document.getElementById("contactEmail").value.trim();

	document.getElementById("addContactResult").innerHTML = "";

	// ensure all info was inputted
	if (!contactFirstName || !contactLastName || !contactPhone || !contactEmail) {
		document.getElementById("addContactResult").innerHTML = "All fields are required!";
		return;
	}

	// create payload
	let tmp = { firstName: contactFirstName, lastName: contactLastName, phone: contactPhone, email: contactEmail, userId: userId };
	let jsonPayload = JSON.stringify(tmp);

	let url = urlBase + '/AddContact.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

	try {
		xhr.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {
				let jsonObject = JSON.parse(xhr.responseText);

				// error check
				if (jsonObject.error && jsonObject.error !== "") {
					document.getElementById("addContactResult").innerHTML = jsonObject.error;
					return;
				}

				// success!
				document.getElementById("addContactResult").innerHTML = "Contact successfully added";

				document.getElementById("contactFirstName").value = "";
				document.getElementById("contactLastName").value = "";
				document.getElementById("contactPhone").value = "";
				document.getElementById("contactEmail").value = "";
				displayContacts();
			}
		};
		xhr.send(jsonPayload);
	}
	catch (err) {
		document.getElementById("addContactResult").innerHTML = err.message;
	}
}

function searchContacts() {

	// clear previous results
	let contactList = "";
	document.getElementById("contactsSearchResult").innerHTML = "";

	let srch = document.getElementById("searchText").value;
	document.getElementById("contactsSearchResult").innerHTML = "";

	// create payload
	let tmp = { search: srch, userId: userId };
	let jsonPayload = JSON.stringify(tmp);

	let url = urlBase + '/SearchContacts.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

	try {
		xhr.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {

				let jsonObject = JSON.parse(xhr.responseText);

				// handle PHP error
				if (jsonObject.error !== "") {
					document.getElementById("contactsSearchResult").innerHTML = jsonObject.error;
					return;
				}

				document.getElementById("contactsSearchResult").innerHTML = "Contact(s) retrieved";

				// format contacts
				for (let i = 0; i < jsonObject.results.length; i++) {
					let contact = jsonObject.results[i];

					contactList +=
						contact.FirstName + " " +
						contact.LastName + " | " +
						contact.Phone + " | " +
						contact.Email + "<br>";
				}

				document.getElementById("contactsSearchResult").innerHTML = contactList;
			}
		};

		xhr.send(jsonPayload);
	}
	catch (err) {
		document.getElementById("contactsSearchResult").innerHTML = err.message;
	}
}

function displayContacts() {
	// prep payload
	let tmp = { userId: userId };
	let jsonPayload = JSON.stringify(tmp);

	let url = urlBase + '/DisplayContacts.' + extension;

	// create request
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

	xhr.onreadystatechange = function () {
		if (this.readyState == 4 && this.status == 200) {
			let contacts;
			try {
				contacts = JSON.parse(xhr.responseText);
			} catch (err) {
				console.error("Failed to parse contacts JSON:", xhr.responseText);
				contacts = [];
			}

			let list = document.getElementById("contactsList");
			list.innerHTML = "";

			if (!contacts || contacts.error) {
				list.innerHTML = "No contacts found.";
				return;
			}

			// add html for each contact
			for (let i = 0; i < contacts.length; i++) {
				let c = contacts[i];

				list.innerHTML += `
                <div class="contact-row">
                    <div class="contact-info">
                        <div class="contact-name">
                            ${c.FirstName} ${c.LastName}
                        </div>
                        <div class="contact-meta">
                            ${c.Phone} • ${c.Email}
                        </div>
                    </div>

                    <div class="contact-actions">
                        <button class="icon-button edit-button">
                            <svg viewBox="0 0 16 16" fill="currentColor">
                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168z"/>
                            </svg>
                        </button>

                        <button class="icon-button delete-button">
                            <svg viewBox="0 0 16 16" fill="currentColor">
                                <path d="M5.5 5.5v7a.5.5 0 0 0 1 0v-7zm3 0v7a.5.5 0 0 0 1 0v-7z"/>
                                <path d="M14.5 3H11V1H5v2H1.5v1H2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4h.5z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                `;
			}
		}
	};

	xhr.send(jsonPayload);
}
