<?php

namespace App\Services\NewsCrawler\DTO;

class CrawledArticleData
{
    /**
     * @param string $title Tiêu đề bài viết
     * @param string $contentHtml Nội dung chi tiết hoặc tóm tắt dạng HTML
     * @param string $sourceUrl Đường dẫn bài báo gốc (dùng để định danh duy nhất)
     * @param string $sourceName Tên nguồn báo (vd: VnExpress)
     * @param string|null $thumbnailUrl Ảnh thumbnail đại diện
     * @param int|null $categoryId ID chuyên mục map với bảng categories
     */
    public function __construct(
        public string $title,
        public string $contentHtml,
        public string $sourceUrl,
        public string $sourceName,
        public ?string $thumbnailUrl = null,
        public ?int $categoryId = null
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'content_html' => $this->contentHtml,
            'source_url' => $this->sourceUrl,
            'source_name' => $this->sourceName,
            'thumbnail_url' => $this->thumbnailUrl,
            'category_id' => $this->categoryId,
        ];
    }
}
