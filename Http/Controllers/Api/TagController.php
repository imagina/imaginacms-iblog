<?php

namespace Modules\Iblog\Http\Controllers\Api;


use Illuminate\Http\Request;
use Log;
use Mockery\CountValidator\Exception;
use Modules\Iblog\Entities\Tag;
use Modules\Iblog\Http\Requests\IblogRequest;
use Modules\Iblog\Repositories\TagRepository;
use Modules\Iblog\Repositories\PostRepository;
use Modules\Iblog\Transformers\TagTransformer;
use Modules\Iblog\Transformers\PostTransformer;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;
use Route;


class TagController extends BaseApiControlle
{

    /**
     *
     * @var TagRepository
     */
    private $post;
    private $category;
    private $tag;

    public function __construct(TagRepository $tag)
    {
        parent::__construct();
        $this->tag = $tag;

    }

    public function tags(Request $request)
    {

        try {
           if(isset($request->include)){
               $includes = explode(",", $request->include);
           }else{$includes=null;}
            if (isset($request->filters) && !empty($request->filters)) {
                $filters = json_decode($request->filters);
                $results = $this->tag->whereFilters($filters, $includes);

                if (isset($filters->take)) {
                    $response = [
                        'meta' => [
                            "take" => $filters->take ?? 5,
                            "skip" => $filters->skip ?? 0,
                        ],
                        'data' =>FullTagTransformer::collection($results),
                    ];
                } else {
                    $response = [
                        'meta' => [
                            "total-pages" => $results->lastPage(),
                            "per_page" => $results->perPage(),
                            "total-item" => $results->total()
                        ],
                        'data' => FullTagTransformer::collection($results),
                        'links' => [
                            "self" => $results->currentPage(),
                            "first" => $results->hasMorePages(),
                            "prev" => $results->previousPageUrl(),
                            "next" => $results->nextPageUrl(),
                            "last" => $results->lastPage()
                        ]

                    ];
                }
            } else {
                $paginate = $request->paginate ?? 12;

                $results = $this->tag->paginate($paginate);
                $response = [
                    'meta' => [
                        "total-pages" => $results->lastPage(),
                        "per_page" => $results->perPage(),
                        "total-item" => $results->total()
                    ],
                    'data' => TagTransformer::collection($results),
                    'links' => [
                        "self" => $results->currentPage(),
                        "first" => $results->hasMorePages(),
                        "prev" => $results->previousPageUrl(),
                        "next" => $results->nextPageUrl(),
                        "last" => $results->lastPage()
                    ]

                ];
            }

        } catch (\Exception $e) {
            Log::error($e);
            $status = 500;
            $response = ['errors' => [
                "code" => "501",
                "source" => [
                    "pointer" => url($request->path()),
                ],
                "title" => "Error Query Tags",
                "detail" => $e->getMessage()
            ]
            ];
        }
        return response()->json($response, $status ?? 200);

    }


    public function tag(tag $tag, Request $request)
    {
        try{
            if (isset($tag->id) && !empty($tag->id)) {
                $response = [
                    "meta" => [
                        "metatitle" => $tag->metatitle,
                        "metadescription" => $tag->metadescription
                    ],
                    "type" => "tags",
                    "id" => $tag->id,
                    "attributes" => new TagTransformer($tag),

                ];

                $includes=explode(",",$request->include);

                if (in_array('posts', $includes)) {
                    $response["relationships"]["posts"] = PostTransformer::collection($tag->posts);
                }


            }
            else {
                $status = 404;
                $response = ['errors' => [
                    "code" => "404",
                    "source" => [
                        "pointer" => url($request->path()),
                    ],
                    "title" => "Not Found",
                    "detail" => 'Query empty'
                ]
                ];
            }}
        catch (\Exception $e){
            Log::error($e);
            $status = 500;
            $response = ['errors' => [
                "code" => "501",
                "source" => [
                    "pointer" => url($request->path()),
                ],
                "title" => "Error Query post",
                "detail" => $e->getMessage()
            ]
            ];
        }

        return response()->json($response, $status ?? 200);
    }



    public function store(IblogRequest $request)
    {
        try {
            $options = (array)$request->options ?? array();
            isset($request->metatitle) ? $options['metatitle'] = $request->metatitle : false;
            isset($request->metadescription) ? $options['metadescription'] = $request->metatitle : false;
            $request['options'] = $options;
            $tag = $this->tag->create($request->all());
            $status = 200;
            $response = [
                'susses' => [
                    'code' => '201',
                    "source" => [
                        "pointer" => url($request->path())
                    ],
                    "title" => trans('core::core.messages.resource created', ['name' => trans('iblog::tags.singular')]),
                    "detail" => [
                        'id' => $tag->id
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error($e);
            $status = 500;
            $response = ['errors' => [
                "code" => "501",
                "source" => [
                    "pointer" => url($request->path()),
                ],
                "title" => "Error Query Tags",
                "detail" => $e->getMessage()
            ]
            ];
        }
        return response()->json($response, $status ?? 200);

    }

    public function update(Tag $tag, IblogRequest $request)
    {

        try {

            if (isset($tag->id) && !empty($tag->id)) {
                $options = (array)$request->options ?? array();
                isset($request->metatitle) ? $options['metatitle'] = $request->metatitle : false;
                isset($request->metadescription) ? $options['metadescription'] = $request->metatitle : false;
                isset($request->mainimage) ? $options["mainimage"] = saveImage($request['mainimage'], "assets/iblog/tag/" . $tag->id . ".jpg") : false;
                $request['options'] = json_encode($options);
                $tag = $this->tag->update($tag, $request->all());

                $status = 200;
                $response = [
                    'susses' => [
                        'code' => '201',
                        "source" => [
                            "pointer" => url($request->path())
                        ],
                        "title" => trans('core::core.messages.resource updated', ['name' => trans('iblog::tags.singular')]),
                        "detail" => [
                            'id' => $tag->id
                        ]
                    ]
                ];


            } else {
                $status = 404;
                $response = ['errors' => [
                    "code" => "404",
                    "source" => [
                        "pointer" => url($request->path()),
                    ],
                    "title" => "Not Found",
                    "detail" => 'Query empty'
                ]
                ];
            }
        } catch (\Exception $e) {
            Log::error($e);
            $status = 500;
            $response = ['errors' => [
                "code" => "501",
                "source" => [
                    "pointer" => url($request->path()),
                ],
                "title" => "Error Query Tag",
                "detail" => $e->getMessage()
            ]
            ];
        }

        return response()->json($response, $status ?? 200);
    }

    public function delete(Tag $tag, Request $request)
    {
        try {
            $this->tag->destroy($tag);
            $status = 200;
            $response = [
                'susses' => [
                    'code' => '201',
                    "title" => trans('core::core.messages.resource deleted', ['name' => trans('iblog::tags.singular')]),
                    "detail" => [
                        'id' => $tag->id
                    ]
                ]
            ];

        } catch (\Exception $e) {
            Log::error($e);
            $status = 500;
            $response = ['errors' => [
                "code" => "501",
                "source" => [
                    "pointer" => url($request->path()),
                ],
                "title" => "Error Query Tags",
                "detail" => $e->getMessage()
            ]
            ];
        }

        return response()->json($response, $status ?? 200);
    }




}