<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use Illuminate\Http\Request;
use silici0\GoogleParserFeed\ParserGoogleFeed;
use App\Services\FeedService;
use Exception;

class FeedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        return view('home');
    }

    // Get feed data as an array
    public function getFeed($id)
    {
        $feedService = new FeedService();
        $feed = $feedService->getFeed($id);
        if(empty($feed)) {
            return response()->json(['success' => false, 'message' => 'Error when getting the feed!']);
        }

        return response()->json(['success' => true, 'message' => '', 'data' => $feed]);
    }

    public function getUserFeeds()
    {
        $feedService = new FeedService();

        $feeds = $feedService->getFeeds()->toArray();
        if(empty($feeds)) {
            return response()->json(['success' => false, 'message' => 'Error when getting the feeds!']);
        }
        return response()->json(['success' => true, 'message' => '', 'data' => $feeds]);
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
            return response()->json(['success' => false, 'message' => "Missing name!"]);
        }
        if($request->missing('url')) {
            return response()->json(['success' => false, 'message' => "Missing URL"]);
        }

        try {
            $file = $request->file('file');
            $content = file_get_contents($file->getRealPath());
        }
        catch(\Throwable $e) {
            return response()->json(['success' => false, 'message' => "Missing JSON config file!"]);
        }

        if($request->missing('merchantId')) {
            return response()->json(['success' => false, 'message' => "Missing merchantID"]);
        }

        try {
            $xml = file_get_contents($request->input('url'));
            $xml_data = new ParserGoogleFeed($xml, LIBXML_NOCDATA);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Invalid URL!"]);
        }

        $feedService = new FeedService();
        $data = [
            'name' => $request->input('name'),
            'url' => $request->input('url'),
            'config' => json_decode($content),
            'configFileName' => $request->input('fileName'),
            'merchantID' => $request->input('merchantId'),
            'xmlData' => $xml_data
        ];

        $response = $feedService->create($data);

        if(!$response) {
            return response()->json(['success' => false, 'message' => "Error when adding a feed!"]);
        }

        return response()->json(['success' => true, 'message' => "Successfully added a feed!"]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     */
    public function update(Request $request, $id)
    {
        $feedService = new FeedService();
        $data = [
            'id' => $id,
            'name' => $request->input('name'),
            'url' => $request->input('url'),
            'merchantID' => $request->input('merchantId'),
            'addColumns' => $request->input('addColumns')
        ];

        $file = $request->file('file');
        if($file) {
            $content = file_get_contents($file->getRealPath());
            $json_content = json_decode($content);
            $data['config'] = $json_content;
            $data['configFileName'] = $request->input('fileName');
        }

        try {
            $response = $feedService->update($data);
            if($response) {
                return response()->json(['success' => true, 'message' => "Successfully edited the feed!"]);
            }
            else {
                return response()->json(['success' => false, 'message' => 'Error when updating the feed!']);
            }
        } catch(Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
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
        $feedService = new FeedService();
        $response = $feedService->deleteColumn($deletedColumn, $id);

        if($response) {
            return response()->json(['success' => true, 'message' => "Successfully deleted the column!"]);
        }
        else {
            return response()->json(['success' => true, 'message' => "Error when deleting the column!"]);
        }
    }

    /**
     *
     *
     * @param Request $request
     * @param $id
     */
    public function editColumn(Request $request, $id)
    {
        $data = [
            'replace' => urldecode($request->input('replace')),
            'with' => urldecode($request->input('with')),
            'columnName' => $request->input('columnName'),
            'newColumnName' => $request->input('newName'),
        ];

        $feedService = new FeedService();

        $response = $feedService->editColumn($data, $id);

        if($response) {
            return response()->json(['success' => true, 'message' => "Successfully edited the feed!"]);
        }
        else {
            return response()->json(['success' => false, 'message' => 'Error when editing the feed!']);
        }
    }

    public function refreshFeedData($id)
    {
        $feedService = new FeedService();
        try {
            $response = $feedService->refreshData($id);
            if($response) {
                return response()->json(['success' => true, 'message' => "Successfully refreshed the feed!"]);
            }
            else {
                return response()->json(['success' => false, 'message' => 'Error when refreshing the feed!']);
            }
        } catch(Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        return response()->json(['success' => "Successfully refreshed data!"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $feedService = new FeedService();

        try {
            $response = $feedService->delete($id);

            if($response) {
                return response()->json(['success' => true, 'message' => "Successfully deleted the feed!"]);
            }
            else {
                return response()->json(['success' => false, 'message' => 'Error when deleting the feed!']);
            }
        } catch(Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
