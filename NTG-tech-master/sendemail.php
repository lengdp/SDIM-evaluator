<?php
$course = isset($_POST['course']) ? $_POST['course'] : "?";
$week = isset($_POST['week']) ? $_POST['week'] : "?";
$account=isset($_POST['Account']) ? $_POST['Account'] : "?";
$password=isset($_POST['Password']) ? $_POST['Password'] : "?";
$subject=isset($_POST['Subject']) ? $_POST['Subject'] : "?";
$body=isset($_POST['Body']) ? $_POST['Body'] : "?";
$command = sprintf('python sendEmail.py %s %d %s %s %s %s',$course,$week,$account,$password, escapeshellarg($subject),escapeshellarg($body));
echo($command);
$output=shell_exec($command);
echo($output)
?>