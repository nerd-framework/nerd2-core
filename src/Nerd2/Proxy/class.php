/**
 * Dynamically Generated Class
 */

class <?= $className ?><?= sizeof($interfaceList) ? ' implements ' : '' ?> <?= implode(', ', $interfaceList) ?>
{
    private $proxyHandler;

    public function __construct(\Nerd2\Proxy\ProxyHandler $proxyHandler)
    {
        $this->proxyHandler = $proxyHandler;
    }

<?php foreach ($methodList as $method): ?>
	public function <?= $method['name'] ?>(<?= implode(', ', $method['args']) ?>)<?= $method['return'] ? ': ' . $method['return'] : '' ?> {
		$args = func_get_args();
		<?= $method['return'] != 'void' ? 'return' : '' ?> call_user_func([$this->proxyHandler, 'invoke'], '<?= $method['name'] ?>', $args);
	}
<?php endforeach ?>
}
