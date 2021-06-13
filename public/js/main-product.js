window.addEventListener("load", function(){
    imageZoom("preview-img", "zoom-img-result"); 
    
    var MAX_QUANTITY_VALUE = 20;
    
    var previewImage = document.getElementById("preview-img");
    var productIndicators = document.querySelectorAll(".product-img-indicator");

    var buyNowBtn = document.getElementById("buy-now");
    var addToCartBtn = document.getElementById("add-to-cart");

    var addQuantityBtn = document.getElementById("add-quantity");
    var subtractQuantityBtn = document.getElementById("subtract-quantity");
    var quantityValueDiv = document.getElementById("quantity-value");

    var quantityValue = +quantityValueDiv.innerHTML || 0;

    var totalRatingCount = document.querySelector(".rating-summary-wrapper").getAttribute("data-rating-count");

    var checkDisablePlusMinus = function() {
        if(quantityValue <= 0){
            subtractQuantityBtn.classList.add("disabled");

        } else {
            subtractQuantityBtn.classList.remove("disabled");
        }

        if(quantityValue >= MAX_QUANTITY_VALUE){
            addQuantityBtn.classList.add("disabled");
        } else {
            addQuantityBtn.classList.remove("disabled");
        }
    }

    checkDisablePlusMinus();

    addQuantityBtn.onclick = function(){
        quantityValue += 1;
        quantityValueDiv.innerHTML = quantityValue;

        checkDisablePlusMinus();
    }
    subtractQuantityBtn.onclick = function(){
        quantityValue -= 1;
        quantityValueDiv.innerHTML = quantityValue;

        checkDisablePlusMinus();
    }

    productIndicators.forEach(function(item){
        item.onclick = function(){
            var shopByContainer = document.querySelector(".shop-by-images-container");

            productIndicators.forEach(function(item){
                item.classList.remove("active");
            });

            this.classList.add("active");

            shopByContainer.scrollTo({
                behavior: 'smooth',
                left: this.offsetLeft
            });
            previewImage.setAttribute("src", this.getAttribute("src"));
            imageZoom("preview-img", "zoom-img-result");
        }
    });

});

