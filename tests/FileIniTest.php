<?php 

use DodoIniManager\Classes\FileIni as FileIni;

define("__DS__", DIRECTORY_SEPARATOR);
define("COPY_FOLDER", __DIR__ . __DS__ . "folder_for_copy");
define("MOVE_FOLDER", __DIR__ . __DS__ . "folder_for_move");
define("MOVE_FOLDER_2", __DIR__ . __DS__ . "folder_for_move_2");
define("TRUE_INI_FILE_FOLDER", __DIR__ . __DS__ . "folder_for_existant_ini_file");
define("TRUE_INI_FILE", __DIR__ . __DS__ . "folder_for_existant_ini_file" . __DS__ . "file.ini");
define("TRUE_INI_FILE_FOR_RENAME", __DIR__ . __DS__ . "folder_for_existant_ini_file" .
  __DS__ . "file_for_rename.ini");
define("TRUE_INI_FILE_AFTER_RENAME", __DIR__ . __DS__ . "folder_for_existant_ini_file" .
  __DS__ . "new_file.ini");
define("ASSET_FILE", __DIR__ . __DS__ . "file.ini");

class FileIniTest extends PHPUnit_Framework_TestCase {

  private $fileIni;

  public function setUp() {
      $this->fileIni = new FileIni(ASSET_FILE);
      mkdir(COPY_FOLDER, 0700);
      mkdir(MOVE_FOLDER, 0700);
      mkdir(TRUE_INI_FILE_FOLDER, 0700);
      fopen(TRUE_INI_FILE, "w");
  }
	
  public function testCreateFileIniObjectWithPath(){
      #create file.ini and get path
      $fileIni = new FileIni(__DIR__ . __DS__ . "folder" . __DS__ . "file.ini");
      $this->assertEquals($fileIni->getPath(), __DIR__ . __DS__ . "folder" . __DS__ . "file.ini");
      #file.ini exists
      $this->assertTrue(file_exists($fileIni->getPath()));
      #create another FileIni Object with the same path
      $fileIni_2 = new FileIni(__DIR__ . __DS__ . "folder" . __DS__ . "file.ini");
      $this->assertTrue(file_exists($fileIni_2->getPath()));
      #create file.ini in existant folder
      $fileIni_3 = new FileIni(TRUE_INI_FILE_FOLDER . __DS__ . "new_file.ini");
      $this->assertTrue(file_exists($fileIni_3->getPath()));
      #create file.ini with existing file's path
      $fileIni_4 = new FileIni(TRUE_INI_FILE);
      $this->assertTrue(file_exists($fileIni_3->getPath()));
  }

  public function testRenameFileIniObjectAndCheckTheNewPath(){
      $fileIni = new FileIni(TRUE_INI_FILE_FOR_RENAME);
      $fileIni->rename("new_file.ini");
      $this->assertTrue(file_exists($fileIni->getPath()));
      $this->assertFalse(file_exists(TRUE_INI_FILE_FOR_RENAME));
  }

  public function testCopyFileIniObjectAndCheckTheTwoFiles(){
      $fileIni = new FileIni(COPY_FOLDER . __DS__ . "file_1.ini");
      $fileIni->copy("file_2.ini");
      $this->assertTrue(file_exists(COPY_FOLDER . __DS__ . "file_1.ini"));
      $this->assertTrue(file_exists(COPY_FOLDER . __DS__ . "file_2.ini"));
  }

  public function testMoveFileIniObjectAndCheckTheTwoFiles(){
      $fileIni = new FileIni(MOVE_FOLDER . __DS__ . "file.ini");
      $fileIni->move(MOVE_FOLDER_2);
      #old file.ini does'nt exist anymore
      $this->assertFalse(file_exists(MOVE_FOLDER . __DS__ . "file.ini"));
      #new file.ini exists now
      $this->assertTrue(file_exists(MOVE_FOLDER_2 . __DS__ . "file.ini"));
      #FileIni Object has $path changed
      $this->assertEquals($fileIni->getPath(), MOVE_FOLDER_2 . __DS__ . "file.ini");
      #move file.ini into non existing folder
      $fileIni->move(MOVE_FOLDER_2 . __DS__ . "non_existing_folder");
      $this->assertTrue(file_exists(MOVE_FOLDER_2 . __DS__ . "non_existing_folder" . __DS__ . "file.ini"));
  }

  public function testReturnSectionFromFileIni(){
    $section = $this->fileIni->get("php");
    $this->assertEquals($section, array("ide" => "phpstorm", "framework" => "symfony", "orm" => "doctrine"));
  }

  public function testCreateSectionFromFileIni(){
    $section = $this->fileIni->set("bash");
    $body = parse_ini_file($this->fileIni->getPath(), true);
    $this->assertTrue(array_key_exists("bash", $body));
  }

