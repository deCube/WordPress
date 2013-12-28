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
	 * Holds lang object for current request
	 *
	 * @access private
	 * @var $lang
	 */
	private $lang;

	/**
	 * Gets $lang
	 *
	 * @return object $lang
	 */
	public function getLang()
	{
		return $this->lang;
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
		if (false != in_array($langCode, $reservedNames) || preg_match( '|([a-z0-9-]+.php.*)|', $_SERVER['REQUEST_URI'] )) {
			return;
		} else {
			if (2 == strlen($langCode)) {
				$this->lang = new WP_Lang( $langCode );

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

class WP_Lang
{
	/**
	 * Language ID
	 * @var int
	 */
	public $ID;
	/**
	 * Language code (ISO 639-1)
	 * @var string
	 */
	public $code;
	/**
	 * Native name of language
	 * @var string
	 */
	public $name;
	/**
	 * Is this language default?
	 * @var int
	 */
	public $default;

	/**
	 * Constructor
	 * @param int/string $lang Language ID or code
	 */
	public function __construct( $lang )
	{
		if (is_numeric($lang)) {
			$this->getLanguageById( $lang );
		} else {
			$this->getLanguageByCode( $lang );
		}
	}

	private function getLanguageById( $lang_ID )
	{
		global $wpdb;

		$language = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->lang WHERE id = %d", $lang_ID ) );

		$this->setVars($language);
	}

	private function getLanguageByCode( $lang_code )
	{
		global $wpdb;

		$language = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->lang WHERE code = %s", $lang_code ) );

		$this->setVars($language);
	}

	private function setVars( $language )
	{
		foreach ( $language as $k => $value ) {
			$this->$k = $value;
		}
	}
}
