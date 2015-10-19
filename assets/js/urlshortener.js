$(document).ready(function(){
  $("#checkbox").change(function() {

    var daysInput = $(".form-inline span, .form-inline .form-control");
    /* Show\Hide input time to live  */
    if (this.checked) {
        daysInput.removeClass("hidden");
    } else {
        daysInput.addClass("hidden");
    }
  });
  $(".btn").click(function(){

    var url = $("p > input").val();
    var ttl = $(".form-inline .form-control").val();
    var alertDanger = $(".alert-danger");
    var alertSuccess = $(".alert-success");
    /* Set error message */
    function showError() {
      alertDanger.removeClass("hidden");
      alertSuccess.addClass("hidden");
    }

    if (!alertDanger.hasClass("hidden")) {
      alertDanger.addClass("hidden")
    }
    if (url) {
      /* Set URL */
      var data = {
        url: url
      };
      /* Set time to live */
      if (ttl && ttl != 0 && $("#checkbox").is(":checked")) {
        data.ttl = ttl;
      }
      $.ajax({
        type: 'GET',
        dataType: 'json',
        url: 'handler.php',
        data: data,
        success: function(data){
          if (data.error) {
            /* Show error message */
            showError();
          } else {
            /* Show URL with short code */
            alertSuccess.removeClass("hidden").html(data.url);
            alertDanger.addClass("hidden");
          }
        }
      });
    } else {
      /* Show error message */
      showError();
    }
  });
});