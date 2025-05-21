<?php

    namespace App\Helpers;

    use Illuminate\Support\Facades\Log;
    use OpenAI\Laravel\Facades\OpenAI;
    class AnalysisHelperAccidentsByEnvironments
    {
        public static function buildAIPrompt($request, $summary)
        {
            $year = self::formatRequestInput($request->input('year'), 'Tüm yıllar');
            $groupCode = self::formatRequestInput($request->input('group_code'), 'Tüm çevre grupları');
            $subGroupCode = self::formatRequestInput($request->input('sub_group_code'), 'Tüm alt gruplar');
            $environmentCode = self::formatRequestInput($request->input('environment_code'), 'Tüm çevre türleri');

            $prompt = "Aşağıdaki çalışma çevrelerine göre iş kazası ve ölüm verilerini ISO 45001 standartlarına uygun teknik raporda analiz et:\n\n";
            $prompt .= "VERİ PARAMETRELERİ (ISO 45003:2021 Formatında):\n";
            $prompt .= "- Analiz Periyodu: ".self::formatISODateRange($year)."\n";
            $prompt .= "- Çevre Grubu: ".self::formatActivityCode($groupCode)."\n";
            $prompt .= "- Alt Grup: ".self::formatActivityCode($subGroupCode)."\n";
            $prompt .= "- Çevre Türü: ".self::formatActivityCode($environmentCode)."\n\n";

            $prompt .= self::generateQuantitativeDataSection($summary);

            $prompt .= "ANALİZ PROTOKOLÜ:\n";
            $prompt .= "1. Risk Değerlendirmesi (ISO 45001 Madde 6.1.2):\n";
            $prompt .= "   - Çalışma çevrelerine göre kaza sıklık oranı\n";
            $prompt .= "   - Ölümlü kazalar için kritik risk haritalandırması (5x5 matris)\n";
            $prompt .= "   - İş göremezlik sürelerine göre çevresel risk sıralaması\n\n";

            $prompt .= "2. Kök Neden Analizi (ISO 45002:2016):\n";
            $prompt .= "   - Çevre gruplarına özgü kaza nedenleri (Fishbone diyagramı)\n";
            $prompt .= "   - Ölümlü kazalar için ILO sınıflandırmasıyla analiz\n\n";

            $prompt .= "3. Demografik Analiz (ILO C161 Uyumlu):\n";
            $prompt .= "   - Çalışma çevrelerine göre cinsiyet dağılımı\n";
            $prompt .= "   - Ölümlü kazalarda çevresel risk faktörleri\n\n";

            $prompt .= "4. İyileştirme Planı (Hierarchy of Controls):\n";
            $prompt .= "   - Çevresel risklere özgü mühendislik kontrolleri (EN ISO 12100)\n";
            $prompt .= "   - Kritik çalışma çevreleri için acil önlem paketleri\n\n";

            $prompt .= "5. Risk Projeksiyonu:\n";
            $prompt .= "   - Çevre gruplarına göre 3 yıllık kaza trend analizi\n\n";

            $prompt .= "RAPOR FORMAT KURALLARI:\n";
            $prompt .= "- Tamamen ISO 45001 terminolojisi kullan\n";
            $prompt .= "- Çevre kodlarını ILO standardına göre belirt\n";
            $prompt .= "- Yalnızca kanıta dayalı çıkarımlar yap\n";
            $prompt .= "- Kesinlikle meta veri içerme\n";
            $prompt .= "- Madde numaralandırması ISO belgeleriyle uyumlu olsun";

            return $prompt;
        }

        private static function formatRequestInput($input, $default = '')
        {
            if(is_array($input)){
                return implode(', ', array_filter($input));
            }
            return $input ?? $default;
        }

        private static function generateQuantitativeDataSection(array $summary): string
        {
            $fatalityRate = $summary['total_cases'] > 0
                ? round(($summary['total_fatalities'] / $summary['total_cases']) * 100, 2)
                : 0;

            return "KANTİTATİF VERİLER (ISO 39001 Formatında):\n".
                "- Toplam Vaka: ".($summary['total_cases'] ?? 0)."\n".
                "- İş Göremezlik Vakaları: ".($summary['total_unfit'] ?? 0)."\n".
                "- Ölümlü Vakalar: ".($summary['total_fatalities'] ?? 0)." (%".$fatalityRate.")\n".
                "- Cinsiyet Dağılımı:\n".
                "  • Erkek: ".($summary['male_count'] ?? 0)." (%".($summary['male_percentage'] ?? 0).")\n".
                "  • Kadın: ".($summary['female_count'] ?? 0)." (%".($summary['female_percentage'] ?? 0).")\n\n";
        }

        private static function formatISODateRange($input): string
        {
            if(str_contains($input, ',')){
                $years = array_map('trim', explode(',', $input));
                return "[".min($years)."-".max($years)."]";
            }
            return $input;
        }

        private static function formatActivityCode($input): string
        {
            if(str_contains($input, ',')){
                return "Çoklu Kod (".$input.")";
            }
            return $input;
        }

        public static function getAICommentary($prompt)
        {
            try{
                $response = OpenAI::chat()->create([
                                                       'model' => env('OPENAI_MODEL', 'gpt-4o'),
                                                       'messages' => [
                                                           [
                                                               'role' => 'system',
                                                               'content' => "Sen bir ISO 45001 Sertifikalı İş Güvenliği Uzmanısın. "
                                                                   ."Tüm çıktıları aşağıdaki kurallara göre hazırla:\n"
                                                                   ."1. Çalışma çevrelerine göre iş kazaları ve ölüm verilerine odaklan\n"
                                                                   ."2. ILO çevresel risk sınıflandırmasını kullan\n"
                                                                   ."3. Yalnızca kanıta dayalı çıkarımlar yap\n"
                                                                   ."4. Madde numaralandırması ISO belge formatında olsun\n"
                                                                   ."KESİNLİKLE YAPMAYACAKLARIN:\n"
                                                                   ."1. İmza, tarih, hazırlayan bilgisi EKLEME\n"
                                                                   ."2. 'Rapor', 'Belge' gibi başlıklar KOYMA\n"
                                                                   ."3. Kişisel ifadeler (ör: 'tavsiye ederim') KULLANMA",
                                                           ],
                                                           ['role' => 'user', 'content' => $prompt],
                                                       ],
                                                       'temperature' => 0.5,
                                                       'max_tokens' => 2000,
                                                   ]);

                return self::sanitizeISOReport($response->choices[0]->message->content);

            }catch(\Exception $e){
                Log::error('OpenAI Hatası: '.$e->getMessage());
                return "ISO 45001 uyumlu çevresel risk analizi geçici olarak mevcut değil. Lütfen teknik ekibe bildiriniz.";
            }
        }

        private static function sanitizeISOReport(string $content): string
        {
            $replacements = [
                '/\b(Tarih|Date):.*$/mi' => '',
                '/\b(Rapor No|Document Number):.*$/mi' => '',
                '/(?<!\w)\.(?!\d)/' => '.',
            ];

            return preg_replace(array_keys($replacements), array_values($replacements), $content);
        }
    }
