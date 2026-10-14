# Converge-Reveal

ویجت المنتور برای ساخت **کالکشن اسکرولی**: عنوان وسط صفحه با اسکرول محو می‌شود و کارت‌ها از دو طرف به وسط می‌آیند و یک گرید می‌سازند. در موبایل یک افکت جداگانه (ورود پله‌ای کارت‌ها) دارد.

> پیش‌نمایش زنده: فایل [index.html](index.html) را باز کنید (یا لینک GitHub Pages ریپو).

- ورژن: `1.0.0`
- وردپرس 6.0 به بالا، PHP 7.4 به بالا، المنتور 3.20 به بالا
- بدون jQuery و بدون کتابخانه خارجی؛ فقط یک CSS و یک JS خالص
- پشتیبانی کامل از RTL و LTR با CSS logical properties

---

## فهرست

1. [ساختار پروژه](#ساختار-پروژه)
2. [نصب](#نصب)
3. [استفاده](#استفاده)
4. [چطور کار می‌کند](#چطور-کار-می‌کند)
5. [توضیح فایل‌ها و توابع](#توضیح-فایلها-و-توابع)
6. [ساختار HTML و کلاس‌ها](#ساختار-html-و-کلاسها)
7. [متغیرهای CSS](#متغیرهای-css)
8. [کنترل‌های ویجت](#کنترلهای-ویجت)
9. [افزودن ویجت جدید](#افزودن-ویجت-جدید)
10. [نکات و محدودیت‌ها](#نکات-و-محدودیتها)

---

## ساختار پروژه

```
.
├── index.html                      صفحه پیش‌نمایش (دمو)
├── demo/                           تصاویر SVG دمو
│   ├── floor.svg
│   ├── wall.svg
│   ├── kitchen.svg
│   └── outdoor.svg
└── converge-reveal/                خود پلاگین وردپرس
    ├── converge-reveal.php         فایل اصلی: هدر، چک نسخه‌ها، ثبت ویجت و asset
    ├── widgets/
    │   └── class-mdc-reveal.php    کلاس ویجت (کنترل‌ها، render، content_template)
    └── assets/
        ├── css/reveal.css
        └── js/reveal.js
```

برای نصب فقط پوشه `converge-reveal/` لازم است؛ `index.html` و `demo/` فقط برای پیش‌نمایش‌اند.

---

## نصب

1. پوشه `converge-reveal` را zip کنید.
2. در وردپرس: **افزونه‌ها ← افزودن ← بارگذاری افزونه** و فایل zip را نصب کنید (یا پوشه را در `wp-content/plugins/` بگذارید).
3. افزونه را فعال کنید. المنتور باید نصب و فعال باشد؛ در غیر این صورت یک اعلان مدیریتی نمایش داده می‌شود.

> ویجت sticky است. آن را داخل کانتینری که `overflow: hidden/auto` دارد نگذارید، وگرنه چسبندگی کار نمی‌کند.

---

## استفاده

1. در المنتور، ویجت **Scroll Reveal Collection** را از دسته **Converge-Reveal** روی صفحه بکشید.
2. در **Content ← Cards** کارت‌ها را اضافه یا ویرایش کنید (تصویر، عنوان، لینک).
3. در **Content ← Centre Title** عنوان وسط را تنظیم کنید.
4. در تب **Style** ظاهر، فاصله‌ها و افکت‌ها را تغییر دهید.

نکته‌ها:
- پیش‌فرض: ۴ کارت (Floor، Wall، Kitchen، Outdoor) و عنوان «New Collection».
- نیمه اول کارت‌ها سمت اول و بقیه سمت دوم عنوان قرار می‌گیرند. با ۴ کارت: ۲ و ۲.
- اگر عنوان را خاموش کنید، کارت‌ها به‌صورت گرید ساده و بدون انیمیشن نمایش داده می‌شوند.
- در ادیتور المنتور انیمیشن پیش‌فرض خاموش است تا ویرایش راحت باشد. برای دیدنش **Preview Animation in Editor** را روشن کنید.

---

## چطور کار می‌کند

### دسکتاپ و تبلت (۷۶۸px به بالا)

ویجت یک **wrapper بلند** (`.mdc-reveal-wrap`) و داخلش یک **stage چسبنده** (`.mdc-reveal`, `position: sticky`) دارد:

```
ارتفاع wrapper = ارتفاع stage + مسیر اسکرول
```

وقتی کاربر اسکرول می‌کند، stage ثابت می‌ماند و wrapper زیرش رد می‌شود. JS فقط یک عدد **پیشرفت `p` بین ۰ تا ۱** حساب می‌کند و روی stage در متغیر `--mdc-p` می‌نویسد:

```
p = clamp( -wrap.top / (wrap.height - stage.height), 0, 1 )
```

بقیه کار با **CSS** است:

- **عنوان:** `opacity = 1 - min(p / fade, 1)`، که `fade` پیش‌فرض `0.35` است. یعنی تا ۳۵٪ مسیر کامل محو می‌شود.
- **کارت‌های سمت اول:** `translateX(+ عرضعنوان/2 × p)`
- **کارت‌های سمت دوم:** `translateX(− عرضعنوان/2 × p)`
- در RTL جهت با متغیر `--_dir: -1` برعکس می‌شود.

در حالت اولیه عنوان وسط است و کارت‌های بیرونی کمی از لبه بریده شده‌اند. با `p = 1` عنوان رفته و چهار کارت کنار هم یک گرید وسط‌چین ساخته‌اند.

### موبایل (۷۶۷px به پایین)

دو حالت:

| حالت | رفتار |
|---|---|
| **Mobile Converge Effect: روشن** (پیش‌فرض) | stage چسبنده؛ عنوان وسط صفحه روی گرید ۲ستونه. کارت‌ها یکی‌یکی (stagger) از چپ و راست به‌صورت متناوب وارد می‌شوند، همزمان بزرگ و کم‌کم دیده می‌شوند. |
| **خاموش** | گرید ساده با تعداد ستون قابل تنظیم، بدون انیمیشن. عنوان به‌صورت پیش‌فرض مخفی است (قابل نمایش). |

پیشرفت هر کارت از `p` سراسری و ایندکس کارت می‌آید:

```
شروع کارت i  = i × step
مدت هر کارت  = max(0.3, 1 − (n−1) × step)
t            = clamp( (p − i×step) / مدت , 0 , 1 )
e            = t² × (3 − 2t)            ← smoothstep برای حرکت نرم
جابجایی      = جهت × سمت × (1 − e) × فاصلهورود
scale        = 0.9 + 0.1 × e
opacity      = min(1, 2e)
```

جهت ورود هر کارت از زوج/فرد بودن ایندکس می‌آید (`--mdc-s` برابر `-1` یا `1`).

### کم‌حرکتی و ادیتور

- با `prefers-reduced-motion: reduce` افکت موبایل غیرفعال و گرید ساده نمایش داده می‌شود.
- در ادیتور المنتور، بدون روشن کردن Preview Animation، stage ثابت نیست؛ دسکتاپ حالت اولیه و موبایل گرید نهایی را نشان می‌دهد.

---

## توضیح فایل‌ها و توابع

### `converge-reveal/converge-reveal.php`

هدر پلاگین، ثابت‌ها و کلاس `Converge_Reveal_Plugin` (Singleton).

**ثابت‌ها**

| ثابت | کاربرد |
|---|---|
| `CONVERGE_REVEAL_VERSION` | نسخه؛ برای cache-busting فایل‌ها |
| `CONVERGE_REVEAL_FILE` / `_PATH` / `_URL` | مسیر و آدرس پلاگین |
| `CONVERGE_REVEAL_MIN_ELEMENTOR` | حداقل نسخه المنتور (`3.20.0`) |
| `CONVERGE_REVEAL_MIN_PHP` | حداقل نسخه PHP (`7.4`) |

**متدهای کلاس**

| متد | کار |
|---|---|
| `instance()` | نمونه یکتا را برمی‌گرداند |
| `init()` | روی `plugins_loaded` (اولویت ۲۰)؛ textdomain را لود می‌کند، نسخه PHP، فعال بودن المنتور و حداقل نسخه آن را چک می‌کند و در صورت مشکل اعلان نشان می‌دهد؛ در غیر این صورت hook ها را ثبت می‌کند |
| `register_category()` | دسته **Converge-Reveal** را در پنل المنتور می‌سازد |
| `register_widgets()` | فایل ویجت را require می‌کند و `Reveal` را ثبت می‌کند. **ویجت‌های بعدی را اینجا اضافه کنید** |
| `register_styles()` | با `wp_register_style` استایل را **ثبت** می‌کند (نه enqueue) |
| `register_scripts()` | با `wp_register_script` اسکریپت را **ثبت** می‌کند (`defer`، در footer) |
| `notice_min_php()` / `notice_missing_elementor()` / `notice_min_elementor()` | متن اعلان‌های هشدار |
| `print_notice()` | اعلان را چاپ می‌کند |

asset ها فقط در صفحه‌هایی لود می‌شوند که ویجت دارند (از طریق `get_style_depends()` و `get_script_depends()`).

---

### `converge-reveal/widgets/class-mdc-reveal.php`

کلاس `Converge_Reveal\Widgets\Reveal` (ارث‌بری از `Widget_Base`)، نام ویجت: `mdc-reveal`.

**متدهای شناسایی** (فقط مقدار ثابت، بدون خواندن تنظیمات)

`get_name()`، `get_title()`، `get_icon()`، `get_categories()`، `get_keywords()`، `get_style_depends()`، `get_script_depends()`

دو متد اجباری برای امنیت ادیتور:

```php
public function has_widget_inner_wrapper(): bool { return false; }
protected function is_dynamic_content(): bool { return false; }
```

> تنظیمات فقط داخل `render()` خوانده می‌شوند. در هیچ متد دیگری `get_settings()` صدا زده نمی‌شود، چون المنتور هنگام لود ادیتور این متدها را بدون وجود تنظیمات صدا می‌زند.

**ثبت کنترل‌ها**

| متد | محتوا |
|---|---|
| `register_controls()` | همه متدهای زیر را صدا می‌زند |
| `register_content_controls()` | تب Content: Cards، Card Titles، Centre Title، Behavior & Performance |
| `register_layout_style_controls()` | Style ← Layout & Stage |
| `register_title_style_controls()` | Style ← Centre Title |
| `register_card_style_controls()` | Style ← Cards (با تب‌های Normal/Hover) |
| `register_image_style_controls()` | Style ← Card Image (با تب‌های Normal/Hover) |
| `register_card_title_style_controls()` | Style ← Card Title |
| `get_tag_options()` | گزینه‌های تگ HTML: `h1`–`h6`، `div`، `span`، `p` |

**خروجی**

| متد | کار |
|---|---|
| `render()` | تنها جایی که `get_settings_for_display()` صدا زده می‌شود. کلاس‌های wrapper را می‌سازد و عنوان را بعد از نیمه اول کارت‌ها چاپ می‌کند |
| `render_card()` | یک کارت: تگ `<a>` (اگر لینک دارد) یا `<div>`، تصویر، عنوان کارت، `aria-label` برای لینک بدون عنوان نمایشی |
| `render_title()` | عنوان مرکزی با تگ انتخابی |
| `get_card_image()` | آدرس تصویر با Image Size، `width`/`height`، `srcset`، و متن `alt` را آماده می‌کند |
| `content_template()` | قالب Backbone برای پیش‌نمایش زنده ادیتور؛ ساختار و کلاس‌ها با `render()` یکی است |

**منطق alt تصویر:** متن Alt کارت ← alt کتابخانه مدیا ← اگر عنوان کارت نمایش داده نمی‌شود، خود عنوان؛ در غیر این صورت خالی (تزئینی)، چون عنوان کارت قبلاً نام آن را می‌گوید.

**بارگذاری تصویر** (کنترل `image_loading`): `lazy` (پیش‌فرض)، `eager`، یا `priority` (`eager` به‌علاوه `fetchpriority="high"` برای تصویر LCP).

---

### `converge-reveal/assets/js/reveal.js`

JS خالص و بدون وابستگی. فقط مقدار `--mdc-p` را می‌نویسد.

| تابع | کار |
|---|---|
| `clamp(value)` | عدد را بین ۰ و ۱ محدود می‌کند |
| `create(wrap)` | برای هر نمونه ویجت یک instance می‌سازد (ایدمپوتنت؛ با `WeakMap`) |
| ↳ `progress()` | پیشرفت اسکرول ۰..۱ را حساب می‌کند |
| ↳ `update()` | مقدار را روی `--mdc-p` می‌نویسد؛ فقط اگر تغییر کرده باشد. اگر wrapper از DOM حذف شده باشد، instance را نابود می‌کند |
| ↳ `request()` | با `requestAnimationFrame` اجرای `update()` را throttle می‌کند |
| ↳ `setActive(on)` | listener اسکرول را فقط وقتی ویجت نزدیک viewport است نگه می‌دارد |
| ↳ `destroy()` | همه listener ها و observer را پاک می‌کند |
| `initIn(root)` | همه ویجت‌های داخل یک المنت را مقداردهی می‌کند |
| `onElementReady(scope)` | هندلر hook المنتور؛ هم jQuery object و هم element را می‌پذیرد |
| `registerElementorHook()` | روی `frontend/element_ready/mdc-reveal.default` ثبت می‌شود |
| `boot()` | اجرای اولیه؛ در ادیتور برای رندرهای مجدد یک `MutationObserver` هم دارد |

نکات عملکردی:
- فقط ویجت‌هایی که نزدیک viewport هستند (`IntersectionObserver` با `rootMargin: 100px`) به اسکرول گوش می‌دهند.
- با خروج ویجت از صفحه یک بار `update()` اجرا می‌شود تا مقدار روی ۰ یا ۱ بنشیند.
- چند ویجت روی یک صفحه مستقل از هم کار می‌کنند.
- روی تغییر `matchMedia('(min-width: 768px)')` و `resize` دوباره محاسبه می‌شود.

---

### `converge-reveal/assets/css/reveal.css`

همه مقدارهای پیش‌فرض اینجاست؛ کنترل‌های المنتور فقط متغیرها را override می‌کنند. بخش‌ها:

1. wrapper و stage و متغیرهای داخلی `--_*`
2. عنوان مرکزی، کارت‌ها، تصویر، عنوان کارت
3. `@media (min-width: 768px)`: sticky و transform ها و حالت ادیتور
4. `@media (max-width: 767px)`: گرید ساده و افکت موبایل (`--m-fx`)
5. `@media (prefers-reduced-motion: reduce)` برای موبایل

---

## ساختار HTML و کلاس‌ها

```html
<div class="mdc-reveal-wrap mdc-reveal-wrap--animated mdc-reveal-wrap--clip mdc-reveal-wrap--m-fx">
  <div class="mdc-reveal" style="--mdc-count:4;">

    <a class="mdc-reveal__item mdc-reveal__item--start" style="--mdc-i:0;--mdc-s:-1;" href="#">
      <img class="mdc-reveal__image" src="..." alt="" width="410" height="640">
      <h3 class="mdc-reveal__item-title">Floor</h3>
    </a>
    <!-- نیمه اول کارت‌ها: --start -->

    <h2 class="mdc-reveal__title">New Collection</h2>

    <!-- نیمه دوم کارت‌ها: --end -->
  </div>
</div>
```

| کلاس | معنی |
|---|---|
| `mdc-reveal-wrap` | wrapper؛ مسیر اسکرول را می‌سازد |
| `--animated` / `--static` | عنوان دارد (انیمیشن) / ندارد (گرید ساده) |
| `--clip` | `overflow-x: clip` روی `html` و `body` (با `:has()`) |
| `--editor-anim` | پیش‌نمایش انیمیشن در ادیتور |
| `--m-fx` | افکت موبایل روشن |
| `--title-mobile` | نمایش عنوان در موبایل بدون افکت |
| `mdc-reveal` | stage چسبنده |
| `mdc-reveal__item` + `--start` / `--end` | کارت و گروه آن |
| `mdc-reveal__image` | تصویر کارت |
| `mdc-reveal__item-title` | عنوان کارت |
| `mdc-reveal__title` | عنوان مرکزی |

---

## متغیرهای CSS

متغیرهای زیر را کنترل‌های المنتور یا خودتان در CSS تنظیم می‌کنید (همه fallback دارند):

| متغیر | پیش‌فرض | معنی |
|---|---|---|
| `--mdc-stage-h` | `100svh` | ارتفاع stage |
| `--mdc-scroll` | `70vh` (موبایل `45vh`) | مسیر اسکرول |
| `--mdc-gap` | `58px` (موبایل `16px`) | فاصله بین کارت‌ها |
| `--mdc-pad` | `41px` | فاصله کناری (دسکتاپ/تبلت) |
| `--mdc-title-w` | `27vw` | عرض عنوان؛ مقدار حرکت کارت‌ها نصف آن است |
| `--mdc-fade` | `0.35` | پیشرفتی که عنوان در آن کامل محو می‌شود |
| `--mdc-m-cols` | `2` | ستون‌های موبایل (بدون افکت) |
| `--mdc-m-step` | `0.07` | تأخیر بین کارت‌ها در افکت موبایل |
| `--mdc-m-offset` | `55vw` | فاصله شروع ورود کارت‌ها در موبایل |

متغیرهای زیر را کد می‌نویسد؛ دستی تغییرشان ندهید:

| متغیر | نویسنده | معنی |
|---|---|---|
| `--mdc-count` | PHP (روی stage) | تعداد کارت‌ها |
| `--mdc-p` | JS (روی stage) | پیشرفت اسکرول ۰..۱ |
| `--mdc-i` | PHP (روی کارت) | ایندکس کارت |
| `--mdc-s` | PHP (روی کارت) | سمت ورود در موبایل (`-1`/`1`) |

---

## کنترل‌های ویجت

### Content

| بخش | کنترل‌ها |
|---|---|
| **Cards** (repeater) | `image`، `image_size`، `image_alt`، `title`، `link` (همگی dynamic) |
| **Card Titles** | `show_card_titles`، `card_title_tag` |
| **Centre Title** | `show_title`، `title`، `title_tag`، `mobile_effect`، `show_title_mobile` |
| **Behavior & Performance** | `image_loading`، `clip_overflow`، `editor_anim` |

### Style

| بخش | کنترل‌ها |
|---|---|
| **Layout & Stage** | `stage_height`، `scroll_distance`، `cards_gap`، `side_spacing`، `title_width`، `align_items`، `mobile_columns`، `mobile_offset`، `mobile_stagger`، `stage_padding_block`، `stage_padding_inline`، `stage_background` |
| **Centre Title** | `title_typography`، `title_color`، `title_text_shadow`، `title_align`، `title_margin`، `title_fade_end` |
| **Cards** | `card_gap`، `card_padding_block`، `card_padding_inline`، `card_radius`، `card_transition`، `card_focus_color`، پس‌زمینه/border/shadow در Normal و Hover |
| **Card Image** | `image_width`، `image_max_width`، `image_height`، `image_aspect_ratio` (پیش‌فرض `41 / 64`)، `image_object_fit`، `image_object_position`، `image_radius`، `image_transition`، opacity/filters/border/shadow در Normal و Hover |
| **Card Title** | `card_title_typography`، `card_title_text_shadow`، `card_title_align`، `card_title_margin`، رنگ Normal/Hover |

کنترل‌های سایز، فاصله و تراز **responsive** هستند. رنگ‌ها و تایپوگرافی با Global Colors/Fonts المنتور سازگارند.

---

## افزودن ویجت جدید

ویجت‌های بعدی را داخل همین پلاگین اضافه کنید:

1. فایل جدید `widgets/class-mdc-<name>.php` بسازید (کلاس در namespace `Converge_Reveal\Widgets`).
2. استایل و اسکریپتش را در `register_styles()` و `register_scripts()` ثبت کنید.
3. در `register_widgets()` فایل را `require_once` و ویجت را `register()` کنید.

---

## نکات و محدودیت‌ها

- افکت موبایل برای حداکثر حدود ۶ کارت مناسب است؛ با کارت بیشتر گرید از ارتفاع صفحه بیرون می‌زند. در آن حالت افکت موبایل را خاموش کنید.
- پیش‌نمایش ادیتور فقط `srcset`، `width/height` و alt کتابخانه مدیا را ندارد؛ در سایت اصلی همه آن‌ها هستند.
- عنوان مرکزی TEXT است (یک خط، `nowrap`)، نه چندخطی.
- `is_dynamic_content()` عمداً `false` ثابت است؛ اگر خروجی را وابسته به کاربر کردید باید `true` شود.
- مقدار `overflow-x: clip` روی `html/body` با `:has()` اعمال می‌شود و فقط وقتی ویجت در صفحه هست؛ از کنترل **Prevent Horizontal Page Overflow** قابل خاموش کردن است.
- فونت‌ها و رنگ‌های پیش‌فرض از قالب ارث می‌برند؛ از تب Style قابل تغییرند.
