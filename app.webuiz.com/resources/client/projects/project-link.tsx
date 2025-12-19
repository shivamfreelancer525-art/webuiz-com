import {EditorProject, Project} from '@app/dashboard/project';
import {getBootstrapData} from '@common/core/bootstrap-data/use-backend-bootstrap-data';
import {Link, LinkProps} from 'react-router-dom';
import {ReactNode, useMemo} from 'react';
import clsx from 'clsx';
import {removeProtocol} from '@common/utils/urls/remove-protocol';

interface Props extends Omit<LinkProps, 'to'> {
  project: Project;
  className?: string;
  children?: ReactNode;
  color?: 'primary' | 'inherit';
}
export function ProjectLink({
  project,
  className,
  children,
  color = 'inherit',
  target,
  ...linkProps
}: Props) {
  const finalUri = useMemo(() => {
    return getProjectPreviewUrl(project);
  }, [project]);

  // Always use anchor tag for project preview URLs to allow opening in new tab
  // React Router Link doesn't support target="_blank" properly
  const shouldOpenInNewTab = target === '_blank';
  
  if (shouldOpenInNewTab) {
    return (
      <a
        href={finalUri}
        target="_blank"
        rel="noopener noreferrer"
        onClick={(e) => {
          e.stopPropagation();
          // Ensure it opens in new tab
          const url = finalUri;
          const absoluteUrl = url.startsWith('http://') || url.startsWith('https://') 
            ? url 
            : `${window.location.origin}${url.startsWith('/') ? url : '/' + url}`;
          console.log('ProjectLink clicked, opening:', absoluteUrl);
          const newWindow = window.open(absoluteUrl, '_blank', 'noopener,noreferrer');
          if (!newWindow) {
            console.warn('Popup blocked for ProjectLink');
          }
          e.preventDefault();
        }}
        className={clsx(
          color === 'primary'
            ? 'text-primary hover:text-primary-dark'
            : 'text-inherit',
          'overflow-x-hidden overflow-ellipsis outline-none transition-colors hover:underline focus-visible:underline',
          className,
        )}
        {...(linkProps as any)}
      >
        {children ?? getProjectPreviewUrl(project, {removeProtocol: true})}
      </a>
    );
  }

  return (
    <Link
      {...linkProps}
      className={clsx(
        color === 'primary'
          ? 'text-primary hover:text-primary-dark'
          : 'text-inherit',
        'overflow-x-hidden overflow-ellipsis outline-none transition-colors hover:underline focus-visible:underline',
        className,
      )}
      to={finalUri}
    >
      {children ?? getProjectPreviewUrl(project, {removeProtocol: true})}
    </Link>
  );
}

export function getProjectPreviewUrl(
  project: Project,
  options?: {removeProtocol?: boolean},
): string {
  let url = `${getBootstrapData().settings.base_url}/sites/${
    project.slug
  }`.replace(/\/$/, '');
  if (options?.removeProtocol) {
    url = removeProtocol(url);
  }
  return url;
}

export function getProjectSubdomainUrl(
  project: Project,
  options?: {removeProtocol?: boolean},
) {
  const base = getBootstrapData().settings.base_url;
  // strip pathname from url if generating subdomain
  const parsed = new URL(base);
  let url = `${parsed.protocol}//${project.slug}.${parsed.host}`.replace(
    /\/$/,
    '',
  );
  if (options?.removeProtocol) {
    url = removeProtocol(url);
  }
  return url;
}

export function getProjectImageUrl(project: Project) {
  return `${getProjectEditorUrl(project)}/thumbnail.png?ts=${
    project.updated_at
  }`;
}

export function getProjectEditorUrl(
  project: Project | EditorProject,
  options?: {relative?: boolean},
): string {
  const p = 'model' in project ? project.model : project;
  const uri = `projects/${p.user_id}/${p.uuid}/`;

  if (options?.relative) return uri;

  return `${getBootstrapData().settings.base_url}/storage/${uri}`;
}
