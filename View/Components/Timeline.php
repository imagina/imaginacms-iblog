<?php

namespace Modules\Iblog\View\Components;

use Illuminate\View\Component;

class Timeline extends Component
{
  public $view;
  public $items;
  public $params;
  public $layout;
  public $repository;
  public $itemComponent;
  public $itemComponentAttributesList;
  public $itemComponentNamespace;
  public $mainLineColor;
  public $mainLinePosition;
  public $imageInterspersed;
  public $firstImagePosition;
  public $firstItemPosition;
  public $showTwoItems;
  public $classItem;

  public $icon;
  public $colorIcon;
  public $sizeIcon;
  public $withNumber;

  public $classNumber;
  public $sizeNumber;
  public $colorNumber;

  public $marginNumber;
  public $borderNumber;
  public $bgNumber;
  public $radiusNumber;
  public $sizeContainerNumber;
  public $withDate;
  public $colorDate;
  public $bgDate;
  public $sizeDate;
  public $paddingDate;
  public $marginDate;
  public  $radiusDate;

  /**
   * Create a new component instance.
   *
   * @return void
   */
  public function __construct(
    $layout = 'timeline-layout-1', $params = [], $itemComponent = [], $itemComponentAttributesList = [],
    $itemComponentNamespace = null, $repository = null, $mainLineColor = 'var(--primary)', $mainLinePosition = '1',
    $imageInterspersed = true, $firstImagePosition = '1', $firstItemPosition = '1', $showTwoItems = false,
    $withNumber = false, $classNumber = 'font-weight-bold', $sizeNumber = '15', $bgNumber = 'var(--secondary)', $borderNumber = '1px solid var(--secondary)',
    $colorNumber = 'var(--primary)', $marginNumber = '10px', $radiusNumber = '0', $sizeContainerNumber = '30', $icon = 'fa-solid fa-circle',
    $colorIcon = "var(--primary)",
    $sizeIcon = '15', $classItem = null, $bgPoint = 'var(--primary)',
    $borderPoint = '4px solid var(--primary)', $withDate = false, $colorDate = 'var(--primary)', $bgDate = 'var(--secondary)',
    $sizeDate = '15', $paddingDate = '10px', $marginDate = '10px', $radiusDate = '8px',
  )
  {
    $this->layout = $layout;
    $this->view = "iblog::frontend.components.timeline.layouts.$layout.index";
    $this->params = $params;
    $this->itemComponent = $itemComponent ?? "isite::item-list";
    $this->itemComponentAttributesList = count($itemComponentAttributesList) ? $itemComponentAttributesList : config('asgard.isite.config.indexItemListAttributesList');
    $this->itemComponentNamespace = $itemComponentNamespace ?? "Modules\Isite\View\Components\ItemList";
    $this->mainLineColor = $mainLineColor;
    $this->icon = $icon;

    $this->sizeIcon = $sizeIcon;
    $this->colorIcon = $colorIcon;

    $this->withNumber = $withNumber;
    $this->classNumber = $classNumber;
    $this->sizeNumber = $sizeNumber;
    $this->bgNumber = $bgNumber;
    $this->borderNumber = $borderNumber;
    $this->radiusNumber = $radiusNumber;
    $this->sizeContainerNumber = $sizeContainerNumber;
    $this->colorNumber = $colorNumber;
    $this->marginNumber = $marginNumber;
    $this->mainLinePosition = $mainLinePosition;
    $this->imageInterspersed = $imageInterspersed;
    $this->firstImagePosition = $firstImagePosition;
    $this->showTwoItems = $showTwoItems;
    $this->firstItemPosition = $firstItemPosition;
    $this->classItem = $classItem;
    $this->withDate = $withDate;
    $this->colorDate = $colorDate;
    $this->bgDate = $bgDate;
    $this->sizeDate = $sizeDate;
    $this->paddingDate = $paddingDate;
    $this->marginDate = $marginDate;
    $this->radiusDate = $radiusDate;

    $this->getItems();
  }

  private function getItems()
  {

    $this->repository = $repository ?? app("Modules\Iblog\Repositories\PostRepository");
    $this->items = $this->repository->getItemsBy(json_decode(json_encode($this->params)));
  }

  /**
   * Get the view / contents that represent the component.
   *
   * @return \Illuminate\Contracts\View\View|string
   */
  public function render()
  {
    return view($this->view);
  }
}
