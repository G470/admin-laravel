<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class StorageService
{
    /**
     * Storage disk to use
     */
    private string $disk;

    /**
     * Available buckets/directories for different file types
     */
    const BUCKETS = [
        'avatars' => 'avatars',
        'company_logos' => 'company-logos',
        'company_banners' => 'company-banners',
        'rental_images' => 'rental-images',
        'documents' => 'documents',
        'uploads' => 'uploads',
        'backups' => 'backups',
    ];

    public function __construct()
    {
        $this->disk = config('filesystems.default', 'local');
    }

    /**
     * Store a file in the appropriate bucket
     */
    public function store(UploadedFile $file, string $type, ?string $path = null): string
    {
        $bucket = $this->getBucketForType($type);
        $filename = $this->generateUniqueFilename($file);
        $fullPath = $path ? "{$bucket}/{$path}/{$filename}" : "{$bucket}/{$filename}";
        
        return $file->storeAs($bucket . ($path ? "/{$path}" : ''), $filename, $this->disk);
    }

    /**
     * Store a file with a specific name
     */
    public function storeAs(UploadedFile $file, string $type, string $filename, ?string $path = null): string
    {
        $bucket = $this->getBucketForType($type);
        $fullPath = $path ? "{$bucket}/{$path}/{$filename}" : "{$bucket}/{$filename}";
        
        return $file->storeAs($bucket . ($path ? "/{$path}" : ''), $filename, $this->disk);
    }

    /**
     * Delete a file
     */
    public function delete(string $filePath): bool
    {
        return Storage::disk($this->disk)->delete($filePath);
    }

    /**
     * Get file URL
     */
    public function url(string $filePath): string
    {
        $storage = Storage::disk($this->disk);
        $config = config("filesystems.disks.{$this->disk}");
        
        // For S3-compatible storage (MinIO), construct URL manually
        if ($config['driver'] === 's3') {
            $bucket = $config['bucket'];
            $endpoint = $config['url'] ?? $config['endpoint'];
            return rtrim($endpoint, '/') . '/' . $bucket . '/' . ltrim($filePath, '/');
        }
        
        // For local storage, use asset() with storage link
        return asset('storage/' . ltrim($filePath, '/'));
    }

    /**
     * Check if file exists
     */
    public function exists(string $filePath): bool
    {
        return Storage::disk($this->disk)->exists($filePath);
    }

    /**
     * Get file size
     */
    public function size(string $filePath): int
    {
        return Storage::disk($this->disk)->size($filePath);
    }

    /**
     * Move file from one location to another
     */
    public function move(string $from, string $to): bool
    {
        if (!$this->exists($from)) {
            return false;
        }

        $success = Storage::disk($this->disk)->move($from, $to);
        
        return $success;
    }

    /**
     * Copy file from one location to another
     */
    public function copy(string $from, string $to): bool
    {
        return Storage::disk($this->disk)->copy($from, $to);
    }

    /**
     * Get appropriate bucket for file type
     */
    private function getBucketForType(string $type): string
    {
        return self::BUCKETS[$type] ?? self::BUCKETS['uploads'];
    }

    /**
     * Generate unique filename while preserving extension
     */
    private function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $sanitizedName = Str::slug($name);
        
        return $sanitizedName . '_' . Str::random(8) . '.' . $extension;
    }

    /**
     * Store avatar image
     */
    public function storeAvatar(UploadedFile $file, int $userId): string
    {
        return $this->store($file, 'avatars', "user_{$userId}");
    }

    /**
     * Store company logo
     */
    public function storeCompanyLogo(UploadedFile $file, int $vendorId): string
    {
        return $this->store($file, 'company_logos', "vendor_{$vendorId}");
    }

    /**
     * Store company banner
     */
    public function storeCompanyBanner(UploadedFile $file, int $vendorId): string
    {
        return $this->store($file, 'company_banners', "vendor_{$vendorId}");
    }

    /**
     * Store rental image
     */
    public function storeRentalImage(UploadedFile $file, int $rentalId, int $index = 0): string
    {
        return $this->store($file, 'rental_images', "rental_{$rentalId}");
    }

    /**
     * Store document
     */
    public function storeDocument(UploadedFile $file, string $type = 'general'): string
    {
        return $this->store($file, 'documents', $type);
    }

    /**
     * Get all files in a bucket
     */
    public function listFiles(string $type, ?string $path = null): array
    {
        $bucket = $this->getBucketForType($type);
        $fullPath = $path ? "{$bucket}/{$path}" : $bucket;
        
        return Storage::disk($this->disk)->files($fullPath);
    }

    /**
     * Get storage statistics
     */
    public function getStorageStats(): array
    {
        $stats = [];
        
        foreach (self::BUCKETS as $key => $bucket) {
            $files = $this->listFiles($key);
            $totalSize = 0;
            
            foreach ($files as $file) {
                $totalSize += $this->size($file);
            }
            
            $stats[$key] = [
                'bucket' => $bucket,
                'files' => count($files),
                'size' => $totalSize,
                'size_formatted' => $this->formatBytes($totalSize),
            ];
        }
        
        return $stats;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Set custom storage disk
     */
    public function setDisk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * Get current disk
     */
    public function getDisk(): string
    {
        return $this->disk;
    }
}
