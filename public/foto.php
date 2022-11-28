<?php
$path = '/sistema/';

if(file_exists($path.$_GET['filename'])){
    header("Content-type: image/jpg");
    echo file_get_contents($path.$_GET['filename']);
}else{
    echo $path.$_GET['filename'];
    echo "Arquivo não encontrado";
}
    
?>