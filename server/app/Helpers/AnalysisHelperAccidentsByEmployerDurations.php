<?php

    namespace App\Helpers;

    use Illuminate\Support\Facades\Log;
    use OpenAI\Laravel\Facades\OpenAI;

    class AnalysisHelperAccidentsByEmployerDurations
    {
        public static function buildAIPrompt($request, array $summary): string
        {
            $year = self::formatRequestInput($request->input('year'), 'Tüm yıllar');
            $duration = self::formatRequestInput($request->input('employment_duration'), 'Tüm çalışma süreleri');
            $gender = self::formatRequestInput($request->input('gender'), 'Tüm cinsiyetler');

            $prompt = "Aşağıdaki işveren nezdindeki çalışma sürelerine göre iş kazası ve ölüm verilerini ISO 45001 standartlarına uygun teknik raporda analiz et:\n\n";
            $prompt .= "VERİ PARAMETRELERİ (ISO 45003:2021 Formatında):\n";
            $prompt .= "- Analiz Periyodu: ".self::formatISODateRange($year)."\n";
            $prompt .= "- Çalışma Süresi Grubu: ".self::formatEmploymentDuration($duration)."\n";
            $prompt .= "- Cinsiyet Filtresi: ".self::formatGender($gender)."\n\n";
            $prompt .= self::generateQuantitativeDataSection($summary);
            $prompt .= "ANALİZ PROTOKOLÜ:\n";
            $prompt .= "1. Risk Değerlendirmesi (ISO 45001 Madde 6.1.2):\n";
            $prompt .= "   - Çalışma sürelerine göre kaza sıklık oranı\n";
            $prompt .= "   - İş göremezlik sürelerine göre riskli işletme büyüklükleri\n\n";
            $prompt .= "2. Kök Neden Analizi (ISO 45002:2016):\n";
            $prompt .= "   - Çalışma süresi gruplarına özgü kaza nedenleri (Fishbone diyagramı)\n\n";
            $prompt .= "3. Demografik Analiz (ILO C161 Uyumlu):\n";
            $prompt .= "   - Çalışma süresi gruplarına göre cinsiyet dağılımı\n\n";
            $prompt .= "4. İyileştirme Planı (Hierarchy of Controls):\n";
            $prompt .= "   - Çalışma süresi gruplarına göre ölçeklenebilir önlemler (EN ISO 12100)\n";
            $prompt .= "   - İş sağlığı ve güvenliği organizasyonu\n\n";
            $prompt .= "5. Risk Projeksiyonu:\n";
            $prompt .= "   - Çalışma süresi gruplarına göre 3 yıllık kaza trend analizi\n\n";
            $prompt .= "RAPOR FORMAT KURALLARI:\n";
            $prompt .= "- Tamamen ISO 45001 terminolojisi kullan\n";
            $prompt .= "- Çalışma süresi gruplarını net belirt\n";
            $prompt .= "- Yalnızca kanıta dayalı çıkarımlar yap\n";
            $prompt .= "- Meta veri veya imza ekleme\n";
            $prompt .= "- Madde numaralandırması ISO belgeleriyle uyumlu olsun";

            return $prompt;
        }

        private static function formatRequestInput($input, string $default): string
        {
            if(is_array($input)){
                $filtered = array_filter($input, fn($v) => !empty($v));
                return implode(', ', $filtered) ? : $default;
            }
            return $input !== null && $input !== '' ? $input : $default;
        }

        private static function generateQuantitativeDataSection(array $s): string
        {
            return "KANTİTATİF VERİLER (ISO 39001 Formatında):\n"
                ."- Toplam Vaka: ".($s['total_cases'] ?? 0)."\n"
                ."- İş Göremezlik Vakaları: ".($s['total_unfit'] ?? 0)."\n"
                ."- Ölümlü Vakalar: ".($s['total_fatalities'] ?? 0)."\n"
                ."- Cinsiyet Dağılımı:\n"
                ."  • Erkek: ".($s['male_count'] ?? 0)." (%".($s['male_percentage'] ?? 0).")\n"
                ."  • Kadın: ".($s['female_count'] ?? 0)." (%".($s['female_percentage'] ?? 0).")\n\n";
        }

        private static function formatISODateRange(string $input): string
        {
            if(str_contains($input, ',')){
                $years = array_map('trim', explode(',', $input));
                return "[".min($years)."-".max($years)."]";
            }
            return $input;
        }

        private static function formatEmploymentDuration(string $input): string
        {
            if(str_contains($input, ',')){
                return "Çoklu Süre Grupları (".$input.")";
            }
            return $input;
        }

        private static function formatGender(string $input): string
        {
            return match ($input) {
                '0'     => 'Erkek',
                '1'     => 'Kadın',
                default => $input,
            };
        }

        public static function getAICommentary(string $prompt): string
        {
            try{
                $response = OpenAI::chat()->create([
                                                       'model' => env('OPENAI_MODEL', 'gpt-4o'),
                                                       'messages' => [
                                                           [
                                                               'role' => 'system',
                                                               'content' => "Sen bir ISO 45001 Sertifikalı İş Güvenliği Uzmanısın. "
                                                                   ."Çıktıda:\n"
                                                                   ."1. Çalışma süresi gruplarına odaklan\n"
                                                                   ."2. ILO sınıflandırmasını kullan\n"
                                                                   ."3. Kanıta dayalı çıkarım yap\n"
                                                                   ."4. ISO madde numaralandırması uygula\n"
                                                                   ."YAPMAYACAKLAR:\n"
                                                                   ."- İmza, tarih, hazırlayan bilgisi ekleme\n"
                                                                   ."- 'Rapor' veya 'Belge' başlıkları koyma\n"
                                                                   ."- Kişisel ifadeler kullanma",
                                                           ],
                                                           ['role' => 'user', 'content' => $prompt],
                                                       ],
                                                       'temperature' => 0.5,
                                                       'max_tokens' => 2000,
                                                   ]);

                return self::sanitizeISOReport($response->choices[0]->message->content);
            }catch(\Exception $e){
                Log::error('OpenAI Hatası: '.$e->getMessage());
                return "ISO 45001 uyumlu analiz geçici olarak mevcut değil. Lütfen teknik ekibe bildiriniz.";
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
