<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\ApiResourceNotFoundException;

class WebserviceController extends Controller
{
    
	private $resourceWhitelist = ['office'];

    /** 
     * Accessed API resource
     * @var string
     */
    private $resourceName;

    private $repository;

    public function __construct(Request $request)
    {
        $this->resourceName = ucfirst($request->route()->parameter('resource'));

        if(!$this->isWhitelisted() || null === $this->setRepository()) {
			throw new ApiResourceNotFoundException();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->repository->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	$response = $this->repository->create($request->all());
        return response($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($resource, $id)
    {
        return $this->repository->get($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $resource, $id)
    {
        return $this->repository->update($id, $request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($resource, $id)
    {
        $this->repository->delete($id);
        return response(null, 204);
    }

    private function isWhitelisted()
    {
    	return in_array(strtolower($this->resourceName), $this->resourceWhitelist);
    }

    private function setRepository()
    {
        $modelClassname = '\\App\\Models\\'.$this->resourceName;
        $repositoryClassname = '\\App\\Repositories\\'.$this->resourceName.'Repository';

        return (class_exists($repositoryClassname)) ? $this->repository = new $repositoryClassname(new $modelClassname) : null;
    }
}