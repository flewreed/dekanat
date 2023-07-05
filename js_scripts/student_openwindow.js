var semestr;

document.getElementsByClassName("semesterbtn")[document.getElementById('semesterP').innerHTML - 1].classList.add('active');

function chooseSemester(_semestr) {
	semestr = _semestr;
	var semestr_btn;

	semestr_btn = document.getElementsByClassName("semesterbtn");
	for (i = 0; i < semestr_btn.length; i++) {
		semestr_btn[i].className = semestr_btn[i].className.replace("active", "");
	}
	semestr_btn[semestr-1].classList.add('active');
}

function student_openwindow(evt, windowName) {
	var i, tabcontent, tablinks;

	tabcontent = document.getElementsByClassName("tabcontent");
	for (i = 0; i < tabcontent.length; i++) {
		tabcontent[i].style.display = "none";
	}

	tablinks = document.getElementsByClassName("tablinks");
	for (i = 0; i < tablinks.length; i++) {
		tablinks[i].className = tablinks[i].className.replace(" active", "");
	}

	document.getElementById(windowName).style.display = "block";
	evt.currentTarget.className += " active";
}


