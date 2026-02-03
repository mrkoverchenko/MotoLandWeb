
const defaultFieldMesage = "A *-al jelszett mezők kitöltése kötelező!";

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

    var param = "regUserName=" + _this.value;
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "checkUserName.php", true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var ret = xhr.responseText;
            if (ret.length > 4) { //{[]}
                _this.focus();
                const mess = document.getElementById("regMessage");
                mess.style.color = "red";
                mess.innerHTML = "Sajnálom, ez a felhasználónév már foglalt!";

                let m = setInterval(clrTimer, 3000);
                function clrTimer() {
                    mess.innerHTML = defaultFieldMesage;
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


function onlyNumber(event) {
    var chr = (event.which)? event.which: event.keyCode;
    if (chr >= 48 && chr <= 57) //numbers
        return true;
    return false;
}  

function checkPW() {
    let pws = document.getElementById("regPassword");
    if (pws.value.length > 0 && !passwordIsOk) {
        //alert("A jelszó nem elég erős!\nMinimum 8 karater, kisbetű, nagybetű, szám formátumban!\nCsak mondom!");

        const mess = document.getElementById("regMessage");
        mess.style.color = "red";
        mess.innerHTML = "A jelszó nem elég erős!\nMinimum 8 karater, kisbetű, nagybetű, szám formátumban!";

        let m1 = setInterval(clrTimer, 3000);
        function clrTimer() {
            mess.innerHTML = defaultFieldMesage;
            mess.style.color = "black";
            clearInterval(m1);
        }

        pws.focus();
    }
}

let passwordIsOk;
function chkp() {
    passwordIsOk = false;
    var validCount = 0;  
    let pws = document.getElementById("regPassword");
    let valid = document.getElementById("passwordValidator");
    var lower = /[a-z]/g;
    if (pws.value.match(lower)) {  
        validCount = validCount + 1;
    }
    
    var upper = /[A-Z]/g;
    if (pws.value.match(upper)) {  
        validCount = validCount + 1;
    }
    
    var numbers = /[0-9]/g;
    if (pws.value.match(numbers)) {  
        validCount = validCount + 1;
    }
    
    if (pws.value.length >= 8) {
        validCount = validCount + 1;
    }

    switch (validCount) {
        case 0: valid.style.backgroundColor = "red";
        break;
        case 1: valid.style.backgroundColor = "orange";
        break;
        case 2: valid.style.backgroundColor = "yellow";
        break;
        case 3: valid.style.backgroundColor = "lightgreen";
        break;
        case 4: valid.style.backgroundColor = "green";
                passwordIsOk = true;
        break;
    }
    if (validCount == 4) {
        valid.style.paddingLeft = "2px";
        valid.innerHTML = '\&#128077';
    } else 
        valid.innerHTML = '';
}

function onlyPhone(event) {
    var chr = (event.which)? event.which: event.keyCode;
    if (chr >= 48 && chr <= 57 || chr == 43 || chr == 45 || chr == 32 || chr == 44) //numbers, +, space, comma
        return true;
    return false;
}

function initFields(fieldID) {

    let container = document.getElementById(fieldID);
    let fileName = "";
    var param = "";
    if (fieldID === "regCountryID" || fieldID === "profileCountryID") {
        fileName = "getCountry.php";
        param = "countryID=";
    }

    var req = new XMLHttpRequest();
    req.open("POST", fileName, true);
    req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    req.onreadystatechange = function () {
        if (req.readyState === 4 && req.status === 200) {
            container.innerHTML = req.responseText;
        }
    }
    req.send(param);
}

function initProfileEditor(userID) {

    var param = "userID=" + userID;

    var req = new XMLHttpRequest();
    req.open("POST", "getUserProfile.php", true);
    req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    req.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {

            const myObj = JSON.parse(this.responseText);
            console.log(myObj);

            document.getElementById("profileUserName").value = myObj[0].UserNickName_MSTR;
            document.getElementById("profileEmail").value = myObj[0].UserMail_MSTR;

            document.getElementById("profileFirstName").value = myObj[0].UserFirstName_DET;
            document.getElementById("profileMiddleName").value = myObj[0].UserMiddleName_DET;
            document.getElementById("profileLastName").value = myObj[0].UserLastName_DET;

            document.getElementById("profileCountryID").value = myObj[0].UserCountryID_DET;
            document.getElementById("profilePostCode").value = myObj[0].UserPostCode_DET;
            document.getElementById("profileCity").value = myObj[0].UserCity_DET;
            document.getElementById("profileStreet").value = myObj[0].UserStreet_DET;
            document.getElementById("profileAddress").value = myObj[0].UserAddress_DET;

            document.getElementById("profilePhone").value = myObj[0].UserPhone_DET;



        }
    }
    req.send(param);
    


}