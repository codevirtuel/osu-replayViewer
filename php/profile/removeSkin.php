<?php
session_start();
require_once 'ini.class.php';

if(empty($_SESSION)){
  header("Location:index.php");
}
require 'replaySettings.php';

function error($error_code){
  header("Location:../../editProfile.php?block=skin&error=".$error_code);
  exit();
}

function getIniKey2($userId,$key){
  $ini = parse_ini_file('../../accounts/'.$userId.'/'.$userId.'.ini');
  return $ini[$key];
}

function multiexplode ($delimiters,$string) {
  $ready = str_replace($delimiters, $delimiters[0], $string);
  $launch = explode($delimiters[0], $ready);
  return  $launch;
}

function listAllSkins2($userId){
  $skins = array();
  $path = __DIR__.'/../../accounts/'.$userId;
  foreach (glob($path.'/*.osk') as $filename) {
    var_dump($filename);
    $tab = multiexplode(array("/","\\"),$filename);
    array_push($skins,$tab[11]);
  }
  return $skins;
}

  $userURL = "../../accounts/".$_SESSION["userId"]."/";
  $skinToRemove = $_POST["skin"];

  //Check if this skin exists
  if(!file_exists($userURL.$skinToRemove)){
    error(12);
  }

  //update ini file
  $skins = listAllSkins2($_SESSION["userId"]);

  //Remove skin from array
  if (($key = array_search($skinToRemove, $skins)) !== false) {
    unset($skins[$key]);
  }

  if(count($skins) <= 0){
    
  }else{
    $customSkin = getIniKey2($_SESSION["userId"],"enable");
    $array = array_rand($skins,1);
    $skin = $skins[$array];
  }

  $customSkin = 'false';
  $skin = "default";


  $dim = getIniKey2($_SESSION["userId"],"dim");

  $ini_dir = '../../accounts/'.$_SESSION["userId"].'/'.$_SESSION["userId"].'.ini';
  $ini = new Ini();
  $ini->read($ini_dir);
  $ini->set("skin","enable",$customSkin);
  $ini->set("skin","fileName",$skin);
  $ini->write($ini_dir);

  //Delete the file
  if(unlink($userURL.$skinToRemove)){
    header('Location:../../editProfile.php?block=skin&success=1');
  }else{
    error(13);
  }
 ?>
