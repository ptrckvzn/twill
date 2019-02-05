<?php

namespace A17\Twill\Models;

use Auth;
use A17\Twill\Models\Behaviors\HasPresenter;
use Carbon\Carbon;
use Cartalyst\Tags\TaggableInterface;
use Cartalyst\Tags\TaggableTrait;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use A17\Twill\Models\Permission;
use A17\Twill\Models\Enums\UserRole;

abstract class Model extends BaseModel implements TaggableInterface
{
    use HasPresenter, SoftDeletes, TaggableTrait;

    public $timestamps = true;

    public function scopePublished($query)
    {
        return $query->wherePublished(true);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('authorized', function (Builder $builder) {
            $permission_models = collect(config('twill.user_management.permission.enabled_modules', []))->map(function ($moduleName) {
                return "App\Models\\" . Str::studly(Str::singular($moduleName));
            });
            
            $model = get_class($builder->getModel());

            if ($permission_models->contains($model) && !in_array(Auth::user()->role_value, ['SUPERADMIN', UserRole::OWNER])) { 
                $builder->whereIn('id', Permission::where([
                    ['twill_user_id', Auth::user()->id],
                    ['permissionable_type', $model],
                ])->pluck('permissionable_id'));
            }
        });
    }

    public function scopePublishedInListings($query)
    {
        if ($this->isFillable('public')) {
            $query->wherePublic(true);
        }

        return $query->published()->visible();
    }

    public function scopeVisible($query)
    {
        if ($this->isFillable('publish_start_date')) {
            $query->where(function ($query) {
                $query->whereNull('publish_start_date')->orWhere('publish_start_date', '<=', Carbon::now());
            });

            if ($this->isFillable('publish_end_date')) {
                $query->where(function ($query) {
                    $query->whereNull('publish_end_date')->orWhere('publish_end_date', '>=', Carbon::now());
                });
            }
        }

        return $query;
    }

    public function setPublishStartDateAttribute($value)
    {
        $this->attributes['publish_start_date'] = $value ?? Carbon::now();
    }

    public function scopeDraft($query)
    {
        return $query->wherePublished(false);
    }

    public function scopeOnlyTrashed($query)
    {
        return $query->whereNotNull('deleted_at');
    }
}
