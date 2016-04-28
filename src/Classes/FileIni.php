<?php

namespace DodoIniManager\Classes;

/**
* General class for DodoIniManager. Provides all the methods to :
* <ul>
*	<li> Create and modify file.ini </li>
*	<li> Create and modify sections </li>
*	<li> Create and modify section's keys and their values </li>
* </ul>
*
* @author Dimitri Lahaye <contact@dimitrilahaye.net>
* @license http://www.dbad-license.org/ DBAD Public License
* @version 0.1.3-beta
*
* @package DodoIniManager
* @subpackage Classes
*/
class FileIni {

	/**
	* @internal
	*
	* Path for this file.
	*
	* @var string
	*/
	private $path;

	/**
	* @internal
	*
	* Constant for DIRECTORY_SEPARATOR.
	*
	* @var string
	*/
	const DS = DIRECTORY_SEPARATOR;

	/**
	* Method constructor. When a new FileIni object is instantiated, if file doesn't exists, we create it.
	* @see DodoIniManager\Classes\FileIni::createFile()	For the creation of the file.ini.
	*
	* @param string $path File's path for this FileIni object.
	*
	* @return void
	*/
	public function __construct($path){
		$this->path = $path;
		if(!file_exists($path)){
			$this->createFile($path);
		}
	}

	/**
	* Get the path for this FileIni object.
	*
	* @return string Returns the file's path for this FileIni object.
	*/
	public function getPath(){
		return $this->path;
	}

	#############################################################################
	################ FILE #######################################################
	#############################################################################

	/**
	* Change the file's name.
	*
	* @param string $name New name for this file.
	*
	* @return void
	*/
	public function rename($name){
		$oldPath = $this->path;
		$path_array = explode(self::DS, $oldPath);
		array_pop($path_array);
		$_newPath = implode(self::DS, $path_array);
		$newPath = $_newPath . self::DS . $name;
		$this->path = $newPath;
		rename($oldPath, $newPath);
	}

	/**
	* Create a copy of the file with another name, at the same location in file system.
	*
	* @param string $name Name for the copied file.
	*
	* @return void
	*/
	public function copy($name){
		$path_array = explode(self::DS, $this->path);
		$oldPath = $this->path;
		$path_array = explode(self::DS, $oldPath);
		array_pop($path_array);
		$_newPath = implode(self::DS, $path_array);
		$newPath = $_newPath . self::DS . $name;
		copy($this->path, $newPath);
	}

	/**
	* Change location for this file. The original file is deleted.
	*
	* @param string $path The new path for this file.
	*
	* @return void
	*/
	public function move($path){
		$oldPath = $this->path;
		$path_array = explode(self::DS, $this->path);
		$fileName = $path_array[sizeof($path_array) - 1];
		$newPath = $path . self::DS . $fileName;
		$this->path = $newPath;
		$this->createFile($newPath);
		unlink($oldPath);
	}

	/**
	* Update entire file with an array
	*
	* @param mixed[] $array The array content to update the file.
	*
	* @return void
	*/
	public function arrayTo($array){
		$this->updateFile($array);
	}

	/**
	* Get the parsed content of this file.
	*
	* @return mixed[] The parsed content of this file obtained with parse_ini_file() method.
	*/
	public function toArray(){
		return parse_ini_file($this->path, true);
	}

	#############################################################################
	################ SECTIONS ###################################################
	#############################################################################

	/**
	* Get a section contained into this file.
	*
	* @example Assuming the file.ini content looks like that :
	*	<pre>
	*		[node]
	*		ide = "webstorm"
	*		framework = "express"
	*		orm = "mongoose"
	*	</pre>
	* This method will return this :
	* 	<pre>
	*		array("node" => array("ide" => "webstorm", "framework" => "express", "orm" => "mongoose"));
	* 	</pre>
	*
	* @param string $section The target section's key.
	*
	* @return mixed[] Returns the section from parsed file.
	*/
	public function get($section){
		$body = $this->toArray();
		return $body[$section];
	}

