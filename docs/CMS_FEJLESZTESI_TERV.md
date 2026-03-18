# Linardics Kft. – CMS Demo Fejlesztési Terv

## Projekt összefoglaló

**Cél:** Működő CMS demo készítése a Géppark oldal kezeléséhez, amely bemutatja az ügyfélnek, hogy házon belül egyszerűen tudják módosítani a gépek adatait.

**Scope:** Csak a `geppark.html` oldal dinamikussá tétele + admin panel

**Prioritás:** Magas (demo az ügyfél jóváhagyásához szükséges)

---

## 1. Jelenlegi állapot elemzése

### Technológiai stack (prototípus)
- **Frontend:** Statikus HTML5 + Tailwind CSS (CDN) + Vanilla JavaScript
- **Backend:** Nincs (statikus hosting)
- **Deployment:** cPanel Git Version Control
- **Fejlesztési környezet:** Python http.server

### Géppark oldal jelenlegi felépítése

**Fájl:** `geppark.html` (45 KB, 680 sor)

**Tartalmi struktúra:**
1. **TRUMPF Lézervágók szekció** (4 gép)
   - TruLaser 3030 Fiber (5 kW)
   - TruLaser 5030 TLF (6 kW CO₂)
   - TruMatic 7000 (Kombigép)
   - TruLaser Tube 5000 (Csőlézer)

2. **TRUMPF Hajlítók szekció** (6 gép)
   - TruBend Center 7030 (kiemelt, automatizált)
   - TruBend 5130 (×2 db)
   - TruBend 3100 (×2 db)
   - TruBend 7036

3. **AMADA Hajlítók szekció** (4 gép)
   - AMADA IT 1250/20t
   - AMADA HFE 3000/100t
   - AMADA HFE 3000/130Lt
   - AMADA HFP 4000/220t

4. **Csőhajlítás & Felületkezelés szekció** (3+4 gép)
   - SOCO csőhajlító
   - Gema porfestő rendszer
   - Wagner porfestő rendszer
   - 4 db kötéstechnikai kisegítő gép

**Összesen:** 15 fő gép + 4 kiegészítő gép = 19 gép

**HTML struktúra gépenként:**
```html
<div class="machine-card">
  <div class="flex items-start justify-between">
    <div>
      <div class="text-[#cc2222]">Kategória · Alcím</div>
      <h3>Gép neve</h3>
    </div>
    <div class="badge">Teljesítmény/típus</div>
  </div>
  <p class="text-white/45">Rövid leírás...</p>
  <table class="spec-table">
    <tr><th>Műszaki adat neve</th><td>Érték</td></tr>
  </table>
</div>
```

### Oldal-szintű szerkeszthető tartalmak
- Főcím (H1): "Géppark"
- Bevezető szöveg (lead paragraph)
- Summary badges (4 db: "TRUMPF lézervágók – 4 gép" stb.)
- Szekció címek: "TRUMPF Lézervágók", "TRUMPF Hajlítók", "AMADA Hajlítók" stb.
- Szekció alcímek (eyebrow): "Lézervágás", "Lemezhajlítás", "Élhajlítás"

---

## 2. Technológiai döntések

### Ajánlott stack (PDF alapján)

**Elsődleges opció: Laravel + Filament PHP**

**Indoklás:**
- ✅ Gyors fejlesztés (1-2 hét alatt demo-ready)
- ✅ Filament = modern, felhasználóbarát admin UI (drag-and-drop, WYSIWYG)
- ✅ Laravel = ismert, stabil, jól dokumentált
- ✅ PHP = hosting-kompatibilis (cPanel támogatás)
- ✅ Könnyű tovább bővíthető (többi oldal, többnyelvűség)

**Alternatív opciók:**
- **Symfony + Sonata Admin:** Robusztusabb, de lassabb fejlesztés
- **Directus (headless CMS):** Gyors, de extra API réteg + frontend átírás
- **Strapi (headless CMS):** Node.js-alapú, hosting váltást igényelhet

### Döntés: Laravel 11 + Filament 3

**Technikai stack:**
```
Backend:
- Laravel 11 (PHP 8.2+)
- Filament 3 (Admin panel)
- MySQL 8.0 (adatbázis)
- Intervention Image (képkezelés)
- Spatie Media Library (fájlfeltöltés)

Frontend:
- Blade templates (SSR)
- Meglévő Tailwind CSS + Vanilla JS
- Képek: WebP, lazy load

Deployment:
- cPanel PHP 8.2
- Composer
- .env config
- Git deploy
```

---

## 3. Adatbázis séma

### Táblák

#### `machines` (gépek)
| Mező | Típus | Leírás |
|------|-------|--------|
| `id` | bigint unsigned | Elsődleges kulcs |
| `name` | varchar(255) | Gép neve (pl. "TRUMPF TruBend 5130") |
| `manufacturer` | varchar(100) | Gyártó (TRUMPF, AMADA, SOCO, Gema, Wagner) |
| `category` | varchar(100) | Kategória (lezervago, hajlito, csohajlito, porfesto, kotestechnika) |
| `category_label` | varchar(100) | Kategória címke (pl. "Fő gép · Fiber lézer") |
| `badge` | varchar(50) | Jelvény szöveg (pl. "5 kW", "Kombi") |
| `badge_color` | varchar(20) | Jelvény színkód (alapértelmezett: red) |
| `short_description` | text | Rövid leírás (2-3 mondat) |
| `long_description` | longtext | Részletes leírás (WYSIWYG) |
| `image` | varchar(255) | Képfájl útvonal (nullable) |
| `section` | varchar(50) | Szekció (trulaser, trubend, amada, egyeb) |
| `order` | integer | Sorrend (drag-and-drop) |
| `is_active` | boolean | Aktív/inaktív kapcsoló |
| `is_featured` | boolean | Kiemelt gép jelölő (pl. TruBend Center 7030) |
| `created_at` | timestamp | Létrehozás |
| `updated_at` | timestamp | Módosítás |

