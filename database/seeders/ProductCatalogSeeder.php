<?php

namespace Database\Seeders;

use App\Domain\Models\Certification;
use App\Domain\Models\HsCode;
use App\Domain\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categories
        $categories = [
            [
                'name' => 'Fruits & Vegetables',
                'slug' => 'fruits-vegetables',
                'icon' => 'pi pi-apple',
                'children' => [
                    ['name' => 'Avocados', 'slug' => 'avocados'],
                    ['name' => 'Berries', 'slug' => 'berries'],
                    ['name' => 'Citrus', 'slug' => 'citrus'],
                    ['name' => 'Mangoes', 'slug' => 'mangoes'],
                    ['name' => 'Tomatoes', 'slug' => 'tomatoes'],
                    ['name' => 'Peppers', 'slug' => 'peppers'],
                    ['name' => 'Cucumbers', 'slug' => 'cucumbers'],
                    ['name' => 'Tropical Fruits', 'slug' => 'tropical-fruits'],
                ]
            ],
            [
                'name' => 'Grains & Cereals',
                'slug' => 'grains-cereals',
                'icon' => 'pi pi-server', // visually approximate to grain sack or bulk
                'children' => [
                    ['name' => 'Corn', 'slug' => 'corn'],
                    ['name' => 'Wheat', 'slug' => 'wheat'],
                    ['name' => 'Rice', 'slug' => 'rice'],
                    ['name' => 'Soybeans', 'slug' => 'soybeans'],
                    ['name' => 'Beans', 'slug' => 'beans'],
                ]
            ],
            [
                'name' => 'Nuts & Seeds',
                'slug' => 'nuts-seeds',
                'icon' => 'pi pi-circle',
                'children' => [
                    ['name' => 'Almonds', 'slug' => 'almonds'],
                    ['name' => 'Walnuts', 'slug' => 'walnuts'],
                    ['name' => 'Pecans', 'slug' => 'pecans'],
                    ['name' => 'Chia Seeds', 'slug' => 'chia-seeds'],
                ]
            ],
            [
                'name' => 'Coffee & Cocoa',
                'slug' => 'coffee-cocoa',
                'icon' => 'pi pi-cloud', // approximate for steam/aroma
                'children' => [
                    ['name' => 'Green Coffee Beans', 'slug' => 'green-coffee'],
                    ['name' => 'Cocoa Beans', 'slug' => 'cocoa-beans'],
                ]
            ],
            [
                'name' => 'Spices & Herbs',
                'slug' => 'spices-herbs',
                'icon' => 'pi pi-star',
                'children' => [
                    ['name' => 'Chili Peppers (Dried)', 'slug' => 'dried-chili'],
                    ['name' => 'Vanilla', 'slug' => 'vanilla'],
                    ['name' => 'Oregano', 'slug' => 'oregano'],
                ]
            ]
        ];

        foreach ($categories as $cat) {
            $parent = ProductCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                    'is_active' => true,
                ]
            );

            foreach ($cat['children'] as $child) {
                ProductCategory::firstOrCreate(
                    ['slug' => $child['slug']],
                    [
                        'name' => $child['name'],
                        'parent_id' => $parent->id,
                        'is_active' => true,
                    ]
                );
            }
        }

        // 2. HS Codes (Sample expansion)
        $hsCodes = [
            ['code' => '080440', 'desc' => 'Avocados, fresh or dried', 'chapter' => '08'],
            ['code' => '081040', 'desc' => 'Cranberries, bilberries and other fruits of the genus Vaccinium, fresh', 'chapter' => '08'],
            ['code' => '080510', 'desc' => 'Oranges, fresh or dried', 'chapter' => '08'],
            ['code' => '080550', 'desc' => 'Lemons and limes, fresh or dried', 'chapter' => '08'],
            ['code' => '070200', 'desc' => 'Tomatoes, fresh or chilled', 'chapter' => '07'],
            ['code' => '090111', 'desc' => 'Coffee, not roasted, not decaffeinated', 'chapter' => '09'],
            ['code' => '100590', 'desc' => 'Maize (corn), other than seed', 'chapter' => '10'],
        ];

        foreach ($hsCodes as $hs) {
             HsCode::firstOrCreate(
                ['code' => $hs['code']],
                [
                    'description' => $hs['desc'],
                    'chapter' => $hs['chapter'],
                    'is_active' => true,
                ]
            );
        }

        // 3. Certifications
        $certs = [
            // Organic & Environmental
            ['slug' => 'usda-organic', 'name' => 'USDA Organic', 'authority' => 'USDA', 'cat' => 'organic', 'markets' => ['US']],
            ['slug' => 'eu-organic', 'name' => 'EU Organic', 'authority' => 'European Commission', 'cat' => 'organic', 'markets' => ['EU']],
            ['slug' => 'jas', 'name' => 'JAS (Japanese Agricultural Standard)', 'authority' => 'MAFF', 'cat' => 'organic', 'markets' => ['JP']],
            ['slug' => 'rainforest-alliance', 'name' => 'Rainforest Alliance', 'authority' => 'Rainforest Alliance', 'cat' => 'environmental', 'markets' => ['Global']],
            ['slug' => 'bird-friendly', 'name' => 'Bird Friendly', 'authority' => 'Smithsonian', 'cat' => 'environmental', 'markets' => ['US']],
            
            // Food Safety & Quality
            ['slug' => 'global-gap', 'name' => 'Global G.A.P.', 'authority' => 'GlobalGAP', 'cat' => 'quality', 'markets' => ['EU', 'Global']],
            ['slug' => 'primus-gfs', 'name' => 'PrimusGFS', 'authority' => 'PrimusLabs', 'cat' => 'safety', 'markets' => ['US', 'Global']],
            ['slug' => 'haccp', 'name' => 'HACCP', 'authority' => 'Various', 'cat' => 'safety', 'markets' => ['Global']],
            ['slug' => 'sqf', 'name' => 'SQF (Safe Quality Food)', 'authority' => 'SQFI', 'cat' => 'safety', 'markets' => ['US', 'Global']],
            ['slug' => 'brcgs', 'name' => 'BRCGS Food Safety', 'authority' => 'BRCGS', 'cat' => 'safety', 'markets' => ['EU', 'Global']],
            ['slug' => 'fsma', 'name' => 'FSMA Compliant', 'authority' => 'FDA', 'cat' => 'safety', 'markets' => ['US']],
            
            // Social & Ethical
            ['slug' => 'fair-trade', 'name' => 'Fair Trade Certified', 'authority' => 'Fair Trade USA', 'cat' => 'social', 'markets' => ['US', 'EU']],
            ['slug' => 'fairtrade-international', 'name' => 'Fairtrade International', 'authority' => 'Fairtrade International', 'cat' => 'social', 'markets' => ['Global']],
            ['slug' => 'smetas-sedex', 'name' => 'SMETA (Sedex)', 'authority' => 'Sedex', 'cat' => 'social', 'markets' => ['Global']],
            
            // Religious
            ['slug' => 'kosher', 'name' => 'Kosher', 'authority' => 'Various', 'cat' => 'religious', 'markets' => ['Global']],
            ['slug' => 'halal', 'name' => 'Halal', 'authority' => 'Various', 'cat' => 'religious', 'markets' => ['Global']],
            
            // Origin & Other
            ['slug' => 'fda-registered', 'name' => 'FDA Facility Registration', 'authority' => 'FDA', 'cat' => 'regulatory', 'markets' => ['US']],
            ['slug' => 'non-gmo-project', 'name' => 'Non-GMO Project', 'authority' => 'Non-GMO Project', 'cat' => 'quality', 'markets' => ['US']],
        ];

        foreach ($certs as $cert) {
            Certification::firstOrCreate(
                ['slug' => $cert['slug']],
                [
                    'name' => $cert['name'],
                    'issuing_authority' => $cert['authority'],
                    'category' => $cert['cat'],
                    'required_for_markets' => $cert['markets'],
                ]
            );
        }
    }
}
