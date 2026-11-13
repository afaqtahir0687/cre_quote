<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Shop;
use App\Models\CmsSolution;
use App\Models\ProposalOption;
use Illuminate\Support\Str;

class CrelogicsServicesSeeder extends Seeder
{
    /**
     * Seed Crelogics authentic services from crelogics.com
     */
    public function run(): void
    {
        $shop = Shop::first();
        $shopId = $shop ? $shop->id : 1;

        // 1. UNITS
        $unitsMap = [];
        $unitsList = [
            ['name' => 'Piece', 'code' => 'pcs'],
            ['name' => 'Square Meter', 'code' => 'sqm'],
            ['name' => 'Meter', 'code' => 'm'],
            ['name' => 'Box', 'code' => 'box'],
            ['name' => 'Set', 'code' => 'set'],
            ['name' => 'Hour', 'code' => 'hr'],
            ['name' => 'Job', 'code' => 'job'],
        ];

        foreach ($unitsList as $u) {
            $unit = Unit::firstOrCreate(
                ['shop_id' => $shopId, 'code' => $u['code']],
                ['name' => $u['name'], 'status' => true]
            );
            $unitsMap[$u['code']] = $unit;
        }

        // 2. CATEGORIES (from crelogics.com service domains)
        $categoriesList = [
            [
                'name' => 'Commercial Printing & Stationery',
                'description' => 'Business cards, letterheads, presentation folders, envelopes, invoices, brochures, and corporate catalogs.'
            ],
            [
                'name' => 'Large Format & Outdoor Advertising',
                'description' => 'Roll-up banners, pop-up backdrops, flex billboards, vinyl stickers, window frosted film, and floor graphics.'
            ],
            [
                'name' => 'Signage & 3D Fabrication',
                'description' => '3D acrylic letters, LED illuminated channel letters, neon signage, shop fascia boards, and reception logos.'
            ],
            [
                'name' => 'Custom Packaging & Branded Boxes',
                'description' => 'Rigid luxury gift boxes, corrugated shipping cartons, food packaging, die-cut product boxes, and paper bags.'
            ],
            [
                'name' => 'Corporate Gifts & Promotional Merchandise',
                'description' => 'Executive diaries, laser-engraved metal pens, branded tumblers & mugs, custom USB drives, and crystal awards.'
            ],
            [
                'name' => 'Branded Apparel & Uniforms',
                'description' => 'Embroidered polo shirts, screen-printed t-shirts, industrial workwear overalls, safety vests, caps, and lanyards.'
            ],
            [
                'name' => 'Vehicle Branding & Fleet Graphics',
                'description' => 'Commercial van graphics, truck wraps, magnetic door signs, and perforated one-way vision window graphics.'
            ],
            [
                'name' => 'Exhibition & Event Branding',
                'description' => 'Modular trade show booths, media backdrops, promotional podium counters, teardrop flags, and badge printing.'
            ],
            [
                'name' => 'Installation & Design Services',
                'description' => 'Graphic design, logo identity, prepress proofing, sign fixing, scaffolding, and site survey services.'
            ],
        ];

        $categoriesMap = [];
        foreach ($categoriesList as $cat) {
            $slug = Str::slug($cat['name']);
            $category = Category::updateOrCreate(
                ['shop_id' => $shopId, 'slug' => $slug],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'status' => true,
                ]
            );
            $categoriesMap[$cat['name']] = $category;
        }

