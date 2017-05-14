$(function() {
	$('#quickalert').delay(1500).fadeOut(1000);

	$("#ann-btn").on( "click", function() {
		$("#data-view").hide();
		$("#announce-view").fadeIn('fast');
		$(this).removeClass("btn-default").addClass("btn-success");
		$("#data-btn").removeClass("btn-success").addClass("btn-default");
	});

	$("#data-btn").on( "click", function() {
		$("#announce-view").hide();
		$("#data-view").fadeIn('fast');
		$(this).removeClass("btn-default").addClass("btn-success");   
		$("#ann-btn").removeClass("btn-success").addClass("btn-default");                             
	});

	$(".delete-btn").on( "click", function() {
		var this_btn = $(this).parent();
	  	$.ajax({
	        url: "./announce_delete.php",
	        type: "post",
	        data: {a_id:$(this).data("item-id")},
	        success: function(results){
				this_btn.fadeOut();
	        },
	        error:function(){
	            alert("failure");
	        }
	    });
	});

	$(".show-btn").on( "click", function() {
		var valueT = 0;
		if($(this).hasClass("fa-check-square-o")) {
			valueT = 0;
			$(this).removeClass("fa-check-square-o").addClass("fa-square-o");
		}
		else {
			valueT = 1;
			$(this).removeClass("fa-square-o").addClass("fa-check-square-o");
		}

		$.ajax({
	        url: "./announce_change.php",
	        type: "post",
	        data: {switchV:$(this).data("btn-type"),value:valueT,a_id:$(this).data("item-id")},
	        success: function(results){
	        },
	        error:function(){
	            alert("failure");
	        }
		});
	});

	$("#admin-list").on( "click", "a", function() {
		$("#table-name-header").text($(this).text());
		$("#hidden-table").val($(this).text());
		$("#panel-content").show();
		$("#panel-content").find("#fields").remove();
		$("#panel-content").prepend("<div id='fields'><b>The following fields (columns) are required for this table:<p style='font-size:0.7em'>" + $(this).data("fields") + "</p></div>");
	});

	$(document).on('change', '.btn-file :file', function() {
	  	var input = $(this),
	    numFiles = input.get(0).files ? input.get(0).files.length : 1,
	    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	  	input.trigger('fileselect', [numFiles, label]);
	});

	$('.btn-file :file').on('fileselect', function(event, numFiles, label) {
		console.log(numFiles);
		console.log(label);
		$(".btn-file").text(label);
	});
});