	/**
	* Add a new section to this file. You are able to add an array of sub-keys for this new section.
	*
	* @example FileIni::set() without string argument :
	*
	* 	<pre>
	* 		$fileIni = new FileIni("file.ini");
	* 		$fileIni->set("node");
	* 	</pre>
	* This snippet will update the file.ini like that :
	*	<pre>
	*		[node]
	*	</pre>
	*
	* FileIni::set() with mixed[] argument :
	*
	* 	<pre>
	* 		$fileIni = new FileIni("file.ini");
	* 		$fileIni->set("node", array("ide" => "webstorm", "framework" => "express", "orm" => "mongoose"));
	* 	</pre>
	* This snippet will update the file.ini like that :
	*	<pre>
	*		[node]
	*		ide = "webstorm"
	*		framework = "express"
	*		orm = "mongoose"
	*	</pre>
	*
	* @param string $section The section's key to add.
	* @param mixed[] $array An array of key => value in case we want to write element to this new section
	*
	* @return void
	*/
	public function set($section, $array = null){
		$body = $this->toArray();
		$body[$section] = array();
		if($array != null){
			$body[$section] = $array;
		}
		$this->updateFile($body);
	}

	/**
	* Modify the key of a section.
	*
	* @param string $section The section's key to modify.
	* @param string $newSection The new section's key.
	*
	* @return void
	*/
	public function rewrite($section, $newSection){
		$temp_section = $this->get($section);
		$this->rm($section);
		$this->set($newSection, $temp_section);
	}

	/**
	* Remove the section with the key passed in argument.
	*
	* @param string $section The section's key of the section to remove.
	*
	* @return void
	*/
	public function rm($section){
		$body = $this->toArray();
		unset($body[$section]);
		$this->updateFile($body);
	}

	/**
	* Move an entire section at the top of this file.ini
	*
	* @param string $section The section's key of the section to move.
	*
	* @return void
	*/
	public function prepend($section){
		$body = $this->toArray();
		$_section = $this->get($section);
		$temp_body = [];
		$i = 0;
		foreach ($body as $key => $value) {
			if($key != $section){
				if($i == 0){
					$temp_body[$section] = $_section;
					$temp_body[$key] = $value;
				} else {
					$temp_body[$key] = $value;
				}
			}
			$i++;
		}
		$this->updateFile($temp_body);
	}

	/**
	* Move an entire section at the bottom of this file.ini
	*
	* @param string $section The section's key of the section to move.
	*
	* @return void
	*/
	public function append($section){
		$body = $this->toArray();
		$_section = $this->get($section);
		$temp_body = [];
		$i = 0;
		$j = sizeof($body);
		foreach ($body as $key => $value) {
			if($key != $section){
				if($i == ($j - 1)){
					$temp_body[$key] = $value;
					$temp_body[$section] = $_section;
				} else {
					$temp_body[$key] = $value;
				}
			}
			$i++;
		}
		$this->updateFile($temp_body);
	}

	/**
	* Move an entire section before another one.
	*
	* @param string $section The section's key of the section to move.
	* @param string $before The section's key of the section before wich we want to move our section.
	*
	* @return void
	*/
	public function before($section, $before){
		$body = $this->toArray();
		$_section = $this->get($section);
		$_before = $this->get($before);
		$temp_body = [];
		foreach ($body as $key => $value) {
			if($key != $section){
				if($key != $before){
					$temp_body[$key] = $value;
				} else {
					unset($body[$before]);
					$temp_body[$section] = $_section;
					$temp_body[$before] = $_before;
				}
			} else {
				unset($body[$section]);
			}
		}
		$this->updateFile($temp_body);
	}

	/**
	* Move an entire section after another one.
	*
	* @param string $section The section's key of the section to move.
	* @param string $after The section's key of the section after wich we want to move our section.
	*
	* @return void
	*/
	public function after($section, $after){
		$body = $this->toArray();
		$_section = $this->get($section);
		$_after = $this->get($after);
		$temp_body = [];
		foreach ($body as $key => $value) {
			if($key != $after){
				if($key != $section){
					$temp_body[$key] = $value;
				} else {
					unset($body[$section]);
					$temp_body[$after] = $_after;
					$temp_body[$section] = $_section;
				}
			} else {
				unset($body[$after]);
			}
		}
		$this->updateFile($temp_body);
	}

	/**
	* Check if this section has another section after it.
	*
	* @param string $section The section's key we want to evaluate.
	*
	* @return boolean True if this section has another one after it, false if not.
	*/
	public function hasNext($section){
		$body = $this->toArray();
		$i = 0;
		foreach ($body as $key => $value) {
			if($key == $section){
				if((sizeof($body) - 1) > $i){
					return true;
				}
			}
			$i++;
		} return false;
	}

