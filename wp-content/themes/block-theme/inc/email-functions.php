<?php
/**
 * Create a link from an email address and add Javascript to prevent visibility to bots without JS.
 *
 * @param string  $email_address     The email address to obfuscate.
 * @param string  $class             Classes to add to the element.
 *
 * @return string                    The output <a> link element.
 */
function obfuscate_email_tag( $email_address, $class='' ) {
    $email = obfuscate_email( $email_address );    
    return '<a href="javascript:void(0);" onclick="window.open(\'mailto:' . $email . '\',\'_blank\');return false;" class="' . $class . '"><script type="text/javascript">document.write(\'' . $email . '\');</script></a>';
}

/**
 * Convert an email string with base_convert.
 *
 * @param string $email     The email address to obfuscate.
 *
 * @return string           The obfuscated email address
 */
function obfuscate_email( $email_address ) {
    $ret = '';
    for ( $x=0; $x<strlen( $email_address ); $x++ ) {
        $ret .= '\u' . sprintf( "%04s", (string) base_convert( ord( $email_address[$x] ), 10, 16 ) );
    }
    return $ret;
}

/**
 * Filters content and obfuscate any email addresses found.
 *
 * @param string  $block_content     The html markup of the content.
 * @param array   $block             An array of block data.
 *
 * @return string                    The content.
 */
function obfuscate_block_content_email_addresses( $block_content, $block ) {
	if ( ! isset( $_GET['_locale'] ) && false !== strpos( $block_content, '@' ) ) {
		$block_content = preg_replace_callback( '/<a[^>]*href="([^"]*@[^"]*)[^>]*>(.*?)<\/a>/ms', 'obfuscate_block_content_email_addresses_callback', $block_content );
	}

	return $block_content;
}
add_filter( 'render_block', 'obfuscate_block_content_email_addresses', 10, 2 );

/**
 * The callback function for the preg_replace_callback to obfuscate email addresses.
 *
 * @param string  $content           The html markup of the content.
 *
 * @return string                    The content.
 */
function obfuscate_block_content_email_addresses_callback( $matches ) {
	$email_address    = str_replace( 'mailto:', '', $matches[1] );
	$obfuscated_email = obfuscate_email( $email_address );
	$link_content     = str_replace( $email_address, '<script type="text/javascript">document.write(\'' . $obfuscated_email . '\');</script>', $matches[2] );

	$new_link = str_replace( '<a', '<a onclick="window.open(\'mailto:' . $obfuscated_email . '\',\'_blank\');return false;"', str_replace( $matches[2], $link_content, str_replace( $matches[1], 'javascript:void(0);', $matches[0] ) ) );

	return $new_link;
}

/**
 * The callback function for the preg_replace_callback to obfuscate email addresses when they're in standard content and not links.
 *
 * @param string  $content           The html markup of the content.
 *
 * @return string                    The content.
 */
function obfuscate_non_link_block_content_email_addresses_callback( $matches ) {
	$obfuscated_email = obfuscate_email( $matches[0] );

	return '<script type="text/javascript">document.write(\'' . $obfuscated_email . '\');</script>';
}