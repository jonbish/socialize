var myOptions = {
	palettes: true
};

jQuery(document).ready(function ($) {
	$('#border-color').wpColorPicker(myOptions);
	$('#background-color').wpColorPicker(myOptions);
	$('#top-border-color').wpColorPicker(myOptions);
	$('#top-background-color').wpColorPicker(myOptions);
	$('#svg-color-picker').wpColorPicker(myOptions);
});