  public function testCreateSectionFromFileIniWithElements(){
    $this->fileIni->set("node", array("ide" => "webstorm",
      "framework" => "express", "orm" => "mongoose"));
    $body = parse_ini_file($this->fileIni->getPath(), true);
    $this->assertTrue(array_key_exists("node", $body));
    $section = $this->fileIni->get("node");
    $this->assertTrue(array_key_exists("ide", $section));
    $this->assertTrue(array_key_exists("framework", $section));
    $this->assertTrue(array_key_exists("orm", $section));
    $this->assertEquals("webstorm", $section["ide"]);
    $this->assertEquals("express", $section["framework"]);
    $this->assertEquals("mongoose", $section["orm"]);
  }

  public function testDeleteSectionFromFileIni(){
    $this->fileIni->rm("bash");
    $body = parse_ini_file($this->fileIni->getPath(), true);
    $this->assertFalse(array_key_exists("bash", $body));
  }

  public function testUpdateSectionFromFileIni(){
    $section = $this->fileIni->set("python", array("ide" => "idle",
      "framework" => "django", "orm" => "Full Stack Python"));
    $section = $this->fileIni->rewrite("python", "js");
    $body = parse_ini_file($this->fileIni->getPath(), true);
    $this->assertTrue(array_key_exists("js", $body));
    $this->assertFalse(array_key_exists("python", $body));
    $section = $this->fileIni->get("js");
    $this->assertTrue(array_key_exists("ide", $section));
    $this->assertTrue(array_key_exists("framework", $section));
    $this->assertTrue(array_key_exists("orm", $section));
    $this->assertEquals("idle", $section["ide"]);
    $this->assertEquals("django", $section["framework"]);
    $this->assertEquals("Full Stack Python", $section["orm"]);
  }

  public function testMoveSectionBeforeAnotherFromFileIni(){
    $section = $this->fileIni->before("ruby", "java");
    $body = parse_ini_file($this->fileIni->getPath(), true);
    $i = 0;
    foreach ($body as $key => $value) {
      if($key == "ruby"){
        $this->assertTrue($i == 1);
      } else if($key == "java"){
        $this->assertTrue($i == 2);
      }
      $i++;
    }
  }

  public function testMoveSectionAfterAnotherFromFileIni(){
    $section = $this->fileIni->after("ruby", "java");
    $body = parse_ini_file($this->fileIni->getPath(), true);
    $i = 0;
    foreach ($body as $key => $value) {
      if($key == "ruby"){
        $this->assertTrue($i == 2);
      } else if($key == "java"){
        $this->assertTrue($i == 1);
      }
      $i++;
    }
  }

  public function testSectionHasNext(){
    $this->assertTrue($this->fileIni->hasNext("ruby"));
    $this->assertFalse($this->fileIni->hasNext("js"));
  }

  public function testSectionGetNext(){
    $section = $this->fileIni->getNext("ruby");
    $this->assertTrue(array_key_exists("ide", $section));
    $this->assertTrue(array_key_exists("framework", $section));
    $this->assertTrue(array_key_exists("orm", $section));
    $this->assertEquals("rubymine", $section["ide"]);
    $this->assertEquals("ruby on rails", $section["framework"]);
    $this->assertEquals("ar", $section["orm"]);
    $this->assertFalse($this->fileIni->getNext("js"));
  }

  public function testGetElement(){
    $element = $this->fileIni->getKey("ruby", "ide");
    $this->assertEquals($element, "rubymine");
  }

  public function testsetElement(){
    $this->fileIni->set("bash");
    $this->fileIni->setKey("bash", "ide");
    $section = $this->fileIni->get("bash");
    $this->assertTrue(array_key_exists("ide", $section));
    $this->fileIni->rm("bash");
    $this->fileIni->set("bash");
    $this->fileIni->setKey("bash", array("ide" => "terminal"));
    $section = $this->fileIni->get("bash");
    $this->assertTrue(array_key_exists("ide", $section));
    $this->assertEquals("terminal", $section["ide"]);
  }

  public function testAddContentToElement(){
    $this->fileIni->setKey("bash", "framework");
    $this->fileIni->writeInKey("bash", "framework", "file system");
    $section = $this->fileIni->get("bash");
    $this->assertTrue(array_key_exists("framework", $section));
    $this->assertEquals("file system", $section["framework"]);
  }

  public function testDeleteElement(){
    $this->fileIni->rmKey("bash", "framework");
    $section = $this->fileIni->get("bash");
    $this->assertFalse(array_key_exists("framework", $section));
  }

  public function testUpdateElement(){
    $this->fileIni->rewriteKey("bash", "ide", "orm");
    $section = $this->fileIni->get("bash");
    $this->assertTrue(array_key_exists("orm", $section));
    $this->assertFalse(array_key_exists("ide", $section));
    $this->fileIni->rewriteKey("bash", "orm", "ide");
    $this->fileIni->setKey("bash", array("framework" => "file system"));
  }

