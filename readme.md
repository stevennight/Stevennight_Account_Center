# Stevennight Account Center -- Stevennight 账户中心

[TOC]

## 简介

​	这是个集成了账号注册，账号登录，账号信息修改，邮箱验证，账号找回以及第三方授权的账户中心 Laravel 应用。既然是 Laravel 应用，使用的当然是 Laravel 框架而写的。其中，登录以及第三方授权没有使用自带的模块进行编写，可能多多少少都会存在问题，欢迎提出。另外，第三方授权只是一个简单的模仿，没有进行权限的区分，所以任何的授权的第三方，能做所有API提供的行为，比如获取用户信息（不包括密码）以及用户的头像，暂时也就这两个功能。再者就是，暂时没有进行后台的编写，配置以及管理起来还是挺费劲的，期待后台的出现=w=。

## 使用说明

### 环境要求

- PHP版本 >= 5.6.4
- PHP扩展：OpenSSL
- PHP扩展：PDO
- PHP扩展：Mbstring
- PHP扩展：Tokenizer

*以上要求，是Laravel所需要的PHP环境要求。

另外我们还需要的还有：

Laravel 支持的数据库中其中的一种数据库，

- MySQL
- Postgres
- SQLite
- SQL Server

以及一台支持SMTP发送邮件的邮件服务器，用于邮箱验证以及密码找回这些需要邮件发送的功能。

### 初始化配置

1、将文件打包下载下来，并放到你想要放置的地方。

2、将网站的根目录只想文件夹中的public目录即可。

3、创建一个.env文件，配置范例可以查看.env example文件。需要配置的有数据库的信息，smtp服务器的信息等等。

4、打开命令行，进入该应用的根目录并输入以下命令进行生成app key。

```cmd
php artisan key:generate
```

5、输入以下命令初始化数据库。

```cmd
php artisan migrate
```

执行以上步骤之后，并且正确配置了网站之后，通常我们就可以浏览该应用的网页了，而不是输出一些错误信息。

### 配置网站设置

因为没有做好后台，所以……网站的配置需要在数据库中进行。以后会尽量补上。

*注意：默认会创建一个用户名为：administrator，密码为：admin12345678的用户，为了安全起见，请在配置之后，立即登录该用户进行密码的修改。（该用户暂时没有什么作用…但以后可能涉及后台等管理员权限。）*

#### 1、网站的全局配置

​	该配置在(数据表前缀)config_global_website表内。

- id: 不用管。

- name: 网站的名字。

- email: 网站邮箱，要注意的是，有时候这个邮箱必须和smtp设置中登录的邮箱相同，避免像腾讯邮件服务器等邮箱会辨别这个发送者邮箱发现其中不一致无法成功发送邮件。

- email_send_interval: 账号的邮件发送间隔，建议设置。

- email_token_expire: 邮箱发送的token过期间隔。token会在该时间段内失效。

- oauth_auth_code_expire: 第三方授权auth code的过期间隔。通常时间不用太长。

- oauth_access_token_expire: 第三方授权access token的过期间隔。

- oauth_update_token_expire: 第三方授权update token的过期间隔。这个是在access token的间隔上往上增加的时间间隔。比如access token 设置的为1天，update token设置为1天，那么算起来2天后update token 将会过期。

- files_path: 文件目录。比如头像上传等文件所在目录。需要在\public\文件夹内建立一个\storage\app的软连接。结尾必须加上‘/’。

- avatar_default: 默认头像的所在位置。

  以上关于时间的配置，单位都为秒(s)。

#### 2、友情链接的配置

在(数据表前缀)links数据表中，增加(insert)友情链接的条目就可以了。

- id: 不用管。
- name: 友情链接标题，显示的文字。
- link: 友情链接指向的路径。

#### 3、第三方授权的配置

第三方授权配置，主要是增加允许访问授权的客户端。

增加一个客户端，只要在(数据表前缀)oauth_clients这个表中添加(insert)条目即可。

- id: 不用自行填写。增加之后，等到了相应的值给予客户端即可。
- user_id: 暂时没有任何作用，填写0即可。
- name: 客户端的名字，展现给用户的客户端称呼。
- secret: 客户端的密钥。绝对不可以公开！
- userurl: 主要是可以填写客户端的主页链接。
- redirect: 进行回调给客户端的一个链接。任何可用的链接都可以，但是千万不要忘记给予一个{authcode}，这个将是给客户端传递一个auth code的重要参数！
- created_at: 可留空。暂时无用。
- updated_at: 可留空。暂时无用。



## 更新日志

2017/02/17

初次完整更新好了。包括这个readme。

## License

[GNU General Public License, version 3](LICENSE)