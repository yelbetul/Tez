<?php

namespace App\Helpers;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class AnalysisHelperOccDiseaseFatalities
{
    public static function buildAIPrompt($request, $summary)
    {
        $year = self::formatRequestInput($request->input('year'), 'Tüm yıllar');
        $groupCode = self::formatRequestInput($request->input('group_code'), 'Tüm meslek grupları');
        $subGroupCode = self::formatRequestInput($request->input('sub_group_code'), 'Tüm alt gruplar');
        $pureCode = self::formatRequestInput($request->input('pure_code'), 'Tüm meslekler');

        $prompt = "Aşağıdaki mesleki hastalık ve ölüm verilerini ISO 45001 ve ILO standartlarına uygun teknik raporda analiz et:\n\n";
        $prompt .= "VERİ PARAMETRELERİ (ISO 45003:2021 Formatında):\n";
        $prompt .= "- Analiz Periyodu: " . self::formatISODateRange($year) . "\n";
        $prompt .= "- Meslek Grubu: " . self::formatOccupationCode($groupCode) . " (ISCO-08)\n";
        $prompt .= "- Alt Grup: " . self::formatOccupationCode($subGroupCode) . "\n";
        $prompt .= "- Meslek: " . self::formatOccupationCode($pureCode) . "\n\n";

        $prompt .= self::generateQuantitativeDataSection($summary);

        $prompt .= "ANALİZ PROTOKOLÜ:\n";
        $prompt .= "1. Risk Değerlendirmesi (ISO 45001 Madde 6.1.2):\n";
        $prompt .= "   - Mesleklere göre hastalık insidans oranı\n";
        $prompt .= "   - Ölümcül meslek hastalıkları için kritik risk haritalandırması\n";
        $prompt .= "   - Letalite oranı (vaka ölüm oranı) hesaplaması\n\n";

        $prompt .= "2. Kök Neden Analizi (ISO 45002:2016):\n";
        $prompt .= "   - Meslek gruplarına özgü hastalık etiyolojisi\n";
        $prompt .= "   - Ölümcül vakalar için ICD-10 sınıflandırması\n";
        $prompt .= "   - Maruziyet süresi ve ölüm ilişkisi\n\n";

        $prompt .= "3. Demografik Analiz (ILO C161 Uyumlu):\n";
        $prompt .= "   - Cinsiyet dağılımına göre hastalık prevalansı\n";
        $prompt .= "   - Mesleklere göre mortalite farklılıkları\n\n";

        $prompt .= "4. Önleme Planı (Hierarchy of Controls):\n";
        $prompt .= "   - Mesleklere özgü hijyen önlemleri (EN 689)\n";
        $prompt .= "   - Biyolojik ve kimyasal maruziyet kontrolleri\n";
        $prompt .= "   - Erken tanı ve tarama programları\n\n";

        $prompt .= "5. Epidemiyolojik Projeksiyon:\n";
        $prompt .= "   - Meslek gruplarına göre 5 yıllık hastalık trendi\n\n";

        $prompt .= "RAPOR FORMAT KURALLARI:\n";
        $prompt .= "- Tamamen ISO 45001 ve ILO terminolojisi kullan\n";
        $prompt .= "- Tıbbi tanımlamalarda ICD-10 kodlarını belirt\n";
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
        return "KANTİTATİF VERİLER (ISO 39001 Formatında):\n" .
            "- Toplam Meslek Hastalığı Vakası: " . ($summary['total_disease_cases'] ?? 0) . "\n" .
            "- Meslek Hastalığı Kaynaklı Ölümler: " . ($summary['total_fatalities'] ?? 0) . "\n" .
            "- Letalite Oranı: %" . ($summary['fatality_rate'] ?? 0) . "\n" .
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

    private static function formatOccupationCode($input): string
    {
        if (str_contains($input, ',')) {
            return "Çoklu Kod (" . $input . ")";
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
                        'content' => "Sen bir ISO 45001 Sertifikalı İş Sağlığı Uzmanı ve Meslek Hastalıkları Hekimisin. "
                            . "Tüm çıktıları aşağıdaki kurallara göre hazırla:\n"
                            . "1. Mesleki hastalık ve ölüm verilerine odaklan\n"
                            . "2. ISCO-08 meslek ve ICD-10 hastalık sınıflandırmasını kullan\n"
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
            return "ISO 45001 uyumlu mesleki hastalık analizi geçici olarak mevcut değil. Lütfen teknik ekibe bildiriniz.";
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
