<?php

namespace App\Models;

use Aws\DynamoDb\Exception\DynamoDbException;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Aws\DynamoDb\Marshaler;
use Serializable;



class DynamoUser implements Authenticatable
{
    protected $attributes = [];
    protected static $dynamoDb;
    protected static $tableName = 'login';

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public static function getDynamoDbClient()
    {
        if (!self::$dynamoDb) {
            self::$dynamoDb = app('aws')->createClient('dynamodb');
        }
        return self::$dynamoDb;
    }

    public static function findByEmail($email)
    {
        $client = self::getDynamoDbClient();
        $marshaler = new Marshaler();

        $response = $client->getItem([
            'TableName' => self::$tableName,
            'Key' => $marshaler->marshalItem(['email' => $email]),
        ]);

        if (!isset($response['Item'])) {
            return null;
        }

        return new self($marshaler->unmarshalItem($response['Item']));
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
        } catch (\Exception $e) {
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
                'TableName' => 'login',
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
                'TableName' => 'login',
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

    public static function subscribe($song){
        $client = DynamoUser::getDynamoDbClient();
        $marshaler = new Marshaler();

        $tableName = 'login';
        $email = auth()->user()->email; 

        $newSubscription = [
            'title' => $song['title'],
            'year' => $song['year'],
            'artist' => $song['artist'],
            'album' => $song['album'],
            'img_url' => $song['img_url'],
        ];

        try {
            $result = $client->getItem([
                'TableName' => $tableName,
                'Key' => $marshaler->marshalItem([
                    'email' => $email,
                ])
            ]);

            $item = $result['Item'] ?? null;

            if (!$item) {
                throw new \Exception('User not found.');
            }

            $userData = $marshaler->unmarshalItem($item);
            $subscriptions = $userData['subscriptions'] ?? [];

            $exists = collect($subscriptions)->contains(function ($sub) use ($newSubscription) {
                return $sub['title'] === $newSubscription['title'] &&
                    $sub['album'] === $newSubscription['album'];
            });

            if ($exists) {
                logger()->info('Duplicate subscription not added.');
                return; 
            }

            $subscriptions[] = $newSubscription;

            $client->updateItem([
                'TableName' => $tableName,
                'Key' => $marshaler->marshalItem([
                    'email' => $email,
                ]),
                'UpdateExpression' => 'SET subscriptions = :subs',
                'ExpressionAttributeValues' => [
                    ':subs' => $marshaler->marshalValue($subscriptions),
                ]
            ]);

            logger()->info('Subscription added successfully.');
            return true;

        } catch (DynamoDbException $e) {
            logger()->error('DynamoDB Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            logger()->error('General Error: ' . $e->getMessage());
        }
    }
}


