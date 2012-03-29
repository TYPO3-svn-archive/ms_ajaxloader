$(document).ready( function() {
	$('.tx-msajaxwrapper-pi1').ms_ajaxwrapper();
});



(function( $ ) {
	$.fn.ms_ajaxwrapper = function(loader) {

			// get my url
		var ajaxUrl;
		var ajaxQuery;
		getUrl();

			// cycle over all loaders
		this.each( function () {
			var loader = $(this).children('.ajaxLoader');
			var params = getParams(loader);


			var back = $.ajax({
				url: ajaxUrl,
				global: false,
				type: 'POST',
				data: {
					parent_uid: params[0],
					content_uid: params[1],
					additional_params: params[2],
					type: params[3]
				},
				dataType: 'html', // expected from the server
				async: true,
				success: function(msg, textStatus, jqXHR){
					var targetArr = $(msg).attr('id').split('_');
					var targetID = '#xc_' + targetArr[1];

					$(targetID).append(msg).removeClass('loading');
					return true;
				}
			});



		});

			// get params
		function getParams(loader) {
			var params = new Array();
			var loaderParams = $(loader).children('.params')

			params[0] = $(loaderParams).children('.uid').text();
			params[1] = $(loaderParams).children('.content_uid').text();
			params[2] = $(loaderParams).children('.additional_params').text();
			params[3] = $(loaderParams).children('.type_num').text();
			return params;
		}


			// get this url
			// found here: http://www.coderholic.com/javascript-the-good-parts/
		function getUrl() {
			var url = document.URL;

			var parse_url = /^(?:([A-Za-z]+):)?(\/{0,3})([0-9.\-A-Za-z]+)(?::(\d+))?(?:\/([^?#]*))?(?:\?([^#]*))?(?:#(.*))?$/;
			var result = parse_url.exec(url);
			if (result[1] != undefined && result[1] != '') {
				var scheme = result[1] + '://';
				var host   = result[3] + '/';
				var path   = result[5];
			} else {
				if ($.browser.msie) {
					var scheme = 'http://';
					var host   = window.location.host + '/';
					var path   = result[3] + '/'  + result[5];
				} else {
					var scheme = '';
					var host   = '';
					var path   = result[3] + '/'  + result[5];
				}
			}

			if (result[6] != undefined && result[6] != '') {
				var query  = '?' + result[6];
			} else {
				var query = '?';
			}

			ajaxUrl = scheme + host + path;
			ajaxQuery = query;
		}
	};
})( jQuery );
