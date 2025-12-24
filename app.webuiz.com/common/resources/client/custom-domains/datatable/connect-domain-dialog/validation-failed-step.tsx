import {useSettings} from '@common/core/settings/use-settings';
import {Trans} from '@common/i18n/trans';
import {Fragment, ReactNode} from 'react';
import {ConnectDomainStepProps} from '@common/custom-domains/datatable/connect-domain-dialog/connect-domain-step';
import {useAuth} from '@common/auth/use-auth';
import {isSubdomain} from '@common/custom-domains/datatable/connect-domain-dialog/is-subdomain';
import {InfoIcon} from '@common/icons/material/Info';
import {useValidateDomainDns} from '@common/custom-domains/datatable/requests/use-validate-domain-dns';
import {DomainProgressIndicator} from '@common/custom-domains/datatable/connect-domain-dialog/domain-progress-indicator';

export function ValidationFailedStep({
  stepper: {
    goToNextStep,
    state: {host, serverIp, isLoading, validationFailReason},
  },
}: ConnectDomainStepProps) {
  const validateDns = useValidateDomainDns();
  const {base_url} = useSettings();
  const {hasPermission} = useAuth();
  const subdomain = isSubdomain(host);
  const record = subdomain ? 'CNAME' : 'A';
  const location = subdomain ? base_url : serverIp;

  if (isLoading) {
    return <DomainProgressIndicator />;
  }

  return (
    <Fragment>
      <div className="flex items-center gap-12 text-base p-12 rounded bg-info/15 text-info font-medium">
        <InfoIcon size="lg" />
        <div>
          <Trans
            message="Please wait up to 60 minutes. This will take a while."
          />
        </div>
      </div>
      <div className="text-sm text-muted mt-10">
        <Trans
          message="Your domain has been added to the validation queue. We'll check it automatically every 10 minutes. You'll be notified once it's validated."
        />
      </div>
    </Fragment>
  );
}

interface ErrorMessageProps {
  children: ReactNode;
}
function ErrorMessage({children}: ErrorMessageProps) {
  return (
    <div className="flex items-center gap-12 text-base p-12 rounded bg-warning/15 text-warning font-medium">
      <WarningIcon size="lg" />
      {children}
    </div>
  );
}
