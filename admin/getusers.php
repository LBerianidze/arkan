<?php
function deleteDir($dirPath)
{
    $files = glob($dirPath.'/*');
    foreach($files as $file)
    {
        if(is_file($file))
            unlink($file);
    }
}

deleteDir(getcwd());