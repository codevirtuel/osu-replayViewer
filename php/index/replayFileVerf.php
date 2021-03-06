<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/startup.php';
require_once '../disableUploads.php';
require '../osuApiFunctions.php';
require '../websiteFunctions.php';

$osuApiKey = getenv('OSU_KEY');

// Create connection
$conn = new mysqli(getenv('MYSQL_HOST'), getenv('MYSQL_USER'), getenv('MYSQL_PASS'), getenv('MYSQL_DB'));

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    header("Location:../../index.php?error=1");
    exit;
}

//booleans
$replayStructure = false;
$beatmapAvailable = false;
$playerOsuAccount = false;
$replayBelow10 = false;
$replayNotDuplicate = false;
$replayNotWaiting = false;

$skinName = 'null';
$beatmapJSON = null;

//---- Functions -----
function replayExist($filedir, $table, $conn)
{
    $md5 = md5_file($filedir);
    $stmt = $conn->prepare("SELECT * FROM " . $table . " WHERE md5=?");
    $stmt->bind_param("s", $md5);

    $stmt->execute();
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function userFileExists($userId)
{
    $user_URL = "../../accounts/" . $userId;
    return is_dir($user_URL);
}

function checkIfIniExists($userId)
{
    $ini_URL = "../../accounts/" . $userId . '/' . $userId . '.ini';
    return file_exists($ini_URL);
}

function getIniKey($userId, $key)
{
    $ini = parse_ini_file('../../accounts/' . $userId . '/' . $userId . '.ini');
    return $ini[$key];
}

//----- CORE ------
require_once '../../php/admins.php';
if (isset($_SESSION['userId']) && in_array($_SESSION['userId'], $admins)) {
    $disableUploads = false;
}

if ($disableUploads || !isset($_FILES['fileToUpload'])) {
    header("Location:../../index.php?error=9");
    exit;
}


$target_dir = "../../uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir);
}

$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$file_name = basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

//check replay structure
if (validateReplayStructure($_FILES["fileToUpload"]["tmp_name"], $osuApiKey) && $imageFileType == "osr") {
    $replayStructure = true;
}

//upload file
move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
$replay_content = getReplayContent("../../uploads/" . $file_name);

if (empty($replay_content)) {
    header("Location:../../index.php?error=2");
}

//check beatmap existance
$beatmapJSON = getBeatmapJSONwMD5($replay_content['md5'], $osuApiKey);

if (empty($beatmapJSON)) {
    header("Location:../../index.php?error=3");
}

if (isBeatmapAvailable($beatmapJSON[0]['beatmap_id'], $osuApiKey)) {
    $beatmapAvailable = true;
}

//Check osu account
$userJSON = getUserJSON($replay_content['user'], $osuApiKey);
if (!empty($userJSON)) {
    $playerOsuAccount = true;
}

//Check replay duration
$replayDuration = $beatmapJSON[0]['total_length'];
if (isDT($replay_content['Mods'])) {
    $replayDuration = $replayDuration - ($replayDuration * (33 / 100));
}
if ($replayDuration <= 600) {
    $replayBelow10 = true;
}

//Check if the replay already exists in database
if (!replayExist("../../uploads/" . $file_name, "replaylist", $conn)) {
    $replayNotDuplicate = true;
}
//Check if the replay is not already in queue
if (!replayExist("../../uploads/" . $file_name, "requestlist", $conn)) {
    $replayNotWaiting = true;
}

//Check the skin used
if (userHasAaccount($userJSON[0]['user_id'], $conn) && userFileExists($userJSON[0]['user_id']) && checkIfIniExists($userJSON[0]['user_id'])) {
    $skinName = getIniKey($userJSON[0]['user_id'], "fileName");
} else {
    $skinName = "osu!replayViewer skin";
}


//Send all the Informations
$_SESSION['filename'] = $file_name;
$_SESSION['skinName'] = $skinName;
$_SESSION['replayStructure'] = $replayStructure;
$_SESSION['beatmapAvailable'] = $beatmapAvailable;
$_SESSION['playerOsuAccount'] = $playerOsuAccount;
$_SESSION['replayBelow10'] = $replayBelow10;
$_SESSION['replayNotDuplicate'] = $replayNotDuplicate;
$_SESSION['replayNotWaiting'] = $replayNotWaiting;
$_SESSION['beatmapName'] = $beatmapJSON[0]['title'];
$_SESSION['beatmapSetId'] = $beatmapJSON[0]['beatmapset_id'];
$_SESSION['difficulty'] = $beatmapJSON[0]['version'];
if (isset($userJSON)) {
    $_SESSION['playername'] = $userJSON[0]['username'];
    $_SESSION['replay_playerId'] = $userJSON[0]['user_id'];
} else {
    $_SESSION['playername'] = 'unknown';
    $_SESSION['replay_playerId'] = null;
}

if (isset($replayDuration)) {
    $_SESSION['duration'] = $replayDuration;
} else {
    $_SESSION['duration'] = 0;
}

if (isset($replay_content)) {
    $_SESSION['mods'] = drawMods($replay_content['Mods']);
} else {
    $_SESSION['mods'] = 'none';
}

header("Location:../../index.php");

?>
