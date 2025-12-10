import {AnimatePresence, m} from 'framer-motion';
import {Fragment} from 'react';
import {opacityAnimation} from '@common/ui/animation/opacity-animation';
import {Skeleton} from '@common/ui/skeleton/skeleton';
import {useProducts} from '@common/billing/pricing-table/use-products';
import {Product} from '@common/billing/product';
import {
  findBestPrice,
  UpsellBillingCycle,
} from '@common/billing/pricing-table/find-best-price';
import {useAuth} from '@common/auth/use-auth';
import clsx from 'clsx';
import {Chip} from '@common/ui/forms/input-field/chip-field/chip';
import {Trans} from '@common/i18n/trans';
import {FormattedPrice} from '@common/i18n/formatted-price';
import {Button} from '@common/ui/buttons/button';
import {Link} from 'react-router-dom';
import {setInLocalStorage} from '@common/utils/hooks/local-storage';
import {ProductFeatureList} from '@common/billing/pricing-table/product-feature-list';

interface PricingTableProps {
  selectedCycle: UpsellBillingCycle;
  className?: string;
  productLoader?: string;
}
export function PricingTable({
  selectedCycle,
  className,
  productLoader,
}: PricingTableProps) {
  const query = useProducts(productLoader);
  return (
    <div
      className={clsx(
        'flex flex-col items-stretch gap-24 overflow-x-auto overflow-y-visible pb-20 md:flex-row md:justify-center',
        className,
      )}
    >
      <AnimatePresence initial={false} mode="wait">
        {query.data ? (
          <PlanList
            key="plan-list"
            plans={query.data.products}
            selectedPeriod={selectedCycle}
          />
        ) : (
          <SkeletonLoader key="skeleton-loader" />
        )}
      </AnimatePresence>
    </div>
  );
}

