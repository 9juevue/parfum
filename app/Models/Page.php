<?php

namespace App\Models;

use App\Observers\PageObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[ObservedBy([PageObserver::class])]
/**
 * 
 *
 * @property int $id
 * @property string|null $slug
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property string|null $seo_keywords
 * @property string|null $breadcrumb
 * @property string $template
 * @property int|null $parent_id
 * @property string|null $pageable_type
 * @property int|null $pageable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Page> $children
 * @property-read int|null $children_count
 * @property-read Model|\Eloquent|null $pageable
 * @property-read Page|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SlugHistory> $slugHistory
 * @property-read int|null $slug_history_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereBreadcrumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page wherePageableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page wherePageableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereSeoDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereSeoKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereSeoTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Page extends Model
{
    protected $fillable = [
        'slug',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'breadcrumb',
        'template',
        'pageable_type',
        'pageable_id'
    ];

    public function pageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id');
    }

    public function parentRecursive(): BelongsTo|\Illuminate\Database\Eloquent\Builder
    {
        return $this->parent()->with('parentRecursive');
    }

    public function childrenRecursive(): \Illuminate\Database\Eloquent\Builder|HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function slugHistory(): HasMany
    {
        return $this->hasMany(SlugHistory::class, 'page_id');
    }
}
