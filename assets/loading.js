var opacity = setTimeout(showPage, 1000);

function showPage() {
    document.getElementById("loader").style.opacity = "0";
    setTimeout(showPageSecond, 600);
}

function showPageSecond() {
    document.getElementById("loader").style.display = "none";
}