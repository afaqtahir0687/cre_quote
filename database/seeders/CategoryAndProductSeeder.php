<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Support\Str;

class CategoryAndProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shop = Shop::first();
        $shopId = $shop ? $shop->id : 1;

        // 1. STANDARD UNITS
        $unitsData = [
            ['name' => 'Piece', 'code' => 'pcs'],
            ['name' => 'Square Meter', 'code' => 'sqm'],
            ['name' => 'Meter', 'code' => 'm'],
            ['name' => 'Roll', 'code' => 'roll'],
            ['name' => 'Box', 'code' => 'box'],
            ['name' => 'Packet', 'code' => 'pkt'],
            ['name' => 'Set', 'code' => 'set'],
            ['name' => 'Hour', 'code' => 'hr'],
        ];

        $units = [];
        foreach ($unitsData as $u) {
            $unit = Unit::firstOrCreate(
                ['shop_id' => $shopId, 'code' => $u['code']],
                ['name' => $u['name'], 'status' => true]
            );
            $units[$u['code']] = $unit;
        }

        // 2. CATEGORIES
        $categoriesData = [
            [
                'name' => 'Banners & Flex Printing',
                'description' => 'Indoor and outdoor flex banners, rollup graphics, vinyl mesh, and backdrops.'
            ],
            [
                'name' => 'Signage & Acrylic Boards',
                'description' => '3D channel letter signage, acrylic letters, neon signs, and lightboxes.'
            ],
            [
                'name' => 'Vehicle Branding & Stickers',
                'description' => 'Die-cut vinyl stickers, car wrap films, frosted glass films, and reflective sheets.'
            ],
            [
                'name' => 'Stationery & Business Cards',
                'description' => 'Premium business cards, letterheads, presentation folders, and envelopes.'
            ],
            [
                'name' => 'Display Stands & Fixtures',
                'description' => 'Roll-up banner stands, pop-up backdrops, X-stands, and promotional counters.'
            ],
            [
                'name' => 'Installation & Design Services',
                'description' => 'Graphic design, sign fixing, hoarding installation, and site survey services.'
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $category = Category::firstOrCreate(
                ['shop_id' => $shopId, 'slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'status' => true,
                ]
            );
            $categories[$cat['name']] = $category;
        }

        // 3. PRODUCTS & SERVICES
        $productsData = [
            // Banners & Flex
            [
                'name' => 'Frontlit Flex Banner 440gsm',
                'sku' => 'PRD-BNR101',
                'category' => 'Banners & Flex Printing',
                'unit' => 'sqm',
                'type' => 'product',
                'purchase_price' => 12.00,
                'sale_price' => 25.00,
                'tax_percentage' => 15.00,
                'initial_stock' => 500,
                'stock' => 380, // Healthy: 76% remaining
                'description' => 'High quality matte frontlit flex banner suitable for outdoor shop hoardings and billboards.',
            ],
            [
                'name' => 'Backlit Banner Film (Lightbox)',
                'sku' => 'PRD-BNR102',
                'category' => 'Banners & Flex Printing',
                'unit' => 'sqm',
                'type' => 'product',
                'purchase_price' => 22.00,
                'sale_price' => 45.00,
                'tax_percentage' => 15.00,
                'initial_stock' => 200,
                'stock' => 45, // Low stock: 22.5% remaining (<= 30%)
                'description' => 'Translucent backlit banner designed for illuminated signage and indoor glow boxes.',
            ],

            // Signage & Acrylic
            [
                'name' => 'Acrylic Sheet 3mm Cast Transparent',
                'sku' => 'PRD-ACR201',
                'category' => 'Signage & Acrylic Boards',
                'unit' => 'pcs',
                'type' => 'product',
                'purchase_price' => 65.00,
                'sale_price' => 120.00,
                'tax_percentage' => 15.00,
                'initial_stock' => 60,
                'stock' => 42, // Healthy: 70% remaining
                'description' => 'Premium cast clear acrylic sheet size 122x244cm for laser cutting and 3D letters.',
            ],
            [
                'name' => '3D LED Channel Letters (Per cm height)',
                'sku' => 'PRD-ACR202',
                'category' => 'Signage & Acrylic Boards',
                'unit' => 'pcs',
                'type' => 'product',
                'purchase_price' => 8.50,
                'sale_price' => 18.00,
                'tax_percentage' => 15.00,
                'initial_stock' => 100,
                'stock' => 18, // Low stock: 18% remaining (<= 30%)
                'description' => 'Stainless steel and acrylic face 3D letter with waterproof internal LED modules.',
            ],
            [
                'name' => 'Aluminum Composite Panel (ACP) Silver 4mm',
                'sku' => 'PRD-ACR203',
                'category' => 'Signage & Acrylic Boards',
                'unit' => 'pcs',
                'type' => 'product',
                'purchase_price' => 95.00,
                'sale_price' => 165.00,
                'tax_percentage' => 15.00,
                'initial_stock' => 40,
                'stock' => 0, // Out of stock (0 remaining)
                'description' => 'Metallic silver 0.3mm skin ACP sheet for shop front cladding and durable facades.',
            ],

            // Stickers & Vinyl
            [
                'name' => 'Monomeric Gloss White Vinyl Sticker',
                'sku' => 'PRD-STK301',
                'category' => 'Vehicle Branding & Stickers',
                'unit' => 'sqm',
                'type' => 'product',
                'purchase_price' => 10.00,
                'sale_price' => 28.00,
                'tax_percentage' => 15.00,
                'initial_stock' => 300,
                'stock' => 240, // Healthy: 80% remaining
                'description' => 'Self-adhesive digital print vinyl with glossy finish and high opacity grey adhesive.',
            ],
            [
                'name' => 'Frosted Window Privacy Film',
                'sku' => 'PRD-STK302',
                'category' => 'Vehicle Branding & Stickers',
                'unit' => 'sqm',
                'type' => 'product',
                'purchase_price' => 14.00,
                'sale_price' => 35.00,
                'tax_percentage' => 15.00,
                'initial_stock' => 150,
                'stock' => 30, // Low stock: 20% remaining (<= 30%)
                'description' => 'Dusted etched crystal film for glass partition decor, office privacy and computer cutting.',
            ],

            // Stationery & Business Cards
            [
                'name' => 'Business Cards 350gsm Velvet Matt Lamination',
                'sku' => 'PRD-STN401',
                'category' => 'Stationery & Business Cards',
                'unit' => 'box',
                'type' => 'product',
                'purchase_price' => 25.00,
                'sale_price' => 60.00,
                'tax_percentage' => 15.00,
                'initial_stock' => 80,
                'stock' => 55, // Healthy: 68.7% remaining
                'description' => 'Box of 1000 double-sided full color business cards printed on 350gsm art card with velvet finish.',
            ],
            [
                'name' => 'Flyers A5 150gsm Gloss Art Paper (Pack of 500)',
                'sku' => 'PRD-STN402',
                'category' => 'Stationery & Business Cards',
                'unit' => 'pkt',
                'type' => 'product',
                'purchase_price' => 45.00,
                'sale_price' => 95.00,
                'tax_percentage' => 15.00,
                'initial_stock' => 50,
                'stock' => 0, // Out of stock
                'description' => '500 sheets of A5 150gsm gloss art paper double-sided offset promotional flyers.',
            ],

            // Display Stands
            [
                'name' => 'Roll-Up Stand Aluminum Base 85x200cm',
                'sku' => 'PRD-STD501',
                'category' => 'Display Stands & Fixtures',
                'unit' => 'pcs',
                'type' => 'product',
                'purchase_price' => 38.00,
                'sale_price' => 85.00,
                'tax_percentage' => 15.00,
                'initial_stock' => 75,
                'stock' => 52, // Healthy: 69% remaining
                'description' => 'Retractable aluminum roll-up mechanism with padded carry bag and top snap rail.',
            ],
            [
                'name' => 'Promotional Pop-Up Curved Counter',
                'sku' => 'PRD-STD502',
                'category' => 'Display Stands & Fixtures',
                'unit' => 'pcs',
                'type' => 'product',
                'purchase_price' => 180.00,
                'sale_price' => 350.00,
                'tax_percentage' => 15.00,
                'initial_stock' => 25,
                'stock' => 6, // Low stock: 24% remaining (<= 30%)
                'description' => 'Portable exhibition reception desk with magnetic graphic wrap and internal shelf.',
            ],

            // Services (Unlimited / No Stock)
            [
                'name' => 'Professional Graphic Design & Layouting',
                'sku' => 'SRV-DSG601',
                'category' => 'Installation & Design Services',
                'unit' => 'hr',
                'type' => 'service',
                'purchase_price' => 0.00,
                'sale_price' => 50.00,
                'tax_percentage' => 15.00,
                'initial_stock' => 0,
                'stock' => 0,
                'description' => 'Custom creative concept design, typography, logo vectorization, and print-ready prepress artwork.',
            ],
            [
                'name' => 'On-Site Signboard Installation & Scaffolding',
                'sku' => 'SRV-INS602',
                'category' => 'Installation & Design Services',
                'unit' => 'set',
                'type' => 'service',
                'purchase_price' => 50.00,
                'sale_price' => 150.00,
                'tax_percentage' => 15.00,
                'initial_stock' => 0,
                'stock' => 0,
                'description' => 'Outdoor site survey, steel frame mounting, electrical hookup, and signage installation with certified safety crew.',
            ],
        ];

        foreach ($productsData as $prod) {
            $cat = $categories[$prod['category']] ?? null;
            $unit = $units[$prod['unit']] ?? null;

            Product::updateOrCreate(
                ['shop_id' => $shopId, 'sku' => $prod['sku']],
                [
                    'category_id' => $cat?->id,
                    'unit_id' => $unit?->id,
                    'name' => $prod['name'],
                    'type' => $prod['type'],
                    'purchase_price' => $prod['purchase_price'],
                    'sale_price' => $prod['sale_price'],
                    'price' => $prod['sale_price'],
                    'tax_percentage' => $prod['tax_percentage'],
                    'initial_stock' => $prod['initial_stock'],
                    'stock' => $prod['stock'],
                    'description' => $prod['description'],
                ]
            );
        }
    }
}

