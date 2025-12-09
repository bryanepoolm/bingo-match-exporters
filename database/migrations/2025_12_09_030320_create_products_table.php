<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producer_id')->constrained('producers')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete(); // Multi-tenancy

            // Basic Info
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('short_description', 500)->nullable();

            // Classification
            $table->foreignId('category_id')->constrained('product_categories');
            $table->foreignId('subcategory_id')->nullable()->constrained('product_categories');
            $table->string('hs_code', 20)->index(); // Match with hs_codes.code
            $table->string('hs_description')->nullable();

            // Characteristics
            $table->string('brand')->nullable();
            $table->char('origin_country', 2);
            $table->string('origin_state');
            $table->string('origin_city');

            // Specs
            $table->string('unit_of_measure'); // Enum
            $table->decimal('minimum_order_quantity', 10, 2);
            $table->decimal('maximum_order_quantity', 10, 2)->nullable();
            $table->decimal('available_quantity', 10, 2);
            $table->decimal('production_capacity_monthly', 10, 2)->nullable();

            // Dimensions/Weight
            $table->decimal('weight_per_unit', 10, 3)->nullable();
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('volume', 10, 4)->nullable();

            // Pricing
            $table->decimal('price_per_unit', 12, 2);
            $table->char('currency', 3);
            $table->string('price_term'); // Enum (Incoterm)

            // Packaging
            $table->string('packaging_type');
            $table->integer('units_per_package')->nullable();
            $table->integer('packages_per_pallet')->nullable();

            // Certs & Quality
            $table->json('certifications')->nullable();
            $table->string('quality_grade')->nullable();
            $table->integer('shelf_life_days')->nullable();

            // Logistics Requirements
            $table->boolean('requires_cold_chain')->default(false);
            $table->decimal('temperature_min', 5, 2)->nullable();
            $table->decimal('temperature_max', 5, 2)->nullable();
            $table->boolean('requires_hazmat')->default(false);
            $table->string('hazmat_class')->nullable();
            $table->boolean('is_perishable')->default(false);
            $table->text('special_handling_notes')->nullable();

            // Documents
            $table->boolean('requires_phytosanitary')->default(false);
            $table->boolean('requires_health_certificate')->default(false);
            $table->boolean('requires_cites')->default(false);
            $table->json('additional_documents_required')->nullable();

            // Restrictions & Compliance
            $table->json('export_restrictions')->nullable();
            $table->json('target_markets')->nullable();
            $table->json('seasonal_availability')->nullable();

            // Lead Times
            $table->integer('lead_time_days');
            $table->integer('harvest_to_ship_days')->nullable();

            // Media
            $table->string('primary_image')->nullable();
            $table->json('images')->nullable();
            $table->json('videos')->nullable();
            $table->json('documents')->nullable();

            // SEO
            $table->json('keywords')->nullable();
            $table->json('tags')->nullable();

            // Status
            $table->string('status')->default('draft'); // Enum
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->string('visibility')->default('public');

            // Metrics
            $table->integer('views_count')->default(0);
            $table->integer('inquiries_count')->default(0);
            $table->integer('matches_count')->default(0);

            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('published_at')->nullable();

            // Indexes
            $table->index(['producer_id', 'status']);
            $table->index(['category_id', 'status']);
            $table->index('published_at');
            $table->fullText(['name', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
