<?php

function __autoload($className)
{
    include $className . '.php';
}

$info = new Integration(
    '1560950580384DEMO',  //Terminal_Key банка
    'f0ffckfde1wkh9a4',   //Secret_Key банка
    "JJNsMgdJ2VdFGx4VgJehPYZxuNqgEiIz"  //Secret_Key Crm
);

// тестовое id заказа
$orderId = 6237;
$data = $info->getOrderData($orderId);

// подключаюсь к банку
$bankApi = $info->bankConnection();

foreach ($data->items as $item) {

    $params = $info->getParams($item->id);

    $bankApi->init($params);

    var_dump($bankApi->response);

    //ссылка, которую получили из банка
    echo "<br>".$bankApi->paymentUrl."<br>";
}


?>

