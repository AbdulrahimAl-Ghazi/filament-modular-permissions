# Filament Modular Permissions

[![Latest Version on Packagist](https://img.php.net/images/logos/php-logo.svg)](https://packagist.org/packages/abdulrahim/filament-modular-permissions)
[![Total Downloads](https://img.shields.io/packagist/dt/abdulrahim/filament-modular-permissions.svg?style=flat-square)](https://packagist.org/packages/abdulrahim/filament-modular-permissions)
[![License](https://img.shields.io/packagist/l/abdulrahim/filament-modular-permissions.svg?style=flat-square)](https://packagist.org/packages/abdulrahim/filament-modular-permissions)

A professional Laravel package for modular roles and permissions in Filament V3. Supporting multi-panel, auto-syncing, and global shield protection.

---

[العربية](#نظام-الصلاحيات-والأدوار-الموديولر-لـ-filament) | [English](#features)

---

## Features

- **Smart Sync**: Sync all resources and widgets with Spatie permissions in one command.
- **Global Shield (New)**: Protect all panel resources from one place using a single Middleware.
- **Multi-panel Support**: Publish and manage permissions for each panel independently.
- **Super Admin Gate**: Automatically grants all permissions to the `super_admin` role.
- **Dynamic & Customizable**: Fully customizable config, stubs, and translations.
- **Localization**: Supports Arabic and English out of the box.

## Installation

1. Install the package via composer:
```bash
composer require abdulrahim/filament-modular-permissions
```

2. Sync initial permissions:
```bash
php artisan permissions:sync
```

3. Publish the Roles resource:
```bash
php artisan permissions:publish-resources
```

## Activation

### Option 1: Global Shield (Recommended)
Add the middleware to your `PanelProvider` to protect all resources automatically:

```php
use Abdulrahim\FilamentModularPermissions\Http\Middleware\ModularPermissionsMiddleware;

public function panel(Panel $panel): Panel
{
    return $panel->authMiddleware([
        ModularPermissionsMiddleware::class,
    ]);
}
```

### Option 2: Manual Trait (Fine-grained)
Add the trait to your Resource class:
```php
use Abdulrahim\FilamentModularPermissions\Traits\HandlesResourcePermissions;

class UserResource extends Resource
{
    use HandlesResourcePermissions;
}
```

## Contact
Email: [abaad.dev8@gmail.com](mailto:abaad.dev8@gmail.com)  
Website: [abaad.dev](https://abaad.dev)

---

# نظام الصلاحيات والأدوار الموديولر لـ Filament

مكتبة احترافية لإدارة الأدوار والصلاحيات في Filament V3 تعتمد على المبدأ الموديولر، مع دعم كامل لتعدد لوحات التحكم (Multi-panel) والترجمة الآلية.

## المميزات الرئيسية

- **نظام مزامنة ذكي**: أمر واحد لمزامنة جميع الموارد والويدجت مع نظام Spatie.
- **الحماية الشاملة (جديد)**: تفعيل نظام الصلاحيات لجميع الأقسام عبر Middleware واحد.
- **دعم تعدد اللوحات**: إدارة الصلاحيات لكل لوحة تحكم بشكل مستقل تماماً.
- **السوبر أدمن**: نظام Gate يعطي كافة الصلاحيات لدور `super_admin` تلقائياً.
- **قابل للتخصيص**: تحكم كامل في الإعدادات، القوالب، والترجمات.

## التثبيت

1. تحميل المكتبة:
```bash
composer require abdulrahim/filament-modular-permissions
```

2. مزامنة الصلاحيات:
```bash
php artisan permissions:sync
```

3. نشر واجهة الإدارة:
```bash
php artisan permissions:publish-resources
```

## تفعيل الحماية

### الطريقة الأولى: الحماية الشاملة (الموصى بها)
أضف الوسيط التالي في ملف الـ `PanelProvider`:

```php
use Abdulrahim\FilamentModularPermissions\Http\Middleware\ModularPermissionsMiddleware;

public function panel(Panel $panel): Panel
{
    return $panel->authMiddleware([
        ModularPermissionsMiddleware::class,
    ]);
}
```

## التواصل
البريد الإلكتروني: [abaad.dev8@gmail.com](mailto:abaad.dev8@gmail.com)  
الموقع الإلكتروني: [abaad.dev](https://abaad.dev)

## License
MIT License.
