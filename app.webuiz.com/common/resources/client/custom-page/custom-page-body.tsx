import {CustomPage} from '@common/admin/custom-pages/custom-page';
import {useEffect, useRef} from 'react';
import {highlightAllCode} from '@common/text-editor/highlight/highlight-code';
import {useLocation} from 'react-router-dom';

interface CustomPageBodyProps {
  page: CustomPage;
}
export function CustomPageBody({page}: CustomPageBodyProps) {
  const bodyRef = useRef<HTMLDivElement>(null);
  const location = useLocation();
  const isPolicyPage = location.pathname === '/privacy-policy' || location.pathname === '/terms-and-conditions';
  
  useEffect(() => {
    if (bodyRef.current) {
      highlightAllCode(bodyRef.current);
    }
  }, []);

  // For privacy policy and terms & conditions, match webuiz format
  if (isPolicyPage) {
    return (
      <>
        {/* Hero section matching webuiz */}
        <section 
          className="bg-primary-light/10 pt-[130px] pb-24 md:pt-[150px] md:pb-24" 
          style={{
            backgroundImage: 'url(/assets/images/hero/hero-three-bg.png)',
            backgroundSize: 'cover',
            backgroundPosition: 'center'
          }}
        >
          <div className="container mx-auto px-14 md:px-40">
            <div className="text-center pt-16 md:pt-16 pb-16 md:pb-16 flex items-center justify-center">
              <h1 
                className="text-4xl md:text-5xl font-bold mb-0 text-dark text-center" 
                data-aos="fade-up" 
                data-aos-duration="1500" 
                data-aos-offset="50"
                style={{
                  textAlign: 'center',
                  width: '100%'
                }}
              >
                {page.title}
              </h1>
            </div>
          </div>
        </section>
        {/* Content section matching webuiz - starts immediately after hero */}
        <section className="pt-0 pb-24 md:pt-0 md:pb-16">
          <div className="container mx-auto px-14 md:px-40">
            <div
              ref={bodyRef}
              className="custom-page-body"
              dangerouslySetInnerHTML={{__html: page.body}}
            />
          </div>
        </section>
      </>
    );
  }

  // Default layout for other pages
  return (
    <div className="px-16 md:px-24">
      <div className="custom-page-body prose mx-auto my-50 dark:prose-invert">
        <h1>{page.title}</h1>
        <div
          ref={bodyRef}
          className="whitespace-pre-wrap break-words"
          dangerouslySetInnerHTML={{__html: page.body}}
        />
      </div>
    </div>
  );
}
