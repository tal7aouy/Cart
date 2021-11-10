<?php

namespace Tal7aouy;

class Cart
{
    /**
     * Unique Id for the cart.
     * @var string
     */
    protected string $cartId;
    /**
     *  A collection of cart items (product,...)
     * @var array
     */
    protected array $items = [];
    /**
     * Maximum item allowed in the cart.
     * @var int
     */
    protected int $maxItem = 0;
    /**
     * Maximum quantity of a item allowed in the cart
     * @var int
     */
    protected  int $itemMaxQte = 0;
    /**
     *  Cookie state (Enable/ Disable)
     * @var bool
     */
    protected bool $cookieState = false;

    /**
     * Init cart.
     */
    public function __construct(?array $options = [])
    {
        $this->init($options);
    }
    private function init(array $options): void
    {

        if (!session_id()) {
            session_start();
        }

        if (isset($options['maxItem']) && preg_match('/^\d+$/', $options['maxItem'])) {
            $this->maxItem = $options['maxItem'];
        }

        if (isset($options['itemMaxQte']) && preg_match('/^\d+$/', $options['itemMaxQte'])) {
            $this->itemMaxQte = $options['itemMaxQte'];
        }

        if (isset($options['cookieState']) && $options['cookieState']) {
            $this->cookieState = true;
        }

        /**
         * HTTP_HOST like localhost:8000 ...
         */
        $this->cartId = md5($_SERVER['HTTP_HOST'] ?? 'shoppingCart') . '_cart';

        $this->read();
    }
    public function add(string $id, int $quantity = 1, array $attributes = []): bool
    {
        $quantity = (preg_match('/^\d+$/', $quantity)) ? $quantity : 1;
        $attributes = (is_array($attributes)) ? array_filter($attributes) : [$attributes];
        $hash = md5(json_encode($attributes));

        if (count($this->items) >= $this->itemMaxQte && $this->itemMaxQte != 0) {
            return false;
        }

        if (isset($this->items[$id])) {
            foreach ($this->items[$id] as $index => $item) {
                if ($item['hash'] == $hash) {
                    $this->items[$id][$index]['quantity'] += $quantity;
                    $this->items[$id][$index]['quantity'] = ($this->itemMaxQte < $this->items[$id][$index]['quantity'] && $this->itemMaxQte != 0) ? $this->itemMaxQuantity : $this->items[$id][$index]['quantity'];

                    $this->write();

                    return true;
                }
            }
        }

        $this->items[$id][] = [
            'id'         => $id,
            'quantity'   => ($quantity > $this->itemMaxQte && $this->itemMaxQte != 0) ? $this->itemMaxQte : $quantity,
            'hash'       => $hash,
            'attributes' => $attributes,
        ];

        $this->write();

        return true;
    }

    public function has(string $id, array $attributes = []): bool
    {
        $attributes = (is_array($attributes)) ? array_filter($attributes) : [$attributes];
        if (isset($this->items[$id])) {
            $hash = md5(json_encode($attributes));
            foreach ($this->items[$id] as $item) {
                if ($item['hash'] == $hash) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getItem(string $id, string $hash = null)
    {
        if ($hash) {
            $key = array_search($hash, array_column($this->items['id'], 'hash'));
            if ($key !== false) return $this->items[$id][$key];
            return false;
        } else {
            return reset($this->items[$id]);
        }
    }


    public function update(string $id, int $quantity = 1, array $attributes = []): bool
    {
        $quantity = (preg_match('/^\d+$/', $quantity)) ? $quantity : 1;

        if ($quantity == 0) {
            $this->remove($id, $attributes);

            return true;
        }

        if (isset($this->items[$id])) {
            $hash = bin2hex(json_encode(array_filter($attributes)));

            foreach ($this->items[$id] as $index => $item) {
                if ($item['hash'] == $hash) {
                    $this->items[$id][$index]['quantity'] = $quantity;
                    $this->items[$id][$index]['quantity'] = ($this->itemMaxQte < $this->items[$id][$index]['quantity'] && $this->itemMaxQte != 0) ? $this->itemMaxQte : $this->items[$id][$index]['quantity'];

                    $this->write();

                    return true;
                }
            }
        }

        return false;
    }

    public function remove(string $id, array $attributes = []): bool
    {
        if (!isset($this->items[$id])) {
            return false;
        }

        if (empty($attributes)) {
            unset($this->items[$id]);

            $this->write();

            return true;
        }
        $hash = md5(json_encode(array_filter($attributes)));

        foreach ($this->items[$id] as $index => $item) {
            if ($item['hash'] == $hash) {
                unset($this->items[$id][$index]);
                $this->items[$id] = array_values($this->items[$id]);

                $this->write();

                return true;
            }
        }

        return false;
    }
    public function getItems(): array
    {
        return $this->items;
    }

    public function isEmpty(): bool
    {
        return empty(array_filter($this->items));
    }

    public function getTotalItems(): int
    {
        $total = 0;
        foreach ($this->items as $items) {
            foreach ($items as $item) {
                ++$total;
            }
        }
        return $total;
    }

    public function getTotalQuantity(): int
    {
        $qte = 0;

        foreach ($this->items as $items) {
            foreach ($items as $item) {
                $qte += $item['quantity'];
            }
        }

        return $qte;
    }

    /**
     * @param string $attribute
     * @return int
     */
    public function getTotalAttribute(string $attribute = 'price'): int
    {
        $total = 0;
        foreach ($this->items as $items) {
            foreach ($items as $item) {
                if (isset($item['attributes'][$attribute])) {
                    $total += $item['attributes'][$attribute] * $item['quantity'];
                }
            }
        }
        return $total;
    }

    public function clear()
    {
        // clear array items
        $this->items = [];
        $this->write();
    }



    /**
     *  Destory cart session.
     */
    public function destroy(): void
    {
        $this->items = [];

        if ($this->cookieState) {
            setcookie($this->cartId, '', -1);
        } else {
            unset($_SESSION[$this->cartId]);
        }
    }
    /**
     * Read items from cart session.
     */
    private function read(): void
    {
        $this->items = ($this->cookieState) ? json_decode((isset($_COOKIE[$this->cartId])) ? $_COOKIE[$this->cartId] : '[]', true) : json_decode((isset($_SESSION[$this->cartId])) ? $_SESSION[$this->cartId] : '[]', true);
    }
    /**
     * Write changes into cart session.
     */
    private function write(): void
    {
        if ($this->cookieState) {
            setcookie($this->cartId, json_encode(array_filter($this->items)), time() + 604800, "/");
        } else {
            $_SESSION[$this->cartId] = json_encode(array_filter($this->items));
        }
    }
}
