<?php

error_reporting(E_ALL &~ E_DEPRECATED);

function cut(array $lines, array $specs) {
	$delim = "[[:space:]]+";
	$bytes = [];
	$fields= [];
	
	foreach ($specs as $spec => $value) {
		switch ($spec) {
		case "d":
			$delim = $value;
			break;
		case "b":
			$bytes = $value;
			break;
		case "f":
			$fields = $value;
			break;
		}
	}
	
	$result = [];
	if ($bytes) {
		$func = "substr";
	} else {
		$func = function($a, $o = 0, $l = 0) {
			return join(" ", array_slice($a, $o, $l ? $l+1 : count($a)-$o));
		};
	}
	foreach ($lines as $line) {
		if ($bytes) {
			$spec = $bytes;
		} else {
			$line = split($delim, $line);
			$spec = $fields;
		}
		
		if ($spec[0] == "-") {
			$result[] = $func($line, 0, $spec[1]);
		} elseif ($spec[1] == "-") {
			if (empty($spec[2])) {
				$result[] = $func($line, $spec[0]);
			} else {
				$result[] = $func($line, $spec[0], $spec[2]-$spec[0]);
			}
		} else {
			$result[] = $line{$spec[0]};
		}
	}
	return $result;
}

function head($file, $lines = 1) {
	$ld = [];
	if (($fd = fopen($file, "r"))) {
		while ($lines--) {
			$ld[] = fgets($fd);
		}
	}
	return $ld;
}

function ns($file) {
	return str_replace("/", "\\", str_replace("//", "/", trim($file, "/.")));
}

function urlpath($dir, $file) {
	return (strlen($dir) ? $dir . "/" : "") . basename($file, ".md");
}

function ls($dir) {
	$dir = rtrim(is_dir($dir) ? $dir : dirname($dir) ."/". basename($dir, ".md"), "/");
	printf("<ul>\n");
	printf("<li><a href=/>Home</a></li>\n");
	if ($dir !== "." && ($dn = dirname($dir)) !== ".") {
		printf("<li><a href=/%s>%s</a></li>\n", 
			urlpath($dir, ".."),
			ns($dn));
	}
	if (is_dir($dir)) {
		if ($dir !== ".") {
			printf("<li>%s</li>\n", ns($dir));
		}
		foreach (scandir($dir) as $file) {
			/* ignore dot-files */
			if ($file{0} === ".") {
				continue;
			}
			
			$path = "$dir/$file";
			
			if (is_file($path)) {
				$pi = pathinfo($path);
				/* ignore files not ending in .md */
				if (!isset($pi["extension"]) || $pi["extension"] != "md") {
					continue;
				}
				if (!ctype_upper($file{0}) && !is_dir("$dir/".$pi["filename"])) {
					continue;
				}
			} else {
				/* ignore directories where an companying file exists */
				if (is_file("$path.md")) {
					continue;
				}
			}
			
			printf("<li><a href=\"/%s\">%s</a></li>\n", 
				urlpath($dir, $file),
				ns("$dir/".basename($file, ".md")));
		}
	}
	
	printf("</ul>\n");
}

function ml($file) {
	$pi = pathinfo($file);
	if (ctype_upper($pi["filename"][0])) {
		printf("<h2>Methods:</h2>\n");
		$dir = $pi["dirname"] . "/" . $pi["filename"];
		if (is_dir($dir)) {
			printf("<ul>\n");
			foreach (scandir($dir) as $file) {
				if (!is_file("$dir/$file") || ctype_upper($file{0})) {
					continue;
				}
				printf("<li><h3>%s</h3><p>%s</p></li>\n",
					basename($file, ".md"),
					join(" ", cut(head("$dir/$file"), ["f"=>"1-"]))
				);
			}
			printf("</ul>\n");
		}
	}
}

function md($file) {
	$file = rtrim($file, "/");
	if (is_file($file) || is_file($file .= ".md")) {
		$r = fopen($file, "r");
		$md = MarkdownDocument::createFromStream($r);
		$md->compile();
		echo $md->getHtml();
		fclose($r);
		ml($file);
	} else {
		printf("<h1>Quick Markdown Doc Browser</h1>\n");
		printf("<p>v0.1.0</p>\n");
		printf("<p>");
		ob_start(function($s) {
			return nl2br(htmlspecialchars($s));
		});
		readfile("LICENSE");
		ob_end_flush();
		printf("</p>\n");
	}
}


function index($pn) {
	?>
	<!doctype html>
	<html>
	<head>
		<meta charset="utf-8">
		<title><?=ns($pn)?></title>
		<link rel="stylesheet" href="/index.css">
	</head>
	<body>
		<div class="sidebar">
			<?php ls($pn); ?>
		</div>
		<?php md($pn); ?>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script> 
		<script src="/index.js"></script>
	</body>
	</html>
	<?php
}

chdir($_SERVER["DOCUMENT_ROOT"]);
$t = ["css"=>"text/css", "js"=>"application/javascript"];
$r = new http\Env\Request;
$u = new http\Url($r->getRequestUrl());
$s = new http\Env\Response;

switch($u->path) {
case "/index.js":
case "/index.css":
	$s->setHeader("Content-type", $t[pathinfo($u->path, PATHINFO_EXTENSION)]);
	$s->setBody(new http\Message\Body(fopen(basename($u->path), "r")));
	$s->send();
	exit;
default:
	ob_start($s);
	index(".".$u->path);
	ob_end_flush();
	$s->send();
	break;
}

?>
