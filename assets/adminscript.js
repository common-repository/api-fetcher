jQuery(function ($) {
	$(document).ready(function () {

		var clickRequest = false;

		// CHECK API RESPONSE
		function api_plugin_isValidHttpUrl(string) {
			let url;
			try {
				url = new URL(string);
			} catch (_) {
				return false;
			}
			return url.protocol === "http:" || url.protocol === "https:";
		}

		// 	INITIALIZE DATA, DO A REQUEST TO DISPLAY EVERYTHING PROPERLY
		function api_plugin_initAllData() {
			if ($('input[name=api__url_field]').val() != '') {
				api_plugin_fetchData(clickRequest);
			}
		}
		setTimeout(function () {
			api_plugin_initAllData();
		}, 500);

		// FETCH THE DATA FUNCTION
		function api_plugin_fetchData(clicker) {
			let apiUrl = $('input[name=api__url_field]').val();
			var responseMessage = 'Nothing happened';
			var responseClass = 'api-warning api-notice';
			let responseField = $('.api__response_field pre');
			$(responseField).html('Checking...');
			if (apiUrl == '' && clicker == true) {
				responseMessage = 'Please enter a url before testing';
				$(responseField).html('Fill in a url above to test the response data');
				api_plugin_apiTestNotice(responseClass, responseMessage, clicker);
			} else {
				if (api_plugin_isValidHttpUrl(apiUrl)) {
					$.ajax({
						type: 'post',
						dataType: 'json',
						url: api_ajax_tester.ajax_url,
						data: {
							action: 'api_test_ajax',
							testurl: apiUrl,
							_ajax_nonce: api_ajax_tester.nonce
						},
						complete: function (xhr, status) {
							if (xhr.status == 200 || status == 'succes') {
								responseClass = 'api-notice api-succes';
								responseMessage = 'Succes, it looks like this url is fully functioning!';
								let jsonResponse = $.parseJSON(xhr.responseText);
								$(responseField).html(JSON.stringify(jsonResponse, null, '  '));
								$('#hidden__api_response').remove();
								$('<input>').attr({
									type: 'hidden',
									id: 'hidden__api_response',
									name: 'api__response_hidden_field',
									value: xhr.responseText
								}).appendTo('body')
								api_plugin_createHtmlElements(xhr.responseText, clicker);
							} else if (xhr.status == 0) {
								responseClass = 'api-notice api-error';
								responseMessage = 'Looks like the url is blocked by CORS policy';
								$(responseField).html(status);
							} else if (xhr.status == 404) {
								responseClass = 'api-notice api-error';
								responseMessage = '404 not found error';
								$(responseField).html(status);
							} else if (status == 'error') {
								responseClass = 'api-notice api-error';
								responseMessage = 'An error occured, please check your url';
								$(responseField).html(status);
							} else {
								responseClass = 'api-notice api-error';
								responseMessage = 'Error unknown';
								$(responseField).html(status);
							}
							api_plugin_apiTestNotice(responseClass, responseMessage, clicker);
						},
						error: function (xhr, status) {
							responseClass = 'api-notice api-error';
							responseMessage = 'The url responded with an error, check the url and make sure it\'s valid';
							api_plugin_apiTestNotice(responseClass, responseMessage, clicker);
							$(responseField).html('The url responded with an error, check the url and make sure it\'s valid')
						}
					});
				} else {
					responseMessage = 'Url is not valid, make sure it starts with http:// or https://';
					api_plugin_apiTestNotice(responseClass, responseMessage, clicker);
				}
			}
		}

		// WHEN CLICKED ON TEST URL OR GET DATA, FETCH THE API AND CREATE USER FEEDBACK
		$(document).on('click', '#api__test_url_field, #api__get_json_response', function (e) {
			e.preventDefault();
			api_plugin_apiTestNotice('api-notice', 'Checking...');
			clickRequest = true;
			api_plugin_fetchData(clickRequest);
		})

		// CREATE USER FEEDBACK NOTICE ON THE RESPONSE OF THE API
		function api_plugin_apiTestNotice(noticeClass, noticeMessage, clicker) {
			if (clicker == true) {
				let responseWindow = $('#api__test_url_response');
				$(responseWindow).text(noticeMessage);
				$(responseWindow).attr('class', noticeClass);
			}
		}

		// CHECK IF POST TYPE EXISTS OR ADD APTION TO CREATE NEW POST TYPE WITH NAME
		$(document).on('change', '#api__post_type_field select', function () {
			if ($(this).val() == 'api__create_new') {
				$('input[name=api__new_post_type_name]').show();
			} else {
				$('input[name=api__new_post_type_name]').hide();
				$('input[name=api__new_post_type_name]').val('');
			}
		});

		// CREATE HTML ELEMENTS OF THE API RESPONSE
		function api_plugin_createHtmlElements(jsonResponse, clicker) {
			var isThereAnObject = false;
			let apiResponse = JSON.parse(jsonResponse);
			$template = $('.api__response_field .api__response_element.template');
			$('#api_data').find('*').not('.template').remove();
			for (var key of Object.keys(apiResponse)) {
				var $element = $template.clone().removeClass('template').appendTo('#api_data');
				var $secElement = $template.clone().removeClass('template').appendTo('#api_data');
				$element.attr('id', key).addClass('response__field');
				$secElement.attr('data-key', 'api__field__' + key).addClass('to__create_field');
				$secElement.html('api__field__' + key);
				if (typeof apiResponse[key] === 'object' && apiResponse[key] !== null) {
					var objectStr = JSON.stringify(apiResponse[key], null, 4);
					$element.html(key + ': ' + objectStr);
					isThereAnObject = true;
				} else {
					$element.html(key + ': ' + apiResponse[key]);
				}
			}
			if (isThereAnObject && clicker == true && $('input[name=api__object_in_array_key]').val() == '') {
				let objectResponseExample = {
					status: 'ok',
					error: 'none',
					posts: [
						{
							post_author: 'John Doe',
							post_body: 'Lorem ipsum...'
						},
						{
							post_author: 'Aliya Tromp',
							post_body: 'Dolor sit amet...'
						}
					]
				};
				let objectResponseEl = $('#api__response_with_object');
				let objectResponseOptions = '<span class="button-primary" id="response__object_selection_mode">Yes, let me select it</span> <span id="response__object_ignore" class="button-secondary">No, this object should be included in each post as a separate field</span>';
				let objectResponseClass = 'api-notice api-warning';
				let objectResponseMessage = '<strong>There is an object in your API response, should each property of this object be a separate post?</strong></br>For example, if your API response is: </br><pre id="object-warning-example"></pre>and you only want to save the posts as a post (including the post_author and post_content), disregard the error and status.</br><div id="response__options"></div>';
				$('#api__response_with_object').slideDown();
				$(objectResponseEl).attr('class', objectResponseClass);
				$(objectResponseEl).html(objectResponseMessage);
				$('#object-warning-example').html(JSON.stringify(objectResponseExample, null, 4));
				$(objectResponseOptions).appendTo('#response__options');
			} else if ($('input[name=api__object_in_array_key]').val() != '') {
				// CALL OBJECT
				let objectSelectThis = $('input[name=api__object_in_array_key]').val();
				$('#' + objectSelectThis + '.api__response_element.response__field').addClass('objectHighlighted');
				api_plugin_highlightSelected();
			}
		}

		// SET HIGHLIGHTMODE ON TO MAKE THE USER SELECT An OBJECT
		var highlightMode = false;
		$(document).on('click', '#response__object_selection_mode', function () {
			$('#api_data').addClass('highlight-mode');
			$('#meta-box-id').addClass('highlight-mode');
			highlightMode = true;
		});

		// IGNORE THE REQUEST TO SELECT AN OBJECT FROM THE API RESPONSE
		$(document).on('click', '#response__object_ignore', function () {
			$('#api_data').removeClass('highlight-mode');
			$('#meta-box-id').removeClass('highlight-mode');
			$('#api__response_with_object').slideUp();
		});

		// HIGHLIGHT MODE FOR SELECTING A TITLE KEY
		var titleHighlightMode = false;
		$(document).on('click', '#post__title_selection_mode', function () {
			$('#api_data').addClass('title-highlight-mode');
			titleHighlightMode = true;
		});

		// WHAT TO DO AFTER THE HIGHLIGHTED OBJECT HAS BEEN SELECTED
		function api_plugin_highlightSelected() {
			var api__objectKey = $('.objectHighlighted').attr('id');
			var api__jsonObject = JSON.parse($('#hidden__api_response').val());
			$template = $('.api__response_field .api__response_element.template');
			$('#api_data').find('*').not('.template').remove();
			for (var key of Object.keys(api__jsonObject[api__objectKey][0])) {
				var $element = $template.clone().removeClass('template').appendTo('#api_data');
				var $secElement = $template.clone().removeClass('template').appendTo('#api_data');
				$element.attr('id', key).addClass('response__field');
				$secElement.attr('data-key', 'api__field__' + key).addClass('to__create_field');
				$secElement.html('api__field__' + key);
				if (typeof api__jsonObject[api__objectKey][0][key] === 'object' && api__jsonObject[api__objectKey][0][key] !== null) {
					var objectStr = JSON.stringify(api__jsonObject[api__objectKey][0][key], null, 4);
					$element.html(key + ': ' + objectStr);
					isThereAnObject = true;
				} else {
					$element.html(key + ': ' + api__jsonObject[api__objectKey][0][key]);
				}
			}
		}

		// SET ELEMENT TO HIGHLIGHTED IF IT HAS BEEN CLICKED IN HIGHLIGHTMODE
		$(document).on('click', '.api__response_element.response__field', function (e) {
			if (highlightMode) {
				$('.api__response_element.response__field').removeClass('objectHighlighted');
				var api__field_object = $(this).attr('id');
				$(this).addClass('objectHighlighted');
				$('#api_data').removeClass('highlight-mode');
				$('#meta-box-id').removeClass('highlight-mode');
				$('#api__response_with_object').slideUp();
				$('input[name=api__object_in_array_key]').val(api__field_object);
				highlightMode = false;
				api_plugin_highlightSelected();
			}
			if (titleHighlightMode) {
				$('.api__response_element.response__field').removeClass('titleHighlighted');
				$(this).addClass('titleHighlighted');
				$('#api_data').removeClass('title-highlight-mode');
				var titleKeyObject = $(this).attr('id');
				titleKeyObject = titleKeyObject.replace('api__', '');
				$('input[name=api__use_as_post_title]').val(titleKeyObject);
			}
		})
	})
})