#### `machine_specs` (műszaki adatok - kapcsolótábla)
| Mező | Típus | Leírás |
|------|-------|--------|
| `id` | bigint unsigned | Elsődleges kulcs |
| `machine_id` | bigint unsigned | Kapcsolat a `machines` táblához |
| `spec_key` | varchar(100) | Műszaki adat neve (pl. "Munkaterület") |
| `spec_value` | varchar(255) | Érték (pl. "3000 × 1500 mm") |
| `order` | integer | Sorrend a táblázatban |
| `created_at` | timestamp | Létrehozás |
| `updated_at` | timestamp | Módosítás |

#### `page_settings` (oldal-szintű beállítások)
| Mező | Típus | Leírás |
|------|-------|--------|
| `id` | bigint unsigned | Elsődleges kulcs |
| `key` | varchar(100) | Beállítás kulcs (pl. "geppark_title") |
| `value` | text | Érték |
| `type` | varchar(50) | Típus (text, textarea, wysiwyg) |
| `group` | varchar(50) | Csoport (geppark) |
| `created_at` | timestamp | Létrehozás |
| `updated_at` | timestamp | Módosítás |

#### `users` (admin felhasználók - Laravel alapértelmezett)
| Mező | Típus | Leírás |
|------|-------|--------|
| `id` | bigint unsigned | Elsődleges kulcs |
| `name` | varchar(255) | Név |
| `email` | varchar(255) | E-mail (egyedi) |
| `password` | varchar(255) | Jelszó (hashed) |
| `remember_token` | varchar(100) | "Emlékezz rám" token |
| `created_at` | timestamp | Létrehozás |
| `updated_at` | timestamp | Módosítás |

### Kapcsolatok
- `machines` 1:N `machine_specs` (egy gépnek több műszaki adata van)

---

## 4. Funkcionális követelmények

### Admin panel (Filament 3)

#### 4.1 Bejelentkezés
- **URL:** `/admin/login`
- **Mezők:** E-mail + jelszó
- **Biztonság:** Laravel Sanctum session-based auth
- **Demo admin:**
  - E-mail: `admin@linardics.hu`
  - Jelszó: `LinardicsDemo2026!`

#### 4.2 Dashboard
- Üdvözlő üzenet
- Gyors statisztikák:
  - Összes gép: 19 db
  - Aktív gépek: X db
  - Lézervágók: 4 db
  - Hajlítók: 10 db
- Gyors linkek: "Gépek kezelése", "Oldal beállítások", "Előnézet megtekintése"

#### 4.3 Gépek kezelése (CRUD)

**Lista nézet (`/admin/machines`):**
- Táblázat oszlopok:
  - Kép (thumbnail, 80×80px)
  - Gép neve
  - Gyártó
  - Kategória (badge)
  - Sorrend (drag-and-drop ikon)
  - Aktív/Inaktív (toggle)
  - Műveletek (Szerkesztés, Törlés)
- **Drag-and-drop:** Filament SortablePlugin (Spatie Eloquent Sortable)
- Szűrők:
  - Gyártó (TRUMPF, AMADA, egyéb)
  - Kategória (lézervágó, hajlító, egyéb)
  - Szekció (TruLaser, TruBend, AMADA, Egyéb)
  - Aktív/Inaktív
- Keresés: gép név alapján
- Bulk actions: Törlés, Aktiválás, Inaktiválás

**Szerkesztő nézet (`/admin/machines/{id}/edit`):**

**Form mezők (tabokkal):**

**Tab 1: Alapadatok**
- Gép neve (szöveg, kötelező)
- Gyártó (legördülő: TRUMPF, AMADA, SOCO, Gema, Wagner, Egyéb)
- Kategória (legördülő: lezervago, hajlito, csohajlito, porfesto, kotestechnika)
- Kategória címke (szöveg, pl. "Fő gép · Fiber lézer")
- Badge szöveg (szöveg, pl. "5 kW")
- Szekció (legördülő: trulaser, trubend, amada, egyeb)
- Kiemelt gép (checkbox)
- Aktív (toggle, alapértelmezett: be)

**Tab 2: Leírások**
- Rövid leírás (textarea, 2-5 mondat)
- Részletes leírás (WYSIWYG, opcionális) – Filament TiptapEditor

**Tab 3: Műszaki adatok**
- Ismételhető mező blokk (Repeater):
  - Műszaki adat neve (szöveg, pl. "Munkaterület")
  - Érték (szöveg, pl. "3000 × 1500 mm")
  - Sorrend (auto, drag-and-drop)
- Add / Remove gombok

**Tab 4: Kép**
- Képfeltöltés (Spatie Media Library)
- Támogatott formátumok: JPG, PNG, WebP
- Max. méret: 2 MB
- Automatikus thumbnail generálás: 800×600px WebP
- Alt szöveg mező (opcionális)

**Mentés gomb + Visszajelzés:**
- Siker: "Gép sikeresen mentve!" (zöld toast)
- Hiba: validációs hibaüzenetek pirossal

#### 4.4 Oldal beállítások (`/admin/settings`)

**Géppark oldal globális tartalmai:**
- Főcím (H1): input text
- Bevezető szöveg: textarea
- Summary badges (4 db repeater):
  - Badge szöveg
  - Sorrend
- Szekció címek (pl. "TRUMPF Lézervágók"): input text
- Szekció alcímek (pl. "Lézervágás"): input text

#### 4.5 Élő előnézet (opcionális, plusz pont)
- Gomb az admin panelen: "Előnézet megtekintése"
- Új tab-ban megnyitja a frontend oldalt
- Vagy: inline iframe preview a szerkesztő nézet jobb oldalán (Filament 3 custom widget)

---

### Frontend (Blade template)

#### 4.6 Dinamikus `geppark.html` (Blade template: `resources/views/geppark.blade.php`)

**Route:** `GET /geppark` → `GepparkController@index`

