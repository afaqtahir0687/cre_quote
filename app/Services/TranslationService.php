<?php

namespace App\Services;

use App\Models\DynamicTranslation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    /**
     * Local in-memory runtime cache to prevent repeat queries on the same request.
     */
    protected static array $memoryCache = [];

    /**
     * Check if a text contains Arabic characters.
     */
    public static function isArabic(?string $text): bool
    {
        if (empty($text)) {
            return false;
        }
        return (bool) preg_match('/[\x{0600}-\x{06FF}]/u', $text);
    }

    /**
     * Translate text between English and Arabic based on active locale or specified target language.
     */
    public static function translate(?string $text, ?string $targetLang = null): string
    {
        if ($text === null) {
            return '';
        }

        $trimmed = trim($text);
        if ($trimmed === '' || is_numeric($trimmed) || strlen($trimmed) < 2) {
            return $text;
        }

        $currentLocale = app()->getLocale() ?: 'en';
        $isArabicSource = self::isArabic($trimmed);

        if ($targetLang === null) {
            if ($currentLocale === 'ar') {
                if ($isArabicSource) {
                    return $text; // Already in Arabic
                }
                $targetLang = 'ar';
            } else {
                if (!$isArabicSource) {
                    return $text; // Already in English
                }
                $targetLang = 'en';
            }
        }

        // If target is same as source detection
        if (($targetLang === 'ar' && $isArabicSource) || ($targetLang === 'en' && !$isArabicSource)) {
            return $text;
        }

        $hash = md5(mb_strtolower($trimmed, 'UTF-8'));
        $cacheKey = "dtrans_{$hash}_{$targetLang}";

        // 1. Check in-memory runtime cache
        if (isset(self::$memoryCache[$cacheKey])) {
            return self::$memoryCache[$cacheKey];
        }

        // 2. Check Laravel Cache
        $cached = Cache::get($cacheKey);
        if ($cached) {
            self::$memoryCache[$cacheKey] = $cached;
            return $cached;
        }

        // 3. Check Database Table
        try {
            $record = DynamicTranslation::where('hash', $hash)
                ->where('target_lang', $targetLang)
                ->first();

            if ($record && !empty($record->translated_text)) {
                Cache::put($cacheKey, $record->translated_text, now()->addDays(30));
                self::$memoryCache[$cacheKey] = $record->translated_text;
                return $record->translated_text;
            }
        } catch (\Throwable $e) {
            // Silently fallback if DB query fails
        }

        // 4. Fetch Translation from Google Translate API
        $translated = self::fetchFromApi($trimmed, $targetLang);

        if (!empty($translated)) {
            // Save to DB
            try {
                DynamicTranslation::updateOrCreate(
                    ['hash' => $hash, 'target_lang' => $targetLang],
                    [
                        'source_text' => $trimmed,
                        'translated_text' => $translated,
                        'source_lang' => $isArabicSource ? 'ar' : 'en',
                    ]
                );
            } catch (\Throwable $e) {
                // Ignore DB write errors
            }

            Cache::put($cacheKey, $translated, now()->addDays(30));
            self::$memoryCache[$cacheKey] = $translated;
            return $translated;
        }

        return $text;
    }

    /**
     * Pre-warm cache for a text in the opposite language on record creation/update.
     */
    public static function warmCache(?string $text): void
    {
        if (empty($text) || is_numeric($text)) {
            return;
        }

        $trimmed = trim($text);
        $isArabic = self::isArabic($trimmed);
        $targetLang = $isArabic ? 'en' : 'ar';

        try {
            self::translate($trimmed, $targetLang);
        } catch (\Throwable $e) {
            // Fail silently so record save is never blocked
        }
    }

    /**
     * Call the translation endpoints (Primary: Google Clients5, Fallback: MyMemory).
     */
    protected static function fetchFromApi(string $text, string $targetLang): ?string
    {
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept' => '*/*',
        ];

        // 1. Primary: Google Clients5 Chrome Extension Endpoint
        try {
            $url = 'https://clients5.google.com/translate_a/t?client=dict-chrome-ex&sl=auto&tl=' . $targetLang . '&q=' . urlencode($text);

            $response = Http::withHeaders($headers)
                ->timeout(3.5)
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data) && isset($data[0])) {
                    if (is_array($data[0]) && isset($data[0][0])) {
                        return trim($data[0][0]);
                    } elseif (is_string($data[0])) {
                        return trim($data[0]);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('TranslationService Primary API error: ' . $e->getMessage());
        }

        // 2. Secondary Fallback: MyMemory API
        try {
            $sourceLang = self::isArabic($text) ? 'ar' : 'en';
            $url = 'https://api.mymemory.translated.net/get?q=' . urlencode($text) . '&langpair=' . $sourceLang . '|' . $targetLang;

            $response = Http::timeout(3.0)->get($url);
            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['responseData']['translatedText'])) {
                    return trim($data['responseData']['translatedText']);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('TranslationService Fallback API error: ' . $e->getMessage());
        }

        return null;
    }
}
