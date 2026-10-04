<?php

namespace App\Services\NewsCrawler\Sources;

use App\Services\NewsCrawler\Contracts\NewsSourceInterface;
use App\Services\NewsCrawler\DTO\CrawledArticleData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VnExpressSource implements NewsSourceInterface
{
    /**
     * Danh sách RSS feeds kèm category ID tương ứng trong bảng categories
     */
    protected array $feeds = [
        [
            'url' => 'https://vnexpress.net/rss/so-hoa.rss',
            'category_id' => 3, // Công nghệ
        ],
        [
            'url' => 'https://vnexpress.net/rss/khoa-hoc.rss',
            'category_id' => 4, // Khoa học
        ],
        [
            'url' => 'https://vnexpress.net/rss/giai-tri.rss',
            'category_id' => 5, // Giải trí
        ],
        [
            'url' => 'https://vnexpress.net/rss/du-lich.rss',
            'category_id' => 1, // Du lịch - Khám phá
        ],
        [
            'url' => 'https://vnexpress.net/rss/tin-moi-nhat.rss',
            'category_id' => 2, // Văn hóa - Xã hội
        ],
    ];

    public function getSourceName(): string
    {
        return 'VnExpress';
    }

    public function getSourceKey(): string
    {
        return 'vnexpress';
    }

    /**
     * @param int $limit Số bài tối đa thu thập
     * @return CrawledArticleData[]
     */
    public function fetchArticles(int $limit = 10): array
    {
        $articles = [];

        foreach ($this->feeds as $feedConfig) {
            if (count($articles) >= $limit) {
                break;
            }

            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) SocialAppNewsBot/1.0',
                    ])
                    ->get($feedConfig['url']);

                if (!$response->successful()) {
                    Log::warning("VnExpress RSS request failed for URL: {$feedConfig['url']}");
                    continue;
                }

                $xml = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
                if (!$xml || !isset($xml->channel->item)) {
                    continue;
                }

                foreach ($xml->channel->item as $item) {
                    if (count($articles) >= $limit) {
                        break 2;
                    }

                    $title = trim((string) $item->title);
                    $link = trim((string) $item->link);
                    $descriptionRaw = (string) $item->description;

                    // Trích xuất ảnh thumbnail từ thẻ <img src="..."> trong description
                    $thumbnailUrl = null;
                    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $descriptionRaw, $matches)) {
                        $thumbnailUrl = $matches[1];
                    }

                    // Tách đoạn text mô tả sạch (loại bỏ thẻ html ban đầu)
                    $cleanSummary = trim(strip_tags($descriptionRaw));

                    // Định dạng thời gian xuất bản gốc của bài báo (không dùng icon, in nghiêng)
                    $publishedInfoHtml = '';
                    if (isset($item->pubDate) && !empty((string) $item->pubDate)) {
                        try {
                            $formattedDate = \Carbon\Carbon::parse((string) $item->pubDate)
                                ->timezone('Asia/Ho_Chi_Minh')
                                ->format('H:i \n\g\à\y d/m/Y');
                            $publishedInfoHtml = "<p><em>Xuất bản lúc {$formattedDate} trên VnExpress</em></p>";
                        } catch (\Throwable) {
                            // Bỏ qua nếu lỗi format ngày
                        }
                    }

                    // Tạo nội dung HTML bài viết chuẩn, đẹp và có dẫn nguồn rõ ràng
                    $contentHtml = "<p><strong>{$cleanSummary}</strong></p>";
                    if ($thumbnailUrl) {
                        $contentHtml .= "<p><img src=\"{$thumbnailUrl}\" alt=\"{$title}\" style=\"max-width:100%; height:auto; border-radius:8px;\" /></p>";
                    }
                    if ($publishedInfoHtml) {
                        $contentHtml .= $publishedInfoHtml;
                    }
                    $contentHtml .= "<p><em>Xem chi tiết bài viết gốc tại <a href=\"{$link}\" target=\"_blank\" rel=\"noopener noreferrer\">VnExpress</a>.</em></p>";

                    $articles[] = new CrawledArticleData(
                        title: $title,
                        contentHtml: $contentHtml,
                        sourceUrl: $link,
                        sourceName: $this->getSourceName(),
                        thumbnailUrl: $thumbnailUrl,
                        categoryId: $feedConfig['category_id']
                    );
                }
            } catch (\Throwable $e) {
                Log::error("Error crawling VnExpress RSS [{$feedConfig['url']}]: " . $e->getMessage());
            }
        }

        return $articles;
    }
}
