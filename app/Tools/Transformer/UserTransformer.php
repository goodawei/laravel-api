<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/11/14
 * Time: 13:51
 */

namespace App\Tools\Transformer;

use Dingo\Api\Http\Request;
use Dingo\Api\Transformer\Binding;
use Dingo\Api\Contract\Transformer\Adapter;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(\App\User $user)
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
        ];
    }


}