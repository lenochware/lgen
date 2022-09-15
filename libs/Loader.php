<?php

class Loader
{
	protected $dir;
	protected $json;

	function __construct($dir)
	{
		$this->dir = $dir;
	}

	function outputJson(array $data)
	{
	  header('Content-Type: application/json; charset=utf-8');
	  die(json_encode($data, JSON_UNESCAPED_UNICODE/*|JSON_PRETTY_PRINT*/));
	}

	function removeComments($s)
	{
		return preg_replace('/\/\/.*/', '', $s);
	}

	//transform tag array to hashtable (more effective lookup)
	// function getLookup(array $list)
	// {
	// 	$tags = [];
	// 	foreach ($list as $id) {
	// 		$tags[$id] = true;
	// 	}

	// 	return $tags;
	// }

	protected function loadJsonFile($path)
	{
		$default = [
			'render' => ['char' => '?', 'color' => 'pink'], 
			'desc' => 'You see nothing interesting.', 
			'tags' => [],
		];

		$s = $this->removeComments(file_get_contents($path));
		$this->json = json_decode($s, true);

		foreach ($this->json as $k => $obj) {
			if (!is_array($obj) or empty($obj['extends'])) continue;
			$this->extendObject($k, $obj['extends']);
		}

		foreach ($this->json as $k => $obj) {
			if (!is_array($obj)) continue;

			$this->json[$k] += $default;			

			$this->json[$k]['id'] = $k;
			if (!isset($obj['name'])) $this->json[$k]['name'] = $k;
			
			// if ($this->json[$k]['tags']) {
			// 	$this->json[$k]['tags_lookup'] = $this->getLookup($this->json[$k]['tags']);
			// }

		}

		return $this->json;
	}

	function loadTemplates()
	{
    $templates = [];
    foreach(glob($this->dir.'/templates/*.html') as $path) {
      $templates[basename($path, ".html")] = file_get_contents($path);
    }

    return $templates;
	}

  function loadObjects()
  {
    $worldParts = ['tiles', 'items', 'actors', 'texts', 'actions', 'levels'];

    $world = [];

    foreach ($worldParts as $part) {
      $world[$part] = $this->loadJsonFile($this->dir."/objects/$part.json");

      if (json_last_error() != JSON_ERROR_NONE) {
        die("Error loading $part: " . json_last_error_msg());
      }
    }

    return $world;
  }

  function save($id)
  {
	$path = $this->dir."/saved/".pcl_ident($id).".json";
	file_put_contents($path, $_POST['data']);
  }

  function load($id)
  {
	$path = $this->dir."/saved/".pcl_ident($id).".json";
	if (!file_exists($path)) return null;

	return json_decode(file_get_contents($path), true);
  }

	function extendObject($id, $parentId, $level = 1)
	{
		if (isset($this->json[$parentId]['extends']) and $level < 50) {
			$this->extendObject($parentId, $this->json[$parentId]['extends'], ++$level);
		}

		$this->json[$id] = $this->json[$id] + $this->json[$parentId];
	}

}

?>