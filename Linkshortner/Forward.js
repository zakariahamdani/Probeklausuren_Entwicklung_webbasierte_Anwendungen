let request = new XMLHttpRequest();

function requestData() { // fordert die Daten asynchron an
    "use strict";
    //ToDo - vervollständigen **************
    
    let str  = document.getElementById("inputLink").value;
    request.open("GET", "CalculateHash.php?inputLink="+str, true);
    request.onreadystatechange = processData;
    request.send(null);
}

function showHint(str){
    if(str.lenght == 0){
        //document.getElementById("txtHash").innerHTML = "";
        return;
    }else{
        
        request.onreadystatechange = function(){
            if(this.readyState == 4 && this.status == 200){
                document.getElementById("linkHash").innerHTML = this.responseText;
            }
        }
        request.open("GET", "CalculateHash.php?inputLink="+str, true);
        request.send();
    }
}

function processData() {
    "use strict";
    if (request.readyState === 4) { // Uebertragung = DONE
        if (request.status === 200) { // HTTP-Status = OK
            if (request.responseText != null){
                updateView(request.responseText);
            }
                //ToDo - vervollständigen ************
            else console.error("Dokument ist leer");
        } else console.error("Uebertragung fehlgeschlagen");
    } // else; // Uebertragung laeuft noch
}

function updateView(data) {
    "use strict";
    console.log(data);

    let dataObject = JSON.parse(data)[0];
    let hash = document.getElementById("linkHash");
    console.log(dataObject);
    hash.innerHTML = "Hash: " + dataObject.hash;
}