**Adatok betöltése:**
```php
$machines = Machine::where('is_active', true)
    ->orderBy('order')
    ->with('specs')
    ->get()
    ->groupBy('section');

$settings = PageSetting::where('group', 'geppark')->pluck('value', 'key');
```

**Blade template változók:**
- `$settings['geppark_title']` → H1 cím
- `$settings['geppark_intro']` → Bevezető szöveg
- `$settings['geppark_summary_badges']` → Summary badges (JSON dekódolva)
- `$machines['trulaser']` → TRUMPF lézervágók gyűjtemény
- `$machines['trubend']` → TRUMPF hajlítók gyűjtemény
- `$machines['amada']` → AMADA hajlítók gyűjtemény
- `$machines['egyeb']` → Egyéb gépek gyűjtemény

**HTML struktúra (foreach loop):**
```blade
@foreach($machines['trulaser'] as $machine)
  <div class="machine-card bg-[#122135] border border-white/8 p-6">
    <div class="flex items-start justify-between mb-4">
      <div>
        <div class="text-[#cc2222] text-xs">{{ $machine->category_label }}</div>
        <h3 class="font-heading font-semibold text-2xl">{{ $machine->name }}</h3>
      </div>
      @if($machine->badge)
        <div class="badge">{{ $machine->badge }}</div>
      @endif
    </div>
    <p class="text-white/45 text-sm">{{ $machine->short_description }}</p>
    <table class="spec-table w-full text-sm">
      <tbody>
        @foreach($machine->specs as $spec)
          <tr>
            <th class="text-left">{{ $spec->spec_key }}</th>
            <td class="text-right text-white/70">{{ $spec->spec_value }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endforeach
```

#### 4.7 Képek kezelése
- Feltöltött képek: `storage/app/public/machines/`
- Symlink: `php artisan storage:link` → `public/storage/machines/`
- Blade-ben: `<img src="{{ asset('storage/' . $machine->image) }}">`
- WebP konverzió: Intervention Image + Spatie Media Library

#### 4.8 Navbar és footer megtartása
- A meglévő `geppark.html` navbar és footer HTML kódja változatlan marad
- Csak a `<!-- TRUMPF LÉZERVÁGÓK -->` ... `<!-- EGYÉB -->` szekciók között cserélődik a tartalom

#### 4.9 Meglévő design és animációk megőrzése
- Tailwind CSS stílusok: változatlan
- Vanilla JS animációk (scroll reveal, navbar, mobile menu): változatlan
- Machine card hover effektusok: változatlan

---

## 5. Fejlesztési lépések (fázisok)

### FÁZIS 1: Laravel projekt inicializálás (1 nap)

#### 1.1 Laravel telepítés
```bash
composer create-project laravel/laravel linardics-cms "^11.0"
cd linardics-cms
```

#### 1.2 .env konfiguráció
```env
APP_NAME="Linardics CMS"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=linardics_cms
DB_USERNAME=root
DB_PASSWORD=

# Egyéb beállítások...
```

#### 1.3 Adatbázis létrehozása
```bash
mysql -u root -p
CREATE DATABASE linardics_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### 1.4 Alapértelmezett migrációk futtatása
```bash
php artisan migrate
```

#### 1.5 Projekt tesztelés
```bash
php artisan serve
# → http://localhost:8000
```

**Tesztelés:** Laravel welcome oldal betöltődik

---

### FÁZIS 2: Filament telepítés + admin felhasználó (0.5 nap)

#### 2.1 Filament 3 telepítés
```bash
composer require filament/filament:"^3.0"
php artisan filament:install --panels
```

#### 2.2 Admin user létrehozása
```bash
php artisan make:filament-user
# Name: Admin
# Email: admin@linardics.hu
# Password: LinardicsDemo2026!
```

#### 2.3 Admin panel tesztelés
```bash
php artisan serve
```
- Böngésző: `http://localhost:8000/admin/login`
- Bejelentkezés: `admin@linardics.hu` / `LinardicsDemo2026!`

**Tesztelés:** Dashboard betöltődik

---

### FÁZIS 3: Adatbázis séma + modellek (1 nap)

#### 3.1 Migrációk létrehozása
```bash
php artisan make:migration create_machines_table
php artisan make:migration create_machine_specs_table
php artisan make:migration create_page_settings_table
```

#### 3.2 Migráció kódok írása
**`create_machines_table.php`:**
```php
Schema::create('machines', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('manufacturer', 100);
    $table->string('category', 100);
    $table->string('category_label', 100)->nullable();
    $table->string('badge', 50)->nullable();
    $table->string('section', 50);
    $table->text('short_description');
    $table->longText('long_description')->nullable();
    $table->string('image')->nullable();
    $table->integer('order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->boolean('is_featured')->default(false);
    $table->timestamps();
});
```

**`create_machine_specs_table.php`:**
```php
Schema::create('machine_specs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('machine_id')->constrained()->onDelete('cascade');
    $table->string('spec_key', 100);
    $table->string('spec_value', 255);
    $table->integer('order')->default(0);
    $table->timestamps();
});
```

**`create_page_settings_table.php`:**
```php
Schema::create('page_settings', function (Blueprint $table) {
    $table->id();
    $table->string('key', 100)->unique();
    $table->text('value');
    $table->string('type', 50)->default('text');
    $table->string('group', 50);
    $table->timestamps();
});
```

#### 3.3 Migrációk futtatása
```bash
php artisan migrate
```

#### 3.4 Modellek létrehozása
```bash
php artisan make:model Machine
php artisan make:model MachineSpec
php artisan make:model PageSetting
```

#### 3.5 Model kapcsolatok definiálása
**`app/Models/Machine.php`:**
```php
class Machine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'manufacturer', 'category', 'category_label',
        'badge', 'section', 'short_description', 'long_description',
        'image', 'order', 'is_active', 'is_featured'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function specs()
    {
        return $this->hasMany(MachineSpec::class)->orderBy('order');
    }
}
```

