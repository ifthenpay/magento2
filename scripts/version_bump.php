<?php

define('MODULE_PATH', '../../etc/module.xml');
// MODULE_PATH = '../../etc/module.xml';

$moduleXml = simplexml_load_file(MODULE_PATH);

if (!$moduleXml) {
    die("Error: Unable to load module.xml file.\n");
}

$currentVersion = (string) $moduleXml->module['setup_version'];

echo "Current version: {$currentVersion}\n";

$nextVersion = bumpVersion($currentVersion);

// Prompt the user for the new version
echo "Enter the new version (press Enter to use suggested next version {$nextVersion}): ";
$newVersion = trim(fgets(STDIN));

// default to the next version if the user didn't enter a new version
if (empty($newVersion)) {
    $newVersion = $nextVersion;
}

if (!isValidVersion($newVersion)) {
    die("Error: Invalid version format. The version should be in the format 'x.y.z' (e.g., 2.0.7).\n");
}

// ask for confirmation if the new version is lower than the current version
if ((version_compare($currentVersion, $newVersion) >= 0)) {

    echo "--------------------------------------------------------------------------------\n";
    echo "                                      WARNING                                   \n";
    echo "--------------------------------------------------------------------------------\n";

    echo "The version you entered is lower than the current version.\n";
    echo "Current version: {$currentVersion}\n";
    echo "New version: {$newVersion}\n";
    echo "Are you sure you want to continue? (y/n): ";
    $canContinue = trim(fgets(STDIN));

    if ($canContinue !== 'y') {
        die("Aborting.\n");
    }
}


try {
    updateFilesWithVersion($newVersion, $moduleXml);
} catch (\Throwable $th) {
    echo "Error: Unable to update files with new version.\n";
    echo $th->getMessage() . "\n";
    die();
}


echo "New version: {$newVersion}\n";






function updateFilesWithVersion($newVersion, $moduleXml)
{
    // updates files that hold the version number, this will change from module to module

    // Update the module.xml file with the new version
    $moduleXml->module['setup_version'] = $newVersion;
    $moduleXml->asXML(MODULE_PATH);
}



function isValidVersion($version)
{
    // The version should be in the format 'x.y.z' (e.g., 2.0.7).
    return preg_match('/^\d+\.\d+\.\d+$/', $version) === 1;
}


function bumpVersion($version)
{
    // Bump the patch version by one
    $version = explode('.', $version);

    $version[2]++;

    return implode('.', $version);
}
?>
