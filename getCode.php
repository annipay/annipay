<?php
date_default_timezone_set('Asia/Shanghai');


$con = mysql_connect("数据库地址","username","pwd");
if (!$con)
{
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("数据库", $con);



if (!isset($_POST["userName"])){
    $arrBack = array(
        "code" => '202',
        "msg" => '请输入用户名（userName）',
        "data" => 'no userName'
    );
    echo json_encode($arrBack);
    return;
}elseif (!isset($_POST["productsID"])){
    $arrBack = array(
        "code" => '202',
        "msg" => '请选择商品（productsID）',
        "data" => 'no productsID'
    );
    echo json_encode($arrBack);
    return;
}elseif (!isset($_POST["userEmail"])){
    $arrBack = array(
        "code" => '202',
        "msg" => '请输入邮箱（userEmail）',
        "data" => 'no userEmail'
    );
    echo json_encode($arrBack);
    return;
}elseif (!isset($_POST["userRemark"])){
    $arrBack = array(
        "code" => '202',
        "msg" => '请输入备注信息（userRemark）',
        "data" => 'no userRemark'
    );
    echo json_encode($arrBack);
    return;
}else{


        $productsArray = mysql_query("SELECT * FROM tableName where id like $_POST[productsID]");
        $rowproducts = mysql_fetch_array($productsArray);
        $productsPrice = $rowproducts['price'];
        $productsName = $rowproducts['name'];
        $result = mysql_query("SELECT * FROM tableName where str like 0 and str like $productsPrice");

        $res = mysql_num_rows($result);

        if($res < 1){

            $arrBack = array(
                "code" => '202',
                "msg" => '请稍后1分钟再试，服务器爆满'.$productsPrice,
                "data" => 'no '.$productsPrice.'qrCode'
            );
            echo json_encode($arrBack);
            return;

        }else {
            $arr = array();

            while ($row = mysql_fetch_array($result)) {
                $arr = array(
                    "wxID" => $row['id'],
                );
                break;
            }
            $currentDate = date("Y-m-d H:i:s");
            $sql = "INSERT INTO tableName (str1, str2, str3, str4, str5, str6)
              VALUES
              ('value1','value2','value3','value4','value5','value6')";

            if (!mysql_query($sql, $con)) {
                die('Error: ' . mysql_error());

            } else {

                $insterID = mysql_insert_id();

                $arrBack = array (
                    "code" => '200',
                    "msg" => '订单生成成功',
                    "data" => array("orderID" => $insterID, "wxQRCodePng" => $row['wxQRCodePng'])
                );

                mysql_query("UPDATE tableName SET status = '1' WHERE str = $arr[wxID]");
                echo json_encode($arrBack);
            }

        }












}
mysql_close($con)
?>
