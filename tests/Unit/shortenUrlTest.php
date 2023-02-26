<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Url;

class ShortenUrlTest extends TestCase
{
    use RefreshDatabase;
    
    public function testShortenUrl()
    {
        $url = 'https://www.example.com';
        
        $response = $this->post('/api/shorten', ['url' => $url]);
        
        $response->assertStatus(200);
        $response->assertJsonStructure(['short_url']);
        
        $shortUrl = $response->json()['short_url'];
        
        $this->assertDatabaseHas('urls', [
            'original_url' => $url,
            'short_url' => $shortUrl,
        ]);
    }
    
}
