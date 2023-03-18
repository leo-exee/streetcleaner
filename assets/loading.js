window.onload = function(e) {
    document.getElementById("loader").style.opacity = "0";
    setTimeout(showPageSecond, 600);
}
function showPageSecond() {
    document.getElementById("loader").style.display = "none";
}