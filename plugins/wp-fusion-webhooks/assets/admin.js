jQuery(document).ready(function($){

	$( "#wpf-webhook-test" ).on( "click", function(event) {

		event.preventDefault();

		$("#wpf-webhook-test").text("Sending...");
		$("#wpf-webhook-test").attr('disabled', true);

		var data = {
			'action'	: 'wpf_webhook_test',
			'data'		: $('#wpf-webhook-settings :input').serialize()
		};

		$.post(ajaxurl, data);

		setTimeout(function() {
			$("#wpf-webhook-test").text("Send Test");
			$("#wpf-webhook-test").removeAttr('disabled',true);
		}, 1000 );

	});

	$('select#wpf-webhook-topic').change(function(event) {

		if( $(this).val() == 'tags_applied' || $(this).val() == 'tags_removed' ) {

			$('tr#which-tags-row .slidewrapper').slideDown('400');

			$('tr#which-tags-row td, tr#which-tags-row th').animate({'padding-top' : '20px', 'padding-bottom' : '20px'}, 'slow');

		} else {

			$('tr#which-tags-row .slidewrapper').slideUp('400');

			$('tr#which-tags-row td, tr#which-tags-row th').animate({'padding-top' : 0, 'padding-bottom' : 0}, 'slow');

		}

		if( $(this).val() == 'form_submitted' ) {

			$('li.show_if_form_submitted').show().find('input').prop('checked', true);

		} else {

			$('li.show_if_form_submitted').hide().find('input').prop('checked', false);;

		}

	});


	$('a#wpf-webhooks-check-all').click(function(event) {
		
		event.preventDefault();

		$('ul#wpf-webhook-post-fields input').each(function(index, el) {
			
			$(this).prop( 'checked', 'true' );

		});

	});

	$('a#wpf-webhooks-uncheck-all').click(function(event) {
		
		event.preventDefault();

		$('ul#wpf-webhook-post-fields input').each(function(index, el) {
			
			$(this).removeProp('checked');

		});

	});


});