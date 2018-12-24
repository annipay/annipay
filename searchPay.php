<?php
header('Access-Control-Allow-Origin:*');
header("Content-Type:text/html;charset=utf-8");
require_once("./phpmailer/functions.php");

$con = mysql_connect("localhost","root","2d7a99e287a7a38f");
if (!$con)
{
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("api_annipay_com", $con);

if (!isset($_POST["orderID"])){
    $arrBack = array(
        "code" => '202',
        "msg" => '请输入订单号（orderID）',
        "data" => 'not orderID'
    );
    echo json_encode($arrBack);
    return;
}elseif (!isset($_POST["wxSure"])){
    $arrBack = array(
        "code" => '202',
        "msg" => '请输入确认信息 2确认支付了 或者 3取消支付了（wxSure）',
        "data" => 'not wxSure'
    );
    echo json_encode($arrBack);
    return;
}else{
    //查询有无订单
    $resultOrders = mysql_query("SELECT * FROM biao2_userPush where id = $_POST[orderID]");

    $resID = mysql_num_rows($resultOrders);

    if ($_POST[wxSure] == '3'){//取消支付
        if($resID < 1){

            $arrBack = array(
                "code" => '202',
                "msg" => '数据库查不到orderID='.$_POST[orderID],
                "data" => 'NO orderID = '.$_POST[orderID]
            );
            echo json_encode($arrBack);
            return;

        }else{

            $orders = mysql_fetch_array($resultOrders);
            $wxID = $orders['wxID'];

            //微信二维码修改 状态为待使用
            mysql_query("UPDATE biao1_wqQRCode SET wxSure = '0' WHERE biao1_wqQRCode.id = $wxID");

            //订单修改 状态为取消支付
            mysql_query("UPDATE biao2_userPush SET wxSure = '3' WHERE biao2_userPush.id = $_POST[orderID]");


            $arrBack = array(
                "code" => '200',
                "msg" => '取消支付成功！订单为'.$_POST[orderID],
                "data" => 'YES'
            );
            echo json_encode($arrBack);
            return;
        }


    }elseif ($_POST[wxSure] == '2'){//确认支付

        if($resID < 1){

            $arrBack = array(
                "code" => '202',
                "msg" => '数据库查不到orderID='.$_POST[orderID],
                "data" => 'NO orderID = '.$_POST[orderID]
            );
            echo json_encode($arrBack);
            return;

        }else{
            $orders = mysql_fetch_array($resultOrders);
            $wxID = $orders['wxID'];
            //微信二维码修改 状态为待使用
            mysql_query("UPDATE biao1_wqQRCode SET wxSure = '2' WHERE biao1_wqQRCode.id = $wxID");
            //订单修改 状态为等待审核
            mysql_query("UPDATE biao2_userPush SET wxSure = '2' WHERE biao2_userPush.id = $_POST[orderID]");

            //查询微信相关信息

            $resultWXArray = mysql_query("SELECT * FROM biao1_wqQRCode where id like $wxID");

            $resultWX = mysql_fetch_array($resultWXArray);

            $wxRemark = $resultWX['wxRemark'];
            $wxMoney = $resultWX['wxMoney'];


            //发送给管理员的文本内容
            $sureHtml = '<span style="color:red;">等待确认</span><br/><br/>
                                <a href="http://api.annipay.com/userSureNot.php?orderID='.$_POST["orderID"].'&wxSure=4" target="_blank">已经支付</a><br/><br/><br/>
                                <a href="http://api.annipay.com/userSureNot.php?orderID='.$_POST["orderID"].'&wxSure=5" target="_blank">没有支付</a><br/><br/><br/>
                                ';



            //管理员邮箱annipay6@gmail.com
            $flag = sendMail('annipay6@gmail.com','【待确认订单ID='.$_POST["orderID"].'】 查询备注信息='.$wxRemark.'【收款码价格】='.$wxMoney, $sureHtml);
            if($flag){
                $arrBack = array(
                    "code" => '200',
                    "msg" => '提交成功，等待自动审核',
                    "data" => 'email send YES'
                );
                echo json_encode($arrBack);
                return;
            }else{
                $arrBack = array(
                    "code" => '202',
                    "msg" => '发送邮件失败！',
                    "data" => 'email send NO'
                );
                echo json_encode($arrBack);
                return;
            }

        }


    }else{
        $arrBack = array(
            "code" => '202',
            "msg" => 'wxSure状态不正确 2提交支付成功审核，3取消支付 (wxSure)',
            "data" => 'wxSure isNot 2/3'
        );
        echo json_encode($arrBack);
        return;
    }

}










mysql_close($con)
?>
