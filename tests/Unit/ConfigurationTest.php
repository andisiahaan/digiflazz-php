<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Tests\Unit;

use AndiSiahaan\Digiflazz\Config\Configuration;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ConfigurationTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated_with_required_parameters(): void
    {
        $config = new Configuration('username', 'api_key');

        $this->assertSame('username', $config->getUsername());
        $this->assertSame('api_key', $config->getApiKey());
        $this->assertSame(Configuration::DEFAULT_BASE_URI, $config->getBaseUri());
        $this->assertSame(Configuration::DEFAULT_TIMEOUT, $config->getTimeout());
        $this->assertTrue($config->shouldVerifySsl());
    }

    #[Test]
    public function it_can_be_instantiated_with_custom_options(): void
    {
        $config = new Configuration(
            username: 'user',
            apiKey: 'key',
            baseUri: 'https://custom.api.com/',
            timeout: 60.0,
            verifySsl: false,
            httpOptions: ['proxy' => 'http://proxy.local'],
        );

        $this->assertSame('https://custom.api.com/', $config->getBaseUri());
        $this->assertSame(60.0, $config->getTimeout());
        $this->assertFalse($config->shouldVerifySsl());
        $this->assertSame(['proxy' => 'http://proxy.local'], $config->getHttpOptions());
    }

    #[Test]
    public function it_throws_exception_for_empty_username(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Username cannot be empty');

        new Configuration('', 'api_key');
    }

    #[Test]
    public function it_throws_exception_for_empty_api_key(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('API key cannot be empty');

        new Configuration('username', '');
    }

    #[Test]
    public function it_can_create_new_config_with_modified_base_uri(): void
    {
        $config = new Configuration('user', 'key');
        $newConfig = $config->withBaseUri('https://new.api.com/');

        $this->assertSame('https://new.api.com/', $newConfig->getBaseUri());
        $this->assertSame(Configuration::DEFAULT_BASE_URI, $config->getBaseUri());
    }

    #[Test]
    public function it_can_create_new_config_with_modified_timeout(): void
    {
        $config = new Configuration('user', 'key');
        $newConfig = $config->withTimeout(120.0);

        $this->assertSame(120.0, $newConfig->getTimeout());
        $this->assertSame(Configuration::DEFAULT_TIMEOUT, $config->getTimeout());
    }

    #[Test]
    public function it_can_create_from_environment_variables(): void
    {
        // Set environment variables
        putenv('TEST_DIGIFLAZZ_USER=env_user');
        putenv('TEST_DIGIFLAZZ_KEY=env_key');

        $config = Configuration::fromEnvironment('TEST_DIGIFLAZZ_USER', 'TEST_DIGIFLAZZ_KEY');

        $this->assertSame('env_user', $config->getUsername());
        $this->assertSame('env_key', $config->getApiKey());

        // Cleanup
        putenv('TEST_DIGIFLAZZ_USER');
        putenv('TEST_DIGIFLAZZ_KEY');
    }

    #[Test]
    public function it_throws_exception_when_env_variable_not_set(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Environment variable NONEXISTENT_VAR is not set');

        Configuration::fromEnvironment('NONEXISTENT_VAR', 'ANOTHER_NONEXISTENT');
    }
}
