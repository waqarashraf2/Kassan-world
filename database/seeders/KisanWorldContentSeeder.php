<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Models\Video;
use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KisanWorldContentSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::updateOrCreate(
            ['email' => 'content@kisanworld.pk'],
            [
                'name' => 'Ch. Iftikhar Sandhu',
                'phone' => '03226780242',
                'role' => 'customer',
                'password' => Hash::make(Str::random(40)),
            ]
        );

        $fertilizers = Category::updateOrCreate(
            ['slug' => 'fertilizers'],
            ['name' => 'Fertilizers', 'name_ur' => 'کھادیں', 'description' => 'Crop nutrition, micronutrients and soil improvement products.']
        );

        $seeds = Category::updateOrCreate(
            ['slug' => 'seeds'],
            ['name' => 'Seeds', 'name_ur' => 'بیج', 'description' => 'Quality seeds for major crops and vegetables.']
        );

        $cropProtection = Category::updateOrCreate(
            ['slug' => 'crop-protection'],
            ['name' => 'Crop Protection', 'name_ur' => 'فصل کا تحفظ', 'description' => 'Products used in responsible crop protection programs.']
        );

        $tools = Category::updateOrCreate(
            ['slug' => 'farming-tools'],
            ['name' => 'Farming Tools', 'name_ur' => 'زرعی اوزار', 'description' => 'Practical tools for farms, orchards and gardens.']
        );

        $mark6 = Product::updateOrCreate(
            ['slug' => 'mark6-fertilizer'],
            [
                'category_id' => $fertilizers->id,
                'name' => 'Mark6 Fertilizer',
                'name_ur' => 'مارک 6 کھاد',
                'sku' => 'KW-MARK6-25KG',
                'short_description' => 'Imported South Korean crop enhancement technology formulated with melted zeolite, micronutrients, hormones and enzymes for crops, vegetables, orchards and fish farms.',
                'description' => $this->mark6ProductEnglish(),
                'description_ur' => $this->mark6ProductUrdu(),
                'price' => 4000,
                'discount_price' => null,
                'stock_quantity' => 100,
                'manage_stock' => true,
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'Mark6 Fertilizer Price, Benefits and Use in Pakistan',
                'meta_description' => 'Learn about Mark6 Fertilizer, its 25 kg pack, application method, micronutrients and use for crops, orchards, vegetables and fish farms.',
            ]
        );
        $this->seedProductImages($mark6);

        $testProducts = [
            [$fertilizers, 'Balanced NPK Fertilizer 20-20-20', 'متوازن این پی کے کھاد', 'KW-NPK-202020', 2850],
            [$fertilizers, 'Zinc Micronutrient 10 kg', 'زنک مائیکرو نیوٹرینٹ', 'KW-ZINC-10', 2350],
            [$fertilizers, 'Boron Crop Supplement', 'بوران کراپ سپلیمنٹ', 'KW-BORON-01', 1850],
            [$fertilizers, 'Organic Soil Conditioner', 'نامیاتی زمین اصلاح کار', 'KW-SOIL-25', 3200],
            [$seeds, 'Certified Wheat Seed', 'تصدیق شدہ گندم کا بیج', 'KW-SEED-WHEAT', 5400],
            [$seeds, 'Hybrid Rice Seed', 'ہائبرڈ دھان کا بیج', 'KW-SEED-RICE', 6750],
            [$cropProtection, 'Broad Spectrum Fungicide', 'فنگس کش دوا', 'KW-FUNG-500', 1950],
            [$cropProtection, 'Responsible Insect Control', 'کیڑوں کے تدارک کی دوا', 'KW-INSECT-500', 2250],
            [$tools, 'Manual Knapsack Sprayer', 'دستی سپرے پمپ', 'KW-SPRAYER-16', 6800],
            [$tools, 'Orchard Pruning Shears', 'باغ کی کٹائی کی قینچی', 'KW-SHEARS-01', 1450],
        ];

        foreach ($testProducts as $index => [$category, $name, $nameUr, $sku, $price]) {
            $product = Product::updateOrCreate(
                ['sku' => $sku],
                [
                    'category_id' => $category->id,
                    'name' => $name,
                    'name_ur' => $nameUr,
                    'short_description' => 'Testing catalog product for the KISANWORLD storefront. Replace this text with the final approved product information before launch.',
                    'description' => 'This sample product is included to test product cards, pagination, cart and checkout. Confirm specifications, directions, pricing and regulatory information before publishing it as a real sale item.',
                    'price' => $price,
                    'discount_price' => $index % 3 === 0 ? $price - 250 : null,
                    'stock_quantity' => 25 + $index,
                    'manage_stock' => true,
                    'is_featured' => $index < 4,
                    'is_active' => true,
                    'meta_title' => $name.' | KISANWORLD',
                    'meta_description' => 'Explore '.$name.' at KISANWORLD. Sample listing for storefront testing.',
                ]
            );
            $this->seedProductImages($product);
        }

        $englishCategory = BlogCategory::updateOrCreate(
            ['slug' => 'crop-enhancement-english'],
            ['name' => 'Crop Enhancement', 'name_ur' => 'فصل کی بہتری', 'language' => 'en', 'description' => 'Crop nutrition and soil improvement articles.']
        );

        $urduCategory = BlogCategory::updateOrCreate(
            ['slug' => 'crop-enhancement-urdu'],
            ['name' => 'Crop Enhancement Urdu', 'name_ur' => 'فصل کی بہتری', 'language' => 'ur', 'description' => 'فصل، زمین اور پانی کی بہتری سے متعلق مضامین۔']
        );

        $englishBlog = Blog::updateOrCreate(
            ['language' => 'en', 'title' => 'Mark6 Fertilizer: A New Revolution in Agriculture'],
            [
                'blog_category_id' => $englishCategory->id,
                'author_id' => $author->id,
                'slug' => 'mark6-fertilizer-a-new-revolution-in-agriculture',
                'excerpt' => 'An overview of Mark6 Fertilizer, its melted zeolite base, micronutrient profile, application methods and reported uses across crops, orchards, vegetables and fish farms.',
                'content' => $this->englishArticle(),
                'featured_image' => 'logos and images/hero-1920.jpg',
                'featured_image_alt' => 'Agricultural field representing Mark6 Fertilizer use',
                'status' => 'published',
                'published_at' => now()->subDay(),
                'meta_title' => 'Mark6 Fertilizer Benefits, Price and Application Guide',
                'meta_description' => 'Read the Mark6 Fertilizer guide covering composition, application, 25 kg pack price, crop benefits, saline land and fish farm use.',
            ]
        );
        $englishBlog->updateQuietly(['slug' => 'mark6-fertilizer-a-new-revolution-in-agriculture']);

        $urduBlog = Blog::updateOrCreate(
            ['language' => 'ur', 'title' => 'مارک 6 کھاد: زراعت میں ایک نیا انقلاب'],
            [
                'blog_category_id' => $urduCategory->id,
                'author_id' => $author->id,
                'slug' => 'mark6-khad-zaraat-mein-naya-inqilab',
                'excerpt' => 'مارک 6 کھاد کی خصوصیات، اجزاء، طریقہ استعمال، فصلوں، باغات، سبزیوں اور فش فارم میں استعمال سے متعلق تفصیلی رہنمائی۔',
                'content' => $this->urduArticle(),
                'featured_image' => 'logos and images/hero-1920.jpg',
                'featured_image_alt' => 'مارک 6 کھاد کے استعمال کی نمائندہ زرعی تصویر',
                'status' => 'published',
                'published_at' => now()->subDay(),
                'meta_title' => 'مارک 6 کھاد کے فوائد، قیمت اور طریقہ استعمال',
                'meta_description' => 'مارک 6 کھاد کی 25 کلو پیکنگ، اجزاء، زمین و پانی کی اصلاح، فصلوں اور فش فارم کیلئے استعمال کی مکمل معلومات۔',
            ]
        );
        $urduBlog->updateQuietly(['slug' => 'mark6-khad-zaraat-mein-naya-inqilab']);

        Video::updateOrCreate(
            ['youtube_video_id' => 'fSwKMJYrbY4'],
            [
                'category' => 'Fertilizer Guide',
                'title' => 'Mark6 Fertilizer Agriculture Guide',
                'slug' => 'mark6-fertilizer-agriculture-guide',
                'description' => 'A KISANWORLD video related to Mark6 Fertilizer and its agricultural use.',
                'youtube_url' => 'https://youtu.be/fSwKMJYrbY4',
                'thumbnail_alt' => 'Mark6 Fertilizer agriculture guide video',
                'is_active' => true,
                'published_at' => now()->subDay(),
                'meta_title' => 'Mark6 Fertilizer Video Guide | KISANWORLD',
                'meta_description' => 'Watch the KISANWORLD Mark6 Fertilizer agriculture guide.',
            ]
        );

        $settings = [
            ['key' => 'site_phone', 'value' => '03226780242', 'type' => 'phone', 'group' => 'contact'],
            ['key' => 'site_email', 'value' => 'kisanworld.magazine@gmail.com', 'type' => 'email', 'group' => 'contact'],
            ['key' => 'site_address', 'value' => 'KISANWORLD Marketing, Lahore, Pakistan', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'whatsapp_url', 'value' => 'https://wa.me/923226780242', 'type' => 'url', 'group' => 'social'],
            ['key' => 'youtube_url', 'value' => 'https://youtu.be/fSwKMJYrbY4', 'type' => 'url', 'group' => 'social'],
            ['key' => 'facebook_url', 'value' => '', 'type' => 'url', 'group' => 'social'],
            ['key' => 'instagram_url', 'value' => '', 'type' => 'url', 'group' => 'social'],
            ['key' => 'about_title', 'value' => 'Serving Pakistan’s farmers with products, knowledge and practical support.', 'type' => 'text', 'group' => 'about'],
            ['key' => 'about_intro', 'value' => 'KISANWORLD Marketing is an agriculture-focused platform based in Lahore. We connect farmers with agricultural products, practical crop information, Urdu and English learning resources, videos and digital magazines.', 'type' => 'textarea', 'group' => 'about'],
            ['key' => 'about_distribution', 'value' => 'Our distribution support reaches farmers across Pakistan through goods transport, while our digital platform makes agricultural information easier to discover and understand.', 'type' => 'textarea', 'group' => 'about'],
            ['key' => 'about_commitment', 'value' => 'We aim to present clear product information, responsible usage guidance and farmer-friendly support. Product claims, application rates and prices should always be confirmed for the farm, crop and season before use.', 'type' => 'textarea', 'group' => 'about'],
        ];

        foreach ($settings as $setting) {
            WebsiteSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        Cache::forget('public_website_settings');
    }

    private function mark6ProductEnglish(): string
    {
        return <<<'TEXT'
Mark6 Fertilizer is presented by KISANWORLD Marketing Lahore as an imported South Korean crop enhancement technology. It is a water-soluble brown powder formulated with a melted zeolite base, micronutrients, hormones and enzymes.

Pack size: 25 kg
Indicative retail price: PKR 4,000 per bag. Price may change.

Composition highlights:
- Zeolite processed at 1300°C
- Zinc, Boron, Calcium, Magnesium and Manganese
- Traces of Iron, Potassium and Molybdenum

Suggested uses in the supplied product information include crops, vegetables, orchards and fish farms. It may be applied with irrigation water or mixed with granular fertilizers such as DAP and Urea.

Application guidance supplied by the distributor:
- Three bags per acre for crops, vegetables, orchards and fish farms
- Apply through broadcasting followed immediately by irrigation, flood irrigation, tube-well tank or water drum
- Do not scatter the powder on dry soil unless irrigation will follow immediately

For details and current booking information, contact KISANWORLD Marketing Lahore on WhatsApp: 03226780242.

Important: Agricultural results vary by soil, water, crop, weather and management. Confirm the application rate and suitability with a qualified local agriculture professional before use.
TEXT;
    }

    private function seedProductImages(Product $product): void
    {
        foreach (range(1, 3) as $index) {
            ProductImage::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'path' => "product-media/demo/agriculture-field-{$index}.jpg",
                ],
                [
                    'alt_text' => $product->name." agriculture view {$index}",
                    'sort_order' => $index - 1,
                    'is_primary' => $index === 1,
                ]
            );
        }
    }

    private function mark6ProductUrdu(): string
    {
        return <<<'TEXT'
مارک 6 کھاد کو کسان ورلڈ مارکیٹنگ لاہور جنوبی کوریا سے درآمد شدہ کراپ انہانسمنٹ ٹیکنالوجی کے طور پر پیش کرتی ہے۔ یہ پانی میں حل پذیر بھورے رنگ کا پاؤڈر ہے جس میں پگھلا ہوا ذیولائٹ، مائیکرو نیوٹرینٹس، ہارمونز اور اینزائمز شامل ہیں۔

پیکنگ: 25 کلوگرام
عام قیمت: 4000 روپے فی بیگ، تاہم قیمت وقت کے ساتھ تبدیل ہو سکتی ہے۔

اہم اجزاء:
- 1300 ڈگری سینٹی گریڈ پر تیار کردہ ذیولائٹ
- زنک، بوران، کیلشیم، میگنیشیم اور میگنانیز
- آئرن، پوٹاشیم اور مولبڈینم کی متوازن مقدار

فراہم کردہ معلومات کے مطابق اسے فصلوں، سبزیوں، باغات اور فش فارم میں استعمال کیا جا سکتا ہے۔ اسے آبپاشی کے پانی کے ساتھ یا ڈی اے پی اور یوریا جیسی دانے دار کھادوں کے ساتھ استعمال کیا جا سکتا ہے۔

تجویز کردہ طریقہ استعمال:
- فصلوں، سبزیوں، باغات اور فش فارم کیلئے تین بیگ فی ایکڑ
- چھٹہ دینے کے فوراً بعد آبپاشی، فلڈ اریگیشن، ٹیوب ویل ٹینک یا پانی کے ڈرم کے ذریعے استعمال
- خشک مٹی پر پاؤڈر ڈالنے سے گریز کریں جب تک فوری آبپاشی نہ ہو

تفصیلات اور موجودہ بکنگ کیلئے کسان ورلڈ مارکیٹنگ لاہور سے واٹس ایپ 03226780242 پر رابطہ کریں۔

اہم نوٹ: زرعی نتائج زمین، پانی، فصل، موسم اور انتظام کے لحاظ سے مختلف ہو سکتے ہیں۔ استعمال سے پہلے مقامی زرعی ماہر سے مقدار اور موزونیت کی تصدیق کریں۔
TEXT;
    }

    private function englishArticle(): string
    {
        return <<<'HTML'
<p><strong>Written by Ch. Iftikhar Sandhu (Quaid-e-Azam Gold Medalist)</strong></p>
<p>Mark6 Fertilizer is described by KISANWORLD Marketing Lahore as an imported crop enhancement technology from South Korea. The supplied product information presents it as a water-soluble brown powder containing a melted zeolite base, hormones, enzymes and a broad micronutrient profile.</p>

<h2>Key features and composition</h2>
<h3>Melted zeolite base</h3>
<p>The zeolite base is processed at approximately 1300°C to produce a porous nutrient carrier. According to the supplied information, this structure is intended to hold nutrients, interact with salts and support soil aeration.</p>
<h3>Micronutrient profile</h3>
<p>The formulation includes Zinc, Boron, Calcium, Magnesium and Manganese, with balanced traces of Iron, Potassium, Molybdenum and plant-supporting compounds.</p>
<h3>Soil and water support</h3>
<p>Mark6 is promoted for use in saline or alkaline land and with hard, brackish irrigation water. The supplied material states that it can support soil structure, cation exchange capacity and nutrient availability.</p>

<h2>Application methods</h2>
<ul>
<li><strong>Standalone:</strong> Apply it directly to the field with immediate irrigation.</li>
<li><strong>Fertilizer mix:</strong> Combine it with granular fertilizers such as DAP or Urea.</li>
<li><strong>Irrigation:</strong> Add it through a tube-well hood, water tank, water drum or flood-irrigation channel.</li>
</ul>
<p>Do not scatter the powder on dry soil without immediate irrigation. Water is required to distribute and activate the material.</p>

<h2>Reported agricultural uses</h2>
<p>The distributor’s material discusses use in wheat, rice, millet, pulses, oil crops, potato, sugarcane, seasonal vegetables and fruit orchards including citrus, mango, guava, strawberry, papaya, banana, apple and grapes.</p>
<p>Reported benefits include recovery from weather stress, deeper root growth, stronger stems, improved nutrient efficiency and reduced lodging. It is also promoted as a soil and water reclaimer for saline fields.</p>

<h2>Fish farm use</h2>
<p>The supplied guide recommends three bags per acre for fish ponds and states that this may reduce the requirement for DAP and Urea. Fish farmers should confirm water chemistry and application rates with a qualified aquaculture professional before use.</p>

<h2>Pack size, price and recommended rate</h2>
<ul>
<li>Pack size: 25 kg</li>
<li>Indicative retail price: PKR 4,000 per bag; price may change</li>
<li>Supplied recommendation: three bags per acre for crops, vegetables, orchards and fish farms</li>
</ul>

<h2>Safety and responsible use</h2>
<p>Agricultural performance varies with soil testing, water quality, crop variety, weather and farm management. Yield percentages and targets in promotional material should not be treated as guaranteed results. Confirm product suitability, rate and compatibility with a qualified local agriculture professional.</p>

<p><strong>Details and booking:</strong><br>Ch. Iftikhar Sandhu<br>KISANWORLD Marketing Lahore<br>WhatsApp: 03226780242</p>
HTML;
    }

    private function urduArticle(): string
    {
        return <<<'HTML'
<p><strong>تحریر: چوہدری افتخار سندھو (قائد اعظم گولڈ میڈلسٹ)</strong></p>
<p>مارک 6 کھاد کو کسان ورلڈ مارکیٹنگ لاہور جنوبی کوریا سے درآمد شدہ جدید کراپ انہانسمنٹ ٹیکنالوجی کے طور پر پیش کرتی ہے۔ فراہم کردہ معلومات کے مطابق یہ پانی میں حل پذیر بھورے رنگ کا پاؤڈر ہے جس میں پگھلا ہوا ذیولائٹ، ہارمونز، اینزائمز اور مختلف مائیکرو نیوٹرینٹس شامل ہیں۔</p>

<h2>نمایاں خصوصیات اور اجزاء</h2>
<h3>پگھلا ہوا ذیولائٹ</h3>
<p>ذیولائٹ کو تقریباً 1300 ڈگری سینٹی گریڈ پر پراسیس کیا جاتا ہے تاکہ ایک مسام دار نیوٹرینٹ کیریئر بن سکے۔ فراہم کردہ معلومات کے مطابق یہ ساخت غذائی اجزاء کو محفوظ رکھنے، نمکیات کے اثرات کم کرنے اور زمین میں ہوا کے گزر کو بہتر بنانے میں مدد دیتی ہے۔</p>
<h3>مائیکرو نیوٹرینٹس</h3>
<p>اس میں زنک، بوران، کیلشیم، میگنیشیم اور میگنانیز کے ساتھ آئرن، پوٹاشیم، مولبڈینم اور پودوں کیلئے معاون اجزاء شامل ہیں۔</p>
<h3>زمین اور پانی کی اصلاح</h3>
<p>مارک 6 کو کلراٹھی یا شور زدہ زمین اور سخت، کھارے یا کڑوے آبپاشی کے پانی میں استعمال کیلئے پیش کیا جاتا ہے۔ فراہم کردہ مواد میں زمین کی ساخت، کیشن ایکسچینج کیپیسٹی اور غذائی اجزاء کی دستیابی بہتر بنانے کا ذکر کیا گیا ہے۔</p>

<h2>طریقہ استعمال</h2>
<ul>
<li><strong>اکیلے استعمال:</strong> کھیت میں ڈالنے کے فوراً بعد آبپاشی کریں۔</li>
<li><strong>کھاد کے ساتھ:</strong> ڈی اے پی یا یوریا جیسی دانے دار کھادوں کے ساتھ ملایا جا سکتا ہے۔</li>
<li><strong>آبپاشی کے ذریعے:</strong> ٹیوب ویل کی ہودی، پانی کے ٹینک، ڈرم یا فلڈ اریگیشن کے پانی میں شامل کیا جا سکتا ہے۔</li>
</ul>
<p>خشک مٹی پر پاؤڈر ڈالنے سے گریز کریں جب تک فوری آبپاشی نہ کی جا رہی ہو۔ مواد کو پھیلانے اور فعال کرنے کیلئے پانی ضروری ہے۔</p>

<h2>زرعی استعمال</h2>
<p>فراہم کردہ معلومات میں گندم، دھان، باجرہ، دالیں، روغنی اجناس، آلو، گنا، موسمی سبزیاں اور کینو، آم، امرود، اسٹرابیری، پپیتا، کیلا، سیب اور انگور کے باغات میں استعمال کا ذکر ہے۔</p>
<p>بیان کردہ فوائد میں موسمی دباؤ سے بحالی، جڑوں کی گہرائی، تنے کی مضبوطی، غذائی اجزاء کی بہتر کارکردگی اور فصل کو گرنے سے بچانے میں مدد شامل ہیں۔ اسے شور زدہ زمین اور کھارے پانی کی اصلاح کیلئے بھی پیش کیا جاتا ہے۔</p>

<h2>فش فارم میں استعمال</h2>
<p>فراہم کردہ گائیڈ میں مچھلی کے تالاب کیلئے تین بیگ فی ایکڑ کی تجویز دی گئی ہے اور ڈی اے پی و یوریا کی ضرورت کم ہونے کا ذکر ہے۔ استعمال سے پہلے پانی کی کیمسٹری اور مقدار کی تصدیق کسی ماہر فش فارمنگ پروفیشنل سے کریں۔</p>

<h2>پیکنگ، قیمت اور مقدار</h2>
<ul>
<li>پیکنگ: 25 کلوگرام</li>
<li>عام قیمت: 4000 روپے فی بیگ، قیمت تبدیل ہو سکتی ہے</li>
<li>فراہم کردہ تجویز: فصلوں، سبزیوں، باغات اور فش فارم کیلئے تین بیگ فی ایکڑ</li>
</ul>

<h2>احتیاط اور ذمہ دارانہ استعمال</h2>
<p>زرعی نتائج زمین کے ٹیسٹ، پانی کی کوالٹی، فصل کی قسم، موسم اور فارم مینجمنٹ کے مطابق مختلف ہوتے ہیں۔ تشہیری مواد میں بیان کردہ پیداوار یا فیصد کو یقینی نتیجہ نہ سمجھا جائے۔ استعمال سے پہلے مقامی زرعی ماہر سے موزونیت، مقدار اور دوسری کھادوں کے ساتھ مطابقت کی تصدیق کریں۔</p>

<p><strong>تفصیلات اور بکنگ:</strong><br>چوہدری افتخار سندھو<br>کسان ورلڈ مارکیٹنگ لاہور<br>واٹس ایپ: 03226780242</p>
HTML;
    }
}
