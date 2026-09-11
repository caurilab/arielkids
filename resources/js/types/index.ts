export type Audience = 'enfant_fille' | 'enfant_garcon' | 'bebe' | 'femme' | 'homme';

export type OrderStatus =
    | 'en_attente'
    | 'payee'
    | 'en_preparation'
    | 'expediee'
    | 'livree'
    | 'annulee';

export type PaymentMethod = 'mobile_money' | 'livraison';

export type PaymentStatus = 'en_attente' | 'paye' | 'echoue';

export interface Category {
    id: number;
    parent_id: number | null;
    name: string;
    slug: string;
    position: number;
    is_active: boolean;
    children?: Category[];
    products_count?: number;
}

export interface Media {
    id: number;
    product_id: number;
    path: string;
    url: string;
    alt: string | null;
    position: number;
}

export interface Product {
    id: number;
    category_id: number;
    name: string;
    slug: string;
    description: string | null;
    price: number;
    audience: Audience;
    sku: string | null;
    stock: number;
    is_active: boolean;
    is_featured: boolean;
    media: Media[];
    category?: Category;
    created_at?: string;
}

export interface CartItem {
    product_id: number;
    name: string;
    slug: string;
    price: number;
    image: string | null;
    quantity: number;
    stock: number;
}

export interface Cart {
    items: CartItem[];
    count: number;
    total: number;
}

export interface Customer {
    id: number;
    name: string;
    phone: string;
    email: string | null;
    address: string;
}

export interface OrderItem {
    id: number;
    product_id: number | null;
    product_name: string;
    unit_price: number;
    quantity: number;
}

export interface Order {
    id: number;
    reference: string;
    status: OrderStatus;
    payment_method: PaymentMethod;
    payment_status: PaymentStatus;
    total: number;
    cinetpay_transaction_id: string | null;
    created_at: string;
    customer?: Customer;
    items?: OrderItem[];
}

export interface InstaPost {
    id: string;
    media_url: string;
    permalink: string;
    caption: string | null;
}

export interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

export interface SharedProps {
    cart: Cart;
    categories: Category[];
    whatsappNumber: string;
    flash: {
        success?: string;
        error?: string;
    };
    auth: {
        user: { id: number; name: string; email: string } | null;
    };
    [key: string]: unknown;
}
