<?php
/**
 * Created by PhpStorm.
 * User: imagina
 * Date: 5/09/2018
 * Time: 2:37 PM
 */

namespace Modules\Iblog\Presenters;


class TagPresenter extends Presenter
{
    /**
     * @var \Modules\Iblog\Entities\Status
     */
    protected $status;
    /**
     * @var \Modules\Iblog\Repositories\TagRepository
     */
    // private $category;

    public function __construct($entity)
    {
        parent::__construct($entity);
        $this->tag = app('Modules\Iblog\Repositories\TagRepository');
        $this->status = app('Modules\Iblog\Entities\Status');
    }

    /**
     * Get the previous tag of the current tag
     * @return object
     */
    public function previous()
    {
        return $this->tag>getPreviousOf($this->entity);
    }

    /**
     * Get the next tag of the current tag
     * @return object
     */
    public function next()
    {
        return $this->tag->getNextOf($this->entity);
    }

    /**
     * Get the tag status
     * @return string
     */
    public function status()
    {
        return $this->status->get($this->entity->status);
    }

    /**
     * Getting the label class for the appropriate status
     * @return string
     */
    public function statusLabelClass()
    {
        switch ($this->entity->status) {
            case Status::DRAFT:
                return 'bg-red';
                break;
            case Status::PENDING:
                return 'bg-orange';
                break;
            case Status::PUBLISHED:
                return 'bg-green';
                break;
            case Status::UNPUBLISHED:
                return 'bg-purple';
                break;
            default:
                return 'bg-red';
                break;
        }
    }

}