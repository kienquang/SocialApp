<?php

namespace App\Services\NewsCrawler\Contracts;

use App\Services\NewsCrawler\DTO\CrawledArticleData;

interface NewsSourceInterface
{
    /**
     * Tên nguồn tin (ví dụ: 'VnExpress', 'Tuổi Trẻ')
     */
    public function getSourceName(): string;

    /**
     * Mã định danh nguồn tin (ví dụ: 'vnexpress')
     */
    public function getSourceKey(): string;

    /**
     * Thu thập danh sách bài báo và chuyển đổi thành mảng CrawledArticleData
     *
     * @param int $limit Số lượng bài tối đa cần lấy
     * @return CrawledArticleData[]
     */
    public function fetchArticles(int $limit = 10): array;
}
