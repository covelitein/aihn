<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Content extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'content';

    const TYPES = [
        'article' => 'Article',
        'document' => 'Document',
        'video' => 'Video',
        'audio' => 'Audio',
        'file' => 'File'
    ];

    protected $fillable = [
        'title',
        'description',
        'type',
        'content',
        'file_path',
        'file_original_name',
        'file_mime_type',
        'file_size',
        'metadata',
        'author_id',
        'is_published',
        'published_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at');
    }

    // Plan-scoped access removed; content is universally accessible to authenticated users

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Methods
    public function isAccessibleByUser(User $user)
    {
        // Any authenticated user can access published content
        return (bool) $user?->id;
    }

    public function incrementViews()
    {
        $this->increment('view_count');
    }

    public function publish()
    {
        $this->update([
            'is_published' => true,
            'published_at' => now()
        ]);
    }

    public function unpublish()
    {
        $this->update([
            'is_published' => false,
            'published_at' => null
        ]);
    }

    public function getFileUrl()
    {
        if (!$this->file_path) {
            return null;
        }

        return Storage::url($this->file_path);
    }

    public function deleteFile()
    {
        if ($this->file_path && Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }
    }

    public function getHumanFileSize()
    {
        if (!$this->file_size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    protected static function boot()
    {
        parent::boot();

        // Auto-set author_id when creating content
        static::creating(function ($content) {
            if (!$content->author_id) {
                $content->author_id = auth()->id();
            }
        });

        // Delete file when deleting content
        static::deleting(function ($content) {
            $content->deleteFile();
        });
    }
}