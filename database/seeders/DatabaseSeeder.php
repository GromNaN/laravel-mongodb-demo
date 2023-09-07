<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Price;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use MongoDB\BSON\ObjectId;

class DatabaseSeeder extends Seeder
{
    private array $addresses = [];
    private array $users = [];
    private array $products = [];
    private array $orders = [];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::truncate();
        Product::truncate();
        Order::truncate();

        $this->loadAddresses();
        $this->loadUsers();
        $this->loadProducts();
        $this->loadOrders();
        //$this->loadPosts();
    }

    private function loadAddresses(): void
    {
        $this->addresses = [
            new Address([
                'street' => '123 Main Street',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
                'country' => 'US',
            ]),
            new Address([
                'street' => '1 rue de Rivoli',
                'city' => 'Paris',
                'state' => null,
                'zip' => '75001',
                'country' => 'FR',
            ]),
            new Address([
                'street' => 'Unter den Linden 1',
                'city' => 'Berlin',
                'state' => null,
                'zip' => '10117',
                'country' => 'DE',
            ]),
            new Address([
                'street' => '1-2-3, Shibuya',
                'city' => 'Tokyo',
                'state' => null,
                'zip' => '150-0002',
                'country' => 'JP',
            ]),
        ];
    }

    private function loadUsers(): void
    {
        foreach ($this->getUserData() as [$name, $username, $password, $email]) {
            $this->users[$username] = $user = User::create([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
            ]);
            $user->save();
        }
    }

    private function loadProducts(): void
    {
        // List of bike products
        $this->products = [
            new Product([
                'name' => 'SuperSix Evo',
                'description' => 'The ultimate road race bike, with Dura-Ace Di2, HollowGram R-SL 50 wheels and R-One SystemBar',
                'properties' => [
                    'picture' => 'https://embed.widencdn.net/img/dorelrl/i5hvubtijg/500px@2x/C23_C11052U_SuperSix_EVO_LAB71_MOX_3Q.jpg',
                    'brand' => 'Cannondale',
                    'color' => 'red',
                    'size' => '47',
                ],
                'prices' => [
                    new Price(['currency' => 'USD', 'amount' => 12000]),
                    new Price(['currency' => 'EUR', 'amount' => 10000]),
                ],
            ]),
            new Product([
                'name' => 'Scalpel SE',
                'description' => 'The ultimate super-fast XC/trail bike, with SRAM’s XX SL Transmission, Lefty Ocho Carbon 120 & HollowGram XC-27 SL carbon wheels',
                'properties' => [
                    'picture' => 'https://embed.widencdn.net/img/dorelrl/0oerraik9d/500px@2x/C23_C24143U_LAB71_Scalpel_SE_GMG_3Q.jpg',
                    'brand' => 'Cannondale',
                    'color' => 'green',
                ],
            ]),
            new Product([
                'name' => 'SCOTT PLASMA RC ULTIMATE BIKE',
                'description' => 'With the SCOTT Plasma RC Ultimate, we really decided to flex our "Aero Muscles". This Triathlon-specific bicycle features fully integrated cables, a hydration system and storage boxes. We\'ve also made it versatile, making sure that anyone can find their most efficient cockpit position on the bike. If you thought the last Plasma was the fastest in the world (it was) then you ain\'t seen nuthin\' yet.',
                'properties' => [
                    'picture' => 'https://asset.scott-sports.com/fit-in/2000x2000/290/290335_3.png?signature=7f8780b64abd04f604a23580dee079d1d16eb91575ba26595ff1033cf6648ac4',
                    'brand' => 'Scott',
                    'color' => 'black',
                ],
            ]),
            new Product([
                'name' => 'SCOTT ADDICT GRAVEL 30 BIKE',
                'description' => 'The Scott Addict Gravel 30 will get you wherever you’re going, no matter what the trail throws at you. More progressive, more control and more mounts mean this adventure-ready machine is your ticket to getting out there. Find yourself, get lost.',
                'properties' => [
                    'picture' => 'https://asset.scott-sports.com/fit-in/2000x2000/290/290507_2.png?signature=0e6fbfbcae613cac9b718dfcee242ed47865f3653fdee141eb7a99c8b6e904d2',
                    'brand' => 'Scott',
                    'color' => 'green',
                ],
            ]),
            new Product([
                'name' => 'Tarmac SL8 Expert',
                'description' => null,
                'properties' => [
                    'picture' => 'https://assets.specialized.com/i/specialized/94924-30_TARMAC-SL8-EXPERT-SMK-OBSD_HERO?bg=rgb(241,241,241)&w=1600&h=900&fmt=auto',
                    'brand' => 'Specialized',
                    'color' => 'grey',
                ],
            ]),
        ];

        /** @var Product $product */
        foreach ($this->products as $product) {
            $product->save();
        }
    }

    private function loadOrders(): void
    {
        // Order 1
        $order = new Order();
        $order->user()->associate($this->users['john_user']);
        $order->products()->associate(new OrderProduct([
            /* @todo relations doesn't work with embeds */
            //'product' => $this->products[0],
            'product_id' => new ObjectId($this->products[0]->_id),
            /* @todo queries doesn't work with embeds */
            //'price' => $this->products[0]->prices()->where('currency', 'USD')->first(),
            'price' => new Price(['currency' => 'USD', 'amount' => 12000]),
            'quantity' => 1,
        ]));
        $order->products()->associate(new OrderProduct([
            /* @todo doesn't work with embeds */
            //'product' => $this->products[0],
            'product_id' => new ObjectId($this->products[2]->_id),
            //'price' => $this->products[2]->prices()->where('currency', 'USD')->first(),
            'price' => new Price(['currency' => 'USD', 'amount' => 12000]),
            'quantity' => 3,
        ]));
        $order->delivery_address()->associate($this->addresses[0]);
        $order->billing_address()->associate($this->addresses[0]);

        $order->save();
        $this->orders[] = $order;
    }

    /**
     * @return array<array{string, string, string, string}>
     */
    private function getUserData(): array
    {
        return [
            // $userData = [$name, $username, $password, $email];
            ['Jane Doe', 'jane_admin', 'kitten', 'jane_admin@mongoshop.com'],
            ['Tom Doe', 'tom_admin', 'kitten', 'tom_admin@mongoshop.com'],
            ['John Doe', 'john_user', 'kitten', 'john_user@mongoshop.com'],
        ];
    }
}
