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
            $pageHtml = null;
            $maxRetries = 2;
            
            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    $pageHtml = $this->generateSinglePage(
                        $prompt,
                        $projectType,
                        $pageName,
                        $pageDescription,
                        $designContext,
                        array_keys($pages)
                    );
                    
                    // Validate content - must have substantial HTML
                    if ($this->isValidPageContent($pageHtml)) {
                        break; // Success, exit retry loop
                    }
                    
                    Log::warning("Page $pageName attempt $attempt: Content too short or invalid, retrying...");
                    $pageHtml = null; // Reset for retry
                    
                } catch (\Exception $e) {
                    Log::error("Failed to generate page: $pageName (attempt $attempt)", ['error' => $e->getMessage()]);
                    $pageHtml = null;
                }
            }
            
            // If still no valid content, use comprehensive fallback
            if (!$pageHtml || !$this->isValidPageContent($pageHtml)) {
                Log::warning("Using fallback for page: $pageName after $maxRetries attempts");
                $pageHtml = $this->createComprehensiveFallbackPage($pageName, $pageDescription, array_keys($pages), $prompt, $designContext);
            }
            
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
     * Validate if page content is substantial
     */
    protected function isValidPageContent(string $html): bool
    {
        // Must be at least 500 characters to be considered valid
        if (strlen($html) < 500) {
            return false;
        }
        
        // Must contain body content (not just head/nav)
        if (!str_contains($html, '<section') && !str_contains($html, '<main') && !str_contains($html, '<div class="container"')) {
            return false;
        }
        
        return true;
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
Generate a COMPLETE, production-ready HTML5 page with FULL CONTENT for a {$projectType} website.

PROJECT DESCRIPTION: "{$userPrompt}"

PAGE TO GENERATE: {$pageName}.html
PAGE PURPOSE: {$pageDescription}

CRITICAL REQUIREMENTS - YOU MUST FOLLOW ALL:

1. STRUCTURE: Start with <!DOCTYPE html>, include complete <head> with:
   - Charset UTF-8
   - Viewport meta tag
   - Descriptive <title>
   - Bootstrap 5 CSS: https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css
   - Font Awesome: https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css
   - Custom <style> section with modern CSS

2. COLORS: Use Primary: {$primaryColor}, Secondary: {$secondaryColor}

3. NAVIGATION (use this exact structure, mark current page as active):
{$navLinks}

4. PAGE CONTENT - THIS IS MANDATORY:
   - Hero section with gradient background and compelling headline
   - At least 3-4 content sections with real, meaningful content
   - Use Bootstrap cards, grids, icons (fa-solid fa-*)
   - Images from https://picsum.photos/[width]/[height] (use 800/600, 600/400, 400/300 sizes)
   - Call-to-action buttons with proper styling
   - Testimonials or quotes where appropriate
   - Icons from Font Awesome (fa-*) to enhance visual appeal

5. DESIGN ELEMENTS:
   - Gradient backgrounds: linear-gradient(135deg, {$primaryColor}, {$secondaryColor})
   - Card shadows: 0 10px 40px rgba(0,0,0,0.1)
   - Hover transitions: transition: all 0.3s ease
   - Rounded corners: border-radius: 1rem
   - Professional spacing: padding, margins using Bootstrap utilities

6. FOOTER: Include footer with:
   - Navigation links
   - Social media icons (fa-facebook, fa-twitter, fa-instagram, fa-linkedin)
   - Copyright notice

7. SCRIPTS: Bootstrap JS at end: https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js

IMPORTANT: Generate substantial content. Do NOT leave sections empty. 
Each section should have real text content, not just placeholders.

OUTPUT: Return ONLY the complete HTML code. No explanations, no markdown code blocks.
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
     * Create a comprehensive fallback page with full content
     */
    protected function createComprehensiveFallbackPage(string $pageName, string $description, array $allPages, string $userPrompt, array $designContext): string
    {
        $navLinks = $this->buildNavigation($allPages, $pageName);
        $title = $this->getPageLabel($pageName);
        $primary = $designContext['primaryColor'] ?? '#667eea';
        $secondary = $designContext['secondaryColor'] ?? '#764ba2';
        
        // Get page-specific content
        $pageContent = $this->getPageSpecificContent($pageName, $userPrompt);
        
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
        .hero { 
            background: linear-gradient(135deg, {$primary} 0%, {$secondary} 100%); 
            color: white; 
            padding: 80px 0;
        }
        .card { 
            border: none; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.1); 
            border-radius: 1rem;
            transition: transform 0.3s ease;
        }
        .card:hover { transform: translateY(-5px); }
        .btn-primary { 
            background: linear-gradient(135deg, {$primary}, {$secondary}); 
            border: none;
        }
        .icon-box { 
            width: 60px; height: 60px; 
            background: linear-gradient(135deg, {$primary}, {$secondary}); 
            border-radius: 1rem; 
            display: flex; align-items: center; justify-content: center;
        }
        .icon-box i { color: white; font-size: 1.5rem; }
        .section-title { color: {$primary}; }
    </style>
</head>
<body>
    {$navLinks}
    
    <section class="hero text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">{$title}</h1>
            <p class="lead mt-3">{$description}</p>
        </div>
    </section>

    {$pageContent}

    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>About Us</h5>
                    <p class="text-muted">Creating exceptional experiences through quality and innovation.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.html" class="text-muted">Home</a></li>
                        <li><a href="about.html" class="text-muted">About</a></li>
                        <li><a href="contact.html" class="text-muted">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Connect With Us</h5>
                    <a href="#" class="text-muted me-3"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-muted me-3"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-muted me-3"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-muted"><i class="fab fa-linkedin fa-lg"></i></a>
                </div>
            </div>
            <hr class="my-4">
            <p class="text-center text-muted mb-0">&copy; 2024 All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
    }

    /**
     * Get page-specific content based on page type
     */
    protected function getPageSpecificContent(string $pageName, string $userPrompt): string
    {
        $templates = [
            'contact' => <<<CONTENT
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <h2 class="section-title mb-4">Get In Touch</h2>
                    <p class="text-muted mb-4">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box me-3"><i class="fas fa-map-marker-alt"></i></div>
                        <div><h6 class="mb-0">Address</h6><p class="text-muted mb-0">123 Business Street, City, Country</p></div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box me-3"><i class="fas fa-phone"></i></div>
                        <div><h6 class="mb-0">Phone</h6><p class="text-muted mb-0">+1 (555) 123-4567</p></div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="icon-box me-3"><i class="fas fa-envelope"></i></div>
                        <div><h6 class="mb-0">Email</h6><p class="text-muted mb-0">info@example.com</p></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card p-4">
                        <form>
                            <div class="mb-3"><input type="text" class="form-control" placeholder="Your Name" required></div>
                            <div class="mb-3"><input type="email" class="form-control" placeholder="Your Email" required></div>
                            <div class="mb-3"><input type="text" class="form-control" placeholder="Subject"></div>
                            <div class="mb-3"><textarea class="form-control" rows="5" placeholder="Your Message" required></textarea></div>
                            <button type="submit" class="btn btn-primary w-100">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="py-5 bg-light">
        <div class="container">
            <div class="ratio ratio-16x9">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193595.15830869428!2d-74.119763973046!3d40.69766374874431!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2s!4v1635959481234!5m2!1sen!2s" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </section>
CONTENT,
            'about' => <<<CONTENT
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4">
                    <img src="https://picsum.photos/600/400" class="img-fluid rounded shadow" alt="About Us">
                </div>
                <div class="col-lg-6">
                    <h2 class="section-title mb-4">Our Story</h2>
                    <p class="text-muted">We are a passionate team dedicated to delivering exceptional results. With years of experience and a commitment to excellence, we've helped countless clients achieve their goals.</p>
                    <p class="text-muted">Our mission is to provide innovative solutions that make a real difference. We believe in building lasting relationships based on trust, transparency, and mutual success.</p>
                    <a href="contact.html" class="btn btn-primary mt-3">Get In Touch</a>
                </div>
            </div>
        </div>
    </section>
    <section class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="section-title mb-5">Our Values</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card p-4 h-100"><div class="icon-box mx-auto mb-3"><i class="fas fa-lightbulb"></i></div><h5>Innovation</h5><p class="text-muted">Constantly pushing boundaries to deliver creative solutions.</p></div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card p-4 h-100"><div class="icon-box mx-auto mb-3"><i class="fas fa-handshake"></i></div><h5>Integrity</h5><p class="text-muted">Building trust through honest and transparent practices.</p></div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card p-4 h-100"><div class="icon-box mx-auto mb-3"><i class="fas fa-star"></i></div><h5>Excellence</h5><p class="text-muted">Committed to delivering the highest quality in everything we do.</p></div>
                </div>
            </div>
        </div>
    </section>
CONTENT,
            'services' => <<<CONTENT
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card p-4 h-100"><div class="icon-box mb-3"><i class="fas fa-rocket"></i></div><h4>Service One</h4><p class="text-muted">Professional solutions tailored to your specific needs and goals.</p><a href="#" class="btn btn-outline-primary">Learn More</a></div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card p-4 h-100"><div class="icon-box mb-3"><i class="fas fa-cogs"></i></div><h4>Service Two</h4><p class="text-muted">Expert implementation with cutting-edge technology and methods.</p><a href="#" class="btn btn-outline-primary">Learn More</a></div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card p-4 h-100"><div class="icon-box mb-3"><i class="fas fa-chart-line"></i></div><h4>Service Three</h4><p class="text-muted">Data-driven strategies that deliver measurable results.</p><a href="#" class="btn btn-outline-primary">Learn More</a></div>
                </div>
            </div>
        </div>
    </section>
CONTENT,
            'pricing' => <<<CONTENT
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-4 mb-4">
                    <div class="card p-4 text-center h-100"><h4>Basic</h4><h2 class="section-title my-3">$29<small class="text-muted">/mo</small></h2><ul class="list-unstyled text-muted"><li class="mb-2">Feature One</li><li class="mb-2">Feature Two</li><li class="mb-2">Feature Three</li></ul><a href="#" class="btn btn-outline-primary w-100">Get Started</a></div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card p-4 text-center h-100 border-primary"><span class="badge bg-primary mb-3">Popular</span><h4>Professional</h4><h2 class="section-title my-3">$79<small class="text-muted">/mo</small></h2><ul class="list-unstyled text-muted"><li class="mb-2">Everything in Basic</li><li class="mb-2">Premium Feature</li><li class="mb-2">Priority Support</li></ul><a href="#" class="btn btn-primary w-100">Get Started</a></div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card p-4 text-center h-100"><h4>Enterprise</h4><h2 class="section-title my-3">$199<small class="text-muted">/mo</small></h2><ul class="list-unstyled text-muted"><li class="mb-2">Everything in Pro</li><li class="mb-2">Custom Solutions</li><li class="mb-2">Dedicated Manager</li></ul><a href="#" class="btn btn-outline-primary w-100">Contact Us</a></div>
                </div>
            </div>
        </div>
    </section>
CONTENT,
        ];
        
        // Default content for any page not in templates
        $default = <<<CONTENT
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="section-title mb-4">Welcome</h2>
                    <p class="text-muted mb-4">Explore our comprehensive offerings designed to meet your needs. We are committed to providing exceptional quality and service.</p>
                    <a href="contact.html" class="btn btn-primary">Contact Us</a>
                </div>
            </div>
        </div>
    </section>
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4"><div class="card p-4 h-100"><img src="https://picsum.photos/400/300" class="card-img-top rounded mb-3" alt="Image"><h5>Feature One</h5><p class="text-muted">High quality solutions tailored to your needs.</p></div></div>
                <div class="col-md-4 mb-4"><div class="card p-4 h-100"><img src="https://picsum.photos/400/301" class="card-img-top rounded mb-3" alt="Image"><h5>Feature Two</h5><p class="text-muted">Expert guidance and professional support.</p></div></div>
                <div class="col-md-4 mb-4"><div class="card p-4 h-100"><img src="https://picsum.photos/400/302" class="card-img-top rounded mb-3" alt="Image"><h5>Feature Three</h5><p class="text-muted">Innovative approaches for modern challenges.</p></div></div>
            </div>
        </div>
    </section>
CONTENT;
        
        return $templates[$pageName] ?? $default;
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
