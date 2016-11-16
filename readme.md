# dingo api , jwt

# 使用 Clockwork 来调试 Laravel App

参考 https://laravel-china.org/topics/23

    http://www.jianshu.com/p/1489d8425bad

### 安装chrome插件部分

Chrome 插件 Clockwork
https://chrome.google.com/webstore/detail/clockwork/dmggabnehkmmfmdffgajcflpdjlnoemp

### 安装Laravel部分
https://github.com/itsgoingd/clockwork

```php
    "require-dev": {
        "itsgoingd/clockwork": "~1.12"
    },
```


### laravel 路由：

路由可以基于域名`dimain`、前缀`prefix`中间件`middleware`、命名空`namespace`来进行分组。

分组后注册路由： `get`、`post`、`any`、`controllers`（支持数组）、`resource`

### 中间件的使用：目的 `http` 请求的 规则、安全 验证

使用 `php artisan make:middleware LhwMiddleware` 来创建一个中间件文件

使用 `php artisan make:middleware BeforeMiddleware` 来创建一个中间件文件

将会在 `app/Http/Middleware` 目录内设定一个名称为 `LhwMiddleware` 的类

重要的方法：

```php
	public function handle($request, Closure $next)
    {
        // 运行动作
        return $next($request);
    }
```
注册中间件：
如果你要指派中间件给特定路由，你得先在 `app/Http/Kernel.php` 给中间件设置一个好记的键，默认情况下，这个文件内的 `$routeMiddleware` 属性已包含了 `Laravel` 目前设置的中间件，你只需要在清单列表中加上一组自定义的键即可。

```php
	protected $routeMiddleware = [
    		'auth' => \App\Http\Middleware\LhwMiddleware::class,
	];
```

中间件一旦在 HTTP kernel 文件内被定义，即可在路由选项内使用 middleware 键值指定：

```php
	Route::get('admin/profile', ['middleware' => 'auth', function () {
    		//
	}]);
```

### laravel中  使用DI：

一 创建服务定位器：目的创建依赖单元

使用 `php artisan make:provider  LhwtestServiceProvider` 创建服务定位器

laravel中所有的服务定位器都集中管理在`App/Providers`中，其中包含两个方法 `register` 和 `boot`

`register`方法用来注册我们的依赖，`boot`在调用依赖中的成员方法前会执行`boot `。

二 使用

laravel 启动会读取 `/config/app.php`

```php
	'providers' => [
		'App\Providers\LhwtestServiceProvider',
	]
```

在具体的业务层 使用：

```php
    App::make('lhw')->lhwAppent();
```

### laravel console 的使用：

使用 `php artisan make:console LhwTest`  生成一个计划任务

默认路径 `app/Console/Commands/LhwTest.php`

重要的属性和方法：

```php
protected $signature = 'command:Lhw{parameter}';

public function handle()
{
	dd($this->argument('parameter'));
    //$this->drip->send(User::find($this->argument('parameter')));
}
```

一旦你的命令编写完成，你需要注册 Artisan 后才能使用。注册文件为
```php
app/Console/Kernel.php。
protected $commands = [
   'App\Console\Commands\Lhw',
];
```

计划任务执行方式 ： php artisan command:Lhw 1


### laravel 队列的使用

首先要选择队列服务的驱动方式如 ：

    QUEUE_DRIVER=database

要使用 `database` 这个队列驱动的话，则需要创建一个数据表来记住任务，你可以用 `queue:table` 这个 `Artisan` 命令来创建这个数据表的迁移。当迁移建好后，就可以用`migrate`这个命令来创建数据表。

```php
php artisan queue:table

php artisan migrate
```

在使用列表里的队列服务前，必须安装以下依赖扩展包：

    Redis：predis/predis ~1.0

在你的应用程序中，队列的任务类都默认放在 `app/Jobs ` 或者 `Commands`目录下，你可以用以下的 `Artisan` 命令来生成一个新的队列任务：

    php artisan make:job LhwJob --queued

    php artisan make:command LhwCommand

