
const defaultFieldMesage = "A *-al jelszett mezők kitöltése kötelező!";
const defaultPath = "http://localhost/mrkoverchenko/MotoLandWeb/";

function checkForms(_this) {
    let thisForm = document.getElementById(_this.id);
    let thisFormInputElements = thisForm.getElementsByTagName('INPUT');
    var pw1 = "";
    var pw2 = "";
    var wrongPasswordField1, wrongPasswordField2;

    for (var ic = 0; ic < thisFormInputElements.length; ++ic) {

        if (thisFormInputElements[ic].alt != "noChecking") {

            if (thisFormInputElements[ic].value.length === 0) {
                alert("Ez a mező nem maradhat üresen!\n"+thisFormInputElements[ic]+"\nCsak mondom!");
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





let magniBig = false;
function changeSize(clear) {
    let explodedViewIMG = document.getElementById("explodedViewIMG");
    if (clear)
        explodedViewIMG.src = "";
    else {
        let magni = document.getElementById("magni");
        if (!magniBig) {
            magni.src = defaultPath + "imgs/magni-.png";
            explodedViewIMG.style.width = "100%";
            explodedViewIMG.style.height = "auto";
            magni.title = "Alaphelyzet";
        } else {
            magni.src = defaultPath + "imgs/magni+.png";
            explodedViewIMG.style.width = "100px";
            explodedViewIMG.style.height = "auto";
            magni.title = "Nagyítás";
        }
        magniBig = !magniBig;
    }

}


let man = false;
let type = false;
let cat = false;
let part = false;

function clearMTCP() {
    man = false;
    type = false;
    cat = false;
    part = false;
}

function manSelect(_this) {

    document.getElementById("motopartscategory").innerHTML = "";
    document.getElementById("motoparts").innerHTML = "";
    clearDetailFields();
    changeSize(true);

    clearMTCP;
    if (_this.selectedIndex > 0) man = true; else man = type = cat = part = false; 
    setSubmitButton();


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
    clearDetailFields();
    changeSize(true);

    if (_this.selectedIndex > 0) type = true; else type = cat = part = false; 
    setSubmitButton();

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
    clearDetailFields();
    changeSize(true);

    if (_this.selectedIndex > 0) cat = true; else cat = part = false; 
    setSubmitButton();

    let manID = document.getElementById("motoman").value;
    let typeID = document.getElementById("mototype").value;
    var param = "typeID=" + typeID + "&manID=" + manID + "&catID=" + _this.value;

    let explodedViewIMG = document.getElementById("explodedViewIMG");
    var expIMG = _this.options[_this.selectedIndex].title;
    explodedViewIMG.src = (expIMG.length > 0)
        ? expIMG
        : "http://localhost/mrkoverchenko/MotoLandWeb/imgs/nopic.png";

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

function partSelect(_this) {

    let manID = document.getElementById("motoman").value;
    let typeID = document.getElementById("mototype").value;
    let catID = document.getElementById("motopartscategory").value;
    clearDetailFields();

    if (_this.selectedIndex > 0) part = true; else part = false; 
    setSubmitButton();

    var param = "typeID=" + typeID + "&manID=" + manID + "&catID=" + catID + "&partID=" + _this.value;
    var req = new XMLHttpRequest();
    req.open("POST", "getMotoPartsDetail.php", true);
    req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    req.onreadystatechange = function () {
        if (req.readyState === 4 && req.status === 200) {
            const myObj = JSON.parse(this.responseText);
            //console.log(myObj);

            var netto = myObj[0].MotoPartsNettoPrice_MSTR;
            document.getElementById("motopartnettoprice").value = Math.round(netto);

            var vat = myObj[0].MotoPartsVAT_MSTR;
            document.getElementById("motopartvat").value = vat * 100;

            let bruttoEUR = myObj[0].MotoPartsBruttoEURPrice_MSTR;    
            document.getElementById("motopartbruttoeurprice").value = Math.round(bruttoEUR * 100) / 100

            var disc = myObj[0].MotoPartsDiscount_MSTR;
            document.getElementById("motopartdiscount").value = disc * 100;

            var q = Math.round(myObj[0].MotoPartsQuantity_MSTR);

            let mee = myObj[0].QuantityUnitUnit_MSTR;
            document.getElementById("motopartquantityunit").value = mee;
            document.getElementById("meeDiv").innerHTML = mee; 
            document.getElementById("mee").innerHTML = "Raktári mennyiség: " + q + " " + mee;

            document.getElementById("motopartquantity").value = q;

            let selectedQuantity = document.getElementById("quantity")
            selectedQuantity.max = q;

            document.getElementById("motopartinfo").value = myObj[0].MotoPartsInfo_MSTR;

            document.getElementById("totalcost").value = ((Number(netto) * Number(vat)) + Number(netto)) * selectedQuantity.value;
        }
    }
    req.send(param);
}

function setCost(_this) {
    var netto = document.getElementById("motopartnettoprice").value;
    var vat = document.getElementById("motopartvat").value / 100;
    document.getElementById("totalcost").value = ((Number(netto) * Number(vat)) + Number(netto)) * _this.value;
}

function clearSC() {
    location.href = defaultPath + "index.php?shoppingcart=cleared&page=sales";
}

function disText(event) {
    if (event.keyCode != 40 && event.keyCode != 38)
        event.preventDefault();
}

function setSubmitButton() {
    let qua = document.getElementById("quantity");
    let iSC = document.getElementById("intoShoppingCart");
    let tcost = document.getElementById("totalcost");
    //alert("man: " + man + "\ntype: " + type + "\ncat: " + cat + "\npart: " + part);
    if (man && type && cat && part) {
        qua.disabled = iSC.disabled = tcost.disabled = false;
    } else {
        qua.disabled = iSC.disabled = tcost.disabled = true;
    }
}

/**********************************************************
 * ORDERING.PHP
 * CLEARING ALL DATABASE FIELD BEFORE SELECT NEXT PART
 */
function clearDetailFields() {
    let parentDiv = document.getElementById("details");
    let thisInputElements = parentDiv.getElementsByTagName('INPUT');

    for (var ic = 0; ic < thisInputElements.length; ++ic) {
        thisInputElements[ic].value = "";
    }
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
    let param = "";
    if (fieldID === "regCountryID" || fieldID === "profileCountryID") {
        fileName = "getCountry.php";
        param = "countryID=";
    }

    if (fieldID == "systemPath") {
        fileName = "getSystem.php";
        param = "field=7";
    }

    if (fieldID == "cartBody") {
        fileName = "getShoppingCart.php";
        param = "field=0";
    }

    var req = new XMLHttpRequest();
    req.open("POST", fileName, true);
    req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    req.onreadystatechange = function () {
        if (req.readyState === 4 && req.status === 200) {
            if (container.type === "text")
                container.value = req.responseText;
            else 
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

var arrow = false;
function setArrow(_this) {
    let defaultText = _this.innerHTML.split(" ")[0];
    if (arrow)
        _this.innerHTML = defaultText + " &#11167;";
    else
        _this.innerHTML = defaultText + " &#11165;";
    arrow = !arrow;
}