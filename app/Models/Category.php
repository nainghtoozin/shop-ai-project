<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'parent_id',
        'description',
        'image',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
                $category->saveQuietly();
            }
        });
    }

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the products for the category.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get all descendants (children and grandchildren).
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Check if category has children.
     */
    public function hasChildren()
    {
        return $this->children()->exists();
    }

    /**
     * Get image URL.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/categories/' . $this->image);
        }
        
        return 'https://via.placeholder.com/300x200/6c757d/ffffff?text=No+Image';
    }

    /**
     * Get full image path for storage.
     */
    public function getImagePathAttribute()
    {
        return $this->image ? 'categories/' . $this->image : null;
    }

    /**
     * Get all possible parent categories (excluding current and descendants).
     */
    public static function getParentOptions($excludeId = null)
    {
        $query = static::whereNull('parent_id');
        
        if ($excludeId) {
            $query->whereNotIn('id', static::getDescendantIds($excludeId));
        }
        
        return $query->orderBy('name')->pluck('name', 'id');
    }

    /**
     * Get all descendant IDs.
     */
    protected static function getDescendantIds($categoryId)
    {
        $category = static::find($categoryId);
        $descendantIds = [$categoryId];
        
        if ($category) {
            $children = $category->descendants;
            foreach ($children as $child) {
                $descendantIds[] = $child->id;
                if ($child->children) {
                    $descendantIds = array_merge($descendantIds, static::getDescendantIds($child->id));
                }
            }
        }
        
        return $descendantIds;
    }
}