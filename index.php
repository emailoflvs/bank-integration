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
);

/* тестовое id заказа */
$orderId = 6237;

$paymentType = "tinkoff-test";

if (isset($orderId)) {

    $parameter = $orderId;
    $filter = "filter[ids][]";

} elseif (isset($paymentType)) {

    $parameter = $paymentType;
    $filter = "filter[paymentTypes][]";

} else
    exit;

/* Собираю данные о заказе */
$data = $order->getOrderData($parameter, $filter, $paymentType);


/* подключаюсь к банку */
$bankApi = $order->bankConnection();

/* Выполняю транзакции для получения ссылок */
foreach ($data->items as $item) {
    $params = $order->getParams($item->id);
    $bankApi->init($params);
    //ссылка, которую получили из банка
    echo $bankApi->paymentUrl . " ";
}

?>

