<?php

namespace App\Domain\Services;

use App\Domain\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {}

    public function listProductsForProducer(int $producerId, int $perPage = 15)
    {
        return $this->productRepository->paginateByProducerId($producerId, $perPage);
    }

    public function createProduct(array $data, int $producerId, int $companyId): Product
    {
        return DB::transaction(function () use ($data, $producerId, $companyId) {
            $data['producer_id'] = $producerId;
            $data['company_id'] = $companyId;
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']) . '-' . time();
            
            // Handle File Uploads
            // Strategy: Result = (preserved_documents) + (newly uploaded documents)
            
            $finalDocuments = $data['preserved_documents'] ?? []; // Existing ones we want to keep
            
            if (isset($data['documents']) && is_array($data['documents'])) {
                foreach ($data['documents'] as $file) {
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $path = $file->store('products/documents', 'public');
                        $finalDocuments[] = [
                            'path' => $path,
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                        ];
                    }
                }
            }
            $data['documents'] = $finalDocuments;

            // Handle Images
            $finalImages = $data['preserved_images'] ?? [];
            
            $uploadedPaths = [];
            if (isset($data['images']) && is_array($data['images'])) {
                 foreach ($data['images'] as $index => $img) {
                      if ($img instanceof \Illuminate\Http\UploadedFile) {
                           $path = $img->store('products/images', 'public');
                           $finalImages[] = $path;
                           $uploadedPaths[$index] = $path;
                      }
                 }
            }
            $data['images'] = $finalImages;

            // Handle Primary Image
            // 1. If index provided, pick from uploaded
            if (isset($data['primary_image_index']) && isset($uploadedPaths[$data['primary_image_index']])) {
                $data['primary_image'] = $uploadedPaths[$data['primary_image_index']];
            } 
            // 2. Fallback: If no primary set but we have images, take the first one
            elseif (!empty($finalImages)) {
                $data['primary_image'] = $finalImages[0];
            }

            unset($data['primary_image_index']);
            unset($data['preserved_documents']);
            unset($data['preserved_images']);

             // Extract relations data if exists
            $certifications = $data['certifications'] ?? null; 
            unset($data['certifications']);
            
            // Allow mass assignment of what remains
            $product = $this->productRepository->create($data);

            if (!empty($certifications)) {
                 $product->certificationsRel()->sync($certifications);
            }

            return $product;
        });
    }

    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
             // Handle File Uploads (Deletion + Addition Logic)
             // Handle File Uploads (Deletion + Addition Logic)
            if (array_key_exists('preserved_documents', $data) || array_key_exists('documents', $data)) {
                 $finalDocuments = $data['preserved_documents'] ?? []; // Frontend sends what is kept
                
                 if (isset($data['documents']) && is_array($data['documents'])) {
                     foreach ($data['documents'] as $file) {
                         if ($file instanceof \Illuminate\Http\UploadedFile) {
                             $path = $file->store('products/documents', 'public');
                             $finalDocuments[] = [
                                 'path' => $path,
                                 'original_name' => $file->getClientOriginalName(),
                                 'mime_type' => $file->getMimeType(),
                             ];
                         }
                     }
                 }
                 $data['documents'] = $finalDocuments;
            }

            // Handle Images
            if (array_key_exists('preserved_images', $data) || array_key_exists('images', $data)) {
                 $finalImages = $data['preserved_images'] ?? [];
    
                 $uploadedPaths = [];
                 if (isset($data['images']) && is_array($data['images'])) {
                      foreach ($data['images'] as $index => $img) {
                           if ($img instanceof \Illuminate\Http\UploadedFile) {
                                $path = $img->store('products/images', 'public');
                                $finalImages[] = $path;
                                $uploadedPaths[$index] = $path;
                           }
                      }
                 }
                 $data['images'] = $finalImages;
    
                 // Handle Primary Image (Only if we are processing images)
                 // 1. Explicit path provided (from existing images)
                 if (!empty($data['primary_image_path'])) {
                      $data['primary_image'] = $data['primary_image_path'];
                 }
                 // 2. Index provided (from NEW uploaded images)
                 elseif (isset($data['primary_image_index']) && isset($uploadedPaths[$data['primary_image_index']])) {
                      $data['primary_image'] = $uploadedPaths[$data['primary_image_index']];
                 }
                 // 3. Fallback: If current primary is gone (not in finalImages), default to first available
                 elseif (!empty($finalImages) && (!isset($data['primary_image']) || !in_array($data['primary_image'], $finalImages))) {
                      $data['primary_image'] = $finalImages[0];
                 }
                  // 4. If no images at all
                 elseif (empty($finalImages)) {
                     $data['primary_image'] = null;
                 }
            }

            unset($data['primary_image_index']);
            unset($data['primary_image_path']);
            unset($data['preserved_documents']);
            unset($data['preserved_images']);

             // Extract relations data if exists
            $certifications = $data['certifications'] ?? null; 
            unset($data['certifications']);

            $product = $this->productRepository->update($product, $data);
            
            if ($certifications !== null) {
                 // Ensure we have IDs if it's a mix of objects and IDs
                 $certIds = collect($certifications)->map(function($cert) {
                     return is_array($cert) ? $cert['id'] : $cert;
                 })->toArray();
                 $product->certificationsRel()->sync($certIds);
            }
            
            return $product;
        });
    }

    public function deleteProduct(Product $product): bool
    {
        return $this->productRepository->delete($product);
    }
}
