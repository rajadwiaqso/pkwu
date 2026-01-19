<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;

class GenerateMarketplaceReport extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'marketplace:report {--detailed} {--export=}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a comprehensive marketplace report';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏪 MarketRaja Gaming Marketplace Report');
        $this->info('====================================');
        
        // Overview Statistics
        $this->generateOverview();
        
        // Product Analysis
        $this->generateProductAnalysis();
        
        // Category Performance
        $this->generateCategoryAnalysis();
        
        // Financial Summary
        $this->generateFinancialSummary();
        
        if ($this->option('detailed')) {
            $this->generateDetailedAnalysis();
        }
        
        if ($this->option('export')) {
            $this->exportReport();
        }
        
        $this->info("\n✅ Report generation completed!");
    }

    private function generateOverview()
    {
        $this->info("\n📊 MARKETPLACE OVERVIEW");
        $this->info("========================");
        
        $totalProducts = Product::count();
        $activeProducts = Product::where('stock', '>', 0)->count();
        $totalCategories = Category::count();
        $totalSellers = User::where('role', 'seller')->count();
        $totalRevenue = Product::sum(\DB::raw('price * sold'));
        $totalSales = Product::sum('sold');
        $avgRating = Product::avg('average_rating');
        
        $this->table([
            'Metric', 'Value'
        ], [
            ['Total Products', number_format($totalProducts)],
            ['Active Products', number_format($activeProducts)],
            ['Total Categories', number_format($totalCategories)],
            ['Active Sellers', number_format($totalSellers)],
            ['Total Revenue', 'Rp ' . number_format($totalRevenue)],
            ['Total Sales', number_format($totalSales) . ' units'],
            ['Average Rating', round($avgRating, 2) . ' ⭐'],
        ]);
    }

    private function generateProductAnalysis()
    {
        $this->info("\n🎮 PRODUCT ANALYSIS");
        $this->info("===================");
        
        $topProducts = Product::orderBy('sold', 'desc')->limit(5)->get();
        
        $this->info("🏆 Top Selling Products:");
        $topProductsData = $topProducts->map(function ($product, $index) {
            return [
                '#' . ($index + 1),
                $product->name,
                $product->sold . ' sold',
                'Rp ' . number_format($product->price),
                $product->average_rating . ' ⭐'
            ];
        })->toArray();
        
        $this->table(['Rank', 'Product', 'Sales', 'Price', 'Rating'], $topProductsData);
        
        // Product type distribution
        $typeDistribution = Product::selectRaw('type, COUNT(*) as count')->groupBy('type')->get();
        $this->info("\n📦 Product Types:");
        foreach ($typeDistribution as $type) {
            $this->line("  • {$type->type}: {$type->count} products");
        }
    }

    private function generateCategoryAnalysis()
    {
        $this->info("\n🏷️ CATEGORY PERFORMANCE");
        $this->info("========================");
        
        $categories = Category::withCount('products')->get();
        $categoryData = [];
        
        foreach ($categories as $category) {
            $categoryProducts = Product::where('category_id', $category->id)->get();
            $totalSales = $categoryProducts->sum('sold');
            $totalRevenue = $categoryProducts->sum(function ($product) {
                return $product->price * $product->sold;
            });
            
            $categoryData[] = [
                $category->name,
                $category->products_count,
                $totalSales,
                'Rp ' . number_format($totalRevenue),
                $categoryProducts->avg('average_rating') ? round($categoryProducts->avg('average_rating'), 1) . ' ⭐' : 'N/A'
            ];
        }
        
        $this->table(['Category', 'Products', 'Sales', 'Revenue', 'Avg Rating'], $categoryData);
    }

    private function generateFinancialSummary()
    {
        $this->info("\n💰 FINANCIAL SUMMARY");
        $this->info("====================");
        
        $totalRevenue = Product::sum(\DB::raw('price * sold'));
        $totalInventoryValue = Product::sum(\DB::raw('price * stock'));
        $avgOrderValue = Product::avg('price');
        $conversionRate = Product::count() > 0 ? (Product::sum('sold') / Product::sum('views')) * 100 : 0;
        
        $this->table([
            'Financial Metric', 'Value'
        ], [
            ['Total Revenue Generated', 'Rp ' . number_format($totalRevenue)],
            ['Current Inventory Value', 'Rp ' . number_format($totalInventoryValue)],
            ['Average Product Price', 'Rp ' . number_format($avgOrderValue)],
            ['Sales Conversion Rate', round($conversionRate, 2) . '%'],
        ]);
        
        // Revenue by product type
        $revenueByType = Product::selectRaw('type, SUM(price * sold) as revenue')
            ->groupBy('type')
            ->get();
            
        $this->info("\n💸 Revenue by Product Type:");
        foreach ($revenueByType as $typeRevenue) {
            $percentage = $totalRevenue > 0 ? round(($typeRevenue->revenue / $totalRevenue) * 100, 1) : 0;
            $this->line("  • {$typeRevenue->type}: Rp " . number_format($typeRevenue->revenue) . " ({$percentage}%)");
        }
    }

    private function generateDetailedAnalysis()
    {
        $this->info("\n🔍 DETAILED ANALYSIS");
        $this->info("====================");
        
        // Inventory alerts
        $lowStock = Product::where('stock', '<=', 5)->where('stock', '>', 0)->count();
        $outOfStock = Product::where('stock', 0)->count();
        
        $this->info("📦 Inventory Status:");
        $this->line("  • Low stock items (≤5): {$lowStock}");
        $this->line("  • Out of stock items: {$outOfStock}");
        
        // Performance insights
        $highPerformers = Product::where('sold', '>', 20)->count();
        $underPerformers = Product::where('sold', 0)->count();
        
        $this->info("\n📈 Performance Insights:");
        $this->line("  • High performers (>20 sales): {$highPerformers}");
        $this->line("  • Underperformers (0 sales): {$underPerformers}");
        
        // Quality metrics
        $excellentRated = Product::where('average_rating', '>=', 4.5)->count();
        $needsImprovement = Product::where('average_rating', '<', 3.5)->count();
        
        $this->info("\n⭐ Quality Metrics:");
        $this->line("  • Excellent rated (≥4.5): {$excellentRated}");
        $this->line("  • Needs improvement (<3.5): {$needsImprovement}");
    }

    private function exportReport()
    {
        $exportPath = $this->option('export');
        $this->info("\n📄 Exporting report to: {$exportPath}");
        
        // This would implement actual file export functionality
        $this->warn("Export functionality not implemented in this demo");
    }
}