<?php
set_time_limit(0);
Class Deploy {
	const DEPLOY_DIR = "deploy";
	private $ignore_folders = array("deploy", "jasmine", "jasmine-1.3.1");
	private $ignore_files = array("README.md","deploy.php","setup.php","deployclean.php");
	public function init() {
		$src = getcwd();
		$dst = getcwd() . "/" . Deploy::DEPLOY_DIR . "/";
		$this -> recurse_copy($src, $dst);
		$this->createTextFile();
	}

	private function recurse_copy($src, $dst) {

		$dir = opendir($src);
		@mkdir($dst);
		echo "<p class='" . ("folder") . "'>" . ($dst) . "</p>";
		while (false !== ($file = readdir($dir))) {
			$exclude = false;
			for ($a = 0; $a < count($this -> ignore_folders); $a++) {

				if ($file == $this -> ignore_folders[$a])
					$exclude = true;
			}

			if (!$exclude && ($file != '.') && ($file != '..')) {
				if (is_dir($src . '/' . $file)) {
					$this -> recurse_copy($src . '/' . $file, $dst . '/' . $file);
				} else {
					$exclude = false;
					for ($a = 0; $a < count($this -> ignore_files); $a++) {
							
						if ($file == $this -> ignore_files[$a])
							$exclude = true;
					}
					if(!$exclude){
						copy($src . '/' . $file, $dst . '/' . $file);
					echo "<p class='" . ("file") . "'>" . ($dst . '/' . $file) . "</p>";
					}
				}
			}
		}
		closedir($dir);
	}
	private function createTextFile()
	{
		$file = getcwd() . "/" . Deploy::DEPLOY_DIR . "/".'deployment_details.txt';
		$date = new DateTime('now');
		$content = "Deployment created on ".$date->format('d-m-Y H:i:s');
		file_put_contents($file, $content);
	}

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>AutoProjectSetup</title>
<meta name="author" content="" />

<style>
	* {
		padding: 0;
		margin: 0;
	}
	html, body {
		width: 100%;
		height: 100%;
		font-family: "Century Gothic";
		background-color: #f9f9f9;
		overflow: auto;
	}
	.wrapper {
		width: 800px;
		margin-left: auto;
		margin-right: auto;
		background-color: #fff;
		border: 1px solid #e9e9e9;
		margin-top: 50px;
		padding: 20px;
	}
	.header {
		width: 100%;
		padding-top: 20px;
		padding-bottom: 20px;
	}
	.header h1 {
		color: #404040;
		width: 100%;
		text-align: center;
		font-weight: normal;
		font-size: 50px;
	}
	.header h1 .blue {
		color: #6DBDD6;
		font-size: 60px;
	}
	h2 {
		color: #666666;
		margin-bottom: 20px;
		width: 100%;
		border-bottom: 1px solid #e9e9e9;
	}
	.list {
		width: 100%;
		border-bottom: 1px solid #e9e9e9;
		padding-bottom: 20px;
		margin-bottom: 20px;
	}
	.msg {
		color: #B71427;
	}
	.folder {
		color: #558C89;
	}
	.file {
		color: #D9853B;
	}
	.footer {
		width: 800px;
		margin-left: auto;
		margin-right: auto;
		padding-top: 20px;
		padding-bottom: 50px;
	}
	.footer p {
		font-size: 11px;
		color: #666666;
	}
	.footer a {
		text-decoration: none;
		color: #666666;
	}
	.footer a:hover {
		color: #000;
	}

</style>
</head>

<body>
<div class="wrapper">
<div class="header">
<h1>AUTO<span class="blue">PROJECT</span>SETUP</h1>
</div>
<h2>List of Completed Tasks for Deployment</h2>
<div class="list">
<?php

$deploy = new Deploy();
$deploy -> init();
?>
</div>
<p class="msg">
<!-- Please Delete 'setup.php' -->
</p>
</div>
<?php
/*
 $data=file_get_contents("jasmine/jasmine.js");
 $data = str_replace(array("\n"),"|_|",$data);
 $autoProjectSetup->makeFile("temp.js",$data);
 */
?>
<div class="footer">
<p>
github: <a href="https://github.com/fahimc/AutoProjectSetup" >https://github.com/fahimc/AutoProjectSetup</a>
</p>
</div>
</body>
</html>
