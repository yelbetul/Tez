<?php

    namespace App\Helpers;

    use OpenAI\Laravel\Facades\OpenAI;
    use Illuminate\Support\Facades\Log;

    class AnalysisHelperOccDiseaseByEmployees
    {
        public static function buildAIPrompt($request, $summary)
        {
            $year = self::formatRequestInput($request->input('year'), 'Tüm yıllar');
            $employeeGroup = self::formatRequestInput($request->input('employee_count'), 'Tüm çalışan grupları');
            $gender = self::formatGenderInput($request->input('gender'));

            $prompt = "Aşağıdaki çalışan gruplarına göre meslek hastalığı verilerini ISO 45001 standartlarına uygun teknik raporda analiz et:\n\n";
            $prompt .= "VERİ PARAMETRELERİ (ISO 45003:2021 Formatında):\n";
            $prompt .= "- Analiz Periyodu: ".self::formatISODateRange($year)."\n";
            $prompt .= "- Çalışan Grubu: ".$employeeGroup."\n";
            $prompt .= "- Cinsiyet Filtresi: ".$gender."\n\n";

            $prompt .= self::generateQuantitativeDataSection($summary);

            $prompt .= "ANALİZ PROTOKOLÜ:\n";
            $prompt .= "1. Risk Değerlendirmesi (ISO 45001 Madde 6.1.2):\n";
            $prompt .= "   - Çalışan gruplarına göre hastalık insidans oranı\n";
            $prompt .= "   - Ölümlü vakaların grup bazlı dağılımı\n";
            $prompt .= "   - Kritik Risk Matrisi (5x5 L matrisi)\n\n";

            $prompt .= "2. Kök Neden Analizi (ISO 45002:2016):\n";
            $prompt .= "   - Meslek hastalıkları için Pareto analizi\n";
            $prompt .= "   - Grup ve cinsiyet bazlı korelasyon analizi\n\n";

            $prompt .= "3. Demografik Analiz (ILO C161 Uyumlu):\n";
            $prompt .= "   - Cinsiyet bazlı hastalık/ölüm oranları\n";
            $prompt .= "   - Grup büyüklüğüne göre morbidite/mortalite\n\n";

            $prompt .= "4. Önleme Stratejileri (Hierarchy of Controls):\n";
            $prompt .= "   - Grup bazlı ergonomi iyileştirmeleri (EN ISO 6385)\n";
            $prompt .= "   - Yüksek riskli gruplar için PDCA planları\n\n";

            $prompt .= "RAPOR FORMAT KURALLARI:\n";
            $prompt .= "- Sadece ISO 45001 terminolojisi kullan\n";
            $prompt .= "- Sayısal verileri mutlaka yüzdeliklerle destekle\n";
            $prompt .= "- Kesinlikle tablo veya görsel önerisi içerme\n";

            return $prompt;
        }

        private static function generateQuantitativeDataSection(array $summary): string
        {
            return "KANTİTATİF VERİLER (ISO 39001 Formatında):\n".
                "- Toplam Meslek Hastalığı: ".($summary['total_cases'] ?? 0)."\n".
                "- Toplam Ölüm Vakası: ".($summary['total_fatalities'] ?? 0)."\n".
                "- Cinsiyet Dağılımı:\n".
                "  • Erkek: ".($summary['male_count'] ?? 0)." (%".($summary['male_percentage'] ?? 0).")\n".
                "  • Kadın: ".($summary['female_count'] ?? 0)." (%".($summary['female_percentage'] ?? 0).")\n\n";
        }

        private static function formatGenderInput($input): string
        {
            return match ($input) {
                '0'     => 'Erkek',
                '1'     => 'Kadın',
                default => 'Tümü'
            };
        }

        private static function formatRequestInput($input, $default = '')
        {
            if(is_array($input)){
                return implode(', ', array_filter($input));
            }
            return $input ?? $default;
        }

        public static function getAICommentary($prompt)
        {
            try{
                $response = OpenAI::chat()->create([
                                                       'model' => env('OPENAI_MODEL', 'gpt-4'),
                                                       'messages' => [
                                                           [
                                                               'role' => 'system',
                                                               'content' => "Sen bir ISO 45001 Sertifikalı İş Sağlığı Uzmanısın. "
                                                                   ."Analizleri sadece aşağıdaki verilere dayandır:\n"
                                                                   ."1. Çalışan grupları (employee_groups.code)\n"
                                                                   ."2. Meslek hastalığı ve ölüm istatistikleri\n"
                                                                   ."3. Cinsiyet bazlı dağılım\n"
                                                                   ."YASAKLAR:\n"
                                                                   ."- Hipotez veya tahmin içeren ifadeler\n"
                                                                   ."- Rapora ait meta veriler\n"
                                                                   ."- Kaynak gösterme (ISO standartları hariç)",
                                                           ],
                                                           ['role' => 'user', 'content' => $prompt],
                                                       ],
                                                       'temperature' => 0.7,
                                                       'max_tokens' => 1500,
                                                   ]);

                return self::sanitizeISOReport($response->choices[0]->message->content);

            }catch(\Exception $e){
                Log::error('OpenAI Hatası: '.$e->getMessage());
                return "Meslek hastalığı analizi şu anda mevcut değil. Lütfen daha sonra tekrar deneyiniz.";
            }
        }
        private static function formatISODateRange($input): string
        {
            if(str_contains($input, ',')){
                $years = array_map('trim', explode(',', $input));
                return "[".min($years)."-".max($years)."]";
            }
            return $input;
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
