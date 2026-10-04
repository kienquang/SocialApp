<?php

namespace App\Console\Commands;

use App\Services\NewsCrawler\NewsCrawlerManager;
use Illuminate\Console\Command;

class CrawlNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:crawl 
                            {--source= : Nguồn tin cụ thể cần cào (ví dụ: vnexpress)} 
                            {--limit=10 : Số lượng bài viết tối đa mỗi nguồn} 
                            {--sync : Chạy xử lý đồng bộ ngay lập tức thay vì đưa vào Queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động cào tin tức từ các nguồn báo ngoài và tạo bài viết trên mạng xã hội';

    /**
     * Execute the console command.
     */
    public function handle(NewsCrawlerManager $manager): int
    {
        $source = $this->option('source');
        $limit = (int) $this->option('limit');
        $sync = (bool) $this->option('sync');

        $this->info("=== BẮT ĐẦU CÀO BÀI TỰ ĐỘNG ===");
        $this->line("Nguồn: " . ($source ?: 'Tất cả nguồn đã đăng ký'));
        $this->line("Số lượng tối đa: {$limit}");
        $this->line("Chế độ: " . ($sync ? 'Đồng bộ (Sync)' : 'Hàng đợi (Queue)'));

        $stats = $manager->crawlAndProcess(
            sourceKey: $source,
            limit: $limit,
            sync: $sync
        );

        $this->newLine();
        $this->info("=== KẾT QUẢ CÀO TIN ===");
        $this->table(
            ['Chỉ số', 'Số lượng'],
            [
                ['Tổng số bài tìm thấy', $stats['total_fetched']],
                ['Bài trùng lặp (bỏ qua)', $stats['skipped_duplicates']],
                ['Bài xử lý thành công', $stats['processed']],
            ]
        );

        return Command::SUCCESS;
    }
}
