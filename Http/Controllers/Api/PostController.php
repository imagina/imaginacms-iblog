<?php

namespace Modules\Iblog\Http\Controllers\Api;

use Illuminate\Http\Request;
use Log;
use Mockery\CountValidator\Exception;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;  //Base API
use Modules\Iblog\Entities\Post;
use Modules\Iblog\Http\Requests\IblogRequest;
use Modules\Iblog\Repositories\PostRepository;
use Modules\Iblog\Transformers\CategoryTransformer;
use Modules\Iblog\Transformers\PostTransformer;
use Modules\Iblog\Transformers\TagTransformer;
use Modules\User\Transformers\UserProfileTransformer;
use Route;

//Base API

class PostController extends BaseApiController
{
    /**
     * @var PostRepository
     */
    private $post;

    public function __construct(PostRepository $post)
    {
        parent::__construct();
        $this->post = $post;

    }

    /**
     * Get Data from Posts
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        try {
            //Get Parameters from URL.
            $p = $this->parametersUrl(false, false, false, []);

            //Request to Repository
            $posts = $this->post->index($p->page, $p->take, $p->filter, $p->include);

            //Response
            $response = ["data" => PostTransformer::collection($posts)];

            //If request pagination add meta-page
            $p->page ? $response["meta"] = ["page" => $this->pageTransformer($posts)] : false;
        } catch (\Exception $e) {
            //Message Error
            $status = 500;
            $response = [
                "errors" => $e->getMessage()
            ];
        }

        return response()->json($response, $status ?? 200);
    }

    /**
     * Get Data by post
     *
     * @param Request $request
     * @return mixed
     */
    public function show($param, Request $request)
    {
        try {
            //Get Parameters from URL.
            $p = $this->parametersUrl(false, false, false, []);

            //Request to Repository
            $post = $this->post->show($param, $p->include);

            //Response
            $response = [
                "data" => is_null($post) ? false : new PostTransformer($post)];
        } catch (\Exception $e) {
            //Message Error
            $status = 500;
            $response = [
                "errors" => $e->getMessage()
            ];
        }

        return response()->json($response, $status ?? 200);
    }


