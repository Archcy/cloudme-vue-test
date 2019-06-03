# Cloudme-Vue后端文件夹
如要正常使用，请关闭php的安全模式，或允许执行openssl，否则分享功能不可用。
这个问题会在未来的版本中加以修改和修复

## 配置信息
服务器端请允许跨站请求，并允许"APPFUNC"头
例如nginx的配置
```
add_header 'Access-Control-Allow-Origin' '*';
add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS';
add_header 'Access-Control-Allow-Headers' 'Authorization,Origin,User-Agent,X-Requested-With,Cache-Control,Content-Type,APPFUNC';
add_header 'Access-Control-Expose-Headers' 'Content-Length,Content-Range';
```
请允许php的文件上传，并酌情修改最大文件数量和文件大小

数据库为database.sql

## special thanks

vuejs-uploader