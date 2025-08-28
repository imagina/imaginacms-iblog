<?php

namespace Modules\Iblog\Models;

use Imagina\Icore\Models\CoreStaticModel;

/**
 * Class Status
 */
class Status extends CoreStaticModel
{
  const DRAFT = 0;

  const PENDING = 1;

  const PUBLISHED = 2;

  const UNPUBLISHED = 3;

  public function __construct()
  {
    $this->records = [
      self::DRAFT => itrans('iblog::common.status.draft'),
      self::PENDING => itrans('iblog::common.status.pending'),
      self::PUBLISHED => itrans('iblog::common.status.published'),
      self::UNPUBLISHED => itrans('iblog::common.status.unpublished'),
    ];
  }
}
