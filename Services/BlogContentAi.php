<?php


namespace Modules\Iblog\Services;

use Illuminate\Http\Request;
use Modules\Isite\Services\AiService;
use Modules\Iblog\Entities\Category;

class BlogContentAi
{
  public $aiService;
  private $log = "Iblog: Services|BlogContentAi|";

  function __construct()
  {
    $this->aiService = new AiService();
  }

  public function getPosts($quantity = 2)
  { 
    \Log::info($this->log."getPosts|INIT");

    //instance the prompt to generate the posts
    $prompt = "Contenido para post de blog que seran usados en un sitio WEB con los siguientes atributos ";
    //Instance attributes
    $prompt .= $this->aiService->getStandardPrompts(
      ["title", "description", "summary", "slug", "category_id"],
      ["categories" => Category::get()]
    );
    //Call IA Service
    $response = $this->aiService->getContent($prompt, $quantity);
    \Log::info($this->log."getPosts|END");
    //Return response
    return $response;
  }
}
