<?php

namespace App\Helpers;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class AnalysisHelperDisabilityOccupationalDiseaseByProvince
{
    public static function buildAIPrompt($request, $summary)
    {
        $year = self::formatRequestInput($request->input('year'), 'Tüm yıllar');
        $provinceCode = self::formatRequestInput($request->input('province_code'), 'Tüm iller');

        $prompt = "Aşağıdaki meslek hastalığı iş göremezlik verilerini ISO 45001 standartlarına uygun teknik raporda analiz et:\n\n";
        $prompt .= "VERİ PARAMETRELERİ (ISO 45003:2021 Formatında):\n";
        $prompt .= "- Analiz Periyodu: " . self::formatISODateRange($year) . "\n";
        $prompt .= "- İl Kodu: " . self::formatISOProvinceCode($provinceCode) . "\n\n";
        
        $prompt .= self::generateQuantitativeDataSection($summary);
        
        $prompt .= "ANALİZ PROTOKOLÜ:\n";
        $prompt .= "1. Risk Değerlendirmesi (ISO 45001 Madde 6.1.2):\n";
        $prompt .= "   - Meslek Hastalığı Sıklık Oranı (OFR) hesaplaması\n";
        $prompt .= "   - Tedavi Türüne Göre Dağılım Analizi\n";
        $prompt .= "   - OHS kriterlerine göre risk skorlaması (5x5 matris)\n\n";
        
        $prompt .= "2. Kök Neden Analizi (ISO 45002:2016):\n";
        $prompt .= "   - Meslek hastalığı türlerine göre Fishbone diyagramı\n";
        $prompt .= "   - İl bazlı meslek hastalığı dağılımı\n\n";
        
        $prompt .= "3. Demografik Analiz (ILO C161 Uyumlu):\n";
        $prompt .= "   - Cinsiyet bazlı iş göremezlik dağılımı\n";
        $prompt .= "   - Ayakta/Yatarak tedavi oranları karşılaştırması\n\n";
        
        $prompt .= "4. İyileştirme Planı (Hierarchy of Controls):\n";
        $prompt .= "   - Erken tanı protokolleri (EN ISO 12100)\n";
        $prompt .= "   - Rehabilitasyon süreçleri (PDCA döngüsüne uygun)\n\n";
        
        $prompt .= "5. Projeksiyon (ISO 45001 Madde 6.1):\n";
        $prompt .= "   - Meslek hastalıkları için 3 yıllık trend analizi (ARIMA model önerisi)\n\n";
        
        $prompt .= "RAPOR FORMAT KURALLARI:\n";
        $prompt .= "- Tamamen ISO 45001 terminolojisi kullan\n";
        $prompt .= "- Yalnızca kanıta dayalı çıkarımlar (Evidence-based)\n";
        $prompt .= "- Kesinlikle meta veri içerme\n";
        $prompt .= "- Madde numaralandırması ISO belgeleriyle uyumlu\n";
        $prompt .= "- Tüm hesaplamalarda formül belirt (Örn: OFR=(Vaka Sayısı/Çalışan Sayısı)x1.000)";

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
        $outpatientPercentage = $summary['total_cases'] > 0 
            ? round(($summary['outpatient_cases']/$summary['total_cases'])*100, 2)
            : 0;
        
        $inpatientPercentage = $summary['total_cases'] > 0 
            ? round(($summary['inpatient_cases']/$summary['total_cases'])*100, 2)
            : 0;

        return "KANTİTATİF VERİLER (ISO 39001 Formatında):\n" .
               "- Toplam Vaka: " . ($summary['total_cases'] ?? 0) . " (n)\n" .
               "- Tedavi Türü:\n" .
               "  • Ayakta Tedavi: " . ($summary['outpatient_cases'] ?? 0) . " vaka (%".$outpatientPercentage.")\n" .
               "  • Yatarak Tedavi: " . ($summary['inpatient_cases'] ?? 0) . " vaka (%".$inpatientPercentage.")\n" .
               "- Cinsiyet Dağılımı:\n" .
               "  • Erkek: %" . ($summary['male_percentage'] ?? 0) . " (95% CI)\n" .
               "  • Kadın: %" . ($summary['female_percentage'] ?? 0) . " (95% CI)\n\n";
    }

    private static function formatISODateRange($input): string
    {
        if (str_contains($input, ',')) {
            $years = explode(',', $input);
            return "[" . min($years) . "-" . max($years) . "]";
        }
        return $input;
    }

    private static function formatISOProvinceCode($input): string
    {
        if (str_contains($input, ',')) {
            return "Çoklu İl (" . strtoupper($input) . ")";
        }
        return strtoupper($input);
    }

    public static function getAICommentary($prompt)
    {
        try {
            $response = OpenAI::chat()->create([
                'model' => env('OPENAI_MODEL', 'gpt-4o'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "Sen bir ISO 45001 Sertifikalı İş Güvenliği Uzmanısın. "
                            . "Tüm çıktıları aşağıdaki kurallara göre hazırla:\n"
                            . "1. Meslek hastalıkları ve iş göremezlik vakalarına odaklan\n"
                            . "2. Tedavi türlerini (ayakta/yatarak) analiz et\n"
                            . "3. Bölgesel/şehir bazlı analiz yap\n"
                            . "4. Yalnızca kanıta dayalı çıkarımlar yap\n"
                            . "5. Meta veri (tarih, imza vb.) EKLEME\n"
                            . "6. Madde numaralandırması ISO belge formatında olsun\n"
                            . "KESİNLİKLE YAPMAYACAKLARIN:\n"
                            . "1. İmza, tarih, hazırlayan bilgisi EKLEME\n"
                            . "2. 'Rapor', 'Belge' gibi başlıklar KOYMA\n"
                            . "3. Kişisel ifadeler (ör: 'tavsiye ederim') KULLANMA\n"
                            . "4. Formülleri RAPORDA GÖSTERME"
                    ],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.5,
                'max_tokens' => 2000
            ]);

            return self::sanitizeISOReport($response->choices[0]->message->content);

        } catch (\Exception $e) {
            Log::error('OpenAI Hatası: ' . $e->getMessage());
            return "ISO 45001 uyumlu analiz geçici olarak mevcut değil. Lütfen teknik ekibe bildiriniz.";
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