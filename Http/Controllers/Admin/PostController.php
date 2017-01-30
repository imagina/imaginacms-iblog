<?php

namespace Modules\Iblog\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Iblog\Entities\Post;
use Modules\Iblog\Entities\Status;
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
        $this->crud->setEntityNameStrings(trans('iblog::post.single'), trans('iblog::post.plural'));
        $this->access = [];
        $this->crud->enableAjaxTable();
        $this->crud->orderBy('created_at', 'DESC');
        $this->crud->limit(100);





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
            'name' => 'category_id', // The db column name
            'label' => trans('iblog::category.single'),// Table column heading
            'type' => 'select',
            'attribute' => 'title',
            'entity' => 'category',
            'model' => "Modules\\Iblog\\Entities\\Category", // foreign key model
        ]);
        $this->crud->addColumn([
            'name' => 'created_at',
            'label' => trans('iblog::common.created_at'),
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
            'type' => 'textarea',
            'attributes' => ['rows'=>'6'],
            'viewposition' => 'left',
        ]);

        $this->crud->addField([
            'name' => 'description',
            'label' => 'Content',
            'type' => 'wysiwyg',
            'viewposition' => 'left',
        ]);

        $this->crud->addField([  // Select
            'label' => "Default Category",
            'type' => 'select',
            'name' => 'category_id', // the db column for the foreign key
            'entity' => 'categories', // the method that defines the relationship in your Model
            'attribute' => 'title', // foreign key attribute that is shown to user
            'model' => "Modules\\Iblog\\Entities\\Category", // foreign key model
            'viewposition' => 'right',
            'nullable'=>false
        ]);


        $this->crud->addField([       // Select2Multiple = n-n relationship (with pivot table)
            'label' => 'Categories',
            'type' => 'categories_checklist',
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
                0 => trans('iblog::common.status.draft'),
                1 => trans('iblog::common.status.pending'),
                2 => trans('iblog::common.status.published'),
                3 => trans('iblog::common.status.unpublished')
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

        return $this->storeCrud($request);

    }

    public function update(IblogRequest $request) {

        //Let's update the image for the post.
        if(!empty($request['mainimage']) && !empty($request['id'])) {
            $request['mainimage'] = $this->saveImage($request['mainimage'],"assets/iblog/post/".$request['id'].".jpg");
        }


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




    public function saveImage($value,$destination_path)
    {

        $disk = "publicmedia";

        //Defined return.
        if(ends_with($value,'.jpg')) {
            return $value;
        }

        // if a base64 was sent, store it in the db
        if (starts_with($value, 'data:image'))
        {
            // 0. Make the image
            $image = \Image::make($value);
            // resize and prevent possible upsizing

            $image->resize(config('asgard.iblog.config.imagesize.width'), config('asgard.iblog.config.imagesize.height'), function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path, $image->stream('jpg','80'));


            // Save Thumbs
            \Storage::disk($disk)->put(
                str_replace('.jpg','_mediumThumb.jpg',$destination_path),
                $image->fit(config('asgard.iblog.config.mediumthumbsize.width'),config('asgard.iblog.config.mediumthumbsize.height'))->stream('jpg','80')
            );

            \Storage::disk($disk)->put(
                str_replace('.jpg','_smallThumb.jpg',$destination_path),
                $image->fit(config('asgard.iblog.config.smallthumbsize.width'),config('asgard.iblog.config.smallthumbsize.height'))->stream('jpg','80')
            );

            // 3. Return the path
            return $destination_path;
        }

        // if the image was erased
        if ($value==null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($destination_path);

            // set null in the database column
            return null;
        }


    }


    /*
     * We had to replace the parent::storeCrud function to save the image at the moment of creation with the id.
     */

    public function storeCrud(\Modules\Bcrud\Http\Requests\CrudRequest $request = null)
    {
        $this->crud->hasAccessOrFail('create');

        // fallback to global request instance
        if (is_null($request)) {
            $request = \Request::instance();
        }

        // replace empty values with NULL, so that it will work with MySQL strict mode on
        foreach ($request->input() as $key => $value) {
            if (empty($value) && $value !== '0') {
                $request->request->set($key, null);
            }
        }

        $requestimage = $request["mainimage"];

        //Put a default image while we save.
        $request["mainimage"] = "assets/iblog/post/default.jpg";

        // insert item in the db
        $item = $this->crud->create($request->except(['redirect_after_save', '', '_token']));

        //Let's save the image for the post.
        if(!empty($requestimage && !empty($item->id))) {
            $mainimage = $this->saveImage($requestimage,"assets/iblog/post/".$item->id.".jpg");

            $item->update($this->crud->compactFakeFields(['mainimage'=>$mainimage], 'update'));
            //TODO: i don't like the re-save. Find another way to do it.

        }


        // redirect the user where he chose to be redirected
        switch ($request->input('redirect_after_save')) {
            case 'current_item_edit':
                return \Redirect::to($this->crud->route.'/'.$item->getKey().'/edit')->withSuccess(trans('bcrud::crud.update_success'));

            default:
                return \Redirect::to($request->input('redirect_after_save'))->withSuccess(trans('bcrud::crud.update_success'));
        }
    }

}
