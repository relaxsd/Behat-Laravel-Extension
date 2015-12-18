<?php

namespace spec\Laracasts\Behat\ServiceContainer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LumenBooterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(__DIR__, 'bootstrap/app.php', '.env.foo');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Laracasts\Behat\ServiceContainer\LumenBooter');
    }

    function it_knows_the_base_path()
    {
        $this->basePath()->shouldBe(__DIR__);
    }

    function it_knows_the_environment_file()
    {
        $this->environmentFile()->shouldBe('.env.foo');
    }

    function it_knows_the_bootstrap_file()
    {
        $this->bootstrapFile()->shouldBe('bootstrap/app.php');
    }

    function it_takes_exception_with_a_missing_bootstrap_file()
    {
        $this->shouldThrow('RuntimeException')->duringBoot();
    }

}
