<?php

function __autoload($className)
{
    include $className . '.php';
}

$order = new Integration(
    $terminalKey,  //Terminal_Key банка
    $secretKey,   //Secret_Key банка
    $apiCrmKey.  //Secret_Key Crm
    $apiCrmUrl
//    '1560950580384DEMO',  //Terminal_Key банка
//    'f0ffckfde1wkh9a4',   //Secret_Key банка
//    "JJNsMgdJ2VdFGx4VgJehPYZxuNqgEiIz",  //Secret_Key Crm
//"http://u5904sbar-mn1-justhost.retailcrm.ru/api/v5/"

);

// тестовое id заказа
$orderId = 6237;

/* Собираю данные о заказе*/
$data = $order->getOrderData($orderId);

/* подключаюсь к банку */
$bankApi = $order->bankConnection();

/* Выполняю транзакции для получения ссылок */
foreach ($data->items as $item) {
    $params = $order->getParams($item->id);
    $bankApi->init($params);
    //ссылка, которую получили из банка
    echo $bankApi->paymentUrl." ";
}


?>

