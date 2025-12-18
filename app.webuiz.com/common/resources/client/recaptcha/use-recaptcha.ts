import lazyLoader from '../utils/http/lazy-loader';
import {useCallback, useEffect, useState} from 'react';
import {apiClient} from '../http/query-client';
import {RecaptchaAction} from '../core/settings/settings';
import {toast} from '../ui/toast/toast';
import {message} from '../i18n/message';
import {useSettings} from '../core/settings/use-settings';

export function useRecaptcha(action: RecaptchaAction) {
  const {recaptcha: {site_key, enable} = {}} = useSettings();
  const enabled = site_key && enable?.[action];

  const [isVerifying, setIsVerifying] = useState(false);

  useEffect(() => {
    if (enabled) {
      load(site_key);
    }
  }, [enabled, site_key]);

  const verify = useCallback(async () => {
    if (!enabled) return true;
    setIsVerifying(true);
    const isValid = await execute(site_key, action);
    if (!isValid) {
      toast.danger(message('Could not verify you are human.'));
    }
    setIsVerifying(false);
    return isValid;
  }, [enabled, site_key, action]);

  return {verify, isVerifying};
}

async function execute(siteKey: string, action: string): Promise<boolean> {
  try {
    console.log('[reCAPTCHA] Loading script for site key:', siteKey);
    await load(siteKey);

    // Wait for grecaptcha to be available with timeout
    const timeout = 10000; // 10 seconds
    const startTime = Date.now();

    while (!window.grecaptcha && Date.now() - startTime < timeout) {
      await new Promise(resolve => setTimeout(resolve, 100));
    }

    if (!window.grecaptcha) {
      console.error(
        '[reCAPTCHA] Failed to load: grecaptcha not available after timeout',
      );
      return false;
    }

    console.log('[reCAPTCHA] Script loaded, executing with action:', action);

    return new Promise(resolve => {
      // Set a timeout to prevent hanging
      const timeoutId = setTimeout(() => {
        console.error('[reCAPTCHA] Execution timeout');
        resolve(false);
      }, timeout);

      if (!window.grecaptcha) {
        console.error('[reCAPTCHA] grecaptcha not available');
        clearTimeout(timeoutId);
        resolve(false);
        return;
      }

      window.grecaptcha.ready(async () => {
        try {
          console.log('[reCAPTCHA] Ready, executing...');

          if (!window.grecaptcha) {
            console.error(
              '[reCAPTCHA] grecaptcha not available in ready callback',
            );
            clearTimeout(timeoutId);
            resolve(false);
            return;
          }

          // Check if execute method exists (v3 API)
          if (!window.grecaptcha.execute) {
            console.error(
              '[reCAPTCHA] execute method not available. This might be a v2 site key. Please use reCAPTCHA v3 keys.',
            );
            clearTimeout(timeoutId);
            resolve(false);
            return;
          }

          let token: string | null = null;
          try {
            token = await window.grecaptcha.execute(siteKey, {action});
          } catch (execError: any) {
            console.error('[reCAPTCHA] Execution failed:', execError);
            // browser-error usually means domain/key mismatch or wrong version
            if (
              execError?.error === 'browser-error' ||
              execError?.message?.includes('browser-error')
            ) {
              console.error(
                '[reCAPTCHA] Browser error detected. This usually means:',
              );
              console.error(
                "  1. Site key is incorrect or doesn't match the domain",
              );
              console.error(
                "  2. You're using a v2 key with v3 API (need v3 keys)",
              );
              console.error(
                '  3. Domain (localhost) is not whitelisted in Google reCAPTCHA console',
              );
            }
            clearTimeout(timeoutId);
            resolve(false);
            return;
          }

          if (!token) {
            console.error('[reCAPTCHA] No token received from execute');
            clearTimeout(timeoutId);
            resolve(false);
            return;
          }

          console.log('[reCAPTCHA] Token received, verifying with backend...');

          try {
            const response = await apiClient.post('recaptcha/verify', {token});
            const success = response.data?.success ?? false;
            const errorCodes = response.data?.error_codes;
            const errorMessage = response.data?.message;

            console.log('[reCAPTCHA] Verification result:', {
              success,
              errorCodes,
              errorMessage,
              fullResponse: response.data,
            });

            if (!success && errorCodes) {
              console.error('[reCAPTCHA] Error codes from Google:', errorCodes);
              if (errorMessage) {
                console.error('[reCAPTCHA] Error message:', errorMessage);
              }
            }

            clearTimeout(timeoutId);
            resolve(success);
          } catch (err: any) {
            console.error('[reCAPTCHA] Verification API error:', {
              message: err?.message,
              response: err?.response?.data,
              status: err?.response?.status,
            });
            clearTimeout(timeoutId);
            resolve(false);
          }
        } catch (error) {
          console.error('[reCAPTCHA] Execution error:', error);
          clearTimeout(timeoutId);
          resolve(false);
        }
      });
    });
  } catch (error) {
    console.error('[reCAPTCHA] Load error:', error);
    return false;
  }
}

function load(siteKey: string) {
  return lazyLoader.loadAsset(
    `https://www.google.com/recaptcha/api.js?render=${siteKey}`,
    {id: 'recaptcha-js'},
  );
}
