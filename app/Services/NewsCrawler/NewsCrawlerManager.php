<?php

namespace App\Services\NewsCrawler;

use App\Jobs\ProcessCrawledArticleJob;
use App\Models\Post;
use App\Services\NewsCrawler\Contracts\NewsSourceInterface;
use App\Services\NewsCrawler\Sources\VnExpressSource;
use Illuminate\Support\Facades\Log;

class NewsCrawlerManager
{
    /**
     * @var NewsSourceInterface[]
     */
    protected array $sources = [];

    public function __construct()
    {
        // Đăng ký các nguồn báo mặc định tại đây
        $this->registerSource(new VnExpressSource());
    }

    /**
     * Đăng ký một nguồn báo mới
     */
    public function registerSource(NewsSourceInterface $source): self
    {
        $this->sources[$source->getSourceKey()] = $source;
        return $this;
    }

    /**
     * Lấy danh sách các nguồn báo khả dụng
     */
    public function getAvailableSources(): array
    {
        return array_keys($this->sources);
    }

    /**
     * Thực hiện cào tin và dispatch vào Queue hoặc xử lý trực tiếp
     *
     * @param string|null $sourceKey Chỉ định nguồn (hoặc null để cào tất cả)
     * @param int $limit Số lượng bài mỗi nguồn
     * @param bool $sync Chạy trực tiếp (true) hay đưa vào Queue Job (false)
     * @return array Kết quả thống kê
     */
    public function crawlAndProcess(?string $sourceKey = null, int $limit = 10, bool $sync = false): array
    {
        $selectedSources = [];
        if ($sourceKey && isset($this->sources[$sourceKey])) {
            $selectedSources[] = $this->sources[$sourceKey];
        } else {
            $selectedSources = array_values($this->sources);
        }

        $stats = [
            'total_fetched' => 0,
            'skipped_duplicates' => 0,
            'processed' => 0,
        ];

        foreach ($selectedSources as $source) {
            $articles = $source->fetchArticles($limit);
            $stats['total_fetched'] += count($articles);

            foreach ($articles as $articleData) {
                // Kiểm tra xem bài báo đã tồn tại trong DB chưa
                if (Post::where('source_url', $articleData->sourceUrl)->exists()) {
                    $stats['skipped_duplicates']++;
                    continue;
                }

                if ($sync) {
                    (new ProcessCrawledArticleJob($articleData))->handle();
                } else {
                    dispatch(new ProcessCrawledArticleJob($articleData));
                }

                $stats['processed']++;
            }
        }

        Log::info("NewsCrawler finished: Fetched {$stats['total_fetched']}, Processed {$stats['processed']}, Skipped {$stats['skipped_duplicates']}");

        return $stats;
    }
}
