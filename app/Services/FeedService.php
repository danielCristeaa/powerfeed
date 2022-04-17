<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Feed;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use silici0\GoogleParserFeed\ParserGoogleFeed;

class FeedService
{
    public function getFeeds() {
        $user = User::getLoggedInUser();

        $feeds = Feed::select('_id', 'name')->where('company_id', $user->company_id)->get();
        return $feeds;
    }

    public function getFeed($id) {
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

    public function delete($id) {
        $user = User::getLoggedInUser();

        try {
            Feed::where('_id', $id)->delete();
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    public function create($data) {
        $userId = Auth::id();
        if(!$userId) {
            return false;
        }

        $feed = new Feed();
        $feed->name = $data['name'];
        $feed->url = $data['url'];

        $feed->config = $data['config'];
        $feed->configFileName = $data['configFileName'];

        $feed->merchantID = $data['merchantID'];

        $user = User::where('_id', Auth::id())->first();
        $company = Company::where('_id', $user->company_id)->first();
        $feed->company_id = $company->id;
        $temp_array = [];

        foreach($data['xmlData']->toArray()['channel']->item as $item) {
            $temp_array[] = $item;
        }

        $originalFields = array_keys(get_object_vars($temp_array[0]));
        $originalFieldsTempArray = [];
        foreach($originalFields as $value) {
            $originalFieldsTempArray[$value] = $value;
        }

        $feed->fields = $originalFieldsTempArray;
        $feed->products = $temp_array;

        try {
            $feed->save();
        } catch(Exception $e) {
            return false;
        }

        return true;
    }

    public function update($data) {
        $updatedFeed = Feed::where('_id', $data['id'])->first();

        $updatedFeed->name = $data['name'];
        $updatedFeed->url = $data['url'];
        $updatedFeed->merchantID = $data['merchantID'];

        if($data['config']) {
            $updatedFeed->config = $data['config'];
            $updatedFeed->configFileName = $data['configFileName'];
        }

        if($data['addColumns'] > 0){
            $newFields = $updatedFeed->fields;
            foreach ($newFields as $field => $value) {
                if(strpos($field, 'new_column_') !== false) {
                    throw new Exception("Error! You you have to rename the $field column before adding another one");
                }
            }

            for($i = 0; $i < $data['addColumns']; $i++) {
                $newFields["new_column_$i"] = "new_column_$i";
            }

            $updatedFeed->fields = $newFields;

            $newProducts = $updatedFeed->products;
            foreach($newProducts as &$product) {
                for($i = 0; $i < $data['addColumns']; $i++) {
                    $product["new_column_$i"] = "";
                }
            }

            $updatedFeed->products = $newProducts;
        }

        try {
            $updatedFeed->save();
        } catch(Exception $e) {
            return false;
        }

        return true;
    }

    public function deleteColumn($deletedColumn, $id) {
        $updatedFeed = Feed::where('_id', $id)->first();

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
        try {
            $updatedFeed->save();
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    public function editColumn($data, $id) {
        $updatedFeed = Feed::where('_id', $id)->first();

        $originalFieldsTempArray = $updatedFeed->fields;
        $originalColumnName = array_search($data['columnName'], $originalFieldsTempArray);
        if(strpos($originalColumnName, 'new_column_') !== false) {
            $originalFieldsTempArray[$data['newColumnName']] = $data['newColumnName'];
            unset($originalFieldsTempArray[$originalColumnName]);
        }
        else {
            $originalFieldsTempArray[$originalColumnName] = $data['newColumnName'];
        }

        $tempArray = [];
        foreach($updatedFeed->products as $product) {
            $newArray = $product;
            foreach($newArray as $key => &$value) {
                if($key == $data['columnName']) {
                    if($data['replace'] == '' && $value == '') {
                        $value = $data['with'];
                    }
                    else {
                        $value = str_replace($data['replace'], $data['with'], $value);
                    }
                }
            }

            $arrayKeys = array_keys($newArray);
            $oldKeyIndex = array_search($data['columnName'], $arrayKeys);
            $arrayKeys[$oldKeyIndex] = $data['newColumnName'];

            $newArray = array_combine($arrayKeys, $newArray);
            $tempArray[] = $newArray;
        }

        $updatedFeed->products = $tempArray;
        $updatedFeed->fields = $originalFieldsTempArray;

        try {
            $updatedFeed->save();
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    public function refreshData($id) {
        $feed = Feed::where('_id', $id)->first();

        try {
            $xml = file_get_contents($feed->url);
        }
        catch (\Exception $e) {
            throw new Exception("URL no longer exists!");
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

        try {
            $feed->save();
            return true;
        } catch(Exception $e) {
            return false;
        }
    }
}
