<?php

namespace App\Helpers;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class AnalysisHelperFatalWorkAccidents
{
    public static function buildAIPrompt($request, $summary)
    {
        $year = self::formatRequestInput($request->input('year'), 'Tüm yıllar');
        $sectorCode = self::formatRequestInput($request->input('sector_code'), 'Tüm sektörler');

        $prompt = "Aşağıdaki ölümlü iş kazası verilerini ISO 45001 standartlarına uygun teknik raporda analiz et:\n\n";
        $prompt .= "VERİ PARAMETRELERİ (ISO 45003:2021 Formatında):\n";
        $prompt .= "- Analiz Periyodu: " . self::formatISODateRange($year) . "\n";
        $prompt .= "- Sektör Kodu: " . self::formatISOSectorCode($sectorCode) . "\n\n";
        
        $prompt .= self::generateQuantitativeDataSection($summary);
        
        $prompt .= "ANALİZ PROTOKOLÜ:\n";
        $prompt .= "1. Risk Değerlendirmesi (ISO 45001 Madde 6.1.2):\n";
        $prompt .= "   - Ölümlü Vaka Sıklık Oranı (FFR) hesaplaması\n";
        $prompt .= "   - OHS kriterlerine göre kritik risk skorlaması (5x5 matris)\n\n";
        
        $prompt .= "2. Kök Neden Analizi (ISO 45002:2016):\n";
        $prompt .= "   - Ölüm vakaları için Fishbone diyagramı çıkarımı\n";
        $prompt .= "   - ISIC Rev.4 sınıflandırmasıyla uyumlu fatalite tespitleri\n\n";
        
        $prompt .= "3. Demografik Analiz (ILO C161 Uyumlu):\n";
        $prompt .= "   - Cinsiyet bazlı mortalite dağılımı (Odds Ratio hesaplamalı)\n\n";
        
        $prompt .= "4. İyileştirme Planı (Hierarchy of Controls):\n";
        $prompt .= "   - Kritik riskler için mühendislik kontrolleri (EN ISO 12100)\n";
        $prompt .= "   - Acil durum önlemleri (PDCA döngüsüne uygun)\n\n";
        
        $prompt .= "5. Projeksiyon (ISO 45001 Madde 6.1):\n";
        $prompt .= "   - Ölümlü vakalar için 3 yıllık trend analizi (ARIMA model önerisi)\n\n";
        
        $prompt .= "RAPOR FORMAT KURALLARI:\n";
        $prompt .= "- Tamamen ISO 45001 terminolojisi kullan\n";
        $prompt .= "- Yalnızca kanıta dayalı çıkarımlar (Evidence-based)\n";
        $prompt .= "- Kesinlikle meta veri içerme\n";
        $prompt .= "- Madde numaralandırması ISO belgeleriyle uyumlu\n";
        $prompt .= "- Tüm hesaplamalarda formül belirt (Örn: FFR=(Ölümlü Vaka Sayısı/Çalışılan Saat)x1.000.000)";

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
            ? round(($summary['total_accident_fatalities']/$summary['total_fatalities'])*100, 2)
            : 0;

        return "KANTİTATİF VERİLER (ISO 39001 Formatında):\n" .
               "- Toplam Ölüm: " . ($summary['total_fatalities'] ?? 0) . " (n)\n" .
               "- İş Kazası Kaynaklı Ölümler: " . ($summary['total_accident_fatalities'] ?? 0) . " (%".$fatalityPercentage.")\n" .
               "- Meslek Hastalığı Kaynaklı Ölümler: " . ($summary['total_disease_fatalities'] ?? 0) . " (ICD-10 koduyla)\n" .
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

    private static function formatISOSectorCode($input): string
    {
        if (str_contains($input, ',')) {
            return "Çoklu Sektör (" . strtoupper($input) . ")";
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
                            . "1. Kesinlikle ISO standart terminolojisi kullan\n"
                            . "2. Yalnızca kanıta dayalı çıkarımlar yap\n"
                            . "3. Meta veri (tarih, imza vb.) EKLEME\n"
                            . "4. Madde numaralandırması ISO belge formatında olsun"
                            ."KESİNLİKLE YAPMAYACAKLARIN:\n"
                            ."1. İmza, tarih, hazırlayan bilgisi EKLEME\n"
                            ."2. 'Rapor', 'Belge' gibi başlıklar KOYMA\n"
                            ."3. Kişisel ifadeler (ör: 'tavsiye ederim') KULLANMA\n"
                      		."4. Formülleri RAPORDA GÖSTERME"
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
        // ISO standartlarına uygun son formatlama
        $replacements = [
            '/\b(Tarih|Date):.*$/mi' => '',
            '/\b(Rapor No|Document Number):.*$/mi' => '',
            '/(?<!\w)\.(?!\d)/' => '.' // Standart sonlandırma
        ];
        
        return preg_replace(array_keys($replacements), array_values($replacements), $content);
    }
}