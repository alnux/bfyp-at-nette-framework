<?php namespace App\Common\Model;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use \Nette\Utils\Strings,
    \Nette\Security\Passwords,
    \Nette\Utils\Random;

use App\Config\TablesSettings;

class UserModel extends BaseModel{

    protected $table = TablesSettings::T_USER;

    /**
     * Const for the sign up, sign ip and banned users
     */
    const STATUS_PENDING = 5;
    const STATUS_ACTIVE = 10;
    const STATUS_BANNED = 15;

    /**
     * Const for the free user or paid
     */
    const TYPE_STATE = 3;


    private $user;

    private $settings;
    
    public function __construct(\Nette\DI\Container $container)
    {
        parent::__construct($container);
        $this->settings = $container->getService('appsettings');
    }


    public function isUsernameTaken($username)
    {
        return !(bool)$this->customField($username,'username')->fetch();
    }

    public function isEmailTaken($email)
    {
        return !(bool)$this->customField($email,'email')->fetch();
    }

    public function UserByEmail($email)
    {
        return $this->customField($email, 'email')->fetch();
    }

    public function findByValidationToken($hash)
    {
        return $this->customField(Strings::match($hash, '/[A-Za-z0-9_-]+/')[0], 'auth_key')->fetch();
    }

    public function authUserOnDb($username, $password, $appkey)
    {
        $check = FALSE;
        if($user = $this->customField($username, 'username')->fetch())
        {
            if($this->verifyPasswordHash($password,$user->password_hash))
            {
                $this->user = $user;
                $check = $user;
            }
            foreach($this->customFieldAndT($user->id, 'user_id', TablesSettings::T_USER_ROLE) as $row)
            {
                if($row->verification != sha1($row->user_id.$appkey.$row->role_id))
                {
                    $check = FALSE;
                }
            }
        }
        return $check;
    }

    public function getUserRoles()
    {
        $roles = $this->context->fetchPairs('SELECT r.id, r.key_name
            FROM ' . TablesSettings::T_ROLE . ' AS r
			RIGHT JOIN ' . TablesSettings::T_USER_ROLE . ' AS us ON r.id=us.role_id
			WHERE us.user_id=?;', $this->user->id);
        
        if($this->user->username == $this->settings->getParam('superuser'))
        {
            $roles[0] = $this->settings->getParam('superuser');
        }        
        return $roles;
    }

    public function generatePasswordHash($password)
    {
        return Passwords::hash($password);
    }

    public function verifyPasswordHash($password, $hash)
    {
        return Passwords::verify($password, $hash);
    }

    /**
     * Generates specified number of random bytes.
     * Note that output may not be ASCII.
     * @see generateRandomString() if you need a string.
     *
     * @param integer $length the number of bytes to generate
     * @return string the generated random bytes
     * @throws Exception on failure.
     */
    public function generateRandomKey($length = 32)
    {
        return Random::generate($length);
    }

    /**
     * Generates a random string of specified length.
     * The string generated matches [A-Za-z0-9_-]+ and is transparent to URL-encoding.
     *
     * @param integer $length the length of the key in characters
     * @return string the generated random key
     * @throws Exception on failure.
     */
    public function generateRandomString($length = 32)
    {
        $bytes = $this->generateRandomKey($length);
        return strtr(substr(base64_encode($bytes), 0, $length), '+/', '_-');
    }

    /**
     * Generates new password reset token
     * broken into 2 lines to avoid wordwrapping
     */
    public function generatePasswordResetToken()
    {
        return $this->generateRandomString()  . '_' . time();
    }


    public function isPasswordResetTokenValid($token)
    {

        if (empty($token))
        {
            return false;
        }

        if ($user = $this->customField(Strings::match($token, '/[A-Za-z0-9_-]+/')[0], 'password_reset_token')->fetch())
        {
            $parts = explode('_', $token);
            $timestamp = (int) end($parts);
            if($timestamp + 3600 < time())
            {
                return false;
            }
            return $user;
        }
        else
        {
            return false;
        }

    }


    public function findByPasswordResetToken($token)
    {
        return $this->isPasswordResetTokenValid($token);
    }
}