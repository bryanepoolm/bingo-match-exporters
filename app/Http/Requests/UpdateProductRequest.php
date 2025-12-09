<?php

namespace App\Http\Requests;

use App\Domain\Enums\Incoterm;
use App\Domain\Enums\UnitOfMeasure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:product_categories,id',
            'hs_code' => 'sometimes|required|string|min:6|exists:hs_codes,code',
            
            'unit_of_measure' => ['sometimes', 'required', new Enum(UnitOfMeasure::class)],
            'minimum_order_quantity' => 'sometimes|required|numeric|min:0',
            'available_quantity' => 'sometimes|required|numeric|min:0',
            
            'price_per_unit' => 'sometimes|required|numeric|min:0',
            'currency' => 'sometimes|required|string|size:3',
            'price_term' => ['sometimes', 'required', new Enum(Incoterm::class)],
            
            'requires_cold_chain' => 'boolean',
            'is_perishable' => 'boolean',
            'temperature_min' => 'required_if:requires_cold_chain,true|nullable|numeric',
            'temperature_max' => 'required_if:requires_cold_chain,true|nullable|numeric',
            
            'target_markets' => 'sometimes|required|array|min:1',
            'target_markets.*' => 'string|size:2',
            
            // Files
            'documents' => 'nullable|array',
            'preserved_documents' => 'nullable|array',
            'images' => 'nullable|array',
            'preserved_images' => 'nullable|array',
            'primary_image_index' => 'nullable|integer|min:0',
            'primary_image_path' => 'nullable|string',

            // Settings
            'status' => 'sometimes|required|in:draft,active,inactive,out_of_stock',
            'visibility' => 'sometimes|required|in:public,private,partners_only',
            'is_featured' => 'boolean',
        ];
    }
}
