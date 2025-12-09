<?php

namespace Database\Seeders;

use App\Domain\Models\Capability;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CapabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $capabilities = [
            // --- Global Food Safety & Quality Certifications ---
            ['name' => 'ISO 9001: Quality Management', 'type' => 'certification'],
            ['name' => 'ISO 14001: Environmental Management', 'type' => 'certification'],
            ['name' => 'ISO 22000: Food Safety Management', 'type' => 'certification'],
            ['name' => 'ISO 45001: Occupational Health and Safety', 'type' => 'certification'],
            ['name' => 'HACCP (Hazard Analysis Critical Control Point)', 'type' => 'certification'],
            ['name' => 'FSSC 22000', 'type' => 'certification'],
            ['name' => 'BRCGS Food Safety', 'type' => 'certification'],
            ['name' => 'IFS Food (International Featured Standards)', 'type' => 'certification'],
            ['name' => 'SQF (Safe Quality Food) Program', 'type' => 'certification'],
            ['name' => 'Global G.A.P.', 'type' => 'certification'],
            ['name' => 'PrimusGFS', 'type' => 'certification'],
            ['name' => 'CanadaGAP', 'type' => 'certification'],
            ['name' => 'JAS (Japanese Agricultural Standard)', 'type' => 'certification'],

            // --- Sustainability, Ethical & Organic Certifications ---
            ['name' => 'USDA Organic', 'type' => 'certification'],
            ['name' => 'EU Organic', 'type' => 'certification'],
            ['name' => 'Fair Trade Certified', 'type' => 'certification'],
            ['name' => 'Rainforest Alliance', 'type' => 'certification'],
            ['name' => 'UTZ Certified', 'type' => 'certification'],
            ['name' => 'Bonsucro', 'type' => 'certification'],
            ['name' => 'RSPO (Roundtable on Sustainable Palm Oil)', 'type' => 'certification'],
            ['name' => 'Non-GMO Project Verified', 'type' => 'certification'],
            ['name' => 'Gluten-Free Certification Organization (GFCO)', 'type' => 'certification'],
            ['name' => 'Vegan Certified', 'type' => 'certification'],
            ['name' => 'Kosher Certification', 'type' => 'certification'],
            ['name' => 'Halal Certification', 'type' => 'certification'],
            ['name' => 'SMETA (Sedex Members Ethical Trade Audit)', 'type' => 'certification'],
            ['name' => 'BSCI (Business Social Compliance Initiative)', 'type' => 'certification'],
            ['name' => 'SA8000 (Social Accountability)', 'type' => 'certification'],
            ['name' => 'Carbon Trust Standard', 'type' => 'certification'],
            ['name' => 'LEED Certification (Green Building)', 'type' => 'certification'],

            // --- Regulatory & Trade Compliance ---
            ['name' => 'FDA Registered (USA)', 'type' => 'certification'],
            ['name' => 'FSMA Compliant (USA)', 'type' => 'certification'],
            ['name' => 'CTPAT (Customs-Trade Partnership Against Terrorism)', 'type' => 'certification'],
            ['name' => 'AEO (Authorized Economic Operator)', 'type' => 'certification'],
            ['name' => 'BASC (Business Alliance for Secure Commerce)', 'type' => 'certification'],
            ['name' => 'CFIA Registered (Canada)', 'type' => 'certification'],
            ['name' => 'GACC Registered (China)', 'type' => 'certification'],
            ['name' => 'SENASICA Certified (Mexico)', 'type' => 'certification'],

            // --- Logistics & Supply Chain Services ---
            ['name' => 'Cold Chain Storage', 'type' => 'logistics'],
            ['name' => 'Frozen Storage (-18°C / 0°F)', 'type' => 'logistics'],
            ['name' => 'Chilled Storage (0-4°C)', 'type' => 'logistics'],
            ['name' => 'Controlled Atmosphere (CA) Storage', 'type' => 'logistics'],
            ['name' => 'Ambient/Dry Storage', 'type' => 'logistics'],
            ['name' => 'Bonded Warehousing', 'type' => 'logistics'],
            ['name' => 'Cross-Docking', 'type' => 'logistics'],
            ['name' => 'Order Fulfillment & Pick/Pack', 'type' => 'logistics'],
            ['name' => 'Freight Forwarding (Air)', 'type' => 'logistics'],
            ['name' => 'Freight Forwarding (Ocean - FCL/LCL)', 'type' => 'logistics'],
            ['name' => 'Freight Forwarding (Land - FTL/LTL)', 'type' => 'logistics'],
            ['name' => 'Intermodal Transportation', 'type' => 'logistics'],
            ['name' => 'Refrigerated Transport (Reefer)', 'type' => 'logistics'],
            ['name' => 'Last Mile Delivery', 'type' => 'logistics'],
            ['name' => 'Customs Brokerage', 'type' => 'logistics'],
            ['name' => 'Cargo Insurance', 'type' => 'logistics'],
            ['name' => 'Real-time Shipment Tracking', 'type' => 'logistics'],

            // --- Processing & Manufacturing Capabilities ---
            ['name' => 'IQF (Individually Quick Frozen)', 'type' => 'processing'],
            ['name' => 'Freeze Drying', 'type' => 'processing'],
            ['name' => 'Dehydration / Air Drying', 'type' => 'processing'],
            ['name' => 'Spray Drying', 'type' => 'processing'],
            ['name' => 'Canning / Retort', 'type' => 'processing'],
            ['name' => 'Pasteurization', 'type' => 'processing'],
            ['name' => 'HPP (High Pressure Processing)', 'type' => 'processing'],
            ['name' => 'Aseptic Processing', 'type' => 'processing'],
            ['name' => 'Fermentation', 'type' => 'processing'],
            ['name' => 'Extraction (Oil, Juice, Essence)', 'type' => 'processing'],
            ['name' => 'Milling & Grinding', 'type' => 'processing'],
            ['name' => 'Roasting', 'type' => 'processing'],
            ['name' => 'Sorting & Grading (Optical/Laser)', 'type' => 'processing'],
            ['name' => 'Washing & Sanitizing', 'type' => 'processing'],

            // --- Packaging & Labeling ---
            ['name' => 'Private Label / OEM', 'type' => 'packaging'],
            ['name' => 'Bulk Packaging (Totes, Drums, Bins)', 'type' => 'packaging'],
            ['name' => 'Retail Packaging (Bags, Boxes, Clamshells)', 'type' => 'packaging'],
            ['name' => 'Vacuum Packaging', 'type' => 'packaging'],
            ['name' => 'MAP (Modified Atmosphere Packaging)', 'type' => 'packaging'],
            ['name' => 'Sustainable/Biodegradable Packaging', 'type' => 'packaging'],
            ['name' => 'Custom Labeling & Barcoding', 'type' => 'packaging'],
            ['name' => 'Co-Packing', 'type' => 'packaging'],

            // --- Target Export Markets ---
            ['name' => 'Export to USA', 'type' => 'market'],
            ['name' => 'Export to Canada', 'type' => 'market'],
            ['name' => 'Export to European Union', 'type' => 'market'],
            ['name' => 'Export to United Kingdom', 'type' => 'market'],
            ['name' => 'Export to China', 'type' => 'market'],
            ['name' => 'Export to Japan', 'type' => 'market'],
            ['name' => 'Export to South Korea', 'type' => 'market'],
            ['name' => 'Export to Middle East', 'type' => 'market'],
            ['name' => 'Export to Latin America', 'type' => 'market'],
            ['name' => 'Export to Australia/NZ', 'type' => 'market'],
        ];

        foreach ($capabilities as $capability) {
            Capability::firstOrCreate(
                ['name' => $capability['name']], 
                ['type' => $capability['type']]
            );
        }
    }
}