	/**
	* Check if this section has another section before it.
	*
	* @param string $section The section's key we want to evaluate.
	*
	* @return boolean True if this section has another one before it, false if not.
	*/
	public function hasBefore($section){
		$body = $this->toArray();
		$i = 0;
		foreach ($body as $key => $value) {
			if($key == $section){
				if($i == 0){
					return false;
				}
			}
			$i++;
		} return true;
	}

	/**
	* Get the next section after the section'key passed in argument.
	*
	* @param string $section The section's key that we want to return the following.
	*
	* @return mixed[] Returns the following section from parsed file.
	*/
	public function getNext($section){
		$body = $this->toArray();
		if(!$this->hasNext($section)){
			return false;
		}
		$index = $this->sectionIndex($section);
		$i = 0;
		foreach ($body as $key => $value) {
			if($i == $index){
				return $body[$key];
			}
			$i++;
		} return false;
	}

	/**
	* Get the next section before the section'key passed in argument.
	*
	* @param string $section The section's key that we want to return the previous one.
	*
	* @return mixed[] Returns the previous section from parsed file.
	*/
	public function getBefore($section){
		$body = $this->toArray();
		if(!$this->hasBefore($section)){
			return false;
		}
		$index = $this->sectionIndex($section);
		$i = 0;
		foreach ($body as $key => $value) {
			if(($i + 1) == $index){
				return $body[$key];
			}
			$i++;
		} return false;
	}

	#############################################################################
	################ ELEMENTS ###################################################
	#############################################################################

	/**
	* Get an element contained into a section of this file.
	*
	* @example Assuming the file.ini content looks like that :
	*	<pre>
	*		[node]
	*		ide = "webstorm"
	*		framework = "express"
	*		orm = "mongoose"
	*	</pre>
	* This method will return this :
	* 	<pre>
	*		$fileIni = new FileIni("file.ini");
	*		echo $fileIni->getKey("ide");
	*		// will output "webstorm"
	* 	</pre>
	*
	* @param string $section The section's key of the target element.
	* @param string $element The target element.
	*
	* @return string Returns the value of the target element
	*/
	public function getKey($section, $element){
		$body = $this->toArray();
		return $body[$section][$element];
	}

	/**
	* Add a new element into a section of this file. You are able to add an array of sub-keys in second 
	* argument in order to add many elements into the target section or just a string in order to add a simple 
	* sub-key.
	*
	* @example FileIni::set() string in second argument :
	*
	* 	<pre>
	* 		$fileIni = new FileIni("file.ini");
	* 		$fileIni->setKey("bash", "ide");
	* 	</pre>
	* This snippet will update the file.ini like that :
	*	<pre>
	*		[bash]
	*		ide =
	*	</pre>
	*
	* FileIni::set() with mixed[] in second argument :
	*
	* 	<pre>
	* 		$fileIni = new FileIni("file.ini");
	* 		$fileIni->setKey("bash", array("ide" => "terminal"));
	* 	</pre>
	* This snippet will update the file.ini like that :
	*	<pre>
	*		[bash]
	*		ide = "terminal"
	*	</pre>
	*
	* @param string $section The section's key of the target element.
	* @param mixed[] $element The target element.
	*
	* @return void
	*/
	public function setKey($section, $element){
		$body = $this->toArray();
		foreach ($body as $key => $value) {
			if($key == $section){
				if(is_string($element)){
					$body[$key][$element] = "";
				} else if(is_array($element)){
					foreach ($element as $k => $v) {
						$body[$key][$k] = $v;
					}
				}
			}
		}
		$this->updateFile($body);
	}

	/**
	* Add a value into the element of a section into this file.
	*
	* @example Assuming the file.ini content looks like that :
	*	<pre>
	*		[node]
	*		ide = "webstorm"
	*		framework = "express"
	*		orm = 
	*	</pre>
	* In php file :
	* 	<pre>
	*		$fileIni = new FileIni("file.ini");
	*		echo $fileIni->writeInKey("node", "orm", "mongoose");
	* 	</pre>
	* This snippet will update the file.ini like that :
	*	<pre>
	*		[node]
	*		ide = "webstorm"
	*		framework = "express"
	*		orm = "mongoose"
	*	</pre>
	*
	* @param string $section The section's key of the target element.
	* @param string $element The target element.
	* @param string $content The value to write in the element.
	*
	* @return void
	*/
	public function writeInKey($section, $element, $content){
		$body = $this->toArray();
		foreach ($body as $key => $value) {
			if($key == $section){
				foreach ($value as $k => $v) {
					if($k = $element){
						$body[$section][$element] = $content;
					}
				}
			}
		}
		$this->updateFile($body);
	}

