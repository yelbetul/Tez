<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class AnalysisHelperAccidentsByMaterials
{
    /**
     * AI analiz promptunu oluşturur.
     *
     * @param Request $request
     * @param array $summary
     * @return string
     */
    public static function buildAIPrompt(Request $request, array $summary): string
    {
        $year = self::formatRequestInput($request->input('year'), 'Tüm yıllar');
        $groupCode = self::formatRequestInput($request->input('group_code'), 'Tüm materyal grupları');
        $subGroupCode = self::formatRequestInput($request->input('sub_group_code'), 'Tüm alt gruplar');
        $materialCode = self::formatRequestInput($request->input('material_code'), 'Tüm materyal türleri');

        $prompt = "Aşağıdaki materyal türlerine göre iş kazası ve ölüm verilerini ISO 45001 standartlarına uygun teknik raporda analiz et:\n\n";
        $prompt .= "VERİ PARAMETRELERİ (ISO 45003:2021 Formatında):\n";
        $prompt .= "- Analiz Periyodu: " . self::formatISODateRange($year) . "\n";
        $prompt .= "- Materyal Grubu: " . self::formatActivityCode($groupCode) . "\n";
        $prompt .= "- Alt Grup: " . self::formatActivityCode($subGroupCode) . "\n";
        $prompt .= "- Materyal Türü: " . self::formatActivityCode($materialCode) . "\n\n";

        $prompt .= self::generateQuantitativeDataSection($summary);

        $prompt .= "ANALİZ PROTOKOLÜ:\n";
        $prompt .= "1. Risk Değerlendirmesi (ISO 45001 Madde 6.1.2):\n";
        $prompt .= "   - Materyal türlerine göre kaza sıklık oranı\n";
        $prompt .= "   - Ölümlü kazalar için kritik risk haritalandırması (5x5 matris)\n";
        $prompt .= "   - İş göremezlik sürelerine göre materyal risk sıralaması\n\n";

        $prompt .= "2. Kök Neden Analizi (ISO 45002:2016):\n";
        $prompt .= "   - Materyal gruplarına özgü kaza nedenleri (Fishbone diyagramı)\n";
        $prompt .= "   - Ölümlü kazalar için ILO sınıflandırmasıyla analiz\n\n";

        $prompt .= "3. Demografik Analiz (ILO C161 Uyumlu):\n";
        $prompt .= "   - Materyal türlerine göre cinsiyet dağılımı\n";
        $prompt .= "   - Ölümlü kazalarda materyal risk faktörleri\n\n";

        $prompt .= "4. İyileştirme Planı (Hierarchy of Controls):\n";
        $prompt .= "   - Materyal türlerine özgü mühendislik kontrolleri (EN ISO 12100)\n";
        $prompt .= "   - Kritik materyal türleri için acil önlem paketleri\n\n";

        $prompt .= "5. Risk Projeksiyonu:\n";
        $prompt .= "   - Materyal gruplarına göre 3 yıllık kaza trend analizi\n\n";

        $prompt .= "RAPOR FORMAT KURALLARI:\n";
        $prompt .= "- Tamamen ISO 45001 terminolojisi kullan\n";
        $prompt .= "- Materyal kodlarını ILO standardına göre belirt\n";
        $prompt .= "- Yalnızca kanıta dayalı çıkarımlar yap\n";
        $prompt .= "- Kesinlikle meta veri içerme\n";
        $prompt .= "- Madde numaralandırması ISO belgeleriyle uyumlu olsun";

        return $prompt;
    }

    /**
     * AI'dan analiz yorumunu alır.
     *
     * @param string $prompt
     * @return string
     */
    public static function getAICommentary(string $prompt): string
    {
        try {
            $response = OpenAI::chat()->create([
                'model' => env('OPENAI_MODEL', 'gpt-4o'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "Sen bir ISO 45001 Sertifikalı İş Güvenliği Uzmanısın. "
                            . "Tüm çıktıları aşağıdaki kurallara göre hazırla:\n"
                            . "1. Materyal türlerine göre iş kazaları ve ölüm verilerine odaklan\n"
                            . "2. ILO materyal sınıflandırmasını kullan\n"
                            . "3. Yalnızca kanıta dayalı çıkarımlar yap\n"
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
            return "ISO 45001 uyumlu materyal analizi geçici olarak mevcut değil. Lütfen teknik ekibe bildiriniz.";
        }
    }

    /**
     * İstek girdisini biçimlendirir.
     *
     * @param mixed $input
     * @param string $default
     * @return string
     */
    private static function formatRequestInput($input, string $default = ''): string
    {
        if (is_array($input)) {
            return implode(', ', array_filter($input));
        }
        return $input ?? $default;
    }

    /**
     * ISO tarih aralığını biçimlendirir.
     *
     * @param string $input
     * @return string
     */
    private static function formatISODateRange(string $input): string
    {
        if (str_contains($input, ',')) {
            $years = array_map('trim', explode(',', $input));
            return "[" . min($years) . "-" . max($years) . "]";
        }
        return $input;
    }

    /**
     * Aktivite kodunu biçimlendirir.
     *
     * @param string $input
     * @return string
     */
    private static function formatActivityCode(string $input): string
    {
        if (str_contains($input, ',')) {
            return "Çoklu Kod (" . $input . ")";
        }
        return $input;
    }

    /**
     * Kantitatif veri bölümünü oluşturur.
     *
     * @param array $summary
     * @return string
     */
    private static function generateQuantitativeDataSection(array $summary): string
    {
        $fatalityRate = $summary['total_cases'] > 0
            ? round(($summary['total_fatalities'] / $summary['total_cases']) * 100, 2)
            : 0;

        return "KANTİTATİF VERİLER (ISO 39001 Formatında):\n" .
            "- Toplam Vaka: " . ($summary['total_cases'] ?? 0) . "\n" .
            "- İş Göremezlik Vakaları: " . ($summary['total_unfit'] ?? 0) . "\n" .
            "- Ölümlü Vakalar: " . ($summary['total_fatalities'] ?? 0) . " (%" . $fatalityRate . ")\n" .
            "- Cinsiyet Dağılımı:\n" .
            "  • Erkek: " . ($summary['male_count'] ?? 0) . " (%" . ($summary['male_percentage'] ?? 0) . ")\n" .
            "  • Kadın: " . ($summary['female_count'] ?? 0) . " (%" . ($summary['female_percentage'] ?? 0) . ")\n\n";
    }

    /**
     * ISO raporunu temizler.
     *
     * @param string $content
     * @return string
     */
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
