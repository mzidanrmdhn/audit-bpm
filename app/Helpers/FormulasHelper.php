<?php

namespace App\Helpers;

class FormulasHelper
{
    // Mengelompokkan pertanyaan berdasarkan formula yg sama
    public $formulas = [
        'formula1' => [
            'questions' => [
                'A.1',
                'B.1',
                'IBIK-STD-01.1',
                'IBIK-STD-01.2',
                'IBIK-STD-01.3',
                'IBIK-STD-02.3',
                'IBIK-STD-02.5',
                'IBIK-STD-02.6',
                'IBIK-STD-02.7',
                'IBIK-STD-02.8',
                'IBIK-STD-05.5',
                'IBIK-STD-05.6',
                'IBIK-STD-06.2',
                'IBIK-STD-06.6',
                'IBIK-STD-06.9',
                'IBIK-STD-07.1',
                'IBIK-STD-08.1',
                'IBIK-STD-09.1',
                'IBIK-STD-09.8',
                'D.1',
                'D.2',
                'D.3',
                'D.4',
                'IBIK-STD-05.4'
            ]
        ],

        'formula2' => [
            'questions' => [
                ['IBIK-STD-02.1A', 'IBIK-STD-02.1B'],
                ['IBIK-STD-02.2A', 'IBIK-STD-02.2B'],
                ['IBIK-STD-03.3A', 'IBIK-STD-03.3B'],
                ['IBIK-STD-06.3A', 'IBIK-STD-06.3B'],
            ],
        ],

        'formula3' => [
            'questions' => [
                'IBIK-STD-02.4A',
                'IBIK-STD-02.4B',
            ],
        ],

        'formula4' => [
            'questions' => [
                'IBIK-STD-03.1'
            ]
        ],

        'formula5' => [
            'questions' => [
                [
                    'IBIK-STD-03.2A',
                    'IBIK-STD-03.2B'
                ]
            ]
        ],

        'formula6' => [
            'questions' => ['IBIK-STD-04.1']
        ],

        'formula7' => [
            'questions' => [
                'IBIK-STD-04.2'
            ]
        ],

        'formula8' => [
            'questions' => [
                'IBIK-STD-04.3'
            ]
        ],

        // ubah
        'formula9' => [
            'questions' => [
                'IBIK-STD-04.4'
            ]
        ],

        // ubah
        'formula10' => [
            'questions' => ['IBIK-STD-04.5']
        ],

        // ubah
        'formula11' => [
            'questions' => ['IBIK-STD-04.6']
        ],

        // ubah
        'formula12' => [
            'questions' => [
                'IBIK-STD-04.7'
            ]
        ],

        // ubah
        'formula13' => [
            'questions' => [
                'IBIK-STD-04.8'
            ]
        ],

        // ubah
        'formula14' => [
            'questions' => [
                [ 'IBIK-STD-04.9' ],
                [ 'IBIK-STD-04.10' ],
            ]
        ],

        'formula15' => [
            'questions' => ['IBIK-STD-04.11']
        ],

        'formula16' => [
            'questions' => ['IBIK-STD-04.12']
        ],

        'formula17' => [
            'questions' => ['IBIK-STD-04.13']
        ],

        'formula18' => [
            'questions' => ['IBIK-STD-04.14']
        ],

        'formula19' => [
            'questions' => [
                ['IBIK-STD-04.15A', 'IBIK-STD-04.15B']
            ],
        ],

        'formula20' => [
            'questions' => ['IBIK-STD-05.1']
        ],

        'formula21' => [
            'questions' => ['IBIK-STD-05.2']
        ],

        'formula22' => [
            'questions' => ['IBIK-STD-05.3']
        ],

        // 'formula23' => [
            // 'questions' => ['IBIK-STD-05.4']
        // ],

        'formula24' => [
            'questions' => [
                ['IBIK-STD-06.1A', 'IBIK-STD-06.1B', 'IBIK-STD-06.1C'],
                ['IBIK-STD-06.7A', 'IBIK-STD-06.7B', 'IBIK-STD-06.7C']
            ],
        ],

        'formula25' => [
            'questions' => [
                ['IBIK-STD-06.4A', 'IBIK-STD-06.4B', 'IBIK-STD-06.4C', 'IBIK-STD-06.4D', 'IBIK-STD-06.4E']
            ],
        ],

        'formula26' => [
            'questions' => ['IBIK-STD-06.5']
        ],

        'formula27' => [
            'questions' => ['IBIK-STD-06.8']
        ],

        'formula28' => [
            'questions' => [
                ['IBIK-STD-06.10A', 'IBIK-STD-06.10B']
            ],
        ],

        'formula29' => [
            'questions' => ['IBIK-STD-07.2']
        ],

        'formula30' => [
            'questions' => ['IBIK-STD-08.2']
        ],

        'formula31' => [
            'questions' => ['IBIK-STD-09.2']
        ],

        'formula32' => [
            'questions' => ['IBIK-STD-09.3']
        ],

        'formula33' => [
            'questions' => ['IBIK-STD-09.4']
        ],

        'formula34' => [
            'questions' => ['IBIK-STD-09.5']
        ],

        'formula35' => [
            'questions' => ['IBIK-STD-09.6']
        ],

        'formula36' => [
            'questions' => ['IBIK-STD-09.7']
        ],

        'formula37' => [
            'questions' => ['IBIK-STD-09.9']
        ],

        'formula38' => [
            'questions' => ['IBIK-STD-09.10']
        ],

        'formula39' => [
            'questions' => ['IBIK-STD-09.11']
        ],

        'formula40' => [
            'questions' => ['IBIK-STD-09.12']
        ],

        'formula41' => [
            'questions' => ['IBIK-STD-09.13']
        ],

        'formula42' => [
            'questions' => ['IBIK-STD-09.14']
        ],
    ];

    public function getFormula()
    {
        return collect($this->formulas)->map(function($formula) {
            return $formula['questions'];
        });
    }


}
