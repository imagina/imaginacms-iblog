<?php

namespace Modules\Iblog\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Iblog\Entities\tag;
use Modules\Iblog\Http\Requests\IblogRequest;

use Modules\Bcrud\Http\Controllers\BcrudController;
use Modules\User\Contracts\Authentication;

class tagController extends BcrudController
{
    /**
     * @var tagRepository
     */
    private $tag;
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
        $this->crud->setModel('Modules\Iblog\Entities\Tag');
        $this->crud->setRoute('backend/iblog/tag');
        $this->crud->setEntityNameStrings('tag', 'tags');
        $this->access = [];

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
            'label' => 'Title',
        ]);



        // ------ CRUD FIELDS
        $this->crud->addField([
            'name' => 'title',
            'label' => 'Title',
        ]);

        $this->crud->addField([
            'name' => 'slug',
            'label' => 'Slug',
            'type' => 'text',
        ]);


        $this->crud->addField([
            'name' => 'description',
            'label' => 'Description',
            'type' => 'textarea',
        ]);



    }

    public function setup()
    {
        parent::setup();

        $permissions = ['index', 'create', 'edit', 'destroy'];
        $allowpermissions = [];
        foreach($permissions as $permission) {

            if($this->auth->hasAccess("iblog.posts.$permission")) {
                if($permission=='index') $permission = 'list';
                if($permission=='edit') $permission = 'update';
                if($permission=='destroy') $permission = 'delete';
                $allowpermissions[] = $permission;
            }

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
