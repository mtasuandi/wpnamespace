var Teguh = Teguh || (function(){
	var _args = {};
	return {
		init : function(Args) {
			_args = Args;
		},
		generateNewToken : function() {
			jQuery(document).ready(function(){
				/**
				 * Trigger when user want to regenerate new Token
				 */
				jQuery(document).on('submit', '#' + _args[0] + '_form_api_key', function(e) {
					e.preventDefault();
					var elApi = jQuery('#' + _args[0] + '_api_key');
					var elButton = jQuery('#' + _args[0] + '_submit');
					var nonce = jQuery('#' + _args[0] + '_nonce').val();
					var action = _args[0] + '_generate_new_api_key';
					var confirm_generate = confirm('Generate new Token?');
					if(true == confirm_generate) {
						try {
							jQuery.ajax({
								type: 'POST',
								url: ajaxurl,
								data: {action:action, nonce:nonce},
								dataType: 'json',
								beforeSend: function() {
									elButton.prop('disabled', true);
								},
								success: function(result) {
									if(0 == result.response_code) {
										elApi.val(result.new_api_key);
									} else {
										alert('Failed to generate new Token!');
									}
								},
								complete: function() {
									setTimeout(function(){elButton.prop('disabled', false);}, 1000);
								},
								error: function(a, b, c) {
									alert(c);
									return false;
								}
							});
						} catch(err) {
							alert(err.message);
							return false;
						}
					}
				});
			});
		},
		onTokenFieldFocus : function() {
			/**
			 * on Focus
			 */
			jQuery(document).on('focus', '#' + _args[0] + '_api_key' ,function() {
				this.select();
			}).mouseup(function() {
				return false;
			});
		}
	}
}());