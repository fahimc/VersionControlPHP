<?php
include_once 'lib/com/fahimchowdhury/server/FTPUploader.php';
class VCS {
	const SETTINGS_URL = "../.repo/settings.xml";
	const COMMITS_URL = "../.repo/commits/";
	const TAGS_URL = "../.repo/tags/";
	const README_URL = "../README.md";
	const MASTER_TREE = "master";
	const ROOT = "../";
	public $settingsXML;
	public $ignore_folders = array("vcs", ".repo");
	public $ignore_files;
	public function init() {
		$this -> createFolder("../.repo");
		$this -> createFolder("../.repo/commits");
		$this -> createFolder("../.repo/tags");
		$this -> createSettingsFile();
		$this -> createReadme();
	}

	private function createFolder($val) {
		if (!file_exists($val))
			mkdir($val);
	}

	private function createSettingsFile() {
		$this -> settingsXML = new DOMDocument();
		if (file_exists(VCS::SETTINGS_URL)) {
			$this -> settingsXML -> load(VCS::SETTINGS_URL);
		} else {
			$settings = $this -> settingsXML -> createElement("settings");
			$tree = $this -> settingsXML -> createElement("tree");
			$remote = $this -> settingsXML -> createElement("remote");

			//create tree name
			$att = $this -> settingsXML -> createAttribute("name");
			$att -> value = VCS::MASTER_TREE;
			$tree -> appendChild($att);
			//set timestamp
			$date = new DateTime();

			$att = $this -> settingsXML -> createAttribute("updated");
			$att -> value = $date -> getTimestamp();
			$tree -> appendChild($att);

			$commit = $this -> settingsXML -> createElement("commits");
			$tags = $this -> settingsXML -> createElement("tags");
			$tree -> appendChild($remote);
			$tree -> appendChild($commit);
			$tree -> appendChild($tags);
			$settings -> appendChild($tree);
			$this -> settingsXML -> appendChild($settings);
			$this -> settingsXML -> save(VCS::SETTINGS_URL);
		}

	}

	private function createReadme() {
		if (!file_exists(VCS::README_URL))
			file_put_contents(VCS::README_URL, "");
	}

	public function remote($url, $user, $pass) {
		$remoteNode = $this -> settingsXML -> getElementsByTagName("remote") -> item(0);
		$att = $this -> settingsXML -> createAttribute("url");
		$att -> value = $url;
		$remoteNode -> appendChild($att);
		
		$useratt = $this -> settingsXML -> createAttribute("user");
		$useratt -> value = $user;
		$remoteNode -> appendChild($useratt);
		
		$passatt = $this -> settingsXML -> createAttribute("pass");
		$passatt -> value = $pass;
		$remoteNode -> appendChild($passatt);
		
		$this -> settingsXML -> save(VCS::SETTINGS_URL);
	}

	public function commit($msg) {
		$commitNode = $this -> settingsXML -> getElementsByTagName("commits") -> item(0);
		//create a new commit item
		$newCommit = $this -> settingsXML -> createElement("commit");
		$date = new DateTime();
		$att = $this -> settingsXML -> createAttribute("timestamp");
		$att -> value = $date -> getTimestamp();
		$newCommit -> appendChild($att);

		$commitNode -> appendChild($newCommit);

		$msgNode = $this -> settingsXML -> createElement("message");
		$msgNode -> nodeValue = $msg;

		$newCommit -> appendChild($msgNode);
		$this -> addNewCommitList($newCommit);

		$this -> checkLastCommitForChanges($newCommit);

		$this -> settingsXML -> save(VCS::SETTINGS_URL);
	}

	public function addNewCommitList($node) {
		$timestamp = $node -> getAttribute("timestamp");
		$folderName = VCS::COMMITS_URL . "commit-" . $timestamp;
		$this -> recurse_copy(VCS::ROOT, $folderName, $node);
	}

