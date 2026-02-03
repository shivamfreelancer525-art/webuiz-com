import { Trans } from '@common/i18n/trans';
import { Button } from '@common/ui/buttons/button';
import { Footer } from '@common/ui/footer/footer';
import React, { useState } from 'react';
import { DashboardNavbar } from '@app/dashboard/dashboard-navbar';
import { StaticPageTitle } from '@common/seo/static-page-title';
import { useAccountUsage } from '@app/editor/use-account-usage';
import { Link } from 'react-router-dom';
import { useProjects } from '@app/dashboard/use-projects';
import { useAuth } from '@common/auth/use-auth';
import { IllustratedMessage } from '@common/ui/images/illustrated-message';
import { SvgImage } from '@common/ui/images/svg-image/svg-image';
import builderImage from '@app/templates/website-builder.svg';
import { ProgressCircle } from '@common/ui/progress/progress-circle';
import { Project } from '@app/dashboard/project';
import { AnimatePresence, m } from 'framer-motion';
import { opacityAnimation } from '@common/ui/animation/opacity-animation';
import { AiIcon } from '@app/editor/ai/ai-icon';
import { PolicyFailMessage } from '@common/billing/upgrade/policy-fail-message';
import { getProjectImageUrl, getProjectPreviewUrl, ProjectLink } from '@app/projects/project-link'; // Added getProjectPreviewUrl
import { FormattedRelativeTime } from '@common/i18n/formatted-relative-time';
import { IconButton } from '@common/ui/buttons/icon-button';
import { MoreVertIcon } from '@common/icons/material/MoreVert';
import { Menu, MenuTrigger } from '@common/ui/navigation/menu/menu-trigger';
import { Item } from '@common/ui/forms/listbox/item';
import { DialogTrigger } from '@common/ui/overlays/dialog/dialog-trigger';
import { ConfirmationDialog } from '@common/ui/overlays/dialog/confirmation-dialog';
import { useDeleteProject } from '@app/projects/use-delete-project';
import { openDialog } from '@common/ui/overlays/store/dialog-store';
import { ProjectSettingsDialog } from '@app/projects/project-settings-dialog/project-settings-dialog';
import { InfiniteScrollSentinel } from '@common/ui/infinite-scroll/infinite-scroll-sentinel';

export function AiProjectsPage() {
    const { user } = useAuth();
    const { data: usage } = useAccountUsage();

    // Fetch only AI-generated projects
    const query = useProjects({
        userId: user!.id,
        paginate: 'lengthAware',
        filters: { is_ai_generated: true }
    });

    // Type-safe access to ai_projects usage
    const aiUsage = (usage as any)?.ai_projects;
    const canCreateAiProject = aiUsage?.create?.allowed !== false;
    const aiProjectsUsed = aiUsage?.used ?? 0;
    const aiProjectsTotal = aiUsage?.total ?? 0;

    return (
        <div className="min-h-screen bg-alt">
            <StaticPageTitle>
                <Trans message="AI Projects" />
            </StaticPageTitle>
            <div className="flex h-screen flex-col overflow-y-scroll">
                <DashboardNavbar />

                <div className="container mx-auto flex-auto px-12">
                    <div className="mb-24 flex flex-col gap-16 py-36 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h1 className="text-2xl font-medium">
                                <Trans message="AI Generated Projects" />
                            </h1>
                            <p className="mt-4 text-muted">
                                <Trans message="Create stunning websites with AI in seconds" />
                            </p>
                        </div>

                        <div className="flex flex-col items-end gap-8">
                            {/* Monthly Usage Display */}
                            <div className="text-sm text-muted">
                                <span className="font-medium text-foreground">{aiProjectsUsed}</span>
                                <span> / </span>
                                <span>{aiProjectsTotal}</span>
                                <span> </span>
                                <Trans message="AI projects this month" />
                            </div>

                            {/* AI Generator Button */}
                            <Button
                                variant="flat"
                                color="primary"
                                elementType={Link}
                                to="/dashboard/ai-generator"
                                startIcon={<AiIcon />}
                                disabled={!canCreateAiProject}
                            >
                                <Trans message="Generate New AI Website" />
                            </Button>
                        </div>
                    </div>

                    {/* Limit Warning */}
                    {!canCreateAiProject && (
                        <PolicyFailMessage
                            className="mb-24 text-center"
                            resourceName={<Trans message="AI projects" />}
                        />
                    )}

                    {/* Projects Grid */}
                    <AnimatePresence initial={false} mode="wait">
                        {query.isLoading ? (
                            <div className="flex items-center justify-center py-80">
                                <ProgressCircle isIndeterminate />
                            </div>
                        ) : query.items.length === 0 ? (
                            <m.div {...opacityAnimation}>
                                <IllustratedMessage
                                    className="mt-60"
                                    image={<SvgImage src={builderImage} />}
                                    title={<Trans message="No AI projects yet" />}
                                    description={
                                        <Trans message="Generate your first AI-powered website in seconds using our intelligent AI generator." />
                                    }
                                    action={
                                        canCreateAiProject && (
                                            <Button
                                                variant="flat"
                                                color="primary"
                                                elementType={Link}
                                                to="/dashboard/ai-generator"
                                                startIcon={<AiIcon />}
                                            >
                                                <Trans message="Create AI Website" />
                                            </Button>
                                        )
                                    }
                                />
                            </m.div>
                        ) : (
                            <m.div {...opacityAnimation}>
                                <div className="grid grid-cols-1 gap-24 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                    {query.items.map((project: Project) => (
                                        <AiProjectGridItem
                                            key={project.id}
                                            project={project}
                                        />
                                    ))}
                                </div>
                                <InfiniteScrollSentinel query={query} />
                            </m.div>
                        )}
                    </AnimatePresence>
                </div>

                <Footer className="mt-60 px-40" />
            </div>
        </div>
    );
}

