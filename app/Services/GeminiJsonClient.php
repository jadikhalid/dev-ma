<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiJsonClient
{
    public function isConfigured(): bool
    {
        return filled(config('services.gemini.api_key'));
    }

    /**
     * @return array<string, mixed>
     */
    public function generateJson(string $prompt, int $maxOutputTokens = 256): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('not_configured');
        }

        $apiKey = (string) config('services.gemini.api_key');
        $primary = (string) config('services.gemini.model', 'gemini-3.5-flash-lite');
        $fallbacks = config('services.gemini.fallback_models', []);
        $models = array_values(array_unique(array_filter(array_merge(
            [$primary],
            is_array($fallbacks) ? $fallbacks : []
        ))));

        $endpointTemplate = (string) config(
            'services.gemini.endpoint',
            'https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent'
        );
        // Stay under typical PHP web max_execution_time (30s), including a possible fallback.
        $timeout = min(20, max(5, (int) config('services.gemini.timeout', 20)));
        $lastException = null;

        foreach ($models as $index => $model) {
            $url = str_replace('{model}', rawurlencode($model), $endpointTemplate);
            $attemptTimeout = $index === 0 ? $timeout : min(12, $timeout);

            try {
                $response = Http::timeout($attemptTimeout)
                    ->connectTimeout(5)
                    ->acceptJson()
                    ->asJson()
                    ->post($url.'?key='.urlencode($apiKey), [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [['text' => $prompt]],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.1,
                            'maxOutputTokens' => $maxOutputTokens,
                            'responseMimeType' => 'application/json',
                        ],
                    ]);
            } catch (ConnectionException $e) {
                $lastException = new RuntimeException('api_unreachable', 0, $e);

                continue;
            }

            if (! $response->successful()) {
                $lastException = new RuntimeException('api_error');

                continue;
            }

            $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

            if (! is_string($text) || trim($text) === '') {
                // Some models put JSON in a later part when thought signatures are present.
                $parts = data_get($response->json(), 'candidates.0.content.parts', []);
                if (is_array($parts)) {
                    foreach ($parts as $part) {
                        if (is_array($part) && isset($part['text']) && is_string($part['text']) && trim($part['text']) !== '') {
                            $text = $part['text'];
                            break;
                        }
                    }
                }
            }

            if (! is_string($text) || trim($text) === '') {
                $lastException = new RuntimeException('empty_response');

                continue;
            }

            $decoded = json_decode($this->extractJsonObject($text), true);

            if (! is_array($decoded)) {
                $lastException = new RuntimeException('invalid_json');

                continue;
            }

            return $decoded;
        }

        throw $lastException ?? new RuntimeException('api_error');
    }

    private function extractJsonObject(string $text): string
    {
        $text = trim($text);

        if (str_starts_with($text, '```')) {
            $text = preg_replace('/^```(?:json)?\s*/i', '', $text) ?? $text;
            $text = preg_replace('/\s*```$/', '', $text) ?? $text;
            $text = trim($text);
        }

        if (str_starts_with($text, '{')) {
            return $text;
        }

        if (preg_match('/\{.*\}/s', $text, $matches) === 1) {
            return $matches[0];
        }

        return $text;
    }
}
