function resetForm(formElement) {
    var frm_elements = formElement.elements;
    
    for (i = 0; i < frm_elements.length; i++)
    {
        field_type = frm_elements[i].type.toLowerCase();
        switch (field_type)
        {
        case "text":
        case "password":
        case "textarea":
        case "hidden":
            frm_elements[i].value = "";
            break;
        case "radio":
        case "checkbox":
            if (frm_elements[i].checked)
            {
                frm_elements[i].checked = false;
            }
            break;
        case "select-one":
        case "select-multi":
            frm_elements[i].selectedIndex = -1;
            break;
        default:
            break;
        }
    }
}

window.addEventListener("load", function () {
    var filterForm = document.getElementById("filter-form");

    var filterBtn = document.querySelector(".filter-btn");
    var resetBtn = document.querySelector(".filter-reset-btn");

    var minPrice = document.getElementById("minPrice");
    var maxPrice = document.getElementById("maxPrice");

    var element = document.getElementById('myRangeSlider');
    var options = {
        isDate: false,
        min: 0,
        max: 5000,
        step: 50,
        start: minPrice.value || 0,
        end: maxPrice.value || 5000,
        overlap: false
    };

    var mySlider = new Slider(element, options);

    mySlider.subscribe('moving', function(data) {
        minPrice.value = data.left.toFixed(0);
        maxPrice.value = data.right.toFixed(0);
    });

    filterBtn.onclick = function () {
        filterForm.submit();
    }
    
    
    resetBtn.onclick = function () {
        mySlider.move({left: 0.2, right: 5000});

        resetForm(filterForm);
    }
})