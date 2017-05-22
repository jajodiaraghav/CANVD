$(function(){
  $("#function-filter").on("click", "a", function() {
    if ($(this).data("func") == 'all') {
      var new_url = window.location.href.split('&int-filter')[0];
      window.location.href = new_url;
    } else {
      var new_url = window.location.href.split('&int-filter')[0] + "&int-filter=" + $(this).data("func");
      window.location.href = new_url;
    }
  });
});