<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\BaseController as BaseController;
use Validator;
use App\Http\Resources\Destination as DestinationResource;

class DestinationController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $destinations = Destination::all();

        return $this->sendResponse(DestinationResource::collection($destinations), 'Destinations retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'category' => 'required',
            'detail' => 'required',
            'price' => 'required',
            'location' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            'rating' => 'required',
            'picture' => 'mimes:png,jpeg|max:5120'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $file = $request->file('picture');
        $name = '/pictures/' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storePubliclyAs('public', $name);

        $data['name'] = $input['name'];
        $data['category'] = $input['category'];
        $data['detail'] = $input['detail'];
        $data['price'] = $input['price'];
        $data['location'] = $input['location'];
        $data['longitude'] = $input['longitude'];
        $data['latitude'] = $input['latitude'];
        $data['rating'] = $input['rating'];
        $data['picture'] = $name;

        $destination = Destination::create($data);

        return $this->sendResponse(new DestinationResource($destination), 'Destination added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Destination  $destination
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $destination = Destination::find($id);

        if (is_null($destination)) {
            return $this->sendError('Destination not found');
        }

        return $this->sendResponse(new DestinationResource($destination), 'Destinations retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Destination  $destination
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Destination $destination)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'category' => 'required',
            'detail' => 'required',
            'price' => 'required',
            'location' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        } else {
            $destination->name = $input['name'];
            $destination->category = $input['category'];
            $destination->detail = $input['detail'];
            $destination->price = $input['price'];
            $destination->location = $input['location'];
            $destination->save();

            return $this->sendResponse(new DestinationResource($destination), 'Destination updated successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Destination  $destination
     * @return \Illuminate\Http\Response
     */
    public function destroy(Destination $destination)
    {
        $destination->delete();

        return $this->sendResponse([], 'Destination deleted successfully.');
    }

    /**
     * Search the specified resource from storage.
     *
     * @param  \App\Models\Destination  $destination
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $data = $request->get('search');

        $search = Destination::where('name', 'like', "%{$data}%")
                    ->orWhere('location', 'like', "%{$data}%")
                    ->get();
        
        return $this->sendResponse(DestinationResource::collection($search), 'Destinations retrieved successfully.');
    }

    /**
     * Show image from storage.
     *
     * @param  \App\Models\Destination  $destination
     * @return \Illuminate\Http\Response
     */
    public function picture($fileName){
        $path = Storage::disk('pictures/' . $filename);

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }
}
