<?php

namespace App\Models;

use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Aws\DynamoDb\Marshaler;
use Serializable;



class DynamoMusic
{
    protected static $dynamoDb;
    protected static $tableName = 'music';

    public static function getDynamoDbClient()
    {
        if (!self::$dynamoDb) {
            self::$dynamoDb = app('aws')->createClient('dynamodb');
        }
        return self::$dynamoDb;
    }

    public static function findByTitle($title)
    {
        $client = self::getDynamoDbClient();
        $marshaler = new Marshaler();

        $response = $client->getItem([
            'TableName' => self::$tableName,
            'Key' => $marshaler->marshalItem(['title' => $title]),
        ]);

        if (!isset($response['Item'])) {
            return null;
        }

        return new self($marshaler->unmarshalItem($response['Item']));
    }
    
    public static function showAllMusic()
    {
        $client = self::getDynamoDbClient();
        $marshaler = new Marshaler();

        $response = $client->scan([
            'TableName' => self::$tableName,
        ]);

        $items = [];

        foreach ($response['Items'] as $item) {
            $items[] = $marshaler->unmarshalItem($item);
        }

        return $items;
    }

    public static function createItem(array $data)
    {
        $client = self::getDynamoDbClient();
        $marshaler = new Marshaler();
        $item = $marshaler->marshalItem($data);

        try {
            $client->putItem([
                'TableName' => self::$tableName,
                'Item' => $item,
            ]);

            $user = new DynamoUser($data);

            return $user;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getAuthIdentifierName()
    {
        return 'email';
    }

    public function getAuthPasswordName()
    {
        return 'password';
    }

    public function getAuthIdentifier()
    {
        return $this->attributes['email'] ?? null;
    }

    public function getAuthPassword()
    {
        return $this->attributes['password'] ?? null;
    }

    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // $this->attributes['remember_token'] = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function serialize()
    {
        return serialize($this->attributes);
    }

    public function unserialize($data)
    {
        $this->attributes = unserialize($data);
    }

    public static function unsubscribe($title, $album)
    {
        try {
            $email = auth()->user()->email;

            $client = DynamoUser::getDynamoDbClient();
            $marshaler = new Marshaler();

            $result = $client->getItem([
                'TableName' => 'Login',
                'Key' => $marshaler->marshalItem(['email' => $email]),
            ]);

            if (!isset($result['Item'])) {
                return false;
            }

            $user = $marshaler->unmarshalItem($result['Item']);
            $subscriptions = $user['subscriptions'] ?? [];

            $updatedSubscriptions = array_filter($subscriptions, function ($sub) use ($title, $album) {
                return !($sub['title'] === $title && $sub['album'] === $album);
            });

            $client->updateItem([
                'TableName' => 'Login',
                'Key' => $marshaler->marshalItem(['email' => $email]),
                'UpdateExpression' => 'SET subscriptions = :subs',
                'ExpressionAttributeValues' => $marshaler->marshalItem([
                    ':subs' => array_values($updatedSubscriptions) 
                ]),
            ]);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public static function query($title = null, $album = null, $artist = null, $year = null)
    {

        $client = self::getDynamoDbClient();
        $marshaler = new Marshaler();

        if ($artist !== null) {
            $queryResult = self::queryMusicByArtist($client, $marshaler, $artist, $title, $album, $year);
            
            if (!empty($queryResult)) {
                return $queryResult;
            } else {
                return self::scanMusic($client, $marshaler, $title, $album, $artist, $year);
            }
        } else {
            return self::scanMusic($client, $marshaler, $title, $album, $artist, $year);
        }
    }

    private static function queryMusicByArtist($client, $marshaler, $artist, $title = null, $album = null, $year = null)    
    {
        $keyConditionExpression = 'artist = :artist';
        $expressionAttributeValues = [':artist' => ['S' => $artist]];
        $expressionAttributeNames = [];
        $filterExpressions = [];

        if ($title !== null) {
            $filterExpressions[] = 'contains(title, :title)';
            $expressionAttributeValues[':title'] = ['S' => $title];
        }
        
        if ($album !== null) {
            $filterExpressions[] = 'contains(album, :album)';
            $expressionAttributeValues[':album'] = ['S' => $album];
        }
        
        if ($year !== null) {
            $filterExpressions[] = 'contains(#year, :year)';
            $expressionAttributeNames['#year'] = 'year';
            $expressionAttributeValues[':year'] = ['S' => $year];
        }

        $params = [
            'TableName' => 'music',
            'IndexName' => 'artist_index',
            'KeyConditionExpression' => $keyConditionExpression,
            'ExpressionAttributeValues' => $expressionAttributeValues
        ];
        
        if (!empty($filterExpressions)) {
            $params['FilterExpression'] = implode(' AND ', $filterExpressions);
            
            if (!empty($expressionAttributeNames)) {
                $params['ExpressionAttributeNames'] = $expressionAttributeNames;
            }
        }
        
        try {
            $result = $client->query($params);
            
            $items = [];
            foreach ($result['Items'] as $item) {
                $items[] = $marshaler->unmarshalItem($item);
            }
            
            return $items;
        } catch (Exception $e) {
            return false;
        }
    }

    private static function scanMusic($client, $marshaler, $title = null, $album = null, $artist = null, $year = null)
    {
        $filterExpressions = [];
        $expressionAttributeValues = [];
        $expressionAttributeNames = [];
        
        if ($title !== null) {
            $filterExpressions[] = 'contains(title, :title)';
            $expressionAttributeValues[':title'] = ['S' => $title];
        }
        
        if ($album !== null) {
            $filterExpressions[] = 'contains(album, :album)';
            $expressionAttributeValues[':album'] = ['S' => $album];
        }
        
        if ($artist !== null) {
            $filterExpressions[] = 'contains(artist, :artist)';
            $expressionAttributeValues[':artist'] = ['S' => $artist];
        }
        
        if ($year !== null) {
            $filterExpressions[] = 'contains(#year, :year)';
            $expressionAttributeNames['#year'] = 'year';
            $expressionAttributeValues[':year'] = ['S' => $year];
        }
        
        $params = [
            'TableName' => 'music',
        ];
        
        if (!empty($filterExpressions)) {
            $params['FilterExpression'] = implode(' AND ', $filterExpressions);
            $params['ExpressionAttributeValues'] = $expressionAttributeValues;
            
            if (!empty($expressionAttributeNames)) {
                $params['ExpressionAttributeNames'] = $expressionAttributeNames;
            }
        }
        
        try {
            $result = $client->scan($params);
            
            $items = [];
            foreach ($result['Items'] as $item) {
                $items[] = $marshaler->unmarshalItem($item);
            }
            
            return $items;
        } catch (Exception $e) {
            return false;
        }
    }
}