	/**
	* Modify the element's key of a section into this file.
	*
	* @example Assuming the file.ini content looks like that :
	*	<pre>
	*		[node]
	*		ide = "webstorm"
	*		framework = "express"
	*		orm = "mongoose"
	*	</pre>
	* In php file :
	* 	<pre>
	*		$fileIni = new FileIni("file.ini");
	*		echo $fileIni->rewriteKey("node", "orm", "odm");
	* 	</pre>
	* This snippet will update the file.ini like that :
	*	<pre>
	*		[node]
	*		ide = "webstorm"
	*		framework = "express"
	*		odm = "mongoose"
	*	</pre>
	*
	* @param string $section The section's key of the target element.
	* @param string $element The target element.
	* @param string $newElement The new name for the target element.
	*
	* @return void
	*/
	public function rewriteKey($section, $element, $newElement){
		$temp_element = $this->getKey($section, $element);
		$this->rmKey($section, $element);
		$this->setKey($section, $newElement);
		$this->writeInKey($section, $newElement, $temp_element);
	}

	/**
	* Overwrite a value into the element of a section into this file.
	*
	* @example Assuming the file.ini content looks like that :
	*	<pre>
	*		[node]
	*		ide = "webstorm"
	*		framework = "express"
	*		orm = "mongoose"
	*	</pre>
	* In php file :
	* 	<pre>
	*		$fileIni = new FileIni("file.ini");
	*		echo $fileIni->rewriteInKey("node", "orm", "mongoengine");
	* 	</pre>
	* This snippet will update the file.ini like that :
	*	<pre>
	*		[node]
	*		ide = "webstorm"
	*		framework = "express"
	*		orm = "mongoengine"
	*	</pre>
	*
	* @param string $section The section's key of the target element.
	* @param string $element The target element.
	* @param string $content The value to write in the element.
	*
	* @return void
	*/
	public function rewriteInKey($section, $element, $content){
		$body = $this->toArray();
		foreach ($body as $key => $value) {
			if($key == $section){
				foreach ($value as $k => $v) {
					if($k = $element){
						$body[$section][$element] = $content;
					}
				}
			}
		}
		$this->updateFile($body);
	}

	/**
	* Remove the value into a target element in a section of this file.
	*
	* @example Assuming the file.ini content looks like that :
	*	<pre>
	*		[node]
	*		ide = "webstorm"
	*		framework = "express"
	*		orm = "mongoose"
	*	</pre>
	* In php file :
	* 	<pre>
	*		$fileIni = new FileIni("file.ini");
	*		echo $fileIni->rmInKey("node", "orm");
	* 	</pre>
	* This snippet will update the file.ini like that :
	*	<pre>
	*		[node]
	*		ide = "webstorm"
	*		framework = "express"
	*		orm = 
	*	</pre>
	*
	* @param string $section The section's key of the target element.
	* @param string $element The target element.
	*
	* @return void
	*/
	public function rmInKey($section, $element){
		$this->rewriteInKey($section, $element, "");
	}

	/**
	* Remove the entire element's key/value into a section of this file.
	*
	* @example Assuming the file.ini content looks like that :
	*	<pre>
	*		[node]
	*		ide = "webstorm"
	*		framework = "express"
	*		orm = "mongoose"
	*	</pre>
	* In php file :
	* 	<pre>
	*		$fileIni = new FileIni("file.ini");
	*		echo $fileIni->rmKey("node", "orm");
	* 	</pre>
	* This snippet will update the file.ini like that :
	*	<pre>
	*		[node]
	*		ide = "webstorm"
	*		framework = "express"
	*	</pre>
	*
	* @param string $section The section's key of the target element.
	* @param string $element The target element to remove.
	*
	* @return void
	*/
	public function rmKey($section, $element){
		$body = $this->toArray();
		unset($body[$section][$element]);
		$this->updateFile($body);
	}

	/**
	* Check if this section's element has another element after it.
	*
	* @param string $section The section's key of the target element.
	* @param string $element The target element to evaluate.
	*
	* @return boolean True if this element has another one after it, false if not.
	*/
	public function keyHasNext($section, $element){
		$body = $this->toArray();
		$i = 0;
		foreach ($body as $key => $value) {
			if($key == $section){
				foreach ($value as $k => $v) {
					if($k == $element){
						if((sizeof($value) - 1) > $i){
							return true;
						}
					}	
					$i++;
				}
			}
		} return false;
	}

