<?php

namespace Common\AiGenerator;

use Common\AiGenerator\Contracts\AiProviderInterface;

class AiProviderFactory
{
    /**
     * Create an AI provider instance
     */
    public static function create(string $provider): AiProviderInterface
    {
        return match ($provider) {
            'openai' => app(OpenAiService::class),
            'claude' => app(ClaudeService::class),
            'gemini' => app(GeminiService::class),
            default => app(GeminiService::class),
        };
    }

    /**
     * Get all available providers with their status
     */
    public static function getAvailableProviders(): array
    {
        $providers = [
            [
                'id' => 'gemini',
                'name' => 'Google Gemini',
                'description' => 'Fast, cost-effective with 32K output tokens',
                'icon' => '✨',
                'configured' => app(GeminiService::class)->isConfigured(),
                'recommended' => true,
            ],
            [
                'id' => 'openai',
                'name' => 'OpenAI GPT-4',
                'description' => 'High quality, generates pages sequentially',
                'icon' => '🤖',
                'configured' => app(OpenAiService::class)->isConfigured(),
                'recommended' => false,
            ],
            [
                'id' => 'claude',
                'name' => 'Anthropic Claude',
                'description' => 'Excellent code quality, 8K output tokens',
                'icon' => '🧠',
                'configured' => app(ClaudeService::class)->isConfigured(),
                'recommended' => false,
            ],
        ];

        return $providers;
    }

    /**
     * Get the default provider (first configured one)
     */
    public static function getDefaultProvider(): string
    {
        if (app(GeminiService::class)->isConfigured()) {
            return 'gemini';
        }
        if (app(OpenAiService::class)->isConfigured()) {
            return 'openai';
        }
        if (app(ClaudeService::class)->isConfigured()) {
            return 'claude';
        }
        return 'gemini';
    }
}
