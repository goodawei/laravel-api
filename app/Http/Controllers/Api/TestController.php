<?php

/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/11/14
 * Time: 11:06
 */

namespace App\Http\Controllers\Api;

use App\Tools\Transformer\UserTransformer;
use App\User;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class TestController extends BaseController
{
    use Helpers;

    public function test(){
        //throw new \Symfony\Component\HttpKernel\Exception\ConflictHttpException('User was updated prior to your request.');
        //throw new \Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException('cam ');

        $users = User::paginate(3);

        return $this->response->item($users, new UserTransformer)->setStatusCode(200);

        return $this->response->item($users, new UserTransformer)->addMeta('foo', 'bar');

        return $this->response->item($users, new UserTransformer)->withHeader('X-Foo', 'Bar');

        return $this->response->errorUnauthorized();

        return $this->response->errorInternal();

        return $this->response->errorForbidden();

        return $this->response->errorBadRequest();

        return $this->response->errorNotFound();

        return $this->response->error('This is an error.', 404);

        return $this->response->created();
        return $this->response->noContent();


        return $this->response->paginator($users, new UserTransformer);

        $users = User::all();

        return $this->response->collection($users, new UserTransformer);

        $user = User::findOrFail(1);
        return $this->response->item($user, new UserTransformer);

        $user = User::findOrFail(1);

        return $this->response->array($user->toArray());

    }



    //                              jwt                          //


    /**
     * 用户名 密码获取 token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTokenByEmailAndPwd(Request $request){

        $credentials = $request->only('email', 'password');
        try {

            $token = JWTAuth::attempt($credentials) ;

            if(! $token){
                return response()->json(['error' => 'invalid_credentials'], 401);
            }

        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }

    /**
     * 获取token使用自定义数组
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTokenByObj(Request $request){

          //使用对象方式

          $users = new User();
          $user = User::first();
//        $token = JWTAuth::fromUser($user);
//        return response()->json(compact('token'));

        //使用自定义数组 + 用户名 密码
          $customClaims = ['foo' => 'bar', 'baz' => 'bob'];
//        $credentials = $request->only('email', 'password');
//        $token = JWTAuth::attempt($credentials, $customClaims);
//        return response()->json(compact('token'));

        //使用自定义数组 + 对象

        $token = JWTAuth::fromUser($user, $customClaims);
        return response()->json(compact('token'));

    }


    /**
     * 遇到问题了，JWTFactory 已经配置别名，但是仍然不可用
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTokenCustm(){

        $payload = JWTFactory::sub(123)->aud('foo')->foo(['bar' => 'baz'])->make();

        $token = JWTAuth::encode($payload);

        return response()->json(compact('token'));

        $customClaims = ['foo' => 'bar', 'baz' => 'bob'];

        $payload = JWTFactory::make($customClaims);

        $token = JWTAuth::encode($payload);

        return response()->json(compact('token'));
    }


    /**
     * Authorization:Bearer {eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmb28iOiJiYXIiLCJiYXoiOiJib2IiLCJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL21hcGkuZGV2XC9nZXQtdG9rZW4tb2JqIiwiaWF0IjoxNDc5MjAwMzQzLCJleHAiOjE0NzkyMDM5NDMsIm5iZiI6MTQ3OTIwMDM0MywianRpIjoiZTQ3OGY0ZDExNTVhZTE3ZTFmNzZlMWMzZDY5ODA3YzUifQ.Dfb_vJIkK05X2swylles7lv5znNQqGcTfdyGwa6Plrs}
     * Content-Type : application/json
     * 验证token
     * @return \Illuminate\Http\JsonResponse
     */
    public function authorization(){
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
        return response()->json(compact('user'));
    }

}