        // 3. PRODUCTS OF TYPE 'SERVICE' (Direct booking / quote items on /services)
        $servicesData = [
            [
                'name' => 'Commercial Printing & Corporate Stationery',
                'sku' => 'SRV-PRT101',
                'category' => 'Commercial Printing & Stationery',
                'unit' => 'job',
                'sale_price' => 120.00,
                'description' => 'High-precision offset & digital commercial printing for business cards, letterheads, presentation folders, official envelopes, carbonless invoices, and marketing brochures with premium lamination & UV spot finishes.',
            ],
            [
                'name' => 'Large Format Printing & Outdoor Media',
                'sku' => 'SRV-LGF201',
                'category' => 'Large Format & Outdoor Advertising',
                'unit' => 'sqm',
                'sale_price' => 35.00,
                'description' => 'High-impact indoor and outdoor large-scale printing including frontlit & backlit flex banners, roll-up banner stands, pop-up backdrops, wall murals, window frosted film, and construction hoarding graphics with UV-resistant inks.',
            ],
            [
                'name' => '3D Acrylic & LED Channel Signage Fabrication',
                'sku' => 'SRV-SGN301',
                'category' => 'Signage & 3D Fabrication',
                'unit' => 'job',
                'sale_price' => 450.00,
                'description' => 'End-to-end custom 3D architectural signage fabrication featuring illuminated LED front-lit & backlit channel letters, acrylic dimensional logos, stainless steel signs, neon art, and outdoor commercial building fascia signboards.',
            ],
            [
                'name' => 'Custom Packaging, Rigid Boxes & Shopping Bags',
                'sku' => 'SRV-PKG401',
                'category' => 'Custom Packaging & Branded Boxes',
                'unit' => 'set',
                'sale_price' => 200.00,
                'description' => 'Turnkey custom packaging engineering including rigid luxury gift boxes with magnetic closures, corrugated shipping cartons, food-grade takeaway packaging, die-cut product boxes, and branded kraft paper shopping bags.',
            ],
            [
                'name' => 'Corporate Gifts & Promotional Merchandise',
                'sku' => 'SRV-GFT501',
                'category' => 'Corporate Gifts & Promotional Merchandise',
                'unit' => 'set',
                'sale_price' => 150.00,
                'description' => 'Executive corporate gift customization with laser engraving, screen printing, and UV printing: executive diaries, metal pens, thermal drinkware mugs, customized USB tech accessories, and crystal/wooden award trophies.',
            ],
            [
                'name' => 'Branded Corporate Apparel, Uniforms & Workwear',
                'sku' => 'SRV-APP601',
                'category' => 'Branded Apparel & Uniforms',
                'unit' => 'set',
                'sale_price' => 85.00,
                'description' => 'Complete corporate clothing and industrial uniforms: custom embroidered polo shirts, screen-printed crewneck t-shirts, heavy-duty industrial workwear overalls, high-visibility safety vests, branded caps, and customized staff lanyards.',
            ],
            [
                'name' => 'Commercial Fleet Branding & Vehicle Wraps',
                'sku' => 'SRV-VEH701',
                'category' => 'Vehicle Branding & Fleet Graphics',
                'unit' => 'job',
                'sale_price' => 300.00,
                'description' => 'Turnkey vehicle graphic solutions for delivery vans, trucks, and company fleets using premium cast vinyl with anti-scratch UV lamination, precision plotter cutting, and bubble-free professional application.',
            ],
            [
                'name' => 'Exhibition Booths, Media Walls & Event Branding',
                'sku' => 'SRV-EVT801',
                'category' => 'Exhibition & Event Branding',
                'unit' => 'job',
                'sale_price' => 500.00,
                'description' => 'Comprehensive exhibition setup services including modular trade show booth design & fabrication, curved media walls, step-and-repeat backdrops, promotional podium counters, teardrop flags, and on-site event assembly.',
            ],
            [
                'name' => 'Professional Graphic Design & Brand Identity',
                'sku' => 'SRV-DSG601',
                'category' => 'Installation & Design Services',
                'unit' => 'hr',
                'sale_price' => 50.00,
                'description' => 'Full-service creative graphic design studio: logo design, brand guideline manuals, corporate profiles, marketing brochures, vector artwork redraws, color separation, and prepress proofing.',
            ],
            [
                'name' => 'On-Site Signboard Installation & Scaffolding',
                'sku' => 'SRV-INS602',
                'category' => 'Installation & Design Services',
                'unit' => 'set',
                'sale_price' => 150.00,
                'description' => 'Certified structural site survey, steel frame mounting, high-altitude crane & scaffolding access, electrical hookup, and signage installation with complete occupational safety compliance.',
            ],
        ];

        foreach ($servicesData as $srv) {
            $cat = $categoriesMap[$srv['category']] ?? null;
            $unit = $unitsMap[$srv['unit']] ?? null;

            Product::updateOrCreate(
                ['shop_id' => $shopId, 'sku' => $srv['sku']],
                [
                    'category_id' => $cat?->id,
                    'unit_id' => $unit?->id,
                    'name' => $srv['name'],
                    'type' => 'service',
                    'purchase_price' => 0.00,
                    'sale_price' => $srv['sale_price'],
                    'price' => $srv['sale_price'],
                    'tax_percentage' => 15.00,
                    'initial_stock' => 0,
                    'stock' => 0,
                    'description' => $srv['description'],
                ]
            );
        }

