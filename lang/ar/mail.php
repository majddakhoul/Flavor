<?php

return [
    'orders' => [
        'confirmation' => [
            'subject' => 'تم استلام الطلب :reference',
            'heading' => 'طلبك وصل',
            'intro' => 'استلم المطبخ طلبك، وسنراسلك عند تغيّر الحالة.',
        ],
        'status' => [
            'subject' => 'الطلب :reference أصبح :status',
            'heading' => 'تحديث الطلب',
            'intro' => 'تغيّرت حالة طلبك.',
        ],
    ],
    'reservations' => [
        'ticket' => [
            'subject' => 'تم تأكيد الحجز :code',
            'heading' => 'طاولتك محجوزة',
            'intro' => 'أظهر هذا الرمز عند الاستقبال ليتم إجلاسك.',
        ],
        'status' => [
            'subject' => 'الحجز :code أصبح :status',
            'heading' => 'تحديث الحجز',
            'intro' => 'تغيّرت حالة حجزك.',
        ],
    ],
    'people' => [
        'credentials' => [
            'subject' => 'حسابك في Flavor جاهز',
            'heading' => 'أهلاً بك في الفريق يا :name',
            'intro' => 'هذه كلمة المرور لأول تسجيل دخول.',
            'footnote' => 'غيّر كلمة المرور بعد أول دخول.',
        ],
    ],
    'auth' => [
        'verification' => [
            'subject' => 'رمز التحقق الخاص بك',
            'heading' => 'أكّد بريدك يا :name',
            'intro' => 'أدخل هذا الرمز في صفحة التحقق.',
        ],
    ],
    'inventory' => [
        'low-stock' => [
            'subject' => 'مخزون منخفض: :ingredient',
            'heading' => 'المخزون على وشك النفاد',
            'intro' => 'انخفضت إحدى المواد إلى الحد الأدنى أو أقل.',
        ],
    ],
];
