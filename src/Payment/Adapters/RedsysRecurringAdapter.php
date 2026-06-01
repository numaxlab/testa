<?php

namespace Testa\Payment\Adapters;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Testa\Payment\RecurringChargeResult;
use Testa\Payment\RedsysRecurringChargeData;

/**
 * MIT (Merchant Initiated Transaction) adapter for Redsys recurring charges.
 *
 * Performs a server-to-server POST to Redsys using a stored payment_identifier
 * (Ds_Merchant_Identifier). No user redirect is involved.
 *
 * Requirements for this adapter to execute:
 *  - testa.{configKey}.enabled = true
 *  - testa.{configKey}.merchant_code is set
 *  - testa.{configKey}.secret_key is set
 *
 * When any requirement is missing the charge is ABORTED (not failed) and
 * logged at warning level so alerts are not triggered unnecessarily.
 */
class RedsysRecurringAdapter
{
    public function charge(RedsysRecurringChargeData $data): RecurringChargeResult
    {
        if (! $this->isConfigured($data->configKey)) {
            Log::warning('RedsysRecurringAdapter: missing or disabled configuration, charge aborted', [
                'config_key' => $data->configKey,
                'subscription_id' => $data->subscription->id,
            ]);

            return RecurringChargeResult::aborted('Redsys recurring not configured or disabled');
        }

        $order = $this->buildOrderNumber($data->subscription->id);
        $merchantCode = config("testa.{$data->configKey}.merchant_code");
        $terminal = config("testa.{$data->configKey}.terminal", '001');
        $secretKey = config("testa.{$data->configKey}.secret_key");
        $endpoint = config("testa.{$data->configKey}.endpoint", 'https://sis.redsys.es/sis/operaciones');

        $params = [
            'DS_MERCHANT_MERCHANTCODE' => $merchantCode,
            'DS_MERCHANT_TERMINAL' => $terminal,
            'DS_MERCHANT_TRANSACTIONTYPE' => '0',
            'DS_MERCHANT_ORDER' => $order,
            'DS_MERCHANT_AMOUNT' => (string) $data->amount,
            'DS_MERCHANT_CURRENCY' => '978',
            'DS_MERCHANT_IDENTIFIER' => $data->paymentIdentifier,
            'DS_MERCHANT_COF_INI' => 'N',
            'DS_MERCHANT_COF_TYPE' => 'R',
        ];

        try {
            $payload = $this->buildSignedPayload($params, $secretKey, $order);

            $response = Http::asForm()->post($endpoint, $payload);

            if ($response->failed()) {
                Log::error('RedsysRecurringAdapter: HTTP error from Redsys', [
                    'status' => $response->status(),
                    'subscription_id' => $data->subscription->id,
                ]);

                return RecurringChargeResult::failure('Redsys HTTP error: '.$response->status());
            }

            return $this->parseResponse($response->json(), $data->subscription->id);
        } catch (\Throwable $e) {
            Log::error('RedsysRecurringAdapter: unexpected error during charge', [
                'error' => $e->getMessage(),
                'subscription_id' => $data->subscription->id,
            ]);

            return RecurringChargeResult::failure('Unexpected error: '.$e->getMessage());
        }
    }

    protected function isConfigured(string $configKey): bool
    {
        return config("testa.{$configKey}.enabled", false)
            && ! empty(config("testa.{$configKey}.merchant_code"))
            && ! empty(config("testa.{$configKey}.secret_key"));
    }

    protected function buildOrderNumber(int $subscriptionId): string
    {
        // Redsys order numbers must be 4–12 alphanumeric chars, start with a digit.
        //
        // Strategy: 7-digit ID token + 4-char timestamp = 11 chars, always valid.
        //
        // ID token: ($subscriptionId % 10_000_000) zero-padded to 7 digits.
        //   - Unique for any realistic dataset (up to ~10M subscriptions, no modular collision).
        //   - Always starts with a digit (0–9).
        //
        // Timestamp: day + hour (format "dH", 4 chars).
        //   - Since each subscription renews at most once per billing period, two renewals
        //     cannot share the same (id_token, day, hour) within the same billing cycle.
        //   - Minutes are omitted intentionally — batch jobs run hourly at most.
        $idToken = sprintf('%07d', $subscriptionId % 10_000_000);
        $timeToken = now()->format('dH');

        return $idToken.$timeToken; // 11 chars, digit-first, Redsys-compliant
    }

    /**
     * Build a HMAC_SHA256_V1 signed payload for Redsys.
     */
    protected function buildSignedPayload(array $params, string $secretKey, string $orderNumber): array
    {
        $merchantParameters = base64_encode(json_encode($params));

        $derivedKey = $this->deriveKey($secretKey, $orderNumber);
        $signature = base64_encode(hash_hmac('sha256', $merchantParameters, $derivedKey, true));

        return [
            'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
            'Ds_MerchantParameters' => $merchantParameters,
            'Ds_Signature' => $signature,
        ];
    }

    /**
     * Derive 3DES key: decrypt the base64-encoded secret with the order number.
     */
    protected function deriveKey(string $base64Secret, string $orderNumber): string
    {
        $secretDecoded = base64_decode($base64Secret);
        $iv = str_repeat("\0", 8);
        $keyPadded = str_pad($orderNumber, 8, "\0");

        return openssl_encrypt(
            $keyPadded,
            'des-ede3-cbc',
            $secretDecoded,
            OPENSSL_RAW_DATA | OPENSSL_NO_PADDING,
            $iv,
        );
    }

    /**
     * Parse the Redsys response and return the appropriate result.
     */
    protected function parseResponse(?array $body, int $subscriptionId): RecurringChargeResult
    {
        if ($body === null || ! isset($body['Ds_MerchantParameters'])) {
            Log::error('RedsysRecurringAdapter: malformed response from Redsys', [
                'subscription_id' => $subscriptionId,
            ]);

            return RecurringChargeResult::failure('Malformed Redsys response');
        }

        $decoded = json_decode(base64_decode($body['Ds_MerchantParameters']), true);
        $dsResponse = $decoded['Ds_Response'] ?? null;

        if ($dsResponse === null) {
            return RecurringChargeResult::failure('Missing Ds_Response in Redsys response');
        }

        $code = (int) $dsResponse;

        if ($code >= 0 && $code <= 99) {
            return RecurringChargeResult::success();
        }

        return RecurringChargeResult::failure(
            'Redsys rejected charge: '.$dsResponse,
            $dsResponse,
        );
    }
}
