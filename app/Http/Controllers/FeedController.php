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
        return [$columns, $feed['products']];
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
     * @param  \Illuminate\Http\Request  $request
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Feed $feed)
    {
        //
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
