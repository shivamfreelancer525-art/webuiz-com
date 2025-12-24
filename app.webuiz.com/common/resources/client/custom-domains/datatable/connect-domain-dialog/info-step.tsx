import {useSettings} from '@common/core/settings/use-settings';
import {Trans} from '@common/i18n/trans';
import {ReactNode} from 'react';
import {ConnectDomainStepProps} from '@common/custom-domains/datatable/connect-domain-dialog/connect-domain-step';
import {isSubdomain} from '@common/custom-domains/datatable/connect-domain-dialog/is-subdomain';
import {DomainProgressIndicator} from '@common/custom-domains/datatable/connect-domain-dialog/domain-progress-indicator';

export function InfoStep({
  stepper: {
    state: {isLoading, host, serverIp},
  },
}: ConnectDomainStepProps) {
  const {base_url} = useSettings();

  if (isLoading) {
    return <DomainProgressIndicator />;
  }

  if (isSubdomain(host)) {
    // Extract subdomain from host (e.g., "www.atwozcraft.com" -> "www")
    const subdomainName = host.split('.')[0];
    return (
      <Message
        title={
          <Trans message="Add this CNAME record to your domain by visiting your DNS provider or registrar." />
        }
        record="CNAME"
        target={base_url}
        hostName={subdomainName}
      />
    );
  }

  return (
    <Message
      title={
        <Trans message="Add this A record to your domain by visiting your DNS provider or registrar." />
      }
      record="A"
      target={serverIp}
      hostName="@"
    />
  );
}

interface MessageProps {
  title: ReactNode;
  record: string;
  target: string;
  hostName?: string;
}

function Message({title, record, target, hostName = '@'}: MessageProps) {
  return (
    <div>
      <div className="text-muted mb-10">{title}</div>
      <div className="text-sm mb-8 text-muted">
        <Trans message="Add the following DNS record to connect this domain to your website:" />
      </div>
      <div className="border border-divider rounded-lg overflow-hidden">
        <table className="w-full text-sm">
          <thead className="bg-primary/5">
            <tr>
              <th className="px-12 py-8 text-left font-semibold">
                <Trans message="Type" />
              </th>
              <th className="px-12 py-8 text-left font-semibold">
                <Trans message="Host name" />
              </th>
              <th className="px-12 py-8 text-left font-semibold">
                <Trans message="Value" />
              </th>
            </tr>
          </thead>
          <tbody>
            <tr className="border-t border-divider">
              <td className="px-12 py-10 font-medium">{record}</td>
              <td className="px-12 py-10 text-muted">{hostName}</td>
              <td className="px-12 py-10 font-mono text-primary font-semibold">
                {target}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  );
}
