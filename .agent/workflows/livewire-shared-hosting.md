---
description: Deploy Livewire 3 to Shared Hosting with Subfolder
---

# Livewire 3 Subfolder Deployment for Shared Hosting

เมื่อ deploy Laravel + Livewire 3 ไปยัง Shared Hosting ที่อยู่ใน subfolder (เช่น `/workcnt`) จะมีปัญหา Livewire ไม่ทำงาน เนื่องจาก:

1. **Livewire script URL ผิด** - ระบบสร้าง URL เป็น `https://domain.com/subfolder?id=xxx` แทนที่จะเป็น `https://domain.com/subfolder/livewire/livewire.js`
2. **Update URI ผิด** - ค่า default เป็น `/livewire/update` แต่ต้องเป็น `/subfolder/livewire/update`
3. **Alpine.js ซ้ำซ้อน** - ถ้า app.js มี Alpine อยู่แล้ว จะ conflict กับ Alpine ที่มากับ Livewire 3

## วิธีแก้ไข

### 1. แก้ไข `resources/views/layouts/app.blade.php`

แทนที่ `@livewireScripts` ด้วย:

```html
<!-- Livewire Scripts (Manual for subfolder) -->
<script>
    window.livewireScriptConfig = {
        "csrf": "{{ csrf_token() }}",
        "uri": "/YOUR_SUBFOLDER/livewire/update",
        "progressBar": true,
        "nonce": ""
    };
</script>
<script src="https://YOUR_DOMAIN/YOUR_SUBFOLDER/livewire/livewire.js" 
        data-csrf="{{ csrf_token() }}" 
        data-update-uri="/YOUR_SUBFOLDER/livewire/update" 
        data-navigate-once="true"
        onload="if(window.Livewire && !Livewire.started){ Livewire.start(); }"></script>
```

**สำคัญ:** 
- เปลี่ยน `YOUR_DOMAIN` เป็น domain ของคุณ (เช่น `nass.ac.th`)
- เปลี่ยน `YOUR_SUBFOLDER` เป็น subfolder ของคุณ (เช่น `workcnt`)

### 2. ลบ Alpine.js CDN ออก (ถ้ามี)

ถ้าใน layout มี script โหลด Alpine จาก CDN ให้ลบออก เช่น:
```html
<!-- ลบบรรทัดนี้ออก -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

### 3. ตั้งค่า `.env` บน Server

```env
APP_URL=https://YOUR_DOMAIN/YOUR_SUBFOLDER
```

### 4. Clear Cache บน Server

```bash
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

## ตัวอย่างการใช้งานจริง

สำหรับ `https://nass.ac.th/workcnt`:

```html
<script>
    window.livewireScriptConfig = {
        "csrf": "{{ csrf_token() }}",
        "uri": "/workcnt/livewire/update",
        "progressBar": true,
        "nonce": ""
    };
</script>
<script src="https://nass.ac.th/workcnt/livewire/livewire.js" 
        data-csrf="{{ csrf_token() }}" 
        data-update-uri="/workcnt/livewire/update" 
        data-navigate-once="true"
        onload="if(window.Livewire && !Livewire.started){ Livewire.start(); }"></script>
```

## สาเหตุที่ต้องทำแบบนี้

1. `@livewireScripts` และ `@livewireScriptConfig` ไม่รองรับ subfolder อย่างถูกต้อง
2. การกำหนด `window.livewireScriptConfig` ด้วยมือช่วยให้กำหนด URI ได้อย่างถูกต้อง
3. `onload` + `Livewire.start()` แก้ปัญหา Alpine conflict ที่ทำให้ Livewire ไม่ boot อัตโนมัติ
