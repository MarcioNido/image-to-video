<?php

namespace Tests\Http\Controllers;

use App\Jobs\ProcessVideo;
use App\Models\Soundtrack;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VideoControllerStoreTest extends TestCase
{
    public function test_it_should_fail_if_user_is_not_authenticated()
    {
        $response = $this->postJson(route('videos.store'), []);

        $response->assertUnauthorized();
    }

    public function test_should_pass_if_data_is_valid()
    {
        Storage::fake('image-files');
        Queue::fake(ProcessVideo::class);

        $soundtrack = Soundtrack::factory()->create();
        $image1 = $this->createImage();
        $image2 = $this->createImage();
        $image3 = $this->createImage();
        $image4 = $this->createImage();
        $image5 = $this->createImage();
        $texts = json_encode([
            [
                "title" => "Apartamento Vila Mariana",
                "subtitle" => "3 quartos, 1 suíte, 2 banheiros",
                "x" => 30,
                "y" => 250,
                "start_time" => 5,
                "duration" => 10,
            ],
        ]);

        $response = $this->actingAsIntegrationUser()->postJson(route('videos.store'), [
            'soundtrack_id' => $soundtrack->id,
            'images' => [
                $image1,
                $image2,
                $image3,
                $image4,
                $image5,
            ],
            'webhook' => 'https://example.com',
            'texts' => $texts,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('videos', [
            'soundtrack_id' => $soundtrack->id,
            'webhook' => 'https://example.com',
        ]);

        $this->assertDatabaseCount('video_images', 5);

        $videoId = $response->json('id');

        Storage::disk('image-files')->assertExists("{$videoId}/{$image1->hashName()}");
        Storage::disk('image-files')->assertExists("{$videoId}/{$image2->hashName()}");
        Storage::disk('image-files')->assertExists("{$videoId}/{$image3->hashName()}");
        Storage::disk('image-files')->assertExists("{$videoId}/{$image4->hashName()}");
        Storage::disk('image-files')->assertExists("{$videoId}/{$image5->hashName()}");

        $response->assertJsonStructure([
            'id',
            'soundtrack_id',
            'webhook',
            'images' => [
                '*' => [
                    'id',
                    'path',
                    'order',
                ],
            ],
        ]);

        Queue::assertPushed(ProcessVideo::class);

    }

    private function createImage(): File
    {
        return UploadedFile::fake()->image('image.jpg');
    }
}
