<?php

namespace Modules\Iblog\Transformers;

use Imagina\Icore\Transformers\CoreResource;

class CategoryTransformer extends CoreResource
{
  /**
   * Attribute to exclude relations from transformed data
   * @var array
   */
  protected array $excludeRelations = [];

  /**
  * Method to merge values with response
  *
  * @return array
  */
  public function modelAttributes($request): array
  {
    return [
      'files' => $this->whenLoaded('files', fn() => $this->files->byZones($this->mediaFillable, $this))
    ];
  }
}
