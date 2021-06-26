window.addEventListener("load", function(){
    var queryForm = this.document.getElementById("queries-form");
    var question = this.document.getElementById("question");
    var queriesContainer = this.document.getElementById("queries-container");
    var queryError = this.document.getElementById("query_error");
    
    var answerForm = this.document.getElementById("answer-form");
    var answer = this.document.getElementById("answer");
    var answeringToText =this.document.getElementById("answering-to-text");
    var queryQuesElems = this.document.querySelectorAll(".query-text");
    var answerItElems = this.document.querySelectorAll(".answer-it");

    query_id =  null;
    product_id = null;
    questionText = null;

    var onQuerySuccess = function(response) {
        hideBigLoader();

        var data = JSON.parse(response);

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

    if(queryForm){
        queryForm.onsubmit = function(e) {
            e.preventDefault();
            showBigLoader();
    
            var action = queryForm.getAttribute("action");
            var data = new FormData();
            data.append("question", question.value);
            ajax("POST", action, data, onQuerySuccess);
        }
    }

    if(answerItElems){
        answerItElems.forEach(function(item, ind){
            item.onclick = function(){
                var ques = queryQuesElems[ind].innerHTML;
                ques = ques.substring(0, 50) + (ques.length > 50 ? "..." : "");

                answeringToText.innerHTML = "Answering the query, \""+ques+"\":";
                answerForm.classList.remove("d-none");
                answer.focus();

                query_id = item.getAttribute("data-query-id");
                product_id = item.getAttribute("data-product-id");
                questionText = ques;
            }
        });
    }

    var onAnswerSuccess = function(response) {
        hideBigLoader();

        var data = JSON.parse(response);

        queryError.innerHTML = "";
        if(data.hasOwnProperty("error")){
            queryError.innerHTML = data.error;
        } 
        else if(data.hasOwnProperty("data")) {
            var obj = data.data;
            var answerRow = "<div class='query-wrapper'><div class='query-indicator answer'></div><div><div class='query-text'>"+obj.ANSWER+"</div><div class='querer-detail'><span>by trader</span><span class='stock-text'> "+obj.AGO_ANSWER+"</span></div></div></div>";
            queriesContainer.insertAdjacentHTML("afterbegin", addedRow);
            var eachRow = document.querySelector(".each-row[data-query-id='"+obj.QUERY_ID+"']");
            eachRow.innerHTML += answerRow;
            answerForm.reset();
            answeringToText.innerHTML = "";
            answerForm.classList.add("d-none");
        }
    }

    if(answerForm){
        answerForm.onsubmit = function(e) {
            e.preventDefault();
            showBigLoader();

            var action = "/ajax/products/"+product_id+"/add-answer/"+query_id+"/";
            var data = new FormData();
            data.append("answer", answer.value);
            ajax("POST", action, data, onAnswerSuccess);
        }
    }
});