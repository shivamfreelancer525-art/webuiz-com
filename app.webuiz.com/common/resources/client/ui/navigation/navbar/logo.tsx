import {useTrans} from '@common/i18n/use-trans';
import {useSettings} from '@common/core/settings/use-settings';
import {Link} from 'react-router-dom';
import {NavbarProps} from '@common/ui/navigation/navbar/navbar';
import clsx from 'clsx';
import {getAssetUrl} from '@common/utils/urls/get-asset-url';

interface LogoProps {
  color?: NavbarProps['color'];
  logoColor?: NavbarProps['logoColor'];
  isDarkMode?: boolean;
  className?: string;
}
export function Logo({color, logoColor, isDarkMode, className}: LogoProps) {
  const {trans} = useTrans();
  const {branding} = useSettings();

  let desktopLogo: string;
  let mobileLogo: string;
  if (
    isDarkMode ||
    !branding.logo_dark ||
    (logoColor !== 'dark' && color !== 'bg' && color !== 'bg-alt')
  ) {
    desktopLogo = branding.logo_light;
    mobileLogo = branding.logo_light_mobile;
  } else {
    desktopLogo = branding.logo_dark;
    mobileLogo = branding.logo_dark_mobile;
  }

  if (!mobileLogo && !desktopLogo) {
    return null;
  }

  const desktopLogoUrl = desktopLogo ? getAssetUrl(desktopLogo) : '';
  const mobileLogoUrl = mobileLogo ? getAssetUrl(mobileLogo) : desktopLogoUrl;

  return (
    <Link
      to="/"
      className={clsx(
        'mr-4 block h-full flex-shrink-0 md:mr-24',
        className,
      )}
      style={{maxHeight: '41px'}}
      aria-label={trans({message: 'Go to homepage'})}
    >
      <picture>
        <source srcSet={mobileLogoUrl} media="(max-width: 768px)" />
        <source srcSet={desktopLogoUrl} media="(min-width: 768px)" />
        <img
          src={desktopLogoUrl}
          className="block h-full w-auto max-h-[58px] md:max-h-[60px]"
          alt={trans({message: 'Site logo'})}
        />
      </picture>
    </Link>
  );
}
