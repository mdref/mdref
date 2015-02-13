<?php

namespace mdref;

/**
 * Static mdref generator
 */
class Generator
{
	/**
	 * @var \mdref\Reference
	 */
	private $reference;
	
	/**
	 * @var \mdref\Generator\Renderer
	 */
	private $renderer;
	
	/**
	 * Create a new generator
	 * @param string $refs list of reference paths
	 * @param string $dir output directory
	 */
	public function __construct($refs, $dir = null) {
		$this->reference = new Reference(explode(PATH_SEPARATOR, $refs));
		$this->renderer = new Generator\Renderer($dir ?: "public/static");
	}
	
	/**
	 * Run the generator
	 */
	public function run() {
		$this->generateRoot();
		foreach ($this->reference as $repo) {
			$iter = new \RecursiveIteratorIterator($repo, 
				\RecursiveIteratorIterator::SELF_FIRST);
			foreach ($iter as $ref) {
				$this->generateEntry($ref);
			}
		}
	}
	
	/**
	 * Generate index.html and LICENSE.html
	 */
	private function generateRoot() {
		printf("Generating index ...\n");
		$data = $this->createPayload(null);
		$data->ref = "index";
		$this->renderer->persist($data);

		printf("Generating LICENSE ...\n");
		$data->text = file_get_contents(__DIR__."/../LICENSE");
		$data->ref = "LICENSE";
		$this->renderer->persist($data);
	}
	
	/**
	 * Generate HTML for an entry
	 * @param \mdref\Entry $ref
	 */
	private function generateEntry(Entry $ref) {
		printf("Generating %s ...\n", $ref->getName());
		$data = $this->createPayload($ref);
		$this->renderer->persist($data);
	}
	
	/**
	 * Create the view payload
	 * @param \mdref\Entry $ref
	 * @param \mdref\Generator\Renderer $view
	 * @return \stdClass
	 */
	private function createPayload(Entry $ref = null) {
		$pld = new \stdClass;
		
		$pld->quick = [$this->reference, "formatString"];
		$pld->file = [$this->reference, "formatFile"];
		$pld->refs = $this->reference;
		$pld->view = $this->renderer;
		if ($ref) {
			$pld->entry = $ref;
			$pld->ref = $ref->getName();
		}
		
		return $pld;
	}
}

namespace mdref\Generator;

class Renderer
{
	/**
	 * @var string
	 */
	private $dir;

	/**
	 * @param string $dir output directory
	 */
	public function __construct($dir = "public/static") {
		$this->dir = $dir;
	}
	
	/**
	 * HTML entity encode special characters
	 * @param string $string
	 * @return string
	 */
	public function esc($string) {
		return htmlspecialchars($string);
	}
	
	/**
	 * Render mdref page
	 * @param \stdClass $pld
	 * @return string
	 */
	public function render(\stdClass $pld) {
		$content = "";
		ob_start(function($data) use(&$content) {
			$content .= $data;
			return true;
		});
		static::renderFile("views/layout.phtml", (array) $pld);
		ob_end_flush();
		return $content;
	}

	/**
	 * Persist mdref page to output directory
	 * @param \stdClass $data
	 */
	public function persist(\stdClass $data) {
		$html = $this->render($data);
		$file = sprintf("%s/%s.html", $this->dir, $data->ref);
		$this->saveFile($file, $html);
		$this->linkIndex(dirname($file));
	}
	
	/**
	 * Save data to file (write to $file.tmp and rename to $file)
	 * @param string $file
	 * @param string $data
	 * @throws \Exception
	 */
	private function saveFile($file, $data) {
		$dir = dirname($file);
		if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
			throw new \Exception("Failed to create directory '$dir'");
		}
		if (!file_put_contents("$file.tmp", $data)) {
			throw new \Exception("Failed to save file '$file.tmp'");
		}
		if (!rename("$file.tmp", $file)) {
			throw new \Exception("Failed to rename to '$file'");
		}
	}
	
	private function linkIndex($dir) {
		$index = "$dir.html";
		$link = "$dir/index.html";
		if (is_file($index) && !is_file($link)) {
			printf("Generating index for '%s'\n", substr($dir, strlen($this->dir)));
			link($index, $link);
		}
	}
	
	/**
	 * Render file
	 */
	static private function renderFile() {
		if (func_num_args() > 1) {
			extract(func_get_arg(1));
		}
		include func_get_arg(0);
	}
}
