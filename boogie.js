

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
    let container = document.getElementById("container");
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


