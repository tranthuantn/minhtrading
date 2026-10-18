<?php

namespace Tests\Unit;

use App\Models\Lesson;
use PHPUnit\Framework\TestCase;

class LessonVideoEmbedTest extends TestCase
{
    public function test_google_drive_view_link_converts_to_preview(): void
    {
        $lesson = new Lesson([
            'video_url' => 'https://drive.google.com/file/d/1o0hitcElZWMs12JLZ27vdwZdWlE-0ZQ_/view'
        ]);

        $this->assertEquals(
            'https://drive.google.com/file/d/1o0hitcElZWMs12JLZ27vdwZdWlE-0ZQ_/preview',
            $lesson->embed_video_url
        );
        $this->assertEquals('drive', $lesson->video_platform);
    }

    public function test_google_drive_with_usp_sharing(): void
    {
        $lesson = new Lesson([
            'video_url' => 'https://drive.google.com/file/d/1o0hitcElZWMs12JLZ27vdwZdWlE-0ZQ_/view?usp=sharing'
        ]);

        $this->assertEquals(
            'https://drive.google.com/file/d/1o0hitcElZWMs12JLZ27vdwZdWlE-0ZQ_/preview',
            $lesson->embed_video_url
        );
    }

    public function test_youtube_watch_converts_to_embed(): void
    {
        $lesson = new Lesson([
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        ]);

        $this->assertEquals(
            'https://www.youtube.com/embed/dQw4w9WgXcQ',
            $lesson->embed_video_url
        );
        $this->assertEquals('youtube', $lesson->video_platform);
    }

    public function test_youtube_short_link_converts_to_embed(): void
    {
        $lesson = new Lesson([
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ'
        ]);

        $this->assertEquals(
            'https://www.youtube.com/embed/dQw4w9WgXcQ',
            $lesson->embed_video_url
        );
    }

    public function test_iframe_extraction(): void
    {
        $lesson = new Lesson([
            'video_url' => '<iframe src="https://drive.google.com/file/d/1o0hitcElZWMs12JLZ27vdwZdWlE-0ZQ_/preview" width="640" height="480"></iframe>'
        ]);

        $this->assertEquals(
            'https://drive.google.com/file/d/1o0hitcElZWMs12JLZ27vdwZdWlE-0ZQ_/preview',
            $lesson->embed_video_url
        );
    }
}

