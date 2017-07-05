$( document ).ready(function() {

  if (tissues_selected.length > 0)
  {
    for (index = 0; index < tissues_selected.length; ++index)
    {
      $("#filter-tissue-list").append("<li><a href='#' class='tissue-filter' data-tissue='" + tissues_selected[index] + "'>" + tissues_selected[index].replace("_", " ").charAt(0).toUpperCase() + tissues_selected[index].replace("_", " ").slice(1) + "</a></li>");
    }
  }

  $(".tissue-filter").on("click", function(){
    $("#current_count").text("0");
    $("#prot_current").text("0");
    $("#variants-results").empty();
    if ($(this).data("tissue") == "ALL"){
        tissues_selected = tissues_original;
    }
    else {
        tissues_selected = [$(this).data("tissue")];
    }
    $("#variants-results").parent().after("<div class='spinner'><div class='bounce1'></div><div class='bounce2'></div><div class='bounce3'></div></div>");
    $.ajax({
      url: "./variant_load.php",
      type: "GET",
      data: { is_ajax: "yes", is_tissue: "yes", tissue:tissues_selected, current_count:parseInt($("#prot_current").text()), prot:prot_name, source:prot_source, mut_type:mut_types},
      success: function(results){
        $("#variants-results").append(results);

        var variant_count = 0;
        $('#variants-results').children('tr').each(function () {
         if ($.isNumeric($(this).find(".mut-count").text())){
           variant_count += parseInt($(this).find(".mut-count").text());
         }
        });
        $("#current_count").html(variant_count);

        $("#total_num").html($("#mut_c").data("count"));
        $("#prot_num").html($("#prot_c").data("count"));

        $("#prot_current").html($("#variants-results .normal").length);
        $(".spinner").remove();
        processing = false;
      },
      error:function(){
      }
    });
  });

  $(document).scroll(function(e){
    if(all_done) return false;
    if (processing) return false;

    if ($(window).scrollTop() >= ($(document).height() - $(window).height())*0.9) {
      processing = true;
      $("#variants-results").parent().after("<div class='spinner'><div class='bounce1'></div><div class='bounce2'></div><div class='bounce3'></div></div>");
      $.ajax({
        url: "./variant_load.php",
        type: "GET",
        data: { is_ajax: "yes", tissue:tissues_selected, current_count:parseInt($("#prot_current").text()), prot:prot_name, source:prot_source, mut_type:mut_types},
        success: function(results){
          $("#variants-results").append(results);
          if ($("#variants-results .normal").length == $("#prot_current").html()) all_done = true;
          //Get current # of variants
          var variant_count = 0;
          $('#variants-results').children('tr').each(function () {
            if ($.isNumeric($(this).find(".mut-count").text())){
              variant_count += parseInt($(this).find(".mut-count").text());
            }
          });          
          $("#current_count").html(variant_count);
          $("#prot_current").html($("#variants-results .normal").length);
          $(".spinner").remove();
          processing = false;
        },
        error:function(){}
      });
    }
  });

  $("#total_num").html(mut_count);
  $("#prot_num").html(prot_count);
  var variant_count = 0;
  var all_done =false;
  $('#variants-results').children('tr').each(function () {
    if ($.isNumeric($(this).find(".mut-count").text())){
      variant_count += parseInt($(this).find(".mut-count").text());
    }
  });
  $("#current_count").html(variant_count);
  $("#prot_current").html($("#variants-results .normal").length);

  $('#variant-table tbody').on("click", "tr", function() {
    window.location.href = './details.php?variant=' +$(this).data("protein") + '&tissues=' + tissues_selected;
  });

  $("#download-current").on("click", function(){
    $('#download_modal').modal('show');
    window.location.href = './download_all.php?tissue=' + tissues_selected + '&variant_search=' + '<?php echo json_encode($_GET["variant_search"]);?>' + '&source=' + '<?php echo json_encode($_GET["source"]);?>' + '&type=' + '<?php echo json_encode($_GET["mut_type"]);?>' + '&end=' + $('#prot_current').text() <?php if(isset($_GET['prot'])){ ?> + '&prot=' + "<?php echo $_GET['prot'];?>"<?php } ?>;
  });

  $("#singlebutton").on("click", function(){
    $("#singlebutton").html("<div class='spinner3'><div class='cube1 cube3'></div><div class='cube2 cube3'></div></div>");
  });
});