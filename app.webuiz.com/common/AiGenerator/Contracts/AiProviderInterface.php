<?php

namespace Common\AiGenerator\Contracts;

interface AiProviderInterface
{
    /**
     * Generate a complete multi-page project
     */
    public function generateProject(string $prompt, string $projectType = 'landing-page'): array;

    /**
     * Check if the provider is configured with valid API key
     */
    public function isConfigured(): bool;

    /**
     * Get the provider name
     */
    public function getName(): string;

    /**
     * Get available project types
     */
    public function getProjectTypes(): array;
}
