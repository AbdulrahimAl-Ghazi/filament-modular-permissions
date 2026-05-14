# Filament Modular Permissions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/abdulrahim/filament-modular-permissions.svg?style=flat-square)](https://packagist.org/packages/abdulrahim/filament-modular-permissions)
[![Total Downloads](https://img.shields.io/packagist/dt/abdulrahim/filament-modular-permissions.svg?style=flat-square)](https://packagist.org/packages/abdulrahim/filament-modular-permissions)
[![License](https://img.shields.io/packagist/l/abdulrahim/filament-modular-permissions.svg?style=flat-square)](https://packagist.org/packages/abdulrahim/filament-modular-permissions)

A professional Laravel package for modular roles and permissions in **Filament v5**. Supporting multi-panel, auto-syncing, and **Global Zero-Config Protection**.

> **Requirements**: PHP 8.2+, Laravel 11+, Filament 5.x, spatie/laravel-permission ^6|^7

---

[العربية](#نظام-الصلاحيات-والأدوار-الموديولر-لـ-filament) | [English](#features)

---

## Features

- **Global Zero-Config Shield**: Protect and hide all resources and widgets automatically based on permissions — no traits needed.
- **Smart Sync**: Sync all resources, widgets, and custom permissions with Spatie in one command.
- **Custom Permissions**: Define standalone permissions (e.g. `export_reports`) directly in config.
- **Multi-panel Support**: Publish and manage permissions for each panel independently.
- **Super Admin Gate**: Automatically grants full access to the `super_admin` role via `Gate::before`.
- **Interactive CLI**: Select your target panel via an interactive CLI menu.
- **Safe Publishing**: Publish commands skip existing files by default; use `--force` to overwrite.
- **Diagnostics**: Inspect any user's roles and permissions with `permissions:check`.
- **User Management**: Pre-configured User Resource with role management.

## Installation

**Step 1** — Install via Composer:

```bash
composer require abdulrahim/filament-modular-permissions
```

**Step 2** — Run the installer:

```bash
php artisan permissions:install
```

This command does three things automatically:
1. Syncs all permissions from your Filament panels to the database
2. Publishes the **Role Management** Resource (to manage roles & assign permissions)
3. Publishes the **User Management** Resource (to manage users & assign roles)

Then register both published resources in your Filament panel.

> Or run each step individually if you need more control:
> ```bash
> php artisan permissions:sync                   # Step 1: sync permissions
> php artisan permissions:publish-resources      # Step 2: publish Role Resource
> php artisan permissions:publish-user-resource  # Step 3: publish User Resource
> ```

> [!IMPORTANT]
> Re-run `php artisan permissions:sync` every time you add a new Resource or Widget to register its permissions in the database.

> [!TIP]
> To publish to a specific panel, or to re-publish and overwrite existing files:
> ```bash
> php artisan permissions:install --panel=admin --force
> ```


## Initial User (Seeding)

To create your first Super Admin user, add this to your `DatabaseSeeder.php`:

```php
use App\Models\User;
use Spatie\Permission\Models\Role;

public function run(): void
{
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

## Custom Permissions

To add standalone permissions not tied to any resource, define them in your config:

```php
// config/filament-modular-permissions.php
'custom_permissions' => [
    'export_reports',
    'access_api',
    'view_dashboard',
],
```

Then run `php artisan permissions:sync` to register them.

## Diagnostics

Inspect a user's roles and effective permissions:

```bash
php artisan permissions:check --user=1
php artisan permissions:check --user=1 --guard=admin
```

## Advanced Concepts

### 1. Multi-Guard Architecture
The package handles multi-panel environments where each panel uses a different Auth Guard. Permissions are always isolated per guard.

### 2. Intelligent Super Admin
The `super_admin` role is granted full access via a global `Gate::before` check. This hook returns `null` (not `false`) when denying, so your own Policies always remain active.

### 3. Policy Compatibility
The `Gate::before` hook only intercepts known Filament abilities (`viewAny`, `view`, `create`, etc.) on Eloquent models. All other policy checks are unaffected.

## Manual Control (Optional)

Disable the global shield in `config/filament-modular-permissions.php`:

```php
'auto_hide_resources' => false,
```

Then use the traits manually in each Resource or Widget:

```php
use Abdulrahim\FilamentModularPermissions\Traits\HandlesResourcePermissions;
use Abdulrahim\FilamentModularPermissions\Traits\HandlesWidgetPermissions;
```

## Available Commands

| Command | Description |
|---------|-------------|
| `permissions:install` | All-in-one installer (sync + publish resources) |
| `permissions:sync` | Sync all resource, widget, and custom permissions |
| `permissions:publish-resources [--panel=] [--force]` | Publish Role Resource files |
| `permissions:publish-user-resource [--panel=] [--force]` | Publish User Resource files |
| `permissions:check [--user=] [--guard=]` | Diagnose user roles and permissions |

## Contact
Email: [abaad.dev8@gmail.com](mailto:abaad.dev8@gmail.com)  
Website: [abaad.dev](https://abaad.dev)

---

# نظام الصلاحيات والأدوار الموديولر لـ Filament

مكتبة احترافية لإدارة الأدوار والصلاحيات في **Filament v5** تعتمد على المبدأ الموديولر، مع دعم كامل لتعدد لوحات التحكم والحماية الشاملة التلقائية.

> **المتطلبات**: PHP 8.2+، Laravel 11+، Filament 5.x، spatie/laravel-permission ^6|^7

## المميزات الرئيسية

- **الحماية الشاملة التلقائية**: حماية المسارات وإخفاء الموارد من القائمة الجانبية تلقائياً بمجرد التثبيت.
- **نظام مزامنة ذكي**: أمر واحد لمزامنة جميع الموارد والويدجت والصلاحيات المخصصة.
- **صلاحيات مخصصة**: تعريف صلاحيات مستقلة مثل `export_reports` من الـ config مباشرةً.
- **دعم تعدد اللوحات**: إدارة الصلاحيات لكل لوحة تحكم بشكل مستقل تماماً.
- **السوبر أدمن**: نظام `Gate::before` يعطي كافة الصلاحيات لدور `super_admin` تلقائياً.
- **نشر آمن**: أوامر النشر تتجاوز الملفات الموجودة بشكل افتراضي؛ استخدم `--force` للكتابة فوقها.
- **تشخيص الأذونات**: فحص أدوار وصلاحيات أي مستخدم عبر `permissions:check`.
- **إدارة المستخدمين**: مورد إدارة مستخدمين جاهز مع إمكانية ربط الأدوار.

## التثبيت

**الخطوة الأولى** — تحميل المكتبة عبر Composer:

```bash
composer require abdulrahim/filament-modular-permissions
```

**الخطوة الثانية** — تشغيل المثبت:

```bash
php artisan permissions:install
```

يقوم هذا الأمر بثلاثة أشياء تلقائياً:
1. مزامنة جميع الصلاحيات من لوحات Filament إلى قاعدة البيانات
2. نشر **واجهة إدارة الأدوار** (لإنشاء الأدوار وتعيين الصلاحيات)
3. نشر **واجهة إدارة المستخدمين** (لإدارة المستخدمين وربطهم بالأدوار)

بعد ذلك، سجّل الـ Resources المنشورة في لوحة Filament الخاصة بك.

> أو نفّذ كل خطوة بشكل منفرد إذا أردت تحكماً أكثر:
> ```bash
> php artisan permissions:sync                   # الخطوة 1: مزامنة الصلاحيات
> php artisan permissions:publish-resources      # الخطوة 2: نشر واجهة الأدوار
> php artisan permissions:publish-user-resource  # الخطوة 3: نشر واجهة المستخدمين
> ```

> [!IMPORTANT]
> أعد تشغيل `php artisan permissions:sync` في كل مرة تضيف فيها مورداً (Resource) أو ويدجت (Widget) جديداً لتسجيل صلاحياته في قاعدة البيانات.

> [!TIP]
> للنشر على لوحة محددة أو لإعادة النشر فوق الملفات الموجودة:
> ```bash
> php artisan permissions:install --panel=admin --force
> ```

## إنشاء المستخدم الأول

```php
Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
$admin = User::firstOrCreate(['email' => 'admin@admin.com'], [
    'name' => 'Admin',
    'password' => bcrypt('12345678'),
]);
$admin->assignRole('super_admin');
```

## الصلاحيات المخصصة

```php
// config/filament-modular-permissions.php
'custom_permissions' => [
    'export_reports',
    'access_api',
],
```

ثم شغّل: `php artisan permissions:sync`

## تشخيص الأذونات

```bash
php artisan permissions:check --user=1
php artisan permissions:check --user=1 --guard=admin
```

## الأوامر المتاحة

| الأمر | الوصف |
|-------|-------|
| `permissions:install` | المثبت الموحد (sync + نشر الموارد) |
| `permissions:sync` | مزامنة جميع الصلاحيات |
| `permissions:publish-resources [--panel=] [--force]` | نشر ملفات إدارة الأدوار |
| `permissions:publish-user-resource [--panel=] [--force]` | نشر ملفات إدارة المستخدمين |
| `permissions:check [--user=] [--guard=]` | تشخيص أدوار وصلاحيات مستخدم |

## التحكم اليدوي (اختياري)

```php
// تعطيل الحماية الشاملة
'auto_hide_resources' => false,

// ثم استخدم الـ Traits يدوياً
use HandlesResourcePermissions;
use HandlesWidgetPermissions;
```

## التواصل
البريد الإلكتروني: [abaad.dev8@gmail.com](mailto:abaad.dev8@gmail.com)  
الموقع الإلكتروني: [abaad.dev](https://abaad.dev)

## License
MIT License.
