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

				Class Deploy {
					const JS_LOCATION = "deploy/js/";
					const CSS_LOCATION = "deploy/css/";
					const IMAGE_LOCATION = "deploy/images/";
					private $htmlList = array();

					public function init() {
						$this -> makeFolder(Deploy::JS_LOCATION);
						$this -> makeFolder(Deploy::CSS_LOCATION);
						$this -> makeFolder(Deploy::IMAGE_LOCATION);

						$this -> getAllHTMLFiles();
						$this -> getAllScripts();

					}

					private function getAllHTMLFiles() {
						foreach (glob("*.html") as $filename) {
							array_push($this -> htmlList, $filename);
						}
					}

					private function getAllScripts() {
						for ($a = 0; $a < count($this -> htmlList); $a++) {
							$link = $this -> htmlList[$a];
							$content = file_get_contents($link);
							$content = $this -> getScriptSrc($content);
							$content = $this -> getLinkSrc($content);
							$content = $this -> getImageSrc($content);
							$this -> makeFile("deploy/" . $this -> htmlList[$a], $content);
						}
					}

					private function getScriptSrc($html) {

						$pattern = '/src=(["\'])(.*?)\1/';
						preg_match_all($pattern, $html, $matches);
						for ($a = 0; $a < count($matches[0]); $a++) {
							$name = preg_replace("/src=([\"'])/", "", $matches[0][$a]);
							$name = preg_replace("/([\"'])/", "", $name);

							if ($name && strrpos($name, ".js") == true) {
								$data = file_get_contents($name);
								$filename = explode("/", $name);
								$html = str_replace($name, "js/" . $filename[count($filename) - 1], $html);
								$this -> putJS(Deploy::JS_LOCATION . $filename[count($filename) - 1], $data);
							}

						}
						return $html;
					}

					private function getLinkSrc($html) {

						$pattern = '/href=(["\'])(.*?)\1/';
						preg_match_all($pattern, $html, $matches);
						for ($a = 0; $a < count($matches[0]); $a++) {
							$name = preg_replace("/href=([\"'])/", "", $matches[0][$a]);
							$name = preg_replace("/([\"'])/", "", $name);

							if ($name && strrpos($name, ".css") == true) {
								$data = file_get_contents($name);
								$filename = explode("/", $name);
								$html = str_replace($name, "css/" . $filename[count($filename) - 1], $html);
								$data = $this -> changeCSSPaths($data, $name);
								$this -> makeFile(Deploy::CSS_LOCATION . $filename[count($filename) - 1], $data);
							}

						}
						return $html;
					}

					private function changeCSSPaths($data, $cssPath) {
						$pattern = '#url\((([^()]+|(?R))*)\)#';
						preg_match_all($pattern, $data, $matches);
						//var_dump($matches);
						for ($a = 0; $a < count($matches[0]); $a++) {

							$name = preg_replace("/([\"'])/", "", $matches[0][$a]);
							$name = str_replace("url(", "", $name);
							$name = str_replace(")", "", $name);
							$filename = explode("/", $name);
							$data = str_replace($name, "../images/" . $filename[count($filename) - 1], $data);
							//get image path
							$root = explode("/", $cssPath);
							$rootPath="";
							for($b=0;$b<count($root)-2;$b++)
							{
								$rootPath .=$root[$b]."/";
							}
							$rootPath = $rootPath.(str_replace("../", "", $name));
							if (!copy($rootPath, Deploy::IMAGE_LOCATION . $filename[count($filename) - 1])) {
									echo "failed to copy $file...\n";
								}
						}
						return $data;
					}

					private function getImageSrc($html) {

						$pattern = '/src=(["\'])(.*?)\1/';
						preg_match_all($pattern, $html, $matches);
						for ($a = 0; $a < count($matches[0]); $a++) {
							$name = preg_replace("/src=([\"'])/", "", $matches[0][$a]);
							$name = preg_replace("/([\"'])/", "", $name);

							if ($name && strrpos($name, ".js") == false) {
								$data = file_get_contents($name);
								$filename = explode("/", $name);
								$html = str_replace($name, "images/" . $filename[count($filename) - 1], $html);
								if (!copy($name, Deploy::IMAGE_LOCATION . $filename[count($filename) - 1])) {
									echo "failed to copy $file...\n";
								}

							}

						}
						return $html;
					}

					private function putJS($url, $data) {
						$this -> makeFile($url, $data);
					}

					private function makeFolder($path) {
						if (!file_exists($path)) {
							mkdir($path, 0777, true);
						}
						$this -> displayMessage("Created folder called " . $path, "folder");
					}

					private function displayMessage($str, $className) {
						echo "<p class='" . ($className ? $className : "") . "'>" . $str . "</p>";
					}

					private function makeFile($url, $data) {
						//if (!file_exists($url))
						file_put_contents($url, $data);
						$this -> displayMessage("Created file called " . $url, "file");
					}

				

				}

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
