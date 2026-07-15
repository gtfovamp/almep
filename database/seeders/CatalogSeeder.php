<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use App\Models\ProductType;
use Illuminate\Database\Seeder;

/**
 * Демо-данные каталога: категория → подкатегория → товары (со спецификациями).
 * Идемпотентен: повторный запуск не плодит дубли (firstOrCreate по ключам).
 *
 * Запуск:  php artisan db:seed --class=CatalogSeeder
 */
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        // ── Тип продукции ──
        $type = ProductType::firstOrCreate(
            ['name' => 'EKF PROxima'],
            ['name_en' => 'EKF PROxima', 'name_az' => 'EKF PROxima']
        );

        // ── Категория ──
        $cat = Category::firstOrCreate(
            ['name' => 'Электротехника'],
            [
                'name_en' => 'Electrical', 'name_az' => 'Elektrotexnika',
                'description'    => 'Сертифицированные электротехнические материалы и оборудование.',
                'description_en' => 'Certified electrical materials and equipment.',
                'description_az' => 'Sertifikatlı elektrik materialları və avadanlıqları.',
                'order_index' => 1,
            ]
        );

        // ── Подкатегория ──
        $sub = Subcategory::firstOrCreate(
            ['category_id' => $cat->id, 'name' => 'Автоматические выключатели'],
            [
                'name_en' => 'Circuit breakers', 'name_az' => 'Avtomatik açarlar',
                'description'    => 'Автоматические выключатели для защиты электрических цепей от перегрузок и коротких замыканий.',
                'description_en' => 'Circuit breakers protecting electrical circuits from overloads and short circuits.',
                'description_az' => 'Elektrik dövrələrini həddindən artıq yüklənmə və qısaqapanmadan qoruyan avtomatik açarlar.',
                'order_index' => 1,
            ]
        );

        // ── Товары ──
        $products = [
            [
                'article' => 'MCB4763-3-40C-PRO',
                'name'    => 'Автоматический выключатель 3P 40A (C) 4,5kA ВА 47-63 EKF PROxima',
                'name_en' => 'Circuit breaker 3P 40A (C) 4.5kA VA 47-63 EKF PROxima',
                'name_az' => 'Avtomatik açar 3P 40A (C) 4,5kA VA 47-63 EKF PROxima',
                'description'    => 'Модульный автоматический выключатель для защиты кабелей и проводов от перегрузок и токов короткого замыкания. Характеристика срабатывания C, номинальный ток 40А, три полюса.',
                'description_en' => 'Modular circuit breaker for protecting cables and wires from overloads and short-circuit currents. Tripping characteristic C, rated current 40A, three poles.',
                'description_az' => 'Kabel və naqilləri həddindən artıq yüklənmə və qısaqapanma cərəyanlarından qoruyan modul avtomatik açar. İşləmə xarakteristikası C, nominal cərəyan 40A, üç qütb.',
                'specs' => [
                    ['Номинальный ток', 'Rated current', 'Nominal cərəyan', '40 А'],
                    ['Количество полюсов', 'Number of poles', 'Qütb sayı', '3P'],
                    ['Характеристика', 'Characteristic', 'Xarakteristika', 'C'],
                    ['Отключающая способность', 'Breaking capacity', 'Söndürmə qabiliyyəti', '4,5 kA'],
                    ['Номинальное напряжение', 'Rated voltage', 'Nominal gərginlik', '400 В'],
                    ['Серия', 'Series', 'Seriya', 'ВА 47-63'],
                    ['Бренд', 'Brand', 'Brend', 'EKF PROxima'],
                    ['Гарантия', 'Warranty', 'Zəmanət', '12 мес.'],
                ],
            ],
            [
                'article' => 'MCB4763-1-16C-PRO',
                'name'    => 'Автоматический выключатель 1P 16A (C) 4,5kA ВА 47-63 EKF PROxima',
                'name_en' => 'Circuit breaker 1P 16A (C) 4.5kA VA 47-63 EKF PROxima',
                'name_az' => 'Avtomatik açar 1P 16A (C) 4,5kA VA 47-63 EKF PROxima',
                'description'    => 'Однополюсный модульный автоматический выключатель, номинальный ток 16А, характеристика C.',
                'description_en' => 'Single-pole modular circuit breaker, rated current 16A, characteristic C.',
                'description_az' => 'Birqütblü modul avtomatik açar, nominal cərəyan 16A, xarakteristika C.',
                'specs' => [
                    ['Номинальный ток', 'Rated current', 'Nominal cərəyan', '16 А'],
                    ['Количество полюсов', 'Number of poles', 'Qütb sayı', '1P'],
                    ['Характеристика', 'Characteristic', 'Xarakteristika', 'C'],
                    ['Отключающая способность', 'Breaking capacity', 'Söndürmə qabiliyyəti', '4,5 kA'],
                    ['Серия', 'Series', 'Seriya', 'ВА 47-63'],
                    ['Бренд', 'Brand', 'Brend', 'EKF PROxima'],
                ],
            ],
            [
                'article' => 'MCB4763-2-25C-PRO',
                'name'    => 'Автоматический выключатель 2P 25A (C) 4,5kA ВА 47-63 EKF PROxima',
                'name_en' => 'Circuit breaker 2P 25A (C) 4.5kA VA 47-63 EKF PROxima',
                'name_az' => 'Avtomatik açar 2P 25A (C) 4,5kA VA 47-63 EKF PROxima',
                'description'    => 'Двухполюсный модульный автоматический выключатель, номинальный ток 25А, характеристика C.',
                'description_en' => 'Two-pole modular circuit breaker, rated current 25A, characteristic C.',
                'description_az' => 'İkiqütblü modul avtomatik açar, nominal cərəyan 25A, xarakteristika C.',
                'specs' => [
                    ['Номинальный ток', 'Rated current', 'Nominal cərəyan', '25 А'],
                    ['Количество полюсов', 'Number of poles', 'Qütb sayı', '2P'],
                    ['Характеристика', 'Characteristic', 'Xarakteristika', 'C'],
                    ['Отключающая способность', 'Breaking capacity', 'Söndürmə qabiliyyəti', '4,5 kA'],
                    ['Серия', 'Series', 'Seriya', 'ВА 47-63'],
                    ['Бренд', 'Brand', 'Brend', 'EKF PROxima'],
                ],
            ],
        ];

        $idx = 0;
        foreach ($products as $p) {
            $product = Product::firstOrCreate(
                ['article' => $p['article']],
                [
                    'subcategory_id'  => $sub->id,
                    'product_type_id' => $type->id,
                    'name'    => $p['name'],
                    'name_en' => $p['name_en'],
                    'name_az' => $p['name_az'],
                    'description'    => $p['description'],
                    'description_en' => $p['description_en'],
                    'description_az' => $p['description_az'],
                    'in_stock' => true,
                    'order_index' => $idx++,
                ]
            );

            // Спецификации
            if ($product->specifications()->count() === 0) {
                $si = 0;
                foreach ($p['specs'] as $s) {
                    ProductSpecification::create([
                        'product_id' => $product->id,
                        'key'    => $s[0], 'key_en' => $s[1], 'key_az' => $s[2],
                        'value'  => $s[3], 'value_en' => $s[3], 'value_az' => $s[3],
                        'order_index' => $si++,
                    ]);
                }
            }

            // Изображение-заглушка (замените на реальные фото через админку)
            if ($product->images()->count() === 0) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url'  => 'https://placehold.co/600x450/fafafb/1C508F?text=EKF+PROxima',
                    'is_primary' => true,
                    'order_index' => 0,
                ]);
            }
        }
    }
}
