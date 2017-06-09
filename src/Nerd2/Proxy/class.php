/**
 * Dynamically Generated Class
 */

class <?= $className ?>
<?= $parentClass ? ' extends ' . $parentClass . ' ' : '' ?>
<?= sizeof($interfaceList) ? ' implements ' : '' ?> <?= implode(', ', $interfaceList) ?>
{
    private $proxyHandler;

    public function __construct(\Closure $proxyHandler)
    {
        $this->proxyHandler = $proxyHandler;
    }

<?php foreach ($methodList as $method): ?>
    public function <?= $method['name'] ?>(<?= implode(', ', $method['args']) ?>)<?= $method['return'] ? ': ' . $method['return'] : '' ?> {
        $args = func_get_args();
        <?= $method['return'] != 'void' ? 'return' : '' ?> call_user_func($this->proxyHandler, '<?= $method['name'] ?>', $args);
    }

<?php endforeach ?>
}
