<?php

/*
 * Contao check
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

/**
 * Check the Contao 4.x requirements
 *
 * @author Fritz Michael Gschwantner <https://github.com/fritzmg>
 */
class Contao4
{
	const PHP_VERSION = '5.6.0';

	/**
	 * @var boolean
	 */
	protected $compatible = true;

	/**
	 * Execute the command
	 */
	public function run()
	{
		include __DIR__ . '/../views/contao4.phtml';
	}

	/**
	 * Return the Contao 4.x compatibility of the environment
	 *
	 * @return boolean True if Contao 4.x can be run
	 */
	public function isCompatible()
	{
		return $this->compatible;
	}

	/**
	 * Executes all compatibility checks.
	 *
	 * @return boolean True if Contao 4.x can be run
	 */
	public function checkCompatibility()
	{
		if (!$this->hasPhp()) {
			return false;
		}

		if (!$this->hasGraphicsLib()) {
			return false;
		}

		if (!$this->hasDom()) {
			return false;
		}

		if (!$this->hasIntl()) {
			return false;
		}

		if (!$this->canWriteTmpDir()) {
			return false;
		}

		if (!$this->canUseSymlink()) {
			return false;
		}

		if (!$this->canCreateSymlinks()) {
			return false;
		}

		if (!$this->hasXmlReader()) {
			return false;
		}

		return true;
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

		$this->compatible = false;

		return false;
	}

	/**
	 * Check whether any of the supported graphics libraries are available
	 *
	 * @return boolean True if any of the supported graphics libraries are available
	 */
	public function hasGraphicsLib()
	{
		if (function_exists('gd_info')) {
			 if (version_compare(GD_VERSION, '2.0.1', '>')) {
			 	return true;
			 }
		}

		if (class_exists('Imagick')) {
			return true;
		}

		if (class_exists('Gmagick')) {
			return true;
		}

		$this->compatible = false;

		return false;
	}

	/**
	 * Check whether the PHP DOM extension is available
	 *
	 * @return boolean True if the PHP DOM extension is available
	 */
	public function hasDom()
	{
		if (extension_loaded('dom')) {
			return true;
		}

		$this->compatible = false;

		return false;
	}

	/**
	 * Check whether the PHP intl extension is available
	 *
	 * @return boolean True if the PHP intl extension is available
	 */
	public function hasIntl()
	{
		if (extension_loaded('intl')) {
			return true;
		}

		$this->compatible = false;

		return false;
	}

	/**
	 * Check whether the system tmp directory is writeable
	 *
	 * @return boolean True if the system tmp directory is writeable
	 */
	public function canWriteTmpDir()
	{
		if (is_writable(sys_get_temp_dir())) {
			return true;
		}

		$this->compatible = false;

		return false;
	}

	/**
	 * Check whether the PHP symlink() function is available
	 *
	 * @return boolean True if the PHP symlink() function is available
	 */
	public function canUseSymlink()
	{
		if (function_exists('symlink')) {
			return true;
		}

		$this->compatible = false;

		return false;
	}

	/**
	 * Check whether a symlink can successfully be created
	 *
	 * @return boolean True if a symlink was successfully created
	 */
	public function canCreateSymlinks()
	{
		@unlink('test');
		$result = @symlink(__FILE__, 'test');
		@unlink('test');

		if (true === $result) {
			return true;
		}

		$this->compatible = false;

		return false;
	}

	/**
	 * Check whether the PHP xmlreader extension is available
	 *
	 * @return boolean True if the PHP xmlreader extension is available
	 */
	public function hasXmlReader()
	{
		if (extension_loaded('xmlreader')) {
			return true;
		}

		$this->compatible = false;

		return false;
	}
}
