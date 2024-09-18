function jsonCreate(key, val) {
    var json = "{";
    for (var x = 0; x < key.length; x++) {
        if (x == key.length - 1) {
            json = json + `"${key[x]}":"${val[x]}"`;
        } else {
            json = json + `"${key[x]}":"${val[x]}",`;
        }
    }
    json = json + "}";
    return json;
}

function ajax(url, actionName, jsonData, callback, vchType = null) {
    var xmlhttp = new XMLHttpRequest();
    var msg;

    //console.log(jsonData);

    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            msg = this.responseText;
            //console.log(msg);
            callback(msg);
        }
    };

    xmlhttp.open("POST", url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(
        "jsonData=" +
        jsonData +
        "&action=" +
        actionName +
        "&voucherType=" +
        vchType
    );
}