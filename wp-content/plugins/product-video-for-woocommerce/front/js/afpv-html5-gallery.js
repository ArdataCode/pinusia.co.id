jQuery(document).ready(function($){

	$(".variation_id").change(function() {

		var value = $(this).val();

		var varId = "afpv_var_ids_" + value;

		if ( value == 0 ) {

			return;
			
		} else { 

			$("#" + varId).removeClass("afpv-hide-swatches");

			$("#" + varId).addClass("afpv-show-swatches");

			$('.afpv-product-feature-img').hide();

		}

	});


	$(".reset_variations").click( function() {

		$var = $(".variation_id").val();

		var varIda = "afpv_var_ids_" + $var;

		$("#" + varIda).removeClass("afpv-show-swatches");

		$("#" + varIda).addClass("afpv-hide-swatches");

		$('.afpv-product-feature-img').css("display", "block");

		$('.afpv-product-feature-img img').css("display", "block");

	});



});

