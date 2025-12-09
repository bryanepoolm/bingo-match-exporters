<?php

namespace App\Http\Requests;

use App\Domain\Enums\Incoterm;
use App\Domain\Enums\UnitOfMeasure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth middleware handles this
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:product_categories,id',
            'hs_code' => 'required|string|max:20',
            
            // Characteristics
            'brand' => 'nullable|string|max:255',
            'origin_country' => 'required|string|size:2', // We might want to use full country names or validate against a list
            'origin_state' => 'required|string|max:100',
            'origin_city' => 'required|string|max:100',
            
            // Pricing & Quantities
            'unit_of_measure' => ['required', new Enum(UnitOfMeasure::class)],
            'price_per_unit' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'price_term' => ['required', new Enum(Incoterm::class)],
            'minimum_order_quantity' => 'required|numeric|min:0',
            'available_quantity' => 'required|numeric|min:0',
            
            // Logistics
            'packaging_type' => 'required|string|max:100',
            'lead_time_days' => 'required|integer|min:0',
            'weight_per_unit' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            
            'requires_cold_chain' => 'boolean',
            'is_perishable' => 'boolean',
            'requires_hazmat' => 'boolean',
            'hazmat_class' => 'required_if:requires_hazmat,true|nullable|string|max:10',
            'special_handling_notes' => 'nullable|string',
            
            'temperature_min' => 'required_if:requires_cold_chain,true|nullable|numeric',
            'temperature_max' => 'required_if:requires_cold_chain,true|nullable|numeric',
            
            // Quality & Certs
            'quality_grade' => 'nullable|string|max:50',
            'shelf_life_days' => 'nullable|integer|min:0',
            'certifications' => 'nullable|array',
            'certifications.*' => 'integer|exists:certifications,id',
            
            // Documentation
            'requires_phytosanitary' => 'boolean',
            'requires_health_certificate' => 'boolean',
            'requires_cites' => 'boolean',
            'additional_documents_required' => 'nullable|array', // JSON list of requirements
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,png|max:10240', // 10MB max

            // Markets
            'target_markets' => 'required|array|min:1',
            'target_markets.*' => 'string|size:2',
            
            // Media
            'images' => 'nullable|array',
            'images.*' => 'nullable', // Allow string or file
            'primary_image_index' => 'nullable|integer|min:0',
            
            // SEO & Search
            'keywords' => 'nullable|array',
            'keywords.*' => 'string|max:50',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            
            // Settings
            'status' => ['required', new Enum(\App\Domain\Enums\ProductStatus::class)],
            'visibility' => 'required|in:public,private,partners_only',
            'is_featured' => 'boolean',
            'is_verified' => 'boolean', // In real app, only admin can set this.
        ];
    }
}
