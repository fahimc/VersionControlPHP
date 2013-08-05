<?php
include_once 'vcs.php';
Class Response {
	const GRANTED = 1;
	const FAILED = -1;
	const COMMITTED = 2;
	const INIT_COMPLETE = 3;
	const REMOTE_ADDED = 4;
	const REMOTE_FAILED = -2;
}

Class RECEIVED {
	const LOGIN = 1;
	const COMMIT = 2;
	const INIT = 3;
	const REMOTE = 4;
}

Class Responder {
	const ADMIN_FILE = "admin.txt";
	private $user = "";
	private $pass = "";
	private $adminuser = "";
	private $adminpass = "";
	public function init() {
		if (isset($_POST['user']) && isset($_POST['pass']) && isset($_POST['command'])) {

			$this -> user = stripcslashes($_POST['user']);
			$this -> pass = stripcslashes($_POST['pass']);
			$command = stripcslashes($_POST['command']);
			$adminData = file_get_contents(Responder::ADMIN_FILE);
			$arr = preg_split("/\r\n|\n|\r/", $adminData);
			$this -> adminuser = trim($arr[0]);
			$this -> adminpass = trim($arr[1]);

			switch($command) {
				case RECEIVED::LOGIN :
					$this -> checkLogin();
					break;
				case RECEIVED::COMMIT :
					$this -> commit();
					break;
				case RECEIVED::INIT :
					$this -> VCSinit();
					break;
				case RECEIVED::REMOTE :
					
					$this -> remote();
					break;
			}

		} else {
			$command = stripcslashes($_POST['command']);
		
			if ($command == RECEIVED::INIT)
				$this -> VCSinit();
		}
	}

	private function checkLogin() {
		if ($this -> user == $this -> adminuser && $this -> pass == $this -> adminpass) {
			$this -> send(Response::GRANTED);
		} else {
			$this -> send(Response::FAILED);
		}
	}

	private function remote() {
		$u = stripcslashes($_POST['r_user']);
		$p = stripcslashes($_POST['r_pass']);
		$url = stripcslashes($_POST['r_url']);

		if (isset($u) && isset($p) && isset($url)) {
			$vcs = new VCS();
			$vcs -> init();
			$vcs -> remote($url, $u, $p);
			$this -> send(Response::REMOTE_ADDED);
		} else {
			$this -> send(Response::REMOTE_FAILED);
		}
	}

	private function VCSinit() {
		$vcs = new VCS();
		$vcs -> init();
		$this -> send(Response::INIT_COMPLETE);
	}

	private function commit() {
		$msg = stripcslashes($_POST['msg']);
		$vcs = new VCS();
		$vcs -> init();
		$vcs -> commit($msg);
		$this -> send(Response::COMMITTED);
	}

	public function send($msg) {
		echo '<script type="text/javascript">', 'parent.onComplete("' . $msg . '");', '</script>';
	}

}

$responder = new Responder();
$responder -> init();
?>