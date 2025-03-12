#!/usr/bin/env php
<?php
/*	sqm_read
	(c) 2025 Darren Creutz
	Licensed under the GNU AFFERO GENERAL PUBLIC LICENSE v3 */

/*	builds the distribution files */

if (!file_exists('dist')) {
	mkdir('dist');
}

function add_copyright($file_text) {
	return "/*" . PHP_EOL . "sqm_read" . PHP_EOL . "(c) 2025 Darren Creutz" . PHP_EOL . "Licensed under the GNU AFFERO GENERAL PUBLIC LICENSE (reproduced below)" . PHP_EOL . "*/" . PHP_EOL . $file_text;
}

$license_text = file_get_contents('LICENSE');

function add_license($file_text,$and_copyright = false) {
	global $license_text;
	if (!str_contains($file_text,$license_text)) {
		if ($and_copyright) {
			$file_text = add_copyright($file_text);
		}
		$file_text .= "/*" . PHP_EOL . $license_text . PHP_EOL . "*/" . PHP_EOL;
	}
	return $file_text;
}

$config_php = file_get_contents('config.php');
file_put_contents('dist/config.php',$config_php);

function perform_includes_and_requires($php,&$required_once) {
	$result = array();
	foreach ($php as $orig_line) {
		$line = trim($orig_line);
		if (str_starts_with($line,"include('") || str_starts_with($line,"require('")) {
			$filename = substr($line,9);
			$pos = strpos($filename,"');");
			if ($pos !== false) {
				$filename = substr($filename,0,$pos);
				if ($filename != "..' . DIRECTORY_SEPARATOR . 'config.php") {
					$result = array_merge($result,
						perform_includes_and_requires(file($filename),$required_once)
					);
				} else {
					array_push($result,"@include('config.php');" . PHP_EOL);
				}
			} else {
				array_push($result,$orig_line);
			}
		} elseif (str_starts_with($line,"require_once('")) {
			$filename = substr($line,14);
			$pos = strpos($filename,"');");
			if ($pos !== false) {
				$filename = substr($filename,0,$pos);
				if (!in_array($filename,$required_once)) {
					array_push($required_once,$filename);
					$result = array_merge($result,
						perform_includes_and_requires(file($filename),$required_once)
					);
				}
			} else {
				array_push($result,$orig_line);
			}		
		} else {
			array_push($result,$orig_line);
		}
	}
	return $result;
}

function strip_openclose($php) {
	$remove = [ 'T_OPEN_TAG', 'T_CLOSE_TAG' ];
	$result = "";
	foreach (token_get_all($php) as $token) {
		if (is_array($token)) {
			if (!in_array(token_name($token[0]),$remove)) {
				$result .= $token[1];
			}
		} else {
			$result .= $token;
		}
	}
	return $result;
}

function strip_comments_and_openclose($php) {
	$remove = [ 'T_COMMENT', 'T_OPEN_TAG', 'T_CLOSE_TAG' ];
	$result = "";
	foreach (token_get_all($php) as $token) {
		if (is_array($token)) {
			if (!in_array(token_name($token[0]),$remove)) {
				$result .= $token[1];
			}
		} else {
			$result .= $token;
		}
	}
	return $result;
}

// perform all includes and requires and require_onces
function perform($file) {
	$php = file($file);
	$required_once = array();
	$php = perform_includes_and_requires($php,$required_once);
	$php = implode("",$php);
	return add_license($php);
}

// make a browser callable php file
function make_php($file) {
	return "<?php" . PHP_EOL . perform($file) . PHP_EOL . "?>" . PHP_EOL;
}

// make a command line executable
function make_cli($file) {
	return  "#!/usr/bin/env php" . PHP_EOL . 
			'<?php' . PHP_EOL .
			perform($file) . PHP_EOL .
			'?>' . PHP_EOL;
}

// strip comments and php tags from source files
mkdir('build');
mkdir('build/src');
foreach (scandir('src') as $file) {
	if ($file[0] != '.') {
		if (!in_array($file,
				['sqm_read.php','sqm_read_to_file.php'])) {
			file_put_contents('build/src/'.$file,
				strip_comments_and_openclose(file_get_contents('src/'.$file)));
		} else {
			file_put_contents('build/src/'.$file,strip_openclose(file_get_contents('src/'.$file)));
		}
	}
}

chdir('build/src');
$read = make_cli('sqm_read.php');
$read_to_file = make_cli('sqm_read_to_file.php');
chdir('../..');

file_put_contents('dist/sqm_read.php',$read);
file_put_contents('dist/sqm_read_to_file.php',$read_to_file);

foreach (scandir('src') as $file) {
	if ($file[0] != '.') {
		unlink('build/src/'.$file);
	}
}
rmdir('build/src');
rmdir('build');
?>