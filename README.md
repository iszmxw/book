# book
**微信小说系统**
> 一.环境要求：操作系统/win或linux、PHP/5.4x、mysql/5.5x、Apache/2.4.x、开启openssl扩展。

> 二.修改数据库配置文件,路经如下\Application\Common\Conf\db.php

> 三.修改漫画书内容详情图片地址前缀,路经如下\Application\Common\Conf\config.php

> 四.导入数据库文件mysql.sql。

> 五.平台默认操作地址以及帐户密码
```text
1.总后台访问地址:http://你的域名/xwcms.php   默认账号:admin  默认密码:123456

2.代理后台访问地址:http://你的域名/daili.php
```
> 六.对接微信公众号:
```text
1.登陆微信公众号-设置-公众号设置-功能设置,填写业务域名、JS接口安全域名、网页授权域名；

2.微信公众号-设置-安全中心-设置ip白名单（即你平台所在服务器的ip）

3.微信公众号-开发-基本配置-公众号开发信息，查看并记录AppID、AppSecret，填去您小说漫画平台总后台-系统设置-公众号设置处。
PS：该处的商户号、支付密钥与API证书上传为申请微信支付后要填写的配置，无申请官方微信支付的不需要填写。

4.微信公众号-开发-基本配置-服务器配置，填写说明如下：
  服务器地址(URL)：http://你的域名/index.php?c=Api
  令牌(Token)：（自由填写）
  消息加解密密钥(EncodingAESKey)：（随机生成）
```
>七.其他第三方服务商联系方式
```text
敬请注意：您与以下任何一家第三方服务商的一切行为均与本商家无关。

1.随意云储存：http://www.suiyiyun.cn      （提供图片、视频云储存服务）

2.短信宝：http://www.smsbao.com       （提供短信服务）

3.ewopay（第三方支付免签代收款通道，支持全行业接入服务商）（提供微信支付、支付宝方式代收款）

4.U支付（多账户个人二维码收款通道服务商）：（多账户个人微信/支付宝二维码方式收款通道服务）

5.码支付（个人二维码收款通道服务商）：https://codepay.fateqq.com  （个人微信/支付宝二维码方式收款通道服务）

6.优云宝（个人二维码收款通道服务商）：https://wvv.com.cn （个人微信/支付宝二维码方式收款通道服务）

7.payspai（个人二维码收款通道服务商）：https://www.paysapi.com （个人微信/支付宝二维码方式收款通道服务）
```

