import { Trans } from '@common/i18n/trans';
import { Button } from '@common/ui/buttons/button';
import { Footer } from '@common/ui/footer/footer';
import React, { useState } from 'react';
import { DashboardNavbar } from '@app/dashboard/dashboard-navbar';
import { StaticPageTitle } from '@common/seo/static-page-title';
import { useForm } from 'react-hook-form';
import { Form } from '@common/ui/forms/form';
import { FormTextField } from '@common/ui/forms/input-field/text-field/text-field';
import { useTrans } from '@common/i18n/use-trans';
import { message } from '@common/i18n/message';
import {
    GenerateProjectPayload,
    useGenerateProject,
    useAiProviders,
} from '@app/dashboard/ai-generator/use-generate-project';
import { useNavigate } from 'react-router-dom';
import { toast } from '@common/ui/toast/toast';
import { ProgressCircle } from '@common/ui/progress/progress-circle';
import { AiIcon } from '@app/editor/ai/ai-icon';
import { Select } from '@common/ui/forms/select/select';
import { Item } from '@common/ui/forms/listbox/item';
import { ButtonBase } from '@common/ui/buttons/button-base';
import { PromptSuggestionIcon } from '@app/editor/ai/prompt-suggestion-icon';
import { DownloadIcon } from '@common/icons/material/Download';
import { OpenInNewIcon } from '@common/icons/material/OpenInNew';
import { useAccountUsage } from '@app/editor/use-account-usage';
import { PolicyFailMessage } from '@common/billing/upgrade/policy-fail-message';

interface ProjectTypeOption {
    id: string;
    name: string;
    description: string;
    icon: string;
}

const projectTypes: ProjectTypeOption[] = [
    {
        id: 'landing-page',
        name: 'Landing Page',
        description: 'High-converting landing pages with CTAs',
        icon: 'fa-rocket',
    },
    {
        id: 'portfolio',
        name: 'Portfolio',
        description: 'Showcase your work elegantly',
        icon: 'fa-briefcase',
    },
    {
        id: 'business',
        name: 'Business Website',
        description: 'Professional corporate websites',
        icon: 'fa-building',
    },
    {
        id: 'blog',
        name: 'Blog',
        description: 'Clean, readable blog templates',
        icon: 'fa-newspaper',
    },
    {
        id: 'ecommerce',
        name: 'E-commerce Landing',
        description: 'Product showcase pages',
        icon: 'fa-shopping-cart',
    },
];

const suggestedPrompts = [
    'Create a modern portfolio for a photographer with dark theme, gallery grid, about section with experience timeline, and contact form. Use black and gold color scheme.',
    'Build a SaaS landing page for a project management tool with hero section, feature cards with icons, pricing table with 3 tiers, testimonials, and newsletter signup. Use blue and purple gradient.',
    'Design a restaurant website with hero image, menu sections, chef introduction, reservation form, and location map. Use warm orange and cream colors.',
    'Create a fitness trainer portfolio with workout programs, client testimonials, pricing packages, before/after gallery, and booking form. Use energetic red and dark theme.',
    'Build a tech startup landing page with animated hero, product features, team section, pricing plans, and demo request form. Use modern purple gradient.',
    'Design a wedding photography business website with portfolio gallery, packages, about the photographer, client reviews, and inquiry form. Use soft pink and white colors.',
];

