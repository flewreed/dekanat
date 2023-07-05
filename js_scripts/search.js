function searchteacher() {
    var input, filter, table, tr, h3, i, txtValue;
    input = document.getElementById("searchinput");
    filter = input.value.toUpperCase();
    table = document.getElementById("teacherslist");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        h3 = tr[i].getElementsByTagName("h3")[0];
        if (h3) {
            txtValue = h3.textContent || h3.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

function searchdiscipline() {
    var input, filter, table, tr, h4, i, txtValue;
    input = document.getElementById("searchinput2");
    filter = input.value.toUpperCase();
    table = document.getElementById("disciplineslist");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        h4 = tr[i].getElementsByTagName("h4")[0];
        if (h4) {
            txtValue = h4.textContent || h4.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

function searchdepartment() {
    var input, filter, table, tr, h4, i, txtValue;
    input = document.getElementById("searchinput1");
    filter = input.value.toUpperCase();
    table = document.getElementById("departmentslist");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        h4 = tr[i].getElementsByTagName("h4")[0];
        if (h4) {
            txtValue = h4.textContent || h4.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

function searchstudent() {
    var input, filter, table, tr, h4, i, txtValue;
    input = document.getElementById("searchinput3");
    filter = input.value.toUpperCase();
    table = document.getElementById("studenslist");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        h3 = tr[i].getElementsByTagName("h3")[0];
        if (h3) {
            txtValue = h3.textContent || h3.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}