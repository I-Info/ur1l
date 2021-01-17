# ur1l
A open short URL service.     
*in developing.*

-------------
### 一期开发已完工，待优化。
由于前期并发性能要求不大，暂且以php+Redis架构开发。   
以后可能会用JAVA+Redis（+MySQL）架构开发。

---------------
### API/config.php
此文件在项目中不可见。需要自行添加。。。

|--变量--|--说明--|
|--|--|
|$recaptcha_key|Google-recaptcha的服务器端token|   
|$min_score|人机验证的最小通过值（0-1）|   
|$redis_host|redis的ip|   
|$redis_port|redis的端口|   
|$base|生成hash字符取值|   

文档也还待补充。。。
