<?php

namespace App\Controller;

use Psr\Container\ContainerInterface;

/**
 * 访问和设置container 的Trait
 *
 * @author Xie yuxuan
 */
trait containerTrait
{
    /** @var ContainerInterface 变量 */
    protected $container = null;

    /**
     * 设置 container 对象
     *
     * @param ContainerInterface $c 设置的container
     */
    protected function setContainer(ContainerInterface $c): void
    {
        $this->container = $c;
    }

    /**
     * 判断是否有container 对象存在
     */
    protected function hasContainer(): bool
    {
        return null !== $this->container;
    }

    /**
     * 从容器中获取对应的依赖
     *
     * @param string $key 要获取的依赖名称
     *
     * @throws \Exception
     *
     * @return mixed 返回很有可能是null
     */
    protected function fetch(string $key)
    {
        if (!$this->hasContainer()) {
            throw new \RuntimeException('Function \'setContainer\' should be use before call function \'fetch\'');
        }

        return $this->container->get($key);
    }

    /**
     * 重载属性直接调用容器中的对象
     *
     * @param $name
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->fetch($name);
    }

    /**
     * 判断容器中是否存在对应的依赖
     *
     * @param string $key 要获取的依赖名称
     *
     * @throws \Exception
     *
     * @return bool
     */
    protected function has(string $key): bool
    {
        if (!$this->hasContainer()) {
            throw new \RuntimeException('Function \'setContainer\' should be use before call function \'has\'');
        }

        return $this->container->has($key);
    }
}
