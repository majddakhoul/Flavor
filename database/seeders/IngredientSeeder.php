<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->ingredients() as $data) {
            $ingredient = Ingredient::query()->firstOrCreate(
                ['name' => $data['name']],
                [
                    'unit' => $data['unit'],
                    'stock_quantity' => $data['stock'],
                    'unit_cost' => $data['cost'],
                    'is_active' => true,
                ]
            );

            $ingredient->syncTranslations([
                'ar' => ['name' => $data['ar'], 'unit' => $data['unit_ar']],
                'fr' => ['name' => $data['fr'], 'unit' => $data['unit_fr']],
            ]);
        }
    }

    protected function ingredients(): array
    {
        $kg = ['kg', 'كغ', 'kg'];
        $g = ['g', 'غ', 'g'];
        $litre = ['litre', 'ليتر', 'litre'];
        $piece = ['piece', 'قطعة', 'pièce'];

        $rows = [
            ['Chicken breast', 'صدر دجاج', 'Blanc de poulet', $kg, 120, 38000],
            ['Lamb shoulder', 'كتف غنم', 'Épaule d\'agneau', $kg, 60, 95000],
            ['Minced beef', 'لحم مفروم', 'Bœuf haché', $kg, 75, 72000],
            ['Basmati rice', 'أرز بسمتي', 'Riz basmati', $kg, 200, 12000],
            ['Bulgur', 'برغل', 'Boulgour', $kg, 90, 7000],
            ['Chickpeas', 'حمص حب', 'Pois chiches', $kg, 110, 9000],
            ['Tahini', 'طحينة', 'Tahini', $kg, 45, 26000],
            ['Olive oil', 'زيت زيتون', 'Huile d\'olive', $litre, 70, 45000],
            ['Yoghurt', 'لبن', 'Yaourt', $kg, 80, 8000],
            ['Halloumi', 'جبنة حلوم', 'Halloumi', $kg, 30, 55000],
            ['Tomato', 'بندورة', 'Tomate', $kg, 150, 5000],
            ['Cucumber', 'خيار', 'Concombre', $kg, 95, 4500],
            ['Parsley', 'بقدونس', 'Persil', $kg, 25, 3000],
            ['Mint', 'نعناع', 'Menthe', $kg, 12, 3500],
            ['Onion', 'بصل', 'Oignon', $kg, 130, 3800],
            ['Garlic', 'ثوم', 'Ail', $kg, 20, 15000],
            ['Lemon', 'ليمون', 'Citron', $kg, 60, 6000],
            ['Aubergine', 'باذنجان', 'Aubergine', $kg, 70, 5200],
            ['Potato', 'بطاطا', 'Pomme de terre', $kg, 180, 4200],
            ['Flour', 'طحين', 'Farine', $kg, 220, 4000],
            ['Sugar', 'سكر', 'Sucre', $kg, 140, 6500],
            ['Butter', 'زبدة', 'Beurre', $kg, 35, 62000],
            ['Pomegranate molasses', 'دبس رمان', 'Mélasse de grenade', $litre, 18, 30000],
            ['Sumac', 'سماق', 'Sumac', $g, 6, 120],
            ['Pine nuts', 'صنوبر', 'Pignons de pin', $kg, 8, 180000],
            ['Pita bread', 'خبز عربي', 'Pain pita', $piece, 400, 900],
            ['Mineral water', 'مياه معدنية', 'Eau minérale', $piece, 300, 1500],
            ['Tea leaves', 'شاي', 'Thé', $kg, 15, 28000],
            ['Coffee beans', 'بن', 'Café', $kg, 22, 88000],
            ['Semolina', 'سميد', 'Semoule', $kg, 40, 6800],
        ];

        return array_map(fn ($row) => [
            'name' => $row[0],
            'ar' => $row[1],
            'fr' => $row[2],
            'unit' => $row[3][0],
            'unit_ar' => $row[3][1],
            'unit_fr' => $row[3][2],
            'stock' => $row[4],
            'cost' => $row[5],
        ], $rows);
    }
}
