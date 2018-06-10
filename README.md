# xjtudance
这是西安交大dance版的报名微信小程序。
## 效果图
![Welcome to Dance!](/docs/images/welcome_to_dance.jpg "dance版微信小程序报名页")
## 更新日志
### 2017.10.06
* 安全 增加mongo数据库每日定时自动备份 - Linlin
* 新增 中秋、国庆节日启动页面 - Linlin
* 新增 服务器响应小程序发回的header中添加errMsg控制字段 - Linlin
* 优化 用户提交报到后，提交按钮disabled - Linlin
* 优化 报到页面增加表单是否已提交判断，防止用户重复点击提交按钮提交重复表单 - Linlin
* 优化 小程序前端toast显示时长统一为1500ms，mask非必须时皆设置为false - Linlin
* 优化 创建db类，处理MongoDB相关操作 - Linlin
* 优化 创建类bmybbs，处理兵马俑bbs相关操作 - Linlin
* 优化 上传图片失败提醒 - Linlin
* 修复 兵马俑bbs无法连接时，小程序端无法报到的问题 - Linlin
* 修复 活动发布页“说点什么吧”项只能填写数字的问题 - Linlin
### 2017.09.29
* 安全 给mongo数据库设置访问权限，建立dance配置文件，添加安全配置说明文档 - Linlin
### 2017.09.14
* 优化 首页，设计为home的形式 - Linlin
### 2017.09.11
* 修复 大图片无法上传的问题，将单个图片上传大小限制设置为20M，并添加提醒 - Linlin
* 优化 舞友重新报到时，删除数据库中旧的报到帖，数据库中旧的照片，以及兵马俑BBS上旧的报到帖 - Linlin
### 2017.09.09
* 新增 主页
* 新增 忽悠功能，包括发表忽悠、查看忽悠列表、查看忽悠具体内容
* 新增 通用获取数据库单条数据和列表数据功能
### 2017.09.06
* 优化 所有颜色改用rgb值设置，防止有时颜色设置无效
* 优化 页面布局及组件样式
* 优化 报名页面所有input背景色改为白色
* 新增 后台从文件读取数据到数据库功能
* 新增 一台后台服务器，一个域名
* 新增 自动选择可用域名功能，用于更换域名时
* 修复 部分中文命名的音频和图片资源无法加载的问题，将其名称改为拼音
* 修复 当audio组件内文字过长时，组件溢出屏幕边缘的问题
* 修复 部分页面上拉时卡顿并且屏幕顶部出现空白区域的问题
* 安全 修改apache服务器配置，禁止外部网络通过url访问Apache目录
### 2017.08.31
* 新增 dance舞种介绍页
* 新增 后台导出数据库到文件功能
* 修复 分享个人展示页打开后无信息的问题