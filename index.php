<?php

error_reporting(E_ALL &~ E_DEPRECATED);

define("OUTPUT", fopen("php://memory", "w+"));

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
	return (strlen($dir) ? $dir . "/" : "") . urlencode($file);
}

function ls($dir, $invert = false) {
	fprintf(OUTPUT, "<ul>\n");
	foreach (scandir($dir) as $file) {
		$dir = trim($dir, "./");
		$html = "";
		if ($file === ".") {
			continue;
		} elseif ($file === "..") {
			if ($dir === "" || $invert) {
				continue;
			}
			$name = sprintf("namespace %s", ns(dirname($dir)));
		} elseif (!$invert && is_dir("./$dir/$file")) {
			$name = sprintf("namespace %s", ns("./$dir/$file"));
		} elseif (!$invert && ctype_upper($file{0})) {
			$name = join(" ", cut(head("./$dir/$file"), ["f"=>"1-2"]));
		} elseif (!$invert || ctype_upper($file{0})) {
			continue;
		} else {
			$name = ns($dir)."::".basename($file, ".md");
			$html = "<p>".join(" ", cut(head("./$dir/$file"), ["f"=>"1-"]))."</p>";
		}

		fprintf(OUTPUT, "<li><a href=\"/%s\">%s</a>%s</li>\n",
			urlpath($dir, $file),
			htmlspecialchars($name),
			$html);
	}
	fprintf(OUTPUT, "</ul>\n");
}

function ml($file) {
	$pi = pathinfo($file);
	if (ctype_upper($pi["filename"][0])) {
		fprintf(OUTPUT, "<h2>Methods:</h2>\n");
		$el = $pi["dirname"] . "/" . $pi["filename"];
		ls($el, true);
	}
}

function md($file) {
	$r = fopen($file, "r");
	$md = MarkdownDocument::createFromStream($r);
	$md->compile();
	$md->writeHtml(OUTPUT);
	unset($md);
	fclose($r);

	// BS Markdown seeks around...
	fseek(OUTPUT, 0, SEEK_END);
	
	ml($file);
}

$r = new http\Env\Request;
$u = new http\Url($r->getRequestUrl());
$t = ["css"=>"text/css", "js"=>"application/javascript"];

switch($u->path) {
case "/index.js":
case "/index.css":
	$s = new http\Env\Response;
	$s->setHeader("Content-type", $t[pathinfo($u->path, PATHINFO_EXTENSION)]);
	$s->setBody(new http\Message\Body(fopen(basename($u->path), "r")));
	$s->send();
	exit;
}

if (is_dir(".".$u->path)) {
	ls(".".$u->path);
} else {
	md(".".$u->path);
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?=$u->path?></title>
<link rel="stylesheet" href="/index.css">
</head>
<body>
<?php
rewind(OUTPUT);
fpassthru(OUTPUT);
fclose(OUTPUT);
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script> 
<script src="/index.js"></script>
</body>
</html>
