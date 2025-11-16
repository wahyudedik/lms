<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuestionBankTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'type',
            'difficulty',
            'question_text',
            'category',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'option_e',
            'correct_answer',
            'correct_answers',
            'pair1_left',
            'pair1_right',
            'pair2_left',
            'pair2_right',
            'pair3_left',
            'pair3_right',
            'default_points',
            'explanation',
            'tags',
            'image_url',
            'is_active',
            'is_verified',
        ];
    }

    public function array(): array
    {
        // Build 5 samples for each of the 4 types (20 rows total)
        $rows = [];

        // MCQ Single (5)
        $rows[] = [
                'mcq_single',
                'easy',
                'What is 2+2?',
                'Mathematics',
                '1',
                '2',
                '3',
                '4',
                '',
                '4',
                '',
                '', '', '', '', '', '',
                '1',
                'Simple addition',
                'math, basic',
                '',
                'yes',
                'yes',
        ];
        $rows[] = [
                'mcq_single','easy','Largest planet in our solar system?','Science',
                'Earth','Jupiter','Mars','Venus','',
                'Jupiter','',
                '', '', '', '', '', '',
                '1','—','science, planets','', 'yes','no',
        ];
        $rows[] = [
                'mcq_single','medium','Capital city of Japan?','Geography',
                'Kyoto','Tokyo','Osaka','Sapporo','',
                'Tokyo','',
                '', '', '', '', '', '',
                '1','—','asia, capital','', 'yes','no',
        ];
        $rows[] = [
                'mcq_single','medium','HTML stands for?','Technology',
                'Hyper Trainer Marking Language','Hyper Text Markup Language','Hyper Text Marketing Language','High Text Markup Language','',
                'Hyper Text Markup Language','',
                '', '', '', '', '', '',
                '1','—','web, html','', 'yes','no',
        ];
        $rows[] = [
                'mcq_single','hard','Speed of light (approx) in km/s?','Physics',
                '3,000','30,000','300,000','3,000,000','',
                '300,000','',
                '', '', '', '', '', '',
                '1','—','physics, constants','', 'yes','no',
        ];

        // MCQ Multiple (5)
        $rows[] = [
                'mcq_multiple',
                'medium',
                'Select prime numbers',
                'Mathematics',
                '1',
                '2',
                '3',
                '4',
                '5',
                '',
                '2,3,5',
                '2',
                'Prime numbers are divisible by 1 and themselves',
                'math, prime',
                '',
                'yes',
                'no',
        ];
        $rows[] = [
                'mcq_multiple','easy','Select even numbers','Mathematics',
                '1','2','3','4','5',
                '','2,4',
                '1','—','math, even','', 'yes','no',
        ];
        $rows[] = [
                'mcq_multiple','medium','Select colors that are primary colors','Art',
                'Red','Green','Blue','Yellow','Black',
                '','Red,Blue,Yellow',
                '2','—','art, colors','', 'yes','no',
        ];
        $rows[] = [
                'mcq_multiple','medium','Select continents','Geography',
                'Asia','Europe','Africa','Brazil','Oceania',
                '','Asia,Europe,Africa,Oceania',
                '1','—','geography, continents','', 'yes','no',
        ];
        $rows[] = [
                'mcq_multiple','hard','Select fruits','Biology',
                'Tomato','Apple','Cucumber','Banana','Potato',
                '','Tomato,Apple,Banana',
                '1','—','fruits','', 'yes','no',
        ];

        // Matching (5)
        $rows[] = [
                'matching','easy','Match country to capital','Geography','', '', '', '', '', '', '',
                'Indonesia','Jakarta','Japan','Tokyo','France','Paris',
                '1','—','matching, capital','', 'yes','no',
        ];
        $rows[] = [
                'matching','medium','Match animal to class','Biology','', '', '', '', '', '', '',
                'Frog','Amphibian','Snake','Reptile','Cat','Mammal',
                '2','—','biology, animals','', 'yes','no',
        ];
        $rows[] = [
                'matching','medium','Match language to country','Language','', '', '', '', '', '', '',
                'Spanish','Spain','Japanese','Japan','Arabic','Saudi Arabia',
                '1','—','language','', 'yes','no',
        ];
        $rows[] = [
                'matching','hard','Match symbol to field','General Knowledge','', '', '', '', '', '', '',
                'π','Mathematics','H2O','Chemistry','CPU','Computing',
                '1','—','symbols','', 'yes','no',
        ];
        $rows[] = [
                'matching','easy','Match color to example object','Art','', '', '', '', '', '', '',
                'Green','Leaf','Blue','Sky','Yellow','Banana',
                '1','—','colors','', 'yes','no',
        ];

        // Essay (5)
        $rows[] = [
                'essay',
                'hard',
                'Explain photosynthesis',
                'Biology',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '5',
                'Process by which plants make food',
                'biology, science',
                '',
                'yes',
                'yes',
        ];
        $rows[] = ['essay','easy','Describe your favorite hobby','General','','','','','','','','','1','—','personal','','yes','no'];
        $rows[] = ['essay','medium','Why is data privacy important?','Technology','','','','','','','','','3','—','privacy, data','','yes','no'];
        $rows[] = ['essay','medium','How do volcanoes form?','Geography','','','','','','','','','3','—','volcano','','yes','no'];
        $rows[] = ['essay','hard','Discuss impacts of globalization','Economy','','','','','','','','','5','—','globalization','','yes','no'];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:W1')->getFont()->setBold(true);
        $sheet->getStyle('A1:W1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE3F2FD');
        $sheet->freezePane('A2');
        $sheet->getStyle('A1:W100')->getAlignment()->setWrapText(true);

        return [];
    }
}

