<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Carbon\CarbonImmutable;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $province_id
 * @property int|null $investor_id
 * @property string|null $district
 * @property string|null $address
 * @property string|null $former_address
 * @property ProjectStatus $status
 * @property CarbonImmutable|null $application_start_at
 * @property CarbonImmutable|null $application_end_at
 * @property int|null $total_units
 * @property int|null $price_from
 * @property int|null $price_to
 * @property int|null $area_from
 * @property int|null $area_to
 * @property string|null $description
 * @property string|null $application_guide
 * @property string|null $source_url
 * @property string|null $source_name
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property CarbonImmutable|null $published_at
 * @property string|null $external_id
 */
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'application_start_at' => 'datetime',
            'application_end_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @param  Builder<Project>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * @return BelongsTo<Province, $this>
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * @return BelongsTo<Investor, $this>
     */
    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    /**
     * @return HasMany<ProjectImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort');
    }

    public function coverImage(): ?ProjectImage
    {
        return $this->images->firstWhere('is_cover', true) ?? $this->images->first();
    }
}
