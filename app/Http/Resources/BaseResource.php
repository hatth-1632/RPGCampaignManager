<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
	public static $wrap = null;

	protected function handleIncludes($request)
    {
        $includes = explode(',',$request->include);
        $supported = array_intersect($includes, $this->includes);

        $results = [];
        foreach($supported as $inc) {
            $data = $this->$inc;

            if (empty($data)) {
            	$results[$inc] = null;
           	} elseif(sizeof($data) == 0){
           		$results[$inc] = [];
            } else {
            	// Is collection? 
	            $resource = $data->first()->getResouce();
	            $results[$inc] = $resource::collection($data);;
	            //or singular
	            // //new $resource($data->first())
	            //
            }
        }

        return $results;
    }

    public function toArray($request)
    {
    	$attributes = $this->attributes($request);
    	$includes = $this->handleIncludes($request);
    	$links = $this->links($request);
    	
    	// Make Json:Api like payload
    	$id = $attributes['id'];
    	$type = empty($attributes['type']) ? class_basename($this->resource) : $attributes['type'];
    	unset($attributes['id']);
    	unset($attributes['type']);

    	$data = array_merge($attributes, $includes);

    	return [
            'id' => $id,
            'type' => $type,
            'data' => $data,
            'links' => $links
        ];

    	return array_merge($data, $includes);
    }

}