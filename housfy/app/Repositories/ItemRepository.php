<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

use App\Jobs\CacheItems;
use App\Exceptions;

abstract class ItemRepository implements RepositoryInterface
{
    private $itemName;
    private $model;
    private $store;


    /**
     * Constructor.
     *
     * @param mixed $model
     * @param string $itemName
     * @return array Array of objects of the type of $this->model
     */
    public function __construct($model, $itemName)
    {
        $this->setModel($model);
        $this->setItemName($itemName);
        $this->setStore($itemName);
    }

    /**
     * Gets a listing of the resource.
     *
     * @return array Array of objects of the type of $this->model
     */
    public function all()
    {
        $items = $this->retrieveItem();
        return $items;
    }

    /**
     * Get a resource in storage.
     *
     * @param  int   $id
     * @return mixed Object of the type of $this->model
     */
    public function get($id)
    {
        $item = $this->retrieveItem($id);
        
        return $item;
    }

    /**
     * Creates a resource in storage.
     *
     * @param  array $data
     * @return mixed Object of the type of $this->model
     */
    public function create(array $data)
    {
        $this->validate($data);

        return $this->model->create($data);
    }

    /**
     * Updates a resource in storage.
     *
     * @param  int   $id
     * @param  array $data
     * @return mixed Object of the type of $this->model
     */
    public function update($id, array $data)
    {
        $item = $this->get($id);

        $dataUpdated = array_merge($item->toArray(),$data);
        
        $this->validate($dataUpdated);

        if(true !== $item->update($dataUpdated)) {
            throw new Exceptions\ApiException('Error Processing Request');
            
        }

        $this->cacheFlush($this->getCacheKey($id));

        return $item->refresh();
    }

    /**
     * Updates a resource in storage.
     *
     * @param  int   $id
     * @return bool
     */
    public function delete($id)
    {
        $result = $this->model->destroy($id);

        if($result) {
            $this->cacheFlush($this->getCacheKey($id));
        }

        return $result;
    }

    /**
     * Validates input data using the corresponding FormRequest.
     *
     * @param  array  $data
     * @return bool True
     * @throws ApiValidationFailedException
     */
    public function validate(array $data)
    {
        $validator = Validator::make($data, $this->store->rules());
        if($validator->fails()) {
            $errors = array();
            foreach($validator->errors()->getMessages() as $key => $values) {
                $errors[$key] = array_shift($values);
            }
            throw new Exceptions\ApiValidationFailedException('Validation failed for '.$this->itemName, $errors);
        }
        return true;
    }

    /**
     * Gets a cached resource in case it's already cached
     *  Otherwise, gets resource from DB and 
     *
     * @param  array  $data
     * @return bool True
     * @throws ApiItemNotFoundException
     */
    private function retrieveItem($id = null)
    {
        $key = (null === $id) ? $this->getCacheKey() : $this->getCacheKey($id);

        // If item exists in cache, return cached version
        if ($this->isCached($key)) {
            return $this->retrieveFromCache($key);
        }

        // If item doesn't exist in DB, throws an exception
        if(null === $item = $this->retrieveFromDatabase($id)) {
            throw new Exceptions\ApiItemNotFoundException($this->itemName.' not found');
        }

        // Add item to cache queue
        $this->queueToCache($key, $item);

        return $item;
    }

    /**
     * Gets resource from database
     *
     * @param  int|null  $id
     * @return mixed Resource|list of resources|null
     */
    private function retrieveFromDatabase($id = null)
    {
        // if $id is null, gets a list of registries
        if(null === $id) {
            $item = $this->model->all();
        // if $id is not null, gets the specific resource
        } else {
            $item = $this->model->find($id);
        }

        return $item;
    }

    /**
     * Gets resource from cache
     *
     * @param  string  $key
     * @return mixed Resource|list of resources
     */
    private function retrieveFromCache($key)
    {
        return Cache::get($key);
    }

    /**
     * Checks if resource is cached
     *
     * @param  string  $key
     * @return bool
     */
    private function isCached($key) {
        return Cache::has($key);
    }

    /**
     * Adds the resource to the cache queue
     *
     * @param  string  $key
     * @param  mixed   $item
     */
    private function queueToCache($key, $item)
    {
        CacheItems::dispatch($item, $key)->onQueue('cache');
    }

    /**
     * Flush a resource from cache
     *
     * @param  string  $key
     */
    private function cacheFlush($key)
    {
        Cache::forget($key); // flush an especific cached resource
        Cache::forget($this->getCacheKey()); // flush cached list, as flushed resource would be outdated 
    }

    /**
     * Setter for $itemName property
     *
     * @param  string  $itemName (model name)
     */
    private function setItemName($itemName)
    {
        $this->itemName = ucfirst($itemName);
    }

    /**
     * Setter for $model property
     *
     * @param  mixed  $model
     */
    private function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Setter for $store property
     *
     * @param  mixed  $itemName (model name)
     */
    private function setStore($itemName)
    {
        $storeClassname = '\App\Http\Requests\Store'.$itemName;
        $reflectionClass = new \ReflectionClass($storeClassname);
        $this->store = $reflectionClass->newInstance();
    }

    /**
     * Generates cache key for resource
     *
     * @param  int|null  $id Integer for resource ID; null for resource list
     * @return string
     */
    private function getCacheKey($id = null)
    {
        $key = $this->itemName.':';
        $key.= ($id !== null)  ? $id : 'all';

        return $key;
    }
}