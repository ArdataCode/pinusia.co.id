jQuery(document).ready(function($){

	$(".variation_id").change(function() {

		var value = $(this).val();

		var varId = "afpv_var_id_" + value;

		var varIds = "afpv_var_ids_" + value;

		if ( value == 0 ) {

			return;

		} else {

			$("#" + varId).removeClass("afpv-var-hide-swatches");

			$("#" + varId).addClass("afpv-var-show-swatches");

			$("#" + varIds).removeClass("afpv-hide-swatches");

			$("#" + varIds).addClass("afpv-show-swatches");

			$('.attachment-post-thumbnail').hide(); 

		}

	});

	$(".reset_variations").click( function() {

		$var = $(".variation_id").val();

		var varIda = "afpv_var_id_" + $var;

		var varIdas = "afpv_var_ids_" + $var;

		console.log(varIda );

		$("#" + varIda).removeClass("afpv-var-show-swatches");

		$("#" + varIda).addClass("afpv-var-hide-swatches");

		$("#" + varIdas).removeClass("afpv-show-swatches");

		$("#" + varIdas).addClass("afpv-hide-swatches");
		
		$('.attachment-post-thumbnail').css("display", "block");

	});

	var dot_option_selected = true;
	if ( 'yes' == (afpv_gallery_thumb_setting.afpv_dots_gallery_controller) ) {
		dot_option_selected = true;
	}

	if ( 'yes' != (afpv_gallery_thumb_setting.afpv_dots_gallery_controller) ) {
		dot_option_selected = false;
	}

	var arrows_option_selected = true;
	if ( 'yes' == (afpv_gallery_thumb_setting.afpv_arrows_gallery_controller) ) {
		arrows_option_selected = true;
	}

	if ( 'yes' != (afpv_gallery_thumb_setting.afpv_arrows_gallery_controller) ) {
		arrows_option_selected = false;
	}

	if ( afpv_gallery_thumb_setting.afpv_gallery_pos == 'pv_gallery_thumbnail_left_position' ) {
	
		$('.gl-product-slides').slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			autoplay: false,
			autoplaySpeed: 4000,
			arrows: false,
			dots: false,
			fade:true,
			asNavFor: '.gl-product-slider-left-nav' 
		});

		$('.gl-product-slider-left-nav').slick({
			slidesToShow: afpv_gallery_thumb_setting.afpv_gallery_thumbnail_to_show,
			vertical: true,
			verticalSwiping: true,
			infinite: true,
			slidesToScroll: 2,
			asNavFor: '.gl-product-slides',
			dots: dot_option_selected,
			arrows:arrows_option_selected,
			focusOnSelect: true,
			autoplay:false
		});

	} else if ( afpv_gallery_thumb_setting.afpv_gallery_pos == 'pv_gallery_thumbnail_right_position' ) {

		jQuery('.gl-product-slides').slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			autoplay: false,
			autoplaySpeed: 4000,
			arrows: false,
			dots: false,
			fade:true,
			asNavFor: '.gl-product-slider-right-nav' 
		});

		jQuery('.gl-product-slider-right-nav').slick({
			slidesToShow: afpv_gallery_thumb_setting.afpv_gallery_thumbnail_to_show,
			vertical: true,
			verticalSwiping: true,
			infinite: true,
			slidesToScroll: 2,
			asNavFor: '.gl-product-slides',
			dots: dot_option_selected,
			arrows:arrows_option_selected,
			focusOnSelect: true,
			autoplay:false
		});

	} else if ( afpv_gallery_thumb_setting.afpv_gallery_pos == 'pv_gallery_thumbnail_top_position' ) {

		jQuery('.gl-product-slides').slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			autoplay: false,
			autoplaySpeed: 4000,
			arrows: false,
			dots: false,
			fade:true,
			asNavFor: '.gl-product-slider-top-nav' 
		});

		jQuery('.gl-product-slider-top-nav').slick({
			slidesToShow: afpv_gallery_thumb_setting.afpv_gallery_thumbnail_to_show,
			infinite: true,
			slidesToScroll: 2,
			asNavFor: '.gl-product-slides',
			dots: dot_option_selected,
			arrows:arrows_option_selected,
			focusOnSelect: true,
			autoplay:false
		});
	
	} else {

		jQuery('.gl-product-slides').slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			autoplay: false,
			autoplaySpeed: 4000,
			arrows: false,
			dots: false,
			fade:true,
			asNavFor: '.gl-product-slider-bottom-nav' 
		});

		jQuery('.gl-product-slider-bottom-nav').slick({
			slidesToShow: afpv_gallery_thumb_setting.afpv_gallery_thumbnail_to_show,
			infinite: true,
			slidesToScroll: 2,
			asNavFor: '.gl-product-slides',
			dots: dot_option_selected,
			arrows:arrows_option_selected,
			nextArrow: '<i class="fa fa-chevron-right next-arrow"></i>',
        	prevArrow: '<i class="fa fa-chevron-left prev-arrow"></i>',
			focusOnSelect: true,
			autoplay:false
		});

	}
  

	jQuery('.gl-product-slides').slickLightbox({
		itemSelector        : 'a',
		navigateByKeyboard  : true
	});



	/*========== Feature Video Script ============*/

	jQuery('.video-thumbnail iframe').fadeOut();
	jQuery('.video-thumbnail video').fadeOut();

	$('.afpv-product-video-play-icon img').click(function(event) {
		event.preventDefault();
		$('.afpv-product-video-thumbnail-image').fadeOut();
		$('.afpv-product-video-play-icon img').fadeOut();
		$('.afpv-product-video-play-icon').fadeOut();
		$('#gl-product-video iframe').fadeIn(2500);
		$('#gl-product-video video').fadeIn(2500);
	});

	$('.afpv-rule-video-play-icon img').click(function(event) {
		event.preventDefault();
		$('.afpv-rule-video-thumbnail-image').fadeOut();
		$('.afpv-rule-video-play-icon img').fadeOut();
		$('.afpv-rule-video-play-icon').fadeOut();
		$('#gl-product-rule-video iframe').fadeIn(2500);
		$('#gl-product-rule-video video').fadeIn(2500);
	});

	/*=========== Zoom Script =============*/

	//jQuery('#gl-proucts .gl-product-thumbnail-zoom').zoom();



});

