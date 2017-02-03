<?php

namespace Modules\Iblog\Http\Controllers\Admin;

use Illuminate\Http\Response;
use Modules\Iblog\Entities\Category;
use Modules\Iblog\Http\Requests\IblogRequest;

use Modules\Bcrud\Http\Controllers\BcrudController;
use Modules\User\Contracts\Authentication;


class CategoryController extends BcrudController
{


    /**
     * @var CategoryRepository
     */
    private $category;
    private $auth;

    public function __construct(Authentication $auth)
    {
        parent::__construct();

        $this->auth = $auth;


        $driver = config('asgard.user.config.driver');
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('Modules\Iblog\Entities\Category');
        $this->crud->setRoute('backend/iblog/category');
        $this->crud->setEntityNameStrings(trans('iblog::category.single'), trans('iblog::category.plural'));
        $this->access = [];
        $this->crud->enableAjaxTable();

        $this->crud->allowAccess('reorder');
        $this->crud->enableReorder('title', 2);

        /*
        |--------------------------------------------------------------------------
        | COLUMNS AND FIELDS
        |--------------------------------------------------------------------------
        */
        // ------ CRUD COLUMNS
        $this->crud->addColumn([
            'name' => 'id',
            'label' => 'ID',
        ]);


        $this->crud->addColumn([
            'name' => 'title',
            'label' => trans('iblog::common.title'),
        ]);

        $this->crud->addColumn([
            'name' => 'parent_id',
            'label' => trans('iblog::common.parent'),
            'type' => 'select',
            'entity' => 'parent',
            'attribute' => 'title',
            'model' => 'Modules\Iblog\Entities\Category',
            'defaultvalue' => '0'
        ]);


        $this->crud->addColumn([
            'name' => 'created_at',
            'label' => trans('iblog::common.created_at'),
        ]);

        // ------ CRUD FIELDS
        $this->crud->addField([
            'name' => 'title',
            'label' => trans('iblog::common.title'),
            'viewposition' => 'left'

        ]);

        $this->crud->addField([
            'name' => 'slug',
            'label' => 'Slug',
            'type' => 'text',
            'viewposition' => 'left'

        ]);

        $this->crud->addField([
            'name' => 'parent_id',
            'label' => trans('iblog::common.parent'),
            'type' => 'select',
            'entity' => 'parent',
            'attribute' => 'title',
            'model' => 'Modules\Iblog\Entities\Category',
            'viewposition' => 'right',
            'emptyvalue'=>0
        ]);


        $this->crud->addField([
            'name' => 'description',
            'label' => trans('iblog::common.description'),
            'type' => 'wysiwyg',
            'viewposition' => 'left'

        ]);


        $this->crud->addField([
            'name' => 'admin_notes',
            'label' => trans('iblog::common.admin_notes'),
            'type' => 'wysiwyg',
            'fake' => true,
            'store_in' => 'options',
            'viewposition' => 'right'
        ]);



    }

    public function edit($id) {

        parent::edit($id);

        // $this->data['thumbnail']= $this->file->findFileByZoneForEntity('thumbnail', $this->data['entry']);

        return view('iblog::admin.edit', $this->data);

    }

    public function create() {

        parent::create();

        return view('iblog::admin.create', $this->data);

    }
    public function show($id=null) {

        parent::show($id=null);

        return view('iblog::admin.show', $this->data);

    }

    public function setup()
    {
        parent::setup();

        $permissions = ['index', 'create', 'edit', 'destroy'];
        $allowpermissions = ['show'];
        foreach($permissions as $permission) {

            if($this->auth->hasAccess("iblog.categories.$permission")) {
                if($permission=='index') $permission = 'list';
                if($permission=='edit') $permission = 'update';
                if($permission=='destroy') $permission = 'delete';
                $allowpermissions[] = $permission;
            }

            $allowpermissions[] = 'reorder';

        }

        $this->crud->access = $allowpermissions;
    }

    public function store(IblogRequest $request)
    {
        return parent::storeCrud();
    }



    public function update(IblogRequest $request)
    {
        return parent::updateCrud($request);
    }


}
