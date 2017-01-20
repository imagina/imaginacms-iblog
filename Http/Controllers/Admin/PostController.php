<?php

namespace Modules\Iblog\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Iblog\Entities\Post;
use Modules\Iblog\Entities\Tag;
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
        //$this->crud->enableAjaxTable();


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
            'name' => 'summary',
            'label' => 'Summary',
            'type' => 'wysiwyg',
            'viewposition' => 'left',
        ]);

        $this->crud->addField([
            'name' => 'description',
            'label' => 'Content',
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
            'type' => 'select2_multiple',
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
                1 => "Pending",
                2 => "Published",
                3 => "Unpublished"
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

    public function store(IblogRequest $request) {
        //se modifica el valor de tags para agregar los nuevos registros
        $request['tags'] = $this->addTags($request['tags']);

        return parent::storeCrud($request);
    }

    public function update(IblogRequest $request) {
        //return public_path();
        //se modifica el valorde tags para agregar los nuevos registros
        //return $request->file('galery')->store(public_path() . '/assets/iblog/post/category');
        //return dd($request);
        $request['tags'] = $this->addTags($request['tags']);

        return parent::updateCrud($request);
    }

    public function uploadGalleryimage(IblogRequest $request){

        $idpost                 = $request->input('idedit');
        $extension              = $request->file('file')->extension();
        $extensionpermissions   = array('JPG','JPEG','PNG','GIF');

        if(!in_array(strtoupper($extension), $extensionpermissions)) {
            return 0;
        }

        $nameimag           = encrypt(rand()) . '.' . $extension;
        $destination_path   = 'assets/iblog/post/gallery/' . $idpost . '/'. $nameimag;

        $request->file('file')->storeAs('assets/iblog/post/gallery/' . $idpost , '/'. $nameimag, 'publicmedia');

        return array('direccion'=> $destination_path);
    }

    public function deleteGalleryimage(IblogRequest $request) {
        $disk       = "publicmedia";
        $dirdata    = $request->input('dirdata');
        \Storage::disk($disk)->delete($dirdata);
        return array('success' => true);
    }


    /**
     * Devuelve un arreglo con los viejos tags y los tags nuevos
     * para ser insertados o actualizados en el registro post
     */
    function addTags($tags){
        $tags       = $tags;
        $newtags    = Array();
        $lasttagsid = Array();
        $newtagsid  = Array();
        //se verifica si se evvio tags desde el form
        if(!empty($tags)){
            //se recorren todos lostags en busca de alguno nuevo
            foreach ($tags as $tag) {
                //si el tag no existe se agrega al array de de nuevos tags
                if(count(Tag::find($tag)) <= 0){
                    array_push($newtags,$tag);
                }else{
                //si el tag existe se agrega en un array de viejos tags
                    array_push($lasttagsid,$tag);
                }
            }
        }
        //se crean todos los tags que no existian
        foreach ($newtags as $newtag) {
            $modeltag = new Tag;
            $modeltag->title = $newtag;
            $modeltag->slug = str_slug($newtag,'-');
            $modeltag->save();
            array_push($newtagsid,$modeltag->id);
        }
        //se modifica el valor tags enviado desde el form uniendolos visjos tags y los tags nuevos
        return array_merge($lasttagsid,$newtagsid);
    }

}
