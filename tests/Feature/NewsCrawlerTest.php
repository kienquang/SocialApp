<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Services\NewsCrawler\DTO\CrawledArticleData;
use App\Jobs\ProcessCrawledArticleJob;
use Tests\TestCase;

class NewsCrawlerTest extends TestCase
{
    public function test_can_process_and_save_crawled_article()
    {
        $uniqueUrl = 'https://vnexpress.net/test-article-' . uniqid() . '.html';

        $dto = new CrawledArticleData(
            title: 'Tiêu đề bài báo thử nghiệm',
            contentHtml: '<p>Nội dung thử nghiệm</p>',
            sourceUrl: $uniqueUrl,
            sourceName: 'VnExpress',
            thumbnailUrl: 'https://example.com/thumb.jpg',
            categoryId: 1
        );

        // Chạy job xử lý
        (new ProcessCrawledArticleJob($dto))->handle();

        // Kiểm tra bài viết đã được lưu
        $post = Post::where('source_url', $uniqueUrl)->first();
        $this->assertNotNull($post);
        $this->assertEquals('Tiêu đề bài báo thử nghiệm', $post->title);
        $this->assertEquals('VnExpress', $post->source_name);
        $this->assertEquals('published', $post->status);

        // Kiểm tra tác giả là bot
        $this->assertStringContainsString('Bot VnExpress', $post->user->name);
    }

    public function test_skips_duplicate_crawled_article()
    {
        $uniqueUrl = 'https://vnexpress.net/test-duplicate-' . uniqid() . '.html';

        $dto = new CrawledArticleData(
            title: 'Bài báo gốc',
            contentHtml: '<p>Nội dung gốc</p>',
            sourceUrl: $uniqueUrl,
            sourceName: 'VnExpress',
            thumbnailUrl: 'https://example.com/thumb.jpg',
            categoryId: 1
        );

        (new ProcessCrawledArticleJob($dto))->handle();
        $initialCount = Post::where('source_url', $uniqueUrl)->count();

        // Chạy lại lần 2 với cùng URL
        (new ProcessCrawledArticleJob($dto))->handle();
        $secondCount = Post::where('source_url', $uniqueUrl)->count();

        $this->assertEquals(1, $initialCount);
        $this->assertEquals(1, $secondCount);
    }
}
