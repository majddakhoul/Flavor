<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Mezze',
                'description' => 'Small cold and warm plates served before the main course.',
                'ar' => ['name' => 'المقبلات', 'description' => 'أطباق صغيرة باردة ودافئة تُقدَّم قبل الطبق الرئيسي.'],
                'fr' => ['name' => 'Mezzé', 'description' => 'Petites assiettes froides et chaudes servies en entrée.'],
            ],
            [
                'name' => 'Grills',
                'description' => 'Charcoal grilled meat and poultry from the open kitchen.',
                'ar' => ['name' => 'المشاوي', 'description' => 'لحوم ودواجن مشوية على الفحم من المطبخ المفتوح.'],
                'fr' => ['name' => 'Grillades', 'description' => 'Viandes et volailles grillées au charbon de bois.'],
            ],
            [
                'name' => 'Main Courses',
                'description' => 'Slow cooked house dishes served with rice or bread.',
                'ar' => ['name' => 'الأطباق الرئيسية', 'description' => 'أطباق البيت المطهوة على نار هادئة مع الأرز أو الخبز.'],
                'fr' => ['name' => 'Plats principaux', 'description' => 'Plats mijotés de la maison servis avec riz ou pain.'],
            ],
            [
                'name' => 'Pastries',
                'description' => 'Oven baked savoury pastries prepared every morning.',
                'ar' => ['name' => 'المعجنات', 'description' => 'معجنات مالحة تُخبز طازجة كل صباح.'],
                'fr' => ['name' => 'Pâtisseries salées', 'description' => 'Feuilletés salés cuits chaque matin.'],
            ],
            [
                'name' => 'Salads',
                'description' => 'Fresh vegetables dressed to order.',
                'ar' => ['name' => 'السلطات', 'description' => 'خضار طازجة تُحضَّر عند الطلب.'],
                'fr' => ['name' => 'Salades', 'description' => 'Légumes frais assaisonnés à la commande.'],
            ],
            [
                'name' => 'Desserts',
                'description' => 'Syrup pastries, puddings and seasonal fruit.',
                'ar' => ['name' => 'الحلويات', 'description' => 'حلويات بالقطر ومهلبيات وفواكه موسمية.'],
                'fr' => ['name' => 'Desserts', 'description' => 'Pâtisseries au sirop, entremets et fruits de saison.'],
            ],
            [
                'name' => 'Drinks',
                'description' => 'Hot and cold drinks, juices and house lemonade.',
                'ar' => ['name' => 'المشروبات', 'description' => 'مشروبات ساخنة وباردة وعصائر وليموناضة البيت.'],
                'fr' => ['name' => 'Boissons', 'description' => 'Boissons chaudes et froides, jus et limonade maison.'],
            ],
        ];

        foreach ($categories as $data) {
            $category = Category::query()->firstOrCreate(
                ['name' => $data['name']],
                ['description' => $data['description'], 'parent_id' => null]
            );

            $category->syncTranslations([
                'ar' => $data['ar'],
                'fr' => $data['fr'],
            ]);
        }
    }
}
