$(function() {
    $("#startDate, #endDate").datepicker({
        dateFormat: 'yy-mm-dd',
        maxDate: new Date(),
        onSelect: function(dateText, inst) {
          if (inst.id === 'startDate') {
            var selectedDate = new Date(dateText);
            $("#endDate").datepicker("option", "minDate", selectedDate);
          }
          if (inst.id === 'endDate') {
            var selectedDate = new Date(dateText);
            $("#startDate").datepicker("option", "maxDate", selectedDate);
          }
        }
      });
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


function submitForm(){
   let validFlag =  validateForm();
   if(validFlag){
    $(".error").text('');
    $('#loader').css('display', 'flex');
    $.ajax({
        url: '/getStockData',
        type: 'POST',
        data: $("#companyForm").serialize(),
        success: function(response) {
            $("#successMsg,#errorMsg").addClass('d-none');
            $('#loader').css('display', 'none');
            if (response.errors) {
                $.each(response.errors, function(key, value) {
                    $("#" + key + "_error").text(value[0]);
                });
            } else if(response.status){
                $("#successMsg").text(response.message);
                $("#successMsg").removeClass('d-none');
                $('input[type=text], input[type=email]').val('');
                $("#startDate,#endDate").datepicker("option", "maxDate", new Date());
                formHtml(response.data.prices);
                drawChart(response.chartData);
            } else {
                $("#errorMsg").text(response.message);
                $("#errorMsg").removeClass('d-none');
            }
        },
        error: function(xhr, status, error) {
            $('#loader').css('display', 'none');
            console.error('Request failed:', status, error);
          }
    });
   }
}

function validateForm(){
    $(".error").text('');
    let flag = true;
    let companySymbol = $("#companySymbol").val();
    let startDate = $("#startDate").val();
    let endDate = $("#endDate").val();
    let email = $("#email").val();
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    
    if(companySymbol == ''){
        displayError('companySymbol','The company symbol field is required.');
        flag = false;
    }else if (!symbolList.hasOwnProperty(companySymbol.toUpperCase())) {
        displayError('companySymbol','Invalid Company Symbol');
        flag = false;
    }

    if(startDate == ''){
        displayError('startDate','The start date field is required.');
        flag = false;
    }else if(!isValidDate(startDate)){
        displayError('startDate','The start date is invalid.');
        flag = false;
    }

    if(endDate == ''){
        displayError('endDate','The end date field is required.');
        flag = false;
    }else if(!isValidDate(endDate)){
        displayError('endDate','The end date is invalid.');
        flag = false;
    }

    if(email == ''){
        displayError('email','The email field is required.');
        flag = false;
    }else if(!emailPattern.test(email)){
        displayError('email','The email must be a valid email address.');
        flag = false;
    }
    return flag;
}

function displayError(fieldName,message) {
    $("#"+fieldName+"_error").text(message);
}

function isValidDate(dateStr) {
    const datePattern = /^\d{4}-\d{2}-\d{2}$/;
    return datePattern.test(dateStr);
}

function getFormattedDate(dateToFormat){
    let timestamp = dateToFormat * 1000; 
    let date = new Date(timestamp);
    let formattedDate = date.toLocaleString();
    return formattedDate
}

function formHtml(priceData){
    $("#historicData").html('');
    let dataHtml = '';
    if (Array.isArray(priceData) && priceData.length > 0) {
        $.each(priceData, function(key, value) {
            dataHtml += '<tr>';
            dataHtml += '<td>'+getFormattedDate(value.date)+'</td>';
            dataHtml += '<td>'+value.open+'</td>';
            dataHtml += '<td>'+value.high+'</td>';
            dataHtml += '<td>'+value.low+'</td>';
            dataHtml += '<td>'+value.close+'</td>';
            dataHtml += '<td>'+value.volume+'</td>';
            dataHtml += '</tr>';
        });
        $("#historicData").html(dataHtml);
    }else{
        $("#historicData").html('<td colspan="6">No data to display</td>');
    }
}


function drawChart(chartData) {
    if(chartData.length !== 0){
        let options = {
            animationEnabled: true,
            theme: "light2", // "light1", "light2", "dark1", "dark2"
            exportEnabled: false,
            title: {
                text: "Historical Data"
            },
            
            axisX: {
                valueFormatString: "DD MMM"
            },
            axisY: {
                prefix: "$",
                title: "Price",
            }, 
            legend: {
                dockInsidePlotArea: true
            },
            data: [{
                type: "candlestick",
                showInLegend: true,
                legendText: "Date",
                xValueType: "dateTime",
                xValueFormatString: "DD MMM",
                risingColor: "#CBE8C8",
                fallingColor: "#FFCCCC",
                dataPoints: []
            }]
        };
        
        $("#chartContainer").CanvasJSChart(options);
        
        options.data[0].dataPoints = chartData;
        $("#chartContainer").CanvasJSChart().render();  
    }  
}