<?php

$vAttributes = include base_path(). '/Modules/Isite/Config/standardValuesForBlocksAttributes.php';

return [
 "TimeLine" => [
  "title" => "Linea de Tiempo",
  "systemName" => "iblog::TimeLine",
  "nameSpace" => "Modules\Iblog\View\Components\Timeline",
  "content" => [
   [
    "label" => "Iblog::Post",
    "value" => "Modules\Iblog\Repositories\PostRepository"
   ],
  ],
   "contentFields" => [
     "title" => [
       "name" => "title",
       "type" => "input",
       "columns" => "col-12",
       "isTranslatable" => true,
       "props" => [
         "label" => "Titulo",
       ]
     ],
     "subtitle" => [
       "name" => "subtitle",
       "type" => "html",
       "columns" => "col-12",
       "isTranslatable" => true,
       "props" => [
         "label" => "Subtitulo",
       ]
     ],
   ],
  "childBlocks" => [
   "itemComponentAttributes" => "isite::item-list",
  ],
  "attributes" => [
    "Titulos" => [
      "title" => "Texto (Titulo y Subtitulo)",
      "fields" => [
        "textPosition" => [
          "name" => "textPosition",
          "value" => "2",
          "type" => "select",
          "columns" => "col-12",
          "props" => [
            "label" => "Posición",
            "options" => [
              ["label" => "Solo título", "value" => "1"],
              ["label" => "Título con descripción abajo", "value" => "2"],
              ["label" => "Título abajo con descripción arriba", "value" => "3"]
            ]
          ]
        ],
        "textAlign" => [
          "name" => "textAlign",
          "value" => "text-left",
          "type" => "select",
          "props" => [
            "label" => "Alineación",
            "options" => $vAttributes["align"]
          ]
        ],
        "titleSize" => [
          "name" => "titleSize",
          "type" => "input",
          "props" => [
            "label" => "Tamaño Fuente (Titulo)",
            "type" => "number"
          ]
        ],
        "titleTransform" => [
          "name" => "titleTransform",
          "type" => "select",
          "props" => [
            "label" => "Transformar (Titulo)",
            "options" => $vAttributes["textTransform"]
          ]
        ],
        "titleColor" => [
          "name" => "titleColor",
          "type" => "select",
          "props" => [
            "label" => "Color (Titulo)",
            "options" => $vAttributes["textColors"]
          ]
        ],
        "titleMarginT" => [
          "name" => "titleMarginT",
          "value" => "mt-0",
          "type" => "select",
          "props" => [
            "label" => "Margen superior (Titulo)",
            "options" => $vAttributes["marginT"]
          ]
        ],
        "titleMarginB" => [
          "name" => "titleMarginB",
          "value" => "mb-0",
          "type" => "select",
          "props" => [
            "label" => "Margen inferior (Titulo)",
            "options" => $vAttributes["marginB"]
          ]
        ],
        "titleWeight" => [
          "name" => "titleWeight",
          "value" => "font-weight-normal",
          "type" => "select",
          "props" => [
            "label" => "Negrita (Titulo)",
            "options" => $vAttributes["textWeight"]
          ]
        ],
        "titleLetterSpacing" => [
          "name" => "titleLetterSpacing",
          "type" => "input",
          "props" => [
            "label" => "Espacio entre letras (Titulo)",
            "type" => "number"
          ]
        ],
        "titleVineta" => [
          "name" => "titleVineta",
          "type" => "input",
          "props" => [
            "label" => "Icon (Titulo)"
          ]
        ],
        "titleVinetaColor" => [
          "name" => "titleVinetaColor",
          "type" => "select",
          "props" => [
            "label" => "Color icon (Titulo)",
            "options" => $vAttributes["textColors"]
          ]
        ],
        "titleClasses" => [
          "name" => "titleClasses",
          "value" => "",
          "type" => "input",
          "columns" => "col-12",
          "props" => [
            "label" => "Clases (Titulo)",
          ]
        ],
        "subtitleSize" => [
          "name" => "subtitleSize",
          "type" => "input",
          "props" => [
            "label" => "Tamaño Fuente (Subtitulo)",
            "type" => "number"
          ]
        ],
        "subtitleColor" => [
          "name" => "subtitleColor",
          "type" => "select",
          "props" => [
            "label" => "Color (Subtitulo)",
            "options" => $vAttributes["textColors"]
          ]
        ],
        "subtitleMarginT" => [
          "name" => "subtitleMarginT",
          "value" => "mt-0",
          "type" => "select",
          "props" => [
            "label" => "Margen superior (Subtitulo)",
            "options" => $vAttributes["marginT"]
          ]
        ],
        "subtitleMarginB" => [
          "name" => "subtitleMarginB",
          "value" => "mb-0",
          "type" => "select",
          "props" => [
            "label" => "Margen inferior (Subtitulo)",
            "options" => $vAttributes["marginB"]
          ]
        ],
        "subtitleTransform" => [
          "name" => "subtitleTransform",
          "type" => "select",
          "props" => [
            "label" => "Transformar (Subtitulo)",
            "options" => $vAttributes["textTransform"]
          ]
        ],
        "subtitleWeight" => [
          "name" => "subtitleWeight",
          "type" => "select",
          "props" => [
            "label" => "Negrita (Subtitulo)",
            "options" => $vAttributes["textWeight"]
          ]
        ],
        "subtitleLetterSpacing" => [
          "name" => "subtitleLetterSpacing",
          "type" => "input",
          "props" => [
            "label" => "Espacio entre letras (Subtitulo)",
            "type" => "number"
          ]
        ],
        "subtitleClasses" => [
          "name" => "subtitleClasses",
          "value" => "",
          "type" => "input",
          "columns" => "col-12",
          "props" => [
            "label" => "Clases (Subtitulo)",
          ]
        ],
        "withLineTitle" => [
          "name" => "withLineTitle",
          "value" => "0",
          "type" => "select",
          "columns" => "col-12",
          "props" => [
            "label" => "Linea",
            "options" => $vAttributes["titleLine"]
          ]
        ],
        "lineTitleConfig" => [
          "name" => "lineTitleConfig",
          "value" => ['background' => 'var(--primary)','height' => '2px','width' => '10%','margin' => '0 auto'],
          "type" => "json",
          "columns" => "col-12",
          "props" => [
            "label" => "Configuración de Línea",
          ]
        ],
      ]
    ],
   "general" => [
    "title" => "General",
    "fields" => [
     "layout" => [
      "name" => "layout",
      "value" => "timeline-layout-3",
      "type" => "select",
      "props" => [
       "label" => "Layout",
       "options" => [
        [ "label" => "timeline-layout-3", "value" => "timeline-layout-3" ],
        [ "label" => "timeline-layout-2", "value" => "timeline-layout-2" ],
        [ "label" => "timeline-layout-1", "value" => "timeline-layout-1" ],
       ]
      ]
     ],
     "mainLineColor" => [
      "name" => "mainLineColor",
      "type" => "inputColor",
      "props" => [
       "label" => "Color de la linea"
      ],
     ],
     "mainLinePosition" => [
      "name" => "mainLinePosition",
      "value" => "1",
      "type" => "select",
      "props" => [
       "label" => "Ubicación de la linea",
       "options" => [
        [ "label" => "Izquierda", "value" => "1" ],
        [ "label" => "Derecha", "value" => "2" ],
       ],
      ],
     ],
     "imageInterspersed" => [
      "name" => "imageInterspersed",
      "value" => "1",
      "type" => "select",
      "props" => [
       "label" => "Imagenes Intercaladas",
       "options" => $vAttributes[ "validation" ],
      ],
     ],
     "firstImagePosition" => [
      "name" => "firstImagePosition",
      "value" => "2",
      "type" => "select",
      "props" => [
       "label" => "Ubicación de la primera imagen",
       "options" => [
        [ "label" => "Izquierda", "value" => "1" ],
        [ "label" => "Derecha", "value" => "2" ],
       ],
      ],
     ],
     "firstItemPosition" => [
      "name" => "firstItemPosition",
      "value" => "2",
      "type" => "select",
      "props" => [
       "label" => "Ubicación del primer Item",
       "options" => [
        [ "label" => "Izquierda", "value" => "1" ],
        [ "label" => "Derecha", "value" => "2" ],
       ],
      ],
     ],
     "showTwoItems" => [
      "name" => "showTwoItems",
      "value" => "1",
      "type" => "select",
      "props" => [
       "label" => "Mostrar Dos Items",
       "options" => $vAttributes[ "validation" ],
      ],
     ],
     "classItem" => [
      "name" => "classItem",
      "type" => "input",
      "props" => [
       "label" => "Clases generales del Item",
      ],
     ],
    ],
   ],
   "icon" => [
    "title" => "Icono",
    "fields" => [
     "icon" => [
      "name" => "icon",
      "type" => "input",
      "columns" => "col-12",
      "props" => [
       "label" => "Icono personalizado",
      ],
     ],
     "colorIcon" => [
      "name" => "colorIcon",
      "type" => "input",
      "props" => [
       "label" => "Color del Icono"
      ],
     ],
     "sizeIcon" => [
      "name" => "sizeIcon",
      "type" => "input",
      "props" => [
       "type" => "number",
       "label" => "Tamaño Icono"
      ],
     ],
    ],
   ],
   "number" => [
    "title" => "Numero",
    "fields" => [
     "withNumber" => [
      "name" => "withNumber",
      "value" => "1",
      "type" => "select",
      "props" => [
       "label" => "Mostrar Numero",
       "options" => $vAttributes[ "validation" ],
      ],
     ],
     "classNumber" => [
      "name" => "classNumber",
      "type" => "input",
      "props" => [
       "label" => "Clases del numero",
      ],
     ],
     "sizeNumber" => [
      "name" => "sizeNumber",
      "type" => "input",
      "props" => [
       "type" => "number",
       "label" => "Tamaño del Numero"
      ],
     ],
     "colorNumber" => [
      "name" => "colorNumber",
      "type" => "input",
      "props" => [
       "label" => "Color Numero",
      ],
     ],
     "marginNumber" => [
      "name" => "marginNumber",
      "type" => "input",
      "props" => [
       "type" => "text",
       "label" => "Margen"
      ],
     ],
     "borderNumber" => [
      "name" => "borderNumber",
      "value" => "0",
      "type" => "select",
      "props" => [
       "label" => "Borde",
       "options" => [
        [ "label" => "0", "value" => "0" ],
        [ "label" => "1px", "value" => "1px" ],
        [ "label" => "2px", "value" => "2px" ],
        [ "label" => "3px", "value" => "3px" ],
        [ "label" => "4px", "value" => "4px" ],
        [ "label" => "5px", "value" => "5px" ]
       ]
      ]
     ],
     "colorBorderNumber" => [
      "name" => "colorBorderNumber",
      "type" => "input",
      "props" => [
       "label" => "Color del borde",
      ],
     ],
     "bgNumber" => [
      "name" => "bgNumber",
      "type" => "input",
      "props" => [
       "label" => "Fondo del Numero",
       "help" => [
        "description" => "Solo valores hexadecimales o variables (#000, var(), White...",
       ]
      ]
     ],
     "radiusNumber" => [
      "name" => "radiusNumber",
      "type" => "input",
      "props" => [
       "type" => "number",
       "label" => "Redondeado"
      ],
     ],
     "sizeContainerNumber" => [
      "name" => "sizeContainerNumber",
      "type" => "input",
      "props" => [
       "type" => "number",
       "label" => "Tamaño del fondo"
      ],
     ],
    ],
   ],
   "contentLabel" => [
    "title" => "Contenido Etiqueta",
    "fields" => [
     "withContentLabel" => [
      "name" => "withContentLabel",
      "value" => "0",
      "type" => "select",
      "props" => [
       "label" => "Mostrar Label",
       "options" => $vAttributes[ "validation" ],
      ],
     ],
      "contentLabel" => [
        "name" => "contentLabel",
        "type" => "input",
        "props" => [
          "label" => "Campo/s a mostrar en el label",
          "help" => [
            "description" => "Valores Separados por coma(,)",
          ]
        ]
      ],
     "colorContentLabel" => [
      "name" => "colorContentLabel",
      "type" => "input",
      "props" => [
       "label" => "Color",
      ],
     ],
     "bgContentLabel" => [
      "name" => "bgContentLabel",
      "type" => "input",
      "props" => [
       "label" => "Color Fondo",
       "help" => [
        "description" => "Solo valores hexadecimales o variables (#000, var(), White...",
       ]
      ]
     ],
     "sizeContentLabel" => [
      "name" => "sizeContentLabel",
      "type" => "input",
      "props" => [
       "type" => "number",
       "label" => "Tamaño "
      ],
     ],
     "paddingContentLabel" => [
      "name" => "paddingContentLabel",
      "type" => "input",
      "props" => [
       "type" => "number",
       "label" => "Espaciado"
      ],
     ],
     "marginContentLabel" => [
      "name" => "marginContentLabel",
      "type" => "input",
      "props" => [
       "type" => "text",
       "label" => "Margen"
      ],
     ],
     "radiusContentLabel" => [
      "name" => "radiusContentLabel",
      "type" => "input",
      "props" => [
       "type" => "number",
       "label" => "Redondeado del borde"
      ],
     ],
      "formatDate" => [
        "name" => "formatDate",
        "value" => "d \\d\\e M",
        "type" => "select",
        "columns"=> "col-12",
        "props" => [
          "label" => "Formato",
          "options" => $vAttributes["formatCreatedDate"]
        ]
      ],
    ],
   ],
  ],
 ],
];
