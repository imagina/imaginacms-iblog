<?php


namespace Modules\Iblog\Services;

use Illuminate\Http\Request;
use Modules\Isite\Services\AiService;
use Modules\Iblog\Entities\Category;
use Modules\Iblog\Entities\Post;

class BlogContentAi
{
  public $aiService;
  private $log = "Iblog: Services|BlogContentAi|";
  private $postRepository;
  private $maxAttempts = 3;
  private $postQuantity = 4;

  function __construct()
  {
    $this->aiService = new AiService();
    $this->postRepository = app("Modules\Iblog\Repositories\PostRepository");
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

  /**
  * Principal
  */
  public function startProcesses()
  {

    \Log::info($this->log."startProcesses");

    $newData = $this->getNewData();
    if(!is_null($newData)){

      $this->deleteOldPosts();
      $this->createPosts($newData);
      
    }

  }

  /**
  * Get the New Data
  */
  public function getNewData()
  {
    
    $newData = null;

    $attempts = 0;
    do {
      \Log::info($this->log."getNewData|Attempt:".($attempts+1));
      $newData = $this->getPosts($this->postQuantity);
      if(is_null($newData)){
        $attempts++;
      }else{
        break;
      }
    }while($attempts < $this->maxAttempts);

    return $newData;
  }

  /**
  * Post to Create
  */
  public function createPosts($posts)
  {

    \Log::info($this->log."createPosts");
    foreach ($posts as $key => $post) {

      $post['user_id'] = 1;
      $post['status'] = 2; //Published

      \Log::info($this->log."createPosts|Category Id:".$post['category_id']);
      
      $result = $this->postRepository->create($post);

      //TODO
      //Proceso to create image

    }
   
  }

  /**
   * Delete old Posts
   */
  public function deleteOldPosts()
  {

    \Log::info($this->log."deleteOldPosts");

    //Problems with constraints
    //$deletedOldPosts = Post::truncate();

    //\DB::table('iblog__post_translations')->truncate();

    $params = [
      "include" => [],
    ];
    $posts = $this->postRepository->getItemsBy(json_decode(json_encode($params)));
    
    if(count($posts)>0){
      foreach ($posts as $key => $post) {
        $post->delete();
      }
    }

  }

}
