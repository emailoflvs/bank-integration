<?php

include_once("TinkoffMerchantAPI.php");

/*
* Class for integration CRM to Tinkoff bank
* */
class Integration
{
    private $terminalKey;  //Terminal_Key банка
    private $secretKey;    //Secret_Key банка
    private $apiCrmUrl;    //url Crm
    private $apiCrmKey;    //Secret_Key Crm

    private $email, $phone, $amount;
    public $items;

    public function __construct($terminalKey, $secretKey, $apiCrmKey, $apiCrmUrl)
    {
        $this->api_url = 'https://securepay.tinkoff.ru/v2/';
        $this->terminalKey = $terminalKey;
        $this->secretKey = $secretKey;

        $this->apiCrmUrl = $apiCrmUrl; //url Crm
        $this->apiCrmKey = $apiCrmKey;
    }


    /*
    * Доступ к API Tinkoff
    *
    * @return string
    * */
    public function bankConnection()
    {
        $bankApi = new TinkoffMerchantAPI(
            $this->terminalKey,
            $this->secretKey
        );
        return $bankApi;
    }

    /*
    * Обязательные данные для оформления заказа и получения ссылки из банка
     *
     * @param $parameter
     * @param $filter
     * @param $paymentType
     *
     * @return
    * */
    public function getOrderData($parameter, $filter, $paymentType = null)
    {
        $orderInfo = file_get_contents($this->apiCrmUrl . "orders?apiKey=" . $this->apiCrmKey . "&" . $filter . "=" . $parameter);
//        $orderInfo = file_get_contents($this->apiCrmUrl . "orders?apiKey=" . $this->apiCrmKey . "&filter[ids][]=" . $orderId);
        $orderInfo = json_decode($orderInfo);
        $orderInfo = $orderInfo->orders[0];

        $this->email = $orderInfo->email;
        $this->phone = $orderInfo->phone;
        $this->amount = $orderInfo->totalSumm * 100;
        $this->items = $orderInfo->items;

        return $orderInfo;
    }


    /*
     * Собирает параметры для передачи банку
     * paymentId - пеередает внешний id, который crm выдала для оплаты конкретного товара
     * */
    public function getParams($paymentId)
    {

        $sum = 0;
        foreach ($this->items as $key => $item) {

            $amount = ($item->initialPrice - $item->discountTotal) * 100;
            $sum += $amount;

            $items [$key] = [
                'Name' => $item->offer->name,
                'Price' => $item->initialPrice,
                'Quantity' => $item->quantity,
                'Amount' => $amount,
                'Tax' => 'vat10',
                'Ean13' => '0123456789',
            ];
        }

        $receipt = [
            'Email' => $this->email,
            'Phone' => $this->phone,
            'Taxation' => 'osn',
            'Items' => $items,
        ];


        $params = [
            'OrderId' => $paymentId,
            'Amount' => $this->amount,
            'DATA' => [
                'Email' => $this->email,
                'Phone' => $this->phone
            ],
            'Receipt' => $receipt
        ];

        return $params;
    }

}