import { useMutation, useQuery } from '@tanstack/react-query';
import { apiClient } from '@common/http/query-client';
import { BackendResponse } from '@common/http/backend-response/backend-response';
import { UseFormReturn } from 'react-hook-form';
import { onFormQueryError } from '@common/errors/on-form-query-error';
import { reloadAccountUsage } from '@app/editor/use-account-usage';

export interface ProjectType {
    id: string;
    name: string;
    description: string;
    icon: string;
}

export interface AiProvider {
    id: string;
    name: string;
    description: string;
    icon: string;
    configured: boolean;
    recommended: boolean;
}

interface ProjectTypesResponse extends BackendResponse {
    types: ProjectType[];
    configured: boolean;
}

interface ProvidersResponse extends BackendResponse {
    providers: AiProvider[];
    default: string;
}

interface GeneratedProject {
    model: {
        id: number;
        uuid: string;
        name: string;
        slug: string;
    };
    pages: Array<{
        name: string;
        html: string;
    }>;
}

interface GenerateProjectResponse extends BackendResponse {
    project: GeneratedProject;
    generated: {
        pages_count: number;
        has_custom_css: boolean;
        has_custom_js: boolean;
    };
    message: string;
}

export interface GenerateProjectPayload {
    prompt: string;
    type?: string;
    name?: string;
    provider?: string;
}

export function useGenerateProject(
    form: UseFormReturn<GenerateProjectPayload>,
) {
    return useMutation({
        mutationFn: (payload: GenerateProjectPayload) => generateProject(payload),
        onError: err => onFormQueryError(err, form),
        onSuccess: () => {
            reloadAccountUsage();
        },
    });
}

async function generateProject(payload: GenerateProjectPayload) {
    return apiClient
        .post<GenerateProjectResponse>('ai/generate-project', payload)
        .then(r => r.data);
}

export function useProjectTypes() {
    return useMutation({
        mutationFn: () => getProjectTypes(),
    });
}

async function getProjectTypes() {
    return apiClient
        .get<ProjectTypesResponse>('ai/project-types')
        .then(r => r.data);
}

export function useAiProviders() {
    return useQuery({
        queryKey: ['ai-providers'],
        queryFn: () => getAiProviders(),
        staleTime: 1000 * 60 * 5, // 5 minutes
    });
}

async function getAiProviders() {
    return apiClient
        .get<ProvidersResponse>('ai/providers')
        .then(r => r.data);
}
