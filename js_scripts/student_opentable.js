// скрываем таблицы
var disciplinescore = document.getElementsByClassName("disciplinescore");
for (i = 0; i < disciplinescore.length; i++) {
	disciplinescore[i].style.display = "none";
}

function openTable(evt, tableName) {
    var i, openscorebtn;

    openscorebtn = document.getElementsByClassName("openscorebtn");
    for (i = 0; i < openscorebtn.length; i++) {
        openscorebtn[i].className = openscorebtn[i].className.replace(" active", "");
    }

    var table = document.getElementById(tableName);

    if (table) {
        if (table.style.display == "none") {
			table.style.display = "inline-table";
        } else {
			table.style.display = "none";
        }
    }
}