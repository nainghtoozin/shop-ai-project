<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'unit_id',
        'name',
        'slug',
        'sku',
        'cost_price',
        'selling_price',
        'stock',
        'alert_stock',
        'description',
        'status',
        'featured',
        'not_for_sale'
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'status' => 'boolean',
        'featured' => 'boolean',
        'not_for_sale' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
        
        static::updating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the unit that owns the product.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the images for the product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get the primary image for the product.
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get the first image as fallback if no primary image is set.
     */
    public function getFirstImageAttribute()
    {
        return $this->primaryImage ?: $this->images()->first();
    }

    /**
     * Get image URL attribute.
     */
    public function getImageUrlAttribute()
    {
        $image = $this->first_image;
        if ($image) {
            return asset('storage/products/' . $image->image);
        }
        
        return 'https://via.placeholder.com/300x300/6c757d/ffffff?text=No+Image';
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Check if stock is below alert level.
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->alert_stock;
    }

    /**
     * Get formatted cost price.
     */
    public function getFormattedCostPriceAttribute(): string
    {
        return number_format($this->cost_price, 2);
    }

    /**
     * Get formatted selling price.
     */
    public function getFormattedSellingPriceAttribute(): string
    {
        return number_format($this->selling_price, 2);
    }

    /**
     * Get profit margin percentage.
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->selling_price > 0) {
            return (($this->selling_price - $this->cost_price) / $this->selling_price) * 100;
        }
        return 0;
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope a query to only include products for sale.
     */
    public function scopeForSale($query)
    {
        return $query->where('not_for_sale', false);
    }

    /**
     * Scope a query to only include in stock products.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}
