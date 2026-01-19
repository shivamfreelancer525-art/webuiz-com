<?php

namespace Tests\Feature;

use App\Models\User;
use Common\AiGenerator\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiProjectGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_requires_authentication_for_project_generation(): void
    {
        $response = $this->postJson('/api/v1/ai/generate-project', [
            'prompt' => 'Create a portfolio website',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_validates_prompt_minimum_length(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/ai/generate-project', [
                'prompt' => 'short',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['prompt']);
    }

    /** @test */
    public function it_returns_project_types(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/ai/project-types');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'types' => [
                '*' => ['id', 'name', 'description', 'icon']
            ],
            'configured',
        ]);
    }

    /** @test */
    public function it_generates_project_with_valid_prompt(): void
    {
        // Mock the Gemini API response
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'pages' => [
                                            [
                                                'name' => 'index',
                                                'html' => '<!DOCTYPE html><html><body><h1>Test</h1></body></html>'
                                            ]
                                        ],
                                        'css' => '.test { color: red; }',
                                        'js' => 'console.log("test");',
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        // Set fake API key for testing
        config(['services.gemini.api_key' => 'test-api-key']);

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/ai/generate-project', [
                'prompt' => 'Create a modern portfolio website for a photographer with dark theme and image gallery',
                'type' => 'portfolio',
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'project',
            'generated' => [
                'pages_count',
                'has_custom_css',
                'has_custom_js',
            ],
            'message',
        ]);
    }

    /** @test */
    public function gemini_service_returns_correct_project_types(): void
    {
        $service = new GeminiService();
        $types = $service->getProjectTypes();

        $this->assertIsArray($types);
        $this->assertCount(5, $types);
        
        $typeIds = array_column($types, 'id');
        $this->assertContains('landing-page', $typeIds);
        $this->assertContains('portfolio', $typeIds);
        $this->assertContains('business', $typeIds);
        $this->assertContains('blog', $typeIds);
        $this->assertContains('ecommerce', $typeIds);
    }

    /** @test */
    public function gemini_service_detects_configuration_status(): void
    {
        // Without API key
        config(['services.gemini.api_key' => null]);
        $service = new GeminiService();
        $this->assertFalse($service->isConfigured());

        // With API key
        config(['services.gemini.api_key' => 'test-key']);
        $service = new GeminiService();
        $this->assertTrue($service->isConfigured());
    }
}
