<?php

namespace App\Helpers;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class AnalysisHelperFatalWorkAccidentsByMonth
{
    public static function buildAIPrompt($request, $summary)
    {
        $year = self::formatRequestInput($request->input('year'), 'Tüm yıllar');
        $month = self::formatRequestInput($request->input('month'), 'Tüm aylar');

        $prompt = "Aşağıdaki ölümlü iş kazası ve meslek hastalığı verilerini ISO 45001 standartlarına uygun teknik raporda analiz et:\n\n";
        $prompt .= "VERİ PARAMETRELERİ (ISO 45003:2021 Formatında):\n";
        $prompt .= "- Analiz Periyodu: " . self::formatISODateRange($year) . "\n";
        $prompt .= "- Ay: " . $month . "\n\n";

        $prompt .= self::generateQuantitativeDataSection($summary);

        $prompt .= "ANALİZ PROTOKOLÜ:\n";
        $prompt .= "1. Risk Değerlendirmesi (ISO 45001 Madde 6.1.2):\n";
        $prompt .= "   - Ölümlü iş kazalarının aylık dağılımı\n";
        $prompt .= "   - Meslek hastalığı kaynaklı ölümlerin analizi\n";
        $prompt .= "   - Kritik Dönem Haritalandırması (5x5 matris)\n\n";

        $prompt .= "2. Kök Neden Analizi (ISO 45002:2016):\n";
        $prompt .= "   - Mevsimsel etki faktörleri\n";
        $prompt .= "   - Ölümlü kazaların temel nedenleri\n\n";

        $prompt .= "3. Demografik Analiz (ILO C161 Uyumlu):\n";
        $prompt .= "   - Cinsiyet bazlı ölüm dağılımı\n";
        $prompt .= "   - Ölüm türlerine göre cinsiyet farklılıkları\n\n";

        $prompt .= "4. Önleme Planı (Hierarchy of Controls):\n";
        $prompt .= "   - Ölümcül kazalar için acil önlemler (EN ISO 12100)\n";
        $prompt .= "   - Ölüm risklerini azaltmaya yönelik PDCA döngüsü\n\n";

        $prompt .= "RAPOR FORMAT KURALLARI:\n";
        $prompt .= "- Tamamen ISO 45001 terminolojisi kullan\n";
        $prompt .= "- Yalnızca kanıta dayalı çıkarımlar (Evidence-based)\n";
        $prompt .= "- Kesinlikle meta veri içerme\n";
        $prompt .= "- Madde numaralandırması ISO belgeleriyle uyumlu\n";

        return $prompt;
    }

    private static function formatRequestInput($input, $default = '')
    {
        if (is_array($input)) {
            return implode(', ', array_filter($input));
        }
        return $input ?? $default;
    }

    private static function generateQuantitativeDataSection(array $summary): string
    {
        $fatalityPercentage = $summary['total_fatalities'] > 0
            ? round(($summary['total_work_accident_fatalities']/$summary['total_fatalities'])*100, 2)
            : 0;

        return "KANTİTATİF VERİLER (ISO 39001 Formatında):\n" .
            "- Toplam Ölüm: " . ($summary['total_fatalities'] ?? 0) . "\n" .
            "- İş Kazası Kaynaklı Ölümler: " . ($summary['total_work_accident_fatalities'] ?? 0) . " (%".$fatalityPercentage.")\n" .
            "- Meslek Hastalığı Kaynaklı Ölümler: " . ($summary['total_occupational_disease_fatalities'] ?? 0) . "\n" .
            "- Cinsiyet Dağılımı:\n" .
            "  • Erkek: " . ($summary['male_count'] ?? 0) . " (%".($summary['male_percentage'] ?? 0).")\n" .
            "  • Kadın: " . ($summary['female_count'] ?? 0) . " (%".($summary['female_percentage'] ?? 0).")\n\n";
    }

    private static function formatISODateRange($input): string
    {
        if (str_contains($input, ',')) {
            $years = explode(',', $input);
            return "[" . min($years) . "-" . max($years) . "]";
        }
        return $input;
    }

    public static function getAICommentary($prompt)
    {
        try {
            $response = OpenAI::chat()->create([
                'model' => env('OPENAI_MODEL', 'gpt-4o'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "Sen bir ISO 45001 Sertifikalı İş Sağlığı Uzmanısın. "
                            . "Tüm çıktıları aşağıdaki kurallara göre hazırla:\n"
                            . "1. Ölümlü iş kazaları ve meslek hastalıklarına odaklan\n"
                            . "2. Yalnızca kanıta dayalı çıkarımlar yap\n"
                            . "3. Meta veri (tarih, imza vb.) EKLEME\n"
                            . "4. Madde numaralandırması ISO belge formatında olsun\n"
                            . "KESİNLİKLE YAPMAYACAKLARIN:\n"
                            . "1. İmza, tarih, hazırlayan bilgisi EKLEME\n"
                            . "2. 'Rapor', 'Belge' gibi başlıklar KOYMA\n"
                            . "3. Kişisel ifadeler (ör: 'tavsiye ederim') KULLANMA"
                    ],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.5,
                'max_tokens' => 2000
            ]);

            return self::sanitizeISOReport($response->choices[0]->message->content);

        } catch (\Exception $e) {
            Log::error('OpenAI Hatası: ' . $e->getMessage());
            return "ISO 45001 uyumlu ölümlü iş kazası analizi geçici olarak mevcut değil. Lütfen teknik ekibe bildiriniz.";
        }
    }

    private static function sanitizeISOReport(string $content): string
    {
        $replacements = [
            '/\b(Tarih|Date):.*$/mi' => '',
            '/\b(Rapor No|Document Number):.*$/mi' => '',
            '/(?<!\w)\.(?!\d)/' => '.'
        ];

        return preg_replace(array_keys($replacements), array_values($replacements), $content);
    }
}
