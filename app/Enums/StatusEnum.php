<?php

namespace App\Enums;

class StatusEnum
{
    const STATUS_AR =[
        '0' => 'جديد',
        '1' => 'جاري التحضير',
        '3' => 'جاري التوصيل',
        '5' => 'تم الالغاء',
        '8' => 'تم التوصيل',
        '9' => 'تم حذفه',
        '10' => 'معلق',
        '99' => 'غير مدفوع',
    ];

    const STATUS_EN = [
        '0' =>'NEW',
        '1' =>'PROCESSING',
        '3' =>'SHIPPING',
        '5' =>'CANCELED',
        '8' =>'DELIVERED',
        '9' =>'DELETED',
        '10' =>'PENDING',
        '99' =>'UNPAID',
    ];
}
