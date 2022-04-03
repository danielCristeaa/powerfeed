<?php


namespace App\Services;

use App\Models\Feed;
use Google\Exception;
use Google\Client;

class GoogleMerchantService
{
    private function underscoreToCamelCase($string, $capitalizeFirstCharacter = false)
    {
        $str = str_replace('_', '', ucwords($string, '_'));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }

    public function sendToGoogleMerchant($feedId)
    {
        $feed = Feed::where('_id', $feedId)->first();

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
            throw $e;
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
                    throw new Exception(json_encode(['error' => ['errors' => [['message' => "Invalid field: $field"]]]]));
                }
            }
            try {
                $product->setPrice($price);

                $merchant->products->insert($feed->merchantID, $product);
            } catch( \Exception $e) {
                throw new Exception($e->getMessage());
            }

        }
    }
}
