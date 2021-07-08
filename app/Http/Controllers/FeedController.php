<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Feed;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use silici0\GoogleParserFeed\ParserGoogleFeed;

class FeedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $user = User::where('_id', Auth::id())->first();
        $feeds = Feed::select('_id', 'name')->where('company_id', $user->company_id)->get()->toArray();

        return view('home', ['feeds' => $feeds]);
    }

    public function getFeed($id)
    {
        $feed = DB::collection('feeds')->where('_id', $id)->first();
        if($feed == null) {
            return [];
        }

        $columns = [];
        foreach($feed['products'][0] as $property => $value) {
            $columns[] = $property;
        }
        return [$columns, $feed['products'], $feed['name'], $feed['url'], $feed['config'], $feed['configFileName'], $feed['merchantID']];
    }

    public function getUserFeeds()
    {
        $user = User::where('_id', Auth::id())->first();
        $feeds = Feed::select('_id', 'name')->where('company_id', $user->company_id)->get()->toArray();

        return $feeds;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     */
    public function create(Request $request)
    {
        set_time_limit(300);

        if($request->missing('name')) {
            return response()->json(['error' => "Missing name!"]);
        }
        if($request->missing('url')) {
            return response()->json(['error' => "Missing URL"]);
        }

        try {
            $file = $request->file('file');
            $content = file_get_contents($file->getRealPath());
        }
        catch(\Throwable $e) {
            return response()->json(['error' => "Missing JSON config file!"]);
        }

        if($request->missing('merchantId')) {
            return response()->json(['error' => "Missing merchantID"]);
        }

        try {
            $xml = file_get_contents($request->input('url'));
            $xml_data = new ParserGoogleFeed($xml, LIBXML_NOCDATA);
        }
        catch (\Exception $e) {
            return response()->json(['error' => "Invalid URL!"]);
        }

        $feed = new Feed();
        $feed->name = $request->input('name');
        $feed->url = $request->input('url');

        $json_content = json_decode($content);
        $feed->config = $json_content;
        $feed->configFileName = $request->input('fileName');

        $feed->merchantID = $request->input('merchantId');

        $user = User::where('_id', Auth::id())->first();
        $company = Company::where('_id', $user->company_id)->first();
        $feed->company_id = $company->id;
        $temp_array = [];

        foreach($xml_data->toArray()['channel']->item as $item) {
            $temp_array[] = $item;
        }

        $originalFields = array_keys(get_object_vars($temp_array[0]));
        $originalFieldsTempArray = [];
        foreach($originalFields as $value) {
            $originalFieldsTempArray[$value] = $value;
        }

        $feed->fields = $originalFieldsTempArray;
        $feed->products = $temp_array;
        $feed->save();

        return response()->json(['success' => "Successfully added a feed!"]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        $updatedFeed = Feed::where('_id', $id)->first();

        $updatedFeed->name = $request->input('name');
        $updatedFeed->url = $request->input('url');
        $updatedFeed->merchantID = $request->input('merchantId');

        $file = $request->file('file');
        if($file) {
            $content = file_get_contents($file->getRealPath());
            $json_content = json_decode($content);
            $updatedFeed->config = $json_content;
            $updatedFeed->configFileName = $request->input('fileName');
        }

        if($request->input('addColumns') > 0){
            $newFields = $updatedFeed->fields;
            foreach ($newFields as $field => $value) {
                if(strpos($field, 'new_column_') !== false) {
                    return response()->json(['error' => "Error! You you have to rename the $field column before adding another one"]);
                }
            }

            for($i = 0; $i<$request->input('addColumns'); $i++) {
                $newFields["new_column_$i"] = "new_column_$i";
            }

            $updatedFeed->fields = $newFields;

            $newProducts = $updatedFeed->products;
            foreach($newProducts as &$product) {
                for($i = 0; $i<$request->input('addColumns'); $i++) {
                    $product["new_column_$i"] = "";
                }
            }

            $updatedFeed->products = $newProducts;
        }

        $updatedFeed->save();
        return response()->json(['success' => "Successfully edited the feed!"]);
    }

    /**
     *
     *
     * @param Request $request
     * @param $id
     */
    public function deleteColumn(Request $request, $id)
    {
        $updatedFeed = Feed::where('_id', $id)->first();
        $deletedColumn = $request->input('column');

        $tempArray = [];
        foreach($updatedFeed->products as $product) {
            $newArray = $product;
            unset($newArray[$deletedColumn]);
            $tempArray[] = $newArray;
        }

        $originalFieldsTempArray = $updatedFeed->fields;
        foreach($originalFieldsTempArray as $key => $value) {
            if($value == $deletedColumn) {
                unset($originalFieldsTempArray[$key]);
            }
        }

        $updatedFeed->products = $tempArray;
        $updatedFeed->fields = $originalFieldsTempArray;
        $updatedFeed->save();

        return json_encode($updatedFeed->products);
    }

    /**
     *
     *
     * @param Request $request
     * @param $id
     */
    public function editColumn(Request $request, $id)
    {
        $updatedFeed = Feed::where('_id', $id)->first();

        $replace = urldecode($request->input('replace'));
        $with = urldecode($request->input('with'));
        $columnName = $request->input('columnName');

        $oldColumnName = $request->input('oldName');
        $newColumnName = $request->input('newName');

        $originalFieldsTempArray = $updatedFeed->fields;
        $originalColumnName = array_search($oldColumnName, $originalFieldsTempArray);
        if(strpos($originalColumnName, 'new_column_') !== false) {
            $originalFieldsTempArray[$newColumnName] = $newColumnName;
            unset($originalFieldsTempArray[$originalColumnName]);
        }
        else {
            $originalFieldsTempArray[$originalColumnName] = $newColumnName;
        }

        $tempArray = [];
        foreach($updatedFeed->products as $product) {
            $newArray = $product;
            foreach($newArray as $key => &$value) {
                if($key == $columnName) {
                    if($replace == '' and $value == '') {
                        $value = $with;
                    }
                    else {
                        $value = str_replace($replace, $with, $value);
                    }
                }
            }

            $arrayKeys = array_keys($newArray);
            $oldKeyIndex = array_search($oldColumnName, $arrayKeys);
            $arrayKeys[$oldKeyIndex] = $newColumnName;

            $newArray = array_combine($arrayKeys, $newArray);
            $tempArray[] = $newArray;
        }

        $updatedFeed->products = $tempArray;
        $updatedFeed->fields = $originalFieldsTempArray;
        $updatedFeed->save();
        return json_encode($updatedFeed->products);
    }

    public function refreshFeedData($id)
    {
        $feed = Feed::where('_id', $id)->first();
        try {
            $xml = file_get_contents($feed->url);
        }
        catch (\Exception $e) {
            return response()->json(['error' => "URL no longer exists!"]);
        }

        $xml_data = new ParserGoogleFeed($xml, LIBXML_NOCDATA);

        $tempArray = [];
        foreach($xml_data->toArray()['channel']->item as $item) {
            $tempArray[] = $item;
        }

        $productsWithEditedFields = [];
        foreach($tempArray as $item) {
            $newArray = [];
            foreach($item as $key => $value) {
                if(!in_array($key, array_keys($feed->fields))) {
                    continue;
                }
                $newArray[$feed->fields[$key]] = $value;
            }
            $productsWithEditedFields[] = $newArray;
        }

        foreach($feed->fields as $field => $value) {
            foreach($productsWithEditedFields as &$product) {
                if(array_key_exists($value, $product) == false) {
                    foreach($feed->products as $feedProduct) {
                        if(array_key_exists('offer_id', $feedProduct)) {
                            if($feedProduct['offer_id'] == $product['offer_id']){
                                if(array_key_exists($value, $feedProduct)) {
                                    $product[$value] = $feedProduct[$value];
                                }
                                else {
                                    $product[$value] = "";
                                }
                            }
                        }
                        elseif(array_key_exists('id', $feedProduct)) {
                            if($feedProduct['id'] == $product['id']){
                                if(array_key_exists($value, $feedProduct)) {
                                    $product[$value] = $feedProduct[$value];
                                }
                                else {
                                    $product[$value] = "";
                                }
                            }
                        }
                    }
                }
            }
        }
        $feed->products = $productsWithEditedFields;
        $feed->save();

        return response()->json(['success' => "Successfully refreshed data!"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param integer $id
     */
    public function delete($id)
    {
        $userId = Auth::id();
        if(!$userId) {
            return;
        }

        return Feed::where('_id', $id)->delete();
    }
}
