<?php
/**
* 
*/
class OPSTheme
{
	public $theme;
	public $themeDir;
	public $pageDir;
	public $partDir;
	public $pagename;
	public $pagepath;

	public $content;

	public function __construct()
	{
		$this->theme    = THEME;

		$this->themeDir = "themes/{$this->theme}";
		if (! file_exists($this->themeDir))
			$this->themeDir = '../' . $this->themeDir;
		if (! file_exists($this->themeDir))
			$this->themeDir = '../' . $this->themeDir;

		$this->pageDir  = "{$this->themeDir}/html/pages";
		$this->partDir  = "{$this->themeDir}/html/parts";
	}

	public function addVariable($name, $value)
	{
		if (is_object($value))
			$value = (array) $value;

		if (! isset($GLOBALS['themeVars']))
			$GLOBALS['themeVars'] = array();

		global $themeVars;
		$themeVars[ $name ] = $value;
	}

	public function viewPage($pagename, $d = false)
	{
		$this->pagename = $pagename;
		$this->pagepath = "{$this->pageDir}/{$this->pagename}.html";

		if (! file_exists($this->pagepath))
		{
			if (! $d)
				return '';

			$this->pagepath = str_replace($this->themeDir, $d . '/theme', $this->pagepath);
		}

		$open = fopen($this->pagepath, 'r');
		$this->content = @fread($open, filesize($this->pagepath));
		fclose($open);

		$this->processVariables();
		return \ops_minify_html($this->content);
	}

	public function viewPart($partname, $d = false)
	{
		$this->pagename = $partname;
		$this->pagepath = "{$this->partDir}/{$this->pagename}.html";

		if (! file_exists($this->pagepath))
		{
			if (! $d)
				return '';

			$this->pagepath = str_replace($this->themeDir, $d . '/theme', $this->pagepath);
		}

		$open = fopen($this->pagepath, 'r');
		$this->content = @fread($open, filesize($this->pagepath));
		fclose($open);

		$this->processVariables();
		return \ops_minify_html($this->content);
	}
	
	public function getVariable($var)
	{
		$name = trim($var);

		if (preg_match('/[^a-zA-Z0-9._]/', $name))
			return '';

		if (preg_match('/^[A-Z_]+$/', $name) && defined($name))
			return constant($name);

		$arrayKeys = explode('.', $name);

		if (! isset($GLOBALS['themeVars']))
			$GLOBALS['themeVars'] = array();

		global $themeVars;

		if (count($arrayKeys) === 1)
		{
			$themeVar = $themeVars[ $name ];

			if (is_array($themeVar))
				$themeVar = json_encode($themeVar);
			
			return $themeVar;
		}

		$arrayVars = $themeVars;

		foreach ($arrayKeys as $key)
		{
			if (!isset($arrayVars[$key]))
			{
				$arrayVars = '';
				break;
			}

			$arrayVars = $arrayVars[$key];
		}

		if (is_array($arrayVars))
			$arrayVars = json_encode($arrayVars);

		return $arrayVars;
	}

	public function processVariables()
	{
		$this->content = preg_replace_callback(
			//'/\{\$([a-zA-Z0-9_]+)\}/',
			'/\{\$(.*?)\}/',

			function ($matches)
			{
				$var = explode('<', $matches[1]);
				$name = trim($var[0]);

				if (preg_match('/[^a-zA-Z0-9._]/', $name))
					return $matches[0];

				if (preg_match('/^[A-Z_]+$/', $name) && defined($name))
					return constant($name);

				$arrayKeys = explode('.', $name);

				if (! isset($GLOBALS['themeVars']))
					$GLOBALS['themeVars'] = array();

				global $themeVars;

				if (count($arrayKeys) === 1)
				{
					if (! isset($themeVars[ $name ]))
						return '';
					
					$themeVar = $themeVars[ $name ];

					if (is_array($themeVar))
						$themeVar = json_encode($themeVar);
					
					return $themeVar;
				}

				$arrayVars = $themeVars;

				foreach ($arrayKeys as $key)
				{
					if (!isset($arrayVars[$key]))
					{
						$arrayVars = '';
						break;
					}

					$arrayVars = $arrayVars[$key];
				}

				if (is_array($arrayVars))
					$arrayVars = json_encode($arrayVars);

				return $arrayVars;
			},

			$this->content
		);
	}
}

$opsTheme = new OPSTheme();
$opsTheme->addVariable('get',   $_GET);
$opsTheme->addVariable('post',  $_POST);
$opsTheme->addVariable('theme', array(
	'id' => THEME,
	'url' => 'themes/' . THEME
));