**`app/Models/MachineSpec.php`:**
```php
class MachineSpec extends Model
{
    use HasFactory;

    protected $fillable = ['machine_id', 'spec_key', 'spec_value', 'order'];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
```

---

### FÁZIS 4: Filament Resources (Admin CRUD) (2 nap)

#### 4.1 Machine Resource létrehozása
```bash
php artisan make:filament-resource Machine --generate
```

#### 4.2 Machine Resource konfiguráció
**`app/Filament/Resources/MachineResource.php`:**

**Form mezők (Tab 1-4):**
```php
public static function form(Form $form): Form
{
    return $form->schema([
        Tabs::make('Tabs')->tabs([
            Tab::make('Alapadatok')->schema([
                TextInput::make('name')->required()->label('Gép neve'),
                Select::make('manufacturer')->options([
                    'TRUMPF' => 'TRUMPF',
                    'AMADA' => 'AMADA',
                    'SOCO' => 'SOCO',
                    'Gema' => 'Gema',
                    'Wagner' => 'Wagner',
                    'Egyéb' => 'Egyéb',
                ])->required()->label('Gyártó'),
                // ... további mezők
            ]),
            Tab::make('Leírások')->schema([
                Textarea::make('short_description')->rows(4),
                RichEditor::make('long_description'),
            ]),
            Tab::make('Műszaki adatok')->schema([
                Repeater::make('specs')->relationship()
                    ->schema([
                        TextInput::make('spec_key')->label('Tulajdonság'),
                        TextInput::make('spec_value')->label('Érték'),
                    ])
                    ->orderColumn('order')
                    ->collapsible(),
            ]),
            Tab::make('Kép')->schema([
                FileUpload::make('image')
                    ->image()
                    ->maxSize(2048)
                    ->directory('machines'),
            ]),
        ]),
    ]);
}
```

**Táblázat oszlopok:**
```php
public static function table(Table $table): Table
{
    return $table
        ->columns([
            ImageColumn::make('image')->size(80),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('manufacturer')->badge(),
            TextColumn::make('category')->badge(),
            IconColumn::make('is_active')->boolean(),
        ])
        ->filters([
            SelectFilter::make('manufacturer'),
            SelectFilter::make('category'),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->reorderable('order'); // Drag-and-drop
}
```

#### 4.3 PageSetting Resource létrehozása
```bash
php artisan make:filament-resource PageSetting
```

Egyszerűbb form: Key-Value párok kezelése

#### 4.4 Tesztelés
- Bejelentkezés: `/admin/login`
- Gépek lista: `/admin/machines`
- Új gép hozzáadása: form mezők tesztelése
- Mentés: validáció + DB insert ellenőrzés

---

### FÁZIS 5: Seeder (demo adatok) (0.5 nap)

#### 5.1 Seeder létrehozása
```bash
php artisan make:seeder MachineSeeder
php artisan make:seeder PageSettingSeeder
```

#### 5.2 Demo adatok importálása
**15 gép + műszaki adatok beszúrása** (jelenlegi `geppark.html` adatai alapján)

**Példa (TruLaser 3030 Fiber):**
```php
$machine = Machine::create([
    'name' => 'TruLaser 3030 Fiber',
    'manufacturer' => 'TRUMPF',
    'category' => 'lezervago',
    'category_label' => 'Fő gép · Fiber lézer',
    'badge' => '5 kW',
    'section' => 'trulaser',
    'short_description' => 'Nagy teljesítményű fiber lézervágó alacsony és közepes vastagságú lemezekhez...',
    'order' => 1,
    'is_active' => true,
    'is_featured' => false,
]);

$machine->specs()->createMany([
    ['spec_key' => 'Munkaterület', 'spec_value' => '3000 × 1500 mm', 'order' => 1],
    ['spec_key' => 'Lézerforrás', 'spec_value' => 'Fiber, 5000 W', 'order' => 2],
    ['spec_key' => 'Max. vastagság (acél)', 'spec_value' => '25 mm', 'order' => 3],
    // ...
]);
```

#### 5.3 Seeder futtatása
```bash
php artisan db:seed --class=MachineSeeder
php artisan db:seed --class=PageSettingSeeder
```

#### 5.4 Ellenőrzés
- Admin panel: `/admin/machines` → 15 gép megjelenik
- Szerkesztés: műszaki adatok megjelennek

---

### FÁZIS 6: Frontend Blade template (1.5 nap)

#### 6.1 Route létrehozása
**`routes/web.php`:**
```php
use App\Http\Controllers\GepparkController;

Route::get('/geppark', [GepparkController::class, 'index'])->name('geppark');
```

#### 6.2 Controller létrehozása
```bash
php artisan make:controller GepparkController
```

**`app/Http/Controllers/GepparkController.php`:**
```php
class GepparkController extends Controller
{
    public function index()
    {
        $machines = Machine::where('is_active', true)
            ->orderBy('order')
            ->with('specs')
            ->get()
            ->groupBy('section');

        $settings = PageSetting::where('group', 'geppark')
            ->pluck('value', 'key');

        return view('geppark', compact('machines', 'settings'));
    }
}
```

#### 6.3 Blade view létrehozása
**`resources/views/geppark.blade.php`:**

1. Meglévő `geppark.html` másolása
2. `<html>`, `<head>`, navbar, footer megtartása
3. Dinamikus szekciók lecserélése Blade direktívákkal

