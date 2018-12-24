<?php
header('Access-Control-Allow-Origin:*');
$con = mysql_connect("localhost","root","2d7a99e287a7a38f");
if (!$con)
{
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("api_annipay_com", $con);



if (!isset($_POST["wxSure"])){
    $arrBack = array(
        "code" => '202',
        "msg" => '请输入wxSure（wxSure）',
        "data" => 'wxSure is not'
    );
    echo json_encode($arrBack);
    return;
}elseif ($_POST["wxSure"] == '1' || $_POST["wxSure"] == '2'|| $_POST["wxSure"] == '3'|| $_POST["wxSure"] == '4'|| $_POST["wxSure"] == '5'){//1 等待支付 2二维码收款等待审核 3取消支付 4审核成功 5审核失败（只能传 1-5）


    $resultArray = mysql_query("SELECT * FROM biao2_userPush where wxSure like $_POST[wxSure]");


    $res = mysql_num_rows($resultArray);

    if($res < 1){

        $arrBack = array(
            "code" => '202',
            "msg" => '暂无数据',
            "data" => 'not products'
        );
        echo json_encode($arrBack);
        return;

    }else{


        $arr;
        while ($row = mysql_fetch_array($resultArray)) {

            $emailStr = $row['userEmail'];
            $emailStr = hideStar($emailStr);

            $arr[] = array(
                "id" => $row['id'],

                "orderTime" => $row['orderTime'],

                "userName" => $row['userName'],
                "userEmail" => $emailStr,
                "userRemark" => $row['userRemark'],

                "productsID" => $row['productsID'],
                "productsName" => $row['productsName'],
                "productsPrice" => $row['productsPrice'],

                "wxID" => $row['wxID'],
                "wxQRCodeStr" => $row['wxQRCodeStr'],
                "wxQRCodePng" => $row['wxQRCodePng'],
                "wxMoney" => $row['wxMoney'],
                "wxRemark" => $row['wxRemark'],
                "wxSure" => $row['wxSure']//等待支付
            );
        }

        $arrBack = array_reverse($arr);
        echo json_encode($arrBack);





    }





}else{
    $arrBack = array(
        "code" => '202',
        "msg" => 'wxSure请传入1-5（wxSure）',
        "data" => 'wxSure is not 1-5'
    );
    echo json_encode($arrBack);
    return;
}


function hideStar($str) { //用户名、邮箱、手机账号中间字符串以*隐藏
    if (strpos($str, '@')) {
        $email_array = explode("@", $str);
        $prevfix = (strlen($email_array[0]) < 4) ? "" : substr($str, 0, 3); //邮箱前缀
        $count = 0;
        $str = preg_replace('/([\d\w+_-]{0,100})@/', '***@', $str, -1, $count);
        $rs = $prevfix . $str;
    } else {
        $pattern = '/(1[3458]{1}[0-9])[0-9]{4}([0-9]{4})/i';
        if (preg_match($pattern, $str)) {
            $rs = preg_replace($pattern, '$1****$2', $str); // substr_replace($name,'****',3,4);
        } else {
            $rs = substr($str, 0, 3) . "***" . substr($str, -1);
        }
    }
    return $rs;
}




mysql_close($con)


?>
