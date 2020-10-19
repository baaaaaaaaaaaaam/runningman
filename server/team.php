<?php
// 데이터베이스 정보입력
$db_host="localhost";
$db_user="ansgyqja";
$db_password="Akwldrk1!";
$db_name="team";

// 데이터베이스 연결
$con=mysqli_connect($db_host,$db_user,$db_password,$db_name);

// 연결에 실패할 경우 예외처리
if(!$con){ die("연결 실패 : ".mysqli_connect_error()); }


/**
 * 어떤 요청인지 확인
 * - jsonObject 또는 jsonArray인 경우에는 디코드해주기
 */
$data = '';
$data_arr = '';
if(substr(file_get_contents("php://input"),0,1)=='['){ // jsonArray
    // echo file_get_contents("php://input");
    $data_arr = json_decode(file_get_contents("php://input"));
}else{ //jsonObject
    // echo file_get_contents("php://input");
    $data = json_decode(file_get_contents("php://input"));
}


/**
 * 테스트
 */
if(($_GET['mode'])=='test'){
  echo "test";
}

/**
 * 로그인 요청
 */
if(($_POST['mode'])=='login_action'){

   $id = $_POST['id'];
  $pw = $_POST['pw'];

   $sql = "SELECT * FROM member WHERE member_id='$id'";
   $result = mysqli_query($con,$sql);
  $login_member = mysqli_fetch_assoc($result);
  $login_member_no = $login_member['member_no'];
  if(!$login_member['member_id'] || !($pw === $login_member['member_pw'])){ // 로그인 실패
    echo -1;
  }else{ // 로그인 성공
    echo $login_member_no;
   }
}

/**
 * 회원가입 요청
 */
if(($_POST['mode'])=='join_action'){

  $id = $_POST['id'];
  $pw = $_POST['pw'];

    if (!mysqli_query($con, "INSERT INTO member (member_id, member_pw) VALUES ('$id','$pw');")) {
        echo "회원가입 실패 (Error: ".mysqli_error($con).")";
    }else{
        if(!mysqli_query($con,"UPDATE member set member_pic='./team/$id.jpg' where member_id='$id';")){
          echo "이미지 수정 실패 (Error: ".mysqli_error($con).")";
        }else{
		if(!mysqli_query($con,"UPDATE member set crop_pic='./team1/$id.jpg' where member_id='$id';")){

		}else{
			echo "0";
		}
          echo "0";
        }
    }
}
/**
 * 로그인 멤버의 정보를 얻는 요청 (JSONObject)
 */
if($data->mode=='find_login_member'){
    /**
     * 요청 인자 받기
     */
    $member_no = $data->login_member_no;

    /**
     * SQL문 시작
     */
    $sql = "SELECT * FROM member WHERE member_no='$member_no';";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);

    $member_no = $row['member_no']; // 멤버 번호
    $member_id = $row['member_id']; // 멤버 아이디
    $member_pw = $row['member_pw']; // 멤버 비밀번호

    $member = ['member_no'=>$member_no,'member_id'=>$member_id,'member_pw'=>$member_pw];
    echo json_encode($member);
}


?>
