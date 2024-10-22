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
  "childBlocks" => [
   "itemComponentAttributes" => "isite::item-list",
  ],
  "attributes" => [
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
   "date" => [
    "title" => "Fecha",
    "fields" => [
     "withDate" => [
      "name" => "withDate",
      "value" => "1",
      "type" => "select",
      "props" => [
       "label" => "Mostrar Fecha",
       "options" => $vAttributes[ "validation" ],
      ],
     ],
     "colorDate" => [
      "name" => "colorDate",
      "type" => "input",
      "props" => [
       "label" => "Color de la Fecha",
      ],
     ],
     "bgDate" => [
      "name" => "bgDate",
      "type" => "input",
      "props" => [
       "label" => "Fondo de la Fecha",
       "help" => [
        "description" => "Solo valores hexadecimales o variables (#000, var(), White...",
       ]
      ]
     ],
     "sizeDate" => [
      "name" => "sizeDate",
      "type" => "input",
      "props" => [
       "type" => "number",
       "label" => "Tamaño de la Fecha"
      ],
     ],
     "paddingDate" => [
      "name" => "paddingDate",
      "type" => "input",
      "props" => [
       "type" => "number",
       "label" => "Espaciado"
      ],
     ],
     "marginDate" => [
      "name" => "marginDate",
      "type" => "input",
      "props" => [
       "type" => "text",
       "label" => "Margen"
      ],
     ],
     "radiusDate" => [
      "name" => "radiusDate",
      "type" => "input",
      "props" => [
       "type" => "number",
       "label" => "Redondeado del borde"
      ],
     ],
    ],
   ],
  ],
 ],
];