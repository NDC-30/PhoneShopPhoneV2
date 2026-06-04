<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Voucher;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function show($slug)
    {
        // Tìm theo slug, nếu không có thì theo id
        $product = Product::active()
            ->with([
                'brand', 'category', 'images',
                'variants.attributeValues.attribute',
                'variants.images',
            ])
            ->where('slug', $slug)
            ->orWhere('product_id', $slug)
            ->firstOrFail();

        // Gom giá trị theo từng thuộc tính (kèm tên + thứ tự để sắp xếp)
        $groups = [];
        foreach ($product->variants as $variant) {
            foreach ($variant->attributeValues as $val) {
                $attr = $val->attribute;
                $aid  = $attr->attribute_id ?? $val->attribute_id;
                if (!isset($groups[$aid])) {
                    $groups[$aid] = [
                        'name'       => $attr->display_name ?? $attr->name ?? 'Tùy chọn',
                        'sort'       => $attr->sort_order ?? 0,
                        'filterable' => (bool) ($attr->filterable ?? false),
                        'values'     => [],
                    ];
                }
                $groups[$aid]['values'][$val->value_id] = [
                    'value_id' => $val->value_id,
                    'value'    => $val->value,
                    'image'    => $val->image,
                ];
            }
        }
        // Sắp theo sort_order của thuộc tính
        uasort($groups, fn ($a, $b) => $a['sort'] <=> $b['sort']);

        // Chỉ thuộc tính được tích "filterable" (Màu / RAM / Dung lượng) MỚI cho chọn.
        // Các thuộc tính còn lại (Camera, CPU, GPU...) luôn nằm ở bảng thông số.
        $optionGroups = [];   // [ ['name' => ..., 'values' => [...]] ]
        $specs        = [];   // [ ['name' => ..., 'value' => ...] ]
        foreach ($groups as $g) {
            $vals = array_values($g['values']);
            if ($g['filterable'] && count($vals) > 1) {
                $optionGroups[] = ['name' => $g['name'], 'values' => $vals];
            } else {
                $specs[] = ['name' => $g['name'], 'value' => $vals[0]['value']];
            }
        }

        // Map biến thể cho JS chọn nhanh
        $variantMap = $product->variants->map(function ($v) {
            return [
                'variant_id'    => $v->variant_id,
                'price'         => (float) $v->price,
                'compare_price' => (float) $v->compare_price,
                'stock'         => (int) $v->stock,
                'sku'           => $v->sku,
                'image'         => $v->image_url,
                'value_ids'     => $v->attributeValues->pluck('value_id')->values(),
                'label'         => $v->label,
            ];
        })->values();

        // Bộ ảnh gallery: ảnh sản phẩm + ảnh các biến thể
        $gallery = collect();
        foreach ($product->images as $im) $gallery->push(asset($im->image_url));
        foreach ($product->variants as $v) {
            if ($v->image) $gallery->push(asset($v->image));
            foreach ($v->images as $vi) $gallery->push(asset($vi->image_url));
        }
        $gallery = $gallery->unique()->values();
        if ($gallery->isEmpty()) $gallery->push($product->thumbnail);

        // Sản phẩm tương tự cùng hãng
        $similar = Product::active()
            ->with(['brand', 'images', 'variants'])
            ->where('brand_id', $product->brand_id)
            ->where('product_id', '!=', $product->product_id)
            ->latest('product_id')->take(4)->get();

        // Voucher đang khả dụng để gợi ý
        $now = Carbon::now();
        $vouchers = Voucher::where('status', 1)
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', $now))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $now))
            ->whereColumn('used_count', '<', 'usage_limit')
            ->take(4)->get();

        return view('customer.products.show', compact(
            'product', 'optionGroups', 'specs', 'variantMap', 'gallery', 'similar', 'vouchers'
        ));
    }
}
