jQuery(document).ready(function($) {
	jQuery('select').select2();
	jQuery('tr').on('change', '.shortcode_select', function(event) {
		event.preventDefault();
		var ids = jQuery('.shortcode_ids').val();
		var inex = jQuery(this).val();
		jQuery('.shortcode_ready').val('[filterable-store '+inex+'="'+ids+'"]');
	});
	jQuery('tr').on('change', '.shortcode_ids', function(event) {
		event.preventDefault();
		var ids = jQuery(this).val();
		var inex = jQuery('.shortcode_select').val();
		jQuery('.shortcode_ready').val('[filterable-store '+inex+'="'+ids+'"]');
	});	
});