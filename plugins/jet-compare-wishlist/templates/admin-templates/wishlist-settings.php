<?php
/**
 * Wishlist settings dashboard template.
 *
 * @since 1.2.2
 * @since 1.5.2 Template updated..
 */
?>
<div id="jet-cw-settings-page jet-cw-settings-page__wishlist">
	<cx-vui-switcher
		name="enable_wishlist"
		label="<?php _e( 'Enable Wishlist', 'jet-cw' ); ?>"
		description="<?php _e( 'Enable wishlist functionality.', 'jet-cw' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		return-true="true"
		return-false="false"
		v-model="pageOptions.enable_wishlist.value"
	></cx-vui-switcher>

	<cx-vui-select
		v-if="pageOptions.enable_wishlist.value === 'true'"
		name="wishlist_store_type"
		label="<?php _e( 'Store type', 'jet-cw' ); ?>"
		description="<?php _e( 'Select store type for wishlist.', 'jet-cw' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="pageOptions.wishlist_store_type.options"
		v-model="pageOptions.wishlist_store_type.value"
	></cx-vui-select>

	<cx-vui-switcher
		v-if="pageOptions.enable_wishlist.value === 'true'"
		name="save_user_wish_list"
		label="<?php _e( 'Save the list for logged users', 'jet-cw' ); ?>"
		description="<?php _e( 'Enable this option if you want save wish list for logged users.', 'jet-cw' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		return-true="true"
		return-false="false"
		v-model="pageOptions.save_user_wish_list.value"
	></cx-vui-switcher>

	<cx-vui-select
		v-if="pageOptions.enable_wishlist.value === 'true'"
		name="wishlist_page"
		label="<?php _e( 'Wishlist Page', 'jet-cw' ); ?>"
		description="<?php _e( 'Choose wishlist page.', 'jet-cw' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="pageOptions.wishlist_page.options"
		v-model="pageOptions.wishlist_page.value"
	></cx-vui-select>

	<cx-vui-switcher
		v-if="pageOptions.enable_wishlist.value === 'true'"
		name="add_default_wishlist_button"
		label="<?php _e( 'Add default Wishlist Button', 'jet-cw' ); ?>"
		description="<?php _e( 'Add wishlist button to default WooCommerce templates.', 'jet-cw' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		return-true="true"
		return-false="false"
		v-model="pageOptions.add_default_wishlist_button.value"
	></cx-vui-switcher>
</div>
