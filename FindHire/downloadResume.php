<?php
require_once "core/dbConfig.php";

$fileName = urldecode($_GET['file']);
$applicationID = intval($_GET['applicationID']);
$uploadDir = "uploads/resumes/job_post_" . $_GET['jobDescID'] . "/"; 

echo "File Name: " . $fileName . "<br>";
echo "Application ID: " . $applicationID . "<br>";
echo "Full Path: " . $uploadDir . $fileName .  "<br>";

$filePath = $uploadDir . $fileName;

if (file_exists($filePath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));

    readfile($filePath);
    exit;
} else {
    die("File not found.");
}
?>