**Példa (TRUMPF Lézervágók szekció):**
```blade
<section class="bg-[#0d1b2a] py-16 px-6 md:px-12" id="trulaser">
  <div class="container">
    <h2 class="font-heading font-semibold text-4xl">
      {{ $settings['trulaser_section_title'] ?? 'TRUMPF Lézervágók' }}
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      @foreach($machines['trulaser'] ?? [] as $machine)
        <div class="machine-card bg-[#122135] border border-white/8 p-6">
          <div class="flex items-start justify-between mb-4">
            <div>
              <div class="text-[#cc2222] text-xs">{{ $machine->category_label }}</div>
              <h3 class="font-heading font-semibold text-2xl">{{ $machine->name }}</h3>
            </div>
            @if($machine->badge)
              <div class="badge">{{ $machine->badge }}</div>
            @endif
          </div>
          <p class="text-white/45 text-sm">{{ $machine->short_description }}</p>
          <table class="spec-table w-full text-sm">
            <tbody>
              @foreach($machine->specs as $spec)
                <tr>
                  <th class="text-left">{{ $spec->spec_key }}</th>
                  <td class="text-right text-white/70">{{ $spec->spec_value }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endforeach
    </div>
  </div>
</section>
```

#### 6.4 Tailwind CSS integrálás
- Meglévő CDN átmenetileg megtartása
- Opcionális: Laravel Mix / Vite build (production-hoz)

#### 6.5 Assets másolása
- `public/assets/images/` → Laravel `public/` mappába
- Statikus CSS/JS fájlok (ha vannak) átmásolása

#### 6.6 Tesztelés
- Böngésző: `http://localhost:8000/geppark`
- Ellenőrzés:
  - Gépek megjelennek (DB-ből)
  - Műszaki adatok táblázat OK
  - Design változatlan (Tailwind stílusok működnek)
  - Animációk (scroll reveal, hover) működnek

---

### FÁZIS 7: Képfeltöltés + Media Library (1 nap)

#### 7.1 Spatie Media Library telepítés
```bash
composer require spatie/laravel-medialibrary
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider"
php artisan migrate
```

#### 7.2 Machine model frissítése
```php
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Machine extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(800)
            ->height(600)
            ->format('webp')
            ->quality(85);
    }
}
```

#### 7.3 Filament form frissítése
```php
SpatieMediaLibraryFileUpload::make('image')
    ->collection('machines')
    ->image()
    ->maxSize(2048)
    ->conversion('thumb'),
```

#### 7.4 Blade template frissítése
```blade
@if($machine->hasMedia('machines'))
  <img src="{{ $machine->getFirstMediaUrl('machines', 'thumb') }}"
       alt="{{ $machine->name }}"
       loading="lazy">
@endif
```

#### 7.5 Storage symlink
```bash
php artisan storage:link
```

#### 7.6 Tesztelés
- Admin: Kép feltöltés + mentés
- Frontend: Kép megjelenik
- WebP konverzió ellenőrzés (DevTools Network tab)

---

### FÁZIS 8: Drag-and-drop sorrendezés (0.5 nap)

#### 7.1 Spatie Eloquent Sortable telepítés
```bash
composer require spatie/eloquent-sortable
```

#### 7.2 Machine model frissítése
```php
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Machine extends Model implements Sortable
{
    use SortableTrait;

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];
}
```

#### 7.3 Filament table frissítése
```php
->reorderable('order')
```

#### 7.4 Tesztelés
- Admin: Gépek listája → drag-and-drop ikon → húzás
- Frontend: Sorrend megváltozik

---

### FÁZIS 9: Oldalbeállítások (Page Settings) (0.5 nap)

#### 9.1 PageSettingSeeder demo adatokkal
```php
PageSetting::create([
    'key' => 'geppark_title',
    'value' => 'Géppark',
    'type' => 'text',
    'group' => 'geppark',
]);

PageSetting::create([
    'key' => 'geppark_intro',
    'value' => 'TRUMPF és AMADA gépek – a világ vezető ipari márkái...',
    'type' => 'textarea',
    'group' => 'geppark',
]);

// Summary badges JSON
PageSetting::create([
    'key' => 'geppark_summary_badges',
    'value' => json_encode([
        ['text' => 'TRUMPF lézervágók – 4 gép'],
        ['text' => 'TRUMPF hajlítók – 6 gép'],
        ['text' => 'AMADA hajlítók – 4 gép'],
        ['text' => 'Porfestés + csőhajlítás'],
    ]),
    'type' => 'json',
    'group' => 'geppark',
]);
```

#### 9.2 Filament Settings page
Egyedi Settings oldal létrehozása (Filament Pages)

#### 9.3 Blade integráció
```blade
<h1>{{ $settings['geppark_title'] ?? 'Géppark' }}</h1>
<p>{{ $settings['geppark_intro'] }}</p>

@php
  $badges = json_decode($settings['geppark_summary_badges'] ?? '[]', true);
@endphp

@foreach($badges as $badge)
  <div class="badge">{{ $badge['text'] }}</div>
@endforeach
```

---

### FÁZIS 10: Finomhangolás + tesztelés (1 nap)

#### 10.1 Responsive tesztelés
- Desktop: 1920×1080
- Tablet: iPad (768×1024)
- Mobile: iPhone 13 (390×844)

#### 10.2 Böngésző kompatibilitás
- Chrome (latest)
- Firefox (latest)
- Safari (macOS + iOS)
- Edge (latest)

#### 10.3 Validáció tesztek
- Üres mezők küldése
- Kép méret limit (3 MB)
- HTML injection (XSS védelem)

#### 10.4 Teljesítmény ellenőrzés
- Lighthouse audit (Chrome DevTools)
- Képek lazy load
- DB query optimalizálás (N+1 probléma ellenőrzés)

#### 10.5 Admin UX javítások
- Tooltip-ek hozzáadása
- Placeholder szövegek
- Help textek mezők alatt

---

### FÁZIS 11: Használati útmutató készítése (0.5 nap)

#### 11.1 Screencast videó készítése (3-5 perc)
**Forgatókönyv:**
1. Bejelentkezés (`/admin/login`)
2. Gépek lista megtekintése
3. Új gép hozzáadása (form kitöltés, kép feltöltés, mentés)
4. Gép szerkesztése (műszaki adat hozzáadás)
5. Sorrend változtatása (drag-and-drop)
6. Gép inaktiválása (toggle)
7. Frontend előnézet (géppark oldal)

**Eszköz:** Loom / OBS Studio / QuickTime (macOS)

