$(function() {

  $("#search_form").on("submit", function( event ) {
    if ($("#search_input").val() == '')
    {
      event.preventDefault();
    }
    else
    {
      $("#search_btn").html("<div class='spinner3' style='margin-left:10px;margin-right:10px;'><div class='cube1 cube3'></div><div class='cube2 cube3'></div></div>");
    }
  });

  //Get total counts for the tables
  var tissue_total = 0;
  var pwm_total = 0;
  var protein_total = 0;
  $.ajax({
    url: "./tables/total.php",
    type: "post",
    dataType: 'json',
    success: function(results) {
      $("#tissue-total").html(results[0]);
      tissue_total = results[0];		      
      $("#pwm-total").html(results[1]);
      pwm_total = results[1];
      $("#protein-total").html(results[2]);
      protein_total = results[2];
    },
    error:function(){}
  });

  function update_protein_view() {
    var t = $("#protein-page").data("page");
    $("#protein-table").fadeOut('fast');
    $("#protein-start").html(t);
    if ($("#protein-page").data("page") +10 < protein_total) $("#protein-end").html(t+10);
    else $("#protein-end").html(protein_total);

    $.ajax({
      url: "./tables/proteins.php",
      type: "post",
      data: { start : t },
      success: function(results) {
          if (onTheClick) $("#protein-table").fadeIn('fast');
          $("#protein-table-body").html('');
          $("#protein-table-body").html(results);
      },
      error:function(){}
    });
  }

  update_protein_view();
  var onTheClick = false;
  $("#protein-back").on( "click", function() {
    onTheClick = true;
    if ($("#protein-page").data("page") != 0)
    {
      $("#protein-page").data("page", $("#protein-page").data("page") - 10);
      update_protein_view();
    }
  });

  $("#protein-forward").on( "click", function() {
    onTheClick = true;
    if($("#protein-page").data("page") + 10 < protein_total)
    {
      $("#protein-page").data("page", $("#protein-page").data("page") + 10);
      update_protein_view();
    }
  });

  function update_pwm_view()
  {
    var t = $("#pwm-page").data("page");
    $("#pwm-start").html(t);
    if ($("#pwm-page").data("page") + 10 < pwm_total) $("#pwm-end").html(t+10);
    else $("#pwm-end").html(pwm_total);
    $.ajax({
      url: "./tables/pwm.php",
      type: "post",
      data: { start : t },
      success: function(results) {
        if (results !='')
        {
          $("#pwm-table-body").html('');
          $("#pwm-table-body").html(results);
        }
      },
      error:function(){}
    });
  }

  update_pwm_view();
  $("#pwm-back").on("click", function() {
    if ($("#pwm-page").data("page") != 0)
    {
      $("#pwm-page").data("page", $("#pwm-page").data("page") - 10);
      update_pwm_view();
    }
  });

  $("#pwm-forward").on( "click", function() {
    if($("#pwm-page").data("page") + 10 < pwm_total)
    {
      $("#pwm-page").data("page", $("#pwm-page").data("page") + 10);
      update_pwm_view();
    }
  });

  function update_tissue_view()
  {
    var t = $("#tissue-page").data("page");
    $("#tissue-start").html(t);
    if($("#tissue-page").data("page") + 10 < tissue_total) $("#tissue-end").html(t+10);
    else $("#tissue-end").html(tissue_total);

    $.ajax({
      url: "./tables/tissues.php",
      type: "post",
      data: { start : t },
      success: function(results) {
        if (results !='')
        {
          $("#tissue-table-body").html('');
          $("#tissue-table-body").html(results);
        }
      },
      error:function(){}
    });
  }

  update_tissue_view();
  $("#tissue-back").on("click", function() {
    if ($("#tissue-page").data("page") != 0)
    {
      $("#tissue-page").data("page", $("#tissue-page").data("page") - 10);
      update_tissue_view();
    }
  });

  $("#tissue-forward").on("click", function() {
    if ($("#tissue-page").data("page") + 10 < tissue_total)
    {
      $("#tissue-page").data("page", $("#tissue-page").data("page") + 10);
      update_tissue_view();
    }
  });

  $("#advanced_btn").on( "click", function() {
    if ($("#advanced_btn").text() == 'Advanced')
    {
      $("#advanced_btn").addClass("btn-info");
      $("#advanced_btn").text("Basic");
      $("#browse-and-tabs").hide();
      $("#advanced-search-box").show();
    }
    else
    {
      $("#advanced_btn").removeClass("btn-info");
      $("#advanced_btn").text("Advanced");
      $("#browse-and-tabs").show();
      $("#advanced-search-box").hide();
    }
  });

  $("body").on( "click", ".pwm-img", function() {
    $("#testing").modal('show');
    $("#actual-image-content").html($(this).clone());
    $("#actual-image-content").append($(this).data("content"));
  });
});
