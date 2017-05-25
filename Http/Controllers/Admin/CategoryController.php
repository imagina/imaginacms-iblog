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
        $this->crud->orderBy('created_at', 'DESC');
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
        $this->crud->addField([ // image
            'label' => trans('iblog::common.image'),
            'name' => "mainimage",
            'type' => 'image',
            'upload' => true,
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 0, // ommit or set to 0 to allow any aspect ratio
            'fake' => true,
            'store_in' => 'options',
            'viewposition' => 'right',
        ]);

    }

    public function edit($id) {

        parent::edit($id);
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
               //Let's update the image for the post.
        if (!empty($request['mainimage']) && !empty($request['id'])) {
            $request['mainimage'] = $this->saveImage($request['mainimage'], "assets/iblog/category/" . $request['id'] . ".jpg");
        }
        return parent::updateCrud($request);
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

        //Imagina- Defaults?
        $requestimage = $request["mainimage"];

        //Put a default image while we save.
        $request["mainimage"] = "assets/iblog/category/default.jpg";


        // insert item in the db
        $item = $this->crud->create($request->except(['redirect_after_save', '_token']));
        $this->data['entry'] = $this->crud->entry = $item;



        //Let's save the image for the post.
        if(!empty($requestimage && !empty($item->id))) {
            $mainimage = $this->saveImage($requestimage,"assets/iblog/category/".$item->id.".jpg");

            $item->update($this->crud->compactFakeFields(['mainimage'=>$mainimage]));
            //TODO: i don't like the re-save. Find another way to do it.
        }

        // show a success message
        //\Alert::success(trans('bcrud::crud.insert_success'))->flash();

        // redirect the user where he chose to be redirected
        switch ($request->input('redirect_after_save')) {
            case 'current_item_edit':
                return \Redirect::to($this->crud->route.'/'.$item->getKey().'/edit');

            default:
                return \Redirect::to($request->input('redirect_after_save'));
        }



    }

}