  /*  public function posts(Request $request)
    {
        try {
            if (isset($request->include)) {
                $includes = explode(",", $request->include);
            } else {
                $includes = null;
            }


            if (isset($request->filters) && !empty($request->filters)) {
                $filters = json_decode($request->filters);
                $results = $this->post->whereFilters($filters, $includes);

                if (isset($filters->take)) {
                    $response = [
                        'meta' => [
                            "take" => $filters->take ?? 5,
                            "skip" => $filters->skip ?? 0,
                        ],
                        'data' => PostTransformer::collection($results),
                    ];
                } else {
                    $response = [
                        'meta' => [
                            "total-pages" => $results->lastPage(),
                            "per_page" => $results->perPage(),
                            "total-item" => $results->total()
                        ],
                        'data' => PostTransformer::collection($results),
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

                $results = $this->post->paginate($request->paginate ?? 12);
                $response = [
                    'meta' => [
                        "total-pages" => $results->lastPage(),
                        "per_page" => $results->perPage(),
                        "total-item" => $results->total()
                    ],
                    'data' => PostTransformer::collection($results),
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
                "title" => "Error Query Products",
                "detail" => $e->getMessage()
            ]
            ];
        }

        return response()->json($response, $status ?? 200);
    }

    public function post(Post $post, Request $request)
    {
        try {
            if (isset($post->id) && !empty($post->id)) {
                $response = [
                    "meta" => [
                        "metatitle" => $post->metatitle,
                        "metadescription" => $post->metadescription
                    ],
                    "type" => "articles",
                    "id" => $post->id,
                    "attributes" => new PostTransformer($post),

                ];

                $includes = explode(",", $request->include);

                if (in_array('author', $includes)) {
                    $response["relationships"]["author"] = new UserProfileTransformer($post->user);

                }
                if (in_array('category', $includes)) {
                    $response["relationships"]["category"] = new CategoryTransformer($post->category);
                }
                if (in_array('tags', $includes)) {
                    $response["relationships"]["tags"] = TagTransformer::collection($post->tags);
                }


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
                "title" => "Error Query post",
                "detail" => $e->getMessage()
            ]
            ];
        }

        return response()->json($response, $status ?? 200);
    }
*/
    public function store(IblogRequest $request)
    {
        try {
            $options = (array)$request->options ?? array();
            isset($request->metatitle) ? $options['metatitle'] = $request->metatitle : false;
            isset($request->metadescription) ? $options['metadescription'] = $request->metatitle : false;
            $request['options'] = $options;
            $post = $this->post->create($request->all());
            $status = 200;
            $response = [
                'susses' => [
                    'code' => '201',
                    "source" => [
                        "pointer" => url($request->path())
                    ],
                    "title" => trans('core::core.messages.resource created', ['name' => trans('iblog::posts.singular')]),
                    "detail" => [
                        'id' => $post->id
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
                "title" => "Error Query Posts",
                "detail" => $e->getMessage()
            ]
            ];
        }
        return response()->json($response, $status ?? 200);

    }

    public function galleryStore(Request $request)
    {
        try {
            $original_filename = $request->file('file')->getClientOriginalName();

            $idpost = $request->input('idedit');
            $extension = $request->file('file')->getClientOriginalExtension();
            $allowedextensions = array('JPG', 'JPEG', 'PNG', 'GIF');

            if (!in_array(strtoupper($extension), $allowedextensions)) {
                return 0;
            }
            $disk = 'publicmedia';
            $image = \Image::make($request->file('file'));
            $name = str_slug(str_replace('.' . $extension, '', $original_filename), '-');


            $image->resize(config('asgard.iblog.config.imagesize.width'), config('asgard.iblog.config.imagesize.height'), function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            if (config('asgard.iblog.config.watermark.activated')) {
                $image->insert(config('asgard.iblog.config.watermark.url'), config('asgard.iblog.config.watermark.position'), config('asgard.iblog.config.watermark.x'), config('asgard.iblog.config.watermark.y'));
            }
            $nameimag = $name . '.' . $extension;
            $destination_path = 'assets/iblog/post/gallery/' . $idpost . '/' . $nameimag;

            \Storage::disk($disk)->put($destination_path, $image->stream($extension, '100'));

            $status = 200;
            $response = [
                'susses' => [
                    'code' => '201',
                    "source" => [
                        "pointer" => url($request->path())
                    ],
                    "title" => trans('core::core.messages.resource created', ['name' => trans('iblog::posts.gallery.create')]),
                    "detail" => [
                        'destination_path' => $destination_path
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
                "title" => "Error publish Gallery image",
                "detail" => $e->getMessage()
            ]
            ];
        }
        return response()->json($response, $status ?? 200);
    }

    public function galleryDelete(Request $request)
    {
        try {
            $disk = "publicmedia";
            $dirdata = $request->input('dirdata');
            \Storage::disk($disk)->delete($dirdata);
            $status = 200;
            $response = [
                'susses' => [
                    'code' => '201',
                    "title" => trans('core::core.messages.resource deleted', ['name' => trans('iblog::posts.gallery.delete')]),
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
                "title" => "Error delete Gallery image",
                "detail" => $e->getMessage()
            ]
            ];
        }
        return response()->json($response, $status ?? 200);
    }

    public function update(Post $post, IblogRequest $request)
    {

        try {

            if (isset($post->id) && !empty($post->id)) {
                $options = (array)$request->options ?? array();
                isset($request->metatitle) ? $options['metatitle'] = $request->metatitle : false;
                isset($request->metadescription) ? $options['metadescription'] = $request->metatitle : false;
                isset($request->mainimage) ? $options["mainimage"] = saveImage($request['mainimage'], "assets/iblog/post/" . $post->id . ".jpg") : false;
                $request['options'] = json_encode($options);
                $post = $this->post->update($post, $request->all());

                $status = 200;
                $response = [
                    'susses' => [
                        'code' => '201',
                        "source" => [
                            "pointer" => url($request->path())
                        ],
                        "title" => trans('core::core.messages.resource updated', ['name' => trans('iblog::posts.singular')]),
                        "detail" => [
                            'id' => $post->id
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
                "title" => "Error Query Post",
                "detail" => $e->getMessage()
            ]
            ];
        }

        return response()->json($response, $status ?? 200);
    }

    public function delete(Post $post, Request $request)
    {
        try {
            $this->post->destroy($post);
            $status = 200;
            $response = [
                'susses' => [
                    'code' => '201',
                    "title" => trans('core::core.messages.resource deleted', ['name' => trans('iblog::posts.singular')]),
                    "detail" => [
                        'id' => $post->id
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
                "title" => "Error Query Post",
                "detail" => $e->getMessage()
            ]
            ];
        }

        return response()->json($response, $status ?? 200);
    }

}