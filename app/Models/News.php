<?php

namespace App\Models;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property string $text
 * @property boolean $is_published
 * @property Carbon $published_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class News extends Model
{
    use HasFactory, Sluggable;

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function save(array $options = [])
    {
        if ($this->exists && $this->isDirty('slug')) {
            $oldSlug = $this->getOriginal('slug');
            $newSlug = $this->slug;

            $redirect = new Redirect();
            $redirect->old_slug = $oldSlug;
            $redirect->new_slug = $newSlug;
            $redirect->save();
        }
        return parent::save($options);
    }
}
