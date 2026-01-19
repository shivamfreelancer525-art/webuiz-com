<?php

namespace Common\AiGenerator;

use Common\AiGenerator\Contracts\AiProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService implements AiProviderInterface
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');
        $this->model = config('services.gemini.model', 'gemini-2.5-flash');
    }

    /**
     * Generate a complete project from a user prompt
     * Using page-by-page generation for reliability
     */
    public function generateProject(string $prompt, string $projectType = 'landing-page'): array
    {
        $pages = $this->getRequiredPages($projectType);
        $generatedPages = [];
        $designContext = $this->generateDesignContext($prompt, $projectType);

        // Generate each page separately for better reliability
        foreach ($pages as $pageName => $pageDescription) {
            try {
                $pageHtml = $this->generateSinglePage(
                    $prompt,
                    $projectType,
                    $pageName,
                    $pageDescription,
                    $designContext,
                    array_keys($pages)
                );
                $generatedPages[] = [
                    'name' => $pageName,
                    'html' => $pageHtml,
                ];
            } catch (\Exception $e) {
                Log::error("Failed to generate page: $pageName", ['error' => $e->getMessage()]);
                // Create a fallback page
                $generatedPages[] = [
                    'name' => $pageName,
                    'html' => $this->createFallbackPage($pageName, $pageDescription, array_keys($pages)),
                ];
            }
        }

        return [
            'pages' => $generatedPages,
            'css' => '',
            'js' => '',
        ];
    }

    /**
     * Generate design context for consistent styling
     */
    protected function generateDesignContext(string $prompt, string $projectType): array
    {
        // Extract color preferences from prompt
        $colors = $this->extractColors($prompt);
        
        return [
            'primaryColor' => $colors['primary'] ?? '#667eea',
            'secondaryColor' => $colors['secondary'] ?? '#764ba2',
            'accentColor' => $colors['accent'] ?? '#f093fb',
            'projectType' => $projectType,
        ];
    }

    /**
     * Extract color preferences from prompt
     */
    protected function extractColors(string $prompt): array
    {
        $promptLower = strtolower($prompt);
        
        $colorMappings = [
            'blue' => ['primary' => '#4f46e5', 'secondary' => '#3b82f6'],
            'green' => ['primary' => '#059669', 'secondary' => '#10b981'],
            'red' => ['primary' => '#dc2626', 'secondary' => '#ef4444'],
            'purple' => ['primary' => '#7c3aed', 'secondary' => '#8b5cf6'],
            'pink' => ['primary' => '#db2777', 'secondary' => '#ec4899'],
            'orange' => ['primary' => '#ea580c', 'secondary' => '#f97316'],
            'dark' => ['primary' => '#1f2937', 'secondary' => '#374151'],
            'black' => ['primary' => '#111827', 'secondary' => '#1f2937'],
            'gold' => ['primary' => '#b8860b', 'secondary' => '#daa520', 'accent' => '#ffd700'],
        ];

        foreach ($colorMappings as $color => $values) {
            if (str_contains($promptLower, $color)) {
                return $values;
            }
        }

        return [];
    }

    /**
     * Generate a single page with context
     */
    protected function generateSinglePage(
        string $userPrompt,
        string $projectType,
        string $pageName,
        string $pageDescription,
        array $designContext,
        array $allPageNames
    ): string {
        $systemPrompt = $this->getPageSystemPrompt();
        $navLinks = $this->buildNavigation($allPageNames, $pageName);
        
        $primaryColor = $designContext['primaryColor'];
        $secondaryColor = $designContext['secondaryColor'];

        $userMessage = <<<PROMPT
Generate a COMPLETE, standalone HTML5 page for a {$projectType} website.

PROJECT DESCRIPTION: "{$userPrompt}"

PAGE TO GENERATE: {$pageName}.html
PAGE PURPOSE: {$pageDescription}

EXACT REQUIREMENTS:
1. Start with <!DOCTYPE html> and include complete <head> with meta tags
2. Use this color scheme: Primary: {$primaryColor}, Secondary: {$secondaryColor}
3. Include Bootstrap 5: https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css
4. Include Font Awesome: https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css
5. Include Bootstrap JS at end: https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js

NAVIGATION HEADER (copy exactly, add "active" class to current page):
{$navLinks}

DESIGN REQUIREMENTS:
- Modern gradient backgrounds using the color scheme
- Card shadows: box-shadow: 0 10px 40px rgba(0,0,0,0.1)
- Smooth transitions on hover
- Professional typography (use system fonts or Google Fonts)
- Hero section with gradient overlay on images
- Use https://picsum.photos/800/600 for images (vary sizes as needed)
- Responsive design with Bootstrap grid
- Footer with same nav links and copyright

CONTENT: Generate realistic, engaging content appropriate for the page purpose.

OUTPUT: Return ONLY the complete HTML code, no explanations or markdown.
PROMPT;

        $response = $this->callGeminiApi($systemPrompt, $userMessage, false);
        return $this->extractHtmlFromResponse($response);
    }

    /**
     * Build navigation HTML
     */
    protected function buildNavigation(array $pageNames, string $currentPage): string
    {
        $links = [];
        foreach ($pageNames as $page) {
            $label = $this->getPageLabel($page);
            $activeClass = ($page === $currentPage) ? ' active' : '';
            $links[] = "<a href=\"{$page}.html\" class=\"nav-link{$activeClass}\">{$label}</a>";
        }
        
        return <<<NAV
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.html">Brand</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
NAV . "\n" . implode("\n", array_map(fn($l) => "                <li class=\"nav-item\">{$l}</li>", $links)) . <<<NAV

            </ul>
        </div>
    </div>
</nav>
NAV;
    }

    /**
     * Get human-readable page label
     */
    protected function getPageLabel(string $pageName): string
    {
        $labels = [
            'index' => 'Home',
            'about' => 'About',
            'services' => 'Services',
            'features' => 'Features',
            'portfolio' => 'Portfolio',
            'projects' => 'Projects',
            'blog' => 'Blog',
            'post' => 'Article',
            'products' => 'Products',
            'product' => 'Product',
            'pricing' => 'Pricing',
            'contact' => 'Contact',
        ];
        return $labels[$pageName] ?? ucfirst($pageName);
    }

    /**
     * Call the Gemini API
     */
    protected function callGeminiApi(string $systemPrompt, string $userPrompt, bool $jsonMode = false): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('Gemini API key is not configured. Please set GEMINI_API_KEY in your .env file.');
        }

        $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        $generationConfig = [
            'temperature' => 0.7,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 8192,
        ];

        if ($jsonMode) {
            $generationConfig['responseMimeType'] = 'application/json';
        }

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemPrompt . "\n\n" . $userPrompt]
                    ]
                ]
            ],
            'generationConfig' => $generationConfig,
        ];

        $response = Http::timeout(180)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($url, $payload);

        if (!$response->successful()) {
            Log::error('Gemini API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Failed to generate content: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Get system prompt for page generation
     */
    protected function getPageSystemPrompt(): string
    {
        return "You are an expert web developer creating professional, modern websites. Generate complete, valid HTML5 pages with inline styles and Bootstrap 5. Create visually stunning designs with gradients, shadows, and smooth animations. Always output clean, production-ready HTML code only.";
    }

    /**
     * Get system prompt based on project type
     */
    protected function getSystemPrompt(string $projectType): string
    {
        $prompts = [
            'landing-page' => 'Create modern landing pages with compelling CTAs, hero sections, and professional design.',
            'portfolio' => 'Create elegant portfolio websites showcasing work with modern gallery layouts.',
            'business' => 'Create professional business websites with corporate styling and trust elements.',
            'blog' => 'Create clean, readable blog templates with excellent typography.',
            'ecommerce' => 'Create product showcase pages with modern e-commerce patterns.',
        ];

        return $prompts[$projectType] ?? $prompts['landing-page'];
    }

    /**
     * Get required pages for project type
     */
    protected function getRequiredPages(string $projectType): array
    {
        $structures = [
            'landing-page' => [
                'index' => 'Home page with hero section, key features, testimonials, and call-to-action',
                'features' => 'Detailed features page with icons, descriptions, and benefits',
                'about' => 'About us page with team info, company story, and values',
                'pricing' => 'Pricing plans with comparison table and FAQ',
                'contact' => 'Contact page with form, address, map placeholder, and social links',
            ],
            'portfolio' => [
                'index' => 'Portfolio home with hero intro and featured works showcase',
                'projects' => 'Full projects gallery with filter categories',
                'about' => 'About page with bio, skills, experience timeline',
                'services' => 'Services offered with pricing and process',
                'contact' => 'Contact page with form and social media links',
            ],
            'business' => [
                'index' => 'Business home with hero, services overview, and client testimonials',
                'services' => 'Detailed services with benefits and process',
                'about' => 'Company history, team members, and core values',
                'portfolio' => 'Case studies and client success stories',
                'contact' => 'Contact form, multiple office locations, support info',
            ],
            'blog' => [
                'index' => 'Blog home with featured articles and category highlights',
                'blog' => 'All posts with search, categories, and pagination',
                'post' => 'Single article template with author bio and related posts',
                'about' => 'About the blog/author with subscribe form',
                'contact' => 'Contact page with form and social links',
            ],
            'ecommerce' => [
                'index' => 'E-commerce home with hero banner, featured products, and categories',
                'products' => 'Product catalog with filters, sort, and grid/list view',
                'product' => 'Single product page with gallery, details, and reviews',
                'about' => 'Brand story, values, and sustainability info',
                'contact' => 'Customer support, FAQ, and contact form',
            ],
        ];

        return $structures[$projectType] ?? $structures['landing-page'];
    }

    /**
     * Extract HTML from response
     */
    protected function extractHtmlFromResponse(array $response): string
    {
        $content = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';

        // Remove markdown code blocks if present
        if (preg_match('/```(?:html)?\s*([\s\S]*?)```/', $content, $matches)) {
            $content = trim($matches[1]);
        }

        // Clean up any leading/trailing whitespace
        $content = trim($content);

        // If it starts with DOCTYPE or html, return it
        if (str_starts_with($content, '<!DOCTYPE') || str_starts_with($content, '<html')) {
            return $content;
        }

        // Wrap in basic HTML structure if needed
        return $this->wrapInHtmlDocument($content);
    }

    /**
     * Create fallback page when generation fails
     */
    protected function createFallbackPage(string $pageName, string $description, array $allPages): string
    {
        $navLinks = $this->buildNavigation($allPages, $pageName);
        $title = $this->getPageLabel($pageName);

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body { padding-top: 76px; }
        .hero { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 100px 0; }
    </style>
</head>
<body>
    {$navLinks}
    
    <section class="hero text-center">
        <div class="container">
            <h1>{$title}</h1>
            <p class="lead">{$description}</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-8 mx-auto text-center">
                    <p>This page is being set up. Please check back soon!</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 Your Website. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
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
    <style>
        body { padding-top: 76px; }
    </style>
</head>
<body>
    {$content}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
    }

    /**
     * Generate HTML for a single page (legacy method)
     */
    public function generateHtml(string $prompt): string
    {
        $systemPrompt = "You are an expert web developer. Generate clean, modern, responsive HTML with inline CSS. Use Bootstrap 5. Create visually stunning designs.";

        $response = $this->callGeminiApi($systemPrompt, $prompt, false);

        return $this->extractHtmlFromResponse($response);
    }

    /**
     * Check if the API key is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Get available project types
     */
    public function getProjectTypes(): array
    {
        return [
            ['id' => 'landing-page', 'name' => 'Landing Page', 'description' => 'High-converting landing pages with CTAs', 'icon' => 'fa-rocket'],
            ['id' => 'portfolio', 'name' => 'Portfolio', 'description' => 'Showcase your work elegantly', 'icon' => 'fa-briefcase'],
            ['id' => 'business', 'name' => 'Business Website', 'description' => 'Professional corporate sites', 'icon' => 'fa-building'],
            ['id' => 'blog', 'name' => 'Blog', 'description' => 'Modern blog templates', 'icon' => 'fa-newspaper'],
            ['id' => 'ecommerce', 'name' => 'E-commerce Landing', 'description' => 'Product showcase pages', 'icon' => 'fa-shopping-cart'],
        ];
    }

    /**
     * Get the provider name
     */
    public function getName(): string
    {
        return 'gemini';
    }
}
