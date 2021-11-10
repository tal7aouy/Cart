# Cart library
A simple PHP shopping cart library to use in ecommerce web applications

## Usage
### Installation
```sh 
  composer require tal7aouy/cart
  ```
### Configuration

##### Options

| Parameter       | Type     | Description                                                            |
| --------------- | -------- | ---------------------------------------------------------------------- |
| maxItem         | **int**  | The maximum item can be added to cart. 0 = Unlimited                   |
| itemMaxQte      | **int**  | The maximum quantity per item can be added to cart. 0 = Unlimited      |
| cookieState     | **bool** |  cookie that helps you to keep data in browser when it closed.         |

```php
//Instantiate cart 
$cart= new Cart(**array** $options);
```


```php
  require_once __DIR__."/vendor/autoload.php";
  use Tal7aouy\Cart;
// Initialize Cart object
$cart = new Cart([
  // Can add unlimited number of item to cart
  'maxItem'      => 0,
  
  // Set maximum quantity allowed per item to 20
  'itemMaxQte'  => 20,
  
  // do not use cookie ,cart data will lost when browser is closed
  'cookieState'        => false,
]);
```



### Add Item

Adds an item to cart.

> $cart->add(**string** $id, **int** $quantity = 1, **array** $attributes = []): **bool**;

```php
// Add item with ID #10
$cart->add('10');

// Add 5 item with ID #12
$cart->add('12', 5);

// Add item with ID #14 with price, color, and size
$cart->add('14', 1, [
  'price'  => '5.99',
  'color'  => 'yellow',
  'size'   => 'SM',
]);

// Item with same ID but different attributes will added as separate item in cart
$cart->add('14', 1, [
  'price'  => '5.99',
  'color'  => 'Brown',
  'size'   => 'M',
]);
```



### Update Item

Updates quantity of an item. Attributes **must be** provides if item with same ID exists with different attributes.

> $cart->update(**string** $id, **int** $quantity = 1, **array** $attributes = []): **bool**;

```php
// Set quantity for item #10 to 5
$cart->update('10', 5);

// Set quantity for item #14 to 2
$cart->update('14' [
  'price'  => '5.99',
  'color'  => 'Red',
  'size'   => 'M',
]);
```



### Remove Item

Removes an item. Attributes **must be** provided to remove specified item, or all items with same ID will be removed from cart.

> $cart->remove(**string** $id, **array** $attributes = []): **bool**;

```php
// Remove item #10
$cart->remove('10');

// Remove item #14 with color white and size XS
$cart->remove('1003', [
  'price'  => '5.99',
  'color'  => 'White',
  'size'   => 'XS',
]);
```



### Get Items

Gets a multi-dimensional array of items stored in cart.

> $cart->getItems( ): **array**;

```php
// Get all items in the cart
$allItems = $cart->getItems();

foreach ($allItems as $items) {
  foreach ($items as $item) {
    echo 'ID: '.$item['id'].'<br />';
    echo 'Qty: '.$item['quantity'].'<br />';
    echo 'Price: '.$item['attributes']['price'].'<br />';
    echo 'Size: '.$item['attributes']['size'].'<br />';
    echo 'Color: '.$item['attributes']['color'].'<br />';
  }
}
```


### Get Item

Gets a multi-dimensional array of one item stored in cart.

> $cart->getItem(**string** $id, **string** $hash): **array**;

```php
// Get first one item from the cart with id 10
$theItem = $cart->getItem('10');

// Get one item from the cart with any id and hash
$theItem = $cart->getItem($item['id'], $item['hash']);
```



### Check Cart Empty

Checks if the cart is empty.

> $cart->isEmpty( ):**bool**;

```php
if ($cart->isEmpty()) {
  echo 'There is nothing in the basket.';
}
```



### Get Total Item

Gets the total of items in the cart.

> $cart->getTotalItems( ): **int**;

```php
echo 'There are '.$cart->getTotalItems().' items in the cart.';
```



### Get Total Quantity

Gets the total of quantity in the cart.

> $cart->getTotalQuantity( ): **int**;

```php
echo $cart->getTotalQuantity();
```



### Get Attribute Total

Gets the sum of a specific attribute.

> $cart->getTotalAttribute( **string** $attribute ): **init**;

```php
echo '<h3>Total Price: $'.number_format($cart->getTotalAttribute('price'), 2, '.', ',').'</h3>';
```



### Clear Cart

Clears all items in the cart.

> $cart->clear( ):void;

```php
$cart->clear();
```



### Destroy Cart

Destroys the entire cart session.

> \$cart->destroy( );

```php
$cart->destroy();
```



### Item Exists

Checks if an item exists in cart.

> **bool** \$cart->isItemExists( **string** \$id\[, **array** \$attributes\] );

```php
if ($cart->isItemExists('1001')) {
  echo 'This item already added to cart.';
}
```


