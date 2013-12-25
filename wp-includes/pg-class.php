<?php
/**
 * @package WordPress
 * @subpackage WPolyglot
 *
 * @since  3.9.0
 */
class WPolyglot
{
	/**
	 * Holds lang code for current request
	 *
	 * @access private
	 * @var $langCode
	 */
	private $langCode;

	/**
	 * Gets $langCode
	 *
	 * @return string $langCode
	 */
	public function getLangCode()
	{
		return $this->langCode;
	}

	public function init()
	{
		//check request uri
		$this->checkRequestUri();
	}

	/**
	 * Checks if request uri has language code, if not, redirects to default language
	 *
	 * @since  3.9.0
	 * @return void
	 */
	private function checkRequestUri()
	{
		$uri = $_SERVER['REQUEST_URI'];
		$hasLang = false;

		if (3 == strlen($_SERVER['REQUEST_URI'])) {
			header( 'Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '/' );
			die();
		}

		$requestParts = explode( '/', trim( $_SERVER['REQUEST_URI'], '/' ) );
		// die(var_dump($requestParts));
		$langCode = $requestParts[0];

		//if in reserved names, stop evaluating
		$reservedNames = array( 'wp-admin', 'wp-includes', 'wp-content', 'files', 'feed' ); //'page', 'comments', 'blog',
		if (false !== in_array($langCode, $reservedNames)) {
			return;
		} else {
			if (2 == strlen($langCode)) {
				$this->langCode = $langCode;

				//strip langCode from request uri
				$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 3);
				return;
			}
		}

		if ( ! $hasLang ) {
			//redirect to default lang
			$defaultLang = $this->getDefaultLang();
			$destination = $_SERVER['HTTP_HOST'] . '/' . $defaultLang->code . '/';

			header( 'Location: http://' . $destination );
			die();
		}
	}

	/**
	 * Tries to retrieve the default language
	 *
	 * @return object Wpdb object carrying default language row
	 */
	private function getDefaultLang()
	{
		global $wpdb;

		$defaultLang = $wpdb->get_row("SELECT * FROM $wpdb->lang WHERE default = 1");
		//no default language specified, keep digging
		if ( ! $defaultLang ) {
			//get default language depending on WPLANG value
			if ( defined('WPLANG') && WPLANG ) {
				$defaultLang = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->lang WHERE code = %s", substr(WPLANG, 0, 2) ) );
			}

			//stil no language... well, get the first language in the language table than
			if (!$defaultLang) {
				$defaultLang = $wpdb->get_row("SELECT * FROM $wpdb->lang LIMIT 1");
			}
		}

		return $defaultLang;
	}
}