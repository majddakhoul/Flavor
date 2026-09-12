<?php

return [
    'orders' => [
        'confirmation' => [
            'title' => 'تم استلام الطلب :reference',
            'body' => 'المطبخ استلم طلبك. سنعلمك عند تغيّر الحالة.',
        ],
        'status' => [
            'title' => 'الطلب :reference أصبح :status',
            'body' => 'تغيّرت حالة طلبك إلى :status.',
        ],
        'new_for_staff' => [
            'title' => 'طلب جديد :reference',
            'body' => 'وصل طلب جديد وينتظر المعالجة.',
        ],
    ],
    'reservations' => [
        'ticket' => [
            'title' => 'تم تأكيد الحجز :code',
            'body' => 'طاولتك محجوزة. أظهر هذا الرمز عند الوصول.',
        ],
        'status' => [
            'title' => 'الحجز :code أصبح :status',
            'body' => 'تغيّرت حالة حجزك إلى :status.',
        ],
        'new_for_staff' => [
            'title' => 'حجز جديد :code',
            'body' => 'تم تسجيل حجز طاولة جديد.',
        ],
    ],
    'inventory' => [
        'low_stock' => [
            'title' => 'نقص في المخزون: :ingredient',
            'body' => 'هذه المادة وصلت إلى حد إعادة الطلب أو أقل.',
        ],
    ],
    'people' => [
        'credentials' => [
            'title' => 'حسابك جاهز الآن',
            'body' => 'أهلاً بك في الفريق. راجع بريدك الإلكتروني لبيانات الدخول.',
        ],
        'new_hire' => [
            'title' => 'عضو جديد في الفريق: :name',
            'body' => 'انضم :name بصفة :position.',
        ],
    ],
];
