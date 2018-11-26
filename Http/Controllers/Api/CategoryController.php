<?php

namespace Modules\Iblog\Http\Controllers\Api;

use Illuminate\Http\Request;
use Log;
use Mockery\CountValidator\Exception;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\Iblog\Entities\Category;
use Modules\Iblog\Http\Requests\IblogRequest;
use Modules\Iblog\Repositories\CategoryRepository;
use Modules\Iblog\Repositories\PostRepository;
use Modules\Iblog\Transformers\CategoryTransformer;
use Modules\Iblog\Transformers\PostTransformer;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;
use Modules\User\Transformers\UserProfileTransformer;
use Route;

//Base API

class CategoryController extends BaseApiController
{
  /**
   *
   * @var CategoryRepository
   */
  private $post;
  private $category;

  public function __construct(PostRepository $post, CategoryRepository $category)
  {
    parent::__construct();
    $this->post = $post;
    $this->category = $category;
  }

  /**
   * Get Data from Categories
   *
   * @param Request $request
   * @return mixed
   */
  public function index(Request $request)
  {
    try {
      //Get Parameters from URL.
      $p = $this->parametersUrl(1, 12, false, []);

      //Request to Repository
      $categories = $this->category->index($p->page, $p->take, $p->filter, $p->include);

      //Response
      $response = ["data" => CategoryTransformer::collection($categories)];

      //If request pagination add meta-page
      $p->page ? $response["meta"] = ["page" => $this->pageTransformer($categories)] : false;
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
   * Get Data by category
   *
   * @param Request $request
   * @return mixed
   */
  public function show($slug, Request $request)
  {
    try {
      //Get Parameters from URL.
      $p = $this->parametersUrl(false, false, false, []);

      //Request to Repository
      $category = $this->category->show($slug, $p->include);

      //dd($category);

      //Response
      $response = [
        "data" => is_null($category) ? false : new CategoryTransformer($category)];
    } catch (\Exception $e) {
      //Message Error
      $status = 500;
      $response = [
        "errors" => $e->getMessage()
      ];
    }

    return response()->json($response, $status ?? 200);
  }





  public function posts(Category $category, Request $request)
  {
    try {
      $includes = explode(",", $request->include);
      if (isset($request->filters) && !empty($request->filters)) {
        $filters = json_decode($request->filters);
        $filters->categories = $category->id;

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

        $results = $this->post->whereFilters((object)$filter = ['categories' => $category->id, 'paginate' => $request->paginate ?? 12], $request->includes ?? false);
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
      $status = 500;
      $response = ['errors' => [
        "code" => "501",
        "source" => [
          "pointer" => url($request->path()),
        ],
        "title" => "Error Query Categories",
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
      $category = $this->category->create($request->all());
      $status = 200;
      $response = [
        'susses' => [
          'code' => '201',
          "source" => [
            "pointer" => url($request->path())
          ],
          "title" => trans('core::core.messages.resource created', ['name' => trans('iblog::categories.singular')]),
          "detail" => [
            'id' => $category->id
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
        "title" => "Error Query Categories",
        "detail" => $e->getMessage()
      ]
      ];
    }
    return response()->json($response, $status ?? 200);

  }

  public function update(Category $category, IblogRequest $request)
  {

    try {

      if (isset($category->id) && !empty($category->id)) {
        $options = (array)$request->options ?? array();
        isset($request->metatitle) ? $options['metatitle'] = $request->metatitle : false;
        isset($request->metadescription) ? $options['metadescription'] = $request->metatitle : false;
        isset($request->mainimage) ? $options["mainimage"] = saveImage($request['mainimage'], "assets/iblog/category/" . $category->id . ".jpg") : false;
        $request['options'] = json_encode($options);
        $category = $this->category->update($category, $request->all());

        $status = 200;
        $response = [
          'susses' => [
            'code' => '201',
            "source" => [
              "pointer" => url($request->path())
            ],
            "title" => trans('core::core.messages.resource updated', ['name' => trans('iblog::categories.singular')]),
            "detail" => [
              'id' => $category->id
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
        "title" => "Error Query Category",
        "detail" => $e->getMessage()
      ]
      ];
    }

    return response()->json($response, $status ?? 200);
  }

  public function delete(Category $category, Request $request)
  {
    try {

      $this->category->destroy($category);
      $status = 200;
      $response = [
        'susses' => [
          'code' => '201',
          "title" => trans('core::core.messages.resource deleted', ['name' => trans('iblog::categories.singular')]),
          "detail" => [
            'id' => $category->id
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
        "title" => "Error Query Category",
        "detail" => $e->getMessage()
      ]
      ];
    }

    return response()->json($response, $status ?? 200);
  }

}