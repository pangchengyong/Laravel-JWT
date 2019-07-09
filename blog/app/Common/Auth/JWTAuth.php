<?php


namespace App\Common\Auth;


use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\ValidationData;

class JWTAuth
{
    /**
     * 最终存放token的变量
     * @var null
     */
    private $JWTToken = null;

    /**
     * 配置颁发者
     * @var string
     */
    private $issuer = 'http://www.guorangxiang.cn';

    /**
     * 用户的id
     */
    private $uid = null;

    /**
     * 需要解析的token
     * @var
     */
    private $decodeToken = null;

    /**
     * 盐值 只有服务器有
     * @var string
     */
    private $secrect = 'SDQW^&%$#$@*(&*(^^&*%&I#Giy907124i+_@{}})a}{sdf5654(*32450897}{P:":L":LRTFYGHU687qweryu';

    /**
     * 配置ID
     * @var string
     */
    private $identifiedId = 'FTV^YBGUHNJIMKKOKLP#$%^*&*()}{":?>asdfa34567';

    /**
     * 访问群体
     * @var string
     */
    private $aud = 'http://www.server.org';

    //私有静态属性，存放该类的实例
    private static $_instalce = null;

    //公共的静态方法，实例化该类本身，只实例化一次
    public static function getInstance()
    {
        if (!self::$_instalce instanceof self) {
            self::$_instalce = new self();
        }
        return self::$_instalce;
    }

    //私有克隆方法，防止克隆
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    //私有构造方法，防止在类的外部实例化
    private function __construct()
    {

    }

    /**
     * token 编码
     * @return $this
     */
    public function encode()
    {
        $time = time();
        $signer = new Sha256();
        $this->JWTToken = (new Builder())
            // 配置颁发者
            ->issuedBy($this->issuer)
            // 配置访问群体
            ->permittedFor($this->aud)
            // 配置ID
            ->identifiedBy($this->identifiedId, true)
            // 令牌发出的时间
            ->issuedAt($time)
            // 配置token生成后多久可以使用 $time 表示生成就直接可以使用
            ->canOnlyBeUsedAfter($time)
            // 配置令牌的过期时间
            ->expiresAt($time + 3600)
            ->withClaim('uid', $this->uid)
            ->getToken($signer,new Key(md5($this->secrect)));
        return $this;
    }

    /**
     * 设置user id
     * @param int $user_id
     * @return $this
     */
    public function setUserId($user_id = 0)
    {
        $this->uid = $user_id;
        return $this;
    }

    /**
     * 设置token
     * @param $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->JWTToken = $token;
        return $this;
    }

    /**
     * 获取token
     * @return string
     */
    public function getToken()
    {
        return (string)$this->JWTToken;
    }

    /**
     * 解析token
     * @return \Lcobucci\JWT\Token|null
     */
    public function decode($token)
    {
        $this->setToken($token);

        if(!$this->decodeToken){
            $this->decodeToken=(new Parser())->parse((string)$this->JWTToken);
            $this->uid=$this->decodeToken->getClaim('uid');
        }
        return $this->decodeToken;
    }

    /**
     * 验证token 前两部分对不对 header payload
     * @param $token
     * @return bool
     */
    public function validate($token)
    {
        $data = new ValidationData();
        $data->setIssuer($this->issuer);
        $data->setAudience($this->aud);
        $data->setId($this->identifiedId);
        return $this->decode($token)->validate($data);
    }

    /**
     * 验证token盐值 主要看token有没有被串改 signature
     * @param $token
     * @return bool
     */
    public function verify($token)
    {
        $result = $this->decode($token)->verify(new Sha256(),new Key(md5($this->secrect)));
        return $result;
    }
}