	/**
	* Check if this section's element has another element before it.
	*
	* @param string $section The section's key of the target element.
	* @param string $element The target element to evaluate.
	*
	* @return boolean True if this element has another one before it, false if not.
	*/
	public function keyHasBefore($section, $element){
		$body = $this->toArray();
		$i = 0;
		foreach ($body as $key => $value) {
			if($key == $section){
				foreach ($value as $k => $v) {
					if($k == $element){
						if($i == 0){
							return false;
						}
					}	
					$i++;
				}
			}
		} return true;
	}

	/**
	* Get the next element after the element'key passed in argument.
	*
	* @param string $section The section's key of the target element.
	* @param string $element The section's element's key that we want to return the following.
	*
	* @return string Returns the following element from parsed file.
	*/
	public function getNextKey($section, $element){
		$body = $this->toArray();
		if(!$this->keyHasNext($section, $element)){
			return false;
		}
		$index = $this->elementIndex($section, $element);
		$i = 0;
		foreach ($body as $key => $value) {
			if($key == $section){
				foreach ($value as $k => $v) {
					if(($index + 1) == $i){
						return array($k => $v);
					}
					$i++;
				}
			}
		} return false;
	}

	/**
	* Get the element before the element'key passed in argument.
	*
	* @param string $section The section's key of the target element.
	* @param string $element The section's element's key that we want to return the previous one.
	*
	* @return string Returns the previous element from parsed file.
	*/
	public function getBeforeKey($section, $element){
		$body = $this->toArray();
		if(!$this->keyHasBefore($section, $element)){
			return false;
		}
		$index = $this->elementIndex($section, $element);
		$i = 0;
		foreach ($body as $key => $value) {
			if($key == $section){
				foreach ($value as $k => $v) {
					if(($i + 1) == $index){
						return array($k => $v);
					}
					$i++;
				}
			}
		} return false;
	}

	/**
	* Move the element into another section. The original element is deleted.
	*
	* @param string $section The section's key of the target element.
	* @param string $element The element's key to move.
	* @param string $newSection The section's key where to move the element.
	*
	* @return void
	*/
	public function moveKey($section, $element, $newSection){
		$_element = $this->getKey($section, $element);
		$this->rmKey($section, $element);
		$this->setKey($newSection, array($element => $_element));
	}

	/**
	* Move an entire element's key/value before another one.
	*
	* @param string $section The section's key of the element to move.
	* @param string $element The element's key of the element to move.
	* @param string $before The element's key before wich we want to move our element.
	*
	* @return void
	*/
	public function beforeKey($section, $element, $before){
		$body = $this->toArray();
		$_element = $this->getKey($section, $element);
		$_before = $this->getKey($section, $before);
		$_section = $this->get($section);
		$temp_section = [];
		$temp_body = [];
		foreach ($_section as $key => $value) {
			if($key != $element){
				if($key != $before){
					$temp_section[$key] = $value;
				} else {
					unset($_section[$before]);
					$temp_section[$element] = $_element;
					$temp_section[$before] = $_before;
				}
			} else {
				unset($_section[$element]);
			}
		}
		foreach ($body as $key => $value) {
			if($key == $section){
				$temp_body[$section] = $temp_section;
			} else {
				$temp_body[$key] = $value;
			}
		}
		$this->updateFile($temp_body);
	}

	/**
	* Move an entire element's key/value after another one.
	*
	* @param string $section The section's key of the element to move.
	* @param string $element The element's key of the element to move.
	* @param string $after The element's key after wich we want to move our element.
	*
	* @return void
	*/
	public function afterKey($section, $element, $after){
		$body = $this->toArray();
		$_element = $this->getKey($section, $element);
		$_after = $this->getKey($section, $after);
		$_section = $this->get($section);
		$temp_section = [];
		$temp_body = [];
		foreach ($_section as $key => $value) {
			if($key != $after){
				if($key != $element){
					$temp_section[$key] = $value;
				} else {
					unset($_section[$element]);
					$temp_section[$after] = $_after;
					$temp_section[$element] = $_element;
				}
			} else {
				unset($_section[$element]);
			}
		}
		foreach ($body as $key => $value) {
			if($key == $section){
				$temp_body[$section] = $temp_section;
			} else {
				$temp_body[$key] = $value;
			}
		}
		$this->updateFile($temp_body);
	}

