<?php
/**
 * Multilingual WordPress API
 *
 * @package WordPress
 * @subpackage WPolyglot
 * @since 3.9.0
 */

add_filter( 'post_link', 'add_lang_code_to_url', 99, 2 );
add_filter( 'page_link', 'add_lang_code_to_url', 99, 2 );
add_filter( 'home_url', 'add_lang_code_to_home_url', 99, 2 );
// add_filter( 'site_url', 'add_lang_code_to_home_url', 99, 2 );

/**
 * Gets current lang of website
 * @return string Lang code of language
 */
function get_current_lang()
{
	global $wpg;

	return $wpg->getLang();
}

function get_post_lang_code( $post_id )
{
	$post = get_post( $post_id );

	if ( $post->lang_ID ) {
		$lang = get_lang_by_ID( $post->lang_ID );

		return $lang->code;
	}

	return false;
}

function get_lang_by_ID( $lang_ID )
{
	return new WP_Lang( $lang_ID );
}

function add_lang_code_to_url( $link, $post_id )
{
	//get lang code of post
	$langCode = get_post_lang_code( $post_id );
	$currentLang = get_current_lang();

	$homeUrl = str_replace($currentLang->code . '/', '', home_url( '/' ));
	$linkParts = explode( $homeUrl, $link );

	$newLink = $homeUrl . $langCode . '/' . $linkParts[1];

	return $newLink;
}

function add_lang_code_to_home_url( $url, $path )
{
	if ( ! $path || '/' == $path ) {
		$currentLang = get_current_lang();
		$url .= $currentLang->code . '/';
	}

	return $url;
}
