<?php


namespace App\Http\Middleware;

use App\Models\Feed;
use App\Models\User;
use Google\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Google\Client;

class GoogleMerchantFeed
{
    private function underscoreToCamelCase($string, $capitalizeFirstCharacter = false)
    {
        $str = str_replace('_', '', ucwords($string, '_'));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }

    public function sendToGoogleMerchant(Request $request)
    {
        $feed = Feed::where('_id', $request->input('feedId'))->first();

        $jsonKey = [];

        foreach($feed->config as $field => $value)
        {
            $jsonKey[$field] = $value;
        }

        $client = new Client();
        try {
            $client->useApplicationDefaultCredentials();
            $client->setApplicationName($feed->name);
            $client->setScopes("https://www.googleapis.com/auth/content");
            $client->setAuthConfig($jsonKey);
        } catch (Exception $e) {
            return response()->json(['error' => "Error when authenticating to Google merchant. Check the JSON config file"]);
        }

        $merchant = new \Google_Service_ShoppingContent($client);

        foreach ($feed->products as $feedProduct)
        {
            $product = new \Google_Service_ShoppingContent_Product();
            $product->setChannel('online');
            $product->setTargetCountry("RO");
            $product->setContentLanguage('ro');

            $price = new \Google_Service_ShoppingContent_Price();
            foreach ($feedProduct as $field => $value)
            {
                try {
                    if(strpos($field, 'price') !== false)
                    {
                        $price->setValue(trim($value));
                        continue;
                    }

                    if(strpos($field, 'currency') !== false) {
                        $price->setCurrency($value);
                        continue;
                    }

                    $googleMethodName = "set".$this->underscoreToCamelCase($field, true);
                    $product->{$googleMethodName}($value);
                } catch(\Throwable $e) {
                    return response()->json(['error' => "Invalid field: $field"]);
                }
            }
            try {
                $product->setPrice($price);

                $merchant->products->insert($feed->merchantID, $product);
            } catch( \Exception $e) {
                $message = json_decode($e->getMessage())->error->message;
                return response()->json(['error' => $message]);
            }

        }
        return response()->json(['success' => "Successfully published feed!"]);
    }
}