	/**
	* Move an entire element at the top of its section.
	*
	* @param string $section The section's key of the section's element to move.
	* @param string $element The element's key to move.
	*
	* @return void
	*/
	public function prependKey($section, $element){
		$body = $this->toArray();
		$i = 0;
		foreach ($body as $key => $value) {
			if($key == $section){
				foreach ($value as $k => $v) {
					if($i == 0){
						$this->beforeKey($section, $element, $k);
					}
					$i++;
				}
			}
		}
	}

	/**
	* Move an entire element at the bottom of its section.
	*
	* @param string $section The section's key of the section's element to move.
	* @param string $element The element's key to move.
	*
	* @return void
	*/
	public function appendKey($section, $element){
		$body = $this->toArray();
		$i = 0;
		$j = sizeof($body);
		foreach ($body as $key => $value) {
			if($key == $section){
				foreach ($value as $k => $v) {
					if($i == ($j - 1)){
						$this->afterKey($section, $element, $k);
					}
					$i++;
				}
			}
		}
	}

	#############################################################################
	################ PRIVATE API ################################################
	#############################################################################

	/**
	* @internal
	*
	* Update this file.ini with a new body.
	*
	* @param mixed[] $body The parsed body to put into this file.
	*
	* @return void
	*/
	private function updateFile($body){
		$content = $this->createContentFromBody($body);
		$this->updateFileWithContent($content);
	}

	/**
	* @internal
	*
	* Update this file.ini with a new body.
	*
	* @param string $content The content buffer to put into this file.
	*
	* @return void
	*/
	private function updateFileWithContent($content){
		file_put_contents($this->path, "");
		file_put_contents($this->path, $content);
	}

	/**
	* @internal
	*
	* Get the index of the target section into the parsed file.ini. <br/>
	*
	* @param string $section The section's key to evaluate.
	*
	* @return int Index of the target section into the parsed file.ini. If this method doesn't find the index,
	* returns -1;
	*/
	private function sectionIndex($section){
		$body = $this->toArray();
		$i = 0;
		foreach ($body as $key => $value) {
			if($key == $section){
				return $i;
			}
			$i++;
		} return -1;
	}

	/**
	* @internal
	*
	* Get the index of the target element of a section into the parsed file.ini. <br/>
	*
	* @param string $section The target element's section's key.
	* @param string $section The element's key to evaluate.
	*
	* @return int Index of the target element into the parsed file.ini. If this method doesn't find the index,
	* returns -1;
	*/
	private function elementIndex($section, $element){
		$body = $this->toArray();
		$i = 0;
		foreach ($body as $key => $value) {
			if($key == $section){
				foreach ($value as $k => $v) {
					if($k == $element){
						return $i;
					}
					$i++;
				}
			}
		} return -1;
	}

	/**
	* @internal
	*
	* Create a content buffer from an array representing the futur file.ini.
	*
	* @param mixed[] $body The parsed body to put into this file.
	*
	* @return string The content buffer to put into this file.
	*/
	private function createContentFromBody($body){
		$content = "";
		foreach ($body as $key => $value) {
			$content .= "[" . $key . "]\n";
			if(is_array($value)){
				foreach ($value as $k => $v) {
				   	end($value);
					$lastKey = key($value);
					$content .= $k . " = " . "\"" . $v . "\"\n";
					if($k == $lastKey){
						$content .= "\n";
					}
				}
			}
		}
		return $content;
	}

	/**
	* @internal
	*
	* Create the file at the path passed in argument.
	*
	* @param string $path The path of the file to create.
	*
	* @return void
	*/
   	private function createFile($path){
	   	$path_array = explode(self::DS, $path);
	   	array_shift($path_array);
	   	$path_temp = self::DS;
	   	end($path_array);
		$lastKey = key($path_array);
	   	for($i = 0; $i < sizeof($path_array); $i++){
	   		$path_temp .= $path_array[$i] . self::DS;
	   		if(!file_exists($path_temp)){
	   			if($i == $lastKey){
	   				$path_temp = substr($path_temp, 0, -1);
					fopen($path_temp, "w");
				} else {
	   				mkdir($path_temp, 0700);
	   			}
	   		}
		}
	}

}