Command 代码：
```php
namespace App\Commands;
use App\Commands\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use App\User;
class LhwCommand extends Command implements SelfHandling
{
    private $user; //user 实例
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function handle()
    {
        \Illuminate\Support\Facades\Log::info('队列测试command'.$this->user->id.'username'.$this->user->name);
    }
}
```

用 `php artisan make:command ` 创建的队列服务默认在 `app/Commands`没有`App\Commands\Command`这个父类。

```php
<?php
namespace App\Commands;
abstract class Command {

}
```
Job 代码：

```php
namespace App\Jobs;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
class Lhw extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $user ;
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function handle()
    {
        \Illuminate\Support\Facades\Log::info('队列测试'.$this->user->id.'username'.$this->user->name);
    }
}

```

业务层推送队列：

```php
        $userModel =   User::findOrFail(1);
        Queue::push(new LhwCommand($userModel));
        $this->dispatch(new Lhw($userModel));
```

队列实时监听：

    php artisan queue:listen


### 常用知识点

```php
//要习惯使用  php artisan tinker 调试代码

$active = new App\cp_active();
$obj = $active->active_sn = 'sn';
$obj->update();
$obj->toArray(); //获取非空的属性值

```

```php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cp_active extends Model
{

    public $table='cp_active'; //手动创建的表 指定表名

    protected $fillable = [] ;// 指定可填充字段
}

```

重要的知识点 laravel 中如何对 保存数据库中的字段 进行预处理： (练习 未 成功)

```php

    public $table='cp_active'; //手动创建的表 指定表名

    protected $fillable = ['active_sn'] ;// 指定可填充字段

    public function setActiveTitleAttribute($active_title){

        $this->attributes['active_title'] = $active_title.'this is cust value';

    }

    public function setPasswordAttribute($passowrd)
    {
        $this->attributes['password'] = Hash::make($passowrd);
        //仅仅是举例
    }

    public function setPublishedAtAttribute($date)
    {
        $this->attributes['published_at'] = Carbon::createFromFormat('Y-m-d',$date);
    }

    // controller  Active::latest()->published()->get()
    public function scopePublished($query){

        $query->where('publis_at', '<=' , Carbon::now());
    }

```

### 项目安装了 barryvdh/laravel-ide-helper

安装中遇到的问题 barryvdh/laravel-ide-helper  依赖 phpdocumentor/reflection-docblock: ^2.0.4 (3.0的 不行)

```php
composer require barryvdh/laravel-ide-helper

php artisan ide-helper:generate
```

### migration 的使用

创建migration:

```php
php artisan make:migration create_restful_api_table  --create=restful
```

生成表:

```php
php artisan migrate
```

撤销操作(将上一步创建的表撤销,相当于drop)

```php
    php artisan migrate:rollback
```

添加和修改表字段(新件migration指定要修改的表):

```php
php artisan make:migration add_intro_column_to_restful --table=restful
```

### Carbon的使用

为了中文化显示

要在你的`app/Providers/AppServiceProvider.php`中添加`\Carbon\Carbon::setLocale('zh')`

```php
    public function boot()
    {
        \Carbon\Carbon::setLocale('zh');
    }
```

在Article的 Model 中添加下面的方法：

```php
  public function getCreatedAtAttribute($date)
    {
	//$data 为数据库里created_at 的值
	//这里使用了Carbon 中的addDays 和 diffForHumans 方法
        if (Carbon::now() < Carbon::parse($date)->addDays(10)) {
            return Carbon::parse($date);
        }

        return Carbon::parse($date)->diffForHumans();
    }
```

最后使用：

```php
	$article = \App\Article::find(1);

	{{ $article->created_at }}; // 视图中直接显示
```

`Carbon`的`toDateString`方法使用

```php
dd(Carbon::parse($date));

/*
 Carbon {#149 ▼
  +"date": "2016-06-08 15:39:30.000000"
  +"timezone_type": 3
  +"timezone": "UTC"
 }
 */

dd(Carbon::parse($date)->toDateString());

//2016-06-08


```



