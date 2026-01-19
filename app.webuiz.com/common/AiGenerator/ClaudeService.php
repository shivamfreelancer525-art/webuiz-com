<?php

namespace Common\AiGenerator;

use Common\AiGenerator\Contracts\AiProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeService implements AiProviderInterface
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://api.anthropic.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.claude.api_key', '');
        $this->model = config('services.claude.model', 'claude-3-5-sonnet-20241022');
    }

    /**
     * Generate a complete project from a user prompt
     * Claude has 8192 output tokens, allowing for more content per request
     */
    public function generateProject(string $prompt, string $projectType = 'landing-page'): array
    {
        $systemPrompt = $this->getSystemPrompt($projectType);
        $userPrompt = $this->buildProjectPrompt($prompt, $projectType);

        $response = $this->callClaudeApi($systemPrompt, $userPrompt);

        return $this->parseProjectResponse($response);
    }

    /**
     * Call the Claude API
     */
    protected function callClaudeApi(string $systemPrompt, string $userPrompt): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('Claude API key is not configured. Please set ANTHROPIC_API_KEY in your .env file.');
        }

        $url = "{$this->baseUrl}/messages";

        $payload = [
            'model' => $this->model,
            'max_tokens' => 8192,
            'system' => $systemPrompt,
            'messages' => [
                ['role' => 'user', 'content' => $userPrompt],
            ],
        ];

        $response = Http::timeout(300)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
            ])
            ->post($url, $payload);

        if (!$response->successful()) {
            Log::error('Claude API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Failed to generate content: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Get system prompt
     */
    protected function getSystemPrompt(string $projectType): string
    {
        return "You are an expert full-stack web developer creating MULTI-PAGE websites.
Generate complete, production-ready HTML5 pages with Bootstrap 5.
Each page must be standalone with proper <head>, <body>, consistent navigation header, and footer.
Use modern design with gradients, shadows, animations, and responsive layouts.
All navigation links must use .html extensions (e.g., about.html, contact.html).
Make the design visually stunning, professional, and user-interactive.
Always respond with valid JSON containing the pages array.";
    }

    /**
     * Build the complete project prompt
     */
    protected function buildProjectPrompt(string $userPrompt, string $projectType): string
    {
        $pageStructure = $this->getPageStructure($projectType);

        return <<<PROMPT
Create a complete, production-ready MULTI-PAGE website based on this description:
"{$userPrompt}"

Project Type: {$projectType}

{$pageStructure}

IMPORTANT: Respond ONLY with valid JSON in this exact format:
{
    "pages": [
        {"name": "index", "html": "<!DOCTYPE html>..."},
        {"name": "about", "html": "<!DOCTYPE html>..."},
        {"name": "features", "html": "<!DOCTYPE html>..."},
        {"name": "contact", "html": "<!DOCTYPE html>..."}
    ],
    "css": "",
    "js": ""
}

REQUIREMENTS:
1. Each page MUST be a complete HTML5 document
2. Use Bootstrap 5 CDN: https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css
3. Use Font Awesome CDN: https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css
4. All navigation links use .html extension (href="about.html")
5. Consistent styling across all pages (same color scheme, fonts)
6. Use https://picsum.photos/ for images
7. Modern design with gradients, shadows, hover effects
8. Fully responsive for mobile/tablet/desktop
9. Add current page "active" class in navigation
10. Include working mobile hamburger menu

Generate the complete JSON response now.
PROMPT;
    }

    /**
     * Get page structure for project type
     */
    protected function getPageStructure(string $projectType): string
    {
        $structures = [
            'landing-page' => 'REQUIRED PAGES: index.html (hero, features, testimonials), features.html (detailed features), about.html (team, story), pricing.html (plans), contact.html (form, map)',
            'portfolio' => 'REQUIRED PAGES: index.html (hero, featured work), projects.html (gallery), about.html (bio, skills), services.html (offerings), contact.html (form)',
            'business' => 'REQUIRED PAGES: index.html (hero, services), services.html (detailed), about.html (team, history), portfolio.html (case studies), contact.html (locations)',
            'blog' => 'REQUIRED PAGES: index.html (featured posts), blog.html (all posts), post.html (single article), about.html (author), contact.html (form)',
            'ecommerce' => 'REQUIRED PAGES: index.html (featured products), products.html (catalog), product.html (detail), about.html (brand story), contact.html (support)',
        ];

        return $structures[$projectType] ?? $structures['landing-page'];
    }

    /**
     * Parse the project response
     */
    protected function parseProjectResponse(array $response): array
    {
        $content = $response['content'][0]['text'] ?? '';

        // Try to parse as JSON
        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Try to extract JSON from the response
            if (preg_match('/\{[\s\S]*"pages"[\s\S]*\}/', $content, $matches)) {
                $decoded = json_decode($matches[0], true);
            }
        }

        if (!$decoded || !isset($decoded['pages'])) {
            // Fallback: wrap raw content as a single page
            return [
                'pages' => [
                    ['name' => 'index', 'html' => $this->wrapInHtmlDocument($content)]
                ],
                'css' => '',
                'js' => '',
            ];
        }

        return [
            'pages' => $decoded['pages'] ?? [],
            'css' => $decoded['css'] ?? '',
            'js' => $decoded['js'] ?? '',
        ];
    }

    /**
     * Wrap content in HTML document
     */
    protected function wrapInHtmlDocument(string $content): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
</head>
<body>
    {$content}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function getName(): string
    {
        return 'claude';
    }

    public function getProjectTypes(): array
    {
        return [
            ['id' => 'landing-page', 'name' => 'Landing Page', 'description' => 'High-converting landing pages', 'icon' => 'fa-rocket'],
            ['id' => 'portfolio', 'name' => 'Portfolio', 'description' => 'Showcase your work', 'icon' => 'fa-briefcase'],
            ['id' => 'business', 'name' => 'Business Website', 'description' => 'Professional corporate sites', 'icon' => 'fa-building'],
            ['id' => 'blog', 'name' => 'Blog', 'description' => 'Modern blog templates', 'icon' => 'fa-newspaper'],
            ['id' => 'ecommerce', 'name' => 'E-commerce Landing', 'description' => 'Product showcase pages', 'icon' => 'fa-shopping-cart'],
        ];
    }
}
