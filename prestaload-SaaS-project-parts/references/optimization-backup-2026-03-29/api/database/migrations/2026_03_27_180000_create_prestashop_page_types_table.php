<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * @var array<int, array{code: string, name: string, description: string}>
     */
    private array $pageTypes = [
        [
            'code' => 'home',
            'name' => 'Home',
            'description' => 'Homepage and storefront landing page.',
        ],
        [
            'code' => 'product',
            'name' => 'Product',
            'description' => 'Individual product detail pages.',
        ],
        [
            'code' => 'category',
            'name' => 'Category',
            'description' => 'Category and collection listing pages.',
        ],
        [
            'code' => 'listing-other',
            'name' => 'Listing Other',
            'description' => 'Manufacturer, supplier, search, new products, best sales, and price drop listings.',
        ],
        [
            'code' => 'cms',
            'name' => 'CMS',
            'description' => 'Content and editorial pages.',
        ],
        [
            'code' => 'contact',
            'name' => 'Contact',
            'description' => 'Contact and support request pages.',
        ],
        [
            'code' => 'stores',
            'name' => 'Stores',
            'description' => 'Store locator and physical store pages.',
        ],
        [
            'code' => 'cart',
            'name' => 'Cart',
            'description' => 'Shopping cart pages.',
        ],
        [
            'code' => 'checkout',
            'name' => 'Checkout',
            'description' => 'Checkout flow pages.',
        ],
        [
            'code' => 'order-confirmation',
            'name' => 'Order Confirmation',
            'description' => 'Order confirmation and thank-you pages.',
        ],
        [
            'code' => 'customer-account',
            'name' => 'Customer Account',
            'description' => 'My account, history, order detail, identity, addresses, discounts, guest tracking, authentication, password, and registration pages.',
        ],
        [
            'code' => 'error-404',
            'name' => 'Error 404',
            'description' => 'Not found pages.',
        ],
        [
            'code' => 'module-page',
            'name' => 'Module Page',
            'description' => 'Module-driven front-office pages.',
        ],
    ];

    public function up(): void
    {
        Schema::create('prestashop_page_types', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('prestashop_shop_urls', function (Blueprint $table): void {
            $table->foreignUuid('page_type_id')
                ->nullable()
                ->after('prestashop_shop_id')
                ->constrained('prestashop_page_types')
                ->nullOnDelete();

            $table->index(['prestashop_shop_id', 'page_type_id'], 'prestashop_shop_urls_shop_page_type_id_index');
        });

        $now = now();
        $seedRows = array_map(function (array $pageType) use ($now): array {
            return [
                'id' => (string) Str::uuid(),
                'code' => $pageType['code'],
                'name' => $pageType['name'],
                'description' => $pageType['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $this->pageTypes);

        DB::table('prestashop_page_types')->insert($seedRows);

        $idByCode = DB::table('prestashop_page_types')
            ->pluck('id', 'code')
            ->all();

        foreach ($idByCode as $code => $id) {
            DB::table('prestashop_shop_urls')
                ->where('page_type', $code)
                ->update([
                    'page_type_id' => $id,
                    'updated_at' => $now,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('prestashop_shop_urls', function (Blueprint $table): void {
            $table->dropIndex('prestashop_shop_urls_shop_page_type_id_index');
            $table->dropConstrainedForeignId('page_type_id');
        });

        Schema::dropIfExists('prestashop_page_types');
    }
};
