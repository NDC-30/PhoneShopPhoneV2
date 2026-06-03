<?php

namespace App\Services;

use App\Models\Variant;
use Illuminate\Support\Facades\Session;

/**
 * Giỏ hàng lưu trong session: ['variant_id' => quantity].
 */
class CartService
{
    protected string $key = 'cart';

    private function raw(): array
    {
        return Session::get($this->key, []);
    }

    private function save(array $data): void
    {
        Session::put($this->key, $data);
    }

    public function add(int $variantId, int $qty = 1): void
    {
        $cart = $this->raw();
        $cart[$variantId] = ($cart[$variantId] ?? 0) + $qty;
        $this->save($cart);
    }

    public function update(int $variantId, int $qty): void
    {
        $cart = $this->raw();
        if ($qty <= 0) {
            unset($cart[$variantId]);
        } else {
            $cart[$variantId] = $qty;
        }
        $this->save($cart);
    }

    public function remove(int $variantId): void
    {
        $cart = $this->raw();
        unset($cart[$variantId]);
        $this->save($cart);
    }

    public function clear(): void
    {
        Session::forget($this->key);
        Session::forget('applied_voucher');
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    public function items()
    {
        $cart = $this->raw();
        if (empty($cart)) return collect();

        $variants = Variant::with(['product.brand', 'product.images', 'attributeValues', 'images'])
            ->whereIn('variant_id', array_keys($cart))
            ->get()
            ->keyBy('variant_id');

        $items = collect();
        $changed = false;

        foreach ($cart as $vid => $qty) {
            $variant = $variants->get($vid);
            if (!$variant) { unset($cart[$vid]); $changed = true; continue; }

            if ($variant->stock > 0 && $qty > $variant->stock) {
                $qty = $variant->stock;
                $cart[$vid] = $qty;
                $changed = true;
            }

            $items->push((object)[
                'variant'    => $variant,
                'quantity'   => $qty,
                'line_total' => $variant->price * $qty,
            ]);
        }

        if ($changed) $this->save($cart);
        return $items;
    }

    public function subtotal(): float
    {
        return $this->items()->sum('line_total');
    }
}