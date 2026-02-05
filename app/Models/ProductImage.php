<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image',
        'is_primary',
        'sort_order'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($productImage) {
            if ($productImage->is_primary) {
                // Remove primary flag from other images of this product
                static::where('product_id', $productImage->product_id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }
            
            // Set default sort order if not provided
            if (is_null($productImage->sort_order)) {
                $maxOrder = static::where('product_id', $productImage->product_id)
                    ->max('sort_order') ?? 0;
                $productImage->sort_order = $maxOrder + 1;
            }
        });
        
        static::updating(function ($productImage) {
            if ($productImage->is_primary && $productImage->wasChanged('is_primary')) {
                // Remove primary flag from other images of this product
                static::where('product_id', $productImage->product_id)
                    ->where('id', '!=', $productImage->id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }
        });
    }

    /**
     * Get the product that owns the image.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get image URL attribute.
     */
    public function getImageUrlAttribute(): string
    {
        return asset('storage/products/' . $this->image);
    }

    /**
     * Get image path attribute.
     */
    public function getImagePathAttribute(): string
    {
        return 'products/' . $this->image;
    }

    /**
     * Get full image path for storage.
     */
    public function getFullImagePathAttribute(): string
    {
        return storage_path('app/public/products/' . $this->image);
    }

    /**
     * Check if image file exists.
     */
    public function fileExists(): bool
    {
        return file_exists($this->full_image_path);
    }

    /**
     * Delete image file from storage.
     */
    public function deleteImageFile(): bool
    {
        if ($this->fileExists()) {
            return unlink($this->full_image_path);
        }
        return true;
    }

    /**
     * Override delete to also remove image file.
     */
    public function delete(): bool
    {
        $this->deleteImageFile();
        return parent::delete();
    }
}