#### 11.2 PDF útmutató (1 oldal)
**Tartalom:**
- Bejelentkezési adatok
- Admin panel URL
- Gépek hozzáadása (lépésről lépésre)
- Képek feltöltése
- Sorrend változtatás
- Oldal beállítások szerkesztése
- Előnézet megtekintése

**Formátum:** A4 PDF, Linardics branding (logó, színek)

---

### FÁZIS 12: Deployment (cPanel) (1 nap)

#### 12.1 cPanel előkészítés
- PHP verzió: 8.2+
- Composer elérhetőség
- MySQL 8.0 adatbázis létrehozása
- SSH hozzáférés (opcionális)

#### 12.2 Git repository
```bash
git init
git add .
git commit -m "Initial commit: Linardics CMS demo"
git branch -M main
git remote add origin https://github.com/birotamas/linardics-cms.git
git push -u origin main
```

#### 12.3 cPanel Git Version Control
- Repository URL: `https://github.com/birotamas/linardics-cms.git`
- Branch: `main`
- Deploy path: `/home/username/public_html/cms-demo`

#### 12.4 Production .env konfiguráció
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://linardics.o11.hu/cms-demo

DB_DATABASE=linardics_cms_prod
DB_USERNAME=username
DB_PASSWORD=secure_password
```

#### 12.5 Composer install (SSH)
```bash
cd public_html/cms-demo
composer install --optimize-autoloader --no-dev
php artisan key:generate
php artisan migrate --force
php artisan db:seed
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 12.6 Webserver konfiguráció
**Public directory:** `/public_html/cms-demo/public`

**.htaccess (Laravel alapértelmezett):**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

#### 12.7 Production tesztelés
- URL: `https://linardics.o11.hu/cms-demo/geppark`
- Admin: `https://linardics.o11.hu/cms-demo/admin/login`
- SSL certifikát ellenőrzés
- Képek betöltődnek

---

## 6. Nem tartozik a demo scope-jába

### Kizárt funkciók (későbbi fázishoz)
- ❌ Többi oldal (Főoldal, Szolgáltatások, Kapcsolat stb.) CMS kezelése
- ❌ Többnyelvűség (HU/EN) admin felületen
- ❌ SEO mezők (meta title, description) szerkesztése
- ❌ Blog / tudástár modul
- ❌ Felhasználói jogosultságok (role-based access control)
- ❌ Audit log (ki mit módosított)
- ❌ Verziókövetés (content versioning)
- ❌ Média könyvtár (shared image gallery)
- ❌ Publikálási időzítés (scheduled publishing)

---

## 7. Átadandó deliverable-ek

### ✅ 1. Működő admin felület
- **URL:** `https://linardics.o11.hu/cms-demo/admin`
- **Funkciók:**
  - Bejelentkezés (email + jelszó)
  - Gépek CRUD (Create, Read, Update, Delete)
  - Drag-and-drop sorrendezés
  - Képfeltöltés (WebP konverzió)
  - Műszaki adatok kezelése (repeater mezők)
  - Aktív/inaktív toggle
  - Oldal beállítások szerkesztése

### ✅ 2. Működő frontend oldal
- **URL:** `https://linardics.o11.hu/cms-demo/geppark`
- **Funkciók:**
  - Dinamikus tartalom (DB-ből tölt)
  - Meglévő design és animációk megőrzése
  - Responsive (mobil, tablet, desktop)
  - WebP képek, lazy load
  - SEO meta tagok (meglévő)

### ✅ 3. Rövid használati útmutató
- **Formátum:** PDF (1 oldal) + Screencast videó (3-5 perc)
- **Tartalom:**
  - Bejelentkezési adatok
  - Gép hozzáadása / szerkesztése
  - Képfeltöltés
  - Sorrend változtatás
  - Előnézet megtekintése

### ✅ 4. Forráskód + dokumentáció
- **GitHub repository:** Teljes Laravel projekt
- **README.md:** Telepítési útmutató (local + production)
- **DEPLOYMENT.md:** cPanel deploy lépések
- **API_ENDPOINTS.md:** Route lista (ha lesz API későbbi bővítéshez)

---

## 8. Költség- és időbecslés

### Fázisok időtartama

| Fázis | Feladat | Becsült idő |
|-------|---------|-------------|
| 1 | Laravel telepítés + konfiguráció | 1 nap |
| 2 | Filament telepítés + admin user | 0.5 nap |
| 3 | Adatbázis séma + modellek | 1 nap |
| 4 | Filament Resources (admin CRUD) | 2 nap |
| 5 | Seeder (demo adatok) | 0.5 nap |
| 6 | Frontend Blade template | 1.5 nap |
| 7 | Képfeltöltés + Media Library | 1 nap |
| 8 | Drag-and-drop sorrendezés | 0.5 nap |
| 9 | Oldal beállítások | 0.5 nap |
| 10 | Finomhangolás + tesztelés | 1 nap |
| 11 | Használati útmutató | 0.5 nap |
| 12 | Deployment (cPanel) | 1 nap |
| **ÖSSZESEN** | | **11 nap** |

### Ajánlott ütemezés

**1. hét (hétfő-péntek):**
- FÁZIS 1-5: Backend + admin panel alapok
- Milestone: Admin panel működik, gépek listája látható

**2. hét (hétfő-csütörtök):**
- FÁZIS 6-9: Frontend + képfeltöltés + finomhangolás
- Milestone: Teljes működő demo (local)

**2. hét (péntek):**
- FÁZIS 10-12: Tesztelés + dokumentáció + deployment
- Milestone: Production deploy + átadás

**Puffer:** +2-3 nap (unplanned issues, bugfixek)

---

## 9. Kockázatok és kérdések

### Tisztázandó kérdések (ügyfél felé)

