<?php

namespace Modules\Iblog\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Iblog\Entities\Post;
use Modules\Iblog\Http\Requests\IblogRequest;
use Modules\Media\Repositories\FileRepository;

use Modules\Bcrud\Http\Controllers\BcrudController;
use Modules\User\Contracts\Authentication;
use Illuminate\Contracts\Foundation\Application;


class PostController extends BcrudController
{
    /**
     * @var PostRepository
     */
    private $post;
    private $auth;
    private $file;

    public function __construct(Authentication $auth, FileRepository $file)
    {
        parent::__construct();

        $this->auth = $auth;
        $this->file= $file;
        $driver = config('asgard.user.config.driver');

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('Modules\Iblog\Entities\Post');
        $this->crud->setRoute('backend/iblog/post');
        $this->crud->setEntityNameStrings('post', 'posts');
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
        $this->crud->addColumn([
            'name' => 'categories', // The db column name
            'label' => 'Category',// Table column heading
            'type' => 'select_multiple',
            'attribute' => 'title',
            'entity' => 'categories',
            'model' => "Modules\\Iblog\\Entities\\Category", // foreign key model
            'pivot' => true,
        ]);
        $this->crud->addColumn([
            'name' => 'created_at',
            'label' => 'Created',
        ]);

        // ------ CRUD FIELDS
        $this->crud->addField([
            'name' => 'title',
            'label' => 'Title',
            'viewposition' => 'left',

        ]);
        $this->crud->addField([       // Select2Multiple = n-n relationship (with pivot table)
            'label' => 'Slug',
            'type' => 'text',
            'name' => 'slug', // the method that defines the relationship in your Model
            'viewposition' => 'left',
        ]);

        $this->crud->addField([
            'name' => 'description',
            'label' => 'Description',
            'type' => 'wysiwyg',
            'viewposition' => 'left',
        ]);
        $this->crud->addField([
            'name' => 'summary',
            'label' => 'Summary',
            'type' => 'wysiwyg',
            'viewposition' => 'left',
        ]);

        $this->crud->addField([       // Select2Multiple = n-n relationship (with pivot table)
            'label' => 'Categories',
            'type' => 'checklist',
            'name' => 'categories', // the method that defines the relationship in your Model
            'entity' => 'categories', // the method that defines the relationship in your Model
            'attribute' => 'title', // foreign key attribute that is shown to user
            'model' => "Modules\\Iblog\\Entities\\Category", // foreign key model
            'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
            'viewposition' => 'right',
        ]);

        $this->crud->addField([       // Select2Multiple = n-n relationship (with pivot table)
            'label' => 'Tags',
            'type' => 'tags',
            'name' => 'tags', // the method that defines the relationship in your Model
            'entity' => 'tags', // the method that defines the relationship in your Model
            'attribute' => 'title', // foreign key attribute that is shown to user
            'model' => "Modules\\Iblog\\Entities\\Tag", // foreign key model
            'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
            'viewposition' => 'right',
        ]);

        $this->crud->addField([  // Select
            'label' => "Author",
            'type' => 'select',
            'name' => 'user_id', // the db column for the foreign key
            'entity' => 'user', // the method that defines the relationship in your Model
            'attribute' => 'email', // foreign key attribute that is shown to user
            'model' => "Modules\\User\\Entities\\{$driver}\\User", // foreign key model,
            'viewposition' => 'right',
        ]);

        $this->crud->addField([
            'name'        => 'status',
            'label'       => 'Status',
            'type'        => 'radio',
            'options'     => [
                0 => "Draft",
                1 => "Published",
                2 => "Program",
            ],
            'viewposition' => 'right',
        ]);
        $this->crud->addField([ // image
            'label' => "Imagen",
            'name' => "mainimage",
            'type' => 'image',
            'upload' => true,
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 0, // ommit or set to 0 to allow any aspect ratio
            'fake' => true,
            'store_in' => 'options',
            'viewposition' => 'left',
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
        $allowpermissions = [];
        foreach($permissions as $permission) {

            if($this->auth->hasAccess("iblog.categories.$permission")) {
                if($permission=='index') $permission = 'list';
                if($permission=='edit') $permission = 'update';
                if($permission=='destroy') $permission = 'delete';
                $allowpermissions[] = $permission;
            }
            if($this->auth->hasAccess("iblog.tags.$permission")) {
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
