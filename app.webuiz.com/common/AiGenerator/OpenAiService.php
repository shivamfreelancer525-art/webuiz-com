<?php

namespace Common\AiGenerator;

use Common\AiGenerator\Contracts\AiProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiService implements AiProviderInterface
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', '');
        $this->model = config('services.openai.model', 'gpt-4o');
    }

    /**
     * Generate a complete project from a user prompt
     * Note: OpenAI has 4096 output token limit, so we generate pages one by one
     */
    public function generateProject(string $prompt, string $projectType = 'landing-page'): array
    {
        $pages = $this->getRequiredPages($projectType);
        $generatedPages = [];
        
        // Generate each page separately due to token limits
        foreach ($pages as $pageName => $pageDescription) {
            $pageHtml = $this->generateSinglePage($prompt, $projectType, $pageName, $pageDescription, $generatedPages);
            $generatedPages[] = [
                'name' => $pageName,
                'html' => $pageHtml,
            ];
        }

        return [
            'pages' => $generatedPages,
            'css' => '',
            'js' => '',
        ];
    }

    /**
     * Generate a single page with context from other pages
     */
    protected function generateSinglePage(string $userPrompt, string $projectType, string $pageName, string $pageDescription, array $existingPages): string
    {
        $systemPrompt = $this->getSystemPrompt($projectType);
        
        $contextInfo = '';
        if (!empty($existingPages)) {
            $pageNames = array_map(fn($p) => $p['name'], $existingPages);
            $contextInfo = "You have already generated: " . implode(', ', $pageNames) . ".html\n";
        }

        $navigationLinks = $this->getNavigationLinks($projectType);

        $userMessage = <<<PROMPT
{$contextInfo}
Now generate the {$pageName}.html page for this website:
"{$userPrompt}"

Project Type: {$projectType}
Page: {$pageName}.html - {$pageDescription}

CRITICAL REQUIREMENTS:
1. Generate a COMPLETE, standalone HTML5 document
2. Include this exact navigation in the header:
{$navigationLinks}
3. Add "active" class to the current page's nav link
4. Use consistent styling with these colors:
   - Primary: #667eea (purple-blue gradient)
   - Secondary: #764ba2
   - Text: #333333
   - Background: #ffffff
5. Include Bootstrap 5: https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css
6. Include Font Awesome: https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css
7. Use images from https://picsum.photos/ (e.g., https://picsum.photos/800/600)
8. Make it fully responsive
9. Add modern design: gradients, shadows, hover effects
10. Include a consistent footer with navigation links

Return ONLY the complete HTML code, no explanations.
PROMPT;

        $response = $this->callOpenAiApi($systemPrompt, $userMessage);
        return $this->extractHtmlFromResponse($response);
    }

    /**
     * Call the OpenAI API
     */
    protected function callOpenAiApi(string $systemPrompt, string $userPrompt): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('OpenAI API key is not configured. Please set OPENAI_API_KEY in your .env file.');
        }

        $url = "{$this->baseUrl}/chat/completions";

        $payload = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'temperature' => 0.7,
            'max_tokens' => 4096,
        ];

        $response = Http::timeout(120)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
            ->post($url, $payload);

        if (!$response->successful()) {
            Log::error('OpenAI API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Failed to generate content: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Get system prompt based on project type
     */
    protected function getSystemPrompt(string $projectType): string
    {
        return "You are an expert full-stack web developer creating MULTI-PAGE websites. 
Generate complete, production-ready HTML5 pages with Bootstrap 5. 
Each page must be standalone with proper <head>, <body>, consistent navigation, and footer.
Use modern design with gradients, shadows, animations, and responsive layouts.
Always include working navigation links between all pages (using .html extensions).
Make the design visually stunning and professional.";
    }

    /**
     * Get required pages for project type
     */
    protected function getRequiredPages(string $projectType): array
    {
        $structures = [
            'landing-page' => [
                'index' => 'Home page with hero, features preview, testimonials, CTA',
                'features' => 'Detailed features/services with icons',
                'about' => 'About us, team, company story',
                'pricing' => 'Pricing plans with comparison',
                'contact' => 'Contact form, address, map',
            ],
            'portfolio' => [
                'index' => 'Hero intro, featured projects, skills',
                'projects' => 'Full project gallery with filters',
                'about' => 'Bio, experience timeline, skills',
                'services' => 'Services offered with pricing',
                'contact' => 'Contact form, social links',
            ],
            'business' => [
                'index' => 'Hero, services overview, client logos',
                'services' => 'Detailed services with benefits',
                'about' => 'Company history, team, values',
                'portfolio' => 'Case studies, testimonials',
                'contact' => 'Contact form, office locations',
            ],
            'blog' => [
                'index' => 'Featured posts, recent articles',
                'blog' => 'All posts with search, categories',
                'post' => 'Single article with comments',
                'about' => 'About the blog/author',
                'contact' => 'Contact form, social links',
            ],
            'ecommerce' => [
                'index' => 'Hero banner, featured products, categories',
                'products' => 'Product grid with filters',
                'product' => 'Single product detail, reviews',
                'about' => 'Brand story, values',
                'contact' => 'Support, FAQ, contact form',
            ],
        ];

        return $structures[$projectType] ?? $structures['landing-page'];
    }

    /**
     * Get navigation links HTML
     */
    protected function getNavigationLinks(string $projectType): string
    {
        $pages = $this->getRequiredPages($projectType);
        $links = [];
        foreach (array_keys($pages) as $page) {
            $label = ucfirst($page);
            if ($page === 'index') $label = 'Home';
            $links[] = "<a href=\"{$page}.html\" class=\"nav-link\">{$label}</a>";
        }
        return implode("\n", $links);
    }

    /**
     * Extract HTML from response
     */
    protected function extractHtmlFromResponse(array $response): string
    {
        $content = $response['choices'][0]['message']['content'] ?? '';

        // Try to extract HTML from markdown code blocks
        if (preg_match('/```(?:html)?\s*([\s\S]*?)```/', $content, $matches)) {
            return trim($matches[1]);
        }

        // If it looks like HTML, return as-is
        if (str_contains($content, '<!DOCTYPE') || str_contains($content, '<html')) {
            return $content;
        }

        // Wrap in basic HTML structure
        return $this->wrapInHtmlDocument($content);
    }

    /**
     * Wrap content in a basic HTML document
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

    /**
     * Check if the API key is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Get provider name
     */
    public function getName(): string
    {
        return 'openai';
    }

    /**
     * Get available project types
     */
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