#### 9.1 Hosting környezet
- ❓ **cPanel PHP verzió:** Elérhető-e PHP 8.2+?
- ❓ **Composer:** Telepíthető-e cPanel-en vagy SSH szükséges?
- ❓ **Adatbázis:** MySQL 8.0 elérhető?
- ❓ **SSL certifikát:** Van-e a demo subdomain-en? (pl. `cms-demo.linardics.o11.hu`)
- ❓ **Disk space:** Mennyit foglalhat a CMS (Laravel + képek)?

#### 9.2 Tartalom
- ❓ **Gép képek:** Van-e minden géphez saját fotó, vagy placeholder képeket használjunk?
- ❓ **Műszaki adatok:** A jelenlegi `geppark.html` adatok pontosak és teljesek?
- ❓ **Kategóriák:** Fix kategóriák (TRUMPF Lézervágó, AMADA Hajlító) vagy dinamikusan bővíthetők?

#### 9.3 Funkcionális
- ❓ **Élő előnézet:** Kell-e inline preview az admin panelen (plusz pont), vagy elég külön tab?
- ❓ **Több admin user:** Demo-ban 1 admin elég, de production-ban több felhasználó?
- ❓ **Backup:** Szükséges-e automatikus DB backup funkció?

#### 9.4 Design
- ❓ **Admin panel testreszabás:** Filament alapértelmezett design OK, vagy Linardics branding (logo, színek)?
- ❓ **Login screen:** Custom design vagy Filament default?

### Technikai kockázatok

#### 🟡 Közepes kockázat
- **cPanel Composer korlátok:** Ha nincs SSH, nehezebb a deploy
  - **Megoldás:** Composer install local-ban → Git commit vendor/ (nem ideális, de működik)

- **Képfeltöltés file size limit:** cPanel-en alacsony `upload_max_filesize` (2 MB)
  - **Megoldás:** `.htaccess` vagy `php.ini` override

#### 🟢 Alacsony kockázat
- **Laravel routing conflict:** A meglévő statikus `geppark.html` és a dinamikus route ütközik
  - **Megoldás:** Dinamikus route: `/geppark-cms`, vagy `.htaccess` rewrite

- **Tailwind CSS CDN → Build:** Production-ban lassú lehet a CDN
  - **Megoldás:** Átmeneti CDN megtartása, későbbi optimalizálás

---

## 10. Javaslatok későbbi fejlesztéshez

### Fázis 2 (production-ready verzió)

#### 10.1 Teljes weboldal CMS-ezése
- Főoldal (Hero, Szolgáltatások, Miért mi? szekcio)
- Szolgáltatások aloldalak (5 db oldal)
- Referenciák oldal (projekt galéria + CRUD)
- Rólunk oldal (timeline, értékek, tanúsítványok)
- GYIK (FAQ CRUD, sorrendezés)

#### 10.2 Többnyelvűség (HU/EN)
- Spatie Laravel Translatable
- Nyelv switcher (minden oldalon)
- URL struktúra: `/hu/geppark`, `/en/machine-park`
- Admin panel: Tabbed translation mezők

#### 10.3 SEO modul
- Meta title / description szerkesztés
- OG image feltöltés oldalanként
- Sitemap generálás (automatikus)
- Schema.org JSON-LD editor

#### 10.4 Fejlett felhasználói szerepkörök
- Super Admin: teljes hozzáférés
- Editor: tartalom szerkesztés (gépek, oldalak)
- Viewer: csak olvasási jog
- Audit log: ki mit módosított, mikor

#### 10.5 Blog / Hírek modul
- Blogbejegyzések CRUD
- Kategóriák, címkék
- Kiemelés (featured posts)
- RSS feed

#### 10.6 Kapcsolati form backend
- `kapcsolat.html` wizard backend integrálás
- Email küldés (SMTP / SendGrid)
- Üzenetek tárolása DB-ben
- Admin értesítés (új üzenet érkezett)

#### 10.7 Teljesítmény optimalizálás
- Tailwind CSS build + purge (CDN lecserélése)
- Redis cache (DB query cache)
- Image lazy load (natív + Intersection Observer)
- Cloudflare CDN (statikus asset-ek)

#### 10.8 Biztonsági hardening
- 2FA (Two-Factor Authentication)
- Rate limiting (brute-force védelem)
- CSRF védelem (Laravel alapértelmezett)
- XSS/SQL injection védelem (Eloquent ORM)

---

## 11. Sikerességi kritériumok

### Demo elfogadásához szükséges

#### ✅ Funkcionális
- [ ] Admin panel elérhető, bejelentkezés működik
- [ ] 15 gép megjelenik az admin listában
- [ ] Új gép hozzáadása működik (form + kép + műszaki adatok)
- [ ] Gép szerkesztése működik (minden mező)
- [ ] Gép törlése működik (soft delete vagy hard delete)
- [ ] Drag-and-drop sorrendezés működik
- [ ] Képfeltöltés működik (JPG, PNG, WebP, max 2 MB)
- [ ] Frontend oldal dinamikusan tölt (DB-ből)
- [ ] Műszaki adatok táblázat megjelenik
- [ ] Inaktív gépek nem látszanak a frontend-en

#### ✅ UX / Design
- [ ] Admin panel intuitív, könnyen kezelhető
- [ ] Form validáció működik (hibás adatok esetén piros highlight)
- [ ] Sikeres mentés után toast üzenet
- [ ] Frontend design változatlan (Tailwind stílusok)
- [ ] Animációk (scroll reveal, hover) működnek
- [ ] Responsive (mobil, tablet, desktop)

#### ✅ Teljesítmény
- [ ] Frontend betöltési idő < 2 mp (gyors internet)
- [ ] Képek lazy load (nem above-fold képek)
- [ ] WebP konverzió működik
- [ ] Nincs N+1 query probléma (Laravel Debugbar ellenőrzés)

#### ✅ Dokumentáció
- [ ] README.md: telepítési útmutató (local)
- [ ] DEPLOYMENT.md: cPanel deploy lépések
- [ ] PDF útmutató (1 oldal): admin használat
- [ ] Screencast videó (3-5 perc): demo bemutatás

