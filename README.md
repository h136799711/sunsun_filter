###森森硬件
项目服务器：

公司服务器

api地址
devsunsun.8raw.com 

public 文件夹
appdowload.php => app下载地址
index.php => 接口请求入口
web.php => 网页模块访问入口
test.php => 暂无特殊意义

//缺少参数的调用
LangHelper::lackParameter('position')

#### require:
composer
thinkphp5.0.7
itboye_sunsun_tcp
#### 文件夹权限:
public/upload 写入权限
runtime 写入权限
open_basedir 权限
#### 开启函数:
scandir,
proc_open,

####启动部署:
1. 过滤桶 FilterVatTask 定时调用用于推送邮件故障消息，发布邮件任务 以及 清理日志表
2. 启动queue队列 bash/start_queue.sh

### redis 安装
http://example.com/redisadmin
git clone https://github.com/ErikDubbelboer/phpRedisAdmin.git
cd phpRedisAdmin
git clone https://github.com/nrk/predis.git vendor
#### 改动:
2016-xx-xx 配置文件设置一律从入口文件处改到config.php中，符合官方规范
2016-12-13 添加faker库,生成假数据,见官方文档或参考写好的getFaker()函数
#### 注意
时区设置问题，流浪器端设置cookies，key=timezone