(function(window) {
	var commands = {
		user : "username: ",
		pass : "pass: ",
		commit : "commit",
		login : "login",
		init : "init"
	}
	var response = {
		granted : {
			index : 1,
			msg : "welcome "
		},
		failed : {
			index : -1,
			msg : "incorrect details "
		},
		commit:
		{
			index : 2,
			missing:"you need to provide -m for a message",
			msg:"commit complete"
		},
		init:
		{
			index : 3,
			msg:"init complete"
		}
	}
	var phpCommands = {
		login : 1,
		commit : 2,
		init : 3
	}
	var user = "";
	var pass = "";
	var granted = false;
	function Main() {
		if (window.addEventListener) {
			window.addEventListener("load", onLoad);
		} else {
			window.attachEvent("onload", onLoad);
		}

	}

	function onLoad() {
		Utensil.addListener(document.getElementById("console"), "keyup", onKeyPress);

		
	}


	window.onComplete = function(msg) {
		console.log("onComplete",msg);
		var res = "";
		switch(Number(msg)) {
			case response.granted.index:
				granted = true;
				res = response.granted.msg + user;
				break;
			case response.failed.index:
				granted = false;
				res = response.failed.msg;
				document.getElementById("console").value = commands.user;
				break;
			case response.commit.index:
				res = response.commit.msg;
				break;
			case response.init.index:
				res = response.init.msg;
				break;
		}
		
		updateStatic(res);
	}
	function onKeyPress(event) {
		if (event.keyCode == 13) {

			var send = checkCommand();
			if (send)
				document.getElementById("dataForm").submit();

		}
	}

	function checkCommand() {
		
		var text = document.getElementById("console").value;
		var arr = text.split("\n");
		var line =arr[arr.length - 2];
		
		if(line.indexOf(commands.login) >= 0) {
			updateStatic(document.getElementById("console").value);
			document.getElementById("console").value = commands.user;
			return false;
		}
		
		if(line.indexOf(commands.init) >= 0) {
			updateStatic(document.getElementById("console").value);
			document.getElementById("console").value = "";
			document.getElementById("command").value = phpCommands.init;
			return true;
		}
		//if user
		if (arr[arr.length - 2].indexOf(commands.user) >= 0) {
			user = arr[arr.length - 2].replace(commands.user, "");
			updateStatic(document.getElementById("console").value);
			document.getElementById("console").value = commands.pass;
			document.getElementById("user").value = user;
			return false;
		}
		//if pass
		if (arr[arr.length - 2].indexOf(commands.pass) >= 0) {
			pass = arr[arr.length - 2].replace(commands.pass, "");
			updateStatic(document.getElementById("console").value);
			document.getElementById("console").value = "";
			document.getElementById("command").value = phpCommands.login;
			document.getElementById("pass").value = pass;
			return true;
		}
		
		if(line.indexOf(commands.commit) >= 0) {
			if(line.indexOf("-m") >= 0)
			{
				var msg = line.split("-m ");
				msg=msg[1]?msg[1]:"";
				updateStatic(document.getElementById("console").value);
			document.getElementById("console").value = "";
				document.getElementById("command").value = phpCommands.commit;
				document.getElementById("msg").value = msg;
				return true;
			}else{
				updateStatic(document.getElementById("console").value);
				document.getElementById("console").value = response.commit.missing+"\n";
			}
			
		}
	}

	function updateStatic(msg) {
		document.getElementById("staticConsole").innerHTML += msg + "<br>";
		
	}

	Main();
}
)(window);
