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
        $columns = [];
        foreach($feed['products'][0] as $property => $value) {
            $columns[] = $property;
        }
        return [$columns, $feed['products'], $feed['name'], $feed['url']];
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
        $xml = file_get_contents($request->input('url'));
        //TODO: check if url exists and is xml
        // ...
        $xml_data = new ParserGoogleFeed($xml, LIBXML_NOCDATA);

        $feed = new Feed();
        $feed->name = $request->input('name');
        $feed->url = $request->input('url');

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
        return redirect()->route('feeds');
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

        $updatedFeed->save();
        return json_encode($updatedFeed);
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
        unset($originalFieldsTempArray[$deletedColumn]);

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

        $replace = $request->input('replace');
        $with = $request->input('with');
        $columnName = $request->input('columnName');

        $oldColumnName = $request->input('oldName');
        $newColumnName = $request->input('newName');

        $originalFieldsTempArray = $updatedFeed->fields;
        $originalColumnName = array_search($oldColumnName, $originalFieldsTempArray);
        $originalFieldsTempArray[$originalColumnName] = $newColumnName;

        $tempArray = [];
        foreach($updatedFeed->products as $product) {
            $newArray = $product;
            foreach($newArray as $key => &$value) {
                if($key == $columnName) {
                    $value = str_replace($replace, $with, $value);
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

        $xml = file_get_contents($feed->url);
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

        $feed->products = $productsWithEditedFields;
        $feed->save();
        return json_encode($productsWithEditedFields);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feed $feed)
    {
        //
    }
}
