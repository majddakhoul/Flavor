<?php

return [
    'user_type' => [
        'Manager' => 'مدير',
        'Employee' => 'موظف',
        'Customer' => 'زبون',
    ],
    'employee_position' => [
        'Manager' => 'مدير',
        'Chef' => 'شيف',
        'Waiter' => 'نادل',
        'Security' => 'أمن',
        'Delivery' => 'توصيل',
    ],
    'order_status' => [
        'Pending' => 'قيد الانتظار',
        'Confirmed' => 'مؤكد',
        'Completed' => 'مكتمل',
        'Cancelled' => 'ملغى',
    ],
    'order_type' => [
        'Delivery' => 'توصيل',
        'Reservation' => 'داخل المطعم',
        'Takeaway' => 'سفري',
    ],
    'reservation_status' => [
        'Pending' => 'قيد الانتظار',
        'Confirmed' => 'مؤكد',
        'Cancelled' => 'ملغى',
        'Completed' => 'منتهٍ',
    ],
    'reservation_type' => [
        'Locally' => 'من المطعم',
        'Application' => 'عبر الموقع',
    ],
    'table_location' => [
        'Indoor' => 'داخلي',
        'Outdoor' => 'خارجي',
        'VIP' => 'كبار الزوار',
        'Roof' => 'السطح',
    ],
    'meal_availability' => [
        'available' => 'متوفر',
        'unavailable' => 'غير متوفر',
    ],
    'gender' => [
        'Male' => 'ذكر',
        'Female' => 'أنثى',
    ],
    'cart_item_type' => [
        'meal' => 'وجبة',
        'offer' => 'عرض',
    ],
    'allergy' => [
        'Cows Milk Allergy' => 'حليب البقر',
        'Egg Allergy' => 'البيض',
        'Peanut Allergy' => 'الفول السوداني',
        'Tree Nut Allergy' => 'المكسرات',
        'Fish Allergy' => 'السمك',
        'Shellfish Allergy' => 'القشريات',
        'Wheat Allergy' => 'القمح',
        'Soy Allergy' => 'الصويا',
        'Seed Allergies' => 'البذور',
        'Red Meat Allergy' => 'اللحم الأحمر',
        'Fruit Allergies' => 'الفواكه',
        'Vegetable Allergies' => 'الخضار',
        'Spice Allergies' => 'البهارات',
    ],
];
