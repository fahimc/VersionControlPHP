<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>FC-VCS</title>
		<meta name="author" content="" />
		<script type="text/javascript" src="lib/com/fahimchowdhury/toolkitMax-v1014-compressed.js"></script>
		<script type="text/javascript" src="src/main.js"></script>
		<style>
		@import url(http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300);
			* {
				padding: 0;
				margin: 0;
			}
			html, body {
				width: 100%;
				height: 100%;
			}
			body {
				background-color: #333;
				color: #FFF;
				font-family: 'Open Sans Condensed', sans-serif;
				font-size: 14px;
				overflow: auto;
			}
			#console {
				width: 90%;
				height: 90%;
				margin-left:5%;
				margin-right:5%;
				background-color: #333;
				color: #FFF;
				border: 0;
				font-family: 'Open Sans Condensed', sans-serif;
				font-size: 14px;
			}
			textarea:focus, input:focus {
				outline: 0;
			}
			*:focus {
				outline: 0;
			}
			iframe
			{
				/*display: none;*/
			}
			#staticConsole
			{
				width: 90%;
				margin-left:5%;
				margin-right:5%;
				margin-top:5%;
			}
		</style>
	</head>
	<body>
		<div id="staticConsole"></div>
		<textarea id="console"></textarea>
		<form id="dataForm" method="post" enctype="multipart/form-data" action="console.php" target="php">
			<input type="hidden" name="user" id="user" value="" />
			<input type="hidden" name="pass" id="pass" value="" />
			<input type="hidden" name="command" id="command" value="" />
			<input type="hidden" name="msg" id="msg" value="" />

		</form>
		<iframe id="php" name="php"></iframe>
	</body>
</html>