interface PlanListProps {
  plans: Product[];
  selectedPeriod: UpsellBillingCycle;
}
function PlanList({plans, selectedPeriod}: PlanListProps) {
  const {isLoggedIn, isSubscribed, user} = useAuth();
  const filteredPlans = plans.filter(plan => !plan.hidden);
  
  // Get user's current subscription product ID, product, price, and price_id
  const currentSubscription = user?.subscriptions?.find(sub => sub.valid);
  const currentProductId = currentSubscription?.product_id;
  const currentProduct = currentSubscription?.product;
  const currentPrice = currentSubscription?.price;
  const currentPriceId = currentSubscription?.price_id;
  
  // Determine current subscription's billing cycle (monthly or yearly)
  // Based on find-best-price.ts logic:
  // - Yearly: interval === 'year' && interval_count >= 1, OR interval === 'month' && interval_count >= 12
  // - Monthly: interval === 'month' && interval_count >= 1, OR interval === 'day' && interval_count >= 30
  const getBillingCycle = (price: typeof currentPrice): UpsellBillingCycle | null => {
    if (!price) return null;
    
    // Check for yearly first (more specific)
    if (
      (price.interval === 'year' && price.interval_count >= 1) ||
      (price.interval === 'month' && price.interval_count >= 12)
    ) {
      return 'yearly';
    }
    
    // Check for monthly
    if (
      (price.interval === 'month' && price.interval_count >= 1) ||
      (price.interval === 'day' && price.interval_count >= 30)
    ) {
      return 'monthly';
    }
    
    return null;
  };
  
  const currentBillingCycle = getBillingCycle(currentPrice);
  
  // Check if user has an active paid plan (not free/Trial)
  const hasActivePaidPlan = isSubscribed && currentProduct && !currentProduct.free;
  
  // Check if user is on Trial plan (logged in but no active paid subscription)
  const isOnTrialPlan = isLoggedIn && !hasActivePaidPlan;
  
  return (
    <Fragment>
      {filteredPlans.map((plan, index) => {
        const isFirst = index === 0;
        const isLast = index === filteredPlans.length - 1;
        const price = findBestPrice(selectedPeriod, plan.prices);
        
        // Check if this is the user's current plan in the selected billing cycle (for disabling button and showing text)
        // For paid plans: check if product ID matches AND billing cycle matches
        // For Trial plan: only show as current if user has no active paid plan
        const isCurrentPlan = plan.free 
          ? isOnTrialPlan 
          : currentProductId === plan.id && (
              currentBillingCycle === selectedPeriod || 
              (currentPriceId && price?.id === currentPriceId)
            );
        
        // Check if this is the same product but different billing cycle (for allowing upgrade/change)
        const isSameProductDifferentCycle = !plan.free && 
          currentProductId === plan.id && 
          currentBillingCycle !== null &&
          currentBillingCycle !== selectedPeriod;

        // Trial plan should always be disabled for all users
        const isTrialPlanDisabled = plan.free;

        let upgradeRoute;
        // Don't set upgrade route for Trial plan (always disabled) or current plan in matching billing cycle
        // Allow upgrade route for same product in different billing cycle (so user can switch)
        if (!isTrialPlanDisabled && !isCurrentPlan) {
          if (!isLoggedIn) {
            upgradeRoute = `/register?redirectFrom=pricing`;
          }
          if (isSubscribed) {
            upgradeRoute = `/change-plan/${plan.id}/${price?.id}/confirm`;
          }
          if (isLoggedIn && !plan.free) {
            upgradeRoute = `/checkout/${plan.id}/${price?.id}`;
          }
        }

        return (
          <m.div
            key={plan.id}
            {...opacityAnimation}
            className={clsx(
              'w-full rounded-panel border bg px-28 py-28 shadow-lg md:min-w-240 md:max-w-350',
              isFirst && 'ml-auto',
              isLast && 'mr-auto',
            )}
          >
            <div className="mb-32">
              <Chip
                radius="rounded"
                size="sm"
                className={clsx(
                  'mb-20 w-min',
                  !plan.recommended && 'invisible',
                )}
              >
                <Trans message="Most popular" />
              </Chip>
              <div className="mb-12 text-xl font-semibold">
                <Trans message={plan.name} />
              </div>
              <div className="text-sm text-muted">
                <Trans message={plan.description} />
              </div>
            </div>
            <div>
              {price ? (
                <FormattedPrice
                  priceClassName="font-bold text-4xl"
                  periodClassName="text-muted text-xs"
                  variant="separateLine"
                  price={price}
                />
              ) : (
                <div className="text-4xl font-bold">
                  <Trans message="Free" />
                </div>
              )}
              <div className="mt-60">
                <Button
                  variant={plan.recommended ? 'flat' : 'outline'}
                  color="primary"
                  className="w-full"
                  size="md"
                  elementType={upgradeRoute ? Link : undefined}
                  disabled={!upgradeRoute || isCurrentPlan || isTrialPlanDisabled}
                  onClick={() => {
                    if (isLoggedIn || !price || !plan) return;
                    setInLocalStorage('be.onboarding.selected', {
                      productId: plan.id,
                      priceId: price.id,
                    });
                  }}
                  to={upgradeRoute}
                >
                  {isTrialPlanDisabled ? (
                    // Trial plan: show "Current Plan" only if user has no active paid plan
                    isOnTrialPlan ? (
                      <Trans message="Current Plan" />
                    ) : (
                      <Trans message="Get started" />
                    )
                  ) : isCurrentPlan ? (
                    // Current plan in matching billing cycle: show "Current Plan"
                    <Trans message="Current Plan" />
                  ) : isSameProductDifferentCycle ? (
                    // Same product but different billing cycle: show "Upgrade" (enabled)
                    <Trans message="Upgrade" />
                  ) : plan.free ? (
                    <Trans message="Get started" />
                  ) : (
                    <Trans message="Upgrade" />
                  )}
                </Button>
              </div>
              <ProductFeatureList product={plan} />
            </div>
          </m.div>
        );
      })}
    </Fragment>
  );
}

function SkeletonLoader() {
  return (
    <Fragment>
      <PlanSkeleton key="skeleton-1" />
      <PlanSkeleton key="skeleton-2" />
      <PlanSkeleton key="skeleton-3" />
    </Fragment>
  );
}

function PlanSkeleton() {
  return (
    <m.div
      {...opacityAnimation}
      className="w-full rounded-lg border px-28 py-90 shadow-lg md:max-w-350"
    >
      <Skeleton className="my-10" />
      <Skeleton className="mb-40" />
      <Skeleton className="mb-40 h-30" />
      <Skeleton className="mb-40 h-40" />
      <Skeleton className="mb-20" />
      <Skeleton />
      <Skeleton />
    </m.div>
  );
}
