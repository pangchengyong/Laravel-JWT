# Laravel-JWT
采用Laravel最新版5.8实现JWT用户登录认证分配Token

##互联网用户认证流程
1、用户向服务器发送用户名和密码。  
2、服务器验证通过后，在当前对话（session）里面保存相关数据，比如用户角色、登录时间等等。  
3、服务器向用户返回一个 session_id，写入用户的 Cookie。  
4、用户随后的每一次请求，都会通过 Cookie，将 session_id 传回服务器。  
5、服务器收到 session_id，找到前期保存的数据，由此得知用户的身份。  

## 原始认证模式存在的问题  
`扩展性不好` 单机没有问题，如果是服务器集群就需要session共享，每台服务器都需要读取到session。    
1.解决办法：session数据持久化，写入数据库或者其他一些持久层，搭建session共享服务器，优点：架构清晰，缺点：工程量大。另外session服务器还得做双机备份，避免session服务器挂掉用户登录失败。  
2.服务器不保存session，所有数据都放入到客户端保存，每次请求都发回到服务器，例如JWT。

## `JWT 的原理`
服务器根据提交的用户名和密码认证后会生成一个token发回给客户端。  
以后用户与服务器通信都需要携带这个token，服务器就不保存任何 session 数据了。  
也就是说，服务器变成无状态了，从而比较容易实现扩展。  
## JWT的组成
`Header（头部）` `Payload（负载）` `Signature（签名）`  
### Header
Header 部分是一个 JSON 对象，描述 JWT 的元数据
```$json
{
  "alg": "HS256", //alg属性表示签名的算法（algorithm），默认是 HMAC SHA256（写成 HS256）
  "typ": "JWT" //typ属性表示这个令牌（token）的类型（type），JWT 令牌统一写为JWT
}
```
### Payload
Payload 部分也是一个 JSON 对象，用来存放实际需要传递的数据。  
JWT 规定了7个官方字段，供选用。  
iss (issuer)：签发人   
exp (expiration time)：过期时间  
sub (subject)：主题  
aud (audience)：受众  
nbf (Not Before)：生效时间  
iat (Issued At)：签发时间  
jti (JWT ID)：编号  
除了官方字段，你还可以在这个部分定义私有字段，下面就是一个例子。  
```$json
{
  "sub": "1234567890",
  "name": "John Doe",
  "admin": true
}
```
JWT 默认是不加密的，任何人都可以读到，所以不要把秘密信息放在这个部分。  
## Signature
Signature 部分是对前两部分的签名，防止数据篡改。  
生成token需要指定一个密钥（secret）。  
这个密钥只有服务器才知道，不能泄露给用户。  
然后，使用 Header 里面指定的签名算法（默认是 HMAC SHA256），按照下面的公式产生签名。  
```$xslt
HMACSHA256(
    base64UrlEncode(header) + "." +
    base64UrlEncode(payload),
    secret
  )
```
算出签名以后，把 Header、Payload、Signature 三个部分拼成一个字符串，每个部分之间用"点"（.）分隔，就可以返回给用户。  
`一般生成签名后在加密一次发送给client`
## JWT 的几个特点
（1）JWT 默认是不加密，但也是可以加密的。生成原始 Token 以后，可以用密钥再加密一次。  
（2）JWT 不加密的情况下，不能将秘密数据写入 JWT。  
（3）JWT 不仅可以用于认证，也可以用于交换信息。有效使用 JWT，可以降低服务器查询数据库的次数。  
（4）JWT 的最大缺点是，由于服务器不保存 session 状态，因此无法在使用过程中废止某个 token，或者更改 token 的权限。也就是说，一旦 JWT 签发了，在到期之前就会始终有效，除非服务器部署额外的逻辑。  
（5）JWT 本身包含了认证信息，一旦泄露，任何人都可以获得该令牌的所有权限。为了减少盗用，JWT 的有效期应该设置得比较短。对于一些比较重要的权限，使用时应该再次对用户进行认证。  
（6）为了减少盗用，JWT 不应该使用 HTTP 协议明码传输，要使用 HTTPS 协议传输。  



