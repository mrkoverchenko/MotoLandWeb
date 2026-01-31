

function checkForms(_this) {
    let thisForm = document.getElementById(_this.id);
    let thisFormInputElements = thisForm.getElementsByTagName('INPUT');
    var pw1 = "";
    var pw2 = "";
    var wrongPasswordField1, wrongPasswordField2;

    for (var ic = 0; ic < thisFormInputElements.length; ++ic) {

        if (thisFormInputElements[ic].alt != "noChecking") {

            if (thisFormInputElements[ic].value.length === 0) {
                alert("Ez a mező nem maradhat üresen!\nCsak mondom!");
                thisFormInputElements[ic].focus();
                return false;
            }

            if (thisFormInputElements[ic].type == 'password') {
                if (pw1 == "") {
                    wrongPasswordField1 = thisFormInputElements[ic];
                    pw1 = thisFormInputElements[ic].value;
                } else {
                    wrongPasswordField2 = thisFormInputElements[ic];
                    pw2 = thisFormInputElements[ic].value;
                }
            }
        }
    }
    if (pw1 != pw2) {
        alert("A két jelszó nem egyezik!");
        wrongPasswordField1.value = wrongPasswordField2.value = "";
        wrongPasswordField1.focus();
        return false;
    }
}

function clearForm(formID) {
    let thisForm = document.getElementById(formID);
    let thisFormInputElements = thisForm.getElementsByTagName('INPUT');

    for (var ic = 0; ic < thisFormInputElements.length; ++ic) {
        thisFormInputElements[ic].value = "";
    }
}


function checkUserName(_this) {

    var defaultText = "A *-al jelszett mezők kitöltése kötelező!";

    var param = "username=" + _this.value;
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "checkUserName.php", true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var ret = xhr.responseText;
            if (ret.length > 4) { //{[]}
                _this.focus();
                const mess = document.getElementById("message");
                mess.style.color = "red";
                mess.innerHTML = "Sajnálom, ez a felhasználónév már foglalt!";

                let m = setInterval(clrTimer, 3000);
                function clrTimer() {
                    mess.innerHTML = defaultText;
                    mess.style.color = "black";
                    _this.value = "";
                    clearInterval(m);
                }

            }
        }
    };
    xhr.send(param);
}

function startItem(item) {
    let container = document.getElementById("subcontainer");
    var param = "";
    var req = new XMLHttpRequest();
    req.open("POST", item + ".php", true);
    req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    req.onreadystatechange = function () {
        if (req.readyState === 4 && req.status === 200) {
            container.innerHTML = req.responseText;
        }
    }
    req.send(param);
}




function manSelect(_this) {
    document.getElementById("motopartscategory").innerHTML = "";
    document.getElementById("motoparts").innerHTML = "";

    let container = document.getElementById("mototype");
    container.innerHTML = "";
    var param = "manufacturerID=" + _this.value;
    var req = new XMLHttpRequest();
    req.open("POST", "getMotoType.php", true);
    req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    req.onreadystatechange = function () {
        if (req.readyState === 4 && req.status === 200) {
            container.innerHTML += req.responseText;
        }
    }
    req.send(param);
}

function typeSelect(_this) {
    document.getElementById("motoparts").innerHTML = "";

    let container = document.getElementById("motopartscategory");
    container.innerHTML = "";
    let manID = document.getElementById("motoman").value;
    var param = "typeID=" + _this.value + "&manID=" + manID;
    var req = new XMLHttpRequest();
    req.open("POST", "getMotoPartsCategory.php", true);
    req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    req.onreadystatechange = function () {
        if (req.readyState === 4 && req.status === 200) {
            container.innerHTML += req.responseText;
        }
    }
    req.send(param);
}

function categorySelect(_this) {
    let container = document.getElementById("motoparts");
    container.innerHTML = "";
    let manID = document.getElementById("motoman").value;
    let typeID = document.getElementById("mototype").value;
    var param = "typeID=" + typeID + "&manID=" + manID + "&catID=" + _this.value;
    var req = new XMLHttpRequest();
    req.open("POST", "getMotoParts.php", true);
    req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    req.onreadystatechange = function () {
        if (req.readyState === 4 && req.status === 200) {
            container.innerHTML += req.responseText;
        }
    }
    req.send(param);
}