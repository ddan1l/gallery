<?php
header("Access-Control-Allow-Origin: http://localhost:8080");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
}
else{
    $imagesDirectory = "src/";
    if(is_dir($imagesDirectory))
    {
        $openDirectory = opendir($imagesDirectory);
        $images = array();
        while (($image = readdir($openDirectory)) !== false)
        {
            if(($image == '.') || ($image == '..'))
            {
                continue;
            }

            $imgFileType = pathinfo($image,PATHINFO_EXTENSION);

            if($imgFileType === 'png' || $imgFileType === 'jpg' || $imgFileType === 'jpeg' || $imgFileType === 'bmp')
            {
                $imageData = file_get_contents($imagesDirectory.'/'.$image);
                array_push($images,"data:image/{$imgFileType};base64,".base64_encode($imageData) );
            }

        }
        http_response_code(200);
        echo json_encode(array(
            "images" => $images,
        ));

        closedir($openDirectory);
    }
    else{
        http_response_code(200);
        echo json_encode(array(
            "message" => 'Нет картинок',
        ));
    }

}


