<?php

namespace Database\Seeders;

use App\Models\Meal;
use App\Models\Offer;
use App\Models\Picture;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class OfferSeeder extends Seeder
{
    /**
     * Disk and folder where offer photos live, e.g. storage/app/public/offers/<slug>.jpg
     * Accepts jpg, jpeg, png or webp — drop in whichever you have.
     */
    protected string $disk = 'public';
    protected string $folder = 'offers';
    protected array $extensions = ['jpg', 'jpeg', 'png', 'webp'];

    public function run(): void
    {
        $meals = Meal::query()->pluck('id', 'name');

        $missing = [];

        $offers = [
            [
                'title' => 'Family grill night',
                'image' => 'family-grill-night',
                'description' => 'A full grill spread for four with mezze, bread and lemonade.',
                'ar' => ['ليلة المشاوي العائلية', 'وجبة مشاوي كاملة لأربعة أشخاص مع مقبلات وخبز وليموناضة.'],
                'fr' => ['Soirée grillades en famille', 'Un plateau de grillades pour quatre avec mezzé, pain et limonade.'],
                'discount' => 20,
                'items' => ['Mixed grill platter' => 1, 'Hummus with pine nuts' => 2, 'Fattoush' => 1, 'House lemonade' => 4],
            ],
            [
                'title' => 'Mezze for two',
                'image' => 'mezze-for-two',
                'description' => 'Five cold and warm mezze plates with fresh bread.',
                'ar' => ['مقبلات لشخصين', 'خمسة أطباق مقبلات باردة ودافئة مع خبز طازج.'],
                'fr' => ['Mezzé pour deux', 'Cinq assiettes de mezzé froid et chaud avec pain frais.'],
                'discount' => 15,
                'items' => ['Hummus with pine nuts' => 1, 'Moutabal' => 1, 'Tabbouleh' => 1, 'Batata harra' => 1, 'Cheese fatayer' => 2],
            ],
            [
                'title' => 'Working lunch',
                'image' => 'working-lunch',
                'description' => 'One main course, a salad and a hot drink, ready in half an hour.',
                'ar' => ['غداء العمل', 'طبق رئيسي وسلطة ومشروب ساخن جاهزة خلال نصف ساعة.'],
                'fr' => ['Déjeuner express', 'Un plat, une salade et une boisson chaude en trente minutes.'],
                'discount' => 12,
                'items' => ['Kabsa with chicken' => 1, 'Tabbouleh' => 1, 'Mint tea' => 1],
            ],
            [
                'title' => 'Sweet finish',
                'image' => 'sweet-finish',
                'description' => 'Two desserts with Arabic coffee for the table.',
                'ar' => ['ختام حلو', 'حلويتان مع قهوة عربية للطاولة.'],
                'fr' => ['Fin sucrée', 'Deux desserts accompagnés d\'un café arabe.'],
                'discount' => 10,
                'items' => ['Muhalabia' => 1, 'Namoura' => 1, 'Arabic coffee' => 2],
            ],
        ];

        foreach ($offers as $data) {
            $pictureId = $this->resolvePicture($data['image'], $missing, $data['title']);

            $offer = Offer::query()->updateOrCreate(
                ['title' => $data['title']],
                [
                    'description' => $data['description'],
                    'discount_amount' => $data['discount'],
                    'is_active' => true,
                    'start_date' => now()->subDays(5)->toDateString(),
                    'end_date' => now()->addDays(25)->toDateString(),
                    'picture_id' => $pictureId,
                ]
            );

            $offer->syncTranslations([
                'ar' => ['title' => $data['ar'][0], 'description' => $data['ar'][1]],
                'fr' => ['title' => $data['fr'][0], 'description' => $data['fr'][1]],
            ]);

            $bundle = [];

            foreach ($data['items'] as $mealName => $quantity) {
                if (isset($meals[$mealName])) {
                    $bundle[$meals[$mealName]] = ['quantity' => $quantity];
                }
            }

            $offer->meals()->sync($bundle);
        }

        if ($missing && $this->command) {
            $this->command->newLine();
            $this->command->warn(count($missing).' offer photo(s) not found in storage/app/public/'.$this->folder.'/:');
            foreach ($missing as $title => $slug) {
                $this->command->line("  - {$title}  →  {$this->folder}/{$slug}.(jpg|jpeg|png|webp)");
            }
        }
    }

    /**
     * Look for <slug>.<ext> on disk, create/refresh its pictures row, return the picture id.
     * Returns null (and records it as missing) when no matching file exists yet.
     */
    protected function resolvePicture(string $slug, array &$missing, string $offerTitle): ?int
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

        $missing[$offerTitle] = $slug;

        return null;
    }
}
