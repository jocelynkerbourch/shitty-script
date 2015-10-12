<?php
define("JSON_FILE", 'videos.json');
define("THUMBS_DIR", 'thumbs/');
define("THUMB_WIDTH", 100);
define("THUMB_HEIGHT", 100);

function resize_crop_image($max_width, $max_height, $source_file, $dst_dir, $quality = 80){
    $imgsize = getimagesize($source_file);
    $width = $imgsize[0];
    $height = $imgsize[1];
    $mime = $imgsize['mime'];
 
    switch($mime){
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            $image = "imagegif";
            break;
 
        case 'image/png':
            $image_create = "imagecreatefrompng";
            $image = "imagepng";
            $quality = 7;
            break;
 
        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            $image = "imagejpeg";
            $quality = 80;
            break;
 
        default:
            return false;
            break;
    }
     
    $dst_img = imagecreatetruecolor($max_width, $max_height);
    $src_img = $image_create($source_file);
     
    $width_new = $height * $max_width / $max_height;
    $height_new = $width * $max_height / $max_width;
    if($width_new > $width){
        $h_point = (($height - $height_new) / 2);
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
    }else{
        $w_point = (($width - $width_new) / 2);
        imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
    }
     
    $image($dst_img, $dst_dir, $quality);
 
    if($dst_img)imagedestroy($dst_img);
    if($src_img)imagedestroy($src_img);
}

function extractIdYoutube($url){
	preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);
	if (array_key_exists(1, $matches)){
		return $matches[1];
	}
	return null;
}

function extractIdVimeo($link){
		$regexstr = '~
			# Match Vimeo link and embed code
			(?:&lt;iframe [^&gt;]*src=")?		# If iframe match up to first quote of src
			(?:							# Group vimeo url
				https?:\/\/				# Either http or https
				(?:[\w]+\.)*			# Optional subdomains
				vimeo\.com				# Match vimeo.com
				(?:[\/\w]*\/videos?)?	# Optional video sub directory this handles groups links also
				\/						# Slash before Id
				([0-9]+)				# $1: VIDEO_ID is numeric
				[^\s]*					# Not a space
			)							# End group
			"?							# Match end quote if part of src
			(?:[^&gt;]*&gt;&lt;/iframe&gt;)?		# Match the end of the iframe
			(?:&lt;p&gt;.*&lt;/p&gt;)?		        # Match any title information stuff
			~ix';
		
		preg_match($regexstr, $link, $matches);
		
		return $matches[1];
}

function extractDailymotion($link){
	$output = parse_url($link);
	$url= $output['path'];
	$parts = explode('/',$url);
	$parts = explode('_',$parts[2]);

	return $parts[0];
}



function url_exists($url) {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_NOBODY, true);
	$result = curl_exec($curl);
	if ($result !== false) {
	  $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
	  return $statusCode !== 404;
	}

	return false;
}

$videosfile = file_get_contents(JSON_FILE);
$videos = json_decode($videosfile);

foreach($videos->data as $video){
	$raw = THUMBS_DIR."raw-".$video->video_id.".jpg";
	$img = THUMBS_DIR.$video->video_id.".jpg";

	if (strpos($video->video_url,'youtu') !== false) {
		$idyoutube = trim(extractIdYoutube($video->video_url));

		if (!empty($idyoutube)){

			$url = "http://img.youtube.com/vi/".$idyoutube."/maxresdefault.jpg";

			
			if (url_exists($url)) {
				file_put_contents($raw, file_get_contents($url));
			}else{
				$url = "http://img.youtube.com/vi/".$idyoutube."/hqdefault.jpg";
				if (url_exists($url)) {
					file_put_contents($raw, file_get_contents($url));
				}else{
					$url = "http://img.youtube.com/vi/".$idyoutube."/mqdefault.jpg";
					if (url_exists($url)) {
						file_put_contents($raw, file_get_contents($url));
					}
				}
			}
		}
	}elseif (strpos($video->video_url,'vimeo') !== false) {	
			$idviemo = trim(extractIdVimeo($video->video_url));
			if (!empty($idviemo)){
				$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$idviemo.php"));
				file_put_contents($raw, file_get_contents($hash[0]['thumbnail_large']));
			}
	}elseif (strpos($video->video_url,'dailymotion') !== false) {	
			$iddailymotion = trim(extractDailymotion($video->video_url));
			if (!empty($iddailymotion)){
				$hash = json_decode(file_get_contents("https://api.dailymotion.com/video/$iddailymotion?fields=thumbnail_large_url"));
				file_put_contents($raw, file_get_contents($hash->thumbnail_large_url));
			}
	}

	if (file_exists($raw)){
		resize_crop_image(THUMB_WIDTH, THUMB_HEIGHT, $raw, $img);
	}
}
