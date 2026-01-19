<?php

namespace Common\AiGenerator;

use Common\AiGenerator\Actions\CreateAiProject;
use Common\Core\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiProjectGeneratorController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Generate a new project from AI prompt
     */
    public function generate(Request $request, CreateAiProject $createAiProject): JsonResponse
    {
        // Check if user has permission for AI features
        $this->authorize('text', 'AI');

        $data = $this->validate($request, [
            'prompt' => 'required|string|min:10|max:3000',
            'type' => 'nullable|string|in:landing-page,portfolio,business,blog,ecommerce',
            'name' => 'nullable|string|max:100',
            'provider' => 'nullable|string|in:gemini,openai,claude',
        ]);

        try {
            $result = $createAiProject->execute($data);

            return $this->success([
                'project' => $result['project'],
                'generated' => $result['generated'],
                'message' => 'Project generated successfully with ' . ucfirst($result['provider']) . '!',
            ]);
        } catch (\Exception $e) {
            return $this->error(
                'Failed to generate project: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    /**
     * Get available project types
     */
    public function projectTypes(): JsonResponse
    {
        $provider = AiProviderFactory::create(AiProviderFactory::getDefaultProvider());
        
        return $this->success([
            'types' => $provider->getProjectTypes(),
            'configured' => $provider->isConfigured(),
        ]);
    }

    /**
     * Get available AI providers
     */
    public function providers(): JsonResponse
    {
        return $this->success([
            'providers' => AiProviderFactory::getAvailableProviders(),
            'default' => AiProviderFactory::getDefaultProvider(),
        ]);
    }

    /**
     * Preview generation without creating project (for testing)
     */
    public function preview(Request $request): JsonResponse
    {
        $this->authorize('text', 'AI');

        $data = $this->validate($request, [
            'prompt' => 'required|string|min:10|max:3000',
            'type' => 'nullable|string|in:landing-page,portfolio,business,blog,ecommerce',
            'provider' => 'nullable|string|in:gemini,openai,claude',
        ]);

        try {
            $providerName = $data['provider'] ?? AiProviderFactory::getDefaultProvider();
            $provider = AiProviderFactory::create($providerName);

            $generated = $provider->generateProject(
                $data['prompt'],
                $data['type'] ?? 'landing-page'
            );

            return $this->success([
                'preview' => $generated,
                'pages_count' => count($generated['pages']),
                'provider' => $providerName,
            ]);
        } catch (\Exception $e) {
            return $this->error(
                'Failed to generate preview: ' . $e->getMessage(),
                [],
                500
            );
        }
    }
}
