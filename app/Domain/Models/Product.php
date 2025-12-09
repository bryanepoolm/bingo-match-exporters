<?php

namespace App\Domain\Models;

use App\Domain\Enums\Incoterm;
use App\Domain\Enums\ProductStatus;
use App\Domain\Enums\UnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'producer_id',
        'company_id',
        'name',
        'slug',
        'description',
        'short_description',
        'category_id',
        'subcategory_id',
        'hs_code',
        'hs_description',
        'brand',
        'origin_country',
        'origin_state',
        'origin_city',
        'unit_of_measure',
        'minimum_order_quantity',
        'maximum_order_quantity',
        'available_quantity',
        'production_capacity_monthly',
        'weight_per_unit',
        'length',
        'width',
        'height',
        'volume',
        'price_per_unit',
        'currency',
        'price_term',
        'packaging_type',
        'units_per_package',
        'packages_per_pallet',
        'certifications',
        'quality_grade',
        'shelf_life_days',
        'requires_cold_chain',
        'temperature_min',
        'temperature_max',
        'requires_hazmat',
        'hazmat_class',
        'is_perishable',
        'special_handling_notes',
        'requires_phytosanitary',
        'requires_health_certificate',
        'requires_cites',
        'additional_documents_required',
        'export_restrictions',
        'target_markets',
        'seasonal_availability',
        'lead_time_days',
        'harvest_to_ship_days',
        'primary_image',
        'images',
        'videos',
        'documents',
        'keywords',
        'tags',
        'status',
        'is_featured',
        'is_verified',
        'visibility',
        'views_count',
        'inquiries_count',
        'matches_count',
        'published_at',
    ];

    protected $casts = [
        'certifications' => 'array',
        'target_markets' => 'array',
        'images' => 'array',
        'videos' => 'array',
        'documents' => 'array',
        'keywords' => 'array',
        'tags' => 'array',
        'seasonal_availability' => 'array',
        'export_restrictions' => 'array',
        'additional_documents_required' => 'array',
        'special_handling_notes' => 'array', // Use simple string in migration, but array cast if user wants structured notes? The prompt said 'text', so strictly it's a string. But validation example implies detailed handling. I will stick to array for flexibility if JSON, but migration was text. Let's keep it 'array' as requested in prompt "casts" section, but migration was text. Wait, user prompt said: "special_handling_notes (text, nullable)" AND "special_handling_notes' => 'array'" in casts. This implies the text column might store JSON. I'll respect the cast.
        
        'requires_cold_chain' => 'boolean',
        'is_perishable' => 'boolean',
        'requires_hazmat' => 'boolean',
        'requires_phytosanitary' => 'boolean',
        'requires_health_certificate' => 'boolean',
        'requires_cites' => 'boolean',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        
        'price_per_unit' => 'decimal:2',
        'minimum_order_quantity' => 'decimal:2',
        'maximum_order_quantity' => 'decimal:2',
        'available_quantity' => 'decimal:2',
        
        'published_at' => 'datetime',
        
        'unit_of_measure' => UnitOfMeasure::class,
        'price_term' => Incoterm::class,
        'status' => ProductStatus::class,
    ];

    public function producer(): BelongsTo
    {
        return $this->belongsTo(Producer::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'subcategory_id');
    }

    public function certificationsRel(): BelongsToMany
    {
        return $this->belongsToMany(Certification::class, 'product_certifications')
            ->withPivot(['certificate_number', 'issued_by', 'issued_at', 'expires_at', 'document_url', 'status'])
            ->withTimestamps();
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }
}
