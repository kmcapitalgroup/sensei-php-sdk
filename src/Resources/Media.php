<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Media resource
 *
 * Manage media library, uploads, and file management
 */
class Media extends Resource
{
    protected string $basePath = 'v1/partners/media';

    // === Files ===

    /**
     * List all files
     */
    public function all(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path(), $params);
    }

    /**
     * Get file details
     */
    public function get(int $fileId): array
    {
        return $this->client->get($this->path($fileId));
    }

    /**
     * Upload file
     */
    public function upload(string $filePath, array $data = []): array
    {
        return $this->client->upload($this->path('upload'), $filePath, 'file', $data);
    }

    /**
     * Upload multiple files
     */
    public function uploadMultiple(array $filePaths, array $data = []): array
    {
        $results = [];
        foreach ($filePaths as $path) {
            $results[] = $this->upload($path, $data);
        }
        return $results;
    }

    /**
     * Upload from URL
     */
    public function uploadFromUrl(string $url, array $data = []): array
    {
        return $this->client->post($this->path('upload-url'), array_merge($data, ['url' => $url]));
    }

    /**
     * Update file metadata
     */
    public function updateFile(int $fileId, array $data): array
    {
        return $this->client->put($this->path($fileId), $data);
    }

    /**
     * Delete file
     */
    public function delete(int $fileId): array
    {
        return $this->client->delete($this->path($fileId));
    }

    /**
     * Delete multiple files
     */
    public function deleteMultiple(array $fileIds): array
    {
        return $this->client->delete($this->path('bulk'), ['file_ids' => $fileIds]);
    }

    /**
     * Duplicate file
     */
    public function duplicate(int $fileId): array
    {
        return $this->client->post($this->path("{$fileId}/duplicate"));
    }

    /**
     * Get download URL
     */
    public function downloadUrl(int $fileId, int $expiresIn = 3600): array
    {
        return $this->client->get($this->path("{$fileId}/download-url"), ['expires_in' => $expiresIn]);
    }

    // === Folders ===

    /**
     * List folders
     */
    public function folders(?int $parentId = null): array
    {
        $params = $parentId ? ['parent_id' => $parentId] : [];
        return $this->client->get($this->path('folders'), $params);
    }

    /**
     * Get folder
     */
    public function folder(int $folderId): array
    {
        return $this->client->get($this->path("folders/{$folderId}"));
    }

    /**
     * Create folder
     */
    public function createFolder(string $name, ?int $parentId = null): array
    {
        return $this->client->post($this->path('folders'), [
            'name' => $name,
            'parent_id' => $parentId
        ]);
    }

    /**
     * Rename folder
     */
    public function renameFolder(int $folderId, string $name): array
    {
        return $this->client->put($this->path("folders/{$folderId}"), ['name' => $name]);
    }

    /**
     * Delete folder
     */
    public function deleteFolder(int $folderId, bool $deleteContents = false): array
    {
        return $this->client->delete($this->path("folders/{$folderId}"), [
            'delete_contents' => $deleteContents
        ]);
    }

    /**
     * Move folder
     */
    public function moveFolder(int $folderId, ?int $parentId = null): array
    {
        return $this->client->post($this->path("folders/{$folderId}/move"), ['parent_id' => $parentId]);
    }

    /**
     * Get folder contents
     */
    public function folderContents(int $folderId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("folders/{$folderId}/contents"), $params);
    }

    // === File Operations ===

    /**
     * Move file to folder
     */
    public function moveFile(int $fileId, ?int $folderId = null): array
    {
        return $this->client->post($this->path("{$fileId}/move"), ['folder_id' => $folderId]);
    }

    /**
     * Move multiple files
     */
    public function moveFiles(array $fileIds, ?int $folderId = null): array
    {
        return $this->client->post($this->path('bulk/move'), [
            'file_ids' => $fileIds,
            'folder_id' => $folderId
        ]);
    }

    /**
     * Rename file
     */
    public function renameFile(int $fileId, string $name): array
    {
        return $this->client->put($this->path($fileId), ['name' => $name]);
    }

    // === Image Processing ===

    /**
     * Resize image
     */
    public function resize(int $fileId, int $width, ?int $height = null): array
    {
        return $this->client->post($this->path("{$fileId}/resize"), [
            'width' => $width,
            'height' => $height
        ]);
    }

    /**
     * Crop image
     */
    public function crop(int $fileId, int $x, int $y, int $width, int $height): array
    {
        return $this->client->post($this->path("{$fileId}/crop"), [
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height
        ]);
    }

    /**
     * Generate thumbnail
     */
    public function generateThumbnail(int $fileId, int $width, int $height): array
    {
        return $this->client->post($this->path("{$fileId}/thumbnail"), [
            'width' => $width,
            'height' => $height
        ]);
    }

    /**
     * Get image variants
     */
    public function variants(int $fileId): array
    {
        return $this->client->get($this->path("{$fileId}/variants"));
    }

    // === Video Processing ===

    /**
     * Get video info
     */
    public function videoInfo(int $fileId): array
    {
        return $this->client->get($this->path("{$fileId}/video-info"));
    }

    /**
     * Generate video thumbnail
     */
    public function videoThumbnail(int $fileId, int $timestamp = 0): array
    {
        return $this->client->post($this->path("{$fileId}/video-thumbnail"), ['timestamp' => $timestamp]);
    }

    /**
     * Transcode video
     */
    public function transcodeVideo(int $fileId, string $format = 'mp4', string $quality = '720p'): array
    {
        return $this->client->post($this->path("{$fileId}/transcode"), [
            'format' => $format,
            'quality' => $quality
        ]);
    }

    /**
     * Get transcoding status
     */
    public function transcodingStatus(int $fileId): array
    {
        return $this->client->get($this->path("{$fileId}/transcode/status"));
    }

    // === Search ===

    /**
     * Search files
     */
    public function search(string $query, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('search'), array_merge($params, ['q' => $query]));
    }

    /**
     * Filter by type
     */
    public function byType(string $type, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path(), array_merge($params, ['type' => $type]));
    }

    // === Tags ===

    /**
     * Get file tags
     */
    public function tags(int $fileId): array
    {
        return $this->client->get($this->path("{$fileId}/tags"));
    }

    /**
     * Add tag to file
     */
    public function addTag(int $fileId, string $tag): array
    {
        return $this->client->post($this->path("{$fileId}/tags"), ['tag' => $tag]);
    }

    /**
     * Remove tag from file
     */
    public function removeTag(int $fileId, string $tag): array
    {
        return $this->client->delete($this->path("{$fileId}/tags/{$tag}"));
    }

    /**
     * Get files by tag
     */
    public function filesByTag(string $tag, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('by-tag/' . urlencode($tag)), $params);
    }

    // === Statistics ===

    /**
     * Get storage statistics
     */
    public function storageStats(): array
    {
        return $this->client->get($this->path('stats/storage'));
    }

    /**
     * Get usage by type
     */
    public function usageByType(): array
    {
        return $this->client->get($this->path('stats/by-type'));
    }

    // === Settings ===

    /**
     * Get media settings
     */
    public function settings(): array
    {
        return $this->client->get($this->path('settings'));
    }

    /**
     * Update media settings
     */
    public function updateSettings(array $settings): array
    {
        return $this->client->put($this->path('settings'), $settings);
    }
}
