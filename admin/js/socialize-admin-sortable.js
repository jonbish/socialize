jQuery(document).ready(function ($) {
	var id = 'socialize_text';

	// Delay to ensure DOM is ready and WP finished loading editors
	setTimeout(function () {
		if (typeof tinymce !== 'undefined') {
			var ed = tinymce.get(id);
			if (ed) {
				ed.remove();
			}
			if (typeof tinyMCEPreInit !== 'undefined' && tinyMCEPreInit.mceInit[id]) {
				tinymce.init(tinyMCEPreInit.mceInit[id]);
			}
		}

		// Re-init Quicktags if needed
		if (typeof quicktags !== 'undefined' && typeof QTags !== 'undefined') {
			if (!QTags.getInstance(id)) {
				quicktags({ id: id });
			}
		}
	}, 300);

	$("#inline-sortable").sortable({
		placeholder: "ui-socialize-highlight"
	});
	$("#alert-sortable").sortable({
		placeholder: "ui-socialize-highlight"
	});
	$("#inline-sortable").disableSelection();
	$("#alert-sortable").disableSelection();
});