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
  public $itemComponentAttributes;
  public $itemComponentNamespace;
  public $title;
  public $subtitle;
  public $textPosition; // 1 -> solo titulo 2 -> titulo con subtitulo debajo 3 -> titulo con subtitilo arria
  public $titleMarginT;
  public $titleMarginB;
  public $titleColor;
  public $titleVineta;
  public $titleVinetaColor;
  public $titleSize;
  public $titleWeight;
  public $titleTransform;
  public $titleLetterSpacing;
  public $subtitleMarginT;
  public $subtitleMarginB;
  public $subtitleColor;
  public $subtitleSize;
  public $subtitleWeight;
  public $subtitleTransform;
  public $subtitleLetterSpacing;
  public $withLineTitle;
  public $lineTitleConfig;
  public $titleClasses;
  public $subtitleClasses;
  public $textAlign;
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
  public $colorBorderNumber;
  public $bgNumber;
  public $radiusNumber;
  public $sizeContainerNumber;
  public $contentLabel;
  public $withContentLabel;
  public $colorContentLabel;
  public $bgContentLabel;
  public $sizeContentLabel;
  public $paddingContentLabel;
  public $marginContentLabel;
  public $radiusContentLabel;
  public $formatDate;

  /**
   * Create a new component instance.
   *
   * @return void
   */
  public function __construct($layout = 'timeline-layout-1', $params = [], $itemComponent = [], $itemComponentAttributes = [],
                             $itemComponentNamespace = null, $repository = null, $mainLineColor = 'var(--primary)', $mainLinePosition = '1',
                             $imageInterspersed = true, $firstImagePosition = '1', $firstItemPosition = '1', $showTwoItems = false,
                             $withNumber = false, $classNumber = 'font-weight-bold', $sizeNumber = '15',$bgNumber = 'var(--secondary)',
                             $borderNumber = '1px solid var(--secondary)', $colorNumber = 'var(--primary)', $marginNumber = '10px',
                             $radiusNumber = '0', $sizeContainerNumber = '30',$icon = 'fa-solid fa-circle',$colorIcon = "var(--primary)",
                             $sizeIcon = '15', $classItem = null, $withContentLabel = false, $colorContentLabel = 'var(--primary)',
                             $bgContentLabel = 'var(--secondary)', $sizeContentLabel = '15', $paddingContentLabel = '10px', $marginContentLabel = '10px 0',
                             $radiusContentLabel = '8px', $colorBorderNumber = 'var(--primary)', $formatDate = "d \\d\\e M", $contentLabel = null, $textPosition = 2,
                            $titleMarginT = "mt-0", $titleMarginB = "mb-0", $titleColor = null, $titleVineta = null, $titleVinetaColor = null,
                            $titleSize = null, $titleWeight = "font-weight-normal", $titleTransform = null, $title="", $subtitle="",
                            $titleLetterSpacing = 0, $subtitleMarginT = "mt-0", $subtitleMarginB = "mb-0",
                            $subtitleColor = null, $subtitleSize = null, $subtitleWeight = "font-weight-normal", $subtitleTransform = null,
                            $subtitleLetterSpacing = 0,$withLineTitle = 0, $lineTitleConfig = [], $titleClasses = "", $subtitleClasses = "",
                            $textAlign = "text-left"
 ){
   $this->layout = $layout;
   $this->view = "iblog::frontend.components.timeline.layouts.$layout.index";
    $this->params = $params;
    $this->itemComponent = $itemComponent ?? "isite::item-list";
    $this->itemComponentAttributes = count($itemComponentAttributes)
                                 ? $itemComponentAttributes
                                 : config('asgard.isite.config.indexItemListAttributes');
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
    $this->colorBorderNumber = $colorBorderNumber;
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
    $this->contentLabel = $contentLabel;
    $this->withContentLabel = $withContentLabel;
    $this->colorContentLabel = $colorContentLabel;
    $this->bgContentLabel = $bgContentLabel;
    $this->sizeContentLabel = $sizeContentLabel;
    $this->paddingContentLabel = $paddingContentLabel;
    $this->marginContentLabel = $marginContentLabel;
    $this->radiusContentLabel = $radiusContentLabel;
    $this->formatDate = $formatDate;
    $this->title = $title;
    $this->subtitle = $subtitle;
    $this->textPosition = $textPosition;
    $this->titleMarginT = $titleMarginT;
    $this->titleMarginB = $titleMarginB;
    $this->titleColor = $titleColor;
    $this->titleVineta = $titleVineta;
    $this->titleVinetaColor = $titleVinetaColor;
    $this->titleSize = $titleSize;
    $this->titleWeight = $titleWeight;
    $this->titleTransform = $titleTransform;
    $this->titleLetterSpacing = $titleLetterSpacing;
    $this->subtitleMarginT = $subtitleMarginT;
    $this->subtitleMarginB = $subtitleMarginB;
    $this->subtitleColor = $subtitleColor;
    $this->subtitleSize = $subtitleSize;
    $this->subtitleWeight = $subtitleWeight;
    $this->subtitleTransform = $subtitleTransform;
    $this->subtitleLetterSpacing = $subtitleLetterSpacing;
    $this->titleClasses = $titleClasses;
    $this->subtitleClasses = $subtitleClasses;
    $this->withLineTitle = $withLineTitle;
    $this->lineTitleConfig = !empty($lineTitleConfig) ? $lineTitleConfig : [
      "background" => "var(--primary)",
      "height" => "2px",
      "width" => "10%",
      "margin" => "0 auto"];
    $this->textAlign = $textAlign;

    //  Covert to array extra fields
    if (!empty($contentLabel)) {
      if (strpos($contentLabel, ',') !== false) {
        $this->contentLabel = explode(",", $contentLabel);
      } else {
        $this->contentLabel = array($contentLabel);
      }
    }

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
