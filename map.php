<?php

$coordinatesData =
    [
        'İSTANBUL' => [
            ['lat' => '41.0003','lng' => '29.2889'],
            ['lat' => '40.9984','lng' => '28.5153'],
            ['lat' => '41.0159','lng' => '28.8666'],
            ['lat' => '40.9067','lng' => '29.2627'],
            ['lat' => '41.0298','lng' => '29.1758'],
            ['lat' => '41.0099','lng' => '29.2203'],
            ['lat' => '40.9636','lng' => '29.0897'],
            ['lat' => '41.0591','lng' => '28.6678'],
            ['lat' => '41.0161','lng' => '29.2631'],
            ['lat' => '41.0534','lng' => '29.2371'],
            ['lat' => '40.9665','lng' => '29.0615'],
            ['lat' => '41.0152','lng' => '29.2325'],
            ['lat' => '40.9748','lng' => '29.1495'],
            ['lat' => '41.0136','lng' => '29.2193'],
            ['lat' => '41.0225','lng' => '29.1141'],
            ['lat' => '41.1049','lng' => '28.8559'],
            ['lat' => '40.9229','lng' => '29.2267'],
            ['lat' => '41.0727','lng' => '28.9184'],
            ['lat' => '40.9340','lng' => '29.1282'],
            ['lat' => '41.0130','lng' => '29.2586'],
            ['lat' => '40.9829','lng' => '29.1063'],
            ['lat' => '41.1367','lng' => '29.0578'],
            ['lat' => '40.9890','lng' => '29.0303'],
            ['lat' => '41.0382','lng' => '28.8407'],
            ['lat' => '40.9318','lng' => '29.1347'],
            ['lat' => '40.9802','lng' => '29.1577'],
            ['lat' => '41.0159','lng' => '29.0235'],
            ['lat' => '40.9230','lng' => '29.2796'],
            ['lat' => '41.0001','lng' => '29.2355'],
            ['lat' => '41.0908','lng' => '28.9712']
        ],
        'ANKARA' => [
            ['lat' => '39.9319','lng' => '32.8949'],
            ['lat' => '39.9718','lng' => '32.8534'],
            ['lat' => '39.9395','lng' => '32.7142'],
            ['lat' => '39.8077','lng' => '32.5730'],
            ['lat' => '39.9436','lng' => '32.6566'],
            ['lat' => '39.3246','lng' => '32.2092'],
            ['lat' => '41.0052','lng' => '29.1468'],
            ['lat' => '39.9669','lng' => '32.6006'],
            ['lat' => '39.9813','lng' => '32.6549'],
            ['lat' => '39.9609','lng' => '32.9372'],
            ['lat' => '40.0067','lng' => '32.8426'],
            ['lat' => '39.9820','lng' => '32.6254'],
            ['lat' => '39.9329','lng' => '32.6321'],
            ['lat' => '39.9823','lng' => '32.7981'],
            ['lat' => '39.9406','lng' => '32.6345'],
            ['lat' => '39.9385','lng' => '32.8858'],
            ['lat' => '39.9759','lng' => '32.9690'],
            ['lat' => '39.9738','lng' => '32.5787'],
            ['lat' => '39.8793','lng' => '32.6951'],
            ['lat' => '39.9702','lng' => '32.6573'],
            ['lat' => '39.9834','lng' => '32.8134'],
            ['lat' => '39.9477','lng' => '32.6584'],
            ['lat' => '39.9011','lng' => '32.9321'],
            ['lat' => '39.9971','lng' => '32.7993'],
            ['lat' => '39.9284','lng' => '32.8474'],
            ['lat' => '39.9882','lng' => '32.8424'],
            ['lat' => '39.9623','lng' => '32.9291'],
            ['lat' => '39.8816','lng' => '32.8396'],
            ['lat' => '39.8836','lng' => '32.8046'],
            ['lat' => '39.9658','lng' => '32.9782']
        ]
    ];

$googleMapKey = 'GOOGLE_MAP_KEY';

