<?php

namespace Modules\Iblog\Sidebar;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\User\Contracts\Authentication;

class SidebarExtender implements \Maatwebsite\Sidebar\SidebarExtender
{
    /**
     * @var Authentication
     */
    protected $auth;

    /**
     * @param Authentication $auth
     *
     * @internal param Guard $guard
     */
    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Menu $menu
     *
     * @return Menu
     */
    public function extendWith(Menu $menu)
    {
        $menu->group(trans('core::sidebar.content'), function (Group $group) {

            $group->item(trans('iblog::common.iblog'), function (Item $item) {
                $item->icon('fa fa-copy');

                $item->item(trans('iblog::category.list'), function (Item $item) {
                    $item->icon('fa fa-file-text');
                    $item->weight(5);
                    $item->append('crud.iblog.category.create');
                    $item->route('crud.iblog.category.index');
                    $item->authorize(
                        $this->auth->hasAccess('iblog.categories.create')
                    );
                });

                $item->item(trans('iblog::post.list'), function (Item $item) {
                    $item->icon('fa fa-copy');
                    $item->weight(5);
                    $item->append('crud.iblog.post.create');
                    $item->route('crud.iblog.post.index');
                    $item->authorize(
                        $this->auth->hasAccess('iblog.posts.index')
                    );
                });

                $item->item(trans('iblog::tag.list'), function (Item $item) {
                    $item->icon('fa fa-tags');
                    $item->weight(5);
                    $item->append('crud.iblog.tag.create');
                    $item->route('crud.iblog.tag.index');
                    $item->authorize(
                        $this->auth->hasAccess('iblog.tags.index')
                    );
                });

                $item->authorize(
                    $this->auth->hasAccess('iblog.categories.index')
                );

            });


        });

        return $menu;
    }
}
