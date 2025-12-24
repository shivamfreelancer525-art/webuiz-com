<?php

namespace App\Console\Commands;

use Common\Domains\CustomDomain;
use Common\Domains\CustomDomainController;
use Common\Domains\PendingCustomDomain;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Http\Client\ConnectionException;

class ValidatePendingCustomDomains extends Command
{
    protected $signature = 'custom-domains:validate-pending';
    protected $description = 'Validate pending custom domains in queue (runs every 10 minutes)';

    public function handle()
    {
        $this->info('Starting pending custom domain validation...');

        // Get pending domains that are less than 1 hour old and haven't exceeded 6 attempts
        $pendingDomains = PendingCustomDomain::where('status', 'pending')
            ->where('created_at', '>=', now()->subHour())
            ->where('validation_attempts', '<', 6)
            ->get();

        $this->info("Found {$pendingDomains->count()} pending domains to validate");

        foreach ($pendingDomains as $pendingDomain) {
            $this->validateDomain($pendingDomain);
        }

        // Mark expired domains as failed
        $expiredCount = PendingCustomDomain::where('status', 'pending')
            ->where('created_at', '<', now()->subHour())
            ->update(['status' => 'failed', 'validation_error' => 'Validation timeout: Domain was not validated within 1 hour']);

        if ($expiredCount > 0) {
            $this->info("Marked {$expiredCount} expired domains as failed");
        }

        $this->info('Validation complete');
        return 0;
    }

    protected function validateDomain(PendingCustomDomain $pendingDomain)
    {
        $this->line("Validating: {$pendingDomain->host}");

        // Update status to validating
        $pendingDomain->update([
            'status' => 'validating',
            'validation_attempts' => $pendingDomain->validation_attempts + 1,
        ]);

        $isValid = false;
        $error = null;

        try {
            // Check DNS
            $host = parse_url($pendingDomain->host, PHP_URL_HOST);
            $dns = dns_get_record($host ?? $pendingDomain->host);
            
            $recordWithIp = Arr::first($dns, fn($record) => isset($record['ip']));
            if (
                !empty($dns) &&
                isset($recordWithIp) &&
                $recordWithIp['ip'] === $pendingDomain->server_ip
            ) {
                // DNS is correct, check HTTP validation
                $host = trim($pendingDomain->host, '/');
                try {
                    $response = Http::timeout(10)->get(
                        "$host/" . CustomDomainController::VALIDATE_CUSTOM_DOMAIN_PATH,
                    )->json();

                    if (Arr::get($response, 'result') === 'connected') {
                        $isValid = true;
                    } else {
                        $error = 'HTTP validation failed: Server not configured';
                    }
                } catch (ConnectionException $e) {
                    $error = 'HTTP connection failed: ' . $e->getMessage();
                }
            } else {
                $error = 'DNS record not found or incorrect IP';
            }
        } catch (Exception $e) {
            $error = 'DNS lookup failed: ' . $e->getMessage();
        }

        if ($isValid) {
            // Move to custom_domains table
            $customDomain = CustomDomain::create([
                'host' => $pendingDomain->host,
                'user_id' => $pendingDomain->user_id,
                'global' => false,
            ]);

            // Mark as valid and delete from queue
            $pendingDomain->update([
                'status' => 'valid',
                'validated_at' => now(),
            ]);
            $pendingDomain->delete();

            $this->info("✓ {$pendingDomain->host} validated successfully");
        } else {
            // Mark as pending again for next attempt
            $pendingDomain->update([
                'status' => 'pending',
                'validation_error' => $error,
            ]);

            $this->warn("✗ {$pendingDomain->host} validation failed: {$error}");
        }
    }
}

