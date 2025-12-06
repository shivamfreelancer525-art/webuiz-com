import {Trans} from '@common/i18n/trans';
import {Footer} from '@common/ui/footer/footer';
import React, {ReactNode} from 'react';
import {DialogTrigger} from '@common/ui/overlays/dialog/dialog-trigger';
import {Button} from '@common/ui/buttons/button';
import {AddIcon} from '@common/icons/material/Add';
import {ConnectDomainDialog} from '@common/custom-domains/datatable/connect-domain-dialog/connect-domain-dialog';
import {useCustomDomains} from '@common/custom-domains/use-custom-domains';
import {useAuth} from '@common/auth/use-auth';
import {IllustratedMessage} from '@common/ui/images/illustrated-message';
import {SvgImage} from '@common/ui/images/svg-image/svg-image';
import worldImage from '@common/custom-domains/datatable/world.svg';
import {PageStatus} from '@common/http/page-status';
import {CustomDomain} from '@common/custom-domains/custom-domain';
import {removeProtocol} from '@common/utils/urls/remove-protocol';
import {ConfirmationDialog} from '@common/ui/overlays/dialog/confirmation-dialog';
import {useDeleteDomain} from '@common/custom-domains/datatable/requests/use-delete-domain';
import {Menu, MenuTrigger} from '@common/ui/navigation/menu/menu-trigger';
import {useProjects} from '@app/dashboard/use-projects';
import {Item} from '@common/ui/forms/listbox/item';
import {getProjectImageUrl} from '@app/projects/project-link';
import {KeyboardArrowDownIcon} from '@common/icons/material/KeyboardArrowDown';
import {useUpdateDomain} from '@common/custom-domains/datatable/requests/use-update-domain';
import {Project} from '@app/dashboard/project';
import {toast} from '@common/ui/toast/toast';
import {message} from '@common/i18n/message';
import {useTrans} from '@common/i18n/use-trans';
import {DashboardNavbar} from '@app/dashboard/dashboard-navbar';
import {DashboardWorkspaceSelector} from '@app/dashboard/dashboard-workspace-selector';
import {useActiveWorkspaceId} from '@common/workspace/active-workspace-id-context';
import {reloadAccountUsage, useAccountUsage} from '@app/editor/use-account-usage';
import {StaticPageTitle} from '@common/seo/static-page-title';
import {PolicyFailMessage} from '@common/billing/upgrade/policy-fail-message';
import {Link} from 'react-router-dom';
import {LinkStyle} from '@common/ui/buttons/external-link';

export function CustomDomainsPage() {
  return (
    <div className="flex h-screen flex-col">
      <StaticPageTitle>
        <Trans message="Branded domains" />
      </StaticPageTitle>
      <DashboardNavbar />
      <div className="container mx-auto mt-40 flex-auto px-20">
        <div>
          <div className="flex items-center gap-14">
            <h1 className="mr-auto text-3xl font-medium">
              <Trans message="Branded domains" />
            </h1>
            <DashboardWorkspaceSelector />
          </div>
          <p className="text-muted">
            <Trans message="Connect and manage your own custom domains." />
          </p>
        </div>
        <Content />
      </div>
      <Footer className="px-40" />
    </div>
  );
}

function Content() {
  const {user, getRestrictionValue} = useAuth();
  const {workspaceId} = useActiveWorkspaceId();
  const {data: usage} = useAccountUsage();
  const query = useCustomDomains<Project>({
    userId: user!.id,
    perPage: 40,
    with: 'resource.user',
    workspaceId,
  });
  const showNoResultsMessage = query.data?.pagination.data.length === 0;
  
  // Get website limit from user's plan (projects.create count restriction)
  const maxWebsites = getRestrictionValue('projects.create', 'count') as
    | number
    | null
    | undefined;
  
  // Get current domain count - prioritize query.data as it's the actual list
  // Fallback to usage API if query not loaded yet
  const currentDomains = query.data?.pagination.data.length ?? usage?.custom_domains.used ?? 0;
  
  // Check gate permission (from custom_domains.create policy)
  const gateAllowed = usage ? (usage.custom_domains.create.allowed !== false) : true;
  
  // Check if within website limit (custom domains should match website limit)
  // If maxWebsites is null/undefined, user has unlimited websites, so unlimited domains
  const withinWebsiteLimit = maxWebsites === null || maxWebsites === undefined || currentDomains < maxWebsites;
  
  // User can create domain if: gate allows AND within website limit
  const canCreateDomain = gateAllowed && withinWebsiteLimit;
  
  // Determine if limit is reached based on website limit
  // Only check if we have a valid maxWebsites value and current count
  const limitReached = 
    maxWebsites !== null && 
    maxWebsites !== undefined && 
    typeof maxWebsites === 'number' &&
    currentDomains >= maxWebsites;
  
  // Check if gate is blocking (from custom_domains.create policy)
  const gateBlocked = usage && usage.custom_domains.create.allowed === false;
  
  // Show message when:
  // 1. Limit is reached (currentDomains >= maxWebsites) - show if we have ANY data source
  // 2. OR gate is explicitly blocking
  // Always show if limit is reached, even if only one data source is available
  const hasData = query.data !== undefined || usage !== undefined;
  const shouldShowMessage = 
    (limitReached && hasData) || 
    (gateBlocked && usage);
  
  return (
    <div className="mt-20 flex-auto">
      <div className="mb-20">
        <DialogTrigger
          type="modal"
          onClose={newDomain => {
            if (newDomain) {
              reloadAccountUsage();
            }
          }}
        >
          <Button
            variant="outline"
            color="primary"
            startIcon={<AddIcon />}
            size="xs"
            disabled={!canCreateDomain}
          >
            <Trans message="Connect domain" />
          </Button>
          <ConnectDomainDialog />
        </DialogTrigger>
      </div>
      
      {shouldShowMessage && (
        <PolicyFailMessage
          className="mb-20"
          size="sm"
          resourceName={<Trans message="custom domains" />}
          reason={limitReached ? 'overQuota' : usage?.custom_domains.create.failReason}
          message={
            limitReached && maxWebsites !== null && maxWebsites !== undefined ? (
              <Trans
                message="You have reached the maximum number of custom domains allowed for your plan. Your plan allows :count website(s), so you can add up to :domainCount custom domain(s). <a>Please upgrade your plan to add more custom domains.</a>"
                values={{
                  count: maxWebsites,
                  domainCount: maxWebsites,
                  a: (text: ReactNode) => (
                    <Link className={LinkStyle} to="/pricing">
                      {text}
                    </Link>
                  ),
                }}
              />
            ) : undefined
          }
        />
      )}
      
      <div className="mt-34">
        {showNoResultsMessage && <NoDomainsMessage />}
        {query.data ? (
          <DomainList domains={query.data.pagination.data} />
        ) : (
          <PageStatus query={query} loaderIsScreen={false} />
        )}
      </div>
    </div>
  );
}

