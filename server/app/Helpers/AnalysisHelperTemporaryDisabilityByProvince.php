<?php

namespace App\Helpers;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class AnalysisHelperTemporaryDisabilityByProvince
{
    public static function buildAIPrompt($request, $summary)
    {
        $year = self::formatRequestInput($request->input('year'), 'Tüm yıllar');
        $provinceCode = self::formatRequestInput($request->input('province_code'), 'Tüm iller');
        $treatmentType = self::formatRequestInput($request->input('is_outpatient'), 'Tüm tedavi türleri');

        $prompt = "Aşağıdaki geçici iş göremezlik verilerini ISO 45001 standartlarına uygun teknik raporda analiz et:\n\n";
        $prompt .= "VERİ PARAMETRELERİ (ISO 45003:2021 Formatında):\n";
        $prompt .= "- Analiz Periyodu: " . self::formatISODateRange($year) . "\n";
        $prompt .= "- İl Kodu: " . self::formatISOProvinceCode($provinceCode) . "\n";
        $prompt .= "- Tedavi Türü: " . ($treatmentType === '1' ? 'Ayakta Tedavi' : ($treatmentType === '0' ? 'Yatarak Tedavi' : $treatmentType)) . "\n\n";
        
        $prompt .= self::generateQuantitativeDataSection($summary);
        
        $prompt .= "ANALİZ PROTOKOLÜ:\n";
        $prompt .= "1. Risk Değerlendirmesi (ISO 45001 Madde 6.1.2):\n";
        $prompt .= "   - İş Göremezlik Vaka Sıklık Oranı (TIFR) hesaplaması\n";
        $prompt .= "   - Kayıp İş Günü Endeksi (LDI) analizi\n";
        $prompt .= "   - İş Göremezlik Süresi Bazlı Risk Haritalandırması (5x5 matris)\n\n";
        
        $prompt .= "2. Kök Neden Analizi (ISO 45002:2016):\n";
        $prompt .= "   - İş göremezlik sürelerine göre Fishbone diyagramı\n";
        $prompt .= "   - İl bazlı iş göremezlik nedenleri sınıflandırması\n\n";
        
        $prompt .= "3. Demografik Analiz (ILO C161 Uyumlu):\n";
        $prompt .= "   - Cinsiyet bazlı iş göremezlik dağılımı\n";
        $prompt .= "   - Tedavi türüne göre iş göremezlik süreleri\n";
        $prompt .= "   - İş göremezlik günlerinin bölgesel dağılımı\n\n";
        
        $prompt .= "4. İyileştirme Planı (Hierarchy of Controls):\n";
        $prompt .= "   - Kısa süreli iş göremezlikler için önleyici tedbirler (EN ISO 12100)\n";
        $prompt .= "   - Uzun süreli iş göremezlikler için rehabilitasyon programları\n";
        $prompt .= "   - Tedavi türüne özel iş sağlığı protokolleri (PDCA döngüsüne uygun)\n\n";
        
        $prompt .= "5. Projeksiyon (ISO 45001 Madde 6.1):\n";
        $prompt .= "   - İş göremezlik süreleri için 3 yıllık trend analizi (ARIMA model önerisi)\n\n";
        
        $prompt .= "RAPOR FORMAT KURALLARI:\n";
        $prompt .= "- Tamamen ISO 45001 terminolojisi kullan\n";
        $prompt .= "- Yalnızca kanıta dayalı çıkarımlar (Evidence-based)\n";
        $prompt .= "- Kesinlikle meta veri içerme\n";
        $prompt .= "- Madde numaralandırması ISO belgeleriyle uyumlu\n";
        $prompt .= "- Tüm hesaplamalarda formül belirt (Örn: LDI=Toplam Kayıp Gün Sayısı/Çalışan Sayısı)";

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
               "- Toplam Kayıp İş Günü: " . ($summary['total_days_unfit'] ?? 0) . " gün\n" .
               "- Vaka Dağılımı:\n" .
               "  • 1 Gün: " . ($summary['one_day_unfit'] ?? 0) . " vaka\n" .
               "  • 2 Gün: " . ($summary['two_days_unfit'] ?? 0) . " vaka\n" .
               "  • 3 Gün: " . ($summary['three_days_unfit'] ?? 0) . " vaka\n" .
               "  • 4 Gün: " . ($summary['four_days_unfit'] ?? 0) . " vaka\n" .
               "  • 5+ Gün: " . ($summary['five_or_more_days_unfit'] ?? 0) . " vaka\n" .
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
                            . "1. Geçici iş göremezlik vakalarına odaklan\n"
                            . "2. İş göremezlik sürelerini ve tedavi türlerini analiz et\n"
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