$imageSource = [];
foreach ($coordinatesData as $city => $coordinates ) {

    $googleAddress = cUrl("https://maps.googleapis.com/maps/api/geocode/json?&address=".$city."&key=".$googleMapKey);
    if ($googleAddress) {
        $googleAddress = json_decode($googleAddress);
        $center = $googleAddress->results[0]->geometry->location;

        $googleMapStaticImage = cUrl("https://maps.googleapis.com/maps/api/staticmap?center=".$center->lat.",".$center->lng."&zoom=10&size=600x400&maptype=roadmap&key=".$googleMapKey);
        if($googleMapStaticImage) {
            $mapImage = imagecreatefromstring($googleMapStaticImage);
            $point = imagecreatefrompng( 'marker-5px.png');
            foreach ($coordinates as $latLng) {
                $px = pointToPx($center->lat,  $center->lng, 10, 600, 400, $latLng['lat'], $latLng['lng']);
                imagecopymerge($mapImage, $point, $px['x'], $px['y'], 0,0,5,5,100);
            }
            imagedestroy($point);
            ob_start();
            imagepng($mapImage);
            $imageSource[$city] = ob_get_clean();
            imagedestroy($mapImage);
        }
    }
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="tr">
    <head><title>Calculating the pixel positions of known coordinates on the image of static Google Map using PHP</title></head>
    <body>
        <?php foreach ($imageSource as $city => $staticMapImage){?>
            <div>
            <h1><?php echo $city?></h1>
            <img alt="map" src="data:image/jpg;base64,<?php echo base64_encode($staticMapImage)?>" style="float:left;padding-right:5px;">
            <div style="font-size:10px"><?php foreach ($coordinatesData[$city] as $coords) {echo join(', ',$coords).'<br>';}?></div>
                <div style="clear:both"></div>
            </div>
        <?php } ?>
    </body>
</html>
<?php

/**
 * @param $lat : center-latitude of the static-map
 * @param $lng : center-longitude of the static-map
 * @param $zoom : zoom of the static-map
 * @param $width : width of the static-map
 * @param $height : height of the static-map
 * @param $pointLat : point-latitude
 * @param $pointLng : point-longitude
 * @return array : x-coordinate and y-coordinate the pixel inside the static-map
 */

function pointToPx($lat, $lng, $zoom, $width, $height, $pointLat, $pointLng){

    $s = min(max(sin($lat * (M_PI / 180)), -.9999), .9999);
    $tiles = 1 << $zoom;
    $centerPoint= [
        'x' => 128 + $lng * (256/ 360),
        'y' => 128 + 0.5 * log((1 + $s) / (1 - $s)) *-(256 / (2 * M_PI))
    ];

    $pixelX = ( ( ($pointLng * (256 / 360)) +  128 ) * $tiles ) - ($centerPoint['x']*$tiles) + ($width/2) ;
    $pixelY =  ( (log( tan ( (($pointLat  * (M_PI / 180)) + (M_PI / 2) ) / 2 ) ) * ( - 256/ (2 * M_PI)) + 128) * $tiles )- ($centerPoint['y']*$tiles) + ($height/2);

    $pixelXY = [
        'x' => round($pixelX),
        'y' => round($pixelY)
    ];

    return $pixelXY;
}

/**
 * @param $lat : center-latitude of the static-map
 * @param $lng : center-longitude of the static-map
 * @param $zoom : zoom of the static-map
 * @param $width : width of the static-map
 * @param $height : height of the static-map
 * @param $pointX : x-coordinate of the pixel inside the static-map
 * @param $pointY : y-coordinate of the pixel inside the static-map
 * @return array : pixel-latitude and pixel-longitude of the static-map
 */
function pxToPoint($lat, $lng, $zoom, $width, $height,  $pointX, $pointY){
    $x = $pointX-($width/2);
    $y = $pointY-($height/2);
    $s = min(max(sin($lat * (M_PI / 180)), -.9999), .9999);
    $tiles = 1 << $zoom;

    $centerPoint= [
        'x' => 128 + $lng * (256/ 360),
        'y' => 128 + 0.5 * log((1 + $s) / (1 - $s)) *-(256 / (2 * M_PI))
    ];

    $pointXY = [
        'x' =>(($centerPoint['x']*$tiles)+$x ) ,
        'y' => (($centerPoint['y']*$tiles)+$y)
    ];

    $pointLatLng = [
        'lat' => (2 * atan(exp((($pointXY['y']/$tiles) - 128) / -(256/ (2 * M_PI)))) - M_PI / 2)/ (M_PI / 180),
        'lng' => ((($pointXY['x']/$tiles) - 128) / (256 / 360)),
    ];

    return $pointLatLng;
}

function cUrl($url, $timeOut = 5)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_REFERER, 'http://localhost');

    $returnData = curl_exec($ch);
  

    curl_close($ch);
    return $returnData;

}
