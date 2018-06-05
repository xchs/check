<?php

/*
 * Contao check
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

require_once __DIR__ . '/file-permissions.php';

/**
 * Check the Composer package manager requirements
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class Composer
{
	const PHP_VERSION = '5.3.4';

	/**
	 * @var boolean
	 */
	protected $filePermissions;

	/**
	 * @var boolean
	 */
	protected $available = true;

	/**
	 * Execute the command
	 */
	public function run()
	{
		$this->filePermissions = $this->checkFilePermissions();

		include __DIR__ . '/../views/composer.phtml';
	}

	/**
	 * Return the availability of the Composer package manager
	 *
	 * @return boolean True if the Composer package manager can be used
	 */
	public function isAvailable()
	{
		return $this->available;
	}

	/**
	 * Check whether the PHP version meets the requirements
	 *
	 * @return boolean True if the PHP version meets the requirements
	 */
	public function hasPhp()
	{
		if (version_compare(phpversion(), static::PHP_VERSION, '>=')) {
			return true;
		}

		$this->available = false;

		return false;
	}

	/**
	 * Check whether the PHP Phar extension is available
	 *
	 * @return boolean True if the PHP Phar extension is available
	 */
	public function hasPhar()
	{
		if (extension_loaded('Phar')) {
			return true;
		}

		$this->available = false;

		return false;
	}

	/**
	 * Check whether the XCache extension is loaded
	 *
	 * @return boolean True if the PHP Phar extension is loaded
	 */
	public function hasXCache()
	{
		if (!extension_loaded('XCache')) {
			return false;
		}

		$this->available = false;

		return true;
	}

	/**
	 * Check whether the PHP cURL extension is available
	 *
	 * @return boolean True if the PHP cURL extension is available
	 */
	public function hasCurl()
	{
		if (function_exists('curl_init')) {
			return true;
		}

		$this->available = false;

		return false;
	}

	/**
	 * Check whether the PHP APC extension is installed
	 *
	 * @return boolean True if the PHP APC extension is installed
	 */
	public function hasApc()
	{
		if (!extension_loaded('apc') || extension_loaded('apcu')) {
			return false;
		}

		$this->available = false;

		return true;
	}

	/**
	 * Check whether the PHP Suhosin extension is enabled
	 *
	 * @return boolean True if the PHP Suhosin extension is enabled
	 */
	public function hasSuhosin()
	{
		$suhosin = ini_get('suhosin.executor.include.whitelist');

		if ($suhosin === false) {
			return false;
		}

		$allowed = array_map('trim', explode(',', $suhosin));

		// The previous check returned false positives for e.g. "phar."
		if (in_array('phar', $allowed) || in_array('phar://', $allowed)) {
			return false;
		}

		$this->available = false;

		return true;
	}

	/**
	 * Check whether "allow_url_fopen" is enabled
	 *
	 * @return boolean True if "allow_url_fopen" is enabled
	 */
	public function hasAllowUrlFopen()
	{
		if (ini_get('allow_url_fopen')) {
			return true;
		}

		$this->available = false;

		return false;
	}

	/**
	 * Return true if the PHP process is allowed to create files
	 *
	 * @return boolean True if the PHP process is allowed to create files
	 */
	public function canCreateFiles()
	{
		if (!$this->filePermissions) {
			return true;
		}

		$this->available = false;

		return false;
	}

	/**
	 * Check whether the PHP shell_exec function is available
	 *
	 * @return boolean True if the PHP shell_exec function is available
	 */
	public function hasShellExec()
	{
		if (function_exists('shell_exec')) {
			return true;
		}

		return false;
	}

	/**
	 * Check whether the PHP proc_open function is available
	 *
	 * @return boolean True if the PHP proc_open function is available
	 */
	public function hasProcOpen()
	{
		if (function_exists('proc_open')) {
			return true;
		}

		return false;
	}

	/**
	 * Return true if the PHP process is allowed to create files
	 *
	 * @return boolean True if the PHP process is allowed to create files
	 */
	protected function checkFilePermissions()
	{
		$permissions = new FilePermissions;

		if ($permissions->hasSafeMode()) {
			return true;
		}

		if (function_disabled('posix_getpwuid')) {
			return true;
		}

		if (!$permissions->canCreateFolder()) {
			return true;
		}

		if (!$permissions->canCreateFile()) {
			return true;
		}

		return false;
	}
}
