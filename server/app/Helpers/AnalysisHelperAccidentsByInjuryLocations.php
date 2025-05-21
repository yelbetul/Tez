<?php

namespace App\Helpers;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class AnalysisHelperAccidentsByInjuryLocations
{
    public static function buildAIPrompt($request, $summary)
    {
        $year = self::formatRequestInput($request->input('year'), 'Tüm yıllar');
        $groupCode = self::formatRequestInput($request->input('group_code'), 'Tüm vücut bölgeleri');
        $subGroupCode = self::formatRequestInput($request->input('sub_group_code'), 'Tüm alt gruplar');
        $locationCode = self::formatRequestInput($request->input('injury_location_code'), 'Tüm yaralanma bölgeleri');

        $prompt = "Aşağıdaki vücut bölgelerine göre iş kazası ve ölüm verilerini ISO 45001 standartlarına uygun teknik raporda analiz et:\n\n";
        $prompt .= "VERİ PARAMETRELERİ (ISO 45003:2021 Formatında):\n";
        $prompt .= "- Analiz Periyodu: " . self::formatISODateRange($year) . "\n";
        $prompt .= "- Vücut Bölgesi Grubu: " . self::formatBodyPartCode($groupCode) . "\n";
        $prompt .= "- Alt Grup: " . self::formatBodyPartCode($subGroupCode) . "\n";
        $prompt .= "- Spesifik Yaralanma Bölgesi: " . self::formatBodyPartCode($locationCode) . "\n\n";

        $prompt .= self::generateQuantitativeDataSection($summary);

        $prompt .= "ANALİZ PROTOKOLÜ:\n";
        $prompt .= "1. Risk Değerlendirmesi (ISO 45001 Madde 6.1.2):\n";
        $prompt .= "   - Vücut bölgelerine göre yaralanma sıklık oranı\n";
        $prompt .= "   - Ölümcül yaralanmalar için kritik risk haritalandırması (5x5 matris)\n";
        $prompt .= "   - İş göremezlik sürelerine göre yaralanma şiddeti analizi\n\n";

        $prompt .= "2. Kök Neden Analizi (ISO 45002:2016):\n";
        $prompt .= "   - Vücut bölgelerine göre kaza mekanizmaları (Fishbone diyagramı)\n";
        $prompt .= "   - Ölümcül yaralanmalar için ICD-10 sınıflandırması\n";
        $prompt .= "   - Koruyucu ekipman etkinlik analizi\n\n";

        $prompt .= "3. Demografik Analiz (ILO C161 Uyumlu):\n";
        $prompt .= "   - Cinsiyete göre yaralanma bölgesi dağılımı\n";
        $prompt .= "   - Yaş gruplarına göre yaralanma şiddeti\n\n";

        $prompt .= "4. Önleme Planı (Hierarchy of Controls):\n";
        $prompt .= "   - Bölgesel koruyucu ekipman iyileştirmeleri (EN ISO 13688)\n";
        $prompt .= "   - Kritik vücut bölgeleri için ergonomik düzenlemeler\n";
        $prompt .= "   - Yaralanma önleme eğitim programları\n\n";

        $prompt .= "5. Projeksiyon (ISO 45001 Madde 6.1):\n";
        $prompt .= "   - Vücut bölgelerine göre 3 yıllık yaralanma trendi\n\n";

        $prompt .= "RAPOR FORMAT KURALLARI:\n";
        $prompt .= "- Tamamen ISO 45001 terminolojisi kullan\n";
        $prompt .= "- Anatomik referansları standardize edilmiş terimlerle belirt\n";
        $prompt .= "- Yalnızca kanıta dayalı çıkarımlar yap\n";
        $prompt .= "- Kesinlikle meta veri içerme\n";
        $prompt .= "- Madde numaralandırması ISO belgeleriyle uyumlu olsun";

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
        $fatalityRate = $summary['total_cases'] > 0
            ? round(($summary['total_fatalities']/$summary['total_cases'])*100, 2)
            : 0;

        return "KANTİTATİF VERİLER (ISO 39001 Formatında):\n" .
            "- Toplam Yaralanma: " . ($summary['total_cases'] ?? 0) . "\n" .
            "- İş Göremezlik Vakaları: " . ($summary['total_unfit'] ?? 0) . "\n" .
            "- Ölümcül Yaralanmalar: " . ($summary['total_fatalities'] ?? 0) . " (%".$fatalityRate.")\n" .
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

    private static function formatBodyPartCode($input): string
    {
        if (str_contains($input, ',')) {
            return "Çoklu Bölge (" . $input . ")";
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
                        'content' => "Sen bir ISO 45001 Sertifikalı İş Güvenliği Uzmanı ve İş Sağlığı Hekimisin. "
                            . "Tüm çıktıları aşağıdaki kurallara göre hazırla:\n"
                            . "1. Vücut bölgelerine göre yaralanma verilerine odaklan\n"
                            . "2. ICD-10 yaralanma sınıflandırmasını kullan\n"
                            . "3. Yalnızca kanıta dayalı tıbbi çıkarımlar yap\n"
                            . "4. Madde numaralandırması ISO belge formatında olsun\n"
                            . "KESİNLİKLE YAPMAYACAKLARIN:\n"
                            . "1. İmza, tarih, hazırlayan bilgisi EKLEME\n"
                            . "2. 'Rapor', 'Belge' gibi başlıklar KOYMA\n"
                            . "3. Kişisel ifadeler (ör: 'tavsiye ederim') KULLANMA\n"
                            . "4. Teşhis ve tedavi önerisi SUNMA"
                    ],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.5,
                'max_tokens' => 2000
            ]);

            return self::sanitizeISOReport($response->choices[0]->message->content);

        } catch (\Exception $e) {
            Log::error('OpenAI Hatası: ' . $e->getMessage());
            return "ISO 45001 uyumlu yaralanma bölgesi analizi geçici olarak mevcut değil. Lütfen teknik ekibe bildiriniz.";
        }
    }

    private static function sanitizeISOReport(string $content): string
    {
        $replacements = [
            '/\b(Tarih|Date):.*$/mi' => '',
            '/\b(Rapor No|Document Number):.*$/mi' => '',
            '/(?<!\w)\.(?!\d)/' => '.',
            '/Teşhis:|Tedavi:/i' => '' // Tıbbi önerileri filtrele
        ];

        return preg_replace(array_keys($replacements), array_values($replacements), $content);
    }
}
