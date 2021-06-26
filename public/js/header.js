var menu_icon = document.getElementById("menu");
var overlay = document.getElementById("overlay");
var menu_container = document.getElementById("menu-container");
var search_icon = document.getElementById("mbl-search");
var search_wrapper = document.querySelector(".search-box-wrapper");
var mbl_search_container = document.getElementById("mbl-search-container");
var header = document.querySelector("header");

var cookie_mssg_box = document.getElementById("cookie-mssg-box") || 0;
var cookie_close = document.getElementById("cookie-box-close") || 0;

function resetToDefault(){
    closeMenu();
    hideOverlay();
    closeSearch();
}
function showOverlay(onclickable=false){
    overlay.classList.add("overlay");
    if(onclickable){
        overlay.classList.add("unclickable");
    }
    document.body.style.overflow = "hidden";
}
function hideOverlay(onclickable=false){
    setTimeout(function(){
        overlay.classList.remove("overlay");
        if(onclickable){
            overlay.classList.remove("unclickable");
        }
        document.body.style = "";
    }, 50);
}
function showBigLoader() {
    showOverlay(true);
    overlay.innerHTML = "<div class='loader'></div>";
}
function hideBigLoader() {
    overlay.innerHTML = ""
    hideOverlay(true);
}
function openMenu(){
    menu_container.classList.add("open");
    menu_icon.classList.add("open");
}
function closeMenu() {
    menu_container.classList.remove("open");
    menu_icon.classList.remove("open");
}
function closeSearch(){
    mbl_search_container.style.height = "0px";
    mbl_search_container.innerHTML = "";
}
function showMessageBox(){
    delay = 12000;
    
    cookie_mssg_box.classList.add("active");
    if(cookie_mssg_box.classList.contains("show-for-info")){
        delay = 25000;
    }

    setTimeout(function(){
        hideMessageBox();
    }, delay);
}
function hideMessageBox(){
    cookie_mssg_box.classList.remove("active");
}
function ajax(method, action, data, callback) {
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        callback(this.responseText);
    }
    xhttp.open(method, action);
    xhttp.send(data);
}
menu_icon.onclick = function(){
    if(this.classList.contains("open")){
        closeMenu();
        hideOverlay();
    } else {
        showOverlay();
        openMenu();
    }
}
overlay.onclick = function(){
    resetToDefault();
}
search_icon.onclick = function(){
    mbl_search_container.innerHTML = search_wrapper.innerHTML;
    mbl_search_container.style.height = header.clientHeight +"px";
    document.querySelectorAll(".search-inpt")[1].focus();
    showOverlay();
}

cookie_close.onclick = function(){
    hideMessageBox();
}

if(cookie_close){
    showMessageBox();
}