interface DomainListProps {
  domains: CustomDomain<Project>[];
}
function DomainList({domains}: DomainListProps) {
  return (
    <div>
      {domains.map(domain => (
        <div key={domain.id} className="rounded-panel border p-16">
          <div className="overflow-hidden overflow-ellipsis whitespace-nowrap font-bold">
            {removeProtocol(domain.host)}
          </div>
          <div className="mb-20 text-sm text-muted">
            {domain.resource ? (
              <div className="mt-4 flex items-center gap-8">
                <img
                  className="h-24 w-24 rounded object-cover"
                  src={getProjectImageUrl(domain.resource)}
                  alt=""
                />
                <div>{domain.resource?.name}</div>
              </div>
            ) : (
              <Trans message="This domain is not connected to any project yet." />
            )}
          </div>
          <div className="flex items-center gap-10">
            <ProjectSelectorButton domain={domain} />
            <RemoveDomainButton domain={domain} />
          </div>
        </div>
      ))}
    </div>
  );
}

interface ProjectSelectorButtonProps {
  domain: CustomDomain<Project>;
}
function ProjectSelectorButton({domain}: ProjectSelectorButtonProps) {
  const {trans} = useTrans();
  const {user} = useAuth();
  const {items} = useProjects({userId: user!.id, published: 'true'});
  const updateDomain = useUpdateDomain();

  const handleDomainChange = (projectId: number | null) => {
    const projectName = items.find(p => p.id === projectId)?.name;
    if (projectId) {
      toast.positive(
        trans(
          message('Connected “:domain” to  “:name”', {
            values: {
              domain: removeProtocol(domain.host),
              name: projectName,
            },
          }),
        ),
      );
    }
  };

  return (
    <MenuTrigger
      selectionMode="single"
      selectedValue={domain.resource?.id ?? ''}
      onItemSelected={projectId => {
        updateDomain.mutate(
          {
            domainId: domain.id,
            resource_id: projectId ? (projectId as number) : null,
            resource_type: projectId ? 'project' : null,
          },
          {
            onSuccess: () => handleDomainChange(projectId as number),
          },
        );
      }}
    >
      <Button
        variant="outline"
        size="xs"
        endIcon={<KeyboardArrowDownIcon />}
        disabled={updateDomain.isPending}
      >
        <Trans message="Change project" />
      </Button>
      <Menu>
        <Item value="">
          <Trans message="No project" />
        </Item>
        {items.map(project => (
          <Item
            value={project.id}
            key={project.id}
            startIcon={
              <img
                className="h-24 w-24 object-cover"
                src={getProjectImageUrl(project)}
                alt=""
              />
            }
          >
            {project.name}
          </Item>
        ))}
      </Menu>
    </MenuTrigger>
  );
}

interface RemoveDomainButtonProps {
  domain: CustomDomain;
}
function RemoveDomainButton({domain}: RemoveDomainButtonProps) {
  const deleteDomain = useDeleteDomain();
  return (
    <DialogTrigger type="modal">
      <Button variant="outline" size="xs" color="danger">
        <Trans message="Remove" />
      </Button>
      <ConfirmationDialog
        isDanger
        isLoading={deleteDomain.isPending}
        title={<Trans message="Remove domain?" />}
        body={<Trans message="Are you sure you want to remove this domain?" />}
        confirm={<Trans message="Remove" />}
        onConfirm={() => {
          deleteDomain.mutate(
            {domain},
            {onSuccess: () => reloadAccountUsage()},
          );
        }}
      />
    </DialogTrigger>
  );
}

function NoDomainsMessage() {
  return (
    <IllustratedMessage
      title={<Trans message="No domains" />}
      description={<Trans message="You have not connected any domains yet" />}
      image={<SvgImage src={worldImage} />}
    />
  );
}
