
jQuery(document).ready(function ($) {
	var t_test = $('input:radio[name=socialize_twitterWidget]:checked').val();
	$(".socialize-twitter-select").hide();
	$("#socialize-twitter-" + t_test).show();

	var f_test = $('input:radio[name=socialize_fbWidget]:checked').val();
	$(".socialize-facebook-select").hide();
	$("#socialize-facebook-" + f_test).show();

	var r_test = $('input:radio[name=socialize_RedditWidget]:checked').val();
	$(".socialize-reddit-select").hide();
	$("#socialize-reddit-" + r_test).show();

	var p_test = $('input:radio[name=socialize_PinterestWidget]:checked').val();
	$(".socialize-pinterest-select").hide();
	$("#socialize-pinterest-" + p_test).show();

	var po_test = $('input:radio[name=socialize_PocketWidget]:checked').val();
	$(".socialize-pocket-select").hide();
	$("#socialize-pocket-" + po_test).show();

	var l_test = $('input:radio[name=socialize_LinkedInWidget]:checked').val();
	$(".socialize-linkedin-select").hide();
	$("#socialize-linkedin-" + l_test).show();

	var d_test = $("input[name=socialize_button_display]:checked").val();
	if (d_test == 'in') {
		$("input[name=socialize_out_margin]").attr("disabled", true);
		$("input[name=socialize_float][value='center']").removeAttr("disabled");
	} else {
		$("input[name=socialize_out_margin]").removeAttr("disabled");
		$("input[name=socialize_float][value='center']").prop("disabled", true);
	}

	$("input[name=socialize_twitterWidget]").change(function () {
		var t_test = $(this).val();
		$(".socialize-twitter-select").hide();
		$("#socialize-twitter-" + t_test).show();
	});
	$("input[name=socialize_fbWidget]").change(function () {
		var f_test = $(this).val();
		$(".socialize-facebook-select").hide();
		$("#socialize-facebook-" + f_test).show();
	});
	$("input[name=socialize_RedditWidget]").change(function () {
		var r_test = $(this).val();
		$(".socialize-reddit-select").hide();
		$("#socialize-reddit-" + r_test).show();
	});

	$("input[name=socialize_PinterestWidget]").change(function () {
		var p_test = $(this).val();
		$(".socialize-pinterest-select").hide();
		$("#socialize-pinterest-" + p_test).show();
	});

	$("input[name=socialize_PocketWidget]").change(function () {
		var po_test = $(this).val();
		$(".socialize-pocket-select").hide();
		$("#socialize-pocket-" + po_test).show();
	});
	$("input[name=socialize_LinkedInWidget]").change(function () {
		var l_test = $(this).val();
		$(".socialize-linkedin-select").hide();
		$("#socialize-linkedin-" + l_test).show();
	});

	$("input[name=socialize_button_display]").change(function () {
		var d_test = $(this).val();
		if (d_test == 'in') {
			$("input[name=socialize_out_margin]").attr("disabled", true);
			$("input[name=socialize_float][value='center']").removeAttr("disabled");
		} else {
			if ($("input[name=socialize_float][value='center']").is(':checked')) {
				$("input[name=socialize_float][value='left']").prop("checked", true);
			}
			$("input[name=socialize_out_margin]").removeAttr("disabled");
			$("input[name=socialize_float][value='center']").prop("disabled", true);;
		}
	});
});