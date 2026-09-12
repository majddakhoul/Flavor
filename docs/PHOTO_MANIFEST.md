# Photo Manifest

For each item below: drop a real photo into the given folder using the exact
filename (any extension: `.jpg`, `.jpeg`, `.png`, `.webp` all work — the
seeder checks each in order). The **search phrase** column is written for
free stock-photo sites (Unsplash, Pexels, Pixabay) or an AI image tool —
paste it directly.

Target folder on the server: `storage/app/public/meals/` and
`storage/app/public/offers/` (create them if they don't exist, then
`php artisan storage:link` once so the `public` disk is browsable).

## Meals (`storage/app/public/meals/`)

| Expected filename | Dish | Search phrase |
| --- | --- | --- |
| `hummus-with-pine-nuts.*` | Hummus with pine nuts | hummus pine nuts tahini bowl |
| `moutabal.*` | Moutabal | baba ganoush eggplant dip |
| `tabbouleh.*` | Tabbouleh | tabbouleh parsley bulgur salad |
| `fattoush.*` | Fattoush | fattoush salad sumac pita |
| `mixed-grill-platter.*` | Mixed grill platter | mixed grill platter kebab shish taouk lamb chops |
| `shish-taouk.*` | Shish taouk | shish taouk chicken skewers grilled |
| `lamb-kebab.*` | Lamb kebab | lamb kebab skewers grilled |
| `kabsa-with-chicken.*` | Kabsa with chicken | chicken kabsa rice pine nuts |
| `freekeh-with-lamb.*` | Freekeh with lamb | freekeh lamb stew |
| `stuffed-vine-leaves.*` | Stuffed vine leaves | stuffed grape vine leaves dolma |
| `cheese-fatayer.*` | Cheese fatayer | cheese fatayer pastry halloumi |
| `meat-sambousek.*` | Meat sambousek | sambousek meat pastry fried |
| `muhalabia.*` | Muhalabia | muhalabia milk pudding rose pistachio |
| `namoura.*` | Namoura | namoura semolina cake syrup almonds |
| `house-lemonade.*` | House lemonade | fresh lemonade mint glass |
| `arabic-coffee.*` | Arabic coffee | arabic coffee cardamom small cup |
| `mint-tea.*` | Mint tea | moroccan mint tea glass |
| `batata-harra.*` | Batata harra | spicy fried potatoes coriander garlic |

## Offers (`storage/app/public/offers/`)

| Expected filename | Offer | Search phrase |
| --- | --- | --- |
| `family-grill-night.*` | Family grill night | grill platter family feast table |
| `mezze-for-two.*` | Mezze for two | mezze platter spread table for two |
| `working-lunch.*` | Working lunch | quick lunch plate rice salad tea |
| `sweet-finish.*` | Sweet finish | arabic desserts coffee dessert platter |

## After adding real photos

```bash
php artisan migrate            # adds offers.picture_id (new migration included)
php artisan db:seed            # or: php artisan migrate:fresh --seed
```

The seeder prints a warning listing any meal/offer still missing a photo, so
you always know exactly what's left to source — nothing fails silently.