  public function testUpdateElementContent(){
    $this->fileIni->setKey("bash", "orm");
    $this->fileIni->writeInKey("bash", "orm", "wrong content");
    $this->fileIni->rewriteInKey("bash", "orm", "apt-get");
    $section = $this->fileIni->get("bash");
    $this->assertTrue(array_key_exists("orm", $section));
    $this->assertEquals("apt-get", $section["orm"]);
  }

  public function testDeleteElementContent(){
    $this->fileIni->rmInKey("bash", "orm");
    $section = $this->fileIni->get("bash");
    $this->assertEquals("", $section["orm"]);
    $this->fileIni->setKey("bash", array("orm" => "apt-get"));
  }

  public function testElementHasNext(){
    $this->assertTrue($this->fileIni->keyHasNext("bash", "ide"));
    $this->assertFalse($this->fileIni->keyHasNext("bash", "orm"));
  }

  public function testElementGetNext(){
    $element = $this->fileIni->getNextKey("bash", "ide");
    $this->assertEquals("file system", $element);
    $this->assertFalse($this->fileIni->getNextKey("bash", "orm"));
  }

  public function testChangeElementSection(){
    $this->fileIni->set("fake_section");
    $this->fileIni->setKey("bash", "fake_element");
    $section = $this->fileIni->get("bash");
    $this->assertTrue(array_key_exists("fake_element", $section));
    $this->fileIni->moveKey("bash", "fake_element", "fake_section");
    $section = $this->fileIni->get("fake_section");
    $this->assertTrue(array_key_exists("fake_element", $section));
    $this->fileIni->rm("fake_section");
  }

  public function testMoveElementBefore(){
    $this->fileIni->beforeKey("js", "framework", "ide");
    $section = $this->fileIni->get("js");
    $i = 0;
    foreach ($section as $key => $value) {
      if($key == "framework"){
        $this->assertTrue($i == 0);
      } else if($key == "ide"){
        $this->assertTrue($i == 1);
      }
      $i++;
    }
  }

  public function testMoveElementAfter(){
    $this->fileIni->afterKey("js", "ide", "orm");
    $section = $this->fileIni->get("js");
    $i = 0;
    foreach ($section as $key => $value) {
      if($key == "ide"){
        $this->assertTrue($i == 2);
      } else if($key == "orm"){
        $this->assertTrue($i == 1);
      }
      $i++;
    }
  }

/*
function set($section, array($element, array($content,...)));
function setKey($section, array($element, array($content,...)));
function writeInKey($section, $element, array($content,...));
function rewriteInKey($section, $element, array($content,...));

check exception (element or section of file does not exist)

*/

  public function tearDown() {
    if(file_exists(COPY_FOLDER . __DS__ . "file_1.ini") && file_exists(COPY_FOLDER . __DS__ . "file_2.ini")){
      unlink(COPY_FOLDER . __DS__ . "file_1.ini");
      unlink(COPY_FOLDER . __DS__ . "file_2.ini");
    }
    if(file_exists(TRUE_INI_FILE)){  
      unlink(TRUE_INI_FILE);
    }
    if(file_exists(TRUE_INI_FILE_AFTER_RENAME)){  
      unlink(TRUE_INI_FILE_AFTER_RENAME);
    }
    if(file_exists(TRUE_INI_FILE_FOLDER)){  
      rmdir(TRUE_INI_FILE_FOLDER);
    }
    if(file_exists(COPY_FOLDER)){  
      rmdir(COPY_FOLDER);
    }
    if(file_exists(MOVE_FOLDER)){  
      rmdir(MOVE_FOLDER);
    }
    if(file_exists(MOVE_FOLDER_2 . __DS__ . "non_existing_folder" . __DS__ . "file.ini")){
      unlink(MOVE_FOLDER_2 . __DS__ . "non_existing_folder" . __DS__ . "file.ini");
    }
    if(file_exists(MOVE_FOLDER_2 . __DS__ . "non_existing_folder")){
      rmdir(MOVE_FOLDER_2 . __DS__ . "non_existing_folder");
    }
    if(file_exists(MOVE_FOLDER_2 . __DS__ . "file.ini")){  
      unlink(MOVE_FOLDER_2 . __DS__ . "file.ini");
    }
    if(file_exists(MOVE_FOLDER_2)){  
      rmdir(MOVE_FOLDER_2);
    }
    if(file_exists(__DIR__ . __DS__ . "folder" . __DS__ . "file.ini")){
      unlink(__DIR__ . __DS__ . "folder" . __DS__ . "file.ini");
      rmdir(__DIR__ . __DS__ . "folder");
    }
  }
  
}