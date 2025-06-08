<?php

namespace App\Services;

class SentimentAnalysisService
{
    protected $positiveWords = [
        'добре', 'чудово', 'супер', 'класно', 'люблю', 'подобається', 'задоволений',
        'радий', 'щасливий', 'позитивний', 'гарний', 'красивий', 'якісний', 'професійний',
        'зручний', 'корисний', 'цікавий', 'захоплюючий', 'надихає', 'мотивує', 'розуміє',
        'підтримує', 'допомагає', 'вирішує', 'покращує', 'розвиває', 'навчає', 'надихає'
    ];

    protected $negativeWords = [
        'погано', 'жах', 'жахливо', 'неподобається', 'незадоволений', 'сумно',
        'негативний', 'поганий', 'неякісний', 'незручний', 'некорисний', 'нудний',
        'складний', 'важкий', 'проблемний', 'незрозумілий', 'неправильний', 'помилковий',
        'неприємний', 'дратує', 'заважає', 'шкодить', 'псує', 'погіршує', 'загальмовує'
    ];

    public function analyze(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $words = preg_split('/[\s,\.!?]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);

        $positiveCount = 0;
        $negativeCount = 0;

        foreach ($words as $word) {
            if (in_array($word, $this->positiveWords)) {
                $positiveCount++;
            }
            if (in_array($word, $this->negativeWords)) {
                $negativeCount++;
            }
        }

        if ($positiveCount > $negativeCount) {
            return 'positive';
        } elseif ($negativeCount > $positiveCount) {
            return 'negative';
        } else {
            return 'neutral';
        }
    }
} 