window.addEventListener("load", function(){
    var queryForm = this.document.getElementById("queries-form");
    var question = this.document.getElementById("question");
    var queriesContainer = this.document.getElementById("queries-container");
    var queryError = this.document.getElementById("query_error");

    var onSuccess = function(response) {
        hideBigLoader();

        var data = JSON.parse(response);
        console.log(data);

        queryError.innerHTML = "";
        if(data.hasOwnProperty("error")){
            queryError.innerHTML = data.error;
        } 
        else if(data.hasOwnProperty("data")) {
            var obj = data.data;
            var addedRow = "<div class='each-query' data-query-id='"+obj.QUERY_ID+"'><div class='query-wrapper'><div class='query-indicator'></div><div><div class='query-text'>"+obj.QUESTION+"</div><div class='querer-detail'><span>by "+obj.QUESTION_BY+"</span><span class='stock-text'>"+obj.AGO_QUESTION+"</span></div></div></div></div>";
            queriesContainer.insertAdjacentHTML("afterbegin", addedRow);
            queryForm.reset();
        }
    }

    queryForm.onsubmit = function(e) {
        e.preventDefault();
        showBigLoader();

        var action = queryForm.getAttribute("action");
        var data = new FormData();
        data.append("question", question.value);
        ajax("POST", action, data, onSuccess);
    }
});