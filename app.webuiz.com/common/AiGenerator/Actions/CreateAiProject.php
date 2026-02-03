<?php

namespace Common\AiGenerator\Actions;

use App\Models\Project;
use App\Services\ProjectRepository;
use Common\AiGenerator\AiProviderFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateAiProject
{
    public function __construct(
        protected ProjectRepository $projectRepository
    ) {}

    /**
     * Execute the action to create an AI-generated project
     */
    public function execute(array $data): array
    {
        $user = Auth::user();
        
        // Check monthly AI project limit
        $aiUsage = $user->getAiProjectUsage();
        if ($aiUsage['total'] && $aiUsage['used'] >= $aiUsage['total']) {
            throw new \RuntimeException('You have reached your monthly AI project limit. Please upgrade your plan or wait until next month.');
        }

        $prompt = $data['prompt'];
        $projectType = $data['type'] ?? 'landing-page';
        $projectName = $data['name'] ?? $this->generateProjectName($prompt);
        $providerName = $data['provider'] ?? AiProviderFactory::getDefaultProvider();

        // Get the AI provider
        $provider = AiProviderFactory::create($providerName);

        // Generate project content using selected provider
        try {
            $generatedContent = $provider->generateProject($prompt, $projectType);
        } catch (\Exception $e) {
            // Provide a friendlier error message for common API key issues
            if (Str::contains($e->getMessage(), ['Incorrect API key', 'invalid_api_key', '401'])) {
                $msg = 'AI Configuration Error: The API key for ' . ucfirst($providerName) . ' is invalid or expired.';
                // If user is admin (implied by dashboard access usually, or just provide context), hint at .env
                if (config('app.debug')) {
                    $msg .= ' Please check your .env file.';
                }
                throw new \RuntimeException($msg);
            }
            throw $e;
        }

        // Create the project using existing ProjectRepository
        $project = $this->projectRepository->create([
            'name' => $projectName,
            'slug' => slugify($projectName),
            'userId' => Auth::id(),
            'pages' => $generatedContent['pages'],
            'published' => false,
        ]);

        // Mark as AI-generated
        $project->is_ai_generated = true;
        $project->save();

        // Add custom CSS if generated
        if (!empty($generatedContent['css'])) {
            $this->projectRepository->update($project, [
                'css' => $generatedContent['css'],
            ]);
        }

        // Add custom JS if generated
        if (!empty($generatedContent['js'])) {
            $this->projectRepository->update($project, [
                'js' => $generatedContent['js'],
            ]);
        }

        // Generate thumbnail from first page HTML
        $this->generateThumbnail($project, $generatedContent['pages']);

        // Load the complete project data
        $loadedProject = $this->projectRepository->load($project);

        return [
            'project' => $loadedProject,
            'generated' => [
                'pages_count' => count($generatedContent['pages']),
                'has_custom_css' => !empty($generatedContent['css']),
                'has_custom_js' => !empty($generatedContent['js']),
            ],
            'provider' => $providerName,
        ];
    }

    /**
     * Generate a project name from the prompt
     */
    protected function generateProjectName(string $prompt): string
    {
        // Extract first few meaningful words from prompt
        $words = preg_split('/\s+/', $prompt);
        $nameWords = array_slice($words, 0, 4);
        $name = implode(' ', $nameWords);
        
        // Clean up and limit length
        $name = Str::title($name);
        $name = Str::limit($name, 50, '');
        
        // Add timestamp if name is too generic
        if (strlen($name) < 10) {
            $name .= ' ' . date('M d');
        }

        return $name;
    }

    /**
     * Generate a thumbnail for the AI project
     */
    protected function generateThumbnail(Project $project, array $pages): void
    {
        if (empty($pages)) {
            return;
        }

        $projectPath = $this->projectRepository->getProjectPath($project);
        $firstPageHtml = $pages[0]['html'] ?? '';

        // Try to extract an image URL from the HTML
        $imageUrl = $this->extractFirstImageUrl($firstPageHtml);

        if ($imageUrl) {
            try {
                // Download the image and save as thumbnail
                $response = Http::timeout(10)->get($imageUrl);
                if ($response->successful()) {
                    $imageContent = $response->body();
                    
                    // Check if it's a valid image
                    if (strlen($imageContent) > 1000) {
                        Storage::disk('projects')->put(
                            "$projectPath/thumbnail.png",
                            $imageContent
                        );
                        return;
                    }
                }
            } catch (\Exception $e) {
                // Fall through to default
            }
        }

        // Generate a simple gradient thumbnail as fallback
        $this->generateGradientThumbnail($projectPath);
    }

    /**
     * Extract the first image URL from HTML content
     */
    protected function extractFirstImageUrl(string $html): ?string
    {
        // Match img tags with src attribute
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $html, $matches)) {
            $url = $matches[1];
            
            // Only use absolute URLs (external images)
            if (Str::startsWith($url, ['http://', 'https://'])) {
                // Prefer larger images (picsum.photos)
                if (Str::contains($url, 'picsum.photos')) {
                    // Modify to get a consistent size
                    return preg_replace('/\d+\/\d+/', '730/456', $url);
                }
                return $url;
            }
        }

        // Try to find background images in style attributes
        if (preg_match('/background[^:]*:\s*url\(["\']?([^"\')\s]+)["\']?\)/', $html, $matches)) {
            $url = $matches[1];
            if (Str::startsWith($url, ['http://', 'https://'])) {
                return $url;
            }
        }

        return null;
    }

    /**
     * Generate a simple gradient thumbnail as fallback
     */
    protected function generateGradientThumbnail(string $projectPath): void
    {
        // Create a simple gradient PNG using GD library if available
        if (!function_exists('imagecreatetruecolor')) {
            return; // GD not available, keep default thumbnail
        }

        $width = 730;
        $height = 456;
        
        $image = imagecreatetruecolor($width, $height);
        
        // Create gradient from purple to blue
        for ($y = 0; $y < $height; $y++) {
            $ratio = $y / $height;
            $r = (int)(102 + (59 - 102) * $ratio);
            $g = (int)(126 + (130 - 126) * $ratio);
            $b = (int)(234 + (246 - 234) * $ratio);
            
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $y, $width, $y, $color);
        }

        // Add "AI Generated" text
        $white = imagecolorallocate($image, 255, 255, 255);
        $text = "AI Generated Website";
        $fontSize = 5;
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $textX = ($width - $textWidth) / 2;
        $textY = $height / 2 - 10;
        imagestring($image, $fontSize, (int)$textX, (int)$textY, $text, $white);

        // Add sparkle icon (simple asterisk)
        imagestring($image, $fontSize, (int)$textX - 25, (int)$textY, "*", $white);
        imagestring($image, $fontSize, (int)$textX + $textWidth + 10, (int)$textY, "*", $white);

        // Save the image
        ob_start();
        imagepng($image);
        $pngContent = ob_get_clean();
        imagedestroy($image);

        Storage::disk('projects')->put("$projectPath/thumbnail.png", $pngContent);
    }
}