	public function checkLastCommitForChanges($node) {
		$changedList = array();
		$commitsNode = $this -> settingsXML -> getElementsByTagName("commits") -> item(0);
		$commits = $commitsNode -> getElementsByTagName("commit");

		$index = 0;
		$updated = false;
		foreach ($commits as $commit) {
			if ($index == $commits -> length - 1) {
				$files = $commit -> getElementsByTagName("file");
				//loop files
				foreach ($files as $file) {
					$mtime = $file -> getAttribute("filemtime");
					$name = $file -> getAttribute("name");
					$updated = $this -> checkIfFileIsModified($name, $mtime);
					if ($updated)
						array_push($changedList, $name);
				}

			}
			$index++;
		}
		//store modified files
		$listString = "";
		for ($a = 0; $a < count($changedList); $a++) {
			$listString .= ($a > 0 ? "," : "") . $changedList[$a];
		}

		$modifiedNode = $this -> settingsXML -> createElement("modified");
		$modifiedNode -> nodeValue = $listString;
		$node -> appendChild($modifiedNode);
	}

	private function checkIfFileIsModified($name, $mtime) {
		$commitsNode = $this -> settingsXML -> getElementsByTagName("commits") -> item(0);
		$commits = $commitsNode -> getElementsByTagName("commit");
		$index = 0;
		if ($commits -> length > 1) {
			foreach ($commits as $commit) {
				if ($index == $commits -> length - 2) {
					$files = $commit -> getElementsByTagName("file");
					//loop files
					foreach ($files as $file) {
						$files = $commit -> getElementsByTagName("file");
						$fname = $file -> getAttribute("name");
						$fmtime = $file -> getAttribute("filemtime");
						if ($fname == $name && $fmtime == $mtime)
							return false;
					}
				}
				$index++;
			}
		}
		return true;
	}

	public function tag($name, $msg) {
		$tagsNode = $this -> settingsXML -> getElementsByTagName("tags") -> item(0);
		//create a new commit item
		$newTag = $this -> settingsXML -> createElement("tag");
		$date = new DateTime();
		$att = $this -> settingsXML -> createAttribute("timestamp");
		$att -> value = $date -> getTimestamp();
		$newTag -> appendChild($att);

		$att = $this -> settingsXML -> createAttribute("name");
		$att -> value = $name;
		$newTag -> appendChild($att);

		$msgNode = $this -> settingsXML -> createElement("message");
		$msgNode -> nodeValue = $msg;

		$newTag -> appendChild($msgNode);

		$tagsNode -> appendChild($newTag);
		$this -> addNewTag($newTag);
		$this -> settingsXML -> save(VCS::SETTINGS_URL);
	}

	public function addNewTag($node) {
		$timestamp = $node -> getAttribute("timestamp");
		$folderName = VCS::TAGS_URL . "tag-" . $timestamp;
		$this -> recurse_copy(VCS::ROOT, $folderName, null);
	}

	private function recurse_copy($src, $dst, $node) {

		$dir = opendir($src);
		@mkdir($dst);
		// echo "<p class='" . ("folder") . "'>" . ($dst) . "</p>";
		while (false !== ($file = readdir($dir))) {
			$exclude = false;
			for ($a = 0; $a < count($this -> ignore_folders); $a++) {

				if ($file == $this -> ignore_folders[$a])
					$exclude = true;
			}

			if (!$exclude && ($file != '.') && ($file != '..')) {
				if (is_dir($src . '/' . $file)) {
					$this -> recurse_copy($src . '/' . $file, $dst . '/' . $file, $node);
				} else {
					$exclude = false;
					for ($a = 0; $a < count($this -> ignore_files); $a++) {

						if ($file == $this -> ignore_files[$a])
							$exclude = true;
					}
					if (!$exclude) {
						copy($src . '/' . $file, $dst . '/' . $file);

						if ($node) {
							//create a node with modified date
							$fileNode = $this -> settingsXML -> createElement("file");
							$att = $this -> settingsXML -> createAttribute("filemtime");
							$att -> value = filemtime($src . '/' . $file);
							$fileNode -> appendChild($att);

							//add name
							$att = $this -> settingsXML -> createAttribute("name");
							$att -> value = $src . '/' . $file;
							$fileNode -> appendChild($att);

							$node -> appendChild($fileNode);
						}

						// echo "<p class='" . ("file") . "'>" . ($dst . '/' . $file) . "</p>";
					}
				}
			}
		}
		closedir($dir);
	}

}
?>
