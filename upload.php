<?php
//upload.php
$AUTH_MENU_GRP__ = 0;
$AUTH_MENU_TOP__ = 0;
$AUTH_MENU_SUB__ = 0;
$MENU__ = 0;

include $_SERVER["DOCUMENT_ROOT"]. "";

function file_get_contents_curl($url) {
    $ch = curl_init();
	//컬 구문

    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       

    $data = curl_exec($ch);
    curl_close($ch);
    return $data; //클릭한 이미지에 대한 외부이미지주소 $data에 담기
}

if(isset($_POST["image_url"]))
{
	$message = '';
	$image = '';
	if(filter_var($_POST["image_url"], FILTER_VALIDATE_URL))
	{
		$fid = $SKP__['num'] ."_pdt_" . SESSION_ID_CREATE__();
		$layer_wh		= 300;
		$layer_edit_wh	= 200;
		
		$image_data = file_get_contents_curl($_POST["image_url"]);
		$work_type = 1;
		$img_type = 1;
		if( $img_type == 1 ){	
			$id_name = "pdt_img_id";  //아이디부여
			}

		$mode = 1;
		

		$new_image_path = $TMP_PDT_IMG_DIR__ . $fid;
		$new_image_url  = $TMP_PDT_IMG_URL__ . $fid;

		file_put_contents($new_image_path, $image_data); //해당 임시파일폴더에 파일로 저장
		chmod($new_image_path, 0777);

		
		//URL로 만든 임시파일을 지정하여 @getinmagesize에 대한 정보배열 부여 
		if(is_file($new_image_path)) {
			$test = $new_image_path;
			$img_info = @getimagesize($test);
			if( $img_info[2] != 2 && $img_info[2] != 3 )		BACK_JSONP__("{|mode|:|2|,|msg|:|이미지는 JPG, PNG파일만 가능합니다.|}");
			if( $img_info[2] == 2 )			$ext_str = ".jpg";
			else if( $img_info[2] == 3 )	$ext_str = ".png";

			$fid = $fid . $ext_str;
			// 원본파일 스토리지로 이동
			copy($test, $TMP_PDT_IMG_DIR__ . $fid);
			chmod($TMP_PDT_IMG_DIR__ . $fid, 0777);
		}
		
		$reg_time = time();
		// 기존 같은 sid 있는경우 삭제후 update
		$tmp_pdt_img_res = DB_FETCH_ARRAY__($MDB__, "select * from tmp_pdt_img where sid = '$sid'");
		
		if( !empty($tmp_pdt_img_res['num']) ){

			@unlink($TMP_PDT_IMG_DIR__ . $tmp_pdt_img_res['fid']);

			mysqli_query($MDB__, "update tmp_pdt_img set	fid = '$fid', reg_time = '$reg_time' where sid = '$sid'");

		// 기존 파일 없으면 sid insert
		}else{

			mysqli_query($MDB__, "insert into tmp_pdt_img set	sid				= '$sid',
																fid				= '$fid',
																reg_time		= '$reg_time'
																");
		}
		if( $img_type == 1 )	$id_name = "pdt_img_id";
		else					$id_name = "pdt_detail_img_id";

		if( $img_info[0] > $img_info[1] ){

			$wh_per = $img_info[1] / $img_info[0];	// htm의 가로,세로비 추출 - width가 큰경우 padding-top으로 센터 맞춰줌 - height가 큰경우엔 htm에서 센터처리함

			$img_tag			= "<img id=$id_name src='" .$TMP_PDT_IMG_URL__ . $fid. "' width=$layer_wh style='margin-top:" .(int)(($layer_wh - ($layer_wh * $wh_per))/2). "px;' />";
			$img_preview_tag	= "<img id=${id_name}_edit_preview_id src='" .$TMP_PDT_IMG_URL__ . $fid. "' width=$layer_edit_wh style='margin-top:" .(int)(($layer_edit_wh - ($layer_edit_wh * $wh_per))/2). "px;' />";


		}else if( $img_info[0] < $img_info[1] ){

			$img_tag			= "<img id=$id_name src='" .$TMP_PDT_IMG_URL__ . $fid. "' height=$layer_wh />";
			$img_preview_tag	= "<img id=${id_name}_edit_preview_id src='" .$TMP_PDT_IMG_URL__ . $fid. "' height=$layer_edit_wh />";

		}else{

			$img_tag			= "<img id=$id_name src='" .$TMP_PDT_IMG_URL__ . $fid. "' width=$layer_wh height=$layer_wh />";
			$img_preview_tag	= "<img id=${id_name}_edit_preview_id src='" .$TMP_PDT_IMG_URL__ . $fid. "' width=$layer_edit_wh height=$layer_edit_wh />";

		}

		BACK_JSONP__("{|mode|:|1|,|tag1|:|$img_tag|,|tag2|:|$img_preview_tag|}");
	}
}


?> 