#### ✅ Biztonság
- [ ] .env fájl nem committed (gitignore)
- [ ] Admin login CSRF védelem
- [ ] XSS védelem (Blade escape `{{ }}`)
- [ ] SQL injection védelem (Eloquent ORM)
- [ ] Fájlfeltöltés validáció (csak kép, max 2 MB)

---

## 12. Következő lépések

### Azonnal (projekt indítás előtt)
1. **Ügyfél jóváhagyás:** PDF brief elfogadása
2. **Kérdések tisztázása:** 9. fejezet (Kockázatok) kérdések megválaszolása
3. **Hosting hozzáférés:** cPanel login, SSH kulcs (ha szükséges)
4. **Git repository:** Létrehozás (GitHub privát repo)

### 1. nap (projekt kickoff)
1. Laravel projekt inicializálás
2. GitHub repo setup + első commit
3. Local fejlesztési környezet tesztelés
4. Filament telepítés + admin user

### Heti status update
- **Hétfő:** Fázis állapot riport (mi készült el, mi van folyamatban)
- **Csütörtök:** Demo session (ügyfél megtekinti az aktuális állapotot)
- **Péntek:** Heti összefoglaló + következő hét tervek

### Befejezés (12. nap)
- Production deploy
- Átadás dokumentáció (URL-ek, bejelentkezési adatok)
- Screencast videó és PDF útmutató megosztása
- Feedback session (ügyfél kipróbálja, kérdések, javítások)

---

## 13. Kapcsolattartás és support

### Fejlesztés alatt
- **Slack / Email:** Azonnali kérdések (technikai, funkcionális)
- **Heti demo:** Live screen share (Zoom / Google Meet)
- **Git commits:** Napi push, átlátható commit message-ek

### Átadás után (warranty period)
- **30 napos support:** Bugfixek, kis módosítások ingyenes
- **Dokumentáció:** Minden kérdésre válasz a README-ben
- **Hotline:** Email support (24h válaszidő)

### Hosszú távú support (opcionális)
- **Havi retainer:** Minor feature update-ek, tartalomfeltöltés segítség
- **Évente 1× nagyobb verzió frissítés:** Laravel / Filament upgrade
- **Hosting monitorozás:** Uptime, teljesítmény, biztonsági frissítések

---

## 14. Megjegyzések a fejlesztőknek

### Best practices
- **Git commit:** Sűrű, kis commit-ok (feature branch workflow)
- **Code comment:** Komplex logikához magyar komment (magyar ügyfél)
- **Migration:** Mindig visszavonható (`down()` metódus implementálva)
- **Seeder:** Idempotens (újrafuttatható adatvesztés nélkül)
- **Blade component:** Ismétlődő UI elemek (pl. machine-card) component-be

### Figyelendő
- **N+1 query:** `with('specs')` eager loading kötelező
- **Image optimization:** Spatie Media Library automatikus WebP konverzió
- **Validation:** Backend validáció minden form submit-nél (nem csak frontend)
- **HTTPS:** Production környezetben SSL kötelező (mixed content warning)

### Tesztelési checklist
- [ ] Admin bejelentkezés (helyes/helytelen jelszó)
- [ ] Gép hozzáadás (minden mező kitöltve)
- [ ] Gép hozzáadás (kötelező mezők üresen → validációs hiba)
- [ ] Képfeltöltés (3 MB fájl → hiba, 1 MB → OK)
- [ ] Műszaki adatok (5 db adat hozzáadása, drag-and-drop sorrend)
- [ ] Gép inaktiválás (toggle → frontend-en eltűnik)
- [ ] Drag-and-drop sorrend (3 gép helycseréje → frontend sorrend OK)
- [ ] Responsive (Chrome DevTools: iPhone, iPad, Desktop)
- [ ] Böngésző kompatibilitás (Chrome, Firefox, Safari)

---

**Verzió:** 1.0
**Utolsó frissítés:** 2026-03-18
**Készítette:** Claude (Anthropic) + Bíró Tamás
**Státusz:** Jóváhagyásra vár

---

## Mellékletek

### A. Minta .env fájl (production)
```env
APP_NAME="Linardics CMS"
APP_ENV=production
APP_KEY=base64:XXXXX...
APP_DEBUG=false
APP_URL=https://linardics.o11.hu/cms-demo

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=linardics_cms
DB_USERNAME=linardics_user
DB_PASSWORD=secure_password_here

FILESYSTEM_DISK=public

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@linardics.hu
MAIL_PASSWORD=app_specific_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@linardics.hu
MAIL_FROM_NAME="Linardics CMS"
```

### B. Hasznos parancsok

**Local fejlesztés:**
```bash
php artisan serve
php artisan migrate:fresh --seed
php artisan storage:link
```

**Cache kezelés:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**Production deploy:**
```bash
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### C. cPanel .htaccess (Laravel)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### D. GitHub repository struktúra
```
linardics-cms/
├── app/
│   ├── Filament/
│   │   └── Resources/
│   │       ├── MachineResource.php
│   │       └── PageSettingResource.php
│   ├── Http/
│   │   └── Controllers/
│   │       └── GepparkController.php
│   └── Models/
│       ├── Machine.php
│       ├── MachineSpec.php
│       └── PageSetting.php
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── MachineSeeder.php
│       └── PageSettingSeeder.php
├── resources/
│   └── views/
│       └── geppark.blade.php
├── public/
│   └── assets/
│       └── images/
├── .env.example
├── README.md
├── DEPLOYMENT.md
└── composer.json
```

---

**Projekt azonosító:** LINARDICS-CMS-DEMO-2026
**Projekt menedzser:** TBD
**Lead developer:** TBD
**Üzleti kapcsolattartó (Linardics):** TBD

---

*Ez a dokumentum a Linardics Kft. számára készült CMS demo fejlesztési tervet tartalmazza. A dokumentum titkos, csak a projekt résztvevői számára hozzáférhető.*
