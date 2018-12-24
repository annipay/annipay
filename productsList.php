<?php
$con = mysql_connect("数据库地址","username","pwd");
if (!$con)
{
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("数据库名", $con);

$result = mysql_query("SELECT * FROM tableName");

$res = mysql_num_rows($result);

if($res < 1){

    $arrBack = array(
        "code" => '202',
        "msg" => '暂无商品',
        "data" => 'no products'
    );
    echo json_encode($arrBack);
    return;

}else{
    $arr = array();

    while($row = mysql_fetch_array($result))
    {
        $arr[] = array(
            "id" => $row['id'],
            "name" => $row['name'],
            "price" => $row['price']
        );
    }

    $arrBack = array(
        "code" => '200',
        "msg" => '获取商品列表成功',
        "data" => array("title" => '标题',"content" => '内容', "productsList" => $arr)
    );


    echo json_encode($arrBack);
}


mysql_close($con)
?>
