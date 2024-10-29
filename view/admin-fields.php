<?php

defined('ABSPATH') || die();

// OUTPUT FIELDS AFTER ADD META BOX CALLBACK FUNCTION IS FIRED
function register_fields_callback($post)
{
	wp_nonce_field(basename(__FILE__), 'api__plugin_nonce');
	$api__plugin_stored_meta = get_post_meta($post->ID);
?>
	<div class="api__field_group" id="api__url_field">
		<p class="api__field_title">API url</p>
		<p class="api__field_description"><?php echo __('Place here your full JSON REST API url including all parameters such as your api-key (if necessary). Make sure the response is in JSON format.'); ?></p>
		<div class="api__field_input">
			<input type="text" name="api__url_field" placeholder="Full url, for example: https://api.exchangerate-api.com/v4/latest/USD" value="<?php if (!empty($api__plugin_stored_meta['api__url_field'])) echo esc_attr($api__plugin_stored_meta['api__url_field'][0]); ?>">
			<a id="api__test_url_field" class="button-primary"><?php echo __('Test url'); ?></a>
		</div>
		<div id="api__test_url_response">
		</div>
	</div>
	<div class="api__field_group" id="api__time_field">
		<p class="api__field_title"><?php echo __('API update schedule'); ?></p>
		<p class="api__field_description"><?php echo __('How often does your API call have to be executed and the posts have to be updated? To minimize stress on your server, decrease the frequency.'); ?></p>
		<div class="api__field_input">
			<select name="api__time_field">
				<?php
				$api__schedule_options = wp_get_schedules();
				$api__schedule_selected = $api__plugin_stored_meta['api__time_field'][0];
				foreach ($api__schedule_options as $optionKey => $optionValue) {
					echo '<option value="' . $optionKey . '" ' . selected($api__schedule_selected, $optionKey) . '>' . $optionValue['display'] . '</option>';
				}
				?>
			</select>
		</div>
	</div>
	<div class="api__field_group" id="api__post_type_field">
		<p class="api__field_title"><?php echo __('Select post type to add new data to'); ?></p>
		<p class="api__field_description"><?php echo __('Select the post type to which new information from the API should saved. When - create new - is selected, enter post type name'); ?></p>
		<div class="api__double_field">
			<div class="api__field_input">
				<select name="api__post_field">
					<?php
					$api__post_type_selected = $api__plugin_stored_meta['api__post_field'][0];
					?>
					<option value="api__create_new" <?php selected($api__post_type_selected, 'api__create_new') ?>>- <?php echo __('Create new post type'); ?> -</option>
					<?php
					$api__get_cpt_args = array(
						'public' => true
					);
					$api__post_types = get_post_types($api__get_cpt_args);
					unset($api__post_types['attachment']);
					unset($api__post_types['api_calls']);
					foreach ($api__post_types as $post_type) {
						echo '<option value="' . $post_type . '" ' . selected($api__post_type_selected, $post_type) . ' >' . $post_type . '</option>';
					}
					?>
				</select>
			</div>
			<div class="api__field_input">
				<input type="text" <?php if ($api__post_type_selected != 'api__create_new' && !empty($api__plugin_stored_meta['api__post_field'])) {
										echo 'style="display:none;"';
									} else {
										echo 'style="display:block;"';
									} ?> name="api__new_post_type_name" placeholder="New post type name" value="<?php if (!empty($api__plugin_stored_meta['api__new_post_type_name'])) echo esc_attr($api__plugin_stored_meta['api__new_post_type_name'][0]); ?>">
			</div>
		</div>
	</div>
	<div class="api__field_group" id="api__response_layout">
		<p class="api__field_title"><?php echo __('Check the response fields of your api'); ?></p>
		<p class="api__field_description"><?php echo __('Make sure you have tested the url above. Match up the API response data with fields that should be created for every new post.'); ?></p>
		<div class="api__field_input">
			<a id="api__get_json_response" class="button-primary" href="#"><?php echo __('Get data'); ?></a>
			<input type="text" readonly="true" placeholder="<?php echo __("Array key in object, leave empty if there's no array/object in the response. (optional)"); ?>" name="api__object_in_array_key" value="<?php if (!empty($api__plugin_stored_meta['api__object_in_array_key'])) echo esc_attr($api__plugin_stored_meta['api__object_in_array_key'][0]); ?>">
		</div>
		<div id="api__response_with_object">
		</div>
		<hr class="api__hr">
		<div class="api__response_field side-by-side">
			<div id="api_data" class="api__response half-response-wrapper">
				<div class="api__response_element template"></div>
			</div>
		</div>
	</div>
	<div class="api__field_group" id="api__select_title_field">
		<p class="api__field_title"><?php echo __('Select the field of which the value should be the title of each new post'); ?></p>
		<p class="api__field_description"><?php echo __('Press the button below to enable selection mode. Select a field that is unique for every post (a title, id or time/date for example).'); ?></p>
		<div class="api__field_input">
			<span class="button-primary" id="post__title_selection_mode"><?php echo __('Yes, let me select it'); ?></span>
			<input type="text" name="api__use_as_post_title" readonly="true" value="<?php if (!empty($api__plugin_stored_meta['api__use_as_post_title'])) echo esc_attr($api__plugin_stored_meta['api__use_as_post_title'][0]); ?>">
		</div>
	<?php
}
