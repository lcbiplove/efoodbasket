window.addEventListener("load", function () {
    var mainCartCountNavElem = document.getElementById("main-cart-count");

    totalItems = 0;
    cartLS.list().forEach(function(item){
        totalItems += item.quantity;
    });

    mainCartCountNavElem.innerHTML = totalItems; 
});
