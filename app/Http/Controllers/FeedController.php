<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $user_id = Auth::id();
        $feeds = Feed::select('_id', 'name')->where('user_id', $user_id)->get()->toArray();

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
        $user_id = Auth::id();
        $feeds = Feed::select('_id', 'name')->where('user_id', $user_id)->get()->toArray();

        return $feeds;
    }

    private function parseXML($xml)
    {
        foreach ($xml->children() as $item)
        {
            $hasChild = count($item->children()) > 0;
            if(!$hasChild)
            {
                $data = $this->parseXML($item);
            }
            $data[] = $item;
        }
        return $data;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     */
    public function create(Request $request)
    {
        $xml = simplexml_load_file($request->input('url'));
        //TODO: check if url exists and is xml
        // ...
        $xml_data = $this->parseXML($xml);
        $feed = new Feed();
        $feed->name = $request->input('name');
        $feed->url = $request->input('url');
        $feed->user_id = Auth::id();
        $temp_array = [];
        foreach($xml_data as $key => $item)
        {
            $temp_array[$key] = $item;
        }
        $feed->products = $temp_array;
        $feed->save();

        return redirect()->route('feeds');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function show(Feed $feed)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function edit(Feed $feed)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     */
    public function renameColumn(Request $request, $id)
    {
        $updatedFeed = Feed::where('_id', $id)->first();
        $oldColumnName = $request->input('oldName');
        $newColumnName = $request->input('newName');

        $tempArray = [];
        foreach($updatedFeed->products as $product) {
            $arrayKeys = array_keys($product);
            $oldKeyIndex = array_search($oldColumnName, $arrayKeys);
            $arrayKeys[$oldKeyIndex] = $newColumnName;

            $newArray = array_combine($arrayKeys, $product);
            $tempArray[] = $newArray;
        }

        $updatedFeed->products = $tempArray;
        $updatedFeed->save();
        return json_encode($updatedFeed->products);
    }
    /**
     *
     * Update the specified resource in storage.
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

        $updatedFeed->products = $tempArray;
        $updatedFeed->save();
        return json_encode($updatedFeed->products);
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