        // 4. CMS SOLUTIONS (High-level Industry Solutions on /services and Homepage)
        $cmsSolutionsData = [
            [
                'tag' => 'Commercial Printing',
                'title' => 'Commercial Printing & Corporate Stationery',
                'description' => 'Precision offset and high-speed digital printing engineered for corporate branding. We deliver crisp text, true-to-life CMYK/Pantone colors, luxury paper stocks, and specialty finishes like spot UV and foil stamping.',
                'checklist' => [
                    'Premium business cards, letterheads, and presentation folders',
                    'Corporate brochures, product catalogs, and company profiles',
                    'Numbered carbonless NCR invoice books and delivery receipts',
                    'Flexible print runs from short-run digital to mass offset',
                ],
                'icon_class' => 'fas fa-print',
                'sort_order' => 1,
            ],
            [
                'tag' => 'Large Format Print',
                'title' => 'Large Format Printing & Outdoor Media',
                'description' => 'High-visibility visual solutions built to captivate audiences. Powered by industrial eco-solvent and UV flatbed printers for outdoor durability, weather resistance, and vibrant resolution.',
                'checklist' => [
                    'Heavy-duty frontlit & backlit flex banners and billboards',
                    'Retractable roll-up standees and pop-up backdrop displays',
                    'Frosted window privacy films and glass partition graphics',
                    'Die-cut vinyl stickers, wall murals, and floor graphics',
                ],
                'icon_class' => 'fas fa-image',
                'sort_order' => 2,
            ],
            [
                'tag' => 'Signage & 3D Fabrication',
                'title' => '3D Signage & Architectural Fabrication',
                'description' => 'Custom illuminated and dimensional signage crafted to elevate brand presence. Our workshop utilizes CNC routers, laser cutters, and high-efficiency Samsung/Epistar LEDs for brilliant night-and-day visibility.',
                'checklist' => [
                    '3D acrylic letters and illuminated LED channel letters',
                    'Custom neon art signs and ultra-bright lightboxes',
                    'Exterior building fascia signage and steel frame structures',
                    'Indoor wayfinding, directional signs, and reception logo displays',
                ],
                'icon_class' => 'fas fa-cube',
                'sort_order' => 3,
            ],
            [
                'tag' => 'Packaging & Boxes',
                'title' => 'Custom Packaging, Rigid Boxes & Bags',
                'description' => 'Bespoke retail and product packaging designed to protect products and create memorable unboxing experiences. Tailored dimensions, custom die-lines, and premium finishing options.',
                'checklist' => [
                    'Rigid luxury magnetic gift boxes and custom hampers',
                    'Corrugated shipping mailers and heavy-duty product boxes',
                    'Food-grade takeaway boxes, greaseproof paper, and cup sleeves',
                    'Branded art paper shopping bags with ribbon or twisted handles',
                ],
                'icon_class' => 'fas fa-box-open',
                'sort_order' => 4,
            ],
            [
                'tag' => 'Corporate Gifts',
                'title' => 'Corporate Gifts & Promotional Merchandise',
                'description' => 'Memorable corporate giveaways and executive gift sets that strengthen client loyalty and elevate event presence with precision laser engraving and full-color UV printing.',
                'checklist' => [
                    'Executive faux-leather diaries, organizers, and notebooks',
                    'Laser-engraved metal pens and premium pen sets',
                    'Custom insulated travel tumblers, ceramic mugs, and bottles',
                    'Branded USB flash drives, wireless power banks, and tech gadgets',
                ],
                'icon_class' => 'fas fa-gift',
                'sort_order' => 5,
            ],
            [
                'tag' => 'Apparel & Uniforms',
                'title' => 'Custom Corporate Uniforms & Workwear',
                'description' => 'Professional apparel solutions tailored for corporate teams, retail staff, and field crews. High-density embroidery, breathable fabrics, and durable industrial safety wear.',
                'checklist' => [
                    'Custom embroidered polo shirts and casual corporate wear',
                    'High-quality screen-printed round neck t-shirts',
                    'Industrial workwear overalls and high-visibility safety vests',
                    'Branded headwear caps and woven staff lanyards with ID cards',
                ],
                'icon_class' => 'fas fa-tshirt',
                'sort_order' => 6,
            ],
            [
                'tag' => 'Vehicle Branding',
                'title' => 'Commercial Fleet Graphics & Vehicle Wraps',
                'description' => 'Turn your company vehicles into mobile billboards. We utilize cast automotive-grade vinyl with UV protective laminates to withstand extreme heat, sun exposure, and frequent washing.',
                'checklist' => [
                    'Full and partial commercial van and delivery truck wraps',
                    'Precision-cut vinyl decals and removable magnetic door signs',
                    'Perforated one-way vision film for rear window graphics',
                    'Certified professional installation with bubble-free warranty',
                ],
                'icon_class' => 'fas fa-truck',
                'sort_order' => 7,
            ],
            [
                'tag' => 'Exhibition & Events',
                'title' => 'Trade Show Booths & Event Branding',
                'description' => 'Complete turnkey event display engineering for trade exhibitions, conferences, product launches, and retail pop-up activations with fast setup and breakdown.',
                'checklist' => [
                    'Modular exhibition stands and branded promotional kiosks',
                    'Curved and straight media walls and step-and-repeat backdrops',
                    'Portable reception desks and branded podium counters',
                    'Outdoor teardrop flags, fabric banners, and event accreditation',
                ],
                'icon_class' => 'fas fa-bullhorn',
                'sort_order' => 8,
            ],
            [
                'tag' => 'Creative Studio',
                'title' => 'Professional Brand Identity & Graphic Design',
                'description' => 'Our in-house design studio turns strategic brand vision into print-ready reality. From initial brand concept to color-calibrated pre-press files ready for high-volume production.',
                'checklist' => [
                    'Corporate logo design and comprehensive brand style guidelines',
                    'Marketing collateral, annual reports, and catalog layout design',
                    'Vector artwork recreation and high-resolution prepress proofing',
                    'Color management (CMYK, RGB, Pantone) for exact print accuracy',
                ],
                'icon_class' => 'fas fa-palette',
                'sort_order' => 9,
            ],
        ];

