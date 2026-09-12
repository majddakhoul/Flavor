<?php

namespace Database\Seeders;

use App\Enums\MealAvailability;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\Picture;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MealSeeder extends Seeder
{
    /**
     * Disk and folder where meal photos live, e.g. storage/app/public/meals/<slug>.jpg
     * Accepts jpg, jpeg, png or webp — drop in whichever you have.
     */
    protected string $disk = 'public';
    protected string $folder = 'meals';
    protected array $extensions = ['jpg', 'jpeg', 'png', 'webp'];

    public function run(): void
    {
        $categories = Category::query()->pluck('id', 'name');
        $ingredients = Ingredient::query()->pluck('id', 'name');

        $missing = [];

        foreach ($this->meals() as $data) {
            $pictureId = $this->resolvePicture($data['image'], $missing, $data['name']);

            $meal = Meal::query()->updateOrCreate(
                ['name' => $data['name']],
                [
                    'prep_time' => $data['prep_time'],
                    'is_vegetarian' => $data['vegetarian'],
                    'percentage' => $data['percentage'],
                    'description' => $data['description'],
                    'availability' => MealAvailability::Available->value,
                    'category_id' => $categories[$data['category']] ?? $categories->first(),
                    'picture_id' => $pictureId,
                ]
            );

            $meal->syncTranslations([
                'ar' => ['name' => $data['ar'][0], 'description' => $data['ar'][1]],
                'fr' => ['name' => $data['fr'][0], 'description' => $data['fr'][1]],
            ]);

            $recipe = [];

            foreach ($data['recipe'] as $ingredientName => $quantity) {
                if (isset($ingredients[$ingredientName])) {
                    $recipe[$ingredients[$ingredientName]] = ['quantity' => $quantity];
                }
            }

            $meal->ingredients()->sync($recipe);
        }

        if ($missing && $this->command) {
            $this->command->newLine();
            $this->command->warn(count($missing).' meal photo(s) not found in storage/app/public/'.$this->folder.'/:');
            foreach ($missing as $name => $slug) {
                $this->command->line("  - {$name}  →  {$this->folder}/{$slug}.(jpg|jpeg|png|webp)");
            }
        }
    }

    /**
     * Look for <slug>.<ext> on disk, create/refresh its pictures row, return the picture id.
     * Returns null (and records it as missing) when no matching file exists yet.
     */
    protected function resolvePicture(string $slug, array &$missing, string $mealName): ?int
    {
        foreach ($this->extensions as $ext) {
            $path = "{$this->folder}/{$slug}.{$ext}";

            if (Storage::disk($this->disk)->exists($path)) {
                $picture = Picture::query()->updateOrCreate(
                    ['path' => $path],
                    ['name' => "{$slug}.{$ext}"]
                );

                return $picture->id;
            }
        }

        $missing[$mealName] = $slug;

        return null;
    }

    protected function meals(): array
    {
        return [
            [
                'name' => 'Hummus with pine nuts',
                'image' => 'hummus-with-pine-nuts',
                'ar' => ['حمص بالصنوبر', 'حمص مخفوق بالطحينة والليمون مع صنوبر محمّر بالزبدة.'],
                'fr' => ['Houmous aux pignons', 'Houmous au tahini et citron, pignons dorés au beurre.'],
                'description' => 'Chickpeas whipped with tahini and lemon, topped with butter toasted pine nuts.',
                'category' => 'Mezze',
                'prep_time' => '00:10:00',
                'percentage' => 55,
                'vegetarian' => true,
                'recipe' => ['Chickpeas' => 0.25, 'Tahini' => 0.06, 'Lemon' => 0.05, 'Garlic' => 0.01, 'Pine nuts' => 0.02, 'Olive oil' => 0.03],
            ],
            [
                'name' => 'Moutabal',
                'image' => 'moutabal',
                'ar' => ['متبل باذنجان', 'باذنجان مشوي على الفحم مع لبن وطحينة وليمون.'],
                'fr' => ['Moutabal', 'Aubergine grillée au charbon, yaourt, tahini et citron.'],
                'description' => 'Charcoal roasted aubergine folded with yoghurt, tahini and lemon.',
                'category' => 'Mezze',
                'prep_time' => '00:15:00',
                'percentage' => 52,
                'vegetarian' => true,
                'recipe' => ['Aubergine' => 0.35, 'Tahini' => 0.05, 'Yoghurt' => 0.08, 'Garlic' => 0.01, 'Olive oil' => 0.02],
            ],
            [
                'name' => 'Tabbouleh',
                'image' => 'tabbouleh',
                'ar' => ['تبولة', 'بقدونس وبندورة وبرغل ناعم مع ليمون وزيت زيتون.'],
                'fr' => ['Taboulé', 'Persil, tomate et boulgour fin, citron et huile d\'olive.'],
                'description' => 'Parsley, tomato and fine bulgur tossed with lemon and olive oil.',
                'category' => 'Salads',
                'prep_time' => '00:12:00',
                'percentage' => 58,
                'vegetarian' => true,
                'recipe' => ['Parsley' => 0.2, 'Tomato' => 0.15, 'Bulgur' => 0.05, 'Mint' => 0.03, 'Lemon' => 0.06, 'Olive oil' => 0.03],
            ],
            [
                'name' => 'Fattoush',
                'image' => 'fattoush',
                'ar' => ['فتوش', 'خضار مقرمشة مع خبز محمّص ودبس رمان وسمّاق.'],
                'fr' => ['Fattouche', 'Légumes croquants, pain grillé, mélasse de grenade et sumac.'],
                'description' => 'Crisp vegetables with toasted bread, pomegranate molasses and sumac.',
                'category' => 'Salads',
                'prep_time' => '00:12:00',
                'percentage' => 56,
                'vegetarian' => true,
                'recipe' => ['Tomato' => 0.12, 'Cucumber' => 0.12, 'Pita bread' => 1, 'Pomegranate molasses' => 0.02, 'Sumac' => 5, 'Olive oil' => 0.03],
            ],
            [
                'name' => 'Mixed grill platter',
                'image' => 'mixed-grill-platter',
                'ar' => ['مشاوي مشكلة', 'شيش طاووق وكباب وريش غنم مع خبز وخضار مشوية.'],
                'fr' => ['Assiette de grillades', 'Chich taouk, kebab et côtelettes d\'agneau, pain et légumes grillés.'],
                'description' => 'Shish taouk, kebab and lamb chops with bread and grilled vegetables.',
                'category' => 'Grills',
                'prep_time' => '00:35:00',
                'percentage' => 42,
                'vegetarian' => false,
                'recipe' => ['Chicken breast' => 0.25, 'Lamb shoulder' => 0.25, 'Minced beef' => 0.2, 'Tomato' => 0.1, 'Onion' => 0.08, 'Pita bread' => 2],
            ],
            [
                'name' => 'Shish taouk',
                'image' => 'shish-taouk',
                'ar' => ['شيش طاووق', 'مكعبات دجاج متبّلة باللبن والثوم مشوية على الفحم.'],
                'fr' => ['Chich taouk', 'Cubes de poulet marinés au yaourt et à l\'ail, grillés au charbon.'],
                'description' => 'Chicken cubes marinated in yoghurt and garlic, grilled over charcoal.',
                'category' => 'Grills',
                'prep_time' => '00:25:00',
                'percentage' => 45,
                'vegetarian' => false,
                'recipe' => ['Chicken breast' => 0.3, 'Yoghurt' => 0.08, 'Garlic' => 0.02, 'Lemon' => 0.04, 'Pita bread' => 1],
            ],
            [
                'name' => 'Lamb kebab',
                'image' => 'lamb-kebab',
                'ar' => ['كباب لحم', 'لحم مفروم مع بقدونس وبصل مشوي على السيخ.'],
                'fr' => ['Kebab d\'agneau', 'Viande hachée au persil et oignon, grillée en brochette.'],
                'description' => 'Minced meat with parsley and onion, shaped on skewers and grilled.',
                'category' => 'Grills',
                'prep_time' => '00:28:00',
                'percentage' => 44,
                'vegetarian' => false,
                'recipe' => ['Minced beef' => 0.28, 'Onion' => 0.08, 'Parsley' => 0.04, 'Pita bread' => 1],
            ],
            [
                'name' => 'Kabsa with chicken',
                'image' => 'kabsa-with-chicken',
                'ar' => ['كبسة دجاج', 'أرز بسمتي مع دجاج وبهارات وصنوبر.'],
                'fr' => ['Kabsa au poulet', 'Riz basmati, poulet, épices et pignons.'],
                'description' => 'Basmati rice cooked with chicken, warm spices and pine nuts.',
                'category' => 'Main Courses',
                'prep_time' => '00:45:00',
                'percentage' => 40,
                'vegetarian' => false,
                'recipe' => ['Basmati rice' => 0.25, 'Chicken breast' => 0.3, 'Onion' => 0.08, 'Tomato' => 0.1, 'Pine nuts' => 0.015],
            ],
            [
                'name' => 'Freekeh with lamb',
                'image' => 'freekeh-with-lamb',
                'ar' => ['فريكة باللحم', 'فريكة مطبوخة بمرق اللحم مع قطع غنم طرية.'],
                'fr' => ['Freekeh à l\'agneau', 'Freekeh mijoté au bouillon avec agneau fondant.'],
                'description' => 'Smoked green wheat simmered in lamb stock with tender shoulder.',
                'category' => 'Main Courses',
                'prep_time' => '00:50:00',
                'percentage' => 38,
                'vegetarian' => false,
                'recipe' => ['Bulgur' => 0.2, 'Lamb shoulder' => 0.3, 'Onion' => 0.07, 'Butter' => 0.03, 'Pine nuts' => 0.015],
            ],
            [
                'name' => 'Stuffed vine leaves',
                'image' => 'stuffed-vine-leaves',
                'ar' => ['ورق عنب', 'ورق عنب محشو بالأرز والخضار ومطهو بالليمون.'],
                'fr' => ['Feuilles de vigne farcies', 'Feuilles farcies au riz et légumes, mijotées au citron.'],
                'description' => 'Vine leaves rolled with rice and vegetables, simmered in lemon.',
                'category' => 'Main Courses',
                'prep_time' => '00:55:00',
                'percentage' => 48,
                'vegetarian' => true,
                'recipe' => ['Basmati rice' => 0.18, 'Tomato' => 0.08, 'Onion' => 0.06, 'Lemon' => 0.06, 'Olive oil' => 0.04],
            ],
            [
                'name' => 'Cheese fatayer',
                'image' => 'cheese-fatayer',
                'ar' => ['فطاير بالجبنة', 'عجينة طرية محشوة بجبنة الحلوم والنعناع.'],
                'fr' => ['Fatayer au fromage', 'Pâte moelleuse garnie de halloumi et de menthe.'],
                'description' => 'Soft dough parcels filled with halloumi and mint.',
                'category' => 'Pastries',
                'prep_time' => '00:20:00',
                'percentage' => 60,
                'vegetarian' => true,
                'recipe' => ['Flour' => 0.15, 'Halloumi' => 0.12, 'Mint' => 0.02, 'Butter' => 0.03],
            ],
            [
                'name' => 'Meat sambousek',
                'image' => 'meat-sambousek',
                'ar' => ['سمبوسك باللحمة', 'معجنات مقلية محشوة باللحم المفروم والصنوبر.'],
                'fr' => ['Sambousek à la viande', 'Chaussons frits farcis de viande hachée et pignons.'],
                'description' => 'Fried pastry pockets filled with minced meat and pine nuts.',
                'category' => 'Pastries',
                'prep_time' => '00:22:00',
                'percentage' => 54,
                'vegetarian' => false,
                'recipe' => ['Flour' => 0.14, 'Minced beef' => 0.15, 'Onion' => 0.05, 'Pine nuts' => 0.01],
            ],
            [
                'name' => 'Muhalabia',
                'image' => 'muhalabia',
                'ar' => ['مهلبية', 'مهلبية حليب بماء الورد مع فستق مطحون.'],
                'fr' => ['Mouhalabia', 'Crème de lait à l\'eau de rose et pistache concassée.'],
                'description' => 'Milk pudding scented with rose water and crushed nuts.',
                'category' => 'Desserts',
                'prep_time' => '00:18:00',
                'percentage' => 62,
                'vegetarian' => true,
                'recipe' => ['Sugar' => 0.08, 'Semolina' => 0.05, 'Butter' => 0.02, 'Pine nuts' => 0.008],
            ],
            [
                'name' => 'Namoura',
                'image' => 'namoura',
                'ar' => ['نمورة', 'حلوى السميد بالقطر مع لوز محمّص.'],
                'fr' => ['Namoura', 'Gâteau de semoule au sirop et amandes grillées.'],
                'description' => 'Semolina cake soaked in syrup with toasted almonds.',
                'category' => 'Desserts',
                'prep_time' => '00:30:00',
                'percentage' => 64,
                'vegetarian' => true,
                'recipe' => ['Semolina' => 0.2, 'Sugar' => 0.15, 'Butter' => 0.05, 'Yoghurt' => 0.06],
            ],
            [
                'name' => 'House lemonade',
                'image' => 'house-lemonade',
                'ar' => ['ليموناضة البيت', 'ليمون طازج مع نعناع وثلج مجروش.'],
                'fr' => ['Limonade maison', 'Citron pressé, menthe fraîche et glace pilée.'],
                'description' => 'Fresh lemon shaken with mint and crushed ice.',
                'category' => 'Drinks',
                'prep_time' => '00:05:00',
                'percentage' => 70,
                'vegetarian' => true,
                'recipe' => ['Lemon' => 0.15, 'Mint' => 0.02, 'Sugar' => 0.05],
            ],
            [
                'name' => 'Arabic coffee',
                'image' => 'arabic-coffee',
                'ar' => ['قهوة عربية', 'بن محمّص مع الهيل يُقدَّم في فنجان صغير.'],
                'fr' => ['Café arabe', 'Café torréfié à la cardamome servi en petite tasse.'],
                'description' => 'Roasted beans brewed with cardamom, served in a small cup.',
                'category' => 'Drinks',
                'prep_time' => '00:07:00',
                'percentage' => 72,
                'vegetarian' => true,
                'recipe' => ['Coffee beans' => 0.02, 'Sugar' => 0.01],
            ],
            [
                'name' => 'Mint tea',
                'image' => 'mint-tea',
                'ar' => ['شاي بالنعناع', 'شاي أسود مع أوراق نعناع طازجة.'],
                'fr' => ['Thé à la menthe', 'Thé noir aux feuilles de menthe fraîche.'],
                'description' => 'Black tea steeped with fresh mint leaves.',
                'category' => 'Drinks',
                'prep_time' => '00:06:00',
                'percentage' => 74,
                'vegetarian' => true,
                'recipe' => ['Tea leaves' => 0.01, 'Mint' => 0.02, 'Sugar' => 0.01],
            ],
            [
                'name' => 'Batata harra',
                'image' => 'batata-harra',
                'ar' => ['بطاطا حارة', 'بطاطا مقلية مع ثوم وكزبرة وفلفل حار.'],
                'fr' => ['Batata harra', 'Pommes de terre sautées à l\'ail, coriandre et piment.'],
                'description' => 'Fried potato cubes tossed with garlic, coriander and chilli.',
                'category' => 'Mezze',
                'prep_time' => '00:16:00',
                'percentage' => 57,
                'vegetarian' => true,
                'recipe' => ['Potato' => 0.3, 'Garlic' => 0.02, 'Olive oil' => 0.04, 'Lemon' => 0.03],
            ],
        ];
    }
}
