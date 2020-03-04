<?php

namespace App\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class User
 * @package App\Model
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'email', 'active', 'username', 'RID', 'email_verified_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * User constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        if(!is_array($attributes)) {
            $attributes = (array)$attributes;
        }
        parent::__construct($attributes);
    }

    /**
     * @return false|string
     */
    public function getLastLogin()
    {
        $loginTry = DB::table('login_try')
            ->where('username_hash', '=', sha1($this->getAttribute('username')))
            ->orderByDesc('created_at');

        if($loginTry->count() > 0) {
            $loginData = $loginTry->first();
            return date('d.m.Y H:i:s', $loginData->created_at);
        }

        return false;
    }

    /**
     * @return string
     */
    public function getRoleName()
    {
        $user = DB::table('user_role')->where('id', '=', $this->getAttribute('RID'));

        if($user->count() === 1) {
            $userData = $user->first();
            return $userData->name;
        }

        return 'guest';
    }
}
