<?php

namespace App\Helpers;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class AnalysisHelperOccupationalDisease
{
    public static function buildAIPrompt($request, $summary)
    {
        $year = self::formatRequestInput($request->input('year'), 'Tüm yıllar');
        $group = self::formatRequestInput($request->input('group_code'), 'Tüm grup kodları');
        $subGroup = self::formatRequestInput($request->input('sub_group_code'), 'Tüm alt grup kodları');

        $prompt = "Aşağıdaki tanı bazlı meslek hastalığı verilerini ISO 45001 standartlarına uygun teknik raporda analiz et:\n\n";
        $prompt .= "VERİ PARAMETRELERİ (ISO 45003:2021 Formatında):\n";
        $prompt .= "- Analiz Periyodu: " . self::formatISODateRange($year) . "\n";
        $prompt .= "- Tanı Grubu: " . $group . "\n";
        $prompt .= "- Tanı Alt Grubu: " . $subGroup . "\n\n";
        
        $prompt .= self::generateQuantitativeDataSection($summary);
        
        $prompt .= "ANALİZ PROTOKOLÜ:\n";
        $prompt .= "1. Risk Değerlendirmesi (ISO 45001 Madde 6.1.2):\n";
        $prompt .= "   - Tanı gruplarına göre hastalık sıklık oranı\n";
        $prompt .= "   - Hastalık nedenlerinin tanı dağılımı\n";
        $prompt .= "   - Kritik Hastalık Risk Haritalandırması (5x5 matris)\n\n";
        
        $prompt .= "2. Kök Neden Analizi (ISO 45002:2016):\n";
        $prompt .= "   - Meslek hastalıkları için Fishbone diyagramı\n";
        $prompt .= "   - Tanı bazlı hastalık nedenleri sınıflandırması\n\n";
        
        $prompt .= "3. Demografik Analiz (ILO C161 Uyumlu):\n";
        $prompt .= "   - Cinsiyet bazlı hastalık dağılımı\n";
        $prompt .= "   - Tanı gruplarına göre cinsiyet farklılıkları\n\n";
        
        $prompt .= "4. Önleme Planı (Hierarchy of Controls):\n";
        $prompt .= "   - Tanı gruplarına özel önlemler (EN ISO 12100)\n";
        $prompt .= "   - Tanıya uygun koruyucu prosedürler (PDCA döngüsüne uygun)\n\n";
        
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
        return "KANTİTATİF VERİLER (ISO 39001 Formatında):\n" .
               "- Toplam Meslek Hastalığı Vakası: " . ($summary['total_disease_cases'] ?? 0) . "\n" .
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
                'model' => env('OPENAI_MODEL', 'gpt-4'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "Sen bir ISO 45001 Sertifikalı İş Sağlığı Uzmanısın. "
                            . "Tüm çıktıları aşağıdaki kurallara göre hazırla:\n"
                            . "1. Tanı gruplarına göre meslek hastalıklarına odaklan\n"
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
            return "ISO 45001 uyumlu meslek hastalığı analizi geçici olarak mevcut değil. Lütfen teknik ekibe bildiriniz.";
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