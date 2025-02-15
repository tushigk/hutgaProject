<?php
/**
 * @package Online Poker Script - Addon Edition 1.0
 * @author Rehan Adil
 * @copyright 2019 Rehan Adil. All rights reserved.
**/

class OPSAddon
{
	private $functions = array();

	// Add hook to a addon
	public function add_hook( $array = array() )
	{
		if (! isset($array['page'], $array['location'], $array['function'])) return false;

		$page     = $array['page'];
		$location = $array['location'];
		$function = $array['function'];

		$this->functions[$page][$location][] = $function;
	}


	// Apply hook to a section of the script
	public function get_hooks( $contentArray = array(), $addonArray = array() )
	{
		if (! isset($contentArray['content'])) $contentArray['content'] = '';
		$content = $contentArray['content'];

		if (! isset($addonArray['page'], $addonArray['location']))
			return $content;

		$page     = $addonArray['page'];
		$location = $addonArray['location'];

		if (! isset($this->functions[$page][$location]))
			return $content;

		$functions = $this->functions[$page][$location];

		if (! is_array($functions))
			return $content;

		if (is_object($content))
			$content = (array) $content;

		if (is_array($content))
		{
			$merge = false;

			if (isset($addonArray['merge_array']) && $addonArray['merge_array'] == true)
				$merge = true;

			if ($merge)
			{
				foreach ($functions as $function)
				{
					$contentArray['content'] = call_user_func($function, $contentArray);
				}
				$updated_content = $contentArray['content'];
			}
			else
			{
				$updated_content = array();
				foreach ($functions as $function)
				{
					$updated_content = call_user_func($function, $contentArray);
				}
			}
		}
		else
		{
			$updated_content = '';
			foreach ($functions as $function)
			{
				$updated_content .= call_user_func($function, $contentArray);
			}
		}

		return $updated_content;
	}


	//
	public static function isActive($addon = '')
	{
		if (strlen($addon) < 1)
			return false;

		$parentDir = 'addons';
		if (! file_exists($parentDir))
			$parentDir = 'includes/' . $parentDir;

		$addonDir   = $parentDir . '/' . $addon;
		$activeFile = $addonDir . '/activated.html';

		if (file_exists($activeFile))
			return true;
		else
			return false;
	}
	
	
	// Apply hook to a section of the script
	public function get_function_names($addonArray = array() )
	{
		if (! isset($addonArray['page'], $addonArray['location']))
			return array();

		$page     = $addonArray['page'];
		$location = $addonArray['location'];

		if (! isset($this->functions[$page][$location]))
			return array();

		$functions = $this->functions[$page][$location];

		if (! is_array($functions))
			return array();

		return $functions;
	}



	// Define the page where the hooks will apply
	public function setPage( $page = '' )
	{
		if (empty($page))
			return false;

		$this->page = $page;
		return true;
	}


	// Define the section on the page where the hooks will apply
	public function setSection( $section = '' )
	{
		if (empty($section))
			return false;

		$this->section = $section;
		return true;
	}


	// Define the location on the section where the hooks will apply
	public function setLocation( $location = '' )
	{
		if (empty($location))
			return false;

		$this->location = $location;
		return true;
	}


	// Define the function to be executed
	public function setFunction( $function = '' )
	{
		if (empty($function))
			return false;

		$this->function = $function;
		return true;
	}


	public static function getSetting( $addon = '', $setting = '' )
	{
		global $addonSettings;
		$addon = preg_replace('/[^A-Za-z0-9_-]/i', '', $addon);

		if (isset($addonSettings[$addon][$setting]))
			return $addonSettings[$addon][$setting];

		$parentDir = 'addons';
		if (! file_exists($parentDir))
			$parentDir = 'includes/' . $parentDir;

		$addonDir     = $parentDir . '/' . $addon;
		$settingsFile = $parentDir . '//settings/' . $addon . '.json';

		if (! file_exists($addonDir . '/init.php')) return false;
		if (! file_exists($settingsFile))           return false;

		$settings = json_decode(file_get_contents($settingsFile), true);
		$addonSettings[$addon] = $settings;

		if (! isset($settings[$setting]))
			return false;

		return $settings[$setting];
	}
}