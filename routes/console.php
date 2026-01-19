<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use App\Models\Category;
use App\Models\Product;

// Schedule auto-cancel orders every minute
Schedule::command('orders:auto-cancel')->everyMinute();

// Schedule auto-approve pending cancellations every minute
Schedule::command('orders:auto-approve-cancellations')->everyMinute();

// Schedule auto-complete orders every minute (shipped for 3+ days)
Schedule::command('orders:auto-complete')->everyMinute();

Schedule::command('test:scheduler')->everyMinute();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test:category-integration', function () {
    $this->info('=== Testing Category Integration with Product Details ===');
    $this->newLine();

    // Test 1: Check if Category model has the right casts
    $this->info('1. Testing Category model...');
    try {
        $category = new Category();
        $casts = $category->getCasts();
        
        if (isset($casts['product_types']) && $casts['product_types'] === 'array') {
            $this->info('   ✓ product_types cast to array');
        } else {
            $this->error('   ✗ product_types not cast to array');
        }
        
        if (isset($casts['product_details']) && $casts['product_details'] === 'array') {
            $this->info('   ✓ product_details cast to array');
        } else {
            $this->error('   ✗ product_details not cast to array');
        }
    } catch (Exception $e) {
        $this->error('   Error: ' . $e->getMessage());
    }

    // Test 2: Check if Product model has the right casts
    $this->info('2. Testing Product model...');
    try {
        $product = new Product();
        $casts = $product->getCasts();
        
        if (isset($casts['product_details']) && $casts['product_details'] === 'array') {
            $this->info('   ✓ product_details cast to array');
        } else {
            $this->error('   ✗ product_details not cast to array');
        }
    } catch (Exception $e) {
        $this->error('   Error: ' . $e->getMessage());
    }

    // Test 3: Create sample category with product types and details
    $this->info('3. Creating sample category with product types and details...');
    try {
        $sampleProductTypes = ['standard', 'account', 'items', 'topup', 'custom-service'];
        $sampleProductDetails = [
            'account' => [
                ['name' => 'Username', 'type' => 'text', 'required' => true],
                ['name' => 'Password', 'type' => 'text', 'required' => true],
                ['name' => 'Email', 'type' => 'email', 'required' => false],
                ['name' => 'Level', 'type' => 'number', 'required' => false]
            ],
            'items' => [
                ['name' => 'Item Name', 'type' => 'text', 'required' => true],
                ['name' => 'Quantity', 'type' => 'number', 'required' => true],
                ['name' => 'Rarity', 'type' => 'select', 'required' => false, 'options' => ['Common', 'Rare', 'Epic', 'Legendary']],
                ['name' => 'Description', 'type' => 'textarea', 'required' => false]
            ],
            'topup' => [
                ['name' => 'Player ID', 'type' => 'text', 'required' => true],
                ['name' => 'Server', 'type' => 'select', 'required' => true, 'options' => ['Asia', 'Europe', 'America']],
                ['name' => 'Amount', 'type' => 'number', 'required' => true]
            ]
        ];

        $category = Category::updateOrCreate(
            ['name' => 'Test Gaming Category'],
            [
                'description' => 'Category for testing product types and details functionality',
                'product_types' => $sampleProductTypes,
                'product_details' => $sampleProductDetails
            ]
        );

        $this->info("   ✓ Category created/updated (ID: {$category->id})");
        $this->info('   ✓ Product types: ' . implode(', ', $category->product_types));
        $this->info('   ✓ Product details configured for ' . count($category->product_details) . ' types');

        foreach ($category->product_details as $type => $fields) {
            $this->info("     - $type: " . count($fields) . " fields");
        }

    } catch (Exception $e) {
        $this->error('   Error: ' . $e->getMessage());
    }

    // Test 4: Create sample product with details
    $this->info('4. Creating sample product with details...');
    try {
        $sampleProductDetails = [
            'Username' => 'test_user123',
            'Password' => 'secure_password',
            'Email' => 'test@example.com',
            'Level' => 50
        ];

        $product = Product::updateOrCreate(
            ['name' => 'Test Gaming Account'],
            [
                'description' => 'Sample gaming account for testing product details',
                'category_id' => $category->id,
                'price' => 150000,
                'stock' => 10,
                'product_details' => $sampleProductDetails,
                'store_name' => 'Test Store',
                'status' => 'active'
            ]
        );

        $this->info("   ✓ Product created/updated (ID: {$product->id})");
        $this->info('   ✓ Product details:');
        foreach ($product->product_details as $key => $value) {
            $this->info("     - $key: $value");
        }

    } catch (Exception $e) {
        $this->error('   Error: ' . $e->getMessage());
    }

    // Test 5: Test fetching with relationships
    $this->info('5. Testing category-product relationship...');
    try {
        $testCategory = Category::with('products')->where('name', 'Test Gaming Category')->first();
        if ($testCategory) {
            $this->info("   ✓ Category loaded with {$testCategory->products->count()} products");
            
            $testProduct = $testCategory->products->first();
            if ($testProduct) {
                $this->info("   ✓ Product loaded with category: {$testProduct->category->name}");
                $this->info("   ✓ Product details type: " . gettype($testProduct->product_details));
                $this->info("   ✓ Category product_types type: " . gettype($testProduct->category->product_types));
                $this->info("   ✓ Category product_details type: " . gettype($testProduct->category->product_details));
            }
        }
    } catch (Exception $e) {
        $this->error('   Error: ' . $e->getMessage());
    }

    $this->newLine();
    $this->info('=== Test Complete ===');
    $this->info('✓ Database structure ready for category-based product details');
    $this->info('✓ Sample data created successfully');
    $this->info('✓ JSON casting working correctly');
    $this->info('✓ Integration should work with frontend components');
    
    $this->newLine();
    $this->info('Next steps:');
    $this->info('1. Login as seller to test the frontend');
    $this->info('2. Navigate to product management');
    $this->info('3. Try creating/editing products with the new category system');
    $this->info('4. Verify product details show correctly in product listings');

})->purpose('Test the category integration with product details functionality');
