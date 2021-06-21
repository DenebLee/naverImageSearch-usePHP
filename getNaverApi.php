<?php
  $client_id = "";  //클라이언트 아이디
  $client_secret = "";		//시크릿코드 
  $encText = urlencode($_POST["encText"]);	//검색한 이미지 변수
  $url = "https://openapi.naver.com/v1/search/image?query=".$encText."&display=100"; // json 결과
  $is_post = false;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, $is_post);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $headers = array();
  $headers[] = "X-Naver-Client-Id: ".$client_id;
  $headers[] = "X-Naver-Client-Secret: ".$client_secret;
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  $response = curl_exec ($ch);
  $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//   echo "status_code:".$status_code."
// ";
  curl_close ($ch);
  if($status_code == 200) {
    echo $response;
  } else {

    echo "Error 내용:".$response;
  }
?>