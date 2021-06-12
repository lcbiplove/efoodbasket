window.addEventListener("load", function(){
    var menu_icon = document.getElementById("menu");
    var overlay = document.getElementById("overlay");
    var menu_container = document.getElementById("menu-container");
    var search_icon = document.getElementById("mbl-search");
    var search_wrapper = document.querySelector(".search-box-wrapper");
    var mbl_search_container = document.getElementById("mbl-search-container");
    var header = document.querySelector("header");

    function resetToDefault(){
        closeMenu();
        hideOverlay();
        closeSearch();
    }
    
    function showOverlay(){
        overlay.classList.add("overlay");
        document.body.style.overflow = "hidden";
    }
    function hideOverlay(){
        overlay.classList.remove("overlay");
        document.body.style = "";
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
});