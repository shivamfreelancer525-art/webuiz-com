import { Project } from '@app/dashboard/project';
import {
  useInfiniteData,
  UseInfiniteDataProps,
} from '@common/ui/infinite-scroll/use-infinite-data';
import { useActiveWorkspaceId } from '@common/workspace/active-workspace-id-context';

interface Params {
  userId?: string | number;
  published?: string;
  paginate?: UseInfiniteDataProps<undefined>['paginate'];
  filters?: {
    is_ai_generated?: boolean;
  };
}

export function useProjects(params: Params) {
  const { workspaceId } = useActiveWorkspaceId();
  const { filters, ...restParams } = params;
  return useInfiniteData<Project>({
    queryKey: ['projects', { ...params, workspaceId }],
    endpoint: 'projects',
    paginate: params.paginate || 'simple',
    defaultOrderBy: 'updated_at',
    defaultOrderDir: 'desc',
    queryParams: {
      ...restParams,
      workspaceId,
      is_ai_generated: filters?.is_ai_generated !== undefined
        ? filters.is_ai_generated ? '1' : '0'
        : null,
    },
    willSortOrFilter: true,
  });
}