        // Delete old default sample solutions to replace with real Crelogics services
        CmsSolution::truncate();
        foreach ($cmsSolutionsData as $sol) {
            CmsSolution::create([
                'tag' => $sol['tag'],
                'title' => $sol['title'],
                'description' => $sol['description'],
                'checklist' => $sol['checklist'],
                'icon_class' => $sol['icon_class'],
                'sort_order' => $sol['sort_order'],
                'is_active' => true,
            ]);
        }

        // 5. UPDATE PROPOSAL OPTIONS (Services dropdown in 4-step modal)
        $proposalServicesList = [
            [
                'name' => 'Commercial Printing & Stationery',
                'subtitle' => 'Business cards, letterheads, brochures & folders',
                'icon' => 'fas fa-print',
                'sort_order' => 1,
            ],
            [
                'name' => 'Large Format & Outdoor Media',
                'subtitle' => 'Roll-ups, pop-up backdrops & flex banners',
                'icon' => 'fas fa-image',
                'sort_order' => 2,
            ],
            [
                'name' => '3D Signage & Channel Letters',
                'subtitle' => 'Acrylic, LED backlit & architectural signs',
                'icon' => 'fas fa-cube',
                'sort_order' => 3,
            ],
            [
                'name' => 'Custom Packaging & Boxes',
                'subtitle' => 'Rigid luxury boxes, mailers & paper bags',
                'icon' => 'fas fa-box-open',
                'sort_order' => 4,
            ],
            [
                'name' => 'Corporate Gifts & Merchandise',
                'subtitle' => 'Diaries, pens, tumblers & promotional giveaways',
                'icon' => 'fas fa-gift',
                'sort_order' => 5,
            ],
            [
                'name' => 'Branded Apparel & Uniforms',
                'subtitle' => 'Embroidered polo shirts, t-shirts & workwear',
                'icon' => 'fas fa-tshirt',
                'sort_order' => 6,
            ],
            [
                'name' => 'Vehicle Branding & Fleet Wraps',
                'subtitle' => 'Delivery vans, trucks & commercial wraps',
                'icon' => 'fas fa-truck',
                'sort_order' => 7,
            ],
            [
                'name' => 'Exhibition & Event Branding',
                'subtitle' => 'Trade show booths, media walls & counters',
                'icon' => 'fas fa-bullhorn',
                'sort_order' => 8,
            ],
            [
                'name' => 'Creative Graphic Design Studio',
                'subtitle' => 'Logos, brand identity & prepress design',
                'icon' => 'fas fa-palette',
                'sort_order' => 9,
            ],
        ];

        ProposalOption::where('type', 'service')->delete();
        foreach ($proposalServicesList as $ps) {
            ProposalOption::create([
                'type' => 'service',
                'name' => $ps['name'],
                'subtitle' => $ps['subtitle'],
                'icon' => $ps['icon'],
                'sort_order' => $ps['sort_order'],
                'is_active' => true,
            ]);
        }
    }
}

