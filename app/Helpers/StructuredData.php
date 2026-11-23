<?php

namespace App\Helpers;

class StructuredData
{
    public $structuredData = [];

    /**
     * $data = [
     *  'name' => 'url',
     *  'name' => 'url',
     * ]
     */
    public function breadcrumb($data = [], $removeHome = false)
    {
        $result = [];
        $result['@context'] = 'https://schema.org';
        $result['@type'] = 'BreadcrumbList';
        $result['itemListElement'] = [];

        $counter = 1;
        foreach ($data as $name => $item) {
            if ($removeHome && $name == __('Home')) {
                continue;
            }

            $itemListElement = [];
            $itemListElement['@type'] = 'ListItem';
            $itemListElement['position'] = $counter;
            $itemListElement['name'] = htmlspecialchars($name);

            if (!empty(trim($item))) {
                $itemListElement['item'] = $item;
            }

            $result['itemListElement'][] = $itemListElement;

            $counter++;
        }

        $this->structuredData[] = $result;
    }

    /**
     * $data = [
     *  'questions' => [
     *      ['question' => '', 'answer' => ''],
     *      ['question' => '', 'answer' => ''],
     *  ], // Required
     *  'title' => '', // Optional
     *  'subject' => '', // Optional
     *  'level' => '', // Optional
     * ]
     */
    public function educationQA($data = [])
    {
        $result = [];
        $result['@context'] = 'https://schema.org';
        $result['@type'] = 'Quiz';

        if (!empty($data['title'])) {
            $result['about'] = [
                '@type' => 'Thing',
                'name' => $data['title'],
            ];
        }

        if (!empty($data['subject']) || !empty($data['level'])) {
            $result['educationalAlignment'] = [];

            if (!empty($data['subject'])) {
                $result['educationalAlignment'][] = [
                    '@type' => 'AlignmentObject',
                    'alignmentType' => 'educationalSubject',
                    'targetName' => $data['subject'],
                ];
            }

            if (!empty($data['level'])) {
                $result['educationalAlignment'][] = [
                    '@type' => 'AlignmentObject',
                    'alignmentType' => 'educationalLevel',
                    'targetName' => $data['level'],
                ];
            }
        }

        $result['hasPart'] = [];

        foreach ($data['questions'] as $question) {
            $result['hasPart'][] = [
                '@context' => 'https://schema.org',
                '@type' => 'Question',
                'eduQuestionType' => 'Flashcard',
                'text' => $question['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $question['answer'],
                ],
            ];
        }

        $this->structuredData[] = $result;
    }

    /**
     * $data = [
     *  'questionShort' => '', // Required
     *  'questionFull' => '', // Optional
     *  'answerCount' => 0, // Required
     *  'upvoteCount' => 0, // Optional
     *  'datePublished' => '', // Optional
     *  'author' => [
     *      'name' => '', // Optional
     *      'url' => '', // Optional
     *  ], // Optional
     *  'answers' => [
     *      [
     *          'text' => '', // Required
     *          'upvoteCount' => 0, // Optional
     *          'url' => '', // Optional
     *          'datePublished' => '', // Optional
     *          'author' => [
     *              'name' => '', // Optional
     *              'url' => '', // Optional
     *          ], // Optional
     *          'isTop' => false, // Optional
     *      ],
     *  ], // Required
     * ]
     */
    public function QA($data = [])
    {
        $result = [];
        $result['@context'] = 'https://schema.org';
        $result['@type'] = 'QAPage';

        $mainEntity = [];
        $mainEntity['@type'] = 'Question';
        $mainEntity['name'] = htmlspecialchars($data['questionShort']);

        if (!empty($data['questionFull'])) {
            $mainEntity['text'] = htmlspecialchars($data['questionFull']);
        }

        $mainEntity['answerCount'] = $data['answerCount'];

        if (isset($data['upvoteCount'])) {
            $mainEntity['upvoteCount'] = $data['upvoteCount'];
        }

        if (isset($data['datePublished'])) {
            $mainEntity['datePublished'] = date('c', $data['datePublished']);
        }

        if (isset($data['author']['name']) && isset($data['author']['url'])) {
            $mainEntity['author'] = [
                '@type' => 'Person',
                'name' => $data['author']['name'],
                'url' => $data['author']['url'],
            ];
        }

        $acceptedAnswer = false;
        $suggestedAnswers = [];

        foreach ($data['answers'] as $answer) {
            if ($acceptedAnswer == false && isset($answer['isTop']) && $answer['isTop']) {
                $acceptedAnswer['@type'] = 'Answer';
                $acceptedAnswer['text'] = htmlspecialchars($answer['text']);

                if (isset($answer['upvoteCount'])) {
                    $acceptedAnswer['upvoteCount'] = $answer['upvoteCount'];
                }

                if (!empty($answer['url'])) {
                    $acceptedAnswer['url'] = $answer['url'];
                }

                if (isset($answer['datePublished'])) {
                    $acceptedAnswer['datePublished'] = date('c', $answer['datePublished']);
                }

                if (isset($answer['author']['name']) && isset($answer['author']['url'])) {
                    $acceptedAnswer['author'] = [
                        '@type' => 'Person',
                        'name' => $answer['author']['name'],
                        'url' => $answer['author']['url'],
                    ];
                }

                $mainEntity['acceptedAnswer'] = $acceptedAnswer;
            }
            else {
                $answerData = [];
                $answerData['@type'] = 'Answer';
                $answerData['text'] = htmlspecialchars($answer['text']);

                if (isset($answer['upvoteCount'])) {
                    $answerData['upvoteCount'] = $answer['upvoteCount'];
                }

                if (!empty($answer['url'])) {
                    $answerData['url'] = $answer['url'];
                }

                if (isset($answer['datePublished'])) {
                    $answerData['datePublished'] = date('c', $answer['datePublished']);
                }

                if (isset($answer['author']['name']) && isset($answer['author']['url'])) {
                    $answerData['author'] = [
                        '@type' => 'Person',
                        'name' => $answer['author']['name'],
                        'url' => $answer['author']['url'],
                    ];
                }

                $suggestedAnswers[] = $answerData;
            }
        }

        $mainEntity['suggestedAnswer'] = $suggestedAnswers;
        
        $result['mainEntity'] = $mainEntity;

        $this->structuredData[] = $result;
    }

    public function render()
    {
        if (count($this->structuredData) > 0) {
            return '<script type="application/ld+json">' . json_encode($this->structuredData, JSON_UNESCAPED_SLASHES) . '</script>';
        }
    }
}