interface AiProjectGridItemProps {
    project: Project;
}

function AiProjectGridItem({ project }: AiProjectGridItemProps) {
    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
    const deleteProject = useDeleteProject();

    return (
        <m.div
            layout
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            exit={{ opacity: 0, scale: 0.9 }}
            className="group relative overflow-hidden rounded-panel border bg-paper shadow-sm transition-shadow hover:shadow-lg"
        >
            <ProjectLink project={project}>
                <div className="relative aspect-video">
                    <img
                        className="h-full w-full object-cover"
                        src={getProjectImageUrl(project)}
                        alt={project.name}
                    />
                    <div className="absolute inset-0 flex items-center justify-center bg-black/0 opacity-0 transition-all group-hover:bg-black/40 group-hover:opacity-100">
                        <span className="rounded-full bg-white px-16 py-8 text-sm font-medium text-black">
                            <Trans message="Edit" />
                        </span>
                    </div>
                    {/* AI Badge */}
                    <div className="absolute left-8 top-8 flex items-center gap-4 rounded-full bg-primary/90 px-8 py-4 text-xs font-medium text-on-primary">
                        <AiIcon size="xs" />
                        <Trans message="AI" />
                    </div>
                </div>
            </ProjectLink>

            <div className="flex items-center gap-6 p-12">
                <div className="flex-auto min-w-0">
                    <button
                        type="button"
                        onClick={(e) => {
                            e.stopPropagation();
                            e.preventDefault();
                            const url = getProjectPreviewUrl(project);
                            const absoluteUrl = url.startsWith('http://') || url.startsWith('https://')
                                ? url
                                : `${window.location.origin}${url.startsWith('/') ? url : '/' + url}`;
                            window.open(absoluteUrl, '_blank', 'noopener,noreferrer');
                        }}
                        className="font-bold hover:text-primary transition-colors cursor-pointer text-left bg-transparent border-0 p-0 m-0 w-auto h-auto block truncate max-w-full"
                        title={`Open ${project.name} in new tab`}
                    >
                        {project.name}
                    </button>
                    <div className="text-sm text-muted truncate">
                        <ProjectLink project={project} target="_blank" />
                    </div>
                    <div className="mt-4 text-xs text-muted">
                        <FormattedRelativeTime date={project.updated_at} />
                    </div>
                </div>

                <MenuTrigger>
                    <IconButton className="text-muted" size="sm">
                        <MoreVertIcon />
                    </IconButton>
                    <Menu>
                        <Item value="edit" elementType={Link} to={`/editor/${project.id}`}>
                            <Trans message="Edit" />
                        </Item>
                        <Item
                            value="preview"
                            elementType={Link}
                            target="_blank"
                            to={getProjectPreviewUrl(project)}
                        >
                            <Trans message="Preview" />
                        </Item>
                        <Item
                            value="settings"
                            onSelected={() => openDialog(ProjectSettingsDialog, { project })}
                        >
                            <Trans message="Settings" />
                        </Item>
                        <Item value="delete" onSelected={() => setDeleteDialogIsOpen(true)}>
                            <Trans message="Delete" />
                        </Item>
                    </Menu>
                </MenuTrigger>
            </div>

            {/* Delete Confirmation Dialog */}
            <DialogTrigger type="modal" isOpen={deleteDialogIsOpen} onClose={() => setDeleteDialogIsOpen(false)}>
                <ConfirmationDialog
                    isDanger
                    title={<Trans message="Delete project" />}
                    body={<Trans message="Are you sure you want to delete this project?" />}
                    confirm={<Trans message="Delete" />}
                    isLoading={deleteProject.isPending}
                    onConfirm={() => {
                        deleteProject.mutate(
                            { projectId: project.id },
                            { onSuccess: () => setDeleteDialogIsOpen(false) }
                        );
                    }}
                />
            </DialogTrigger>
        </m.div>
    );
}

export default AiProjectsPage;
