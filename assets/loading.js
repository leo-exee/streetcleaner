window.onload = function(e) {
    document.getElementById("loader").style.opacity = "0";
    setTimeout(showPageSecond, 600);
}
function showPageSecond() {
    document.getElementById("loader").remove();
}

function confirmation(id) {
    document.getElementById("modal").style.display = "flex";
    document.getElementById("no").onclick = function(){
        return false;
    };
    document.getElementById("yes").onclick = function(){
        return true;
    };
}