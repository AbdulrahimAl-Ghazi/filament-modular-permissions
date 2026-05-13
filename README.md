# Filament Modular Permissions

[![Latest Version on Packagist](https://img.php.net/images/logos/php-logo.svg)](https://packagist.org/packages/abdulrahim/filament-modular-permissions)
[![Total Downloads](https://img.shields.io/packagist/dt/abdulrahim/filament-modular-permissions.svg?style=flat-square)](https://packagist.org/packages/abdulrahim/filament-modular-permissions)
[![License](https://img.shields.io/packagist/l/abdulrahim/filament-modular-permissions.svg?style=flat-square)](https://packagist.org/packages/abdulrahim/filament-modular-permissions)

A professional Laravel package for modular roles and permissions in Filament V3. Supporting multi-panel, auto-syncing, and **Global Zero-Config Protection**.

---

[العربية](#نظام-الصلاحيات-والأدوار-الموديولر-لـ-filament) | [English](#features)

---

## Features

- **Global Zero-Config Shield**: Protect and hide all resources and widgets automatically based on permissions.
- **Smart Sync**: Sync all resources and widgets with Spatie permissions in one command.
- **Multi-panel Support**: Publish and manage permissions for each panel independently.
- **Super Admin Gate**: Automatically grants all permissions to the `super_admin` role.
- **Interactive CLI**: Select your target panel via an interactive CLI menu.
- **User Management**: Pre-configured User Resource with role management.

## Installation

1. Install the package via composer:

```bash
composer require abdulrahim/filament-modular-permissions
```

2. Sync initial permissions:

```bash
php artisan permissions:sync
```

> [!IMPORTANT]
> You **must** run `php artisan permissions:sync` whenever you add a new Resource or Widget to your Filament project to ensure its permissions are registered in the database.

3. Publish Resources:

```bash
# To manage Roles and Permissions
php artisan permissions:publish-resources

# To manage Users and assign Roles to them
php artisan permissions:publish-user-resource
```

## Initial User (Seeding)

To create your first Super Admin user, add this to your `DatabaseSeeder.php`:

```php
use App\Models\User;
use Spatie\Permission\Models\Role;

public function run(): void
{
    // Ensure the super_admin role exists for the web guard
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

    $admin = User::firstOrCreate(
        ['email' => 'admin@admin.com'],
        [
            'name' => 'Admin',
            'password' => bcrypt('12345678'),
        ]
    );

    $admin->assignRole('super_admin');
}
```

## Advanced Concepts

### 1. Multi-Guard Architecture
The package handles multi-panel environments where each panel might use a different Auth Guard. It automatically detects and uses the correct guard for syncing and checking permissions.

### 2. Intelligent Super Admin
The `super_admin` role is a master role. The package registers a global `Gate::before` check that grants full access to anyone with this role (guard-aware).

## Manual Control (Optional)

Disable the global shield in `config/filament-modular-permissions.php`:
`'auto_hide_resources' => false,`

Then use the traits manually:
`use HandlesResourcePermissions;` or `use HandlesWidgetPermissions;`

## Contact
Email: [abaad.dev8@gmail.com](mailto:abaad.dev8@gmail.com)  
Website: [abaad.dev](https://abaad.dev)

---

# نظام الصلاحيات والأدوار الموديولر لـ Filament

مكتبة احترافية لإدارة الأدوار والصلاحيات في Filament V3 تعتمد على المبدأ الموديولر، مع دعم كامل لتعدد لوحات التحكم والحماية الشاملة التلقائية.

## المميزات الرئيسية

- **الحماية الشاملة التلقائية**: حماية المسارات وإخفاء الموارد من القائمة الجانبية تلقائياً بمجرد التثبيت.
- **نظام مزامنة ذكي**: أمر واحد لمزامنة جميع الموارد والويدجت مع نظام Spatie.
- **دعم تعدد اللوحات**: إدارة الصلاحيات لكل لوحة تحكم بشكل مستقل تماماً عبر أوامر تفاعلية.
- **السوبر أدمن**: نظام Gate يعطي كافة الصلاحيات لدور `super_admin` تلقائياً.
- **إدارة المستخدمين**: مورد إدارة مستخدمين جاهز مع إمكانية ربط الأدوار والترجمة الكاملة.

## التثبيت

1. تحميل المكتبة:

```bash
composer require abdulrahim/filament-modular-permissions
```

2. مزامنة الصلاحيات:

```bash
php artisan permissions:sync
```

> [!IMPORTANT]
> **يجب** عليك تنفيذ أمر `php artisan permissions:sync` في كل مرة تقوم فيها بإضافة مورد (Resource) أو ويدجت (Widget) جديد لمشروعك، لضمان تسجيل صلاحياته في قاعدة البيانات.

3. نشر واجهات الإدارة:

```bash
# لنشر واجهة إدارة الأدوار والصلاحيات (RoleResource)
php artisan permissions:publish-resources

# لنشر واجهة إدارة المستخدمين وربطهم بالأدوار (UserResource)
php artisan permissions:publish-user-resource
```

## إنشاء المستخدم الأول (Seeding)

لإنشاء أول مستخدم بصلاحيات المدير العام (Super Admin)، أضف الكود التالي في ملف `DatabaseSeeder.php`:

```php
use App\Models\User;
use Spatie\Permission\Models\Role;

public function run(): void
{
    // التأكد من وجود دور السوبر أدمن للحارس الافتراضي
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

    $admin = User::firstOrCreate(
        ['email' => 'admin@admin.com'],
        [
            'name' => 'Admin',
            'password' => bcrypt('12345678'),
        ]
    );

    $admin->assignRole('super_admin');
}
```

## مفاهيم متقدمة

### 1. معمارية الحراس المتعددة (Multi-Guard)
تتعامل المكتبة بذكاء مع اللوحات التي تستخدم حراس أمان مختلفة، وتفصل بين صلاحيات كل حارس تلقائياً.

### 2. السوبر أدمن الذكي (Intelligent Super Admin)
بمجرد إعطاء دور `super_admin` للمستخدم، فإنه سيحصل على وصول كامل لكافة الأقسام تلقائياً عبر نظام `Gate::before`.

## التحكم اليدوي (اختياري)

قم بتعطيل الحماية الشاملة في ملف الإعدادات:
`'auto_hide_resources' => false,`

ثم استخدم الـ Traits يدوياً:
`use HandlesResourcePermissions;` أو `use HandlesWidgetPermissions;`

## التواصل
البريد الإلكتروني: [abaad.dev8@gmail.com](mailto:abaad.dev8@gmail.com)  
الموقع الإلكتروني: [abaad.dev](https://abaad.dev)

## License
MIT License.