export function AiGeneratorPage() {
    const { trans } = useTrans();
    const navigate = useNavigate();
    const [selectedType, setSelectedType] = useState('landing-page');
    const [selectedProvider, setSelectedProvider] = useState('gemini');
    const [generatedProject, setGeneratedProject] = useState<any>(null);

    const { data: providersData } = useAiProviders();
    const { data: usage } = useAccountUsage();

    const canCreateProject = usage?.projects.create.allowed !== false;

    const form = useForm<GenerateProjectPayload>({
        defaultValues: {
            prompt: '',
            type: 'landing-page',
            provider: 'gemini',
        },
    });
    const generateProject = useGenerateProject(form);

    const handleGenerate = (data: GenerateProjectPayload) => {
        generateProject.mutate(
            { ...data, type: selectedType, provider: selectedProvider },
            {
                onSuccess: response => {
                    setGeneratedProject(response.project);
                    toast(response.message);
                },
            },
        );
    };

    const handleOpenInEditor = () => {
        if (generatedProject?.model?.id) {
            navigate(`/editor/${generatedProject.model.id}`);
        }
    };

    const handleDownload = () => {
        if (generatedProject?.model?.id) {
            window.open(
                `/api/v1/projects/${generatedProject.model.id}/download`,
                '_blank',
            );
        }
    };

    const providers = providersData?.providers || [
        { id: 'gemini', name: 'Google Gemini', icon: '✨', configured: true, recommended: true },
        { id: 'openai', name: 'OpenAI GPT-4', icon: '🤖', configured: false, recommended: false },
        { id: 'claude', name: 'Anthropic Claude', icon: '🧠', configured: false, recommended: false },
    ];

    return (
        <div className="min-h-screen bg-alt">
            <StaticPageTitle>
                <Trans message="AI Project Generator" />
            </StaticPageTitle>
            <DashboardNavbar />
            <div className="container mx-auto mt-40 px-20">
                <div className="mb-40">
                    <h1 className="text-3xl font-medium">
                        <Trans message="Generate Website with AI" />
                    </h1>
                    <p className="mt-2 text-muted">
                        <Trans message="Describe your project and let AI create a complete, professional website for you." />
                    </p>
                </div>

                <div className="grid gap-40 lg:grid-cols-2">
                    {/* Left Column - Form */}
                    <div className="rounded-xl border bg-paper p-24 shadow-sm">
                        <Form form={form} onSubmit={handleGenerate}>
                            {/* AI Provider Selection */}
                            <div className="mb-24">
                                <label className="mb-8 block text-sm font-medium">
                                    <Trans message="AI Provider" />
                                </label>
                                <div className="flex flex-wrap gap-8">
                                    {providers.map(provider => (
                                        <button
                                            key={provider.id}
                                            type="button"
                                            onClick={() => setSelectedProvider(provider.id)}
                                            disabled={!provider.configured}
                                            className={`flex items-center gap-8 rounded-lg border px-12 py-8 transition-all ${selectedProvider === provider.id
                                                ? 'border-primary bg-primary/10 text-primary'
                                                : provider.configured
                                                    ? 'border-divider hover:border-primary/50'
                                                    : 'cursor-not-allowed border-divider opacity-50'
                                                }`}
                                        >
                                            <span className="text-lg">{provider.icon}</span>
                                            <span className="font-medium">{provider.name}</span>
                                            {provider.recommended && (
                                                <span className="rounded bg-primary/20 px-6 py-2 text-xs text-primary">
                                                    Recommended
                                                </span>
                                            )}
                                            {!provider.configured && (
                                                <span className="text-xs text-danger">Not configured</span>
                                            )}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* Project Type Selection */}
                            <div className="mb-24">
                                <label className="mb-8 block text-sm font-medium">
                                    <Trans message="Project Type" />
                                </label>
                                <Select
                                    selectionMode="single"
                                    selectedValue={selectedType}
                                    onItemSelected={value => setSelectedType(value as string)}
                                    className="w-full"
                                >
                                    {projectTypes.map(type => (
                                        <Item key={type.id} value={type.id}>
                                            <div className="flex items-center gap-8">
                                                <i className={`fas ${type.icon} text-primary`} />
                                                <span>{type.name}</span>
                                            </div>
                                        </Item>
                                    ))}
                                </Select>
                            </div>

                            {/* Prompt Input */}
                            <div className="mb-24">
                                <label className="mb-8 block text-sm font-medium">
                                    <Trans message="Describe Your Project" />
                                </label>
                                <div className="relative">
                                    <FormTextField
                                        name="prompt"
                                        inputElementType="textarea"
                                        rows={5}
                                        placeholder={trans(
                                            message(
                                                'Describe the website you want to create in detail... Include: business name, color scheme (e.g., blue and gold), specific sections needed, and the overall style.',
                                            ),
                                        )}
                                        className={
                                            generateProject.isPending
                                                ? 'opacity-50'
                                                : ''
                                        }
                                        minLength={50}
                                        maxLength={3000}
                                    />
                                    {generateProject.isPending && (
                                        <div className="absolute inset-0 flex items-center justify-center rounded-lg bg-black/5">
                                            <div className="flex flex-col items-center gap-12">
                                                <ProgressCircle isIndeterminate size="lg" />
                                                <span className="text-sm text-muted">
                                                    <Trans message="Generating your website..." />
                                                </span>
                                            </div>
                                        </div>
                                    )}
                                </div>
                                <div className="mt-8 rounded-lg bg-primary/5 p-12 text-xs text-muted">
                                    <div className="font-medium text-foreground mb-4">💡 Tips for better results:</div>
                                    <ul className="list-disc ml-16 space-y-4">
                                        <li>Include your <strong>business/project name</strong></li>
                                        <li>Mention <strong>color preferences</strong> (e.g., "blue and gold", "dark theme")</li>
                                        <li>List specific <strong>sections</strong> you want (hero, features, pricing, etc.)</li>
                                        <li>Describe the <strong>style</strong> (modern, minimalist, corporate, playful)</li>
                                    </ul>
                                </div>
                            </div>

                            {/* Optional Name */}
                            <div className="mb-24">
                                <FormTextField
                                    name="name"
                                    label={<Trans message="Project Name (optional)" />}
                                    placeholder={trans(message('My Awesome Website'))}
                                />
                            </div>

                            {/* Generate Button */}
                            <Button
                                type="submit"
                                variant="flat"
                                color="primary"
                                className="w-full"
                                size="lg"
                                disabled={generateProject.isPending || !canCreateProject}
                                endIcon={<AiIcon />}
                            >
                                {generateProject.isPending ? (
                                    <Trans message="Generating..." />
                                ) : !canCreateProject ? (
                                    <Trans message="Project Limit Reached" />
                                ) : (
                                    <Trans message="Generate Website" />
                                )}
                            </Button>

                            {!canCreateProject && (
                                <PolicyFailMessage
                                    className="mt-16 text-center"
                                    resourceName={<Trans message="sites" />}
                                />
                            )}
                        </Form>

                        {/* Suggested Prompts */}
                        <div className="mt-24 border-t pt-20">
                            <div className="mb-12 text-sm font-medium text-muted">
                                <Trans message="Try these prompts" />
                            </div>
                            <div className="flex flex-wrap gap-8">
                                {suggestedPrompts.map(prompt => (
                                    <ButtonBase
                                        key={prompt}
                                        onClick={() => {
                                            form.setValue('prompt', prompt);
                                        }}
                                        className="flex items-center gap-6 rounded-full border px-10 py-6 text-xs hover:bg-hover"
                                    >
                                        <PromptSuggestionIcon size="xs" className="text-muted" />
                                        <span className="max-w-200 truncate">{prompt}</span>
                                    </ButtonBase>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Right Column - Preview/Result */}
                    <div className="rounded-xl border bg-paper p-24 shadow-sm">
                        {generatedProject ? (
                            <div>
                                <div className="mb-20 flex items-center justify-between">
                                    <h2 className="text-xl font-medium">
                                        <Trans message="Your Website is Ready!" />
                                    </h2>
                                    <div className="flex gap-8">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            startIcon={<DownloadIcon />}
                                            onClick={handleDownload}
                                        >
                                            <Trans message="Download" />
                                        </Button>
                                        <Button
                                            variant="flat"
                                            color="primary"
                                            size="sm"
                                            startIcon={<OpenInNewIcon />}
                                            onClick={handleOpenInEditor}
                                        >
                                            <Trans message="Open in Editor" />
                                        </Button>
                                    </div>
                                </div>

                                {/* Project Info */}
                                <div className="mb-20 rounded-lg bg-alt p-16">
                                    <div className="grid grid-cols-2 gap-12 text-sm">
                                        <div>
                                            <span className="text-muted">Project Name:</span>
                                            <span className="ml-8 font-medium">
                                                {generatedProject.model?.name}
                                            </span>
                                        </div>
                                        <div>
                                            <span className="text-muted">Pages:</span>
                                            <span className="ml-8 font-medium">
                                                {generatedProject.pages?.length || 1}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {/* Preview Frame */}
                                <div className="overflow-hidden rounded-lg border">
                                    <div className="flex items-center gap-6 border-b bg-alt px-12 py-8">
                                        <div className="h-10 w-10 rounded-full bg-danger/50" />
                                        <div className="h-10 w-10 rounded-full bg-warning/50" />
                                        <div className="h-10 w-10 rounded-full bg-positive/50" />
                                        <span className="ml-8 text-xs text-muted">Preview</span>
                                    </div>
                                    <iframe
                                        srcDoc={generatedProject.pages?.[0]?.html || ''}
                                        className="h-400 w-full"
                                        title="Preview"
                                    />
                                </div>

                                {/* Action Buttons */}
                                <div className="mt-20 flex gap-12">
                                    <Button
                                        variant="outline"
                                        className="flex-1"
                                        onClick={() => setGeneratedProject(null)}
                                    >
                                        <Trans message="Generate Another" />
                                    </Button>
                                    <Button
                                        variant="flat"
                                        color="primary"
                                        className="flex-1"
                                        onClick={handleOpenInEditor}
                                    >
                                        <Trans message="Edit in Builder" />
                                    </Button>
                                </div>
                            </div>
                        ) : (
                            <div className="flex h-full min-h-400 flex-col items-center justify-center text-center">
                                <div className="mb-16 rounded-full bg-primary/10 p-20">
                                    <AiIcon className="h-48 w-48 text-primary" />
                                </div>
                                <h2 className="mb-8 text-xl font-medium">
                                    <Trans message="AI-Powered Generation" />
                                </h2>
                                <p className="max-w-320 text-muted">
                                    <Trans message="Enter a prompt describing your ideal website and our AI will generate a complete, professional template for you." />
                                </p>
                                <div className="mt-24 grid grid-cols-3 gap-16 text-center text-xs text-muted">
                                    <div>
                                        <div className="mb-4 text-2xl">🎨</div>
                                        <Trans message="Modern Design" />
                                    </div>
                                    <div>
                                        <div className="mb-4 text-2xl">📱</div>
                                        <Trans message="Responsive" />
                                    </div>
                                    <div>
                                        <div className="mb-4 text-2xl">⚡</div>
                                        <Trans message="Fast Generation" />
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>

                <Footer className="mt-60" />
            </div>
        </div>
    );
}

export default AiGeneratorPage;
