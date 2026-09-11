import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { Cart, SharedProps } from '@/types';

/**
 * Panier géré côté serveur (session). Les actions passent par Inertia
 * et la prop partagée `cart` est rafraîchie automatiquement.
 */
export function useCart() {
    const page = usePage<SharedProps>();

    const cart = computed<Cart>(() => page.props.cart);
    const count = computed(() => cart.value.count);
    const total = computed(() => cart.value.total);

    const add = (productId: number, quantity = 1) => {
        router.post(
            route('cart.store'),
            { product_id: productId, quantity },
            { preserveScroll: true },
        );
    };

    const update = (productId: number, quantity: number) => {
        router.patch(
            route('cart.update', productId),
            { quantity },
            { preserveScroll: true },
        );
    };

    const remove = (productId: number) => {
        router.delete(route('cart.destroy', productId), { preserveScroll: true });
    };

    return { cart, count, total, add, update, remove };
}
