<?php 

namespace rasteiner\opcache;

use Kirby\Cache\Value;
use Kirby\Cache\FileCache;
use Kirby\Filesystem\F;

class OpcacheDriver extends FileCache
{
    static $memcache = [];

    public function __construct(array $options)
    {
        parent::__construct([
            'extension' => 'php',
        ] + $options);
    }

    public function set(string $key, mixed $value, int $duration = 0): bool
    {
        
        $file = $this->file($key);
        $value = new Value($value, $duration);
        self::$memcache[$key] = $value;

        // stringify to php literal
        $value = var_export($value->toArray(), true);
        $value = "<?php\nreturn $value;\n";
        
        // write to file
        return F::write($file, $value);
    }

    public function retrieve(string $key): Value|null
    {
        if (isset(self::$memcache[$key])) {
            return self::$memcache[$key];
        }
        $file = $this->file($key);

        if (!is_file($file)) {
            return null;
        }

        $value = include $file;
        $value = Value::fromArray($value);

        return $value ?? null;
    }

    public function remove(string $key): bool
    {
        unset(self::$memcache[$key]);
        return parent::remove($key);
    }
    
    public function flush(): bool
    {
        self::$memcache = [];
        return parent::flush();
    }
}
