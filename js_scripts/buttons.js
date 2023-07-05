function opendata(evt, windowName) {
    var i, tabc, tabl;

    tabc = document.getElementsByClassName("tabc");
    for (i = 0; i < tabc.length; i++) {
        tabc[i].style.display = "none";
    }

    tabl = document.getElementsByClassName("tabl");
    for (i = 0; i < tabl.length; i++) {
        tabl[i].className = tabl[i].className.replace(" active", "");
    }

    document.getElementById(windowName).style.display = "block";
    evt.currentTarget.className += " active";
}