<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\MealRating;
use App\Models\Offer;
use App\Models\OfferRating;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FocusedRestaurantSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $now = Carbon::now();

        // 1. إنشاء الفئات الرئيسية الأربع
        $categories = [
            'Appetizers',
            'Main Courses',
            'Desserts',
            'Beverages',
        ];


        $categoryIds = [];
        foreach ($categories as $category) {
            $cat = Category::create([
                'name' => $category,
                'description' => $faker->sentence,
                'parent_id' => null
            ]);
            $categoryIds[$category] = $cat->id;
        }

        // 2. توليد 100 مكون
        $ingredients = [
            ['name' => 'Chicken Breast', 'unit' => 'kg', 'unit_cost' => 5000],
            ['name' => 'Ground Beef', 'unit' => 'kg', 'unit_cost' => 7000],
            ['name' => 'Salmon Fillet', 'unit' => 'kg', 'unit_cost' => 10000],
            ['name' => 'Shrimp', 'unit' => 'kg', 'unit_cost' => 12000],
            ['name' => 'Tofu', 'unit' => 'kg', 'unit_cost' => 3000],
            ['name' => 'Lettuce', 'unit' => 'kg', 'unit_cost' => 2000],
            ['name' => 'Tomatoes', 'unit' => 'kg', 'unit_cost' => 1500],
            ['name' => 'Onions', 'unit' => 'kg', 'unit_cost' => 1000],
            ['name' => 'Potatoes', 'unit' => 'kg', 'unit_cost' => 1200],
            ['name' => 'Carrots', 'unit' => 'kg', 'unit_cost' => 1300],
            ['name' => 'Spinach', 'unit' => 'kg', 'unit_cost' => 2400],
            ['name' => 'Broccoli', 'unit' => 'kg', 'unit_cost' => 2800],
            ['name' => 'Bell Peppers', 'unit' => 'kg', 'unit_cost' => 2500],
            ['name' => 'Garlic', 'unit' => 'kg', 'unit_cost' => 4500],
            ['name' => 'Ginger', 'unit' => 'kg', 'unit_cost' => 6000],
            ['name' => 'Chili Peppers', 'unit' => 'kg', 'unit_cost' => 4000],
            ['name' => 'Mushrooms', 'unit' => 'kg', 'unit_cost' => 5500],
            ['name' => 'Olive Oil', 'unit' => 'L', 'unit_cost' => 8000],
            ['name' => 'Butter', 'unit' => 'kg', 'unit_cost' => 5000],
            ['name' => 'Milk', 'unit' => 'L', 'unit_cost' => 3000],
            ['name' => 'Cheddar Cheese', 'unit' => 'kg', 'unit_cost' => 6000],
            ['name' => 'Mozzarella Cheese', 'unit' => 'kg', 'unit_cost' => 6500],
            ['name' => 'Parmesan Cheese', 'unit' => 'kg', 'unit_cost' => 9000],
            ['name' => 'Yogurt', 'unit' => 'L', 'unit_cost' => 2500],
            ['name' => 'Flour', 'unit' => 'kg', 'unit_cost' => 2000],
            ['name' => 'Sugar', 'unit' => 'kg', 'unit_cost' => 1500],
            ['name' => 'Salt', 'unit' => 'kg', 'unit_cost' => 1000],
            ['name' => 'Black Pepper', 'unit' => 'kg', 'unit_cost' => 5000],
            ['name' => 'Cinnamon', 'unit' => 'kg', 'unit_cost' => 5500],
            ['name' => 'Nutmeg', 'unit' => 'kg', 'unit_cost' => 6000],
            ['name' => 'Vanilla Extract', 'unit' => 'L', 'unit_cost' => 12000],
            ['name' => 'Eggs', 'unit' => 'dozen', 'unit_cost' => 2500],
            ['name' => 'Honey', 'unit' => 'kg', 'unit_cost' => 10000],
            ['name' => 'Maple Syrup', 'unit' => 'L', 'unit_cost' => 15000],
            ['name' => 'Chocolate Chips', 'unit' => 'kg', 'unit_cost' => 9000],
            ['name' => 'Coffee Beans', 'unit' => 'kg', 'unit_cost' => 15000],
            ['name' => 'Tea Leaves', 'unit' => 'kg', 'unit_cost' => 8000],
            ['name' => 'Cocoa Powder', 'unit' => 'kg', 'unit_cost' => 7000],
            ['name' => 'Baking Powder', 'unit' => 'kg', 'unit_cost' => 4000],
            ['name' => 'Baking Soda', 'unit' => 'kg', 'unit_cost' => 3500],
            ['name' => 'Cornstarch', 'unit' => 'kg', 'unit_cost' => 3000],
            ['name' => 'Coconut Milk', 'unit' => 'L', 'unit_cost' => 4000],
            ['name' => 'Almonds', 'unit' => 'kg', 'unit_cost' => 13000],
            ['name' => 'Walnuts', 'unit' => 'kg', 'unit_cost' => 14000],
            ['name' => 'Peanuts', 'unit' => 'kg', 'unit_cost' => 9000],
            ['name' => 'Cashews', 'unit' => 'kg', 'unit_cost' => 15000],
            ['name' => 'Raisins', 'unit' => 'kg', 'unit_cost' => 7000],
            ['name' => 'Cilantro', 'unit' => 'kg', 'unit_cost' => 2500],
            ['name' => 'Parsley', 'unit' => 'kg', 'unit_cost' => 2000],
            ['name' => 'Basil', 'unit' => 'kg', 'unit_cost' => 3000],
            ['name' => 'Oregano', 'unit' => 'kg', 'unit_cost' => 3500],
            ['name' => 'Thyme', 'unit' => 'kg', 'unit_cost' => 4000],
            ['name' => 'Rosemary', 'unit' => 'kg', 'unit_cost' => 4500],
            ['name' => 'Bay Leaves', 'unit' => 'kg', 'unit_cost' => 3000],
            ['name' => 'Mustard Seeds', 'unit' => 'kg', 'unit_cost' => 5000],
            ['name' => 'Curry Powder', 'unit' => 'kg', 'unit_cost' => 6000],
            ['name' => 'Turmeric', 'unit' => 'kg', 'unit_cost' => 5500],
            ['name' => 'Saffron', 'unit' => 'g', 'unit_cost' => 30000],
            ['name' => 'Vanilla Beans', 'unit' => 'kg', 'unit_cost' => 45000],
            ['name' => 'Lemon Juice', 'unit' => 'L', 'unit_cost' => 5000],
            ['name' => 'Orange Juice', 'unit' => 'L', 'unit_cost' => 4500],
            ['name' => 'Coconut Water', 'unit' => 'L', 'unit_cost' => 4000],
            ['name' => 'Cucumber', 'unit' => 'kg', 'unit_cost' => 2000],
            ['name' => 'Zucchini', 'unit' => 'kg', 'unit_cost' => 2200],
            ['name' => 'Eggplant', 'unit' => 'kg', 'unit_cost' => 2500],
            ['name' => 'Corn', 'unit' => 'kg', 'unit_cost' => 1800],
            ['name' => 'Green Beans', 'unit' => 'kg', 'unit_cost' => 2400],
            ['name' => 'Peas', 'unit' => 'kg', 'unit_cost' => 2300],
            ['name' => 'Sweet Corn', 'unit' => 'kg', 'unit_cost' => 2100],
            ['name' => 'Sweet Potatoes', 'unit' => 'kg', 'unit_cost' => 2800],
            ['name' => 'Pineapple', 'unit' => 'kg', 'unit_cost' => 3200],
            ['name' => 'Strawberries', 'unit' => 'kg', 'unit_cost' => 7000],
            ['name' => 'Blueberries', 'unit' => 'kg', 'unit_cost' => 9000],
            ['name' => 'Raspberries', 'unit' => 'kg', 'unit_cost' => 8500],
            ['name' => 'Mango', 'unit' => 'kg', 'unit_cost' => 4000],
            ['name' => 'Banana', 'unit' => 'kg', 'unit_cost' => 2000],
        ];


        $ingredientIds = [];
        foreach ($ingredients as $ingredient) {
            $ing = Ingredient::create([
                'name' => $ingredient['name'],
                'unit' => $ingredient['unit'],
                'stock_quantity' => 1000,
                'unit_cost' => $ingredient['unit_cost'],
                'is_active' => true
            ]);
            $ingredientIds[] = $ing->id;
        }

        // 3. توليد الوجبات (75 وجبة)
        $mealsByCategory = [
            'Appetizers' => [
                'Hummus',
                'Spring Rolls',
                'Mozzarella Sticks',
                'Chicken Wings',
                'Shrimp Cocktail',
                'Bruschetta',
                'Stuffed Mushrooms',
                'Nachos',
                'Calamari',
                'Garlic Bread',
                'Spinach Dip',
                'Deviled Eggs',
                'Chicken Satay',
                'Crab Cakes',
                'Potato Skins',
                'Onion Rings',
                'Stuffed Jalapenos',
                'Bacon Wrapped Dates',
                'Meatballs',
                'Caprese Salad',
                'Greek Salad',
                'Falafel',
                'Cheese Platter',
                'Pita Bread with Tzatziki',
                'Fried Zucchini',
                'Eggplant Fries',
                'Clam Chowder',
                'Stuffed Olives',
                'Ceviche',
                'Tuna Tartare',
            ],
            'Main Courses' => [
                'Grilled Salmon',
                'Beef Stroganoff',
                'Chicken Parmesan',
                'Vegetable Stir Fry',
                'Pork Tenderloin',
                'Herb Crusted Lamb',
                'Fish Tacos',
                'Eggplant Parmesan',
                'Shrimp Scampi',
                'Seafood Paella',
                'Beer Battered Fish',
                'Lobster Roll',
                'Seared Tuna',
                'Mussels Marinara',
                'Seafood Risotto',
                'Chicken Alfredo',
                'Roast Turkey',
                'Duck Confit',
                'Chicken Curry',
                'Chicken Piccata',
                'Beef Wellington',
                'Filet Mignon',
                'Prime Rib',
                'Pork Chops',
                'Vegetable Lasagna',
                'Stuffed Peppers',
                'Falafel',
                'Vegetable Curry',
                'Bean Burger',
                'Spinach Pie',
                'Vegetarian Pizza',
                'Mushroom Risotto',
                'Lamb Chops',
                'BBQ Ribs',
                'Grilled Steak',
                'Pulled Pork Sandwich',
                'Chicken Fajitas',
                'Seafood Linguine',
                'Pasta Primavera',
                'Beef Tacos',
            ],
            'Desserts' => [
                'Chocolate Cake',
                'Tiramisu',
                'Cheesecake',
                'Crème Brûlée',
                'Apple Pie',
                'Ice Cream',
                'Chocolate Mousse',
                'Fruit Tart',
                'Bread Pudding',
                'Panna Cotta',
                'Chocolate Soufflé',
                'Banana Split',
                'Key Lime Pie',
                'Peach Cobbler',
                'Brownie Sundae',
                'Cannoli',
                'Lemon Bars',
                'Carrot Cake',
                'Baklava',
                'Macarons',
                'Cupcakes',
                'Crepes',
                'Churros',
                'Profiteroles',
            ],
            'Beverages' => [
                'Lemonade',
                'Iced Tea',
                'Smoothie',
                'Chocolate Shake',
                'Orange Juice',
                'Coffee',
                'Hot Chocolate',
                'Mint Tea',
                'Cappuccino',
                'Latte',
                'Espresso',
                'Mocha',
                'Matcha Latte',
                'Chai Latte',
                'Bubble Tea',
                'Lime Soda',
                'Apple Cider',
                'Ginger Ale',
                'Sparkling Water',
                'Cola',
                'Mango Lassi',
                'Pina Colada',
                'Mojito',
                'Sangria',
            ],
        ];


        $mealIds = [];
        foreach ($mealsByCategory as $category => $mealNames) {
            $categoryId = $categoryIds[$category];

            foreach ($mealNames as $mealName) {
                $prepTime = $faker->numberBetween(10, 60);

                $meal = Meal::create([
                    'name' => $mealName,
                    'prep_time' => sprintf('%02d:%02d:00', floor($prepTime / 60), $prepTime % 60),
                    'is_vegetarian' => $faker->boolean(30),
                    'percentage' => $faker->numberBetween(0, 100),
                    'description' => "This is a description",//$faker->sentence,
                    'availability' => 'available',
                    'category_id' => $categoryId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $mealIds[] = $meal->id;

                // ربط 5-10 مكونات عشوائية بالوجبة
                $ingredientCount = $faker->numberBetween(5, 10);
                $selectedIngredients = $faker->randomElements($ingredientIds, $ingredientCount);

                foreach ($selectedIngredients as $ingId) {
                    $meal->ingredients()->attach($ingId, [
                        'quantity' => $faker->randomFloat(2, 0.1, 0.5),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        // 4. توليد العروض (15 عرض)
        $offerTitles = [
            ['title' => 'Summer Special Discount', 'description' => '20% off on all beverages'],
            ['title' => 'Family Feast', 'description' => 'Buy 3 main courses, get 1 appetizer free'],
            ['title' => 'Dessert Delight', 'description' => 'Free dessert with any main course order'],
            ['title' => 'Happy Hour', 'description' => 'Half price on all cocktails from 5 PM to 7 PM'],
            ['title' => 'Weekend Brunch Deal', 'description' => '15% off on brunch items'],
            ['title' => 'Loyalty Program', 'description' => 'Earn double points on all orders over $50'],
            ['title' => 'Kids Eat Free', 'description' => 'One free kids meal with every adult meal'],
            ['title' => 'Early Bird Special', 'description' => '10% off for orders before 12 PM'],
        ];


        $offerIds = [];
        foreach ($offerTitles as $title) {
            // إنشاء نطاقات تاريخ منطقية
            $dateType = $faker->numberBetween(1, 4);

            switch ($dateType) {
                case 1: // عرض نشط (التاريخ الحالي ضمن النطاق)
                    $startDate = Carbon::now()->subDays($faker->numberBetween(1, 30));
                    $endDate = Carbon::now()->addDays($faker->numberBetween(1, 30));
                    $isActive = true;
                    break;

                case 2: // عرض قادم (يبدأ في المستقبل)
                    $startDate = Carbon::now()->addDays($faker->numberBetween(1, 30));
                    $endDate = $startDate->copy()->addDays($faker->numberBetween(7, 60));
                    $isActive = false;
                    break;

                case 3: // عرض منتهي (انتهى في الماضي)
                    $endDate = Carbon::now()->subDays($faker->numberBetween(1, 30));
                    $startDate = $endDate->copy()->subDays($faker->numberBetween(7, 60));
                    $isActive = false;
                    break;

                case 4: // عرض انتهى مؤخراً (انتهى للتو)
                    $endDate = Carbon::now()->subDays(1);
                    $startDate = $endDate->copy()->subDays($faker->numberBetween(7, 30));
                    $isActive = false;
                    break;
            }

            $offer = Offer::create([
                'title' => $title['title'],
                'description' => $title['description'],
                'discount_amount' => $faker->numberBetween(10, 40),
                'is_active' => $isActive,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $offerIds[] = $offer->id;

            // ربط 3-6 وجبات عشوائية بالعرض
            $mealCount = $faker->numberBetween(3, 6);
            $selectedMeals = $faker->randomElements($mealIds, $mealCount);

            foreach ($selectedMeals as $mealId) {
                $offer->meals()->attach($mealId, [
                    'quantity' => $faker->numberBetween(1, 3),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // 5. إنشاء العملاء إذا لم يكونوا موجودين
        $customerCount = Customer::count();
        if ($customerCount < 100) {
            $needed = 100 - $customerCount;
            Customer::factory()->count($needed)->create();
        }
        $customers = Customer::all();

        // 6. توليد تقييمات الوجبات (تقييم واحد لكل عميل لكل وجبة)
        foreach ($mealIds as $mealId) {
            foreach ($customers as $customer) {
                MealRating::create([
                    'number_stars' => $faker->numberBetween(1, 5),
                    'meal_id' => $mealId,
                    'customer_id' => $customer->id,
                    'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                    'updated_at' => now()
                ]);
            }
        }

        // 7. توليد تقييمات العروض (تقييم واحد لكل عميل لكل عرض)
        foreach ($offerIds as $offerId) {
            foreach ($customers as $customer) {
                OfferRating::create([
                    'number_stars' => $faker->numberBetween(1, 5),
                    'offer_id' => $offerId,
                    'customer_id' => $customer->id,
                    'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
